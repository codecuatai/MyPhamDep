<?php
/**
 * MOBILE BOTTOM NAVIGATION BAR - ALICE COSMETIC (CHUẨN APP SHOPEE)
 */
?>
<?php
$isHomePage = (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'index.php' && empty($_GET['category']) && empty($_GET['search']));
$homeUrlPrefix = htmlspecialchars($baseUrl ?? '');
?>
<div class="mobile-bottom-nav">
    <div class="container">
        <div class="mobile-nav-grid">
            <a href="<?= $isHomePage ? '#top' : $homeUrlPrefix . '/' ?>" class="mobile-nav-item active text-decoration-none">
                <i class="fa-solid fa-house-chimney mobile-nav-icon"></i>
                <span>Trang Chủ</span>
            </a>

            <a href="<?= $isHomePage ? '#san-pham-section' : $homeUrlPrefix . '/#san-pham-section' ?>" class="mobile-nav-item text-decoration-none">
                <i class="fa-solid fa-layer-group mobile-nav-icon"></i>
                <span>Danh Mục</span>
            </a>

            <a href="<?= htmlspecialchars($shopConfig['zalo_url']) ?>" target="_blank" class="mobile-nav-item text-decoration-none" style="color: #0068ff;">
                <img src="<?= $homeUrlPrefix ?>/assets/images/zalo-icon.svg" alt="Zalo" style="width: 20px; height: 20px; border-radius: 5px; margin-bottom: 2px;">
                <span>Tư Vấn Da</span>
            </a>

            <a href="javascript:void(0)" onclick="AliceApp.openCartDrawer()" class="mobile-nav-item text-decoration-none position-relative">
                <i class="fa-solid fa-bag-shopping mobile-nav-icon"></i>
                <span class="header-badge-count cart-badge-count" style="display: none; top: -6px; right: 8px;">0</span>
                <span>Giỏ Hàng</span>
            </a>

            <a href="javascript:void(0)" onclick="AliceApp.openAccountOrAuth()" class="mobile-nav-item text-decoration-none">
                <i class="fa-solid fa-circle-user mobile-nav-icon" id="mobile-nav-user-icon"></i>
                <span id="mobile-nav-user-label">Tài Khoản</span>
            </a>
        </div>
    </div>
</div>
