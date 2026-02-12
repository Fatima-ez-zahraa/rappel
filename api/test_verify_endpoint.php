<?php
// Test verification endpoint directly
header('Content-Type: application/json');
require_once 'config/db.php';
require_once 'controllers/AuthController.php';

// Simulate POST data
$_POST['email'] = 'test@example.com';
$_POST['code'] = '123456';

// Create test input
$test_data = json_encode([
    'email' => 'test@example.com',
    'code' => '123456'
]);

// Temporarily override php://input for testing
file_put_contents('php://temp/test_input', $test_data);

try {
    $auth = new AuthController();
    
    // Test that we can instantiate
    echo json_encode(['status' => 'AuthController instantiated successfully']);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}
?>
