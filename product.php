<?php
/**
 * TRANG CHI TIẾT SẢN PHẨM CHUẨN SEO (SERVER-SIDE RENDERED) - ALICE COSMETIC
 * Đầy đủ thẻ Meta động, Schema Product JSON-LD, Breadcrumb, Open Graph & Canonical
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$shopConfig = getShopConfig($pdo);
$baseUrl = getBaseUrl();

$slug = cleanText($_GET['slug'] ?? '');
$id = (int)($_GET['id'] ?? 0);

$product = null;
if (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ?");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();
} elseif ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
}

// Xử lý 404 nếu không tìm thấy sản phẩm
if (!$product) {
    http_response_code(404);
    $pageTitle = "404 - Không tìm thấy sản phẩm — " . htmlspecialchars($shopConfig['shop_name']);
    $pageDesc = "Sản phẩm bạn đang tìm kiếm không tồn tại hoặc đã ngừng kinh doanh tại ALICE Cosmetic.";
    $canonicalUrl = $baseUrl . '/';
    $ogImage = "https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=700&q=80";
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $pageTitle ?></title>
        <meta name="robots" content="noindex, follow">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
        <link href="<?= $baseUrl ?>/assets/css/style.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" rel="stylesheet">
        <?php include __DIR__ . '/templates/social_auth_head.php'; ?>
    </head>
    <body id="top" style="background: var(--bg-soft);">
        <div id="alice-toast-container" class="alice-toast-container"></div>
        <?php include __DIR__ . '/templates/header.php'; ?>
        <div class="container py-5 text-center my-5">
            <div class="p-5 bg-white rounded-4 shadow-sm mx-auto" style="max-width: 550px;">
                <div class="display-1 text-danger mb-3">🌸 404</div>
                <h2 class="font-bold mb-3">Ôi, Không Tìm Thấy Sản Phẩm!</h2>
                <p class="text-muted mb-4">Sản phẩm này có thể đã hết hàng hoặc đường dẫn đã thay đổi. Bạn hãy khám phá các bộ sưu tập khác nhé!</p>
                <a href="<?= $baseUrl ?>/" class="btn-hero-cta text-decoration-none">
                    <i class="fa-solid fa-house me-1"></i>Về Trang Chủ ALICE
                </a>
            </div>
        </div>
        <?php
        include __DIR__ . '/templates/footer.php';
        include __DIR__ . '/templates/modals.php';
        include __DIR__ . '/templates/mobile_nav.php';
        include __DIR__ . '/templates/floating_contact.php';
        ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script>window.ALICE_BASE_URL = <?= json_encode($baseUrl) ?>;</script>
        <script src="<?= $baseUrl ?>/assets/js/app.js?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>"></script>
    </body>
    </html>
    <?php
    exit;
}

// Giải mã dữ liệu JSON bổ sung
$gallery = json_decode($product['gallery'] ?: '[]', true);
if (!is_array($gallery) || empty($gallery)) {
    $gallery = [$product['image']];
}
$variants = json_decode($product['variants'] ?: '[]', true);
if (!is_array($variants)) {
    $variants = [];
}

// Lấy thông tin danh mục
$catStmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$catStmt->execute([$product['category_id']]);
$category = $catStmt->fetch() ?: ['name' => 'Mỹ phẩm', 'slug' => 'all'];

// Lấy đánh giá
$revStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
$revStmt->execute([$product['id']]);
$reviews = $revStmt->fetchAll();

// Lấy sản phẩm liên quan
$relStmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? ORDER BY sold_count DESC LIMIT 4");
$relStmt->execute([$product['category_id'], $product['id']]);
$relatedProducts = $relStmt->fetchAll();

// Thiết lập thông số SEO
$pageTitle = htmlspecialchars($product['name']) . " Chính Hãng — ALICE Cosmetic";
$pageDesc = truncateSeoDescription(!empty($product['description']) ? $product['description'] : ($product['name'] . ' chính hãng tại ALICE Cosmetic. Freeship toàn quốc từ ' . formatVND($shopConfig['freeship_min']) . ', tặng kèm hộp quà nơ hồng pastel & thiệp viết tay xinh xắn.'));
$canonicalUrl = $baseUrl . '/san-pham/' . $product['slug'];
$ogImage = (strpos($product['image'], 'http') === 0) ? $product['image'] : ($baseUrl . '/' . ltrim($product['image'], '/'));

// Dữ liệu có cấu trúc Schema JSON-LD
$productSchema = generateProductJsonLd($product, $shopConfig, $baseUrl);
$breadcrumbSchema = generateBreadcrumbJsonLd([
    'Trang chủ' => $baseUrl . '/',
    $category['name'] => $baseUrl . '/danh-muc/' . $category['slug'],
    $product['name'] => $canonicalUrl
], $baseUrl);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <meta name="description" content="<?= $pageDesc ?>">
    <link rel="canonical" href="<?= $canonicalUrl ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="<?= htmlspecialchars($product['brand'] ?: $shopConfig['shop_name']) ?>">

    <!-- Open Graph (Facebook / Zalo chia sẻ link) -->
    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="product">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $pageDesc ?>">
    <meta property="og:url" content="<?= $canonicalUrl ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($shopConfig['shop_name']) ?>">
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="product:price:amount" content="<?= $product['price'] ?>">
    <meta property="product:price:currency" content="VND">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $pageTitle ?>">
    <meta name="twitter:description" content="<?= $pageDesc ?>">
    <meta name="twitter:image" content="<?= $ogImage ?>">

    <!-- Resource Hints & Favicon -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌸</text></svg>">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>/assets/css/style.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" rel="stylesheet">

    <!-- Dữ liệu có cấu trúc Schema.org JSON-LD (Rich Snippets Google sao vàng & giá tiền) -->
    <script type="application/ld+json">
    <?= json_encode($productSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <script type="application/ld+json">
    <?= json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <?php include __DIR__ . '/templates/social_auth_head.php'; ?>
</head>
<body id="top" style="background: #fffbfd;">

    <!-- Toast Notification Container -->
    <div id="alice-toast-container" class="alice-toast-container"></div>

    <?php 
    // Header & Navigation
    include __DIR__ . '/templates/header.php';
    ?>

    <!-- Breadcrumb SEO Navigation -->
    <div class="py-2 border-bottom bg-white">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small" style="font-size: 0.82rem;">
                    <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/danh-muc/<?= htmlspecialchars($category['slug']) ?>" class="text-decoration-none text-muted"><?= htmlspecialchars($category['name']) ?></a></li>
                    <li class="breadcrumb-item active text-truncate font-bold" aria-current="page" style="color: var(--primary-dark); max-width: 320px;"><?= htmlspecialchars($product['name']) ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Product Detail View -->
    <main class="container py-4">
        <div class="bg-white rounded-4 shadow-sm p-3 p-md-4 mb-4 border" style="border-color: var(--primary-pastel) !important;">
            <div class="row g-4">
                <!-- Cột Trái: Ảnh Gallery -->
                <div class="col-lg-5 col-md-6">
                    <div class="product-gallery-wrap position-relative">
                        <span class="badge-mall-official position-absolute top-0 start-0 m-3 z-2">CHÍNH HÃNG 100%</span>
                        <?php if ($product['discount_percent'] > 0): ?>
                            <span class="badge-discount-tag position-absolute top-0 end-0 m-3 z-2">-<?= $product['discount_percent'] ?>%</span>
                        <?php endif; ?>
                        
                        <div class="main-image-box text-center p-2 rounded-4 overflow-hidden mb-3 border" style="background: #fff; max-height: 420px;">
                            <img id="detail-main-img" src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded-3" style="max-height: 400px; width: 100%; object-fit: contain;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80';">
                        </div>

                        <?php if (count($gallery) > 1): ?>
                            <div class="d-flex gap-2 overflow-auto pb-2">
                                <?php foreach ($gallery as $idx => $gImg): ?>
                                    <img src="<?= htmlspecialchars($gImg) ?>" alt="<?= htmlspecialchars($product['name']) ?> ảnh <?= $idx + 1 ?>" class="rounded-3 border thumbnail-click <?= $idx === 0 ? 'border-danger' : '' ?>" style="width: 72px; height: 72px; object-fit: cover; cursor: pointer;" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80';" onclick="document.getElementById('detail-main-img').src = this.src; document.querySelectorAll('.thumbnail-click').forEach(el => el.classList.remove('border-danger')); this.classList.add('border-danger');">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Cột Phải: Thông tin & Mua hàng -->
                <div class="col-lg-7 col-md-6 d-flex flex-column">
                    <div class="mb-2">
                        <span class="badge rounded-pill px-3 py-1 font-bold" style="background: var(--primary-pastel); color: var(--primary-dark); font-size: 0.78rem;">
                            <?= htmlspecialchars($product['brand'] ?: 'ALICE Official') ?>
                        </span>
                        <span class="small text-muted ms-2">Mã SKU: ALICE-SKU-<?= $product['id'] ?></span>
                    </div>

                    <h1 class="font-bold mb-2" style="font-family: var(--font-heading); font-size: 1.65rem; color: var(--text-main); line-height: 1.35;">
                        <?= htmlspecialchars($product['name']) ?>
                    </h1>

                    <!-- Rating, Sold & Stock Row -->
                    <div class="d-flex align-items-center flex-wrap gap-2 small mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center text-warning">
                            <span class="font-bold me-1 text-dark"><?= number_format($product['rating'], 1) ?></span>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star-half-stroke"></i>
                        </div>
                        <span class="text-muted">•</span>
                        <span class="text-muted"><strong class="text-dark"><?= count($reviews) ?: 12 ?></strong> Đánh giá thực</span>
                        <span class="text-muted">•</span>
                        <span class="text-muted">Đã bán <strong class="text-dark"><?= number_format($product['sold_count']) ?></strong></span>
                        <span class="text-muted">•</span>
                        <?php 
                        $stockQty = (int)($product['stock_quantity'] ?? 50);
                        if ($stockQty > 10): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>Còn hàng (<?= $stockQty ?>)</span>
                        <?php elseif ($stockQty > 0): ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="fa-solid fa-fire me-1"></i>Sắp hết hàng (Chỉ còn <?= $stockQty ?>)</span>
                        <?php else: ?>
                            <span class="badge bg-danger text-white px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i>Tạm hết hàng</span>
                        <?php endif; ?>
                    </div>

                    <!-- Khung thông số nhanh (Xuất xứ, Dung tích, HSD) -->
                    <div class="row g-2 mb-3 small">
                        <div class="col-4">
                            <div class="p-2 rounded-3 text-center border" style="background: #fafafa;">
                                <div class="text-muted" style="font-size: 0.72rem;"><i class="fa-solid fa-earth-americas me-1 text-danger"></i>Xuất xứ</div>
                                <strong class="text-dark text-truncate d-block" style="font-size: 0.85rem;"><?= htmlspecialchars($product['origin'] ?: 'Hàn Quốc') ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded-3 text-center border" style="background: #fafafa;">
                                <div class="text-muted" style="font-size: 0.72rem;"><i class="fa-solid fa-flask-vial me-1 text-primary"></i>Dung tích</div>
                                <strong class="text-dark text-truncate d-block" style="font-size: 0.85rem;"><?= htmlspecialchars($product['capacity'] ?: 'Tiêu chuẩn') ?></strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded-3 text-center border" style="background: #fafafa;">
                                <div class="text-muted" style="font-size: 0.72rem;"><i class="fa-regular fa-calendar-check me-1 text-success"></i>Hạn sử dụng</div>
                                <strong class="text-dark text-truncate d-block" style="font-size: 0.85rem;"><?= htmlspecialchars($product['expiry'] ?: '36 tháng') ?></strong>
                            </div>
                        </div>
                    </div>

                    <?php
                    $firstVariant = !empty($variants) ? $variants[0] : null;
                    $initialVariantName = is_array($firstVariant) ? ($firstVariant['name'] ?? '') : (is_string($firstVariant) ? $firstVariant : '');
                    $basePrice = (float)$product['price'];
                    $baseOriginalPrice = (float)$product['original_price'];
                    $initialPrice = (is_array($firstVariant) && isset($firstVariant['price']) && (float)$firstVariant['price'] > 0)
                        ? (float)$firstVariant['price']
                        : $basePrice;
                    $initialOriginalPrice = (is_array($firstVariant) && isset($firstVariant['original_price']) && (float)$firstVariant['original_price'] > 0)
                        ? (float)$firstVariant['original_price']
                        : $baseOriginalPrice;
                    ?>
                    <!-- Price Box (Shopee Style Tone Hồng) -->
                    <div class="p-3 rounded-4 mb-3" style="background: var(--primary-soft); border: 1px dashed var(--primary);">
                        <div class="d-flex align-items-baseline flex-wrap gap-3">
                            <span id="detail-price-current" class="fs-2 font-bold" style="color: var(--primary-dark); font-family: var(--font-heading);"><?= formatVND($initialPrice) ?></span>
                            <span id="detail-price-original" class="text-muted text-decoration-line-through fs-5<?= $initialOriginalPrice > $initialPrice ? '' : ' d-none' ?>"><?= formatVND($initialOriginalPrice) ?></span>
                            <span id="detail-price-saving" class="badge bg-danger rounded-pill px-2 py-1 small<?= $initialOriginalPrice > $initialPrice ? '' : ' d-none' ?>">Tiết kiệm <?= formatVND(max(0, $initialOriginalPrice - $initialPrice)) ?></span>
                        </div>
                        <div class="small text-muted mt-2 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-gift text-danger"></i>
                            <span>Tặng kèm hộp nơ hồng pastel & thiệp tay độc quyền mọi đơn hàng</span>
                        </div>
                    </div>

                    <!-- Variants selection if available -->
                    <?php if (!empty($variants)): ?>
                        <div class="mb-3">
                            <div class="small font-bold mb-2">Phân loại / Dung tích:</div>
                            <div class="d-flex flex-wrap gap-2" id="detail-variants-wrap">
                                <?php foreach ($variants as $idx => $v):
                                    $vName = is_array($v) ? ($v['name'] ?? '') : (string)$v;
                                    $vPrice = (is_array($v) && isset($v['price']) && (float)$v['price'] > 0) ? (float)$v['price'] : $basePrice;
                                    $vOriginal = (is_array($v) && isset($v['original_price']) && (float)$v['original_price'] > 0) ? (float)$v['original_price'] : $baseOriginalPrice;
                                ?>
                                    <button type="button"
                                            class="btn btn-sm <?= $idx === 0 ? 'btn-danger' : 'btn-outline-secondary' ?> rounded-pill px-3 py-1 font-semibold variant-select-btn"
                                            data-variant-name="<?= htmlspecialchars($vName, ENT_QUOTES, 'UTF-8') ?>"
                                            data-variant-price="<?= $vPrice ?>"
                                            data-variant-original-price="<?= $vOriginal ?>"
                                            onclick="window.selectProductVariant(this)">
                                        <?= htmlspecialchars($vName) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-2 mb-4 mt-2">
                        <?php if ($stockQty > 0): ?>
                            <button type="button" class="btn-hero-cta flex-grow-1 border-0 py-3" onclick="window.addCurrentPageProductToCart(true)">
                                <i class="fa-solid fa-bolt-lightning me-1"></i>Mua Ngay Nhanh Chóng
                            </button>
                            <button type="button" class="btn-pdp-add-cart px-4 py-3" onclick="window.addCurrentPageProductToCart(false)">
                                <i class="fa-solid fa-cart-plus me-1"></i>Thêm Vào Giỏ
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary flex-grow-1 rounded-pill py-3 font-bold" disabled>
                                <i class="fa-solid fa-circle-xmark me-1"></i>Sản Phẩm Tạm Hết Hàng
                            </button>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars($shopConfig['zalo_url']) ?>" target="_blank" class="btn btn-light rounded-pill px-3 py-3 border font-semibold text-primary d-inline-flex align-items-center justify-content-center" title="Tư vấn soi da qua Zalo">
                            <i class="fa-solid fa-comment-dots me-1"></i>Tư vấn Zalo
                        </a>
                    </div>

                    <!-- Trust Perks Bar -->
                    <div class="p-3 bg-light rounded-3 small text-muted mt-auto">
                        <div class="row g-2">
                            <div class="col-6"><i class="fa-solid fa-shield-heart text-danger me-1"></i>100% Chính hãng có tem phụ</div>
                            <div class="col-6"><i class="fa-solid fa-truck-fast text-success me-1"></i>Freeship từ <?= formatVND($shopConfig['freeship_min']) ?></div>
                            <div class="col-6"><i class="fa-solid fa-rotate-left text-primary me-1"></i>Đổi trả 7 ngày nếu kích ứng</div>
                            <div class="col-6"><i class="fa-solid fa-qrcode text-warning me-1"></i>VietQR tự động xác nhận 24/7</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Nội dung chi tiết & Đánh giá -->
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Mô tả & Thành phần chuẩn SEO -->
                <article class="bg-white rounded-4 shadow-sm p-4 mb-4 border">
                    <h2 class="font-bold pb-2 border-bottom mb-3" style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--text-main);">
                        Thông Số Chi Tiết Sản Phẩm
                    </h2>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-striped align-middle small mb-0">
                            <tbody>
                                <tr>
                                    <th class="bg-light text-muted" style="width: 30%;">Thương hiệu</th>
                                    <td class="fw-bold text-danger"><?= htmlspecialchars($product['brand'] ?: 'ALICE Official') ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-muted">Xuất xứ sản xuất</th>
                                    <td><?= htmlspecialchars($product['origin'] ?: 'Hàn Quốc') ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-muted">Dung tích / Trọng lượng</th>
                                    <td><?= htmlspecialchars($product['capacity'] ?: 'Tiêu chuẩn') ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-muted">Hạn sử dụng</th>
                                    <td><?= htmlspecialchars($product['expiry'] ?: '36 tháng kể từ NSX (12 tháng sau khi mở nắp)') ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-muted">Loại da phù hợp</th>
                                    <td><?= htmlspecialchars($product['skin_type'] === 'all' ? 'Mọi loại da, kể cả da nhạy cảm' : ($product['skin_type'] ?: 'Mọi loại da')) ?></td>
                                </tr>
                                <tr>
                                    <th class="bg-light text-muted">Tình trạng kho hàng</th>
                                    <td>
                                        <?php if ($stockQty > 10): ?>
                                            <span class="badge bg-success text-white px-2 py-1"><i class="fa-solid fa-boxes-stacked me-1"></i>Sẵn sàng giao ngay (Còn <?= $stockQty ?> sản phẩm)</span>
                                        <?php elseif ($stockQty > 0): ?>
                                            <span class="badge bg-warning text-dark px-2 py-1"><i class="fa-solid fa-triangle-exclamation me-1"></i>Sắp hết hàng (Chỉ còn <?= $stockQty ?> sản phẩm)</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger text-white px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i>Tạm hết hàng</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h2 class="font-bold pb-2 border-bottom mb-3" style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--text-main);">
                        Mô Tả Sản Phẩm & Công Dụng Chi Tiết
                    </h2>
                    <div class="product-description-content text-muted" style="line-height: 1.8; font-size: 0.95rem;">
                        <?= nl2br(htmlspecialchars($product['description'] ?: 'Sản phẩm chính hãng được nhập khẩu và phân phối bởi ALICE Cosmetic. Phù hợp chăm sóc da hàng ngày.')) ?>
                    </div>

                    <?php if (!empty($product['ingredients'])): ?>
                        <h2 class="font-bold pb-2 border-bottom mt-4 mb-3" style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--text-main);">
                            Bảng Thành Phần Chính
                        </h2>
                        <div class="small text-muted p-3 rounded-3" style="background: var(--bg-soft); line-height: 1.7;">
                            <?= nl2br(htmlspecialchars($product['ingredients'])) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($product['how_to_use'])): ?>
                        <h2 class="font-bold pb-2 border-bottom mt-4 mb-3" style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--text-main);">
                            Hướng Dẫn Sử Dụng & Routine Gợi Ý
                        </h2>
                        <div class="small text-muted p-3 rounded-3" style="background: var(--bg-soft); line-height: 1.7;">
                            <?= nl2br(htmlspecialchars($product['how_to_use'])) ?>
                        </div>
                    <?php endif; ?>
                </article>

                <!-- Đánh giá khách hàng -->
                <section class="bg-white rounded-4 shadow-sm p-4 mb-4 border">
                    <h2 class="font-bold pb-2 border-bottom mb-3" style="font-family: var(--font-heading); font-size: 1.35rem; color: var(--text-main);">
                        Đánh Giá Từ Khách Hàng (<?= count($reviews) ?: 1 ?>)
                    </h2>
                    <?php if (!empty($reviews)): ?>
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($reviews as $rev): ?>
                                <div class="p-3 rounded-3 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <div class="font-bold small"><?= htmlspecialchars($rev['author_name']) ?> <span class="badge bg-light text-muted border ms-1 font-normal"><?= htmlspecialchars($rev['skin_type'] ?: 'Khách hàng đã mua') ?></span></div>
                                        <div class="text-warning small">
                                            <?= str_repeat('★', (int)$rev['rating']) ?>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-1"><?= htmlspecialchars($rev['comment']) ?></p>
                                    <div class="small text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($rev['created_at'] ?: date('Y-m-d')) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-3 rounded-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="font-bold small">Thu Hương <span class="badge bg-light text-muted border ms-1">Đã mua tại ALICE</span></div>
                                <div class="text-warning small">★★★★★</div>
                            </div>
                            <p class="small text-muted mb-0">Hàng chính hãng đóng hộp rất đẹp, có thiệp viết tay xinh xắn, giao hàng nhanh. Rất hài lòng!</p>
                        </div>
                    <?php endif; ?>
                </section>
            </div>

            <!-- Cột Phải: Sản phẩm liên quan -->
            <div class="col-lg-4">
                <aside class="bg-white rounded-4 shadow-sm p-3 border mb-4">
                    <h2 class="font-bold pb-2 border-bottom mb-3" style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--text-main);">
                        Sản Phẩm Cùng Bộ Sưu Tập
                    </h2>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($relatedProducts as $rp): ?>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="<?= $baseUrl ?>/san-pham/<?= htmlspecialchars($rp['slug']) ?>" class="flex-shrink-0" style="width: 70px; height: 70px;">
                                    <img src="<?= htmlspecialchars($rp['image']) ?>" alt="<?= htmlspecialchars($rp['name']) ?>" class="w-100 h-100 rounded-3 border" style="object-fit: cover;" loading="lazy" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80';">
                                </a>
                                <div class="small">
                                    <a href="<?= $baseUrl ?>/san-pham/<?= htmlspecialchars($rp['slug']) ?>" class="text-decoration-none text-dark font-semibold d-block text-truncate-2" style="font-size: 0.85rem;">
                                        <?= htmlspecialchars($rp['name']) ?>
                                    </a>
                                    <div class="text-danger font-bold mt-1"><?= formatVND($rp['price']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <?php 
    // Footer & Modals
    include __DIR__ . '/templates/footer.php';
    include __DIR__ . '/templates/modals.php';
    include __DIR__ . '/templates/mobile_nav.php';
    include __DIR__ . '/templates/floating_contact.php';
    ?>

    <!-- Bootstrap 5.3 & App Engine -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.ALICE_BASE_URL = <?= json_encode($baseUrl) ?>;
        window.PRODUCT_PAGE_STATE = {
            id: <?= (int)$product['id'] ?>,
            name: <?= json_encode($product['name'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            image: <?= json_encode($product['image'], JSON_UNESCAPED_SLASHES) ?>,
            price: <?= $initialPrice ?>,
            originalPrice: <?= $initialOriginalPrice ?>,
            variantName: <?= json_encode($initialVariantName, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            stockQuantity: <?= (int)$stockQty ?>
        };
        window.selectProductVariant = function(btn) {
            document.querySelectorAll('.variant-select-btn').forEach(b => {
                b.classList.remove('btn-danger');
                b.classList.add('btn-outline-secondary');
            });
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-danger');

            const vName = btn.getAttribute('data-variant-name') || '';
            const vPrice = parseFloat(btn.getAttribute('data-variant-price')) || window.PRODUCT_PAGE_STATE.price;
            const vOrig = parseFloat(btn.getAttribute('data-variant-original-price')) || 0;

            window.PRODUCT_PAGE_STATE.variantName = vName;
            window.PRODUCT_PAGE_STATE.price = vPrice;
            window.PRODUCT_PAGE_STATE.originalPrice = vOrig;

            const fmt = (n) => new Intl.NumberFormat('vi-VN').format(Math.round(n)) + '₫';
            const elCur = document.getElementById('detail-price-current');
            const elOrig = document.getElementById('detail-price-original');
            const elSav = document.getElementById('detail-price-saving');
            if (elCur) elCur.textContent = fmt(vPrice);
            if (elOrig && elSav) {
                if (vOrig > vPrice) {
                    elOrig.textContent = fmt(vOrig);
                    elOrig.classList.remove('d-none');
                    elSav.textContent = 'Tiết kiệm ' + fmt(vOrig - vPrice);
                    elSav.classList.remove('d-none');
                } else {
                    elOrig.classList.add('d-none');
                    elSav.classList.add('d-none');
                }
            }
        };
        window.addCurrentPageProductToCart = function(buyNow = false) {
            const s = window.PRODUCT_PAGE_STATE;
            const app = window.AliceApp || (typeof AliceApp !== 'undefined' ? AliceApp : null);
            if (!app) {
                console.error('AliceApp is not loaded yet.');
                return;
            }
            if (typeof s.stockQuantity !== 'undefined' && Number(s.stockQuantity) <= 0) {
                app.showToast('Sản phẩm "' + s.name + '" tạm thời đã hết hàng trong kho!', 'warning');
                return;
            }
            if (buyNow) {
                app.addItemToCart({
                    id: s.id,
                    name: s.name,
                    image: s.image,
                    price: s.price,
                    variant_name: s.variantName || '',
                    quantity: 1
                });
                const label = s.variantName ? `${s.name} (${s.variantName})` : s.name;
                app.showToast(`Đã chọn mua "${label}"`, 'success');
                if (typeof app.openCheckoutModal === 'function') {
                    app.openCheckoutModal();
                } else {
                    app.openCartDrawer();
                }
            } else if (typeof app.quickAddToCart === 'function') {
                app.quickAddToCart(s.id, s.name, s.image, s.price, s.variantName || '');
            }
        };
    </script>
    <script src="<?= $baseUrl ?>/assets/js/app.js?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>"></script>
</body>
</html>
