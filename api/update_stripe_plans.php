<?php
require_once 'config/db.php';

$database = new Database();
$db = $database->getConnection();

$updates = [
    'Starter' => 'prod_U3B0mXkBIfJBeP',
    'Pack Pro' => 'prod_U3BD6TV9WZOBKT',
    'Business' => 'prod_U3BGsASWJ7Pen5'
];

foreach ($updates as $name => $stripeId) {
    $query = "UPDATE subscription_plans SET stripe_price_id = :stripe_id WHERE name = :name";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':stripe_id', $stripeId);
    $stmt->bindParam(':name', $name);
    
    if ($stmt->execute()) {
        echo "Plan '$name' updated with Stripe ID '$stripeId'.\n";
    } else {
        echo "Failed to update plan '$name'.\n";
    }
}
?>
