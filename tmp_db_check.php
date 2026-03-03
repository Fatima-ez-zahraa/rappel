<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3308;dbname=rappel;charset=utf8mb4','root','');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
foreach (['user_profiles','leads','lead_assignments','quotes'] as $t) {
  $c = $pdo->query("SELECT COUNT(*) c FROM $t")->fetch(PDO::FETCH_ASSOC)['c'];
  echo $t . '=' . $c . PHP_EOL;
}
