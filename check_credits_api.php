<?php
require_once 'api/config/db.php';
require_once 'api/utils/JwtUtils.php';

try {
    $db = (new Database())->getConnection();
    $jwt = new JwtUtils();
    
    // Get headers
    $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        $payload = $jwt->validate($token);
        if ($payload) {
            $userId = $payload->id;
            $stmt = $db->prepare("SELECT id, email, role, lead_credits, plan_id, subscription_status FROM user_profiles WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(["status" => "success", "user" => $user]);
            exit;
        }
    }
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
