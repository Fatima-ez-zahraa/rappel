<?php
// debug_api_siret.php
$siret = "31920876100075";
$url = "https://recherche-entreprises.api.gouv.fr/search?q=" . $siret . "&limit=1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Rappel-App/1.0");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

echo "RESPONSE:\n";
echo $response;
?>
