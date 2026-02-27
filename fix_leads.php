<?php
require_once __DIR__ . '/public/includes/auth.php';
require_once __DIR__ . '/api/config/db.php';

session_start();
$user = getCurrentUser();

if (!$user) {
    die("Erreur : Vous devez être connecté en tant que client.");
}

$db = (new Database())->getConnection();
$userId = $user['id'];

$stmt = $db->prepare("UPDATE leads SET user_id = ? WHERE user_id IS NULL OR user_id = ''");
$stmt->execute([$userId]);
$count = $stmt->rowCount();

echo "Succès ! $count demandes ont été assignées à votre compte (ID: $userId).<br>";
echo "<a href='/rappel/public/client/mes-demandes.php'>Retourner à Mes Demandes</a>";
