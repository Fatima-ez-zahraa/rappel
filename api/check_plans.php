<?php
require_once 'config/db.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT id, name, stripe_price_id FROM subscription_plans";
$stmt = $db->prepare($query);
$stmt->execute();
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($plans, JSON_PRETTY_PRINT);
?>
