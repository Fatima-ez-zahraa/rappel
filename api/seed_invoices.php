<?php
// api/seed_invoices.php

require_once __DIR__ . '/config/db.php';

try {
    $db = (new Database())->getConnection();

    // 1. Get all providers
    $providers = $db->query("SELECT id, email FROM user_profiles WHERE role = 'provider'")->fetchAll(PDO::FETCH_ASSOC);

    if (empty($providers)) {
        die("No providers found in database.\n");
    }

    foreach ($providers as $provider) {
        $providerId = $provider['id'];
        echo "Seeding invoices for provider: " . $provider['email'] . " ($providerId)\n";

        // 2. Insert new invoices
        $invoices = [
            ['INV-' . strtoupper(substr(uniqid(), -4)) . '-001', 29.99, 'paid', '2025-12-15 10:30:00'],
            ['INV-' . strtoupper(substr(uniqid(), -4)) . '-002', 29.99, 'paid', '2026-01-14 09:45:00'],
            ['INV-' . strtoupper(substr(uniqid(), -4)) . '-003', 49.99, 'paid', '2026-02-20 11:20:00'],
        ];

        $query = "INSERT INTO invoices (id, provider_id, invoice_number, amount, currency, status, created_at) 
                  VALUES (UUID(), ?, ?, ?, 'EUR', ?, ?)
                  ON DUPLICATE KEY UPDATE amount = VALUES(amount)";
        
        $stmt = $db->prepare($query);

        foreach ($invoices as $inv) {
            $stmt->execute([
                $providerId,
                $inv[0],
                $inv[1],
                $inv[2],
                $inv[3]
            ]);
            echo "Created invoice: " . $inv[0] . "\n";
        }
    }

    echo "Seeding complete!\n";

} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
