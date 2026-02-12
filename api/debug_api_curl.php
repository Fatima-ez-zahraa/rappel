<?php
// debug_api_curl.php
$siren = "319208761";
$url = "https://recherche-entreprises.api.gouv.fr/search?q=" . $siren . "&limit=1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Rappel-App/1.0");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable for debug
$response = curl_exec($ch);
curl_close($ch);

echo "RESPONSE:\n";
echo $response;
echo "\n\nDECODED:\n";
print_r(json_decode($response, true));
?>
