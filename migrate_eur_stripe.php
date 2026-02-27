<?php
require_once 'api/config/db.php';

try {
    $db = (new Database())->getConnection();
    echo "Connected to database.\n";

    // 1. Add Stripe columns to invoices table
    $columns = [
        'stripe_session_id' => 'VARCHAR(255)',
        'stripe_payment_id' => 'VARCHAR(255)',
        'stripe_customer_id' => 'VARCHAR(255)'
    ];

    foreach ($columns as $col => $type) {
        // Check if column exists
        $check = $db->query("SHOW COLUMNS FROM invoices LIKE '$col'");
        if ($check->rowCount() === 0) {
            $db->exec("ALTER TABLE invoices ADD COLUMN $col $type NULL AFTER currency");
            echo "Column '$col' added to invoices table.\n";
        } else {
            echo "Column '$col' already exists in invoices table.\n";
        }
    }

    // 2. Force all invoices to EUR
    $db->exec("UPDATE invoices SET currency = 'EUR'");
    echo "All invoices updated to EUR.\n";

    // 3. Force all subscription_plans to EUR
    $db->exec("UPDATE subscription_plans SET currency = 'EUR'");
    echo "All subscription plans updated to EUR.\n";

    echo "Migration completed successfully.\n";

} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
