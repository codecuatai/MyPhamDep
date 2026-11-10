<?php
/**
 * API KIỂM TRA & ÁP DỤNG VOUCHER - ALICE COSMETIC
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();

$code = strtoupper(trim($_GET['code'] ?? ''));
$subtotal = (float)($_GET['subtotal'] ?? 0);

if (empty($code)) {
    jsonResponse(['success' => false, 'message' => 'Vui lòng nhập mã giảm giá!'], 400);
}

$stmt = $pdo->prepare("SELECT * FROM coupons WHERE UPPER(code) = ?");
$stmt->execute([$code]);
$coupon = $stmt->fetch();

if (!$coupon) {
    jsonResponse(['success' => false, 'message' => 'Mã giảm giá không tồn tại hoặc đã hết lượt!'], 404);
}

if ($subtotal < (float)$coupon['min_order']) {
    jsonResponse([
        'success' => false, 
        'message' => 'Mã này chỉ áp dụng cho đơn hàng từ ' . formatVND($coupon['min_order']) . ' trở lên!'
    ], 400);
}

$discount = 0;
if ($coupon['discount_type'] === 'percent') {
    $discount = ($subtotal * (float)$coupon['discount_value']) / 100;
    if ((float)$coupon['max_discount'] > 0 && $discount > (float)$coupon['max_discount']) {
        $discount = (float)$coupon['max_discount'];
    }
} else {
    $discount = (float)$coupon['discount_value'];
}

if ($discount > $subtotal) {
    $discount = $subtotal;
}

jsonResponse([
    'success' => true,
    'code' => $coupon['code'],
    'discount_amount' => $discount,
    'discount_formatted' => formatVND($discount),
    'description' => $coupon['description'],
    'message' => 'Áp dụng mã giảm giá thành công! Tiết kiệm ' . formatVND($discount)
]);
