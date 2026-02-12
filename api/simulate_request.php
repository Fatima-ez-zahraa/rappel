<?php
// Test the actual API call via HTTP simulation
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Simulating Real HTTP Request to verify-email ===\n\n";

// Simulate the HTTP environment
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/api/auth/verify-email';
$_SERVER['SCRIPT_NAME'] = '/api/index.php';
$_SERVER['HTTP_HOST'] = 'localhost';

// Create JSON input
$json_input = json_encode([
    'email' => 'test@example.com',
    'code' => '123456'
]);

// Mock php://input by creating a temporary stream
$temp = fopen('php://temp', 'r+');
fwrite($temp, $json_input);
rewind($temp);
stream_filter_append($temp, 'string.toupper', STREAM_FILTER_READ);

echo "Request: POST /api/auth/verify-email\n";
echo "Body: $json_input\n\n";

// Capture all output
ob_start();

// Set up input mock
$_POST_BACKUP = $_POST;
$GLOBALS['HTTP_RAW_POST_DATA'] = $json_input;

try {
    // Include the main index file
    include 'index.php';
    
    $response = ob_get_clean();
    
    echo "=== RAW RESPONSE ===\n";
    echo $response;
    echo "\n=== END RESPONSE ===\n\n";
    
    // Try to decode as JSON
    $json = json_decode($response);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON\n";
        echo "Decoded: " . print_r($json, true) . "\n";
    } else {
        echo "❌ Invalid JSON!\n";
        echo "Error: " . json_last_error_msg() . "\n";
        echo "First 500 chars:\n" . substr($response, 0, 500) . "\n";
    }
    
} catch (Exception $e) {
    $response = ob_get_clean();
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Response before exception:\n$response\n";
}

$_POST = $_POST_BACKUP;
fclose($temp);
?>
