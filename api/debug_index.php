<?php
// Debug logging for verify-email endpoint
file_put_contents(__DIR__ . '/debug.log', "=== NEW REQUEST ===\n", FILE_APPEND);
file_put_contents(__DIR__ . '/debug.log', date('Y-m-d H:i:s') . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/debug.log', "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/debug.log', "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);

$raw_input = file_get_contents("php://input");
file_put_contents(__DIR__ . '/debug.log', "RAW INPUT: " . $raw_input . "\n", FILE_APPEND);

$decoded = json_decode($raw_input);
file_put_contents(__DIR__ . '/debug.log', "DECODED: " . print_r($decoded, true) . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/debug.log', "JSON ERROR: " . json_last_error_msg() . "\n", FILE_APPEND);
file_put_contents(__DIR__ . '/debug.log', "\n", FILE_APPEND);

// Continue with normal processing
require_once 'index.php';
?>
