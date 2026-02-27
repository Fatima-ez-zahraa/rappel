<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
$email = 'pro@ycndev.com';
$stmt = $db->prepare("SELECT id, email, lead_credits, subscription_status FROM user_profiles WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    echo "USER_FOUND: " . $user['email'] . "\n";
    echo "CREDITS: " . $user['lead_credits'] . "\n";
    echo "STATUS: " . $user['subscription_status'] . "\n";
} else {
    echo "USER_NOT_FOUND: $email\n";
}
