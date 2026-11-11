<?php
/**
 * API ĐỒNG BỘ & QUẢN LÝ GIỎ HÀNG THEO TỪNG TÀI KHOẢN (PER-USER CART API)
 * ALICE COSMETIC - Hỗ trợ lưu đám mây và đồng bộ đa thiết bị
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();
$userId = $_SESSION['user']['id'] ?? null;
$action = $_GET['action'] ?? ($_POST['action'] ?? 'get');

// 1. Lấy giỏ hàng của tài khoản hiện tại
if ($action === 'get') {
    if (!$userId) {
        jsonResponse([
            'authenticated' => false,
            'cart' => [],
            'coupon' => null,
            'message' => 'Chưa đăng nhập (dùng giỏ hàng khách)'
        ]);
    }

    $stmt = $pdo->prepare("SELECT cart_json, coupon_json FROM user_carts WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();

    $cart = ($row && !empty($row['cart_json'])) ? json_decode($row['cart_json'], true) : [];
    $coupon = ($row && !empty($row['coupon_json'])) ? json_decode($row['coupon_json'], true) : null;

    jsonResponse([
        'authenticated' => true,
        'user_id' => $userId,
        'cart' => is_array($cart) ? $cart : [],
        'coupon' => $coupon
    ]);
}

// Yêu cầu đăng nhập cho các thao tác lưu/hợp nhất
if (!$userId) {
    jsonResponse(['success' => false, 'message' => 'Vui lòng đăng nhập để đồng bộ giỏ hàng!'], 401);
}

// 2. Lưu giỏ hàng lên máy chủ (Cloud Sync)
if ($action === 'save') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $cart = $input['cart'] ?? [];
    $coupon = $input['coupon'] ?? null;

    $cartJson = json_encode(is_array($cart) ? $cart : [], JSON_UNESCAPED_UNICODE);
    $couponJson = !empty($coupon) ? json_encode($coupon, JSON_UNESCAPED_UNICODE) : null;

    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("INSERT INTO user_carts (user_id, cart_json, coupon_json, updated_at) VALUES (?, ?, ?, ?) ON CONFLICT (user_id) DO UPDATE SET cart_json = EXCLUDED.cart_json, coupon_json = EXCLUDED.coupon_json, updated_at = EXCLUDED.updated_at");
    $stmt->execute([$userId, $cartJson, $couponJson, $now]);

    jsonResponse([
        'success' => true,
        'message' => 'Đã đồng bộ giỏ hàng cá nhân thành công!',
        'count' => count($cart)
    ]);
}

// 3. Hợp nhất giỏ hàng khách khi vừa đăng nhập (Merge Cart on Login)
if ($action === 'merge') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $guestCart = $input['guest_cart'] ?? [];
    $guestCoupon = $input['guest_coupon'] ?? null;

    // Lấy giỏ hàng đang có trong DB của user
    $stmt = $pdo->prepare("SELECT cart_json, coupon_json FROM user_carts WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();

    $userCart = ($row && !empty($row['cart_json'])) ? json_decode($row['cart_json'], true) : [];
    $userCoupon = ($row && !empty($row['coupon_json'])) ? json_decode($row['coupon_json'], true) : null;

    if (!is_array($userCart)) {
        $userCart = [];
    }

    // Tiến hành gộp các sản phẩm khách vừa chọn vào giỏ hàng của user
    if (!empty($guestCart) && is_array($guestCart)) {
        foreach ($guestCart as $guestItem) {
            $foundIndex = -1;
            foreach ($userCart as $idx => $userItem) {
                $userVariant = $userItem['variant_name'] ?? ($userItem['selectedVariant'] ?? '');
                $guestVariant = $guestItem['variant_name'] ?? ($guestItem['selectedVariant'] ?? '');
                if ((string)($userItem['id'] ?? '') === (string)($guestItem['id'] ?? '') && $userVariant === $guestVariant) {
                    $foundIndex = $idx;
                    break;
                }
            }

            if ($foundIndex >= 0) {
                // Tăng số lượng nếu sản phẩm đã có trong giỏ
                $userCart[$foundIndex]['quantity'] = ($userCart[$foundIndex]['quantity'] ?? 1) + ($guestItem['quantity'] ?? 1);
            } else {
                // Thêm mới sản phẩm vào giỏ
                $userCart[] = $guestItem;
            }
        }
    }

    $finalCoupon = $userCoupon ?: $guestCoupon;
    $cartJson = json_encode($userCart, JSON_UNESCAPED_UNICODE);
    $couponJson = !empty($finalCoupon) ? json_encode($finalCoupon, JSON_UNESCAPED_UNICODE) : null;

    // Lưu lại giỏ đã gộp vào Database
    $now = date('Y-m-d H:i:s');
    $saveStmt = $pdo->prepare("INSERT INTO user_carts (user_id, cart_json, coupon_json, updated_at) VALUES (?, ?, ?, ?) ON CONFLICT (user_id) DO UPDATE SET cart_json = EXCLUDED.cart_json, coupon_json = EXCLUDED.coupon_json, updated_at = EXCLUDED.updated_at");
    $saveStmt->execute([$userId, $cartJson, $couponJson, $now]);

    jsonResponse([
        'success' => true,
        'message' => 'Đã hợp nhất giỏ hàng thành công!',
        'cart' => $userCart,
        'coupon' => $finalCoupon
    ]);
}

// 4. Xóa trắng giỏ hàng của user (Ví dụ sau khi thanh toán đơn thành công)
if ($action === 'clear') {
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("UPDATE user_carts SET cart_json = '[]', coupon_json = NULL, updated_at = ? WHERE user_id = ?");
    $stmt->execute([$now, $userId]);

    jsonResponse([
        'success' => true,
        'message' => 'Đã làm mới giỏ hàng cá nhân'
    ]);
}

jsonResponse(['success' => false, 'message' => 'Yêu cầu không hợp lệ'], 400);
