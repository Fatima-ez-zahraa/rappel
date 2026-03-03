<?php
$pdo=new PDO('mysql:host=127.0.0.1;port=3308;dbname=rappel;charset=utf8mb4','root','');
$st=$pdo->query("SELECT l.id,l.name,l.sector,l.status,la.provider_id,l.created_at FROM leads l LEFT JOIN lead_assignments la ON la.lead_id=l.id WHERE l.name LIKE '%ggg%' OR l.need LIKE '%ggg%' ORDER BY l.created_at DESC");
foreach($st as $r){echo implode(' | ',[$r['id'],$r['name'],$r['sector'],$r['status'],$r['provider_id']?:'NULL',$r['created_at']]).PHP_EOL;}
?>
