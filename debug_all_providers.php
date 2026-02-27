<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();

echo "=== PROVIDERS WITH 0 CREDITS ===\n";
$stmt = $db->query("SELECT id, email, first_name, last_name, lead_credits, subscription_status FROM user_profiles WHERE role = 'provider' AND lead_credits <= 0");
$providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($providers as $p) {
    echo "ID: " . $p['id'] . " | Email: " . $p['email'] . " | Credits: " . $p['lead_credits'] . "\n";
}

echo "\n=== SEARCH FOR Y.AIT.AHMED ===\n";
$stmt = $db->prepare("SELECT id, email, lead_credits FROM user_profiles WHERE email = ?");
$stmt->execute(['y.ait.ahmed@kipoced.com']);
$res = $stmt->fetch(PDO::FETCH_ASSOC);
if ($res) {
    echo "FOUND: " . $res['email'] . " | Credits: " . $res['lead_credits'] . "\n";
} else {
    echo "NOT FOUND: y.ait.ahmed@kipoced.com\n";
}
