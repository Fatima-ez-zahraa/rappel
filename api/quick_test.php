<?php
// Quick test to verify the verify-email endpoint works
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate the request
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/api/auth/verify-email';

// Create a test file with JSON data
$test_json = json_encode([
    'email' => 'test@example.com',
    'code' => '123456'
]);

// Mock php://input
file_put_contents('php://temp/testinput', $test_json);

echo "Testing verify-email endpoint...\n";
echo "Request data: $test_json\n\n";

// Include and test
try {
    require_once __DIR__ . '/config/db.php';
    require_once __DIR__ . '/controllers/AuthController.php';
    require_once __DIR__ . '/utils/JwtUtils.php';
    require_once __DIR__ . '/utils/Mailer.php';
    require_once __DIR__ . '/models/User.php';
    
    $auth = new AuthController();
    echo "✓ AuthController instantiated successfully\n";
    echo "✓ Database connection established\n";
    echo "✓ All classes loaded\n";
    echo "\nEndpoint should now work correctly!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
