<?php
require_once __DIR__ . '/../includes/db.php';
$pdo = getDbConnection();

echo "Banners count: " . $pdo->query("SELECT COUNT(*) FROM banners")->fetchColumn() . "\n";

// Update some sample stock quantities and origins for the 16 products so there is realistic variety
$updates = [
    1 => ['stock' => 45, 'origin' => 'La Roche-Posay (Pháp)', 'capacity' => '30ml / 50ml', 'expiry' => '3 năm kể từ NSX (12 tháng sau mở nắp)'],
    2 => ['stock' => 8,  'origin' => 'The Ordinary (Canada)', 'capacity' => '30ml / 60ml', 'expiry' => '36 tháng kể từ NSX'], // low stock
    3 => ['stock' => 0,  'origin' => 'CeraVe (Mỹ)',           'capacity' => '236ml / 473ml', 'expiry' => '36 tháng kể từ NSX'], // out of stock
    4 => ['stock' => 120, 'origin' => 'Skin1004 (Hàn Quốc)',   'capacity' => '55ml / 100ml', 'expiry' => '30 tháng kể từ NSX'],
    5 => ['stock' => 6,   'origin' => 'Anessa (Nhật Bản)',     'capacity' => '60ml', 'expiry' => '36 tháng kể từ NSX'], // low stock
];

foreach ($updates as $pid => $info) {
    $stmt = $pdo->prepare("UPDATE products SET stock_quantity = ?, origin = ?, capacity = ?, expiry = ? WHERE id = ?");
    $stmt->execute([$info['stock'], $info['origin'], $info['capacity'], $info['expiry'], $pid]);
}

// For remaining products, ensure stock_quantity > 15
$pdo->exec("UPDATE products SET stock_quantity = 50 WHERE stock_quantity IS NULL OR stock_quantity <= 0 AND id NOT IN (3)");

echo "Stock and product details updated successfully!\n";
