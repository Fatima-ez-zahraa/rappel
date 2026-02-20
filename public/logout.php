<?php
require_once __DIR__ . '/includes/auth.php';

$user = getCurrentUser();
$isAdmin = ($user['role'] ?? '') === 'admin';

clearSession();
header('Location: ' . ($isAdmin ? '/rappel/public/' : '/rappel/public/pro/login.php'));
exit;
