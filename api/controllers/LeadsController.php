<?php
// controllers/LeadsController.php

require_once 'config/db.php';
require_once 'models/Lead.php';
require_once 'models/LeadInteraction.php';
require_once 'models/LeadAssignment.php';
require_once 'utils/JwtUtils.php';
require_once 'utils/Mailer.php';

class LeadsController
{
    private $db;
    private $lead;
    private $jwt;
    private $table_name = "leads";

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->lead = new Lead($this->db);
        $this->jwt = new JwtUtils();
        $this->ensureLeadColumns();
    }

    private function ensureLeadColumns()
    {
        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM leads LIKE 'preferred_date'");
            if (!$stmt || !$stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->db->exec("ALTER TABLE leads ADD COLUMN preferred_date DATE NULL AFTER time_slot");
            }

            $stmt = $this->db->query("SHOW COLUMNS FROM leads LIKE 'doc_path'");
            if (!$stmt || !$stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->db->exec("ALTER TABLE leads ADD COLUMN doc_path VARCHAR(500) NULL AFTER preferred_date");
            }
        } catch (Exception $e) {
            error_log("Unable to ensure leads columns: " . $e->getMessage());
        }
    }

    private function normalizeSectors($sectorsInput)
    {
        if (is_array($sectorsInput)) {
            $values = $sectorsInput;
        } else {
            $raw = trim((string)$sectorsInput);
            if ($raw === '') {
                return [];
            }

            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $values = $decoded;
            } else {
                $values = preg_split('/[;,|]/', $raw);
            }
        }

        $normalized = [];
        foreach ($values as $v) {
            $val = trim((string)$v);
            if ($val === '') {
                continue;
            }
            foreach ($this->expandSectorAliases($val) as $alias) {
                $normalized[] = $alias;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function normalizeSectorKey($value)
    {
        $raw = trim((string)$value);
        if ($raw === '') {
            return '';
        }

        $lower = function_exists('mb_strtolower') ? mb_strtolower($raw, 'UTF-8') : strtolower($raw);
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $lower);
        $ascii = $ascii !== false ? strtolower($ascii) : $lower;
        $ascii = preg_replace('/[^a-z0-9]+/', '', $ascii);

        $map = [
            'assurance' => 'assurance',
            'assurances' => 'assurance',
            'renovation' => 'renovation',
            'renovations' => 'renovation',
            'energie' => 'energie',
            'energies' => 'energie',
            'finance' => 'finance',
            'finances' => 'finance',
            'garage' => 'garage',
            'garages' => 'garage',
            'telecom' => 'telecom',
            'telecoms' => 'telecom',
            'general' => 'general',
            'generale' => 'general',
            'generaliste' => 'general'
        ];

        return $map[$ascii] ?? $ascii;
    }

    private function expandSectorAliases($value)
    {
        $key = $this->normalizeSectorKey($value);
        if ($key === '') {
            return [];
        }

        $aliases = [
            'assurance' => ['assurance', 'assurances'],
            'renovation' => ['renovation', 'renovations', 'rénovation', 'rénovations'],
            'energie' => ['energie', 'energies', 'énergie', 'énergies'],
            'finance' => ['finance', 'finances'],
            'garage' => ['garage', 'garages'],
            'telecom' => ['telecom', 'telecoms', 'télécom', 'télécoms'],
            'general' => ['general', 'général', 'generale', 'générale', 'generaliste', 'généraliste']
        ];

        if (!isset($aliases[$key])) {
            return [$key];
        }

        $expanded = [];
        foreach ($aliases[$key] as $alias) {
            $expanded[] = function_exists('mb_strtolower') ? mb_strtolower($alias, 'UTF-8') : strtolower($alias);
        }
        return array_values(array_unique($expanded));
    }

    private function getProviderSectors($providerId)
    {
        require_once 'models/User.php';
        $userModel = new User($this->db);
        $userModel->id = $providerId;
        $userModel->readOne();
        return $this->normalizeSectors($userModel->sectors ?? []);
    }

    private function getAuthHeader()
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        return $headers['Authorization'] ??
               $headers['authorization'] ??
               $_SERVER['HTTP_AUTHORIZATION'] ??
               $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ??
               $_SERVER['HTTP_AUTHORISATION'] ??
               $_SERVER['REDIRECT_HTTP_AUTHORISATION'] ??
               '';
    }

    // Middleware simple pour vérifier le token
    private function authenticate()
    {
        $authHeader = $this->getAuthHeader();

        if (preg_match('/Bearer\\s(\\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $payload = $this->jwt->validate($token);
            if ($payload) {
                return (array)$payload;
            }
        }

        http_response_code(401);
        echo json_encode(["error" => "Non autorisé"]);
        throw new Exception("Unauthorized");
    }

    private function tryAuthenticate()
    {
        $authHeader = $this->getAuthHeader();
        if (!preg_match('/Bearer\\s(\\S+)/', $authHeader, $matches)) {
            return null;
        }

        $token = $matches[1];
        $payload = $this->jwt->validate($token);
        return $payload ? (array)$payload : null;
    }

    private function parseLeadPayload()
    {
        if (!empty($_POST)) {
            return (object)$_POST;
        }

        $raw = file_get_contents("php://input");
        if (!$raw) {
            return null;
        }

        $decoded = json_decode($raw);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return null;
    }

    private function handleLeadDocUpload($leadId)
    {
        if (empty($_FILES['doc']) || !isset($_FILES['doc']['error']) || $_FILES['doc']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['doc'];
        $size = (int)($file['size'] ?? 0);
        if ($size <= 0 || $size > 10 * 1024 * 1024) {
            throw new Exception("Document invalide (taille max 10MB).");
        }

        $allowedExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg'];
        $original = (string)($file['name'] ?? '');
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if ($ext === '' || !in_array($ext, $allowedExt, true)) {
            throw new Exception("Format de document non autorisé.");
        }

        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($original, PATHINFO_FILENAME));
        if (!$safeName) {
            $safeName = 'doc';
        }

        $relativeDir = '/rappel/public/uploads/leads/' . $leadId;
        $absoluteDir = dirname(__DIR__, 2) . '/public/uploads/leads/' . $leadId;
        if (!is_dir($absoluteDir) && !mkdir($absoluteDir, 0775, true) && !is_dir($absoluteDir)) {
            throw new Exception("Impossible de créer le dossier d'upload.");
        }

        $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '_' . $safeName . '.' . $ext;
        $absolutePath = $absoluteDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
            throw new Exception("Impossible d'enregistrer le document.");
        }

        return $relativeDir . '/' . $filename;
    }

    public function getAll()
    {
        $auth = $this->authenticate();
        
        if ($auth['role'] === 'client') {
            $stmt = $this->lead->readByClient($auth['id']);
        } elseif ($auth['role'] === 'provider') {
            $providerSectors = $this->getProviderSectors($auth['id']);
            if (empty($providerSectors)) {
                echo json_encode([]);
                return;
            }

            // Read all leads visible to this provider (assigned to me or unassigned),
            // then apply strict sector matching in PHP with normalized keys.
            // This avoids SQL collation/encoding mismatches causing hidden leads.
            $stmt = $this->lead->read($auth['id']);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $allowedSectors = array_values(array_unique(array_filter(array_map(function ($s) {
                return $this->normalizeSectorKey((string)$s);
            }, $providerSectors))));

            $filtered = array_values(array_filter($rows, function ($row) use ($allowedSectors) {
                $leadSector = $this->normalizeSectorKey((string)($row['sector'] ?? ''));
                return $leadSector !== '' && in_array($leadSector, $allowedSectors, true);
            }));

            echo json_encode($filtered);
            return;
        } else {
            $stmt = $this->lead->read($auth['id']);
        }
        
        $num = $stmt->rowCount();

        if ($num > 0) {
            $leads_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $leads_arr[] = $row;
            }
            echo json_encode($leads_arr);
        } else {
            echo json_encode([]);
        }
    }

    public function create($returnResult = false)
    {
        $data = $this->parseLeadPayload();
        $auth = $this->tryAuthenticate();
        
        if (!$data && property_exists($this, 'tempData')) {
            $data = $this->tempData;
        }

        // Handle first_name + last_name -> name
        if ($data && empty($data->name) && (!empty($data->first_name) || !empty($data->last_name))) {
            $data->name = trim(($data->first_name ?? '') . ' ' . ($data->last_name ?? ''));
        }

        // Fallback: if logged as client, enrich missing name/phone from profile
        if ($data && $auth && ($auth['role'] ?? null) === 'client' && (empty($data->name) || empty($data->phone))) {
            try {
                $stmt = $this->db->prepare("SELECT first_name, last_name, phone FROM user_profiles WHERE id = ? LIMIT 1");
                $stmt->execute([$auth['id']]);
                $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

                if (empty($data->name)) {
                    $profileName = trim((($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')));
                    if ($profileName !== '') {
                        $data->name = $profileName;
                    }
                }
                if (empty($data->phone) && !empty($profile['phone'])) {
                    $data->phone = $profile['phone'];
                }
            } catch (Exception $e) {
                error_log("Lead create profile fallback failed: " . $e->getMessage());
            }
        }

        if ($data && empty($data->name) && !empty($data->phone)) {
            $data->name = 'Client';
        }

        if ($data && !empty($data->name) && !empty($data->phone)) {
            // UUID v4
            $this->lead->id = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
            );

            $this->lead->name = $data->name;
            $this->lead->phone = $data->phone;
            $this->lead->email = $data->email ?? null;
            $incomingSector = $data->service_type ?? ($data->sector ?? 'Général');
            $this->lead->sector = $this->normalizeSectorKey($incomingSector); // canonical sector key
            $this->lead->need = $data->need ?? '';
            $this->lead->time_slot = $data->time_slot ?? '';
            $preferredDateRaw = trim((string)($data->preferred_date ?? ''));
            $this->lead->preferred_date = preg_match('/^\d{4}-\d{2}-\d{2}$/', $preferredDateRaw) ? $preferredDateRaw : null;
            $this->lead->budget = $data->budget ?? 0;
            $this->lead->status = 'pending';
            $this->lead->address = $data->address ?? '';
            $this->lead->zip_code = $data->zip_code ?? '';
            $this->lead->city = $data->city ?? '';
            $this->lead->user_id = $data->user_id ?? null;
            $this->lead->doc_path = null;

            try {
                $this->lead->doc_path = $this->handleLeadDocUpload($this->lead->id);
            } catch (Exception $uploadError) {
                if ($returnResult) return false;
                http_response_code(400);
                echo json_encode(["error" => $uploadError->getMessage()]);
                return;
            }

            // If user is logged in as client, auto-assign user_id
            if ($auth && ($auth['role'] ?? null) === 'client') {
                $this->lead->user_id = $auth['id'];
            }

            if ($this->lead->create()) {
                if ($returnResult) return $this->lead->id;

                http_response_code(201);
                 
                // Send Email
                // Wrapped in try-catch to prevent hanging if SMTP fails
                $mailer = new Mailer();
                if ($this->lead->email) {
                    try {
                        $mailer->sendConfirmation($this->lead->email, $this->lead->name, [
                            'need' => !empty($this->lead->need) ? $this->lead->need : $this->lead->sector,
                            'time_slot' => $this->lead->time_slot,
                            'phone' => $this->lead->phone
                        ]);
                    } catch (Exception $e) {
                         error_log("Email sending failed: " . $e->getMessage());
                         // Continue execution even if email fails
                    } catch (Throwable $t) {
                         error_log("Email sending fatal error: " . $t->getMessage());
                    }
                }

                // Notify Providers in the same sector
                if (!empty($this->lead->sector)) {
                    require_once 'models/User.php';
                    $userModel = new User($this->db);
                    $providers = $userModel->findBySector($this->lead->sector);
                    
                    foreach ($providers as $prov) {
                        try {
                            $mailer->sendLeadNotificationToProvider($prov['email'], ($prov['first_name'] . ' ' . $prov['last_name']), [
                                'sector' => $this->lead->sector,
                                'city' => $this->lead->city,
                                'budget' => $this->lead->budget
                            ]);
                        } catch (Exception $e) {
                            error_log("Provider notification failed for {$prov['email']}: " . $e->getMessage());
                        }
                    }
                }

                echo json_encode(["message" => "Lead créé.", "id" => $this->lead->id]);
            } else {
                if ($returnResult) return false;
                http_response_code(503);
                echo json_encode(["error" => "Impossible de créer le lead."]);
            }
        } else {
            if ($returnResult) return false;
            http_response_code(400);
            echo json_encode(["error" => "Données incomplètes (nom et téléphone requis)."]);
        }
    }

    public function get($id)
    {
        $query = "SELECT l.*, 
                         cp.first_name AS client_first_name, 
                         cp.last_name AS client_last_name,
                         cp.phone AS client_profile_phone,
                         cp.address AS client_profile_address,
                         cp.city AS client_profile_city,
                         cp.zip AS client_profile_zip
                  FROM " . $this->table_name . " l
                  LEFT JOIN user_profiles cp ON l.user_id = cp.id
                  WHERE l.id = ? LIMIT 0,1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($row);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Lead non trouvé."]);
        }
    }

    public function createManual()
    {
        $auth = $this->authenticate();
        $leadId = $this->create(true); 

        if ($leadId) {
            if ($this->lead->createAssignment($leadId, $auth['id'])) {
                http_response_code(201);
                echo json_encode(["message" => "Lead manuel créé et assigné.", "id" => $leadId]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Lead créé mais échec de l'assignation."]);
            }
        } else {
            // Error response already handled by create(true) if it had echoed, 
            // but we need to ensure some response if it returned false.
            if (http_response_code() == 200) { // Default
                http_response_code(400);
                echo json_encode(["error" => "Échec de la création du lead manuel."]);
            }
        }
    }

    public function update($id)
    {
        $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Données manquantes."]);
            return;
        }

        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            // Filter allowed keys for security
            if (in_array($key, ['name', 'email', 'phone', 'address', 'sector', 'need', 'budget', 'status', 'time_slot', 'preferred_date'])) {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(["error" => "Aucun champ valide à mettre à jour."]);
            return;
        }

        $values[] = $id;
        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($query);

        if ($stmt->execute($values)) {
            echo json_encode(["message" => "Lead mis à jour."]);
        } else {
            http_response_code(503);
            echo json_encode(["error" => "Échec de la mise à jour."]);
        }
    }

    public function getInteractions($lead_id)
    {
        $auth = $this->authenticate();
        $interaction = new LeadInteraction($this->db);
        $stmt = $interaction->readByLead($lead_id);
        
        $interactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $interactions[] = $row;
        }
        echo json_encode($interactions);
    }

    public function addInteraction($lead_id)
    {
        $auth = $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->comment)) {
            http_response_code(400);
            echo json_encode(["error" => "Commentaire requis."]);
            return;
        }

        $interaction = new LeadInteraction($this->db);
        $interaction->id = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        $interaction->lead_id = $lead_id;
        $interaction->provider_id = $auth['id'];
        $interaction->comment = $data->comment;

        if ($interaction->create()) {
            http_response_code(201);
            echo json_encode(["message" => "Commentaire ajouté.", "id" => $interaction->id]);
        } else {
            http_response_code(503);
            echo json_encode(["error" => "Échec de l'ajout du commentaire."]);
        }
    }

    public function deleteInteraction($id)
    {
        $auth = $this->authenticate();
        $interaction = new LeadInteraction($this->db);
        
        // Ownership check
        $query = "SELECT provider_id FROM lead_interactions WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            http_response_code(404);
            echo json_encode(["error" => "Interaction non trouvée."]);
            return;
        }
        
        if ((string)$row['provider_id'] !== (string)$auth['id']) {
            http_response_code(403);
            echo json_encode(["error" => "Vous ne pouvez supprimer que vos propres commentaires."]);
            return;
        }
        
        $interaction->id = $id;
        if ($interaction->delete()) {
            echo json_encode(["message" => "Commentaire supprimé."]);
        } else {
            http_response_code(503);
            echo json_encode(["error" => "Échec de la suppression."]);
        }
    }

    public function claim($lead_id)
    {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'provider') {
            http_response_code(403);
            echo json_encode(["error" => "Seuls les prestataires peuvent réclamer des leads."]);
            return;
        }

        // Check credits
        require_once 'models/User.php';
        $userModel = new User($this->db);
        $userModel->id = $auth['id'];
        
        if (!$userModel->hasEnoughCredits()) {
            http_response_code(403);
            echo json_encode(["error" => "Vous n'avez plus de crédits lead. Veuillez upgrader votre offre dans la section Facturation."]);
            return;
        }

        $providerSectors = $this->getProviderSectors($auth['id']);
        if (empty($providerSectors)) {
            http_response_code(403);
            echo json_encode(["error" => "Aucun secteur n'est configuré sur votre compte expert."]);
            return;
        }

        $leadSectorStmt = $this->db->prepare("SELECT sector FROM leads WHERE id = ? LIMIT 1");
        $leadSectorStmt->execute([$lead_id]);
        $leadRow = $leadSectorStmt->fetch(PDO::FETCH_ASSOC);
        if (!$leadRow) {
            http_response_code(404);
            echo json_encode(["error" => "Lead non trouvé."]);
            return;
        }

        $leadSector = $this->normalizeSectorKey($leadRow['sector'] ?? '');
        if ($leadSector === '' || !in_array($leadSector, $providerSectors, true)) {
            http_response_code(403);
            echo json_encode(["error" => "Ce lead n'appartient pas à votre secteur d'activité."]);
            return;
        }

        // Check if already assigned
        $checkStmt = $this->db->prepare("SELECT id FROM lead_assignments WHERE lead_id = ?");
        $checkStmt->execute([$lead_id]);
        if ($checkStmt->fetch()) {
            http_response_code(400);
            echo json_encode(["error" => "Ce lead est déjà assigné."]);
            return;
        }

        try {
            $this->db->beginTransaction();

            // 1. Create assignment
            if ($this->lead->createAssignment($lead_id, $auth['id'])) {
                // 2. Deduct credit
                if (!$userModel->deductCredit()) {
                    throw new Exception("Échec de la déduction de crédit.");
                }

                // 3. Update lead status to assigned
                $updateStmt = $this->db->prepare("UPDATE leads SET status = 'assigned' WHERE id = ?");
                $updateStmt->execute([$lead_id]);

                $this->db->commit();
                http_response_code(200);
                echo json_encode(["message" => "Lead réclamé avec succès. 1 crédit a été déduit."]);
            } else {
                throw new Exception("Échec de l'assignation du lead.");
            }
        } catch (Exception $e) {
            $this->db->rollBack();
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
?>
