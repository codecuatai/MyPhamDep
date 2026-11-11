<?php
/**
 * TRANG DANH SÁCH BÀI VIẾT CẨM NANG LÀM ĐẸP - ALICE COSMETIC
 * Chuẩn SEO On-page, tối ưu tỷ lệ click và điều hướng người dùng
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

$selectedCategory = cleanInput($_GET['category'] ?? 'all');
$search = cleanInput($_GET['search'] ?? '');

$sql = "SELECT * FROM posts WHERE 1=1";
$params = [];

if ($selectedCategory !== 'all' && !empty($selectedCategory)) {
    $sql .= " AND category = ?";
    $params[] = $selectedCategory;
}

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll();

// SEO Metadata
$pageTitle = "Cẩm Nang Làm Đẹp & Bí Quyết Skincare Chuẩn Y Khoa — ALICE Cosmetic";
$pageDesc = "Khám phá các bí quyết chăm sóc da mụn, phục hồi da, cách chọn kem chống nắng và xu hướng trang điểm hot nhất từ chuyên gia da liễu ALICE Cosmetic.";
$canonicalUrl = $baseUrl . '/cam-nang';
$ogImage = "https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80";

// Breadcrumb Schema
$breadcrumbSchema = generateBreadcrumbJsonLd([
    'Trang chủ' => $baseUrl . '/',
    'Cẩm nang làm đẹp' => $canonicalUrl
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
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $pageDesc ?>">
    <meta property="og:url" content="<?= $canonicalUrl ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($shopConfig['shop_name']) ?>">
    <meta property="og:image" content="<?= $ogImage ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $pageTitle ?>">
    <meta name="twitter:description" content="<?= $pageDesc ?>">
    <meta name="twitter:image" content="<?= $ogImage ?>">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌸</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>/assets/css/style.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" rel="stylesheet">

    <script type="application/ld+json">
    <?= json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
</head>
<body id="top" style="background: #fffbfd;">

    <div id="alice-toast-container" class="alice-toast-container"></div>

    <?php include __DIR__ . '/templates/header.php'; ?>

    <!-- Breadcrumb SEO Navigation -->
    <div class="py-2 border-bottom bg-white">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small" style="font-size: 0.82rem;">
                    <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/" class="text-decoration-none text-muted"><i class="fa-solid fa-house me-1"></i>Trang chủ</a></li>
                    <li class="breadcrumb-item active text-truncate font-bold" aria-current="page" style="color: var(--primary-dark);">Cẩm nang làm đẹp</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Blog Banner Hero -->
    <section class="py-5 text-center" style="background: linear-gradient(135deg, #fff0f5 0%, #ffe4ec 100%); border-bottom: 1px solid var(--primary-pastel);">
        <div class="container">
            <span class="badge rounded-pill px-3 py-2 font-bold mb-2" style="background: var(--primary-pastel); color: var(--primary-dark);">
                🌸 CẨM NANG LÀM ĐẸP CHUẨN Y KHOA
            </span>
            <h1 class="font-bold mb-3" style="font-family: var(--font-heading); font-size: 2.2rem; color: var(--text-main);">
                Bí Quyết Chăm Sóc Da & Skincare Routine
            </h1>
            <p class="text-muted mx-auto mb-4" style="max-width: 650px; font-size: 1rem;">
                Cập nhật kiến thức da liễu, review mỹ phẩm chân thực và các hướng dẫn routine phục hồi màng ẩm cho người mới bắt đầu từ đội ngũ ALICE Cosmetic.
            </p>

            <!-- Category Filter Tabs -->
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="<?= $baseUrl ?>/cam-nang" class="btn btn-sm rounded-pill px-3 py-2 <?= $selectedCategory === 'all' ? 'btn-danger' : 'btn-outline-secondary bg-white' ?> font-semibold">Tất Cả Bài Viết</a>
                <a href="<?= $baseUrl ?>/cam-nang?category=Skincare" class="btn btn-sm rounded-pill px-3 py-2 <?= $selectedCategory === 'Skincare' ? 'btn-danger' : 'btn-outline-secondary bg-white' ?> font-semibold">🌿 Skincare Routine</a>
                <a href="<?= $baseUrl ?>/cam-nang?category=Chống+nắng" class="btn btn-sm rounded-pill px-3 py-2 <?= $selectedCategory === 'Chống nắng' ? 'btn-danger' : 'btn-outline-secondary bg-white' ?> font-semibold">☀️ Chống Nắng</a>
                <a href="<?= $baseUrl ?>/cam-nang?category=Trang+điểm" class="btn btn-sm rounded-pill px-3 py-2 <?= $selectedCategory === 'Trang điểm' ? 'btn-danger' : 'btn-outline-secondary bg-white' ?> font-semibold">💄 Trang Điểm</a>
            </div>
        </div>
    </section>

    <!-- Blog Posts Grid -->
    <main class="container py-5">
        <?php if (!empty($posts)): ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php foreach ($posts as $p): ?>
                    <div class="col">
                        <div class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden bg-white d-flex flex-column" style="border: 1px solid var(--primary-pastel) !important;">
                            <a href="<?= $baseUrl ?>/cam-nang/<?= htmlspecialchars($p['slug']) ?>" class="d-block overflow-hidden" style="height: 200px;">
                                <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s;" loading="lazy" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            </a>
                            <div class="card-body p-3 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge rounded-pill px-2 py-1 font-bold" style="background: var(--primary-soft); color: var(--primary-dark); font-size: 0.72rem;">
                                        <?= htmlspecialchars($p['category']) ?>
                                    </span>
                                    <span class="small text-muted" style="font-size: 0.75rem;"><i class="fa-regular fa-clock me-1"></i><?= $p['reading_time'] ?> phút đọc</span>
                                </div>
                                <h2 class="card-title font-bold mb-2" style="font-size: 1.05rem; line-height: 1.4;">
                                    <a href="<?= $baseUrl ?>/cam-nang/<?= htmlspecialchars($p['slug']) ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($p['title']) ?>
                                    </a>
                                </h2>
                                <p class="card-text small text-muted text-truncate-3 mb-3" style="line-height: 1.6;">
                                    <?= htmlspecialchars($p['excerpt'] ?: strip_tags($p['content'])) ?>
                                </p>
                                <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="small text-muted" style="font-size: 0.75rem;"><i class="fa-solid fa-user-pen me-1"></i><?= htmlspecialchars($p['author'] ?: 'ALICE Team') ?></span>
                                    <a href="<?= $baseUrl ?>/cam-nang/<?= htmlspecialchars($p['slug']) ?>" class="small font-bold" style="color: var(--primary-dark);">
                                        Đọc tiếp <i class="fa-solid fa-angle-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <p class="text-muted fs-5">Chưa có bài viết nào trong danh mục này.</p>
                <a href="<?= $baseUrl ?>/cam-nang" class="btn btn-outline-danger rounded-pill px-4">Xem tất cả bài viết</a>
            </div>
        <?php endif; ?>
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
