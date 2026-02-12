<?php
// debug_api.php
$siren = "319208761";
$url = "https://recherche-entreprises.api.gouv.fr/search?q=" . $siren . "&limit=1";
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Rappel-App/1.0\r\n"
    ]
];
$context = stream_context_create($opts);
$response = file_get_contents($url, false, $context);
echo "RESPONSE:\n";
echo $response;
echo "\n\nDECODED:\n";
print_r(json_decode($response, true));
?>
