<?php
require_once 'config/db.php';
require_once 'models/Lead.php';
require_once 'models/Quote.php';
require_once 'models/User.php';
require_once 'models/Invoice.php';
require_once 'utils/JwtUtils.php';

class CompanyController {
    
    private $db;
    private $lead;
    private $quote;
    private $user;
    private $invoice;
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
        $this->invoice = new Invoice($this->db);
        $this->jwt = new JwtUtils();
    }

    /**
     * Middleware simple pour vérifier le token
     */
    public function authenticate()
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
        // Return ALL quotes for providers as requested
        $stmt = $this->quote->readAll();
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
        $this->quote->lead_id = $data->lead_id ?? null;

        if ($this->quote->create()) {
            // Also update lead status to quote_sent
            if (!empty($data->lead_id)) {
                $updLead = $this->db->prepare("UPDATE leads SET status = 'quote_sent' WHERE id = ?");
                $updLead->execute([$data->lead_id]);
            }
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
        $this->quote->lead_id = $data->lead_id ?? $this->quote->lead_id;

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
        $providerId = $auth['id'];
        
        // 1. Basic Lead Stats (Specific to Provider)
        $leadStatsStmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN l.status = 'pending' THEN 1 ELSE 0 END) as pending
            FROM leads l
            INNER JOIN lead_assignments la ON l.id = la.lead_id
            WHERE la.provider_id = ?
        ");
        $leadStatsStmt->execute([$providerId]);
        $leadStats = $leadStatsStmt->fetch(PDO::FETCH_ASSOC);
        
        $totalLeads = (int)($leadStats['total'] ?? 0);
        $pendingLeadsCount = (int)($leadStats['pending'] ?? 0);
        $closedLeads = $totalLeads - $pendingLeadsCount;

        // 2. Quote Stats (Specific to Provider)
        $quoteStatsStmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status IN ('accepted', 'signe') THEN 1 ELSE 0 END) as signed,
                SUM(amount) as total_amount
            FROM quotes
            WHERE provider_id = ?
        ");
        $quoteStatsStmt->execute([$providerId]);
        $quoteStats = $quoteStatsStmt->fetch(PDO::FETCH_ASSOC);
        
        $quotesCount = (int)($quoteStats['total'] ?? 0);
        $signedQuotesCount = (int)($quoteStats['signed'] ?? 0);
        $totalAmount = (float)($quoteStats['total_amount'] ?? 0);

        $conversionRate = $totalLeads > 0 ? round(($signedQuotesCount / $totalLeads) * 100, 1) : 0;

        // 3. Growth Calculation (Revenue month over month)
        $currentMonthRevenueStmt = $this->db->prepare("
            SELECT SUM(amount) as amount 
            FROM quotes 
            WHERE provider_id = ? AND status IN ('accepted', 'signe') 
            AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())
        ");
        $currentMonthRevenueStmt->execute([$providerId]);
        $currentMonthRevenue = (float)($currentMonthRevenueStmt->fetchColumn() ?: 0);

        $lastMonthRevenueStmt = $this->db->prepare("
            SELECT SUM(amount) as amount 
            FROM quotes 
            WHERE provider_id = ? AND status IN ('accepted', 'signe') 
            AND MONTH(created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) 
            AND YEAR(created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)
        ");
        $lastMonthRevenueStmt->execute([$providerId]);
        $lastMonthRevenue = (float)($lastMonthRevenueStmt->fetchColumn() ?: 0);

        $revenueGrowth = 0;
        if ($lastMonthRevenue > 0) {
            $revenueGrowth = round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100);
        } elseif ($currentMonthRevenue > 0) {
            $revenueGrowth = 100; // 100% growth if we had nothing last month
        }

        // 4. Monthly Activity (last 6 months, specific to provider)
        $monthlyStmt = $this->db->prepare("
            SELECT DATE_FORMAT(l.created_at, '%Y-%m') as month_key, 
                   COUNT(*) as count 
            FROM leads l
            INNER JOIN lead_assignments la ON l.id = la.lead_id
            WHERE la.provider_id = ?
            GROUP BY month_key 
            ORDER BY month_key DESC 
            LIMIT 6
        ");
        $monthlyStmt->execute([$providerId]);
        $monthlyActivity = array_reverse($monthlyStmt->fetchAll(PDO::FETCH_ASSOC));

        // 5. Sector Breakdown (Specific to provider)
        $sectorStmt = $this->db->prepare("
            SELECT l.sector, 
                   COUNT(*) as total,
                   SUM(CASE WHEN l.status IN ('processed', 'confirmé', 'completed') THEN 1 ELSE 0 END) as processed
            FROM leads l
            INNER JOIN lead_assignments la ON l.id = la.lead_id
            WHERE la.provider_id = ?
            GROUP BY l.sector
            ORDER BY total DESC
        ");
        $sectorStmt->execute([$providerId]);
        $sectorImpact = $sectorStmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "totalLeads" => $totalLeads, 
            "pendingLeads" => $pendingLeadsCount,
            "closedLeads" => $closedLeads,
            "totalQuotes" => $quotesCount,
            "signedQuotes" => $signedQuotesCount,
            "totalAmount" => $totalAmount,
            "conversionRate" => $conversionRate,
            "revenueGrowth" => $revenueGrowth,
            "analytics" => [
                "monthlyActivity" => $monthlyActivity,
                "sectorImpact" => $sectorImpact
            ]
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
                         up.company_name AS assigned_provider_company,
                         cp.first_name AS client_first_name,
                         cp.last_name AS client_last_name,
                         cp.phone AS client_profile_phone,
                         cp.address AS client_profile_address,
                         cp.city AS client_profile_city,
                         cp.zip AS client_profile_zip
                  FROM leads l
                  LEFT JOIN lead_assignments la ON l.id = la.lead_id
                  LEFT JOIN user_profiles up ON la.provider_id = up.id
                  LEFT JOIN user_profiles cp ON l.user_id = cp.id
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

        $stmt = $this->db->prepare("SELECT COALESCE(SUM(amount), 0) FROM quotes WHERE status = 'signe' AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
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
     * Get invoices for the authenticated provider
     */
    public function getInvoices() {
        $auth = $this->authenticate();
        $stmt = $this->invoice->readByProvider($auth['id']);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($invoices);
    }

    /**
     * Get a single invoice for the authenticated provider
     */
    public function getInvoice($id) {
        $auth = $this->authenticate();
        $invoiceData = $this->invoice->readOne($id);

        if (!$invoiceData || $invoiceData['provider_id'] !== $auth['id']) {
            http_response_code(404);
            echo json_encode(["error" => "Facture non trouvée."]);
            return;
        }

        echo json_encode($invoiceData);
    }

    /**
     * Send an invoice by email to the authenticated provider
     */
    public function sendInvoiceEmail($id) {
        $auth = $this->authenticate();
        $invoiceData = $this->invoice->readOne($id);

        if (!$invoiceData || $invoiceData['provider_id'] !== $auth['id']) {
            http_response_code(404);
            echo json_encode(["error" => "Facture non trouvée."]);
            return;
        }

        require_once 'utils/Mailer.php';
        $mailer = new Mailer();
        
        // We use the email from the auth token or fetch from DB if needed
        // Assuming $auth has 'email' and 'name'
        $to_email = $auth['email'] ?? null;
        $to_name = $auth['name'] ?? 'Prestataire';

        if (!$to_email) {
            // Fallback: fetch user details from DB
            require_once 'models/User.php';
            $user = new User($this->db);
            $user->id = $auth['id'];
            $userData = $user->readOne();
            if ($userData) {
                $to_email = $userData['email'];
                $to_name = ($userData['first_name'] ?? '') . ' ' . ($userData['last_name'] ?? '');
            }
        }

        if (!$to_email) {
            http_response_code(400);
            echo json_encode(["error" => "Email du destinataire introuvable."]);
            return;
        }

        if ($mailer->sendInvoiceEmail($to_email, trim($to_name), $invoiceData)) {
            echo json_encode(["message" => "La facture a été envoyée par email avec succès."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de l'envoi de l'email."]);
        }
    }

    /**
     * Get dashboard stats for the authenticated client
     */
    public function getClientDashboardStats() {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'client') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        // Active requests (status = pending/assigned)
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leads WHERE user_id = ? AND status IN ('pending', 'assigned')");
        $stmt->execute([$auth['id']]);
        $activeRequests = $stmt->fetchColumn();

        // Pending quotes
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM quotes q 
                                   INNER JOIN leads l ON q.lead_id = l.id 
                                   WHERE l.user_id = ? AND q.status = 'attente_client'");
        $stmt->execute([$auth['id']]);
        $pendingQuotes = $stmt->fetchColumn();

        // Completed interventions
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM leads WHERE user_id = ? AND status = 'completed'");
        $stmt->execute([$auth['id']]);
        $completedInterventions = $stmt->fetchColumn();

        echo json_encode([
            "active_requests" => $activeRequests,
            "pending_quotes" => $pendingQuotes,
            "completed_interventions" => $completedInterventions
        ]);
    }

    /**
     * Delete client account and all related data (GDPR Control)
     */
    public function deleteClientAccount() {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'client') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $this->db->beginTransaction();
        try {
            // Delete user profile (cascades to leads and quotes if FKs are set correctly)
            // Manual step just in case FKs are not restrictive/cascading
            $stmt = $this->db->prepare("DELETE FROM leads WHERE user_id = ?");
            $stmt->execute([$auth['id']]);

            $stmt = $this->db->prepare("DELETE FROM user_profiles WHERE id = ?");
            $stmt->execute([$auth['id']]);
            
            $this->db->commit();
            echo json_encode(["message" => "Compte et données supprimés définitivement."]);
        } catch (Exception $e) {
            $this->db->rollBack();
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de la suppression du compte: " . $e->getMessage()]);
        }
    }

    /**
     * Export all data related to the client (GDPR Portability)
     */
    public function exportClientData() {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'client') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        try {
            // 1. Profile
            $stmt = $this->db->prepare("SELECT id, email, first_name, last_name, phone, role, created_at FROM user_profiles WHERE id = ?");
            $stmt->execute([$auth['id']]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2. Leads (Requests)
            $stmt = $this->db->prepare("SELECT id, name, phone, email, sector, need, time_slot, status, address, created_at FROM leads WHERE user_id = ?");
            $stmt->execute([$auth['id']]);
            $leads = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 3. Quotes related to those leads
            $quotes = [];
            if (!empty($leads)) {
                $leadIds = array_column($leads, 'id');
                $placeholders = implode(',', array_fill(0, count($leadIds), '?'));
                $stmt = $this->db->prepare("SELECT id, lead_id, provider_id, client_name, project_name, amount, status, created_at FROM quotes WHERE lead_id IN ($placeholders)");
                $stmt->execute($leadIds);
                $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $export = [
                "generated_at" => date('Y-m-d H:i:s'),
                "profile" => $profile,
                "leads" => $leads,
                "quotes" => $quotes
            ];

            echo json_encode($export);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de l'export des données: " . $e->getMessage()]);
        }
    }

    public function getClientQuotes() {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'client') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $stmt = $this->quote->readByClient($auth['id']);
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($quotes);
    }

    public function acceptQuote($id) {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'client') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $this->quote->id = $id;
        // Verify quote belongs to client
        $stmt = $this->quote->readByClient($auth['id']);
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $owned = false;
        foreach($quotes as $q) {
            if ($q['id'] === $id) {
                $owned = true;
                break;
            }
        }

        if (!$owned) {
            http_response_code(404);
            echo json_encode(['error' => 'Devis non trouvé']);
            return;
        }

        // Update status to 'signe'
        $query = "UPDATE quotes SET status = 'signe' WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt->execute([$id])) {
            // Also update lead status to confirmed
            $quoteData = $this->db->prepare("SELECT lead_id FROM quotes WHERE id = ?");
            $quoteData->execute([$id]);
            $leadId = $quoteData->fetchColumn();
            if ($leadId) {
                $updLead = $this->db->prepare("UPDATE leads SET status = 'confirmé' WHERE id = ?");
                $updLead->execute([$leadId]);
            }
            echo json_encode(["message" => "Devis accepté avec succès"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors de l'acceptation"]);
        }
    }

    public function refuseQuote($id) {
        $auth = $this->authenticate();
        if ($auth['role'] !== 'client') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        // Similar verification as acceptQuote
        $this->quote->id = $id;
        $stmt = $this->quote->readByClient($auth['id']);
        $quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $owned = false;
        foreach($quotes as $q) {
            if ($q['id'] === $id) {
                $owned = true;
                break;
            }
        }

        if (!$owned) {
            http_response_code(404);
            echo json_encode(['error' => 'Devis non trouvé']);
            return;
        }

        $query = "UPDATE quotes SET status = 'refuse' WHERE id = ?";
        $stmt = $this->db->prepare($query);
        if ($stmt->execute([$id])) {
            echo json_encode(["message" => "Devis refusé"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Erreur lors du refus"]);
        }
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
        $monthlyRaw = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fill gaps for the last 12 months
        $monthlySeries = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthDate = new DateTime();
            $monthDate->modify("-$i months");
            $label = $monthDate->format('Y-m');
            
            $found = false;
            foreach ($monthlyRaw as $row) {
                if ($row['month_label'] === $label) {
                    $monthlySeries[] = $row;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $monthlySeries[] = ['month_label' => $label, 'leads_count' => 0];
            }
        }

        $demandStmt = $this->db->prepare("SELECT COALESCE(NULLIF(TRIM(sector), ''), 'Non renseigne') AS sector, 
                                                 COUNT(*) AS demand,
                                                 SUM(CASE WHEN status IN ('processed', 'confirmé', 'closed') THEN 1 ELSE 0 END) as processed
                                          FROM leads
                                          GROUP BY COALESCE(NULLIF(TRIM(sector), ''), 'Non renseigne')
                                          ORDER BY demand DESC");
        $demandStmt->execute();

        // Status Distribution (Global)
        $distStmt = $this->db->prepare("SELECT status, COUNT(*) as count FROM leads GROUP BY status");
        $distStmt->execute();
        $distribution = $distStmt->fetchAll(PDO::FETCH_ASSOC);

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
            "monthly_series" => $monthlySeries,
            "sector_demand" => $demandStmt->fetchAll(PDO::FETCH_ASSOC),
            "distribution" => $distribution,
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

        $providersStmt = $this->db->prepare("SELECT id, email, first_name, last_name, company_name, is_verified, subscription_status, sectors, lead_credits
                                             FROM user_profiles
                                             WHERE role = 'provider' AND subscription_status = 'active' AND is_verified = 1 AND lead_credits > 0
                                             ORDER BY created_at ASC");
        $providersStmt->execute();
        $providers = $providersStmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($providers)) {
            http_response_code(400);
            echo json_encode(["error" => "Aucun prestataire actif avec des crédits n'est disponible pour le dispatch."]);
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
        $deductCredit = $this->db->prepare("UPDATE user_profiles SET lead_credits = lead_credits - 1 WHERE id = ? AND lead_credits > 0");

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
                
                // Only consider providers with credits left in their cached load
                $currentCredits = (int)$provider['lead_credits'] - ($providerLoad[$provider['id']] - $this->getInitialLoad($provider['id'], $providerLoad)); // Simplified check
                
                if ($leadSectorLower !== '' && in_array($leadSectorLower, $providerSectors, true) && $provider['lead_credits'] > 0) {
                    $matchingProviders[] = $provider;
                }
            }

            if (empty($matchingProviders)) {
                continue;
            }

            $candidateProviders = $matchingProviders;
            $bestProvider = null;
            $bestScore = PHP_INT_MAX;

            foreach ($candidateProviders as $candidate) {
                $providerId = $candidate['id'];
                
                // Double check if provider still has credits (could be exhausted during this loop)
                if ($candidate['lead_credits'] <= 0) continue;

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

                $score = ($totalAssigned * 2) + $sectorAssigned;
                if ($score < $bestScore) {
                    $bestScore = $score;
                    $bestProvider = $candidate;
                }
            }

            if (!$bestProvider) {
                continue;
            }

            $this->db->beginTransaction();
            try {
                $assignmentId = $this->generateUuid();
                $providerId = $bestProvider['id'];
                
                if ($insertAssignment->execute([$assignmentId, $leadId, $providerId])) {
                    if ($deductCredit->execute([$providerId])) {
                        $updateLeadStatus->execute([$leadId]);
                        $this->db->commit();
                        
                        $providerLoad[$providerId] = ($providerLoad[$providerId] ?? 0) + 1;
                        $providerSectorLoad[$providerId][$leadSectorLower] = ($providerSectorLoad[$providerId][$leadSectorLower] ?? 0) + 1;
                        
                        // Update credits in local provider object to prevent over-assignment
                        foreach ($providers as &$p) {
                            if ($p['id'] === $providerId) {
                                $p['lead_credits']--;
                                break;
                            }
                        }

                        $assignments[] = [
                            "lead_id" => $leadId,
                            "lead_sector" => $leadSector,
                            "provider_id" => $providerId,
                            "provider_name" => trim((string)(($bestProvider['first_name'] ?? '') . ' ' . ($bestProvider['last_name'] ?? ''))),
                            "provider_email" => $bestProvider['email'] ?? "",
                            "matching_type" => "niche_match"
                        ];
                    } else {
                        $this->db->rollBack();
                    }
                } else {
                    $this->db->rollBack();
                }
            } catch (Exception $e) {
                $this->db->rollBack();
            }
        }

        echo json_encode([
            "message" => "Dispatch automatique termine.",
            "assigned_count" => count($assignments),
            "assignments" => $assignments
        ]);
    }

    private function getInitialLoad($providerId, $providerLoad) {
        return $providerLoad[$providerId] ?? 0;
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
     * Create a Stripe Checkout Session
     */
    public function createCheckout() {
        $auth = $this->authenticate();
        
        // Get JSON input
        $data = json_decode(file_get_contents("php://input"), true);
        $planId = $data['plan_id'] ?? '';

        if (!$planId) {
            http_response_code(400);
            echo json_encode(["error" => "ID du plan manquant."]);
            return;
        }

        // Fetch plan details from DB
        $query = "SELECT name, price, stripe_price_id, max_leads FROM subscription_plans WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(1, $planId);
        $stmt->execute();
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$plan) {
            http_response_code(404);
            echo json_encode(["error" => "Plan introuvable."]);
            return;
        }

        $stripeSecret = $_ENV['STRIPE_SECRET_KEY'] ?? '';
        if (!$stripeSecret) {
            http_response_code(500);
            echo json_encode(["error" => "Configuration Stripe (Secret Key) manquante dans le fichier .env."]);
            return;
        }

        // Build Stripe-compatible HTTP POST body (no nested http_build_query)
        // Use product ID in price_data to link to the existing Stripe product
        $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/rappel/public/pro';
        $successUrl = $baseUrl . '/pricing.php?success=true&session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = $baseUrl . '/pricing.php?canceled=true';

        $postParams = [
            'payment_method_types[0]'                           => 'card',
            'line_items[0][price_data][currency]'               => 'eur',
            'line_items[0][price_data][product]'                => $plan['stripe_price_id'],
            'line_items[0][price_data][unit_amount]'            => (int)($plan['price'] * 100),
            'line_items[0][price_data][recurring][interval]'    => 'month',
            'line_items[0][quantity]'                           => 1,
            'mode'                                              => 'subscription',
            'success_url'                                       => $successUrl,
            'cancel_url'                                        => $cancelUrl,
            'customer_email'                                    => $auth['email'] ?? '',
            'client_reference_id'                               => $auth['id'],
            'metadata[user_id]'                                 => $auth['id'],
            'metadata[plan_id]'                                 => $planId,
            'metadata[plan_name]'                               => $plan['name'],
            'metadata[max_leads]'                               => (string)($plan['max_leads'] ?? 0),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postParams));
        curl_setopt($ch, CURLOPT_USERPWD, $stripeSecret . ':');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            http_response_code($httpCode);
            echo $result;
            return;
        }

        echo $result;
    }

    /**
     * Handle Stripe Webhook
     */
    public function handleWebhook() {
        $payload = file_get_contents('php://input');
        $data = json_decode($payload, true);

        if (!$data || !isset($data['type'])) {
            http_response_code(400);
            echo json_encode(["error" => "Payload invalide"]);
            return;
        }

        // Log the event for debugging
        file_put_contents(__DIR__ . '/stripe_webhooks.log', date('[Y-m-d H:i:s] ') . $data['type'] . "\n", FILE_APPEND);

        if ($data['type'] === 'checkout.session.completed') {
            $session = $data['data']['object'];
            $metadata = $session['metadata'] ?? [];
            $userId = $metadata['user_id'] ?? $session['client_reference_id'] ?? '';
            $planId = $metadata['plan_id'] ?? '';
            $maxLeads = (int)($metadata['max_leads'] ?? 0);

            if ($userId && $planId) {
                try {
                    $this->db->beginTransaction();

                    // 1. Update user profile
                    $updateQuery = "UPDATE user_profiles 
                                   SET plan_id = ?, 
                                       subscription_status = 'active', 
                                       lead_credits = lead_credits + ? 
                                   WHERE id = ?";
                    $stmt = $this->db->prepare($updateQuery);
                    $stmt->execute([$planId, $maxLeads, $userId]);

                    // 2. Create invoice record using the model
                    $this->invoice->provider_id = $userId;
                    $this->invoice->invoice_number = 'INV-' . strtoupper(substr(uniqid(), -8));
                    $this->invoice->amount = (float)($session['amount_total'] / 100);
                    $this->invoice->currency = strtoupper($session['currency'] ?? 'EUR');
                    $this->invoice->stripe_session_id = $session['id'] ?? null;
                    $this->invoice->stripe_payment_id = $session['payment_intent'] ?? null;
                    $this->invoice->stripe_customer_id = $session['customer'] ?? null;
                    $this->invoice->status = 'paid';
                    
                    if (!$this->invoice->create()) {
                        throw new Exception("Erreur lors de la création de la facture.");
                    }

                    $this->db->commit();
                    echo json_encode(["status" => "success", "message" => "Compte mis à jour."]);
                } catch (Exception $e) {
                    $this->db->rollBack();
                    http_response_code(500);
                    echo json_encode(["error" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["status" => "ignored", "message" => "Metadata manquante."]);
            }
        } else {
            echo json_encode(["status" => "ignored", "message" => "Evenement non géré."]);
        }
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
