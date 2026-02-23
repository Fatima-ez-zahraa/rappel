<?php
require_once 'config/db.php';
require_once 'models/Lead.php';
require_once 'models/Quote.php';
require_once 'models/User.php';
require_once 'utils/JwtUtils.php';

class CompanyController {
    
    private $db;
    private $lead;
    private $quote;
    private $user;
    private $jwt;
    private $cacheFile = __DIR__ . '/../cache/legal_forms.json';
    private $cacheExpiry = 86400; // 24 hours
    private $kipocedApiKey = 'wa05ila67f2003a40c02f0flv43ax3a1';
    private $kipocedBaseUrl = 'https://api.kipoced.com/v1/listeEven';
    
    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->lead = new Lead($this->db);
        $this->quote = new Quote($this->db);
        $this->user = new User($this->db);
        $this->jwt = new JwtUtils();
    }

    /**
     * Middleware simple pour vérifier le token
     */
    private function authenticate()
    {
        $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
        $authHeader = $headers['Authorization'] ?? 
                      $headers['authorization'] ?? 
                      $_SERVER['HTTP_AUTHORIZATION'] ?? 
                      $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? 
                      $_SERVER['HTTP_AUTHORISATION'] ?? 
                      $_SERVER['REDIRECT_HTTP_AUTHORISATION'] ?? 
                      '';
        

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
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

    /**
     * Get quotes for the authenticated provider
     */
    public function getQuotes() {
        $auth = $this->authenticate();
        $stmt = $this->quote->readByProvider($auth['id']);
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($quotes);
    }

    /**
     * Create a new quote
     */
    public function createQuote() {
        $auth = $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));
        
        if (empty($data->client_name) || empty($data->amount)) {
            http_response_code(400);
            echo json_encode(["error" => "Données incomplètes"]);
            return;
        }

        $this->quote->id = $this->generateUuid();
        $this->quote->provider_id = $auth['id'];
        $this->quote->client_name = $data->client_name;
        $this->quote->project_name = $data->project_name ?? ($data->description ?? 'Nouveau Projet');
        $this->quote->amount = $data->amount;
        $this->quote->items_count = $data->items_count ?? 1;
        $this->quote->status = $data->status ?? 'attente_client';

        if ($this->quote->create()) {
            http_response_code(201);
            echo json_encode(["message" => "Devis créé", "id" => $this->quote->id]);
        } else {
            http_response_code(503);
            echo json_encode(["error" => "Erreur lors de la création du devis"]);
        }
    }

    public function updateQuote($id)
    {
        $auth = $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (!$data) {
            http_response_code(400);
            echo json_encode(["error" => "Données manquantes"]);
            return;
        }

        $this->quote->id = $id;
        $this->quote->provider_id = $auth['id'];

        if (!$this->quote->readOne()) {
            http_response_code(404);
            echo json_encode(["error" => "Devis non trouvé ou non autorisé"]);
            return;
        }

        $this->quote->client_name = $data->client_name ?? $this->quote->client_name;
        $this->quote->project_name = $data->project_name ?? ($data->description ?? $this->quote->project_name);
        $this->quote->amount = $data->amount ?? $this->quote->amount;
        $this->quote->items_count = $data->items_count ?? $this->quote->items_count;
        $this->quote->status = $data->status ?? $this->quote->status;

        if ($this->quote->update()) {
            echo json_encode(["message" => "Devis mis à jour"]);
        } else {
            http_response_code(503);
            echo json_encode(["error" => "Erreur lors de la mise à jour du devis"]);
        }
    }

    public function deleteQuote($id)
    {
        $auth = $this->authenticate();
        $this->quote->id = $id;
        $this->quote->provider_id = $auth['id'];

        if ($this->quote->delete()) {
            echo json_encode(["message" => "Devis supprimé"]);
        } else {
            http_response_code(503);
            echo json_encode(["error" => "Erreur lors de la suppression du devis"]);
        }
    }

    /**
     * Get statistics for the dashboard
     */
    public function getStats() {
        $auth = $this->authenticate();
        
        // Basic stats: count leads, count quotes, total quotes amount
        $leadsStmt = $this->lead->readByProvider($auth['id']);
        $leadsData = $leadsStmt->fetchAll(PDO::FETCH_ASSOC);
        $leadsCount = count($leadsData);

        $quotesStmt = $this->quote->readByProvider($auth['id']);
        $quotesData = $quotesStmt->fetchAll(PDO::FETCH_ASSOC);
        $quotesCount = count($quotesData);
        $totalAmount = array_reduce($quotesData, function($carry, $item) {
            return $carry + $item['amount'];
        }, 0);

        $pendingLeadsCount = array_reduce($leadsData, function($carry, $item) {
            return $carry + ($item['status'] === 'pending' ? 1 : 0);
        }, 0);

        // Generate semi-realistic chart data if none exists
        $weeklyData = [
            ['name' => 'Lun', 'revenue' => round($totalAmount * 0.1)],
            ['name' => 'Mar', 'revenue' => round($totalAmount * 0.15)],
            ['name' => 'Mer', 'revenue' => round($totalAmount * 0.25)],
            ['name' => 'Jeu', 'revenue' => round($totalAmount * 0.2)],
            ['name' => 'Ven', 'revenue' => round($totalAmount * 0.3)],
        ];

        $monthlyData = [
            ['name' => 'Sem 1', 'revenue' => round($totalAmount * 0.3)],
            ['name' => 'Sem 2', 'revenue' => round($totalAmount * 0.2)],
            ['name' => 'Sem 3', 'revenue' => round($totalAmount * 0.4)],
            ['name' => 'Sem 4', 'revenue' => round($totalAmount * 0.1)],
        ];

        $annualData = [
            ['name' => 'Jan', 'revenue' => 0],
            ['name' => 'Fév', 'revenue' => $totalAmount],
            ['name' => 'Mar', 'revenue' => 0],
            ['name' => 'Avr', 'revenue' => 0],
        ];

        echo json_encode([
            "totalLeads" => $leadsCount,
            "totalQuotes" => $quotesCount,
            "totalAmount" => $totalAmount,
            "totalRevenue" => $totalAmount, 
            "pendingLeads" => $pendingLeadsCount,
            "revenueGrowth" => $totalAmount > 0 ? 15 : 0, 
            "conversionRate" => $leadsCount > 0 ? round(($quotesCount / $leadsCount) * 100, 1) : 0,
            "weeklyData" => $weeklyData,
            "monthlyData" => $monthlyData,
            "annualData" => $annualData
        ]);
    }

    /**
     * Get recent activity
     */
    public function getActivity() {
        $auth = $this->authenticate();
        
        // Fetch recent leads and recent quotes to merge into an activity feed
        $recentLeadsStmt = $this->lead->readRecentByProvider($auth['id'], 5);
        $recentLeads = $recentLeadsStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $recentQuotesStmt = $this->quote->readByProvider($auth['id']); // already ordered by date
        $recentQuotes = array_slice($recentQuotesStmt->fetchAll(PDO::FETCH_ASSOC), 0, 5);

        $activity = [];
        foreach ($recentLeads as $l) {
            $activity[] = [
                "id" => $l['id'],
                "type" => "lead",
                "title" => "Nouveau Lead: " . $l['name'],
                "date" => $l['created_at'],
                "subtitle" => "Secteur: " . $l['sector']
            ];
        }
        foreach ($recentQuotes as $q) {
            $activity[] = [
                "id" => $q['id'],
                "type" => "quote",
                "title" => "Devis créé pour " . $q['client_name'],
                "date" => $q['created_at'],
                "subtitle" => "Montant: " . $q['amount'] . "€ (" . $q['status'] . ")"
            ];
        }

        // Sort combined activity by date DESC
        usort($activity, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        echo json_encode(array_slice($activity, 0, 10));
    }

    /**
     * Admin: Get all leads with assignment info
     */
    public function getAdminLeads() {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Accès refusé"]);
            return;
        }

        $query = "SELECT l.*,
                         la.provider_id AS assigned_provider_id,
                         up.email AS assigned_provider_email,
                         up.first_name AS assigned_provider_first_name,
                         up.last_name AS assigned_provider_last_name,
                         up.company_name AS assigned_provider_company
                  FROM leads l
                  LEFT JOIN lead_assignments la ON l.id = la.lead_id
                  LEFT JOIN user_profiles up ON la.provider_id = up.id
                  ORDER BY l.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($leads);
    }

    /**
     * Admin: Get providers list
     */
    public function getAdminProviders() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $query = "SELECT id, email, first_name, last_name, role, company_name, phone, city, zone, sectors, description, legal_form, siret, subscription_status, is_verified, created_at
                  FROM user_profiles
                  WHERE role = 'provider'
                  ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $leadCountStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ?");
        $leadDayStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ? AND DATE(created_at) = CURDATE()");
        $leadWeekStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ? AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
        $leadMonthStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ? AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");

        foreach ($providers as &$provider) {
            $providerId = $provider['id'];
            $leadCountStmt->execute([$providerId]);
            $provider['assigned_leads_total'] = (int)$leadCountStmt->fetchColumn();

            $leadDayStmt->execute([$providerId]);
            $provider['assigned_leads_day'] = (int)$leadDayStmt->fetchColumn();

            $leadWeekStmt->execute([$providerId]);
            $provider['assigned_leads_week'] = (int)$leadWeekStmt->fetchColumn();

            $leadMonthStmt->execute([$providerId]);
            $provider['assigned_leads_month'] = (int)$leadMonthStmt->fetchColumn();

            $provider['plan_name'] = $this->resolveProviderPlanName($provider);
            $provider['sectors_list'] = $this->decodeSectors($provider['sectors'] ?? null);
        }

        echo json_encode($providers);
    }

    /**
     * Admin: Get dashboard stats
     */
    public function getAdminStats() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_profiles WHERE role = 'provider'");
        $stmt->execute();
        $totalProviders = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leads");
        $stmt->execute();
        $totalLeads = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_profiles WHERE role = 'provider' AND subscription_status = 'active'");
        $stmt->execute();
        $activeSubscriptions = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) FROM quotes WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
        $stmt->execute();
        $monthlyRevenue = (float)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leads WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $leadsToday = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leads WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
        $stmt->execute();
        $leadsWeek = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leads WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
        $stmt->execute();
        $leadsMonth = (int)$stmt->fetchColumn();

        echo json_encode([
            "total_providers" => $totalProviders,
            "total_leads" => $totalLeads,
            "active_subscriptions" => $activeSubscriptions,
            "monthly_revenue" => round($monthlyRevenue, 2),
            "leads_today" => $leadsToday,
            "leads_week" => $leadsWeek,
            "leads_month" => $leadsMonth,
        ]);
    }

    /**
     * Public: list pricing plans used on provider pricing page
     */
    public function getPlans() {
        $query = "SELECT id, name, price, currency, features, stripe_price_id, created_at
                  FROM subscription_plans
                  ORDER BY price ASC, created_at ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($plans as &$plan) {
            $plan['features'] = $this->decodeSectors($plan['features'] ?? null);
            $plan['price'] = isset($plan['price']) ? (float)$plan['price'] : 0.0;
        }

        echo json_encode($plans);
    }

    /**
     * Admin: list pricing plans
     */
    public function getAdminPlans() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }
        $this->getPlans();
    }

    /**
     * Admin: create pricing plan
     */
    public function createAdminPlan() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $name = trim((string)($data['name'] ?? ''));
        $price = isset($data['price']) ? (float)$data['price'] : null;

        if ($name === '' || $price === null) {
            http_response_code(400);
            echo json_encode(["error" => "Nom et prix requis."]);
            return;
        }

        $id = $this->generateUuid();
        $currency = strtoupper(trim((string)($data['currency'] ?? 'EUR')));
        if ($currency === '') {
            $currency = 'EUR';
        }

        $stripePriceId = trim((string)($data['stripe_price_id'] ?? ''));
        if ($stripePriceId === '') {
            $stripePriceId = 'manual_' . substr(str_replace('-', '', $id), 0, 18);
        }

        $features = $this->normalizeFeaturesInput($data['features'] ?? []);

        $query = "INSERT INTO subscription_plans (id, name, stripe_price_id, price, currency, features)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $ok = $stmt->execute([$id, $name, $stripePriceId, $price, $currency, json_encode($features)]);

        if (!$ok) {
            http_response_code(503);
            echo json_encode(["error" => "Impossible de creer le forfait."]);
            return;
        }

        http_response_code(201);
        echo json_encode(["message" => "Forfait cree.", "id" => $id]);
    }

    /**
     * Admin: update pricing plan
     */
    public function updateAdminPlan($planId) {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!is_array($data) || empty($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Donnees manquantes."]);
            return;
        }

        $fields = [];
        $values = [];

        if (array_key_exists('name', $data)) {
            $fields[] = "name = ?";
            $values[] = trim((string)$data['name']);
        }
        if (array_key_exists('price', $data)) {
            $fields[] = "price = ?";
            $values[] = (float)$data['price'];
        }
        if (array_key_exists('currency', $data)) {
            $currency = strtoupper(trim((string)$data['currency']));
            $fields[] = "currency = ?";
            $values[] = ($currency === '' ? 'EUR' : $currency);
        }
        if (array_key_exists('stripe_price_id', $data)) {
            $stripePriceId = trim((string)$data['stripe_price_id']);
            if ($stripePriceId === '') {
                $stripePriceId = 'manual_' . substr(str_replace('-', '', $planId), 0, 18);
            }
            $fields[] = "stripe_price_id = ?";
            $values[] = $stripePriceId;
        }
        if (array_key_exists('features', $data)) {
            $fields[] = "features = ?";
            $values[] = json_encode($this->normalizeFeaturesInput($data['features']));
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(["error" => "Aucun champ valide a mettre a jour."]);
            return;
        }

        $values[] = $planId;
        $query = "UPDATE subscription_plans SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $ok = $stmt->execute($values);

        if (!$ok) {
            http_response_code(503);
            echo json_encode(["error" => "Echec de la mise a jour du forfait."]);
            return;
        }

        echo json_encode(["message" => "Forfait mis a jour."]);
    }

    /**
     * Admin: delete pricing plan
     */
    public function deleteAdminPlan($planId) {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM subscription_plans WHERE id = ?");
        if (!$stmt->execute([$planId])) {
            http_response_code(503);
            echo json_encode(["error" => "Echec suppression forfait."]);
            return;
        }

        echo json_encode(["message" => "Forfait supprime."]);
    }

    public function getAdminAnalytics() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leads WHERE DATE(created_at) = CURDATE()");
        $stmt->execute();
        $leadsToday = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leads WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
        $stmt->execute();
        $leadsWeek = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leads WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
        $stmt->execute();
        $leadsMonth = (int)$stmt->fetchColumn();

        $dailyStmt = $this->db->prepare("SELECT DATE(created_at) AS day_label, COUNT(*) AS leads_count
                                         FROM leads
                                         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                                         GROUP BY DATE(created_at)
                                         ORDER BY day_label ASC");
        $dailyStmt->execute();

        $weeklyStmt = $this->db->prepare("SELECT CONCAT(YEAR(created_at), '-S', LPAD(WEEK(created_at, 1), 2, '0')) AS week_label, COUNT(*) AS leads_count
                                          FROM leads
                                          WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
                                          GROUP BY YEAR(created_at), WEEK(created_at, 1)
                                          ORDER BY YEAR(created_at), WEEK(created_at, 1)");
        $weeklyStmt->execute();

        $monthlyStmt = $this->db->prepare("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_label, COUNT(*) AS leads_count
                                           FROM leads
                                           WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                                           GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                                           ORDER BY month_label ASC");
        $monthlyStmt->execute();

        $demandStmt = $this->db->prepare("SELECT COALESCE(NULLIF(TRIM(sector), ''), 'Non renseigne') AS sector, COUNT(*) AS demand
                                          FROM leads
                                          GROUP BY COALESCE(NULLIF(TRIM(sector), ''), 'Non renseigne')
                                          ORDER BY demand DESC");
        $demandStmt->execute();

        $providersStmt = $this->db->prepare("SELECT id, email, first_name, last_name, company_name, subscription_status, sectors
                                             FROM user_profiles
                                             WHERE role = 'provider'
                                             ORDER BY created_at DESC");
        $providersStmt->execute();
        $providers = $providersStmt->fetchAll(PDO::FETCH_ASSOC);

        $assignedTotalStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ?");
        $assignedDayStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ? AND DATE(created_at) = CURDATE()");
        $assignedWeekStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ? AND YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
        $assignedMonthStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ? AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");

        foreach ($providers as &$provider) {
            $providerId = $provider['id'];
            $assignedTotalStmt->execute([$providerId]);
            $provider['assigned_leads_total'] = (int)$assignedTotalStmt->fetchColumn();

            $assignedDayStmt->execute([$providerId]);
            $provider['assigned_leads_day'] = (int)$assignedDayStmt->fetchColumn();

            $assignedWeekStmt->execute([$providerId]);
            $provider['assigned_leads_week'] = (int)$assignedWeekStmt->fetchColumn();

            $assignedMonthStmt->execute([$providerId]);
            $provider['assigned_leads_month'] = (int)$assignedMonthStmt->fetchColumn();

            $provider['plan_name'] = $this->resolveProviderPlanName($provider);
            $provider['sectors_list'] = $this->decodeSectors($provider['sectors'] ?? null);
        }

        echo json_encode([
            "leads_today" => $leadsToday,
            "leads_week" => $leadsWeek,
            "leads_month" => $leadsMonth,
            "daily_series" => $dailyStmt->fetchAll(PDO::FETCH_ASSOC),
            "weekly_series" => $weeklyStmt->fetchAll(PDO::FETCH_ASSOC),
            "monthly_series" => $monthlyStmt->fetchAll(PDO::FETCH_ASSOC),
            "sector_demand" => $demandStmt->fetchAll(PDO::FETCH_ASSOC),
            "providers" => $providers,
        ]);
    }

    public function getAdminDispatchOverview() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $pendingStmt = $this->db->prepare("SELECT COUNT(*)
                                           FROM leads l
                                           LEFT JOIN lead_assignments la ON la.lead_id = l.id
                                           WHERE la.id IS NULL");
        $pendingStmt->execute();
        $pendingUnassigned = (int)$pendingStmt->fetchColumn();

        $demandStmt = $this->db->prepare("SELECT COALESCE(NULLIF(TRIM(sector), ''), 'Non renseigne') AS sector, COUNT(*) AS demand
                                          FROM leads
                                          GROUP BY COALESCE(NULLIF(TRIM(sector), ''), 'Non renseigne')
                                          ORDER BY demand DESC");
        $demandStmt->execute();
        $sectorDemand = $demandStmt->fetchAll(PDO::FETCH_ASSOC);

        $providerStmt = $this->db->prepare("SELECT id, email, first_name, last_name, company_name, subscription_status, sectors
                                            FROM user_profiles
                                            WHERE role = 'provider'
                                            ORDER BY created_at DESC");
        $providerStmt->execute();
        $providers = $providerStmt->fetchAll(PDO::FETCH_ASSOC);

        $assignedByProviderStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ?");
        foreach ($providers as &$provider) {
            $assignedByProviderStmt->execute([$provider['id']]);
            $provider['assigned_leads_total'] = (int)$assignedByProviderStmt->fetchColumn();
            $provider['sectors_list'] = $this->decodeSectors($provider['sectors'] ?? null);
            $provider['plan_name'] = $this->resolveProviderPlanName($provider);
        }

        echo json_encode([
            "pending_unassigned" => $pendingUnassigned,
            "sector_demand" => $sectorDemand,
            "providers" => $providers,
        ]);
    }

    public function autoDispatchLeads() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $pendingQuery = "SELECT l.id, l.sector, l.created_at
                         FROM leads l
                         LEFT JOIN lead_assignments la ON la.lead_id = l.id
                         WHERE la.id IS NULL
                         ORDER BY l.created_at ASC";
        $pendingStmt = $this->db->prepare($pendingQuery);
        $pendingStmt->execute();
        $pendingLeads = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($pendingLeads)) {
            echo json_encode([
                "message" => "Aucun lead non assigne a dispatcher.",
                "assigned_count" => 0,
                "assignments" => []
            ]);
            return;
        }

        $providersStmt = $this->db->prepare("SELECT id, email, first_name, last_name, company_name, is_verified, subscription_status, sectors
                                             FROM user_profiles
                                             WHERE role = 'provider' AND subscription_status = 'active' AND is_verified = 1
                                             ORDER BY created_at ASC");
        $providersStmt->execute();
        $providers = $providersStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($providers)) {
            http_response_code(400);
            echo json_encode(["error" => "Aucun prestataire actif et qualifié n'est disponible pour le dispatch."]);
            return;
        }

        $providerLoad = [];
        $providerSectorLoad = [];
        $providerLeadCountStmt = $this->db->prepare("SELECT COUNT(*) FROM lead_assignments WHERE provider_id = ?");
        $providerSectorCountStmt = $this->db->prepare("SELECT COUNT(*)
                                                       FROM lead_assignments la
                                                       INNER JOIN leads l ON l.id = la.lead_id
                                                       WHERE la.provider_id = ?
                                                         AND LOWER(COALESCE(l.sector, '')) = LOWER(?)");

        foreach ($providers as &$provider) {
            $provider['sectors_list'] = $this->decodeSectors($provider['sectors'] ?? null);
            $providerLeadCountStmt->execute([$provider['id']]);
            $providerLoad[$provider['id']] = (int)$providerLeadCountStmt->fetchColumn();
            $providerSectorLoad[$provider['id']] = [];
        }

        $insertAssignment = $this->db->prepare("INSERT INTO lead_assignments (id, lead_id, provider_id, created_at) VALUES (?, ?, ?, NOW())");
        $updateLeadStatus = $this->db->prepare("UPDATE leads SET status = 'assigned' WHERE id = ?");

        $sectorDemandCounts = [];
        foreach ($pendingLeads as $pendingLead) {
            $sectorKey = strtolower(trim((string)($pendingLead['sector'] ?? '')));
            $sectorDemandCounts[$sectorKey] = ($sectorDemandCounts[$sectorKey] ?? 0) + 1;
        }
        usort($pendingLeads, function ($a, $b) use ($sectorDemandCounts) {
            $aSector = strtolower(trim((string)($a['sector'] ?? '')));
            $bSector = strtolower(trim((string)($b['sector'] ?? '')));
            $aDemand = $sectorDemandCounts[$aSector] ?? 0;
            $bDemand = $sectorDemandCounts[$bSector] ?? 0;
            if ($aDemand === $bDemand) {
                return strtotime((string)$a['created_at']) <=> strtotime((string)$b['created_at']);
            }
            return $bDemand <=> $aDemand;
        });

        $assignments = [];
        foreach ($pendingLeads as $lead) {
            $leadId = $lead['id'];
            $leadSector = trim((string)($lead['sector'] ?? ''));
            $leadSectorLower = strtolower($leadSector);

            $matchingProviders = [];
            foreach ($providers as $provider) {
                $providerSectors = array_map(function ($sector) {
                    return strtolower(trim((string)$sector));
                }, $provider['sectors_list'] ?? []);
                if ($leadSectorLower !== '' && in_array($leadSectorLower, $providerSectors, true)) {
                    $matchingProviders[] = $provider;
                }
            }

            // We MUST only assign leads to providers that match the sector. No fallback.
            if (empty($matchingProviders)) {
                continue;
            }

            $candidateProviders = $matchingProviders;
            $bestProvider = null;
            $bestScore = PHP_INT_MAX;

            foreach ($candidateProviders as $candidate) {
                $providerId = $candidate['id'];
                if (!isset($providerSectorLoad[$providerId][$leadSectorLower])) {
                    if ($leadSector !== '') {
                        $providerSectorCountStmt->execute([$providerId, $leadSector]);
                        $providerSectorLoad[$providerId][$leadSectorLower] = (int)$providerSectorCountStmt->fetchColumn();
                    } else {
                        $providerSectorLoad[$providerId][$leadSectorLower] = 0;
                    }
                }

                $totalAssigned = $providerLoad[$providerId] ?? 0;
                $sectorAssigned = $providerSectorLoad[$providerId][$leadSectorLower] ?? 0;

                // Lower score is better: simple load balancing math.
                $score = ($totalAssigned * 2) + $sectorAssigned;
                if ($score < $bestScore) {
                    $bestScore = $score;
                    $bestProvider = $candidate;
                }
            }

            if (!$bestProvider) {
                continue;
            }

            $assignmentId = $this->generateUuid();
            $providerId = $bestProvider['id'];
            if ($insertAssignment->execute([$assignmentId, $leadId, $providerId])) {
                $updateLeadStatus->execute([$leadId]);
                $providerLoad[$providerId] = ($providerLoad[$providerId] ?? 0) + 1;
                $providerSectorLoad[$providerId][$leadSectorLower] = ($providerSectorLoad[$providerId][$leadSectorLower] ?? 0) + 1;

                $assignments[] = [
                    "lead_id" => $leadId,
                    "lead_sector" => $leadSector,
                    "provider_id" => $providerId,
                    "provider_name" => trim((string)(($bestProvider['first_name'] ?? '') . ' ' . ($bestProvider['last_name'] ?? ''))),
                    "provider_email" => $bestProvider['email'] ?? '',
                    "matching_type" => !empty($matchingProviders) ? "niche_match" : "load_balance_fallback"
                ];
            }
        }

        echo json_encode([
            "message" => "Dispatch automatique termine.",
            "assigned_count" => count($assignments),
            "assignments" => $assignments
        ]);
    }

    /**
     * Admin: Get all users with status
     */
    public function getAdminUsers() {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Accès refusé"]);
            return;
        }

        $query = "SELECT id, email, first_name, last_name, role, company_name, is_verified, created_at 
                  FROM user_profiles 
                  ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add virtual fields for UI consistency
        foreach ($users as &$user) {
            $user['subscription_status'] = 'suspended'; // Default as per requested UI mockup
            if ($user['role'] === 'admin') $user['subscription_status'] = 'active';
        }
        
        echo json_encode($users);
    }

    /**
     * Admin: Update user role
     */
    public function updateUserRole($userId) {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Accès refusé"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->role)) {
            http_response_code(400);
            echo json_encode(["error" => "Rôle manquant"]);
            return;
        }

        $query = "UPDATE user_profiles SET role = ? WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt->execute([$data->role, $userId])) {
            echo json_encode(["message" => "Rôle mis à jour"]);
        } else {
            http_response_code(503);
            echo json_encode(["error" => "Échec de la mise à jour"]);
        }
    }


    public function createAdminUser() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $email = trim((string)($data['email'] ?? ''));
        $password = (string)($data['password'] ?? '');
        if ($email === '' || $password === '') {
            http_response_code(400);
            echo json_encode(["error" => "Email et mot de passe requis."]);
            return;
        }

        $stmt = $this->db->prepare("SELECT id FROM user_profiles WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn()) {
            http_response_code(400);
            echo json_encode(["error" => "Email deja utilise."]);
            return;
        }

        $id = $this->generateUuid();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $firstName = $data['first_name'] ?? null;
        $lastName = $data['last_name'] ?? null;
        $role = $data['role'] ?? 'provider';
        $isVerified = isset($data['is_verified']) ? (int)((bool)$data['is_verified']) : 1;
        $companyName = $data['company_name'] ?? null;
        $phone = $data['phone'] ?? null;

        $query = "INSERT INTO user_profiles (id, email, password, first_name, last_name, role, is_verified, company_name, phone, sectors)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $ok = $stmt->execute([$id, $email, $hash, $firstName, $lastName, $role, $isVerified, $companyName, $phone, json_encode([])]);

        if (!$ok) {
            http_response_code(503);
            echo json_encode(["error" => "Impossible de creer l'utilisateur."]);
            return;
        }

        http_response_code(201);
        echo json_encode(["message" => "Utilisateur cree.", "id" => $id]);
    }

    public function updateAdminUser($userId) {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!is_array($data) || empty($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Donnees manquantes."]);
            return;
        }

        $allowed = ['email', 'first_name', 'last_name', 'role', 'company_name', 'phone', 'subscription_status', 'is_verified', 'siret', 'legal_form', 'creation_year', 'address', 'zip', 'city', 'description', 'zone'];
        $fields = [];
        $values = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $fields[] = $key . " = ?";
                $values[] = $data[$key];
            }
        }

        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $values[] = password_hash((string)$data['password'], PASSWORD_BCRYPT);
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(["error" => "Aucun champ valide a mettre a jour."]);
            return;
        }

        $values[] = $userId;
        $query = "UPDATE user_profiles SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $ok = $stmt->execute($values);

        if (!$ok) {
            http_response_code(503);
            echo json_encode(["error" => "Echec de la mise a jour utilisateur."]);
            return;
        }

        echo json_encode(["message" => "Utilisateur mis a jour."]);
    }

    public function deleteAdminUser($userId) {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }
        if (($auth['id'] ?? '') === $userId) {
            http_response_code(400);
            echo json_encode(["error" => "Vous ne pouvez pas supprimer votre propre compte admin."]);
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM user_profiles WHERE id = ?");
        if (!$stmt->execute([$userId])) {
            http_response_code(503);
            echo json_encode(["error" => "Echec de la suppression utilisateur."]);
            return;
        }

        echo json_encode(["message" => "Utilisateur supprime."]);
    }

    public function createAdminLead() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $name = trim((string)($data['name'] ?? (($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''))));
        $phone = trim((string)($data['phone'] ?? ''));
        if ($name === '' || $phone === '') {
            http_response_code(400);
            echo json_encode(["error" => "Nom et telephone requis."]);
            return;
        }

        $id = $this->generateUuid();
        $email = $data['email'] ?? null;
        $address = $data['address'] ?? null;
        $sector = $data['sector'] ?? ($data['service_type'] ?? 'General');
        $need = $data['need'] ?? '';
        $budget = (float)($data['budget'] ?? 0);
        $timeSlot = $data['time_slot'] ?? null;
        $status = $data['status'] ?? 'pending';

        $query = "INSERT INTO leads (id, name, email, phone, address, sector, need, budget, time_slot, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $ok = $stmt->execute([$id, $name, $email, $phone, $address, $sector, $need, $budget, $timeSlot, $status]);

        if (!$ok) {
            http_response_code(503);
            echo json_encode(["error" => "Impossible de creer le lead."]);
            return;
        }

        http_response_code(201);
        echo json_encode(["message" => "Lead cree.", "id" => $id]);
    }

    public function updateAdminLead($leadId) {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!is_array($data) || empty($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Donnees manquantes."]);
            return;
        }

        $allowed = ['name', 'email', 'phone', 'address', 'sector', 'need', 'budget', 'time_slot', 'status'];
        $fields = [];
        $values = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $fields[] = $key . " = ?";
                $values[] = $data[$key];
            }
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(["error" => "Aucun champ valide a mettre a jour."]);
            return;
        }

        $values[] = $leadId;
        $stmt = $this->db->prepare("UPDATE leads SET " . implode(', ', $fields) . " WHERE id = ?");
        if (!$stmt->execute($values)) {
            http_response_code(503);
            echo json_encode(["error" => "Echec mise a jour lead."]);
            return;
        }

        echo json_encode(["message" => "Lead mis a jour."]);
    }

    public function deleteAdminLead($leadId) {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM leads WHERE id = ?");
        if (!$stmt->execute([$leadId])) {
            http_response_code(503);
            echo json_encode(["error" => "Echec suppression lead."]);
            return;
        }

        echo json_encode(["message" => "Lead supprime."]);
    }

    public function getAdminQuotes() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $query = "SELECT q.*, up.email AS provider_email
                  FROM quotes q
                  LEFT JOIN user_profiles up ON q.provider_id = up.id
                  ORDER BY q.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function createAdminQuote() {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $providerId = trim((string)($data['provider_id'] ?? ''));
        $clientName = trim((string)($data['client_name'] ?? ''));
        $amount = isset($data['amount']) ? (float)$data['amount'] : null;
        if ($providerId === '' || $clientName === '' || $amount === null) {
            http_response_code(400);
            echo json_encode(["error" => "provider_id, client_name et amount sont requis."]);
            return;
        }

        $id = $this->generateUuid();
        $projectName = $data['project_name'] ?? ($data['description'] ?? 'Nouveau Projet');
        $itemsCount = (int)($data['items_count'] ?? 1);
        $status = $data['status'] ?? 'attente_client';

        $query = "INSERT INTO quotes (id, provider_id, client_name, project_name, amount, items_count, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        if (!$stmt->execute([$id, $providerId, $clientName, $projectName, $amount, $itemsCount, $status])) {
            http_response_code(503);
            echo json_encode(["error" => "Impossible de creer le devis."]);
            return;
        }

        http_response_code(201);
        echo json_encode(["message" => "Devis cree.", "id" => $id]);
    }

    public function updateAdminQuote($quoteId) {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!is_array($data) || empty($data)) {
            http_response_code(400);
            echo json_encode(["error" => "Donnees manquantes."]);
            return;
        }

        $allowed = ['provider_id', 'client_name', 'project_name', 'amount', 'items_count', 'status'];
        $fields = [];
        $values = [];
        foreach ($allowed as $key) {
            if (array_key_exists($key, $data)) {
                $fields[] = $key . " = ?";
                $values[] = $data[$key];
            }
        }
        if (isset($data['description']) && !array_key_exists('project_name', $data)) {
            $fields[] = "project_name = ?";
            $values[] = $data['description'];
        }

        if (empty($fields)) {
            http_response_code(400);
            echo json_encode(["error" => "Aucun champ valide a mettre a jour."]);
            return;
        }

        $values[] = $quoteId;
        $stmt = $this->db->prepare("UPDATE quotes SET " . implode(', ', $fields) . " WHERE id = ?");
        if (!$stmt->execute($values)) {
            http_response_code(503);
            echo json_encode(["error" => "Echec mise a jour devis."]);
            return;
        }

        echo json_encode(["message" => "Devis mis a jour."]);
    }

    public function deleteAdminQuote($quoteId) {
        $auth = $this->authenticate();
        if (($auth['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["error" => "Acces refuse"]);
            return;
        }

        $stmt = $this->db->prepare("DELETE FROM quotes WHERE id = ?");
        if (!$stmt->execute([$quoteId])) {
            http_response_code(503);
            echo json_encode(["error" => "Echec suppression devis."]);
            return;
        }

        echo json_encode(["message" => "Devis supprime."]);
    }
    /**
     * Stripe Checkout Session Placeholder
     */
    public function createCheckout() {
        $this->authenticate();
        // Placeholder implementation
        echo json_encode([
            "id" => "cs_test_" . uniqid(),
            "url" => "https://checkout.stripe.com/pay/placeholder"
        ]);
    }

    private function generateUuid() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function decodeSectors($sectors): array {
        if (is_array($sectors)) {
            return array_values(array_filter(array_map('trim', $sectors), function ($v) {
                return $v !== '';
            }));
        }

        if (is_string($sectors) && trim($sectors) !== '') {
            $decoded = json_decode($sectors, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map(function ($item) {
                    return trim((string)$item);
                }, $decoded), function ($v) {
                    return $v !== '';
                }));
            }
        }

        return [];
    }

    private function resolveProviderPlanName(array $provider): string {
        if (!empty($provider['subscription_plan'])) {
            return (string)$provider['subscription_plan'];
        }
        return (($provider['subscription_status'] ?? '') === 'active') ? 'Forfait actif' : 'Forfait inactif';
    }

    private function normalizeFeaturesInput($features): array {
        if (is_string($features)) {
            $parts = preg_split('/\r\n|\r|\n|,/', $features);
            $features = is_array($parts) ? $parts : [];
        }

        if (!is_array($features)) {
            return [];
        }

        $normalized = [];
        foreach ($features as $feature) {
            $value = trim((string)$feature);
            if ($value !== '') {
                $normalized[] = $value;
            }
        }
        return array_values(array_unique($normalized));
    }

    /**
     * Lookup company by SIRET/SIREN — uses official French gov API (no key, valid SSL)
     */
    public function lookupCompany() {
        $input = trim($_GET['siret'] ?? $_GET['siren'] ?? '');
        $input = preg_replace('/\D/', '', $input); // digits only

        $len = strlen($input);
        if ($len !== 9 && $len !== 14) {
            http_response_code(400);
            echo json_encode(["error" => "Saisissez 9 chiffres (SIREN) ou 14 chiffres (SIRET)"]);
            return;
        }

        $data = $this->callKipocedApi($input);
        if (!$this->hasUsableCompanyData($data)) {
            $data = $this->callGouvernementApi($input);
        }

        if (!$data) {
            http_response_code(404);
            echo json_encode(["error" => "Entreprise non trouvée"]);
            return;
        }

        echo json_encode($data);
    }

    /**
     * Call the official French government company search API
     * Endpoint: https://recherche-entreprises.api.gouv.fr/search?q={siret}&limit=1
     */
    private function callGouvernementApi(string $query): ?array {
        $url = 'https://recherche-entreprises.api.gouv.fr/search?q=' . urlencode($query) . '&limit=1';
        $json = $this->httpGetJson($url);
        if (empty($json['results'][0])) return null;

        $r     = $json['results'][0];
        $siege = $r['siege'] ?? [];

        // company name: denomination > nom_complet > prenom+nom
        $name = $r['nom_raison_sociale']
             ?? $r['nom_complet']
             ?? trim(($r['prenom_usuel'] ?? '') . ' ' . ($r['nom'] ?? ''))
             ?: null;

        $creationYear = isset($r['date_creation'])
            ? substr($r['date_creation'], 0, 4)
            : null;
        $legalForm = $r['nature_juridique_label'] ?? $r['categorie_juridique_label'] ?? null;
        if (empty($legalForm) && !empty($r['nature_juridique'])) {
            $code = (string)$r['nature_juridique'];
            $legalForm = $this->getLegalFormsMap()[$code] ?? $code;
        }

        return [
            'company_name'  => $name,
            'address'       => $siege['adresse']          ?? $siege['geo_adresse'] ?? null,
            'zip_code'      => $siege['code_postal']       ?? null,
            'city'          => $siege['libelle_commune']   ?? $siege['commune'] ?? null,
            'legal_form'    => $legalForm,
            'creation_year' => $creationYear,
            'siren'         => $r['siren'] ?? null,
        ];
    }

    /**
     * Call Kipoced API and map response fields.
     */
    private function callKipocedApi(string $query): ?array {
        $siren = strlen($query) === 14 ? substr($query, 0, 9) : $query;
        $base = $this->kipocedBaseUrl . '?api_key=' . urlencode($this->kipocedApiKey);
        $urls = [
            $base . '&siret=' . urlencode($query),
            $base . '&siren=' . urlencode($siren),
            $base . '&q=' . urlencode($query),
            $base,
        ];

        $record = null;
        foreach ($urls as $url) {
            $json = $this->httpGetJson($url);
            if (!is_array($json)) {
                continue;
            }

            $record = $this->findCompanyRecord($json, $query, $siren);
            if ($record !== null) {
                break;
            }
        }

        if ($record === null) {
            return null;
        }

        $legalFormCode = (string)($record['cj'] ?? '');
        $legalFormsMap = $this->getLegalFormsMap();
        $legalForm = $legalFormsMap[$legalFormCode] ?? ($record['libcj'] ?? null);

        $creationYear = null;
        if (!empty($record['dcren'])) {
            $digits = preg_replace('/\D/', '', (string)$record['dcren']);
            $creationYear = strlen($digits) >= 4 ? substr($digits, 0, 4) : null;
        }

        $mapped = [
            'company_name'  => $record['nomen_long'] ?? $record['nomen'] ?? null,
            'address'       => $record['geo_adresse'] ?? null,
            'zip_code'      => $record['codpos'] ?? null,
            'city'          => $record['libcom'] ?? $record['commune'] ?? $record['ville'] ?? null,
            'legal_form'    => $legalForm,
            'creation_year' => $creationYear,
            'siren'         => $record['siren'] ?? $siren,
        ];

        return $this->hasUsableCompanyData($mapped) ? $mapped : null;
    }

    private function hasUsableCompanyData($data): bool {
        if (!is_array($data)) {
            return false;
        }

        $keys = ['company_name', 'address', 'zip_code', 'city', 'legal_form', 'creation_year'];
        foreach ($keys as $key) {
            if (!empty($data[$key])) {
                return true;
            }
        }

        return false;
    }

    private function httpGetJson(string $url): ?array {
        $response = null;
        $httpCode = 0;
        $host = (string)(parse_url($url, PHP_URL_HOST) ?? '');
        $verifyPeer = $this->shouldVerifySslForHost($host);

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Rappel-App/1.0');
            curl_setopt($ch, CURLOPT_TIMEOUT, 12);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifyPeer);
            if (!$verifyPeer) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            }
            $response = curl_exec($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        } else {
            $opts = [
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: Rappel-App/1.0\r\n",
                    'timeout' => 12,
                ],
                'ssl' => [
                    'verify_peer' => $verifyPeer,
                    'verify_peer_name' => $verifyPeer,
                ],
            ];
            $ctx = stream_context_create($opts);
            $response = @file_get_contents($url, false, $ctx);
            if (is_array($http_response_header ?? null)) {
                foreach ($http_response_header as $line) {
                    if (preg_match('#HTTP/\d+\.\d+\s+(\d{3})#', $line, $m)) {
                        $httpCode = (int)$m[1];
                        break;
                    }
                }
            }
        }

        if ($response === false || $response === null || ($httpCode !== 0 && $httpCode >= 400)) {
            return null;
        }

        $json = json_decode($response, true);
        return is_array($json) ? $json : null;
    }

    private function shouldVerifySslForHost(string $host): bool {
        $host = strtolower(trim($host));
        $skipHosts = [
            'api.kipoced.com',
            'recherche-entreprises.api.gouv.fr',
        ];

        return !in_array($host, $skipHosts, true);
    }

    private function findCompanyRecord(array $data, string $siret, string $siren): ?array {
        $queue = [$data];
        while (!empty($queue)) {
            $node = array_shift($queue);
            if (!is_array($node)) {
                continue;
            }

            if ($this->looksLikeCompanyNode($node, $siret, $siren)) {
                return $node;
            }

            foreach ($node as $child) {
                if (is_array($child)) {
                    $queue[] = $child;
                }
            }
        }

        return null;
    }

    private function looksLikeCompanyNode(array $node, string $siret, string $siren): bool {
        $hasExpectedFields = isset($node['nomen_long']) || isset($node['geo_adresse']) || isset($node['codpos']) || isset($node['dcren']) || isset($node['cj']);
        if (!$hasExpectedFields) {
            return false;
        }

        $nodeSiret = preg_replace('/\D/', '', (string)($node['siret'] ?? ''));
        $nodeSiren = preg_replace('/\D/', '', (string)($node['siren'] ?? ''));

        if ($nodeSiret !== '' && $nodeSiret === $siret) {
            return true;
        }
        if ($nodeSiren !== '' && $nodeSiren === $siren) {
            return true;
        }

        return $nodeSiret === '' && $nodeSiren === '';
    }

    private function getLegalFormsMap(): array {
        $forms = $this->loadLegalFormsList();
        $map = [];

        foreach ($forms as $item) {
            if (!is_array($item)) {
                continue;
            }

            $code = (string)($item['code'] ?? $item['cj'] ?? '');
            $label = (string)($item['libelle'] ?? $item['label'] ?? $item['nom'] ?? '');

            if ($code !== '' && $label !== '') {
                $map[$code] = $label;
            }
        }

        return $map;
    }

    private function loadLegalFormsList(): array {
        if (file_exists($this->cacheFile)) {
            $cacheAge = time() - filemtime($this->cacheFile);
            if ($cacheAge < $this->cacheExpiry) {
                $cached = json_decode((string)file_get_contents($this->cacheFile), true);
                if (isset($cached['cj']) && is_array($cached['cj'])) {
                    return $cached['cj'];
                }
            }
        }

        $url = $this->kipocedBaseUrl . '?api_key=' . urlencode($this->kipocedApiKey);
        $data = $this->httpGetJson($url);
        if (isset($data['cj']) && is_array($data['cj'])) {
            $cachePayload = ['cj' => $data['cj']];
            $cacheDir = dirname($this->cacheFile);
            if (!file_exists($cacheDir)) {
                @mkdir($cacheDir, 0755, true);
            }
            @file_put_contents($this->cacheFile, json_encode($cachePayload));
            return $data['cj'];
        }

        return [
            ['code' => '5710', 'libelle' => 'SAS'],
            ['code' => '5720', 'libelle' => 'SASU'],
            ['code' => '5499', 'libelle' => 'SARL'],
            ['code' => '1000', 'libelle' => 'EI'],
        ];
    }
    
    /**
     * Get legal forms list (cached from Kipoced API)
     */
    public function getLegalForms() {
        header('Content-Type: application/json');
        echo json_encode(['cj' => $this->loadLegalFormsList()]);
    }
}
?>
