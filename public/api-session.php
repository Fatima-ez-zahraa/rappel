<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$action = $body['action'] ?? '';

if ($action === 'set') {
    $token = $body['token'] ?? '';
    $user  = $body['user']  ?? [];
    if (empty($token)) {
        http_response_code(400);
        echo json_encode(['error' => 'Token required']);
        exit;
    }
    $_SESSION['rappel_token'] = $token;
    $_SESSION['rappel_user']  = $user;
    echo json_encode(['success' => true]);

} elseif ($action === 'clear') {
    unset($_SESSION['rappel_token'], $_SESSION['rappel_user']);
    echo json_encode(['success' => true]);

} elseif ($action === 'get') {
    echo json_encode([
        'token' => $_SESSION['rappel_token'] ?? null,
        'user'  => $_SESSION['rappel_user']  ?? null,
    ]);

} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
}
