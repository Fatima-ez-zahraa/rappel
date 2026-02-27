<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SHOW TABLES");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
