<?php
// api/migrate_interactions.php
require_once __DIR__ . '/config/db.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS lead_interactions (
        id CHAR(36) PRIMARY KEY,
        lead_id CHAR(36) NOT NULL,
        provider_id CHAR(36) NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Note: Skipping foreign keys for now as UUIDs might be stored as strings in MariaDB/MySQL without explicit UUID type
    // and to avoid issues with existing data types if they don't match exactly.
    
    $db->exec($sql);
    echo "Table 'lead_interactions' created or already exists.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
