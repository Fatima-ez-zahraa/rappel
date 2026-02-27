<?php
// api/debug_invoices.php
require_once __DIR__ . '/config/db.php';

try {
    $db = (new Database())->getConnection();
    
    echo "--- USERS ---\n";
    $users = $db->query("SELECT id, email, first_name, last_name, role FROM user_profiles")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $u) {
        echo "ID: {$u['id']} | Email: {$u['email']} | Name: {$u['first_name']} {$u['last_name']} | Role: {$u['role']}\n";
    }

    echo "\n--- INVOICES ---\n";
    $invoices = $db->query("SELECT id, provider_id, invoice_number, amount FROM invoices")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($invoices as $i) {
        echo "ID: {$i['id']} | ProviderID: {$i['provider_id']} | Number: {$i['invoice_number']} | Amount: {$i['amount']}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
