<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
$users = $db->query("SELECT id, email, role FROM users")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($users, JSON_PRETTY_PRINT);
