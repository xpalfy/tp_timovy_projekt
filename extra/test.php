<?php

$url = "http://127.0.0.1:5000/modules/classify"; // Flask server URL
$data = json_encode(["path" => "/path/to/your/file"]); // JSON payload

$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
curl_close($ch);

echo $response;

?>

<footer class="footer bg-dark">
    © Proje      ct Site <a href="https://tptimovyprojekt.ddns.net/">tptimovyprojekt.ddns.net</a>
</footer>