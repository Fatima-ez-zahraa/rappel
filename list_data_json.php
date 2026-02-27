<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
$plans = $db->query("SELECT * FROM subscription_plans")->fetchAll(PDO::FETCH_ASSOC);
$users = $db->query("SELECT id, email, role, lead_credits, subscription_status, plan_id FROM user_profiles WHERE role = 'provider' LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['plans' => $plans, 'users' => $users], JSON_PRETTY_PRINT);
