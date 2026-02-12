<?php
require_once 'config/db.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $sql = "ALTER TABLE user_profiles 
            ADD COLUMN verification_code VARCHAR(6) NULL AFTER role,
            ADD COLUMN is_verified TINYINT(1) DEFAULT 0 AFTER verification_code";

    $db->exec($sql);
    echo "Table user_profiles updated successfully.\n";

} catch(PDOException $e) {
    echo "Error updating table: " . $e->getMessage() . "\n";
}
?>
