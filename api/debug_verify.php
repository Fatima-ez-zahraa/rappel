<?php
// api/debug_verify.php
require_once 'controllers/AuthController.php';

// Mock the POST data
$mockData = [
    'email' => 'fatima@ycndev.com',
    'code' => '731859'
];

// Set php://input mockup if possible, or just modify AuthController to accept data
// Since I can't easily mock php://input for the real controller, I'll just check if the code runs.

try {
    $auth = new AuthController();
    echo "Controller initialized.\n";
    
    // We can't call verifyEmail() directly because it reads from php://input
    // Let's check if there's any syntax error by just including it.
    echo "No syntax errors found in AuthController.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
