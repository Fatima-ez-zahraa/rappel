<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT email, role, lead_credits, subscription_status FROM user_profiles");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    echo "EMAIL: [" . $u['email'] . "] | ROLE: " . $u['role'] . " | CREDITS: " . $u['lead_credits'] . " | STATUS: " . $u['subscription_status'] . "\n";
}
