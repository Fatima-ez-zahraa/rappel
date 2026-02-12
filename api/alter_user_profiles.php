<?php
// Script to safely add verification columns to user_profiles table
require_once 'config/db.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "Checking user_profiles table structure...\n";
    
    // Check if columns already exist
    $stmt = $db->query("SHOW COLUMNS FROM user_profiles LIKE 'verification_code'");
    $has_verification_code = $stmt->rowCount() > 0;
    
    $stmt = $db->query("SHOW COLUMNS FROM user_profiles LIKE 'is_verified'");
    $has_is_verified = $stmt->rowCount() > 0;
    
    if ($has_verification_code && $has_is_verified) {
        echo "✓ Columns already exist. No migration needed.\n";
        exit(0);
    }
    
    // Add columns if they don't exist
    if (!$has_verification_code) {
        echo "Adding verification_code column...\n";
        $db->exec("ALTER TABLE user_profiles ADD COLUMN verification_code VARCHAR(6) NULL AFTER role");
        echo "✓ verification_code column added.\n";
    }
    
    if (!$has_is_verified) {
        echo "Adding is_verified column...\n";
        $db->exec("ALTER TABLE user_profiles ADD COLUMN is_verified TINYINT(1) DEFAULT 0 AFTER verification_code");
        echo "✓ is_verified column added.\n";
    }
    
    echo "\n✅ Migration completed successfully!\n";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
