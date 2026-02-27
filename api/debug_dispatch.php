<?php
require_once 'config/db.php';

$database = new Database();
$db = $database->getConnection();

$data = [
    'providers' => $db->query("SELECT id, email, is_verified, subscription_status, lead_credits, sectors FROM user_profiles WHERE role = 'provider'")->fetchAll(PDO::FETCH_ASSOC),
    'unassigned_leads' => $db->query("SELECT l.id, l.sector, l.status FROM leads l LEFT JOIN lead_assignments la ON la.lead_id = l.id WHERE la.id IS NULL")->fetchAll(PDO::FETCH_ASSOC)
];

file_put_contents('debug_dispatch.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Debug data saved to debug_dispatch.json\n";
?>
