<?php
declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

function fail(string $message, array $context = []): void {
    echo "[FAIL] {$message}\n";
    if (!empty($context)) {
        echo json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    }
    exit(1);
}

function ok(string $message): void {
    echo "[OK] {$message}\n";
}

function apiRequest(string $baseUrl, string $method, string $endpoint, ?array $payload = null, ?string $token = null): array {
    $url = rtrim($baseUrl, '/') . $endpoint;
    $ch = curl_init($url);
    $headers = ['Accept: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    if ($payload !== null) {
        $headers[] = 'Content-Type: application/json';
    }

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    if ($payload !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    $body = curl_exec($ch);
    if ($body === false) {
        $err = curl_error($ch);
        curl_close($ch);
        fail("HTTP request failed: {$method} {$endpoint}", ['error' => $err]);
    }
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $json = json_decode($body, true);
    return ['status' => $status, 'body' => $json, 'raw' => $body];
}

function login(string $baseUrl, string $email, string $password, string $expectedRole): array {
    $res = apiRequest($baseUrl, 'POST', '/auth/login', [
        'email' => $email,
        'password' => $password,
        'expected_role' => $expectedRole
    ]);
    if ($res['status'] !== 200) {
        fail("Login failed for {$expectedRole}", $res);
    }
    $token = $res['body']['session']['access_token'] ?? null;
    if (!$token) {
        fail("Missing access token for {$expectedRole}", $res);
    }
    return ['token' => $token, 'user' => $res['body']['user'] ?? []];
}

$envPath = __DIR__ . '/.env';
$env = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim((string)$line);
        if ($line === '' || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim($v);
    }
}
if (empty($env)) {
    fail('Unable to read .env');
}

$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $env['DB_HOST'] ?? '127.0.0.1',
    $env['DB_PORT'] ?? '3306',
    $env['DB_NAME'] ?? ''
);

try {
    $pdo = new PDO($dsn, $env['DB_USER'] ?? 'root', $env['DB_PASS'] ?? '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    fail('DB connection failed', ['error' => $e->getMessage()]);
}

$baseUrl = 'http://localhost/rappel/api';
$suffix = date('YmdHis');
$password = 'Pass12345!';
$hash = password_hash($password, PASSWORD_BCRYPT);

$users = [
    ['role' => 'client', 'email' => "e2e_client_{$suffix}@test.local", 'first' => 'E2E', 'last' => 'Client', 'phone' => '0600000001', 'sectors' => null],
    ['role' => 'provider', 'email' => "e2e_expert_{$suffix}@test.local", 'first' => 'E2E', 'last' => 'Expert', 'phone' => '0600000002', 'sectors' => json_encode(['telecom'])],
    ['role' => 'admin', 'email' => "e2e_admin_{$suffix}@test.local", 'first' => 'E2E', 'last' => 'Admin', 'phone' => '0600000003', 'sectors' => null],
];

$insertUser = $pdo->prepare(
    "INSERT INTO user_profiles (
        id, email, password, first_name, last_name, phone, role, is_verified, sectors,
        subscription_status, lead_credits, created_at, updated_at
    ) VALUES (
        UUID(), :email, :password, :first_name, :last_name, :phone, :role, 1, :sectors,
        'active', 50, NOW(), NOW()
    )"
);

$getUser = $pdo->prepare("SELECT id, email, role FROM user_profiles WHERE email = :email LIMIT 1");
$created = [];
foreach ($users as $u) {
    $insertUser->execute([
        ':email' => $u['email'],
        ':password' => $hash,
        ':first_name' => $u['first'],
        ':last_name' => $u['last'],
        ':phone' => $u['phone'],
        ':role' => $u['role'],
        ':sectors' => $u['sectors'],
    ]);
    $getUser->execute([':email' => $u['email']]);
    $created[] = array_merge($u, $getUser->fetch() ?: []);
}
ok('Test users created in DB');

$clientCred = $created[0];
$expertCred = $created[1];
$adminCred = $created[2];

$clientLogin = login($baseUrl, $clientCred['email'], $password, 'client');
$expertLogin = login($baseUrl, $expertCred['email'], $password, 'provider');
$adminLogin = login($baseUrl, $adminCred['email'], $password, 'admin');
ok('Client/Expert/Admin login OK');

$leadTelecom = apiRequest($baseUrl, 'POST', '/leads', [
    'first_name' => 'Client',
    'last_name' => 'E2E',
    'phone' => $clientCred['phone'],
    'email' => $clientCred['email'],
    'service_type' => 'telecom',
    'need' => 'Besoin test telecom E2E',
    'budget' => 3000,
    'time_slot' => 'Matin (09h - 12h)',
    'zip_code' => '75000',
    'city' => 'Paris',
], $clientLogin['token']);
if ($leadTelecom['status'] !== 201 || empty($leadTelecom['body']['id'])) {
    fail('Telecom lead creation failed', $leadTelecom);
}
$leadTelecomId = $leadTelecom['body']['id'];
ok('Client created telecom lead');

$leadFinance = apiRequest($baseUrl, 'POST', '/leads', [
    'first_name' => 'Client',
    'last_name' => 'E2E',
    'phone' => $clientCred['phone'],
    'email' => $clientCred['email'],
    'service_type' => 'finance',
    'need' => 'Besoin test finance E2E',
    'budget' => 1200,
    'time_slot' => 'Après-midi (14h - 18h)',
], $clientLogin['token']);
if ($leadFinance['status'] !== 201 || empty($leadFinance['body']['id'])) {
    fail('Finance lead creation failed', $leadFinance);
}
$leadFinanceId = $leadFinance['body']['id'];
ok('Client created finance lead');

$expertLeads = apiRequest($baseUrl, 'GET', '/leads', null, $expertLogin['token']);
if ($expertLeads['status'] !== 200 || !is_array($expertLeads['body'])) {
    fail('Expert leads fetch failed', $expertLeads);
}
$expertLeadIds = array_map(static fn($x) => (string)($x['id'] ?? ''), $expertLeads['body']);
if (!in_array($leadTelecomId, $expertLeadIds, true)) {
    fail('Expert cannot see telecom lead from its sector', ['expert_leads' => $expertLeadIds, 'telecom_lead' => $leadTelecomId]);
}
if (in_array($leadFinanceId, $expertLeadIds, true)) {
    fail('Expert can see finance lead outside its sector', ['expert_leads' => $expertLeadIds, 'finance_lead' => $leadFinanceId]);
}
ok('Expert sector filtering OK');

$quoteCreate = apiRequest($baseUrl, 'POST', '/quotes', [
    'lead_id' => $leadTelecomId,
    'client_name' => 'E2E Client',
    'project_name' => 'Devis test E2E',
    'amount' => 1500,
    'status' => 'attente_client'
], $expertLogin['token']);
if ($quoteCreate['status'] !== 201 || empty($quoteCreate['body']['id'])) {
    fail('Expert quote creation failed', $quoteCreate);
}
$quoteId = $quoteCreate['body']['id'];
ok('Expert created quote');

$clientQuotes = apiRequest($baseUrl, 'GET', '/client/quotes', null, $clientLogin['token']);
if ($clientQuotes['status'] !== 200 || !is_array($clientQuotes['body'])) {
    fail('Client quotes fetch failed', $clientQuotes);
}
$clientQuoteIds = array_map(static fn($q) => (string)($q['id'] ?? ''), $clientQuotes['body']);
if (!in_array($quoteId, $clientQuoteIds, true)) {
    fail('Client does not see received quote', ['quote_id' => $quoteId, 'client_quotes' => $clientQuoteIds]);
}
ok('Client sees received quote');

$accept = apiRequest($baseUrl, 'PATCH', '/client/accept-quote/' . $quoteId, [], $clientLogin['token']);
if ($accept['status'] !== 200) {
    fail('Client quote acceptance failed', $accept);
}
ok('Client accepted quote');

$expertQuotes = apiRequest($baseUrl, 'GET', '/quotes', null, $expertLogin['token']);
if ($expertQuotes['status'] !== 200 || !is_array($expertQuotes['body'])) {
    fail('Expert quotes fetch failed', $expertQuotes);
}
$accepted = null;
foreach ($expertQuotes['body'] as $q) {
    if ((string)($q['id'] ?? '') === (string)$quoteId) {
        $accepted = strtolower((string)($q['status'] ?? ''));
        break;
    }
}
if ($accepted === null || !in_array($accepted, ['accepted', 'signe'], true)) {
    fail('Expert does not see accepted quote status', ['quote_id' => $quoteId, 'status' => $accepted]);
}
ok('Expert sees accepted quote status');

$adminLeads = apiRequest($baseUrl, 'GET', '/admin/leads', null, $adminLogin['token']);
if ($adminLeads['status'] !== 200 || !is_array($adminLeads['body'])) {
    fail('Admin leads fetch failed', $adminLeads);
}
$adminLeadIds = array_map(static fn($l) => (string)($l['id'] ?? ''), $adminLeads['body']);
if (!in_array($leadTelecomId, $adminLeadIds, true)) {
    fail('Admin does not see telecom lead', ['lead_id' => $leadTelecomId]);
}

$adminQuotes = apiRequest($baseUrl, 'GET', '/admin/quotes', null, $adminLogin['token']);
if ($adminQuotes['status'] !== 200 || !is_array($adminQuotes['body'])) {
    fail('Admin quotes fetch failed', $adminQuotes);
}
$adminQuoteIds = array_map(static fn($q) => (string)($q['id'] ?? ''), $adminQuotes['body']);
if (!in_array($quoteId, $adminQuoteIds, true)) {
    fail('Admin does not see quote', ['quote_id' => $quoteId]);
}
ok('Admin sees leads and quotes');

$leadDb = $pdo->prepare("SELECT id, user_id, sector, status, preferred_date, doc_path, time_slot FROM leads WHERE id = ?");
$leadDb->execute([$leadTelecomId]);
$leadRow = $leadDb->fetch();
if (!$leadRow) {
    fail('Lead not persisted in DB', ['lead_id' => $leadTelecomId]);
}

$quoteDb = $pdo->prepare("SELECT id, lead_id, provider_id, amount, status, doc_path FROM quotes WHERE id = ?");
$quoteDb->execute([$quoteId]);
$quoteRow = $quoteDb->fetch();
if (!$quoteRow) {
    fail('Quote not persisted in DB', ['quote_id' => $quoteId]);
}
if (!in_array(strtolower((string)$quoteRow['status']), ['accepted', 'signe'], true)) {
    fail('Quote DB status not updated after acceptance', ['status' => $quoteRow['status']]);
}
ok('DB persistence checks OK');

echo "\n=== E2E SUMMARY ===\n";
echo "Client: {$clientCred['email']}\n";
echo "Expert: {$expertCred['email']} (sector telecom)\n";
echo "Admin : {$adminCred['email']}\n";
echo "Lead telecom ID: {$leadTelecomId}\n";
echo "Lead finance ID: {$leadFinanceId}\n";
echo "Quote ID: {$quoteId}\n";
echo "Lead DB status: " . ($leadRow['status'] ?? 'n/a') . "\n";
echo "Quote DB status: " . ($quoteRow['status'] ?? 'n/a') . "\n";
echo "All checks passed.\n";
