<?php
require_once 'config/db.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->query("DESCRIBE user_profiles");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "Columns in user_profiles:\n";
    print_r($columns);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
