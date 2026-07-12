<?php
header('Content-Type: text/plain');
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "--- DIAGNOSTICS START ---\n";
echo "PHP Version: " . phpversion() . "\n";
echo "curl extension: " . (extension_loaded('curl') ? 'ENABLED' : 'DISABLED') . "\n";

include('includes/supabase_config.php');
echo "SUPABASE_URL: " . SUPABASE_URL . "\n";
echo "SUPABASE_ANON_KEY Length: " . strlen(SUPABASE_ANON_KEY) . "\n";

$url = SUPABASE_URL . "/rest/v1/tblservices?limit=1";
$headers = [
    "apikey: " . SUPABASE_ANON_KEY,
    "Authorization: Bearer " . SUPABASE_ANON_KEY
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HEADER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
if ($curlErr) {
    echo "cURL Error: " . $curlErr . "\n";
} else {
    echo "Response:\n" . $response . "\n";
}
echo "--- DIAGNOSTICS END ---\n";
?>
