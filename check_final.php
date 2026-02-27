<?php
require_once 'api/config/db.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT email, lead_credits FROM user_profiles WHERE role = 'provider' AND lead_credits <= 0");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($res) === 0) {
    echo "SUCCESS: No providers with 0 credits found.\n";
} else {
    echo "FAILURE: Some providers still have 0 credits:\n";
    foreach ($res as $r) {
        echo "- " . $r['email'] . " (" . $r['lead_credits'] . ")\n";
    }
}
