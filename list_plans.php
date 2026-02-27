<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
echo "=== PLANS ===\n";
$stmt = $db->query("SELECT * FROM subscription_plans");
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($plans as $p) {
    echo "ID: {$p['id']} | Name: {$p['name']} | Max Leads: {$p['max_leads']} | Price: {$p['price']}\n";
}

echo "\n=== PROVIDERS CREDITS ===\n";
$stmt = $db->query("SELECT id, email, lead_credits, subscription_status, plan_id FROM user_profiles WHERE role = 'provider'");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    echo "ID: " . substr($u['id'], 0, 8) . "... | Email: {$u['email']} | Credits: {$u['lead_credits']} | Status: {$u['subscription_status']} | Plan: {$u['plan_id']}\n";
}
