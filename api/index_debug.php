<?php
// api/index_debug.php
// Simulate a full request to index.php and capture output

ob_start();
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/api/auth/verify-email';
$_SERVER['SCRIPT_NAME'] = '/api/index.php';

// Mock php://input
// This is hard, but we can bypass it by mocking json_decode result if we modify index.php
// Instead, let's just RUN it and see if there are any warnings/errors that appear.

try {
    include 'index.php';
} catch (Throwable $t) {
    echo "CAUGHT: " . $t->getMessage();
}

$output = ob_get_clean();
echo "RAW OUTPUT START\n";
echo $output;
echo "\nRAW OUTPUT END\n";
?>
