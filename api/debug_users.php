<?php
require_once 'config/db.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->query("SELECT email, verification_code, is_verified FROM user_profiles ORDER BY id DESC LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Recent users:\n";
    print_r($users);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
