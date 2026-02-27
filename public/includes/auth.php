<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('API_URL', 'http://127.0.0.1/rappel/api');

function isLoggedIn(): bool {
    return isset($_SESSION['rappel_token']) && !empty($_SESSION['rappel_token']);
}

function getCurrentUser(): ?array {
    return $_SESSION['rappel_user'] ?? null;
}

function getToken(): ?string {
    return $_SESSION['rappel_token'] ?? null;
}

function isAdmin(): bool {
    $user = getCurrentUser();
    return $user && ($user['role'] ?? '') === 'admin';
}

function isVerified(): bool {
    $user = getCurrentUser();
    if (!$user) return false;
    return ($user['is_verified'] ?? false) || isAdmin();
}

function isProvider(): bool {
    $user = getCurrentUser();
    return $user && ($user['role'] ?? '') === 'provider';
}

function isClient(): bool {
    $user = getCurrentUser();
    return $user && ($user['role'] ?? '') === 'client';
}

function hasSubscription(): bool {
    $user = getCurrentUser();
    if (!$user) return false;
    return ($user['subscription_status'] ?? '') === 'active' || isAdmin();
}

function requireAuth(bool $requireVerified = true): void {
    if (!isLoggedIn()) {
        $path = $_SERVER['REQUEST_URI'];
        $loginUrl = '/rappel/public/pro/login.php';
        if (strpos($path, '/rappel/public/client/') === 0) {
            $loginUrl = '/rappel/public/client/login.php';
        }
        header('Location: ' . $loginUrl . '?redirect=' . urlencode($path));
        exit;
    }
    if ($requireVerified && !isVerified()) {
        header('Location: /rappel/public/pro/verify.php');
        exit;
    }
}

function requireAdmin(): void {
    if (!isLoggedIn()) {
        header('Location: /rappel/public/admin/login.php');
        exit;
    }
    if (!isAdmin()) {
        header('Location: /rappel/public/pro/dashboard.php');
        exit;
    }
}

function setSession(array $user, string $token): void {
    $_SESSION['rappel_token'] = $token;
    $_SESSION['rappel_user'] = $user;
}

function clearSession(): void {
    unset($_SESSION['rappel_token'], $_SESSION['rappel_user']);
}

// Sync user from API (refresh profile)
function syncUser(): void {
    $token = getToken();
    if (!$token) return;
    $ch = curl_init(API_URL . '/profile');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Authorization: Bearer $token", "Content-Type: application/json"],
        CURLOPT_TIMEOUT => 5,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if (isset($data['user']) && is_array($data['user'])) {
            $_SESSION['rappel_user'] = $data['user'];
        }
    }
}
?>
