<?php
require_once __DIR__ . '/public/includes/auth.php';
require_once __DIR__ . '/api/config/db.php';

$user = getCurrentUser();
echo "Current User ID: " . ($user['id'] ?? 'NONE') . "\n";
echo "Current Role: " . ($user['role'] ?? 'NONE') . "\n";

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "\nDatabase Lead counts by user_id:\n";
    $stmt = $db->query("SELECT user_id, COUNT(*) as count FROM leads GROUP BY user_id");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "User ID: " . ($row['user_id'] ?: 'NULL') . " -> " . $row['count'] . " leads\n";
    }
}
