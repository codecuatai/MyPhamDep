<?php
/**
 * TRANG CHI TIẾT CẨM NANG LÀM ĐẸP CHUẨN SEO (SERVER-SIDE RENDERED) - ALICE COSMETIC
 * Tích hợp Schema Article, BreadcrumbList, Open Graph, & Khối Sản Phẩm Gợi Ý Chốt Đơn
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$shopConfig = getShopConfig($pdo);
$baseUrl = getBaseUrl();

$slug = cleanInput($_GET['slug'] ?? '');
$post = null;

if (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ?");
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
}

// Xử lý 404 nếu không tìm thấy bài viết
if (!$post) {
    http_response_code(404);
    $pageTitle = "404 - Không tìm thấy bài viết — " . htmlspecialchars($shopConfig['shop_name']);
    $pageDesc = "Bài viết cẩm nang bạn đang tìm kiếm không tồn tại hoặc đã được chuyển sang địa chỉ mới.";
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
    </head>
    <body style="background: var(--bg-soft);">
        <?php include __DIR__ . '/templates/header.php'; ?>
        <div class="container py-5 text-center my-5">
            <div class="p-5 bg-white rounded-4 shadow-sm mx-auto" style="max-width: 550px;">
                <div class="display-1 text-danger mb-3">🌸 404</div>
                <h2 class="font-bold mb-3">Không Tìm Thấy Bài Viết</h2>
                <p class="text-muted mb-4">Bài viết này không tồn tại hoặc đã được gỡ bỏ. Mời bạn ghé thăm chuyên mục cẩm nang làm đẹp nhé!</p>
                <a href="<?= $baseUrl ?>/cam-nang" class="btn-hero-cta text-decoration-none">
                    <i class="fa-solid fa-book-open-reader me-1"></i>Xem Tất Cả Cẩm Nang
                </a>
            </div>
        </div>
        <?php include __DIR__ . '/templates/footer.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="<?= $baseUrl ?>/assets/js/app.js?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>"></script>
    </body>
    </html>
    <?php
    exit;
}

// Cập nhật lượt xem
$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);

// Lấy danh sách sản phẩm được gợi ý trong bài viết
$relatedProductIds = json_decode($post['related_products'] ?: '[]', true);
$recommendedProducts = [];
if (!empty($relatedProductIds)) {
    $placeholders = str_repeat('?,', count($relatedProductIds) - 1) . '?';
    $rpStmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $rpStmt->execute($relatedProductIds);
    $recommendedProducts = $rpStmt->fetchAll();
}

// Lấy các bài viết cùng chuyên mục khác
$otherStmt = $pdo->prepare("SELECT * FROM posts WHERE id != ? ORDER BY id DESC LIMIT 3");
$otherStmt->execute([$post['id']]);
$otherPosts = $otherStmt->fetchAll();

// Thiết lập thông số SEO
$pageTitle = htmlspecialchars($post['title']) . " — Cẩm Nang ALICE";
$pageDesc = truncateSeoDescription(!empty($post['excerpt']) ? $post['excerpt'] : strip_tags($post['content']));
$canonicalUrl = $baseUrl . '/cam-nang/' . $post['slug'];
$ogImage = (strpos($post['image'], 'http') === 0) ? $post['image'] : ($baseUrl . '/' . ltrim($post['image'], '/'));

// Dữ liệu cấu trúc Schema Article & Breadcrumb
$articleSchema = generateArticleJsonLd($post, $shopConfig, $baseUrl);
$breadcrumbSchema = generateBreadcrumbJsonLd([
    'Trang chủ' => $baseUrl . '/',
    'Cẩm nang làm đẹp' => $baseUrl . '/cam-nang',
    $post['title'] => $canonicalUrl
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
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="author" content="<?= htmlspecialchars($post['author'] ?: $shopConfig['shop_name']) ?>">

    <!-- Open Graph (Facebook / Zalo chia sẻ bài viết) -->
    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $pageDesc ?>">
    <meta property="og:url" content="<?= $canonicalUrl ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($shopConfig['shop_name']) ?>">
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="article:published_time" content="<?= date('c', strtotime($post['created_at'])) ?>">
    <meta property="article:section" content="<?= htmlspecialchars($post['category']) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $pageTitle ?>">
    <meta name="twitter:description" content="<?= $pageDesc ?>">
    <meta name="twitter:image" content="<?= $ogImage ?>">

    <!-- Resource Hints & Favicon -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌸</text></svg>">

    <!-- CSS Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>/assets/css/style.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" rel="stylesheet">

    <!-- Schema.org JSON-LD (Google Article & Breadcrumbs) -->
    <script type="application/ld+json">
    <?= json_encode($articleSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <script type="application/ld+json">
    <?= json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>

    <style>
        .article-content {
            font-size: 1.05rem;
            line-height: 1.85;
            color: #333;
        }
        .article-content h2 {
            font-family: var(--font-heading);
            color: var(--primary-dark);
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.45rem;
            font-weight: 700;
            border-left: 4px solid var(--primary);
            padding-left: 0.75rem;
        }
        .article-content h3 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .article-content blockquote {
            background: #fff0f5;
            border-left: 4px solid var(--primary);
            padding: 1rem 1.25rem;
            border-radius: 0 12px 12px 0;
            margin: 1.5rem 0;
            font-style: italic;
        }
        .article-content p.lead {
            font-size: 1.15rem;
            color: #555;
            font-weight: 500;
        }
    </style>
</head>
<body id="top" style="background: #fffbfd;">

    <!-- Toast Notification Container -->
    <div id="alice-toast-container" class="alice-toast-container"></div>

    <?php include __DIR__ . '/templates/header.php'; ?>

    <!-- Breadcrumb SEO Navigation -->
    <div class="py-2 border-bottom bg-white">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small" style="font-size: 0.82rem;">
                    <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/cam-nang" class="text-decoration-none text-muted">Cẩm nang làm đẹp</a></li>
                    <li class="breadcrumb-item active text-truncate font-bold" aria-current="page" style="color: var(--primary-dark); max-width: 320px;"><?= htmlspecialchars($post['title']) ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Article Section -->
    <main class="container py-4">
        <div class="row g-4">
            <!-- Cột Bài Viết Chính -->
            <div class="col-lg-8">
                <article class="bg-white rounded-4 shadow-sm p-4 p-md-5 border" style="border-color: var(--primary-pastel) !important;">
                    <!-- Metadata Header -->
                    <div class="mb-3">
                        <span class="badge rounded-pill px-3 py-1 font-bold" style="background: var(--primary-pastel); color: var(--primary-dark); font-size: 0.78rem;">
                            <?= htmlspecialchars($post['category']) ?>
                        </span>
                        <span class="small text-muted ms-2"><i class="fa-regular fa-clock me-1"></i><?= $post['reading_time'] ?> phút đọc</span>
                        <span class="small text-muted ms-3"><i class="fa-regular fa-eye me-1"></i><?= number_format($post['views']) ?> lượt xem</span>
                    </div>

                    <!-- Semantic H1 Title -->
                    <h1 class="font-bold mb-3" style="font-family: var(--font-heading); font-size: 2rem; color: var(--text-main); line-height: 1.3;">
                        <?= htmlspecialchars($post['title']) ?>
                    </h1>

                    <!-- Author Info & Date -->
                    <div class="d-flex align-items-center gap-2 mb-4 pb-3 border-bottom text-muted small">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 36px; height: 36px; background: var(--gradient-pink); font-size: 0.85rem;">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <div>
                            <span class="font-bold text-dark"><?= htmlspecialchars($post['author'] ?: 'Chuyên Gia Da Liễu ALICE') ?></span>
                            <div style="font-size: 0.75rem;">Đăng ngày <?= date('d/m/Y', strtotime($post['created_at'])) ?> • Tham vấn y khoa</div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="mb-4 text-center rounded-4 overflow-hidden border">
                        <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="img-fluid w-100" style="max-height: 420px; object-fit: cover;">
                    </div>

                    <!-- Article Body Content -->
                    <div class="article-content">
                        <?= $post['content'] ?>
                    </div>

                    <!-- Khối Sản Phẩm Được Gợi Ý Trong Bài Viết (Vũ Khí Chốt Đơn) -->
                    <?php if (!empty($recommendedProducts)): ?>
                        <div class="mt-5 p-4 rounded-4 border" style="background: linear-gradient(135deg, #fff0f5 0%, #ffe4ec 100%); border-color: var(--primary-pastel) !important;">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <span class="badge bg-danger rounded-pill px-3 py-1 font-bold">🌸 SẢN PHẨM KHUYÊN DÙNG</span>
                                <h3 class="font-bold mb-0" style="font-size: 1.25rem; color: var(--primary-dark);">Sản Phẩm Được Nhắc Đến Trong Bài</h3>
                            </div>
                            <p class="small text-muted mb-3">Các dòng mỹ phẩm chính hãng được chuyên gia da liễu tin chọn cho routine chăm sóc da này:</p>
                            
                            <div class="row row-cols-1 row-cols-md-3 g-3">
                                <?php foreach ($recommendedProducts as $rp): ?>
                                    <div class="col">
                                        <div class="bg-white rounded-3 p-3 h-100 shadow-sm d-flex flex-column text-center">
                                            <a href="<?= $baseUrl ?>/san-pham/<?= htmlspecialchars($rp['slug']) ?>" class="d-block mb-2 text-decoration-none">
                                                <img src="<?= htmlspecialchars($rp['image']) ?>" alt="<?= htmlspecialchars($rp['name']) ?>" class="rounded-3" style="width: 100%; height: 130px; object-fit: contain;" loading="lazy">
                                            </a>
                                            <div class="small font-bold text-truncate-2 mb-2" style="font-size: 0.85rem;">
                                                <a href="<?= $baseUrl ?>/san-pham/<?= htmlspecialchars($rp['slug']) ?>" class="text-decoration-none text-dark">
                                                    <?= htmlspecialchars($rp['name']) ?>
                                                </a>
                                            </div>
                                            <div class="text-danger font-bold mt-auto mb-2"><?= formatVND($rp['price']) ?></div>
                                            <button type="button" class="btn btn-sm btn-add-cart-pink w-100" onclick="AliceApp.quickAddToCart(<?= $rp['id'] ?>, '<?= addslashes($rp['name']) ?>', '<?= $rp['image'] ?>', <?= $rp['price'] ?>); AliceApp.openCartDrawer();">
                                                <i class="fa-solid fa-cart-plus me-1"></i>Mua Nhanh
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Social Share Buttons -->
                    <div class="mt-4 pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="small text-muted font-bold">Chia sẻ cẩm nang này:</div>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($canonicalUrl) ?>" target="_blank" class="btn btn-sm rounded-pill px-3 text-white" style="background: #1877f2;"><i class="fa-brands fa-facebook-f me-1"></i>Facebook</a>
                            <a href="https://zalo.me/share?url=<?= urlencode($canonicalUrl) ?>" target="_blank" class="btn btn-sm rounded-pill px-3 text-white" style="background: #0068ff;"><i class="fa-solid fa-share-nodes me-1"></i>Zalo</a>
                            <button type="button" class="btn btn-sm rounded-pill px-3 btn-outline-secondary" onclick="navigator.clipboard.writeText('<?= $canonicalUrl ?>'); AliceApp.showToast('Đã sao chép liên kết bài viết!', 'success');"><i class="fa-solid fa-link me-1"></i>Copy Link</button>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Cột Phải: Sidebar & Bài Viết Khác -->
            <div class="col-lg-4">
                <!-- Box Tư vấn Soi Da -->
                <aside class="bg-white rounded-4 shadow-sm p-4 border mb-4 text-center" style="background: linear-gradient(135deg, #fffbfd 0%, #fff0f5 100%);">
                    <div class="brand-icon-box mx-auto mb-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                        <i class="fa-solid fa-spa"></i>
                    </div>
                    <h3 class="font-bold mb-2" style="font-size: 1.15rem; color: var(--primary-dark);">Tư Vấn Soi Da Miễn Phí</h3>
                    <p class="small text-muted mb-3">Chưa biết làn da bạn cần routine nào? Nhắn tin ngay với đội ngũ chuyên gia ALICE qua Zalo để được soi da và lên phác đồ miễn phí 1:1.</p>
                    <a href="<?= htmlspecialchars($shopConfig['zalo_url']) ?>" target="_blank" class="btn btn-hero-cta w-100 text-decoration-none">
                        <i class="fa-solid fa-comments me-1"></i>Nhắn Zalo Soi Da Ngay
                    </a>
                </aside>

                <!-- Box Bài Viết Mới Nhất -->
                <aside class="bg-white rounded-4 shadow-sm p-3 border mb-4">
                    <h3 class="font-bold pb-2 border-bottom mb-3" style="font-family: var(--font-heading); font-size: 1.15rem; color: var(--text-main);">
                        Bài Viết Cùng Chuyên Mục
                    </h3>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($otherPosts as $op): ?>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="<?= $baseUrl ?>/cam-nang/<?= htmlspecialchars($op['slug']) ?>" class="flex-shrink-0" style="width: 75px; height: 75px;">
                                    <img src="<?= htmlspecialchars($op['image']) ?>" alt="<?= htmlspecialchars($op['title']) ?>" class="w-100 h-100 rounded-3 border" style="object-fit: cover;" loading="lazy">
                                </a>
                                <div class="small">
                                    <a href="<?= $baseUrl ?>/cam-nang/<?= htmlspecialchars($op['slug']) ?>" class="text-decoration-none text-dark font-semibold d-block text-truncate-2" style="font-size: 0.85rem; line-height: 1.4;">
                                        <?= htmlspecialchars($op['title']) ?>
                                    </a>
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;"><i class="fa-regular fa-clock me-1"></i><?= $op['reading_time'] ?> phút đọc</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </aside>
            </div>
        </div>
    </main>

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
