<?php
/**
 * HEADER COMPONENT - ALICE COSMETIC (TONE HỒNG PASTEL)
 */
?>
<!-- 1. TOP PROMO BAR (Mộc Miên Style) -->
<div class="top-promo-bar">
    <div class="container d-flex flex-nowrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center flex-nowrap gap-2 text-truncate">
            <span class="text-truncate"><i class="fa-solid fa-gift me-1"></i>Tặng hộp nơ hồng pastel & thiệp viết tay mọi đơn hàng</span>
            <span class="top-badge-item d-none d-md-inline-flex"><i class="fa-solid fa-truck-fast"></i> Freeship từ <?= formatVND($shopConfig['freeship_min']) ?></span>
            <span class="top-badge-item d-none d-lg-inline-flex"><i class="fa-solid fa-shield-heart"></i> 100% Chính Hãng Cam Kết</span>
        </div>
        <div class="d-flex align-items-center flex-nowrap gap-3 small flex-shrink-0">
            <span class="text-nowrap"><i class="fa-solid fa-phone-volume me-1"></i>Hotline: <strong><?= htmlspecialchars($shopConfig['hotline']) ?></strong></span>
            <span class="d-none d-md-inline text-nowrap"><i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($shopConfig['address']) ?></span>
        </div>
    </div>
</div>

<!-- 2. MAIN HEADER (Lai ghép Shopee Search Bar & Mộc Miên Branding) -->
<?php
$isHomePage = (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'index.php' && empty($_GET['category']) && empty($_GET['search']));
$headerBaseUrl = htmlspecialchars($baseUrl ?? '');
?>
<header class="site-header py-2 shadow-sm">
    <div class="container">
        <div class="row align-items-center g-2">
            <!-- Cột 1: Logo ALICE Cosmetic -->
            <div class="col-lg-3 col-md-4 col-auto">
                <a href="<?= $isHomePage ? '#top' : $headerBaseUrl . '/' ?>" class="brand-logo-wrap text-decoration-none">
                    <div class="brand-icon-box">
                        <i class="fa-solid fa-spa"></i>
                    </div>
                    <div>
                        <div class="brand-name">ALICE Cosmetic</div>
                        <div class="brand-sub">Mỹ Phẩm & Skincare Chuẩn Hàn</div>
                    </div>
                </a>
            </div>

            <!-- Cột 2: Thanh tìm kiếm Shopee-style Tone Hồng (Dài rộng & Thanh thoát) -->
            <div class="col-lg-6 col-md-5 col">
                <div class="search-container">
                    <div class="search-input-group">
                        <i class="fa-solid fa-magnifying-glass text-muted me-2" style="font-size: 0.8rem;"></i>
                        <input type="text" id="header-search-input" placeholder="Tìm serum B5, kem chống nắng, son Rom&nd...">
                        <button type="button" class="btn-search-pink" onclick="AliceApp.handleHeaderSearch()">
                            <span>Tìm kiếm</span>
                            <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                        </button>
                    </div>
                    <div class="search-quick-tags d-none d-md-flex">
                        <span>Gợi ý hot:</span>
                        <a href="<?= $headerBaseUrl ?>/danh-muc/serum-tinh-chat" onclick="if(document.getElementById('san-pham-section')){event.preventDefault(); AliceApp.goToCategory('serum-tinh-chat');}">Serum B5</a>
                        <a href="<?= $headerBaseUrl ?>/danh-muc/chong-nang" onclick="if(document.getElementById('san-pham-section')){event.preventDefault(); AliceApp.goToCategory('chong-nang');}">Kem chống nắng</a>
                        <a href="<?= $headerBaseUrl ?>/danh-muc/makeup-son" onclick="if(document.getElementById('san-pham-section')){event.preventDefault(); AliceApp.goToCategory('makeup-son');}">Son môi</a>
                        <a href="<?= $headerBaseUrl ?>/danh-muc/combo-routine" onclick="if(document.getElementById('san-pham-section')){event.preventDefault(); AliceApp.goToCategory('combo-routine');}">Set quà</a>
                    </div>
                </div>
            </div>

            <!-- Cột 3: Nút Hành động & Giỏ hàng -->
            <div class="col-lg-3 col-md-3 col-auto">
                <div class="header-actions justify-content-end align-items-center">
                    <!-- Nút Tra cứu đơn hàng -->
                    <button class="btn-header-icon" title="Tra cứu đơn hàng" data-bs-toggle="modal" data-bs-target="#modalOrderLookup">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </button>

                    <!-- Khung Đăng Nhập / Tài Khoản Khách Hàng (Dạng nút tròn đồng bộ) -->
                    <div id="auth-header-container" class="d-flex align-items-center">
                        <!-- Nút Đăng nhập hình tròn đồng bộ (xóa chữ) -->
                        <button class="btn-header-icon" title="Đăng nhập / Tài khoản" data-bs-toggle="modal" data-bs-target="#modalAuth" id="btn-header-login">
                            <i class="fa-solid fa-circle-user"></i>
                        </button>

                        <!-- Menu User Dropdown khi đã đăng nhập (hình tròn chứa avatar) -->
                        <div class="dropdown" id="dropdown-user-logged" style="display: none;">
                            <button class="btn-header-icon p-0 border overflow-hidden" data-bs-toggle="dropdown" title="Tài khoản cá nhân" style="border-color: var(--primary) !important;">
                                <img id="header-user-avatar" src="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2" style="min-width: 220px; border: 1px solid var(--primary-pastel) !important;">
                                <li>
                                    <div class="px-3 py-2 border-bottom mb-1" style="background: var(--primary-soft); border-radius: 10px;">
                                        <div class="small font-bold" id="dropdown-user-fullname" style="color: var(--primary-dark);"></div>
                                        <div class="small text-muted text-truncate" id="dropdown-user-email" style="font-size: 0.72rem;"></div>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item small py-2 rounded-3" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalOrderLookup">
                                        <i class="fa-solid fa-receipt me-2 text-danger"></i>Đơn mua của tôi
                                    </a>
                                </li>
                                <li id="dropdown-admin-link" style="display: none;">
                                    <a class="dropdown-item small py-2 rounded-3 text-dark fw-bold" href="<?= $headerBaseUrl ?>/admin.php" style="background: #fff0f5; color: #ff4d8d !important;">
                                        <i class="fa-solid fa-gauge-high me-2 text-danger"></i>Quản Trị Hệ Thống
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item small py-2 rounded-3 text-danger font-bold" href="javascript:void(0)" onclick="AliceApp.logout()">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Nút Mở Giỏ Hàng Mini -->
                    <button class="btn-header-icon position-relative" title="Giỏ hàng ALICE" onclick="AliceApp.openCartDrawer()">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="header-badge-count cart-badge-count" style="display: none;">0</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</header>
