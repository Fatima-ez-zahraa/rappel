<?php
// debug_final_siren.php
$input = "443845442"; // Google France SIREN
$url = "https://recherche-entreprises.api.gouv.fr/search?q=" . urlencode($input) . "&limit=1";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Rappel-App/1.0");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "URL: $url\n";
echo "HTTP CODE: $httpCode\n";
echo "RESPONSE:\n$response\n";

$data = json_decode($response, true);
if (empty($data['results'])) {
    echo "RESULTS ARRAY IS EMPTY\n";
} else {
    echo "FOUND RESULT: " . $data['results'][0]['nom_complet'] . "\n";
}
?>
