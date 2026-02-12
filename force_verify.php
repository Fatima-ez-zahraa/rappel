<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
// Force verification for the user
$sql = "UPDATE user_profiles SET is_verified = 1, verification_code = NULL WHERE email LIKE 'fatima%'";
$db->query($sql);
echo "User updated to verified.\n";
?>
