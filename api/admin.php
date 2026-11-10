<?php
/**
 * API QUẢN TRỊ TOÀN DIỆN (FULL ADMIN API) - ALICE COSMETIC
 * Quản lý Đơn hàng, Sản phẩm, Mã giảm giá, Thống kê & Cấu hình VietQR
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();

// KIỂM TRA QUYỀN QUẢN TRỊ VIÊN (ADMIN AUTH GUARD)
if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    jsonResponse(['success' => false, 'message' => 'Bạn không có quyền truy cập trang quản trị!'], 403);
}

$action = $_GET['action'] ?? ($_POST['action'] ?? 'stats');

// 1. Thống kê tổng quan (Dashboard Stats)
if ($action === 'stats') {
    $totalOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $pendingOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'pending'")->fetchColumn();
    $paidOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
    $totalRevenue = (float)$pdo->query("SELECT COALESCE(SUM(final_amount), 0) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
    $totalProducts = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    $recentOrdersStmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
    $recentOrders = $recentOrdersStmt->fetchAll();

    jsonResponse([
        'success' => true,
        'stats' => [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'paid_orders' => $paidOrders,
            'total_revenue' => $totalRevenue,
            'total_products' => $totalProducts,
            'total_users' => $totalUsers
        ],
        'recent_orders' => $recentOrders
    ]);
}

// 2. Quản lý Đơn hàng (Orders)
if ($action === 'orders') {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 200");
    $orders = $stmt->fetchAll();
    jsonResponse(['success' => true, 'orders' => $orders]);
}

if ($action === 'update_order_status') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $orderId = (int)($input['order_id'] ?? 0);
    $paymentStatus = cleanInput($input['payment_status'] ?? 'pending');
    $syncStatus = cleanInput($input['sync_status'] ?? 'pending');

    if (!$orderId) {
        jsonResponse(['success' => false, 'message' => 'Thiếu ID đơn hàng'], 400);
    }

    $stmt = $pdo->prepare("UPDATE orders SET payment_status = ?, sync_status = ? WHERE id = ?");
    $stmt->execute([$paymentStatus, $syncStatus, $orderId]);

    jsonResponse(['success' => true, 'message' => "Đã cập nhật trạng thái đơn hàng #$orderId!"]);
}

// 3. Quản lý Sản phẩm (Products)
if ($action === 'products') {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
    jsonResponse(['success' => true, 'products' => $products]);
}

if ($action === 'save_product') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = !empty($input['id']) ? (int)$input['id'] : null;
    $name = cleanInput($input['name'] ?? '');
    $brand = cleanInput($input['brand'] ?? 'ALICE');
    $categoryId = cleanInput($input['category_id'] ?? 'son-moi');
    $price = (float)($input['price'] ?? 0);
    $originalPrice = (float)($input['original_price'] ?? $price);
    $image = trim($input['image'] ?? '');
    $description = cleanInput($input['description'] ?? '');
    $ingredients = cleanInput($input['ingredients'] ?? '');
    $howToUse = cleanInput($input['how_to_use'] ?? '');
    $isFlashSale = !empty($input['is_flash_sale']) ? 1 : 0;
    $flashSaleStock = (int)($input['flash_sale_stock'] ?? 50);
    $skinType = cleanInput($input['skin_type'] ?? 'all');

    if (empty($name) || $price <= 0 || empty($image)) {
        jsonResponse(['success' => false, 'message' => 'Vui lòng điền tên, giá bán và link ảnh sản phẩm!'], 400);
    }

    $customSlug = trim($input['slug'] ?? '');
    $slug = !empty($customSlug) ? slugify($customSlug) : slugify($name);
    if (empty($slug)) {
        $slug = 'san-pham-' . time();
    }
    $discountPercent = ($originalPrice > $price) ? round((($originalPrice - $price) / $originalPrice) * 100) : 0;

    if ($id) {
        // Cập nhật sản phẩm
        $stmt = $pdo->prepare("UPDATE products SET 
            name = ?, slug = ?, brand = ?, category_id = ?, price = ?, original_price = ?, 
            discount_percent = ?, image = ?, description = ?, ingredients = ?, how_to_use = ?, 
            is_flash_sale = ?, flash_sale_stock = ?, skin_type = ? 
            WHERE id = ?");
        $stmt->execute([
            $name, $slug, $brand, $categoryId, $price, $originalPrice,
            $discountPercent, $image, $description, $ingredients, $howToUse,
            $isFlashSale, $flashSaleStock, $skinType, $id
        ]);
        // Tự động làm mới sitemap.xml
        @file_get_contents(getBaseUrl() . '/sitemap.php');
        jsonResponse(['success' => true, 'message' => 'Cập nhật sản phẩm thành công!']);
    } else {
        // Thêm mới sản phẩm
        $stmt = $pdo->prepare("INSERT INTO products 
            (name, slug, brand, category_id, price, original_price, discount_percent, image, description, ingredients, how_to_use, is_flash_sale, flash_sale_stock, flash_sale_sold, skin_type, rating, review_count, sold_count) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 5.0, 1, 0)");
        $stmt->execute([
            $name, $slug, $brand, $categoryId, $price, $originalPrice,
            $discountPercent, $image, $description, $ingredients, $howToUse,
            $isFlashSale, $flashSaleStock, $skinType
        ]);
        // Tự động làm mới sitemap.xml
        @file_get_contents(getBaseUrl() . '/sitemap.php');
        jsonResponse(['success' => true, 'message' => 'Thêm mới sản phẩm mỹ phẩm thành công!']);
    }
}

if ($action === 'delete_product') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $id = (int)($input['id'] ?? 0);
    if (!$id) {
        jsonResponse(['success' => false, 'message' => 'Thiếu ID sản phẩm!'], 400);
    }
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    jsonResponse(['success' => true, 'message' => 'Đã xóa sản phẩm thành công!']);
}

// 4. Quản lý Mã giảm giá (Coupons)
if ($action === 'coupons') {
    $stmt = $pdo->query("SELECT * FROM coupons ORDER BY code ASC");
    $coupons = $stmt->fetchAll();
    jsonResponse(['success' => true, 'coupons' => $coupons]);
}

if ($action === 'save_coupon') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $code = strtoupper(trim(cleanInput($input['code'] ?? '')));
    $discountType = cleanInput($input['discount_type'] ?? 'fixed');
    $discountValue = (float)($input['discount_value'] ?? 0);
    $minOrder = (float)($input['min_order'] ?? 0);
    $maxDiscount = (float)($input['max_discount'] ?? 0);
    $description = cleanInput($input['description'] ?? '');

    if (empty($code) || $discountValue <= 0) {
        jsonResponse(['success' => false, 'message' => 'Vui lòng nhập mã voucher và giá trị giảm!'], 400);
    }

    $stmt = $pdo->prepare("INSERT OR REPLACE INTO coupons (code, discount_type, discount_value, min_order, max_discount, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$code, $discountType, $discountValue, $minOrder, $maxDiscount, $description]);

    jsonResponse(['success' => true, 'message' => "Đã tạo/cập nhật mã voucher $code thành công!"]);
}

if ($action === 'delete_coupon') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $code = strtoupper(trim($input['code'] ?? ''));
    if (empty($code)) {
        jsonResponse(['success' => false, 'message' => 'Thiếu mã voucher!'], 400);
    }
    $pdo->prepare("DELETE FROM coupons WHERE code = ?")->execute([$code]);
    jsonResponse(['success' => true, 'message' => "Đã xóa mã voucher $code!"]);
}

// 5. Cài đặt Cửa hàng & VietQR (Settings)
if ($action === 'settings') {
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
        jsonResponse(['success' => true, 'message' => 'Đã lưu cấu hình Cửa hàng & VietQR thành công!']);
    } else {
        $config = getShopConfig($pdo);
        jsonResponse(['success' => true, 'config' => $config, 'banks' => VIETQR_BANKS]);
    }
}

// 6. Danh sách thành viên (Users)
if ($action === 'users') {
    $stmt = $pdo->query("SELECT id, fullname, email, phone, avatar, role, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll();
    jsonResponse(['success' => true, 'users' => $users]);
}

jsonResponse(['success' => false, 'message' => 'Hành động không hợp lệ'], 400);
