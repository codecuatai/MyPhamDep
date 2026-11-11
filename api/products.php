<?php
/**
 * API LẤY DANH SÁCH & CHI TIẾT SẢN PHẨM - ALICE COSMETIC
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();

$action = $_GET['action'] ?? 'list';

if ($action === 'detail' || (isset($_GET['id']) && !isset($_GET['category']) && !isset($_GET['search']))) {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        jsonResponse(['error' => 'Không tìm thấy sản phẩm'], 404);
    }

    $product['gallery'] = json_decode($product['gallery'] ?: '[]', true);
    $product['variants'] = json_decode($product['variants'] ?: '[]', true);
    $product['add_on_deals'] = json_decode($product['add_on_deals'] ?: '[]', true);
    $product['add_ons'] = $product['add_on_deals'];
    $product['stock_quantity'] = (int)($product['stock_quantity'] ?? 50);
    $product['in_stock'] = $product['stock_quantity'] > 0;
    $product['is_low_stock'] = $product['stock_quantity'] > 0 && $product['stock_quantity'] <= 10;
    $product['origin'] = $product['origin'] ?: 'Chính hãng nhập khẩu';
    $product['capacity'] = $product['capacity'] ?: 'Tiêu chuẩn';
    $product['expiry'] = $product['expiry'] ?: '36 tháng kể từ NSX (12 tháng sau mở nắp)';

    // Lấy reviews
    $revStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
    $revStmt->execute([$id]);
    $product['reviews'] = $revStmt->fetchAll();

    $response = $product;
    $response['product'] = $product;
    jsonResponse($response);
}

// Mặc định: Lấy danh sách sản phẩm theo bộ lọc
$categoryId = $_GET['category'] ?? 'all';
$skinType   = $_GET['skin_type'] ?? 'all';
$isFlash    = isset($_GET['flash_sale']) ? (int)$_GET['flash_sale'] : null;
$search     = trim($_GET['search'] ?? '');
$sort       = $_GET['sort'] ?? 'popular';

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($categoryId !== 'all' && !empty($categoryId)) {
    $sql .= " AND category_id = ?";
    $params[] = $categoryId;
}

if ($skinType !== 'all' && !empty($skinType)) {
    $sql .= " AND (skin_type = ? OR skin_type = 'all')";
    $params[] = $skinType;
}

if ($isFlash !== null) {
    $sql .= " AND is_flash_sale = ?";
    $params[] = $isFlash;
}

if (!empty($search)) {
    $sql .= " AND (LOWER(name) LIKE LOWER(?) OR LOWER(brand) LIKE LOWER(?) OR LOWER(description) LIKE LOWER(?))";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY rating DESC";
        break;
    case 'popular':
    default:
        $sql .= " ORDER BY sold_count DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

foreach ($products as &$p) {
    $p['gallery'] = json_decode($p['gallery'] ?: '[]', true);
    $p['variants'] = json_decode($p['variants'] ?: '[]', true);
    $p['add_on_deals'] = json_decode($p['add_on_deals'] ?: '[]', true);
    $p['stock_quantity'] = (int)($p['stock_quantity'] ?? 50);
    $p['in_stock'] = $p['stock_quantity'] > 0;
    $p['is_low_stock'] = $p['stock_quantity'] > 0 && $p['stock_quantity'] <= 10;
    $p['origin'] = $p['origin'] ?: 'Chính hãng nhập khẩu';
    $p['capacity'] = $p['capacity'] ?: 'Tiêu chuẩn';
    $p['expiry'] = $p['expiry'] ?: '36 tháng kể từ NSX';
}

jsonResponse([
    'total' => count($products),
    'data' => $products
]);
