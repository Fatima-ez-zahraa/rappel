<?php
// check_user.php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT email, is_verified FROM user_profiles WHERE email = 'fatima@ycndev.com'");
$stmt->execute();
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>
