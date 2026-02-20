<?php
// logout.php - Clears session and redirects based on role
require_once __DIR__ . '/auth.php';

$user = getCurrentUser();
$isAdmin = ($user['role'] ?? '') === 'admin';

clearSession();
header('Location: ' . ($isAdmin ? '/rappel/public/' : '/rappel/public/pro/login.php'));
exit;
