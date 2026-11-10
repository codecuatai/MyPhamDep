<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$pdo = getDbConnection();
$config = getShopConfig($pdo);
echo "Webhook in shop config: " . ($config['google_sheet_webhook'] ?? 'EMPTY') . "\n";

$stmt = $pdo->query("SELECT * FROM settings WHERE `key` = 'google_sheet_webhook'");
$row = $stmt->fetch();
if ($row) {
    echo "Webhook in settings DB: " . $row['value'] . "\n";
} else {
    echo "Webhook not in settings DB\n";
}
