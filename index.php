<?php
/**
 * ALICE COSMETIC - HỆ THỐNG WEBSITE & APP BÁN MỸ PHẨM CHÍNH HÃNG
 * Giao diện Tone Hồng Pastel ngọt ngào & thanh lịch
 * Kết hợp thẩm mỹ Mộc Miên Books x Sức mạnh chuyển đổi Shopee
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

// SEO Meta variables
$canonicalUrl = $baseUrl . '/';
$pageTitle = htmlspecialchars($shopConfig['shop_name']) . ' — Thế Giới Mỹ Phẩm & Routine Skincare Chính Hãng';
$pageDesc = htmlspecialchars($shopConfig['slogan']) . ' - Cam kết 100% chính hãng, Freeship toàn quốc từ ' . formatVND($shopConfig['freeship_min']) . ', tặng hộp quà nơ hồng & thiệp viết tay yêu thương.';
$ogImage = 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=700&q=80';

// JSON-LD Schemas
$shopSchema = generateShopJsonLd($shopConfig, $baseUrl);
$websiteSchema = [
    "@context" => "https://schema.org",
    "@type" => "WebSite",
    "name" => $shopConfig['shop_name'] ?? 'ALICE Cosmetic',
    "url" => $baseUrl,
    "potentialAction" => [
        "@type" => "SearchAction",
        "target" => $baseUrl . "/?search={search_term_string}",
        "query-input" => "required name=search_term_string"
    ]
];

// Lấy danh mục
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY count DESC");
$categories = $catStmt->fetchAll();

// Lấy sản phẩm Flash Sale
$flashStmt = $pdo->query("SELECT * FROM products WHERE is_flash_sale = 1 ORDER BY flash_sale_sold DESC");
$flashSaleProducts = $flashStmt->fetchAll();

// Lấy toàn bộ sản phẩm
$prodStmt = $pdo->query("SELECT * FROM products ORDER BY sold_count DESC");
$allProducts = $prodStmt->fetchAll();
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
    <meta name="author" content="<?= htmlspecialchars($shopConfig['shop_name']) ?>">

    <!-- Open Graph / Facebook & Zalo SEO -->
    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="<?= $pageDesc ?>">
    <meta property="og:url" content="<?= $canonicalUrl ?>">
    <meta property="og:site_name" content="<?= htmlspecialchars($shopConfig['shop_name']) ?>">
    <meta property="og:image" content="<?= $ogImage ?>">
    <meta property="og:image:width" content="700">
    <meta property="og:image:height" content="467">
    <meta property="og:image:alt" content="<?= htmlspecialchars($shopConfig['shop_name']) ?> Mỹ Phẩm Chính Hãng">

    <!-- Twitter Card SEO -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $pageTitle ?>">
    <meta name="twitter:description" content="<?= $pageDesc ?>">
    <meta name="twitter:image" content="<?= $ogImage ?>">

    <!-- DNS Prefetch & Resource Hints (Tối ưu tốc độ tải trang Core Web Vitals) -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="https://images.unsplash.com">
    <link rel="dns-prefetch" href="https://accounts.google.com">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌸</text></svg>">

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Custom Tone Hồng Pastel Stylesheet (Cache tối ưu theo Version) -->
    <link href="assets/css/style.css?v=<?= defined('APP_VERSION') ? APP_VERSION : '1.0.0' ?>" rel="stylesheet">

    <!-- Dữ liệu có cấu trúc Schema.org JSON-LD (Hiển thị thông tin thương hiệu & tìm kiếm Google) -->
    <script type="application/ld+json">
    <?= json_encode($shopSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <script type="application/ld+json">
    <?= json_encode($websiteSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>

    <!-- Google Identity Services (Sign in with Google) -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        window.GOOGLE_CLIENT_ID = "<?= htmlspecialchars(defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '') ?>";
        window.FACEBOOK_APP_ID = "<?= htmlspecialchars(defined('FACEBOOK_APP_ID') ? FACEBOOK_APP_ID : '') ?>";
        window.fbAsyncInit = function() {
            if (window.FACEBOOK_APP_ID && window.FACEBOOK_APP_ID.trim() !== '') {
                FB.init({
                    appId: window.FACEBOOK_APP_ID.trim(),
                    cookie: true,
                    xfbml: false,
                    fedCM: false,
                    version: 'v19.0'
                });
            }
        };
    </script>
    <!-- Facebook SDK for JavaScript -->
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js"></script>
</head>
<body id="top">

    <!-- Toast Notification Container -->
    <div id="alice-toast-container" class="alice-toast-container"></div>

    <?php 
    // 1. Header & Top Promo Bar
    include __DIR__ . '/templates/header.php';

    // 2. Hero Banner & Trust Badges
    include __DIR__ . '/templates/hero.php';

    // 3. Bubble Categories (Mộc Miên Style)
    include __DIR__ . '/templates/bubble_categories.php';

    // 4. Shopee-Style Flash Sale Widget
    include __DIR__ . '/templates/flash_sale.php';

    // 5. Product Grid & Skincare Routine Filter
    include __DIR__ . '/templates/product_grid.php';

    // 6. Gift Set Routine Section (Mộc Miên Signature)
    include __DIR__ . '/templates/gift_combos.php';

    // 7. Customer Reviews & Feedback
    include __DIR__ . '/templates/feedback.php';

    // 8. Beauty Blog & Guides
    include __DIR__ . '/templates/blog.php';

    // 9. Footer Bo Cong Pastel
    include __DIR__ . '/templates/footer.php';

    // 10. Modals & Slide-over Drawer
    include __DIR__ . '/templates/modals.php';

    // 11. Mobile Bottom Navigation (Shopee App Style)
    include __DIR__ . '/templates/mobile_nav.php';

    // 12. Floating Quick Contact Buttons
    include __DIR__ . '/templates/floating_contact.php';
    ?>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- App Engine JavaScript -->
    <script src="assets/js/app.js?v=<?= time() ?>"></script>
</body>
</html>
