<?php
/**
 * API TRA CỨU ĐƠN HÀNG - ALICE COSMETIC
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();

$action = $_GET['action'] ?? 'lookup';

// 1. Kiểm tra trạng thái thanh toán thời gian thực (Polling Check)
if ($action === 'check_status') {
    $orderCode = cleanInput($_GET['order_code'] ?? ($_POST['order_code'] ?? ''));
    if (!empty($orderCode)) {
        $stmt = $pdo->prepare("SELECT order_code, customer_name, final_amount, payment_status, sync_status, created_at FROM orders WHERE order_code = ? LIMIT 1");
        $stmt->execute([$orderCode]);
        $order = $stmt->fetch();
        if ($order) {
            jsonResponse([
                'success' => true,
                'is_paid' => ($order['payment_status'] === 'paid'),
                'payment_status' => $order['payment_status'],
                'order' => $order
            ]);
        }
    }
    jsonResponse(['success' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
}

// 2. Xác nhận thanh toán (Từ điện thoại hoặc Webhook)
if ($action === 'confirm_payment') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $orderCode = cleanInput($input['order_code'] ?? ($_GET['order_code'] ?? ''));
    if (!empty($orderCode)) {
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = 'paid' WHERE order_code = ?");
        $stmt->execute([$orderCode]);
        jsonResponse([
            'success' => true, 
            'message' => 'Đã xác nhận thanh toán thành công cho đơn hàng #' . $orderCode,
            'order_code' => $orderCode,
            'payment_status' => 'paid'
        ]);
    }
    jsonResponse(['success' => false, 'message' => 'Mã đơn không hợp lệ'], 400);
}

// Tra cứu theo số điện thoại hoặc mã đơn
$query = trim($_GET['q'] ?? '');

if (empty($query)) {
    // Trả về 10 đơn gần nhất (đối với view client)
    $stmt = $pdo->query("SELECT order_code, customer_name, phone, final_amount, payment_method, payment_status, created_at, items_json FROM orders ORDER BY id DESC LIMIT 10");
    $orders = $stmt->fetchAll();
    foreach ($orders as &$o) {
        $o['items'] = json_decode($o['items_json'] ?: '[]', true);
        $o['final_amount_formatted'] = formatVND($o['final_amount']);
    }
    jsonResponse(['orders' => $orders]);
}

$stmt = $pdo->prepare("SELECT order_code, customer_name, phone, address, final_amount, payment_method, payment_status, created_at, items_json FROM orders WHERE order_code = ? OR phone = ? ORDER BY id DESC");
$stmt->execute([$query, $query]);
$orders = $stmt->fetchAll();

foreach ($orders as &$o) {
    $o['items'] = json_decode($o['items_json'] ?: '[]', true);
    $o['final_amount_formatted'] = formatVND($o['final_amount']);
}

jsonResponse(['orders' => $orders]);
