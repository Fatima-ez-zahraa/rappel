<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();

$credits = 50;

$stmt = $db->prepare("UPDATE user_profiles SET lead_credits = ?, subscription_status = 'active' WHERE role = 'provider'");
if ($stmt->execute([$credits])) {
    echo "Credits and status updated for ALL providers. Set to $credits credits.\n";
} else {
    echo "Failed to update credits.\n";
}
