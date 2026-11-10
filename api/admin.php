<?php
/**
 * API QUẢN TRỊ CÀI ĐẶT & CẤU HÌNH SHOP - ALICE COSMETIC
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    
    $allowedKeys = [
        'shop_name', 'slogan', 'hotline', 'zalo', 'address', 'freeship_min',
        'bank_id', 'bank_name', 'account_no', 'account_name', 'qr_template',
        'google_sheet_webhook'
    ];

    $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (`key`, `value`) VALUES (?, ?)");
    foreach ($allowedKeys as $key) {
        if (isset($input[$key])) {
            $stmt->execute([$key, trim($input[$key])]);
        }
    }

    jsonResponse(['success' => true, 'message' => 'Cập nhật cấu hình ALICE Cosmetic thành công!']);
}

// GET: Lấy cấu hình hiện tại
$config = getShopConfig($pdo);
jsonResponse(['config' => $config, 'banks' => VIETQR_BANKS]);
