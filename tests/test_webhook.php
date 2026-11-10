<?php
$url = "https://script.google.com/macros/s/AKfycbwwnmNJvhW77HaXTrzTjiOmWjtzbJNPxrVJkiuMXppd96TgJ-tGpSGB5HsC39ESOsJs/exec";

$payload = [
    'action' => 'create_order',
    'orderCode' => 'TEST-' . time(),
    'customerName' => 'Test Khách Hàng',
    'phone' => '0987654321',
    'address' => '123 Đường Test, Hà Nội',
    'totalAmount' => '379.000đ',
    'paymentMethod' => 'VietQR',
    'items' => 'Kem Chống Nắng La Roche-Posay x1',
    'createdAt' => date('d/m/Y H:i:s')
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$res = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $res\n";
if ($err) echo "Error: $err\n";
