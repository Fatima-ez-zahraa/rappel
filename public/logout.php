<?php
require_once __DIR__ . '/includes/auth.php';

$user = getCurrentUser();
$isAdmin = ($user['role'] ?? '') === 'admin';

clearSession();
// Redirection logic: All users go to home except specifically handled roles if needed
header('Location: /rappel/public/');
exit;
