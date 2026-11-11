<?php
/**
 * TRANG QUẢN TRỊ HỆ THỐNG (ADMIN DASHBOARD) - ALICE COSMETIC
 * Quản lý đơn hàng, kho mỹ phẩm, voucher và cấu hình VietQR
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$shopConfig = getShopConfig($pdo);
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = ($currentUser && ($currentUser['role'] ?? '') === 'admin');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Hệ Thống — <?= htmlspecialchars($shopConfig['shop_name']) ?></title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⚙️</text></svg>">
    
    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --admin-pink: #ff4d8d;
            --admin-dark: #1e293b;
            --admin-bg: #f8fafc;
            --admin-border: #e2e8f0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--admin-bg);
            color: #334155;
            min-height: 100vh;
        }
        .navbar-admin {
            background: #ffffff;
            border-bottom: 1px solid var(--admin-border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .admin-nav-pill .nav-link {
            border-radius: 999px;
            color: #64748b;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            transition: all 0.2s ease;
        }
        .admin-nav-pill .nav-link:hover {
            color: var(--admin-pink);
            background: #fff0f5;
        }
        .admin-nav-pill .nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, #ff4d8d 0%, #ff758c 100%);
            box-shadow: 0 4px 12px rgba(255, 77, 141, 0.3);
        }
        .stat-card {
            background: #ffffff;
            border: 1px solid var(--admin-border);
            border-radius: 16px;
            padding: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }
        .table-custom {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--admin-border);
        }
        .badge-status {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            font-weight: 700;
        }
        .btn-pink {
            background: linear-gradient(135deg, #ff4d8d 0%, #ff758c 100%);
            color: white;
            border: none;
            font-weight: 600;
            border-radius: 10px;
        }
        .btn-pink:hover {
            background: linear-gradient(135deg, #e63976 0%, #ff5c77 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(255, 77, 141, 0.3);
        }
        .bg-pink {
            background: linear-gradient(135deg, #ff4d8d 0%, #ff758c 100%) !important;
            color: #ffffff !important;
        }
        .login-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
            border: 1px solid var(--admin-border);
            max-width: 440px;
            width: 100%;
        }
    </style>
</head>
<body>

<?php if (!$isAdmin): ?>
    <!-- ========================================== -->
    <!-- MÀN HÌNH ĐĂNG NHẬP DÀNH RIÊNG CHO ADMIN    -->
    <!-- ========================================== -->
    <div class="d-flex align-items-center justify-content-center min-vh-100 p-3">
        <div class="login-card p-4 p-md-5 text-center">
            <div class="mb-4">
                <div class="d-inline-flex p-3 rounded-circle mb-3" style="background: #fff0f5;">
                    <i class="fa-solid fa-user-shield fs-1" style="color: var(--admin-pink);"></i>
                </div>
                <h3 class="fw-bold mb-1">Cổng Quản Trị Hệ Thống</h3>
                <p class="text-muted small mb-0"><?= htmlspecialchars($shopConfig['shop_name']) ?></p>
            </div>

            <form id="formAdminLogin" onsubmit="AdminApp.handleLogin(event)">
                <div class="mb-3 text-start">
                    <label class="form-label small fw-semibold text-secondary">Email Quản Trị</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                        <input type="email" id="adminEmail" class="form-control border-start-0" required placeholder="admin@alicecosmetic.vn">
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label class="form-label small fw-semibold text-secondary">Mật Khẩu Quản Trị</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" id="adminPassword" class="form-control border-start-0" required placeholder="Nhập mật khẩu...">
                    </div>
                </div>

                <button type="submit" id="btnAdminLogin" class="btn btn-pink w-100 py-2 mb-3">
                    <i class="fa-solid fa-right-to-bracket me-2"></i>Đăng Nhập Quản Trị
                </button>

                <a href="index.php" class="text-decoration-none small text-muted">
                    <i class="fa-solid fa-arrow-left me-1"></i>Quay lại Cửa Hàng
                </a>
            </form>
        </div>
    </div>

<?php else: ?>
    <!-- ========================================== -->
    <!-- BẢNG ĐIỀU KHIỂN CHÍNH THỨC (ADMIN DASHBOARD) -->
    <!-- ========================================== -->
    <nav class="navbar navbar-admin sticky-top py-3">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center gap-3">
                <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-dark m-0" href="admin.php">
                    <span class="d-inline-flex p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #ff4d8d 0%, #ff758c 100%);">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </span>
                    <span>ALICE <span style="color: var(--admin-pink);">Admin</span></span>
                </a>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 small">
                    <i class="fa-solid fa-shield-halved me-1"></i>Quản Trị Viên
                </span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="index.php" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Xem Cửa Hàng
                </a>
                <div class="d-flex align-items-center gap-2 border-start ps-3">
                    <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=150&q=80') ?>" class="rounded-circle border" width="36" height="36" alt="Avatar">
                    <div class="d-none d-md-block text-start">
                        <div class="small fw-bold text-dark"><?= htmlspecialchars($currentUser['fullname'] ?? 'Admin') ?></div>
                        <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars($currentUser['email'] ?? '') ?></div>
                    </div>
                </div>
                <button class="btn btn-light btn-sm rounded-circle text-danger border" title="Đăng xuất" onclick="AdminApp.logout()">
                    <i class="fa-solid fa-power-off"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        <!-- Tabs chuyển trang Quản trị -->
        <ul class="nav admin-nav-pill gap-2 mb-4 bg-white p-2 rounded-pill border" id="adminTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-dashboard" type="button">
                    <i class="fa-solid fa-chart-pie me-1"></i>Tổng Quan
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-orders" type="button" onclick="AdminApp.loadOrders()">
                    <i class="fa-solid fa-box-open me-1"></i>Đơn Hàng (<span id="count-orders-badge">0</span>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-products" type="button" onclick="AdminApp.loadProducts()">
                    <i class="fa-solid fa-spray-can-sparkles me-1"></i>Kho Mỹ Phẩm (<span id="count-products-badge">0</span>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-banners" type="button" onclick="AdminApp.loadBanners()">
                    <i class="fa-solid fa-images me-1"></i>Banner Quảng Cáo (<span id="count-banners-badge">0</span>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-categories" type="button" onclick="AdminApp.loadCategories()">
                    <i class="fa-solid fa-shapes me-1"></i>Danh Mục (<span id="count-categories-badge">6</span>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-coupons" type="button" onclick="AdminApp.loadCoupons()">
                    <i class="fa-solid fa-ticket me-1"></i>Mã Giảm Giá
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-posts" type="button" onclick="AdminApp.loadPosts()">
                    <i class="fa-solid fa-newspaper me-1"></i>Cẩm Nang SEO (<span id="count-posts-badge">3</span>)
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-settings" type="button" onclick="AdminApp.loadSettings()">
                    <i class="fa-solid fa-qrcode me-1"></i>Cài Đặt VietQR & Shop
                </button>
            </li>
        </ul>

        <!-- NỘI DUNG CÁC TABS -->
        <div class="tab-content" id="adminTabContent">

            <!-- TAB 1: TỔNG QUAN -->
            <div class="tab-pane fade show active" id="tab-dashboard" role="tabpanel">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-xl">
                        <div class="stat-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">DOANH THU ĐÃ THU</span>
                                <div class="stat-icon" style="background: #dcfce7; color: #16a34a;">
                                    <i class="fa-solid fa-coins"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1" id="stat-revenue" style="color: #16a34a;">0₫</h3>
                            <div class="text-muted small">Đơn VietQR & Đã giao</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl">
                        <div class="stat-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">TỔNG ĐƠN HÀNG</span>
                                <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1 text-dark" id="stat-orders">0</h3>
                            <div class="text-muted small"><span id="stat-pending-orders" class="text-danger fw-bold">0</span> đơn chờ duyệt</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl">
                        <div class="stat-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">SẢN PHẨM MỸ PHẨM</span>
                                <div class="stat-icon" style="background: #fdf2f8; color: #db2777;">
                                    <i class="fa-solid fa-wand-magic"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1 text-dark" id="stat-products">0</h3>
                            <div class="text-muted small">Kho mỹ phẩm ALICE</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl">
                        <div class="stat-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">CẢNH BÁO TỒN KHO</span>
                                <div class="stat-icon" style="background: #ffedd5; color: #ea580c;">
                                    <i class="fa-solid fa-boxes-stacked"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1" id="stat-low-stock" style="color: #ea580c;">0</h3>
                            <div class="text-muted small"><span id="stat-out-of-stock" class="text-danger fw-bold">0</span> sản phẩm hết sạch kho</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl">
                        <div class="stat-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">BANNER SLIDER</span>
                                <div class="stat-icon" style="background: #ede9fe; color: #7c3aed;">
                                    <i class="fa-solid fa-images"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1 text-dark" id="stat-banners">0</h3>
                            <div class="text-muted small">Banner đang hoạt động</div>
                        </div>
                    </div>
                </div>

                <!-- CẢNH BÁO TỒN KHO: SẮP HẾT & ĐÃ HẾT HÀNG -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 border-start border-4 border-warning">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h5 class="fw-bold m-0 text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Cảnh Báo Tồn Kho: Sản Phẩm Sắp Hết & Hết Hàng (&lt; 10 sp)</h5>
                            <p class="text-muted small mb-0">Cần nhập thêm tồn kho kịp thời để khách hàng không bị gián đoạn đơn mua</p>
                        </div>
                        <button class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="AdminApp.switchToTab('tab-products')">Vào Kho Mỹ Phẩm</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>ẢNH</th>
                                    <th>TÊN SẢN PHẨM</th>
                                    <th>THƯƠNG HIỆU & XUẤT XỨ</th>
                                    <th>GIÁ BÁN</th>
                                    <th>SỐ LƯỢNG TỒN</th>
                                    <th>TÌNH TRẠNG</th>
                                    <th class="text-end">NHẬP THÊM HÀNG NHANH</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-low-stock">
                                <tr><td colspan="7" class="text-center py-4 text-muted">Đang kiểm tra hàng tồn kho...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold m-0"><i class="fa-solid fa-clock-rotate-left me-2 text-danger"></i>Đơn Hàng Gần Đây</h5>
                        <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="AdminApp.switchToTab('tab-orders')">Xem Tất Cả Đơn</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>MÃ ĐƠN</th>
                                    <th>KHÁCH HÀNG</th>
                                    <th>SỐ ĐIỆN THOẠI</th>
                                    <th>TỔNG TIỀN</th>
                                    <th>PHƯƠNG THỨC</th>
                                    <th>TRẠNG THÁI</th>
                                    <th>THỜI GIAN</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-recent-orders">
                                <tr><td colspan="7" class="text-center py-4 text-muted">Đang tải đơn hàng mới...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 2: QUẢN LÝ ĐƠN HÀNG -->
            <div class="tab-pane fade" id="tab-orders" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Danh Sách Toàn Bộ Đơn Hàng</h5>
                            <p class="text-muted small mb-0">Theo dõi, kiểm tra thanh toán VietQR và cập nhật trạng thái đơn</p>
                        </div>
                        <div class="d-flex gap-2">
                            <select id="filterOrderStatus" class="form-select form-select-sm rounded-pill" onchange="AdminApp.filterOrders()">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="pending">Chờ thanh toán (Pending)</option>
                                <option value="paid">Đã thanh toán (Paid)</option>
                                <option value="shipping">Đang giao hàng</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="AdminApp.loadOrders()">
                                <i class="fa-solid fa-rotate-right me-1"></i>Làm mới
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>MÃ ĐƠN</th>
                                    <th>KHÁCH HÀNG</th>
                                    <th>SĐT / ĐỊA CHỈ</th>
                                    <th>SẢN PHẨM MUA</th>
                                    <th>TỔNG THANH TOÁN</th>
                                    <th>HÌNH THỨC</th>
                                    <th>TRẠNG THÁI</th>
                                    <th class="text-end">THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-orders">
                                <tr><td colspan="8" class="text-center py-4 text-muted">Đang tải danh sách đơn hàng...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 3: QUẢN LÝ SẢN PHẨM -->
            <div class="tab-pane fade" id="tab-products" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Kho Mỹ Phẩm ALICE Cosmetic</h5>
                            <p class="text-muted small mb-0">Quản lý số lượng tồn kho, thông số xuất xứ, dung tích, giá bán & Flash Sale</p>
                        </div>
                        <div class="d-flex gap-2">
                            <select id="filterProductStock" class="form-select form-select-sm rounded-pill" onchange="AdminApp.filterProducts()">
                                <option value="all">Tất cả tồn kho</option>
                                <option value="in_stock">Còn hàng (> 10)</option>
                                <option value="low_stock">Sắp hết hàng (<= 10)</option>
                                <option value="out_of_stock">Hết sạch hàng (0)</option>
                            </select>
                            <button class="btn btn-pink rounded-pill px-4" onclick="AdminApp.openModalProduct()">
                                <i class="fa-solid fa-plus me-2"></i>Thêm Mỹ Phẩm Mới
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>ẢNH</th>
                                    <th>TÊN SẢN PHẨM</th>
                                    <th>THƯƠNG HIỆU & XUẤT XỨ</th>
                                    <th>DANH MỤC & DUNG TÍCH</th>
                                    <th>GIÁ BÁN</th>
                                    <th>GIÁ GỐC</th>
                                    <th>HÀNG TỒN KHO</th>
                                    <th>FLASH SALE</th>
                                    <th class="text-end">THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-products">
                                <tr><td colspan="9" class="text-center py-4 text-muted">Đang tải sản phẩm...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB BANNER: QUẢN LÝ BANNER QUẢNG CÁO -->
            <div class="tab-pane fade" id="tab-banners" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fa-solid fa-images me-2 text-danger"></i>Banner Quảng Cáo & Slider Trang Chủ</h5>
                            <p class="text-muted small mb-0">Quản lý các banner carousel nổi bật hiển thị ở đầu trang web</p>
                        </div>
                        <button class="btn btn-pink rounded-pill px-4" onclick="AdminApp.openModalBanner()">
                            <i class="fa-solid fa-plus me-2"></i>Thêm Banner Mới
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th style="width: 140px;">HÌNH ẢNH</th>
                                    <th>TIÊU ĐỀ & PHỤ ĐỀ</th>
                                    <th>BADGE</th>
                                    <th>ĐƯỜNG DẪN BẤM</th>
                                    <th>NÚT CTA</th>
                                    <th>THỨ TỰ</th>
                                    <th>HIỂN THỊ</th>
                                    <th class="text-end">THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-banners">
                                <tr><td colspan="8" class="text-center py-4 text-muted">Đang tải danh sách banner...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB: QUẢN LÝ DANH MỤC TRÒN (CATEGORIES) -->
            <div class="tab-pane fade" id="tab-categories" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fa-solid fa-shapes me-2 text-danger"></i>6 Danh Mục Làm Đẹp Tròn Trang Chủ</h5>
                            <p class="text-muted small mb-0">Quản lý hình ảnh và thông tin của 6 bong bóng danh mục tròn (Khám Phá Theo Nhu Cầu Da)</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th style="width: 90px;">ẢNH TRÒN</th>
                                    <th>TÊN DANH MỤC</th>
                                    <th>MÃ ĐỊNH DANH (SLUG)</th>
                                    <th>SẢN PHẨM HIỆN CÓ</th>
                                    <th>MÔ TẢ CHI TIẾT</th>
                                    <th class="text-end" style="width: 100px;">THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-categories">
                                <tr><td colspan="6" class="text-center py-4 text-muted">Đang tải danh sách danh mục...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 4: MÃ GIẢM GIÁ (COUPONS) -->
            <div class="tab-pane fade" id="tab-coupons" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Quản Lý Mã Khuyến Mãi (Vouchers)</h5>
                            <p class="text-muted small mb-0">Tạo mã giảm giá trực tiếp cho khách mua hàng</p>
                        </div>
                        <button class="btn btn-pink rounded-pill px-4" onclick="AdminApp.openModalCoupon()">
                            <i class="fa-solid fa-plus me-2"></i>Tạo Mã Giảm Giá Mới
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>MÃ VOUCHER</th>
                                    <th>LOẠI GIẢM</th>
                                    <th>MỨC GIẢM</th>
                                    <th>ĐƠN TỐI THIỂU</th>
                                    <th>GIẢM TỐI ĐA</th>
                                    <th>MÔ TẢ</th>
                                    <th class="text-end">THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-coupons">
                                <tr><td colspan="7" class="text-center py-4 text-muted">Đang tải mã giảm giá...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 5: CÀI ĐẶT VIETQR & SHOP -->
            <div class="tab-pane fade" id="tab-settings" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12 col-lg-7">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <h5 class="fw-bold mb-3"><i class="fa-solid fa-sliders me-2 text-primary"></i>Thông Tin Cửa Hàng & Ngân Hàng VietQR</h5>
                            <form id="formAdminSettings" onsubmit="AdminApp.saveSettings(event)">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Tên Cửa Hàng</label>
                                        <input type="text" id="cfg_shop_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Hotline Tư Vấn</label>
                                        <input type="text" id="cfg_hotline" class="form-control" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Slogan Thương Hiệu</label>
                                        <input type="text" id="cfg_slogan" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Số Zalo CSKH</label>
                                        <input type="text" id="cfg_zalo" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Mức Đơn Được Freeship (VNĐ)</label>
                                        <input type="number" id="cfg_freeship_min" class="form-control" placeholder="299000">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Địa Chỉ Cửa Hàng</label>
                                        <input type="text" id="cfg_address" class="form-control">
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h6 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-building-columns me-2"></i>Tài Khoản Nhận Tiền VietQR</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Chọn Ngân Hàng Thụ Hưởng</label>
                                        <select id="cfg_bank_id" class="form-select" onchange="AdminApp.updatePreviewQR()"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Số Tài Khoản</label>
                                        <input type="text" id="cfg_account_no" class="form-control fw-bold" required oninput="AdminApp.updatePreviewQR()">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Tên Chủ Tài Khoản (In hoa không dấu)</label>
                                        <input type="text" id="cfg_account_name" class="form-control fw-bold text-uppercase" required oninput="AdminApp.updatePreviewQR()">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Webhook Google Sheets (Đồng bộ đơn tự động)</label>
                                        <input type="url" id="cfg_google_sheet_webhook" class="form-control" placeholder="https://script.google.com/macros/s/.../exec">
                                    </div>
                                </div>

                                <button type="submit" id="btnSaveSettings" class="btn btn-pink px-4 py-2">
                                    <i class="fa-solid fa-floppy-disk me-2"></i>Lưu Tất Cả Cài Đặt
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                            <h6 class="fw-bold mb-1">Mã VietQR Nhận Tiền Trực Quan</h6>
                            <p class="text-muted small mb-3">Mã này sẽ hiển thị cho khách quét khi thanh toán đơn hàng</p>
                            <div class="p-3 bg-light rounded-4 d-inline-block border mx-auto mb-3">
                                <img id="preview-vietqr-img" src="" alt="Mã VietQR" style="max-width: 250px; width: 100%; border-radius: 12px;">
                            </div>
                            <div class="small fw-semibold text-dark" id="preview-qr-info">...</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: QUẢN LÝ CẨM NANG LÀM ĐẸP CHUẨN SEO -->
            <div class="tab-pane fade" id="tab-posts" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="fa-solid fa-newspaper me-2 text-danger"></i>Danh Sách Bài Viết Cẩm Nang Chuẩn SEO</h5>
                            <p class="text-muted small mb-0">Viết bài chia sẻ bí quyết làm đẹp để kéo hàng nghìn lượt tìm kiếm tự nhiên từ Google</p>
                        </div>
                        <button type="button" class="btn btn-pink rounded-pill px-3" onclick="AdminApp.openPostModal()">
                            <i class="fa-solid fa-plus me-1"></i>Viết Bài Mới
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">Ảnh</th>
                                    <th>Tiêu Đề & Đường Dẫn SEO</th>
                                    <th>Chuyên Mục</th>
                                    <th>Thời Gian Đọc</th>
                                    <th>Lượt Xem</th>
                                    <th>Ngày Đăng</th>
                                    <th class="text-end">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="posts-table-body">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Đang tải danh sách bài viết...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL THÊM / SỬA BÀI VIẾT CẨM NANG -->
    <div class="modal fade" id="modalAdminPost" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalPostTitle">Viết Bài Cẩm Nang Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPostDetail" onsubmit="AdminApp.savePost(event)">
                        <input type="hidden" id="post_id">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Tiêu Đề Bài Viết *</label>
                                <input type="text" id="post_title" class="form-control" required placeholder="Ví dụ: Routine 4 Bước Giảm Mụn Phục Hồi Da">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Chuyên Mục</label>
                                <select id="post_category" class="form-select">
                                    <option value="Skincare">Skincare Routine</option>
                                    <option value="Chống nắng">Chống Nắng</option>
                                    <option value="Trang điểm">Trang Điểm</option>
                                    <option value="Chăm sóc da">Chăm Sóc Da</option>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Đường Dẫn SEO (Slug - Để trống hệ thống tự tạo)</label>
                                <input type="text" id="post_slug" class="form-control" placeholder="routine-4-buoc-giam-mun">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Thời Gian Đọc (Phút)</label>
                                <input type="number" id="post_reading_time" class="form-control" value="5" min="1">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Tác Giả</label>
                                <input type="text" id="post_author" class="form-control" value="Chuyên Gia Da Liễu ALICE">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Ảnh Bìa Bài Viết (URL) *</label>
                                <input type="url" id="post_image" class="form-control" required placeholder="https://images.unsplash.com/...">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Tóm Tắt Ngắn (Meta Description - 1-2 câu hiển thị trên Google)</label>
                                <textarea id="post_excerpt" class="form-control" rows="2" placeholder="Tóm tắt ngắn gọn nội dung bài viết để thu hút người đọc bấm vào..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Nội Dung Bài Viết Chi Tiết (Hỗ trợ định dạng HTML: &lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;strong&gt;) *</label>
                                <textarea id="post_content" class="form-control font-monospace" rows="10" required placeholder="Nội dung bài viết..."></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">ID Sản Phẩm Gợi Ý Trong Bài (Cách nhau bởi dấu phẩy, ví dụ: 1, 2, 4)</label>
                                <input type="text" id="post_related_products" class="form-control" placeholder="1, 4, 11">
                                <div class="form-text small">Hệ thống sẽ tự động hiển thị nút mua nhanh các sản phẩm này ở cuối bài viết để khách bấm mua ngay.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" id="btnSavePost" class="btn btn-pink rounded-pill px-4">
                                <i class="fa-solid fa-cloud-arrow-up me-1"></i>Lưu & Xuất Bản
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL THÊM / SỬA SẢN PHẨM -->
    <div class="modal fade" id="modalAdminProduct" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalProductTitle">Thêm Sản Phẩm Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formProductDetail" onsubmit="AdminApp.saveProduct(event)">
                        <input type="hidden" id="prod_id">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Tên Sản Phẩm *</label>
                                <input type="text" id="prod_name" class="form-control" required placeholder="Ví dụ: Son Kem Lì Rom&nd Zero Velvet Tint">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Thương Hiệu</label>
                                <input type="text" id="prod_brand" class="form-control" placeholder="Rom&nd, La Roche-Posay...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Danh Mục</label>
                                <select id="prod_category_id" class="form-select">
                                    <option value="tay-trang">Làm Sạch & Tẩy Trang</option>
                                    <option value="serum-tinh-chat">Tinh Chất & Serum</option>
                                    <option value="chong-nang">Kem Chống Nắng</option>
                                    <option value="kem-duong">Dưỡng Ẩm & Phục Hồi</option>
                                    <option value="makeup-son">Son Môi & Trang Điểm</option>
                                    <option value="combo-routine">Set Quà & Routine</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Giá Bán (VNĐ) *</label>
                                <input type="number" id="prod_price" class="form-control" required placeholder="159000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Giá Gốc Niêm Yết (VNĐ)</label>
                                <input type="number" id="prod_original_price" class="form-control" placeholder="219000">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-danger"><i class="fa-solid fa-boxes-stacked me-1"></i>Hàng Tồn Kho *</label>
                                <input type="number" id="prod_stock_quantity" class="form-control fw-bold" required value="50" min="0" placeholder="50">
                                <div class="form-text small" style="font-size: 0.72rem;">Tự động trừ khi khách đặt mua</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Xuất Xứ (Nước SX)</label>
                                <input type="text" id="prod_origin" class="form-control" placeholder="Hàn Quốc, Pháp, Mỹ...">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Dung Tích / Quy Cách</label>
                                <input type="text" id="prod_capacity" class="form-control" placeholder="50ml, 30ml, 3.8g...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Hạn Sử Dụng</label>
                                <input type="text" id="prod_expiry" class="form-control" placeholder="36 tháng kể từ NSX (12 tháng mở nắp)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Loại Da Phù Hợp</label>
                                <select id="prod_skin_type" class="form-select">
                                    <option value="all">Mọi loại da (All skin types)</option>
                                    <option value="oil">Da Dầu / Hỗn hợp thiên dầu</option>
                                    <option value="dry">Da Khô / Thiếu ẩm</option>
                                    <option value="sensitive">Da Nhạy Cảm / Dễ kích ứng</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Link Ảnh Sản Phẩm (URL) *</label>
                                <input type="url" id="prod_image" class="form-control" required placeholder="https://images.unsplash.com/...">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Mô Tả Sản Phẩm & Công Dụng</label>
                                <textarea id="prod_description" class="form-control" rows="3" placeholder="Mô tả công dụng, tính năng nổi bật..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Bảng Thành Phần Chi Tiết (Ingredients)</label>
                                <textarea id="prod_ingredients" class="form-control" rows="2" placeholder="Ví dụ: Niacinamide 10%, Zinc PCA 1%, Chiết xuất Rau má..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Hướng Dẫn Sử Dụng & Bảo Quản (How to use)</label>
                                <textarea id="prod_how_to_use" class="form-control" rows="2" placeholder="Ví dụ: Thoa đều sáng và tối sau bước rửa mặt..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="prod_is_flash_sale">
                                    <label class="form-check-label fw-semibold" for="prod_is_flash_sale">Đưa vào Flash Sale Chớp Nhoáng</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Số Lượng Flash Sale (Stock)</label>
                                <input type="number" id="prod_flash_sale_stock" class="form-control" value="50">
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" id="btnSaveProduct" class="btn btn-pink rounded-pill px-4">Lưu Sản Phẩm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL THÊM / SỬA BANNER TRANG CHỦ -->
    <div class="modal fade" id="modalAdminBanner" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalBannerTitle">Thêm Banner Quảng Cáo Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formBannerDetail" onsubmit="AdminApp.saveBanner(event)">
                        <input type="hidden" id="banner_id">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-semibold">Tiêu Đề Banner *</label>
                                <input type="text" id="banner_title" class="form-control" required placeholder="Ví dụ: Đại Tiệc Mỹ Phẩm Chuẩn Hàn">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Badge Nổi Bật</label>
                                <input type="text" id="banner_badge_text" class="form-control" placeholder="Ví dụ: DEAL HOT HÔM NAY">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Phụ Đề / Mô Tả Ngắn</label>
                                <input type="text" id="banner_subtitle" class="form-control" placeholder="Ví dụ: Giảm tới 45% toàn bộ sản phẩm serum & kem chống nắng chính hãng">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Link Ảnh Banner (URL) *</label>
                                <input type="url" id="banner_image_url" class="form-control" required placeholder="https://images.unsplash.com/..." oninput="AdminApp.previewBannerImage(this.value)">
                                <div class="mt-2 text-center p-2 border rounded-3 bg-light" id="banner-preview-box" style="display: none;">
                                    <div class="small text-muted mb-1">Xem trước ảnh banner:</div>
                                    <img id="banner-preview-img" src="" alt="Banner Preview" style="max-height: 180px; width: 100%; object-fit: cover; border-radius: 8px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Đường Dẫn Bấm Vào (Link URL)</label>
                                <input type="text" id="banner_link_url" class="form-control" placeholder="#products-all hoặc /san-pham/...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Nút Bấm Kêu Gọi</label>
                                <input type="text" id="banner_button_text" class="form-control" placeholder="Khám Phá Ngay">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Thứ Tự Ưu Tiên</label>
                                <input type="number" id="banner_sort_order" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="banner_is_active" checked>
                                    <label class="form-check-label fw-semibold" for="banner_is_active">Kích hoạt hiển thị trên slider trang chủ</label>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" id="btnSaveBanner" class="btn btn-pink rounded-pill px-4">Lưu Banner</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TẠO MÃ GIẢM GIÁ -->
    <div class="modal fade" id="modalAdminCoupon" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tạo Mã Giảm Giá Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCouponDetail" onsubmit="AdminApp.saveCoupon(event)">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Mã Voucher (In hoa) *</label>
                            <input type="text" id="cpn_code" class="form-control text-uppercase fw-bold" required placeholder="VÍ DỤ: SALE50K">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Loại Giảm Giá</label>
                                <select id="cpn_discount_type" class="form-select">
                                    <option value="fixed">Giảm số tiền cố định (VNĐ)</option>
                                    <option value="percent">Giảm theo phần trăm (%)</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Mức Giảm *</label>
                                <input type="number" id="cpn_discount_value" class="form-control" required placeholder="50000 hoặc 10">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Đơn Tối Thiểu (VNĐ)</label>
                                <input type="number" id="cpn_min_order" class="form-control" value="0">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-semibold">Giảm Tối Đa (Nếu %)</label>
                                <input type="number" id="cpn_max_discount" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Mô Tả Mã</label>
                            <input type="text" id="cpn_description" class="form-control" placeholder="Giảm 50.000đ cho đơn từ 299k">
                        </div>
                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" id="btnSaveCoupon" class="btn btn-pink rounded-pill px-4">Tạo Mã Voucher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CHỈNH SỬA DANH MỤC TRÒN -->
    <div class="modal fade" id="modalEditCategory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="modalCategoryTitle">Chỉnh Sửa Danh Mục Tròn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCategoryDetail" onsubmit="AdminApp.saveCategory(event)">
                        <input type="hidden" id="cat_id">
                        
                        <!-- Preview ảnh tròn -->
                        <div class="text-center mb-3">
                            <label class="form-label small fw-semibold d-block text-muted">Xem Trước Ảnh Bong Bóng</label>
                            <div class="d-inline-block position-relative p-1 rounded-circle border border-2 border-danger-subtle shadow-sm bg-light">
                                <img id="cat_image_preview" src="" alt="Preview" 
                                     class="rounded-circle object-fit-cover" 
                                     style="width: 80px; height: 80px;"
                                     onerror="this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=500&q=80'">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Link Ảnh Danh Mục (URL) *</label>
                            <input type="url" id="cat_image" class="form-control" required 
                                   placeholder="https://images.unsplash.com/photo-..."
                                   oninput="document.getElementById('cat_image_preview').src = this.value.trim()">
                            <div class="form-text small">Dán link ảnh trực tiếp (Unsplash, Imgur, link web ảnh công khai...).</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Tên Danh Mục *</label>
                            <input type="text" id="cat_name" class="form-control" required placeholder="Ví dụ: Son Môi & Trang Điểm">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Mô Tả Ngắn</label>
                            <textarea id="cat_description" class="form-control" rows="2" placeholder="Nước tẩy trang, sữa rửa mặt dịu lành..."></textarea>
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-light rounded-pill px-4 me-2" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" id="btnSaveCategory" class="btn btn-pink rounded-pill px-4">
                                <i class="fa-solid fa-floppy-disk me-1"></i>Lưu Danh Mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const AdminApp = {
    allOrders: [],
    banks: [],
    cachedLowStock: [],
    cachedProducts: [],
    cachedBanners: [],
    cachedCategories: [],
    cachedCoupons: [],
    cachedPosts: [],

    formatVND(num) {
        return new Intl.NumberFormat('vi-VN').format(num) + '₫';
    },

    escHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    },

    decodeEntities(str) {
        const raw = String(str ?? '');
        if (!raw || !raw.includes('&')) return raw;
        const ta = document.createElement('textarea');
        ta.innerHTML = raw;
        return ta.value;
    },

    async handleLogin(e) {
        e.preventDefault();
        const email = document.getElementById('adminEmail').value.trim();
        const password = document.getElementById('adminPassword').value.trim();
        const btn = document.getElementById('btnAdminLogin');

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang đăng nhập...`;

        try {
            const res = await fetch('api/auth.php?action=login', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ identifier: email, password: password })
            });
            const data = await res.json();
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-right-to-bracket me-2"></i>Đăng Nhập Quản Trị`;

            if (data.success) {
                if (data.user && data.user.role === 'admin') {
                    window.location.reload();
                } else {
                    alert('Tài khoản này không phải là Quản Trị Viên!');
                }
            } else {
                alert(data.message || 'Sai email hoặc mật khẩu quản trị!');
            }
        } catch(err) {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-right-to-bracket me-2"></i>Đăng Nhập Quản Trị`;
            alert('Lỗi kết nối máy chủ!');
        }
    },

    async logout() {
        if (!confirm('Bạn có chắc chắn muốn đăng xuất quyền Quản Trị?')) return;
        await fetch('api/auth.php?action=logout');
        window.location.reload();
    },

    switchToTab(tabId) {
        const triggerEl = document.querySelector(`button[data-bs-target="#${tabId}"]`);
        if (triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
            if (tabId === 'tab-orders') this.loadOrders();
            else if (tabId === 'tab-products') this.loadProducts();
            else if (tabId === 'tab-banners') this.loadBanners();
            else if (tabId === 'tab-categories') this.loadCategories();
            else if (tabId === 'tab-coupons') this.loadCoupons();
            else if (tabId === 'tab-posts') this.loadPosts();
            else if (tabId === 'tab-settings') this.loadSettings();
        }
    },

    // 1. TẢI THỐNG KÊ TỔNG QUAN
    async loadStats() {
        try {
            const res = await fetch('api/admin.php?action=stats');
            const data = await res.json();
            if (data.success) {
                const s = data.stats;
                document.getElementById('stat-revenue').textContent = this.formatVND(s.total_revenue);
                document.getElementById('stat-orders').textContent = s.total_orders;
                document.getElementById('stat-pending-orders').textContent = s.pending_orders;
                document.getElementById('stat-products').textContent = s.total_products;
                
                // Cảnh báo tồn kho & Banner
                const elLowStock = document.getElementById('stat-low-stock');
                if (elLowStock) elLowStock.textContent = s.low_stock_count;
                const elOutOfStock = document.getElementById('stat-out-of-stock');
                if (elOutOfStock) elOutOfStock.textContent = s.out_of_stock_count;
                const elBanners = document.getElementById('stat-banners');
                if (elBanners) elBanners.textContent = (typeof s.active_banners !== 'undefined') ? s.active_banners : (s.total_banners || 0);

                document.getElementById('count-orders-badge').textContent = s.total_orders;
                document.getElementById('count-products-badge').textContent = s.total_products;
                const badgeBanners = document.getElementById('count-banners-badge');
                if (badgeBanners) badgeBanners.textContent = s.total_banners || 0;
                const badgeCategories = document.getElementById('count-categories-badge');
                if (badgeCategories && typeof s.total_categories !== 'undefined') badgeCategories.textContent = s.total_categories;
                const badgePosts = document.getElementById('count-posts-badge');
                if (badgePosts && typeof s.total_posts !== 'undefined') badgePosts.textContent = s.total_posts;

                // Render danh sách cảnh báo tồn kho sắp hết (< 10)
                this.cachedLowStock = data.low_stock_products || [];
                const tbodyLowStock = document.getElementById('tbody-low-stock');
                if (tbodyLowStock) {
                    if (this.cachedLowStock.length > 0) {
                        tbodyLowStock.innerHTML = this.cachedLowStock.map(p => {
                            const isZero = (p.stock_quantity <= 0);
                            const prodImg = this.escHtml(p.image || 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80');
                            const prodName = this.escHtml(this.decodeEntities(p.name));
                            return `
                                <tr>
                                    <td><img src="${prodImg}" width="42" height="42" class="rounded-3 object-fit-cover border" alt="${prodName}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80';"></td>
                                    <td>
                                        <div class="fw-semibold text-dark" style="max-width: 250px;">${prodName}</div>
                                        <div class="small text-muted font-monospace">SKU: ALICE-${Number(p.id)}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-secondary border">${this.escHtml(this.decodeEntities(p.brand || 'ALICE'))}</span>
                                        <span class="small text-muted ms-1">${this.escHtml(this.decodeEntities(p.origin || 'Hàn Quốc'))}</span>
                                    </td>
                                    <td class="fw-bold text-danger">${this.formatVND(p.price)}</td>
                                    <td>
                                        <span class="fw-bold fs-6 ${isZero ? 'text-danger' : 'text-warning-emphasis'}">${Number(p.stock_quantity)} sp</span>
                                    </td>
                                    <td>
                                        ${isZero 
                                            ? '<span class="badge bg-danger text-white"><i class="fa-solid fa-circle-xmark me-1"></i>Hết Hàng (0)</span>' 
                                            : '<span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i>Sắp Hết</span>'}
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 font-semibold" onclick="AdminApp.quickEditStock(${Number(p.id)})">
                                            <i class="fa-solid fa-plus me-1"></i>Nhập Hàng
                                        </button>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                    } else {
                        tbodyLowStock.innerHTML = `<tr><td colspan="7" class="text-center py-3 text-success fw-semibold"><i class="fa-solid fa-circle-check me-2"></i>Tuyệt vời! Tất cả sản phẩm mỹ phẩm trong kho đều còn đủ số lượng (> 10 sp).</td></tr>`;
                    }
                }

                // Render đơn gần đây
                const tbody = document.getElementById('tbody-recent-orders');
                if (data.recent_orders && data.recent_orders.length > 0) {
                    tbody.innerHTML = data.recent_orders.map(o => `
                        <tr>
                            <td class="fw-bold text-dark">#${this.escHtml(o.order_code)}</td>
                            <td>${this.escHtml(this.decodeEntities(o.customer_name))}</td>
                            <td>${this.escHtml(o.phone)}</td>
                            <td class="fw-bold text-danger">${this.formatVND(o.final_amount)}</td>
                            <td><span class="badge bg-light text-dark border">${o.payment_method === 'vietqr' ? '⚡ VietQR' : '💵 COD'}</span></td>
                            <td>
                                <span class="badge badge-status ${o.payment_status === 'paid' ? 'bg-success text-white' : 'bg-warning text-dark'}">
                                    ${o.payment_status === 'paid' ? 'Đã Thanh Toán' : 'Chờ Thanh Toán'}
                                </span>
                            </td>
                            <td class="small text-muted">${this.escHtml(o.created_at || 'Mới đây')}</td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">Chưa có đơn hàng nào</td></tr>`;
                }
            }
        } catch(e) {
            console.error(e);
        }
    },

    // 2. TẢI & QUẢN LÝ ĐƠN HÀNG
    async loadOrders() {
        try {
            const res = await fetch('api/admin.php?action=orders');
            const data = await res.json();
            if (data.success) {
                this.allOrders = data.orders || [];
                document.getElementById('count-orders-badge').textContent = this.allOrders.length;
                this.filterOrders();
            }
        } catch(e) {
            console.error(e);
        }
    },

    filterOrders() {
        const filter = document.getElementById('filterOrderStatus').value;
        const tbody = document.getElementById('tbody-orders');
        const list = (filter === 'all') 
            ? this.allOrders 
            : this.allOrders.filter(o => o.payment_status === filter);

        if (list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted">Không tìm thấy đơn hàng nào phù hợp</td></tr>`;
            return;
        }

        tbody.innerHTML = list.map(o => {
            let items = [];
            try { items = JSON.parse(o.items_json); } catch(e) {}
            const itemsSummary = Array.isArray(items)
                ? items.map(it => `<div>• ${this.escHtml(this.decodeEntities(it.name))} (x${Number(it.quantity) || 1})</div>`).join('')
                : '';

            return `
                <tr>
                    <td class="fw-bold text-dark">#${this.escHtml(o.order_code)}</td>
                    <td class="fw-semibold">${this.escHtml(this.decodeEntities(o.customer_name))}</td>
                    <td>
                        <div class="small fw-semibold text-dark">${this.escHtml(o.phone)}</div>
                        <div class="small text-muted text-truncate" style="max-width: 200px;">${this.escHtml(this.decodeEntities(o.address))}</div>
                    </td>
                    <td class="small text-muted" style="max-width: 250px;">${itemsSummary || 'Mỹ phẩm'}</td>
                    <td class="fw-bold text-danger">${this.formatVND(o.final_amount)}</td>
                    <td><span class="badge bg-light text-dark border">${o.payment_method === 'vietqr' ? '⚡ VietQR' : '💵 COD'}</span></td>
                    <td>
                        <select class="form-select form-select-sm rounded-pill fw-semibold ${o.payment_status === 'paid' ? 'text-success border-success' : 'text-warning border-warning'}" onchange="AdminApp.updateOrderStatus(${Number(o.id)}, this.value)">
                            <option value="pending" ${o.payment_status === 'pending' ? 'selected' : ''}>⏳ Chờ TT</option>
                            <option value="paid" ${o.payment_status === 'paid' ? 'selected' : ''}>✅ Đã Thanh Toán</option>
                            <option value="shipping" ${o.payment_status === 'shipping' ? 'selected' : ''}>🚚 Đang Giao</option>
                            <option value="cancelled" ${o.payment_status === 'cancelled' ? 'selected' : ''}>❌ Đã Hủy</option>
                        </select>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-outline-danger btn-sm rounded-pill" onclick="AdminApp.quickConfirmPaid(${Number(o.id)})" title="Đánh dấu đã thanh toán">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    },

    async updateOrderStatus(orderId, status) {
        try {
            const res = await fetch('api/admin.php?action=update_order_status', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ order_id: orderId, payment_status: status })
            });
            const data = await res.json();
            if (data.success) {
                this.loadStats();
            }
        } catch(e) {
            alert('Lỗi cập nhật trạng thái đơn!');
        }
    },

    async quickConfirmPaid(orderId) {
        if (!confirm(`Xác nhận khách đã thanh toán thành công cho đơn #${orderId}?`)) return;
        await this.updateOrderStatus(orderId, 'paid');
        await this.loadOrders();
    },

    // 3. TẢI & QUẢN LÝ SẢN PHẨM & TỒN KHO
    async loadProducts() {
        try {
            const res = await fetch('api/admin.php?action=products');
            const data = await res.json();
            if (data.success) {
                this.cachedProducts = data.products || [];
                document.getElementById('count-products-badge').textContent = this.cachedProducts.length;
                this.filterProducts();
            }
        } catch(e) {
            console.error(e);
        }
    },

    filterProducts() {
        const filter = document.getElementById('filterProductStock') ? document.getElementById('filterProductStock').value : 'all';
        const tbody = document.getElementById('tbody-products');
        let list = this.cachedProducts || [];

        if (filter === 'in_stock') {
            list = list.filter(p => p.stock_quantity > 10);
        } else if (filter === 'low_stock') {
            list = list.filter(p => p.stock_quantity > 0 && p.stock_quantity <= 10);
        } else if (filter === 'out_of_stock') {
            list = list.filter(p => p.stock_quantity <= 0);
        }

        if (list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center py-5 text-muted">Không tìm thấy sản phẩm nào phù hợp bộ lọc</td></tr>`;
            return;
        }

        tbody.innerHTML = list.map(p => {
            const stock = (typeof p.stock_quantity !== 'undefined') ? parseInt(p.stock_quantity) : 50;
            let stockBadge = '';
            if (stock > 10) {
                stockBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-boxes-stacked me-1"></i>${stock} sp</span>`;
            } else if (stock > 0) {
                stockBadge = `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="fa-solid fa-triangle-exclamation me-1"></i>Sắp hết (${stock})</span>`;
            } else {
                stockBadge = `<span class="badge bg-danger text-white px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i>Hết hàng (0)</span>`;
            }

            const prodImg = this.escHtml(p.image || 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80');
            const prodName = this.escHtml(this.decodeEntities(p.name));

            return `
                <tr>
                    <td><img src="${prodImg}" width="48" height="48" class="rounded-3 object-fit-cover border" alt="${prodName}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80';"></td>
                    <td>
                        <div class="fw-semibold text-dark" style="max-width: 230px;">${prodName}</div>
                        <div class="small text-muted font-monospace">SKU: ALICE-${Number(p.id)}</div>
                    </td>
                    <td>
                        <span class="badge bg-light text-secondary border">${this.escHtml(this.decodeEntities(p.brand || 'ALICE'))}</span>
                        <div class="small text-muted mt-1"><i class="fa-solid fa-earth-americas me-1 text-danger"></i>${this.escHtml(this.decodeEntities(p.origin || 'Hàn Quốc'))}</div>
                    </td>
                    <td>
                        <div class="small text-dark fw-semibold">${this.escHtml(p.category_id)}</div>
                        <div class="small text-muted">${this.escHtml(this.decodeEntities(p.capacity || 'Tiêu chuẩn'))}</div>
                    </td>
                    <td class="fw-bold text-danger">${this.formatVND(p.price)}</td>
                    <td class="text-muted text-decoration-line-through small">${this.formatVND(p.original_price)}</td>
                    <td>
                        <div class="d-flex align-items-center gap-1">
                            ${stockBadge}
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-0" style="font-size: 0.72rem;" onclick="AdminApp.quickEditStock(${Number(p.id)})" title="Sửa nhanh số lượng kho">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        ${p.is_flash_sale ? '<span class="badge bg-danger rounded-pill">⚡ Bật (' + (Number(p.flash_sale_stock) || 0) + ')</span>' : '<span class="badge bg-light text-muted border">Tắt</span>'}
                    </td>
                    <td class="text-end">
                        <button class="btn btn-outline-primary btn-sm rounded-circle me-1" onclick="AdminApp.editProduct(${Number(p.id)})" title="Chỉnh sửa chi tiết"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn btn-outline-danger btn-sm rounded-circle" onclick="AdminApp.deleteProduct(${Number(p.id)})" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>
            `;
        }).join('');
    },

    async quickEditStock(id, nameArg = null, stockArg = null) {
        const cached = (this.cachedProducts || []).find(it => it.id == id) || (this.cachedLowStock || []).find(it => it.id == id);
        const name = this.decodeEntities(nameArg !== null ? nameArg : (cached ? cached.name : `#${id}`));
        const currentStock = stockArg !== null ? stockArg : (cached && typeof cached.stock_quantity !== 'undefined' ? cached.stock_quantity : 0);

        const input = prompt(`Nhập số lượng tồn kho mới cho sản phẩm:\n"${name}"`, currentStock);
        if (input === null) return;
        const newStock = parseInt(input.trim());
        if (isNaN(newStock) || newStock < 0) {
            alert('Vui lòng nhập số lượng hợp lệ (>= 0)!');
            return;
        }

        try {
            const res = await fetch('api/admin.php?action=update_stock', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id: id, stock_quantity: newStock })
            });
            const data = await res.json();
            if (data.success) {
                this.loadProducts();
                this.loadStats();
            } else {
                alert(data.message || 'Lỗi cập nhật tồn kho!');
            }
        } catch(e) {
            alert('Lỗi kết nối máy chủ!');
        }
    },

    openModalProduct() {
        document.getElementById('modalProductTitle').textContent = 'Thêm Mỹ Phẩm Mới';
        document.getElementById('formProductDetail').reset();
        document.getElementById('prod_id').value = '';
        document.getElementById('prod_stock_quantity').value = 50;
        document.getElementById('prod_origin').value = 'Hàn Quốc';
        document.getElementById('prod_capacity').value = '50ml';
        document.getElementById('prod_expiry').value = '36 tháng kể từ NSX (12 tháng mở nắp)';
        document.getElementById('prod_skin_type').value = 'all';
        document.getElementById('prod_ingredients').value = '';
        document.getElementById('prod_how_to_use').value = '';
        new bootstrap.Modal(document.getElementById('modalAdminProduct')).show();
    },

    editProduct(id) {
        const p = (this.cachedProducts || []).find(it => it.id == id);
        if (!p) return;

        const cleanName = this.decodeEntities(p.name);
        document.getElementById('modalProductTitle').textContent = 'Sửa Sản Phẩm: ' + cleanName;
        document.getElementById('prod_id').value = p.id;
        document.getElementById('prod_name').value = cleanName;
        document.getElementById('prod_brand').value = this.decodeEntities(p.brand);
        document.getElementById('prod_category_id').value = p.category_id;
        document.getElementById('prod_price').value = p.price;
        document.getElementById('prod_original_price').value = p.original_price;
        document.getElementById('prod_image').value = p.image;
        document.getElementById('prod_description').value = this.decodeEntities(p.description || '');
        document.getElementById('prod_is_flash_sale').checked = (p.is_flash_sale == 1);
        document.getElementById('prod_flash_sale_stock').value = p.flash_sale_stock || 50;

        // Các thông số mới & tồn kho
        document.getElementById('prod_stock_quantity').value = (typeof p.stock_quantity !== 'undefined') ? p.stock_quantity : 50;
        document.getElementById('prod_origin').value = this.decodeEntities(p.origin || 'Hàn Quốc');
        document.getElementById('prod_capacity').value = this.decodeEntities(p.capacity || 'Tiêu chuẩn');
        document.getElementById('prod_expiry').value = this.decodeEntities(p.expiry || '36 tháng kể từ NSX (12 tháng mở nắp)');
        document.getElementById('prod_skin_type').value = p.skin_type || 'all';
        document.getElementById('prod_ingredients').value = this.decodeEntities(p.ingredients || '');
        document.getElementById('prod_how_to_use').value = this.decodeEntities(p.how_to_use || '');

        new bootstrap.Modal(document.getElementById('modalAdminProduct')).show();
    },

    async saveProduct(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveProduct');
        btn.disabled = true;

        const payload = {
            id: document.getElementById('prod_id').value || null,
            name: document.getElementById('prod_name').value.trim(),
            brand: document.getElementById('prod_brand').value.trim(),
            category_id: document.getElementById('prod_category_id').value,
            price: document.getElementById('prod_price').value,
            original_price: document.getElementById('prod_original_price').value,
            image: document.getElementById('prod_image').value.trim(),
            description: document.getElementById('prod_description').value.trim(),
            is_flash_sale: document.getElementById('prod_is_flash_sale').checked ? 1 : 0,
            flash_sale_stock: document.getElementById('prod_flash_sale_stock').value,
            stock_quantity: document.getElementById('prod_stock_quantity').value,
            origin: document.getElementById('prod_origin').value.trim(),
            capacity: document.getElementById('prod_capacity').value.trim(),
            expiry: document.getElementById('prod_expiry').value.trim(),
            skin_type: document.getElementById('prod_skin_type').value,
            ingredients: document.getElementById('prod_ingredients').value.trim(),
            how_to_use: document.getElementById('prod_how_to_use').value.trim()
        };

        try {
            const res = await fetch('api/admin.php?action=save_product', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            btn.disabled = false;
            if (data.success) {
                alert(data.message);
                bootstrap.Modal.getInstance(document.getElementById('modalAdminProduct')).hide();
                this.loadProducts();
                this.loadStats();
            } else {
                alert(data.message || 'Lỗi lưu sản phẩm!');
            }
        } catch(err) {
            btn.disabled = false;
            alert('Lỗi kết nối máy chủ!');
        }
    },

    async deleteProduct(id, nameArg = null) {
        const cached = (this.cachedProducts || []).find(it => it.id == id);
        const name = this.decodeEntities(nameArg !== null ? nameArg : (cached ? cached.name : `#${id}`));
        if (!confirm(`Bạn có chắc muốn xóa vĩnh viễn sản phẩm: "${name}"?`)) return;
        try {
            const res = await fetch('api/admin.php?action=delete_product', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id: id })
            });
            const data = await res.json();
            if (data.success) {
                this.loadProducts();
                this.loadStats();
            } else {
                alert(data.message || 'Không thể xóa sản phẩm!');
            }
        } catch(e) {
            alert('Lỗi kết nối máy chủ!');
        }
    },

    // ==========================================
    // 3.1 QUẢN LÝ BANNER QUẢNG CÁO
    // ==========================================
    async loadBanners() {
        const tbody = document.getElementById('tbody-banners');
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tải danh sách banner...</td></tr>';

        try {
            const res = await fetch('api/admin.php?action=banners');
            const data = await res.json();
            if (data.success) {
                this.cachedBanners = data.banners || [];
                const countBadge = document.getElementById('count-banners-badge');
                if (countBadge) countBadge.textContent = this.cachedBanners.length;
                const statBanners = document.getElementById('stat-banners');
                if (statBanners) statBanners.textContent = this.cachedBanners.filter(b => b.is_active == 1).length;

                if (this.cachedBanners.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted">Chưa có banner nào. Hãy bấm "Thêm Banner Mới"!</td></tr>`;
                    return;
                }

                tbody.innerHTML = this.cachedBanners.map(b => {
                    const bImg = this.escHtml(b.image_url || 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1200&q=80');
                    const bTitle = this.escHtml(this.decodeEntities(b.title));
                    return `
                    <tr>
                        <td>
                            <img src="${bImg}" width="130" height="60" class="rounded-3 object-fit-cover border shadow-sm" alt="${bTitle}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1200&q=80';">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">${bTitle}</div>
                            <div class="small text-muted text-truncate" style="max-width: 250px;">${this.escHtml(this.decodeEntities(b.subtitle || ''))}</div>
                        </td>
                        <td>
                            ${b.badge_text ? '<span class="badge bg-pink text-white rounded-pill px-2 py-1">' + this.escHtml(this.decodeEntities(b.badge_text)) + '</span>' : '<span class="text-muted small">—</span>'}
                        </td>
                        <td>
                            <span class="small font-monospace text-primary text-truncate d-inline-block" style="max-width: 140px;">${this.escHtml(this.decodeEntities(b.link_url || '#'))}</span>
                        </td>
                        <td>
                            <span class="small fw-semibold text-dark">${this.escHtml(this.decodeEntities(b.button_text || 'Xem Ngay'))}</span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">${Number(b.sort_order) || 0}</span>
                        </td>
                        <td>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" ${b.is_active == 1 ? 'checked' : ''} onchange="AdminApp.toggleBanner(${Number(b.id)}, this.checked ? 1 : 0)">
                                <label class="form-check-label small ${b.is_active == 1 ? 'text-success fw-bold' : 'text-muted'}">${b.is_active == 1 ? 'Bật' : 'Tắt'}</label>
                            </div>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-outline-primary btn-sm rounded-circle me-1" onclick="AdminApp.editBanner(${Number(b.id)})" title="Chỉnh sửa"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-outline-danger btn-sm rounded-circle" onclick="AdminApp.deleteBanner(${Number(b.id)})" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                }).join('');
            }
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Lỗi kết nối tải banner!</td></tr>';
        }
    },

    openModalBanner() {
        document.getElementById('modalBannerTitle').textContent = 'Thêm Banner Quảng Cáo Mới';
        document.getElementById('formBannerDetail').reset();
        document.getElementById('banner_id').value = '';
        document.getElementById('banner_sort_order').value = 0;
        document.getElementById('banner_is_active').checked = true;
        document.getElementById('banner_button_text').value = 'Khám Phá Ngay';
        document.getElementById('banner_badge_text').value = 'MỸ PHẨM CHÍNH HÃNG';
        document.getElementById('banner-preview-box').style.display = 'none';
        new bootstrap.Modal(document.getElementById('modalAdminBanner')).show();
    },

    previewBannerImage(url) {
        const box = document.getElementById('banner-preview-box');
        const img = document.getElementById('banner-preview-img');
        if (url && url.trim().length > 5) {
            box.style.display = 'block';
            img.src = url.trim();
        } else {
            box.style.display = 'none';
        }
    },

    editBanner(id) {
        const b = (this.cachedBanners || []).find(it => it.id == id);
        if (!b) return;

        const cleanTitle = this.decodeEntities(b.title);
        document.getElementById('modalBannerTitle').textContent = 'Sửa Banner: ' + cleanTitle;
        document.getElementById('banner_id').value = b.id;
        document.getElementById('banner_title').value = cleanTitle;
        document.getElementById('banner_subtitle').value = this.decodeEntities(b.subtitle || '');
        document.getElementById('banner_badge_text').value = this.decodeEntities(b.badge_text || '');
        document.getElementById('banner_image_url').value = b.image_url;
        document.getElementById('banner_link_url').value = this.decodeEntities(b.link_url || '');
        document.getElementById('banner_button_text').value = this.decodeEntities(b.button_text || 'Khám Phá Ngay');
        document.getElementById('banner_sort_order').value = b.sort_order || 0;
        document.getElementById('banner_is_active').checked = (b.is_active == 1);

        this.previewBannerImage(b.image_url);
        new bootstrap.Modal(document.getElementById('modalAdminBanner')).show();
    },

    async saveBanner(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveBanner');
        btn.disabled = true;

        const payload = {
            id: document.getElementById('banner_id').value || null,
            title: document.getElementById('banner_title').value.trim(),
            subtitle: document.getElementById('banner_subtitle').value.trim(),
            badge_text: document.getElementById('banner_badge_text').value.trim(),
            image_url: document.getElementById('banner_image_url').value.trim(),
            link_url: document.getElementById('banner_link_url').value.trim(),
            button_text: document.getElementById('banner_button_text').value.trim(),
            sort_order: parseInt(document.getElementById('banner_sort_order').value) || 0,
            is_active: document.getElementById('banner_is_active').checked ? 1 : 0
        };

        try {
            const res = await fetch('api/admin.php?action=save_banner', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            btn.disabled = false;
            if (data.success) {
                alert(data.message || 'Đã lưu banner thành công!');
                bootstrap.Modal.getInstance(document.getElementById('modalAdminBanner')).hide();
                this.loadBanners();
                this.loadStats();
            } else {
                alert(data.message || 'Lỗi lưu banner!');
            }
        } catch(err) {
            btn.disabled = false;
            alert('Lỗi kết nối máy chủ!');
        }
    },

    async toggleBanner(id, isActive) {
        try {
            const payload = { id: id };
            if (typeof isActive !== 'undefined') {
                payload.is_active = isActive ? 1 : 0;
            }
            const res = await fetch('api/admin.php?action=toggle_banner', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                this.loadBanners();
                this.loadStats();
            } else {
                alert(data.message || 'Lỗi đổi trạng thái banner!');
            }
        } catch(e) {
            alert('Lỗi kết nối máy chủ!');
        }
    },

    async deleteBanner(id, titleArg = null) {
        const cached = (this.cachedBanners || []).find(it => it.id == id);
        const title = this.decodeEntities(titleArg !== null ? titleArg : (cached ? cached.title : `#${id}`));
        if (!confirm(`Bạn có chắc chắn muốn xóa vĩnh viễn banner "${title}"?`)) return;
        try {
            const res = await fetch('api/admin.php?action=delete_banner', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id: id })
            });
            const data = await res.json();
            if (data.success) {
                this.loadBanners();
                this.loadStats();
            } else {
                alert(data.message || 'Lỗi xóa banner!');
            }
        } catch(e) {
            alert('Lỗi kết nối máy chủ!');
        }
    },

    // 3.5. QUẢN LÝ DANH MỤC TRÒN (CATEGORIES)
    async loadCategories() {
        const tbody = document.getElementById('tbody-categories');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tải danh mục...</td></tr>';

        try {
            const res = await fetch('api/admin.php?action=categories');
            const data = await res.json();
            if (data.success) {
                this.cachedCategories = data.categories || [];
                const badgeCategories = document.getElementById('count-categories-badge');
                if (badgeCategories) badgeCategories.textContent = this.cachedCategories.length;
                if (this.cachedCategories.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">Chưa có danh mục nào.</td></tr>`;
                    return;
                }

                tbody.innerHTML = this.cachedCategories.map(c => {
                    const catImg = this.escHtml(c.image || 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=500&q=80');
                    const catName = this.escHtml(this.decodeEntities(c.name));
                    const catDesc = this.escHtml(this.decodeEntities(c.description || ''));
                    const catId = this.escHtml(c.id);
                    return `
                    <tr>
                        <td>
                            <div class="d-inline-block p-1 rounded-circle border border-danger-subtle bg-white shadow-sm">
                                <img src="${catImg}" width="50" height="50" class="rounded-circle object-fit-cover" alt="${catName}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=500&q=80'">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">${catName}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark font-monospace border px-2 py-1">${catId}</span>
                        </td>
                        <td>
                            <span class="badge bg-pink text-white rounded-pill px-3 py-1">
                                <i class="fa-solid fa-tag me-1"></i>${Number(c.actual_product_count) || 0} sản phẩm
                            </span>
                        </td>
                        <td>
                            <div class="small text-muted text-truncate" style="max-width: 280px;" title="${catDesc}">${catDesc || '—'}</div>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-cat-id="${catId}" onclick="AdminApp.editCategory(this.dataset.catId)">
                                <i class="fa-solid fa-pen me-1"></i>Sửa
                            </button>
                        </td>
                    </tr>
                `;
                }).join('');
            }
        } catch(e) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-danger">Lỗi tải danh mục!</td></tr>';
        }
    },

    editCategory(id) {
        const cat = (this.cachedCategories || []).find(c => c.id === id);
        if (!cat) return;
        const cleanName = this.decodeEntities(cat.name);
        document.getElementById('cat_id').value = cat.id;
        document.getElementById('cat_name').value = cleanName;
        document.getElementById('cat_image').value = cat.image;
        document.getElementById('cat_image_preview').src = cat.image;
        document.getElementById('cat_description').value = this.decodeEntities(cat.description || '');
        document.getElementById('modalCategoryTitle').textContent = `Chỉnh Sửa Danh Mục: ${cleanName}`;
        new bootstrap.Modal(document.getElementById('modalEditCategory')).show();
    },

    async saveCategory(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveCategory');
        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang lưu...`;

        const payload = {
            id: document.getElementById('cat_id').value.trim(),
            name: document.getElementById('cat_name').value.trim(),
            image: document.getElementById('cat_image').value.trim(),
            description: document.getElementById('cat_description').value.trim()
        };

        try {
            const res = await fetch('api/admin.php?action=save_category', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-floppy-disk me-1"></i>Lưu Danh Mục`;

            if (data.success) {
                alert(data.message || 'Cập nhật danh mục thành công!');
                bootstrap.Modal.getInstance(document.getElementById('modalEditCategory')).hide();
                this.loadCategories();
            } else {
                alert(data.message || 'Lưu thất bại!');
            }
        } catch(err) {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-floppy-disk me-1"></i>Lưu Danh Mục`;
            alert('Lỗi kết nối máy chủ!');
        }
    },

    // 4. TẢI & QUẢN LÝ MÃ GIẢM GIÁ (COUPONS)
    async loadCoupons() {
        try {
            const res = await fetch('api/admin.php?action=coupons');
            const data = await res.json();
            if (data.success) {
                const coupons = data.coupons || [];
                this.cachedCoupons = coupons;
                const tbody = document.getElementById('tbody-coupons');
                if (coupons.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted">Chưa có mã voucher nào</td></tr>`;
                    return;
                }

                tbody.innerHTML = coupons.map(c => {
                    const codeEsc = this.escHtml(this.decodeEntities(c.code));
                    return `
                    <tr>
                        <td class="fw-bold text-dark"><span class="badge bg-pink text-white rounded-pill px-3 py-1">${codeEsc}</span></td>
                        <td>${c.discount_type === 'percent' ? 'Giảm %' : 'Số tiền cố định'}</td>
                        <td class="fw-bold text-danger">${c.discount_type === 'percent' ? Number(c.discount_value) + '%' : this.formatVND(c.discount_value)}</td>
                        <td>${this.formatVND(c.min_order)}</td>
                        <td>${c.max_discount > 0 ? this.formatVND(c.max_discount) : 'Không giới hạn'}</td>
                        <td class="small text-muted">${this.escHtml(this.decodeEntities(c.description || ''))}</td>
                        <td class="text-end">
                            <button class="btn btn-outline-danger btn-sm rounded-circle" data-code="${codeEsc}" onclick="AdminApp.deleteCoupon(this.dataset.code)" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                }).join('');
            }
        } catch(e) {
            console.error(e);
        }
    },

    openModalCoupon() {
        document.getElementById('formCouponDetail').reset();
        new bootstrap.Modal(document.getElementById('modalAdminCoupon')).show();
    },

    async saveCoupon(e) {
        e.preventDefault();
        const payload = {
            code: document.getElementById('cpn_code').value.trim(),
            discount_type: document.getElementById('cpn_discount_type').value,
            discount_value: document.getElementById('cpn_discount_value').value,
            min_order: document.getElementById('cpn_min_order').value,
            max_discount: document.getElementById('cpn_max_discount').value,
            description: document.getElementById('cpn_description').value.trim()
        };

        try {
            const res = await fetch('api/admin.php?action=save_coupon', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                bootstrap.Modal.getInstance(document.getElementById('modalAdminCoupon')).hide();
                this.loadCoupons();
            } else {
                alert(data.message || 'Lỗi tạo voucher!');
            }
        } catch(e) {
            alert('Lỗi kết nối máy chủ!');
        }
    },

    async deleteCoupon(code) {
        if (!confirm(`Xác nhận xóa mã voucher "${code}"?`)) return;
        try {
            const res = await fetch('api/admin.php?action=delete_coupon', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ code: code })
            });
            const data = await res.json();
            if (data.success) {
                this.loadCoupons();
            } else {
                alert(data.message || 'Lỗi xóa voucher!');
            }
        } catch(e) {
            alert('Lỗi máy chủ!');
        }
    },

    // 5. CÀI ĐẶT CỬA HÀNG & VIETQR
    async loadSettings() {
        try {
            const res = await fetch('api/admin.php?action=settings');
            const data = await res.json();
            if (data.success) {
                const c = data.config;
                this.banks = data.banks || [];

                document.getElementById('cfg_shop_name').value = c.shop_name || '';
                document.getElementById('cfg_slogan').value = c.slogan || '';
                document.getElementById('cfg_hotline').value = c.hotline || '';
                document.getElementById('cfg_zalo').value = c.zalo || '';
                document.getElementById('cfg_freeship_min').value = c.freeship_min || 299000;
                document.getElementById('cfg_address').value = c.address || '';
                document.getElementById('cfg_account_no').value = c.account_no || '';
                document.getElementById('cfg_account_name').value = c.account_name || '';
                document.getElementById('cfg_google_sheet_webhook').value = c.google_sheet_webhook || '';

                // Populate bank list
                const bankSelect = document.getElementById('cfg_bank_id');
                bankSelect.innerHTML = this.banks.map(b => `
                    <option value="${b.id}" ${b.id === c.bank_id ? 'selected' : ''}>${b.name} (${b.id})</option>
                `).join('');

                this.updatePreviewQR();
            }
        } catch(e) {
            console.error(e);
        }
    },

    updatePreviewQR() {
        const bankId = document.getElementById('cfg_bank_id').value || 'MB';
        const accountNo = document.getElementById('cfg_account_no').value.trim() || '5550372005777';
        const accountName = document.getElementById('cfg_account_name').value.trim() || 'ALICE COSMETIC';

        const qrUrl = `https://img.vietqr.io/image/${encodeURIComponent(bankId)}-${encodeURIComponent(accountNo)}-compact2.png?accountName=${encodeURIComponent(accountName)}`;
        document.getElementById('preview-vietqr-img').src = qrUrl;
        document.getElementById('preview-qr-info').innerHTML = `<strong>${this.escHtml(bankId)}</strong>: ${this.escHtml(accountNo)} (${this.escHtml(accountName)})`;
    },

    async saveSettings(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSaveSettings');
        btn.disabled = true;

        const payload = {
            shop_name: document.getElementById('cfg_shop_name').value.trim(),
            slogan: document.getElementById('cfg_slogan').value.trim(),
            hotline: document.getElementById('cfg_hotline').value.trim(),
            zalo: document.getElementById('cfg_zalo').value.trim(),
            freeship_min: document.getElementById('cfg_freeship_min').value,
            address: document.getElementById('cfg_address').value.trim(),
            bank_id: document.getElementById('cfg_bank_id').value,
            bank_name: (this.banks.find(b => b.id === document.getElementById('cfg_bank_id').value) || {}).name || '',
            account_no: document.getElementById('cfg_account_no').value.trim(),
            account_name: document.getElementById('cfg_account_name').value.trim().toUpperCase(),
            qr_template: 'compact2',
            google_sheet_webhook: document.getElementById('cfg_google_sheet_webhook').value.trim()
        };

        try {
            const res = await fetch('api/admin.php?action=settings', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            btn.disabled = false;
            alert(data.message || 'Đã lưu cấu hình!');
        } catch(err) {
            btn.disabled = false;
            alert('Lỗi lưu cài đặt!');
        }
    },

    async loadPosts() {
        const tbody = document.getElementById('posts-table-body');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><i class="fa-solid fa-spinner fa-spin text-pink-500 me-2"></i>Đang tải danh sách bài viết...</td></tr>';

        try {
            const res = await fetch('api/admin.php?action=posts');
            const data = await res.json();
            if (!data.success) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">${this.escHtml(data.message || 'Lỗi tải bài viết')}</td></tr>`;
                return;
            }

            const posts = data.posts || [];
            this.cachedPosts = posts;
            document.getElementById('count-posts-badge').innerText = posts.length;

            if (posts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Chưa có bài viết cẩm nang nào. Hãy bấm "Viết Bài Mới"!</td></tr>';
                return;
            }

            const defaultPostImg = 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80';
            tbody.innerHTML = posts.map(p => {
                const postImg = this.escHtml(p.image || defaultPostImg);
                const postTitle = this.escHtml(this.decodeEntities(p.title));
                const postSlug = this.escHtml(p.slug || '');
                return `
                <tr>
                    <td>
                        <img src="${postImg}" alt="${postTitle}" style="width: 55px; height: 55px; object-fit: cover; border-radius: 8px;" onerror="this.onerror=null; this.src='${defaultPostImg}';">
                    </td>
                    <td>
                        <div class="fw-bold text-dark">${postTitle}</div>
                        <div class="small text-muted font-monospace"><a href="cam-nang/${postSlug}" target="_blank" class="text-decoration-none text-primary"><i class="fa-solid fa-link me-1"></i>/cam-nang/${postSlug}</a></div>
                    </td>
                    <td><span class="badge rounded-pill bg-light text-dark border px-2 py-1">${this.escHtml(this.decodeEntities(p.category || 'Skincare'))}</span></td>
                    <td><span class="small text-muted"><i class="fa-regular fa-clock me-1"></i>${Number(p.reading_time) || 5} phút</span></td>
                    <td><span class="small fw-semibold text-danger"><i class="fa-regular fa-eye me-1"></i>${Number(p.views) || 0}</span></td>
                    <td><span class="small text-muted">${this.escHtml((p.created_at || '').substring(0, 10))}</span></td>
                    <td class="text-end">
                        <a href="cam-nang/${postSlug}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 me-1" title="Xem trên web"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 me-1" onclick="AdminApp.editPost(${Number(p.id)})" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" onclick="AdminApp.deletePost(${Number(p.id)})" title="Xóa bài"><i class="fa-solid fa-trash-can"></i></button>
                    </td>
                </tr>
            `;
            }).join('');
        } catch(err) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Lỗi kết nối tải bài viết!</td></tr>';
        }
    },

    openPostModal(post = null) {
        document.getElementById('post_id').value = post ? post.id : '';
        document.getElementById('post_title').value = post ? this.decodeEntities(post.title) : '';
        document.getElementById('post_slug').value = post ? (post.slug || '') : '';
        document.getElementById('post_category').value = post ? this.decodeEntities(post.category || 'Skincare') : 'Skincare';
        document.getElementById('post_reading_time').value = post ? post.reading_time : 5;
        document.getElementById('post_author').value = post ? this.decodeEntities(post.author || 'Chuyên Gia Da Liễu ALICE') : 'Chuyên Gia Da Liễu ALICE';
        document.getElementById('post_image').value = post ? (post.image || '') : '';
        document.getElementById('post_excerpt').value = post ? this.decodeEntities(post.excerpt || '') : '';
        document.getElementById('post_content').value = post ? (post.content || '') : '';
        
        let relatedStr = '';
        if (post && post.related_products) {
            try {
                const decodedRelated = this.decodeEntities(post.related_products);
                const arr = JSON.parse(decodedRelated);
                relatedStr = Array.isArray(arr) ? arr.join(', ') : '';
            } catch(e) { relatedStr = this.decodeEntities(post.related_products); }
        }
        document.getElementById('post_related_products').value = relatedStr;

        document.getElementById('modalPostTitle').innerText = post ? 'Chỉnh Sửa Bài Viết Cẩm Nang' : 'Viết Bài Cẩm Nang Mới';
        new bootstrap.Modal(document.getElementById('modalAdminPost')).show();
    },

    async editPost(id) {
        try {
            const res = await fetch(`api/admin.php?action=post_detail&id=${id}`);
            const data = await res.json();
            if (data.success && data.post) {
                this.openPostModal(data.post);
            } else {
                alert(data.message || 'Không thể tải chi tiết bài viết!');
            }
        } catch(e) {
            alert('Lỗi kết nối!');
        }
    },

    async savePost(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSavePost');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Đang lưu...';

        const id = document.getElementById('post_id').value;
        const relatedRaw = document.getElementById('post_related_products').value.trim();
        let relatedArr = [];
        if (relatedRaw) {
            relatedArr = relatedRaw.split(',').map(s => parseInt(s.trim())).filter(n => !isNaN(n));
        }

        const payload = {
            id: id ? parseInt(id) : 0,
            title: document.getElementById('post_title').value.trim(),
            slug: document.getElementById('post_slug').value.trim(),
            category: document.getElementById('post_category').value,
            reading_time: parseInt(document.getElementById('post_reading_time').value) || 5,
            author: document.getElementById('post_author').value.trim(),
            image: document.getElementById('post_image').value.trim(),
            excerpt: document.getElementById('post_excerpt').value.trim(),
            content: document.getElementById('post_content').value.trim(),
            related_products: JSON.stringify(relatedArr)
        };

        try {
            const res = await fetch('api/admin.php?action=save_post', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up me-1"></i>Lưu & Xuất Bản';

            if (data.success) {
                alert(data.message || 'Đã lưu bài viết!');
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAdminPost'));
                if (modal) modal.hide();
                this.loadPosts();
                this.loadStats();
            } else {
                alert(data.message || 'Lỗi lưu bài viết!');
            }
        } catch(err) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up me-1"></i>Lưu & Xuất Bản';
            alert('Lỗi kết nối máy chủ!');
        }
    },

    async deletePost(id, titleArg = null) {
        const cached = (this.cachedPosts || []).find(it => it.id == id);
        const title = this.decodeEntities(titleArg !== null ? titleArg : (cached ? cached.title : `#${id}`));
        if (!confirm(`Bạn có chắc chắn muốn xóa bài viết "${title}"?`)) return;

        try {
            const res = await fetch('api/admin.php?action=delete_post', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ id: id })
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message || 'Đã xóa bài viết!');
                this.loadPosts();
                this.loadStats();
            } else {
                alert(data.message || 'Không thể xóa bài viết!');
            }
        } catch(err) {
            alert('Lỗi kết nối máy chủ!');
        }
    }
};

<?php if ($isAdmin): ?>
document.addEventListener('DOMContentLoaded', () => {
    AdminApp.loadStats();
});
<?php endif; ?>
</script>

</body>
</html>
