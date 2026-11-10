<?php
/**
 * API LẤY DANH SÁCH & CHI TIẾT SẢN PHẨM - ALICE COSMETIC
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();

$action = $_GET['action'] ?? 'list';

if ($action === 'detail') {
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

    // Lấy reviews
    $revStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
    $revStmt->execute([$id]);
    $product['reviews'] = $revStmt->fetchAll();

    jsonResponse($product);
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
    $sql .= " AND (name LIKE ? OR brand LIKE ? OR description LIKE ?)";
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
}

jsonResponse([
    'total' => count($products),
    'data' => $products
]);
