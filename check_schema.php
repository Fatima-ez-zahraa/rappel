<?php
require_once 'api/config/db.php';
$database = new Database();
$db = $database->getConnection();
$stmt = $db->query("DESCRIBE leads");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
file_put_contents('schema_output.json', json_encode($columns, JSON_PRETTY_PRINT));
echo "Schema saved to schema_output.json\n";
