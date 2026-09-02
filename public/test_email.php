<?php
$ch = curl_init('http://localhost:8080/test-email');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
echo "RESPONSE:\n";
echo $result;
