<?php
require_once 'config/db.php';

$database = new Database();
$db = $database->getConnection();

$updates = [
    'hicham@ycndev.com' => ['sectors' => json_encode(['garage', 'renovation']), 'credits' => 10, 'status' => 'active'],
    'adnane@ycndev.com' => ['sectors' => json_encode(['finance', 'assurance']), 'credits' => 10, 'status' => 'active'],
    'yassir@ycndev.com' => ['sectors' => json_encode(['assurance', 'banque']), 'credits' => 10, 'status' => 'active'],
    'mouad@ycndev.com'  => ['sectors' => json_encode(['renovation', 'energie']), 'credits' => 10, 'status' => 'active'],
    'fatimaezahra@ycndev.com' => ['sectors' => json_encode(['garage', 'finance']), 'credits' => 10, 'status' => 'active']
];

foreach ($updates as $email => $data) {
    $stmt = $db->prepare("UPDATE user_profiles SET sectors = ?, lead_credits = ?, subscription_status = ?, is_verified = 1 WHERE email = ?");
    $stmt->execute([$data['sectors'], $data['credits'], $data['status'], $email]);
    echo "Updated $email with sectors: {$data['sectors']} and credits: {$data['credits']}\n";
}

echo "Seeding complete.\n";
?>
