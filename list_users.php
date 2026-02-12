<?php
// list_users.php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT email, is_verified, role FROM user_profiles");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
