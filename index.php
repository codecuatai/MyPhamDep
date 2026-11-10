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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($shopConfig['shop_name']) ?> — Thế Giới Mỹ Phẩm & Routine Skincare Chính Hãng</title>
    <meta name="description" content="<?= htmlspecialchars($shopConfig['slogan']) ?> - Freeship toàn quốc, tặng hộp quà nơ hồng & thiệp viết tay, thanh toán VietQR tự động 24/7.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌸</text></svg>">

    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Custom Tone Hồng Pastel Stylesheet -->
    <link href="assets/css/style.css?v=<?= time() ?>" rel="stylesheet">

    <!-- Google Identity Services (Sign in with Google) -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        window.GOOGLE_CLIENT_ID = "<?= htmlspecialchars(defined('GOOGLE_CLIENT_ID') ? GOOGLE_CLIENT_ID : '') ?>";
    </script>
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
