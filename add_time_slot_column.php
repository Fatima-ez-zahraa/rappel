<?php
require_once __DIR__ . '/api/config/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Check if column exists
    $stmt = $conn->query("SHOW COLUMNS FROM leads LIKE 'time_slot'");
    if ($stmt->rowCount() == 0) {
        $conn->exec("ALTER TABLE leads ADD COLUMN time_slot VARCHAR(50) DEFAULT 'Non spécifié' AFTER budget");
        echo "Colonne 'time_slot' ajoutée avec succès.\n";
    } else {
        echo "La colonne 'time_slot' existe déjà.\n";
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
