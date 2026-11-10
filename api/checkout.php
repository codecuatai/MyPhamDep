<?php
/**
 * API ĐẶT HÀNG & TẠO VIETQR TỰ ĐỘNG - ALICE COSMETIC
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Phương thức không được hỗ trợ'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$pdo = getDbConnection();
$config = getShopConfig($pdo);

$customerName   = cleanInput($input['customer_name'] ?? '');
$phone          = cleanInput($input['phone'] ?? '');
$address        = cleanInput($input['address'] ?? '');
$note           = cleanInput($input['note'] ?? '');
$paymentMethod  = cleanInput($input['payment_method'] ?? 'vietqr');
$couponCode     = cleanInput($input['coupon_code'] ?? '');
$items          = $input['items'] ?? ($input['cart'] ?? []);
$totalAmount    = (float)($input['total_amount'] ?? 0);
$discountAmount = (float)($input['discount_amount'] ?? 0);

if ($totalAmount <= 0 && !empty($items)) {
    foreach ($items as $it) {
        $price = (float)($it['price'] ?? 0);
        $qty = (int)($it['quantity'] ?? 1);
        $totalAmount += $price * $qty;
    }
}
$finalAmount    = (float)($input['final_amount'] ?? max(0, $totalAmount - $discountAmount));

if (empty($customerName) || empty($phone) || empty($address)) {
    jsonResponse(['success' => false, 'message' => 'Vui lòng điền đầy đủ Họ tên, Số điện thoại và Địa chỉ nhận hàng!'], 400);
}

if (empty($items)) {
    jsonResponse(['success' => false, 'message' => 'Giỏ hàng đang trống! Vui lòng chọn sản phẩm mỹ phẩm yêu thích.'], 400);
}

// 1. Kiểm tra tồn kho trước khi đặt hàng
foreach ($items as $it) {
    $pid = (int)($it['id'] ?? 0);
    $qty = (int)($it['quantity'] ?? 1);
    if ($pid > 0) {
        $pStmt = $pdo->prepare("SELECT name, stock_quantity FROM products WHERE id = ?");
        $pStmt->execute([$pid]);
        $prodInfo = $pStmt->fetch();
        if ($prodInfo && isset($prodInfo['stock_quantity'])) {
            $avail = (int)$prodInfo['stock_quantity'];
            if ($avail < $qty) {
                if ($avail <= 0) {
                    jsonResponse(['success' => false, 'message' => "Sản phẩm '{$prodInfo['name']}' hiện đã hết hàng trong kho!"], 400);
                } else {
                    jsonResponse(['success' => false, 'message' => "Sản phẩm '{$prodInfo['name']}' hiện chỉ còn lại {$avail} sản phẩm trong kho!"], 400);
                }
            }
        }
    }
}

$orderCode = generateOrderCode();
$itemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);

// Thêm vào cơ sở dữ liệu
try {
    $stmt = $pdo->prepare("INSERT INTO orders 
        (order_code, customer_name, phone, address, note, payment_method, total_amount, discount_amount, final_amount, coupon_code, items_json, payment_status, sync_status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', datetime('now', 'localtime'))");
    $stmt->execute([
        $orderCode, $customerName, $phone, $address, $note,
        $paymentMethod, $totalAmount, $discountAmount, $finalAmount,
        $couponCode, $itemsJson
    ]);

    // 2. Tự động trừ tồn kho và tăng số lượng đã bán
    foreach ($items as $it) {
        $pid = (int)($it['id'] ?? 0);
        $qty = (int)($it['quantity'] ?? 1);
        if ($pid > 0) {
            $deductStmt = $pdo->prepare("UPDATE products SET stock_quantity = MAX(0, stock_quantity - ?), sold_count = sold_count + ? WHERE id = ?");
            $deductStmt->execute([$qty, $qty, $pid]);
        }
    }
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Lỗi tạo đơn hàng: ' . $e->getMessage()], 500);
}

// Tạo thông tin VietQR nếu thanh toán qua chuyển khoản
$vietqrData = null;
if ($paymentMethod === 'vietqr') {
    $bankId = $config['bank_id'] ?? 'MB';
    $accountNo = $config['account_no'] ?? '0869571734';
    $accountName = $config['account_name'] ?? 'ALICE COSMETIC';
    $template = $config['qr_template'] ?? 'compact2';

    // Nội dung chuyển khoản chuẩn: Mã đơn hàng
    $addInfo = urlencode($orderCode);
    $accNameEncoded = urlencode($accountName);
    
    $qrUrl = "https://api.vietqr.io/image/{$bankId}-{$accountNo}-{$template}.jpg?amount={$finalAmount}&addInfo={$addInfo}&accountName={$accNameEncoded}";

    $vietqrData = [
        'bank_id' => $bankId,
        'bank_name' => $config['bank_name'] ?? 'MBBank',
        'account_no' => $accountNo,
        'account_name' => $accountName,
        'amount' => $finalAmount,
        'amount_formatted' => formatVND($finalAmount),
        'add_info' => $orderCode,
        'qr_image_url' => $qrUrl
    ];
}

// Tự động đồng bộ sang Google Sheets nếu đã cấu hình WebApp URL
$syncSuccess = false;
$webhookUrl = $config['google_sheet_webhook'] ?? '';
if (!empty($webhookUrl)) {
    try {
        $itemNames = array_map(function($it) {
            $name = $it['name'] ?? 'Sản phẩm';
            $variant = !empty($it['variant_name']) ? " (" . $it['variant_name'] . ")" : "";
            $qty = $it['quantity'] ?? 1;
            return "{$name}{$variant} x{$qty}";
        }, $items);

        $payload = [
            'action' => 'create_order',
            'orderCode' => $orderCode,
            'customerName' => $customerName,
            'phone' => $phone,
            'address' => $address,
            'note' => $note,
            'paymentMethod' => $paymentMethod === 'vietqr' ? 'VietQR Chuyển Khoản' : 'COD (Thanh toán khi nhận)',
            'totalAmount' => $finalAmount,
            'items' => implode(', ', $itemNames),
            'createdAt' => date('d/m/Y H:i:s')
        ];

        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 400) {
            $syncSuccess = true;
            $pdo->prepare("UPDATE orders SET sync_status = 'synced' WHERE order_code = ?")->execute([$orderCode]);
        }
    } catch (Exception $e) {
        // Log quietly
    }
}

jsonResponse([
    'success' => true,
    'message' => 'Đặt hàng thành công tại ALICE Cosmetic!',
    'order_code' => $orderCode,
    'final_amount' => $finalAmount,
    'final_amount_formatted' => formatVND($finalAmount),
    'payment_method' => $paymentMethod,
    'vietqr_url' => $vietqrData ? $vietqrData['qr_image_url'] : null,
    'vietqr' => $vietqrData,
    'synced_to_sheets' => $syncSuccess
]);
