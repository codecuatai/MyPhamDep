<?php
/**
 * TRANG QUẢN TRỊ HỆ THỐNG (ADMIN DASHBOARD) - ALICE COSMETIC
 * Quản lý đơn hàng, kho mỹ phẩm, voucher và cấu hình VietQR
 */

if (session_status() === PHP_SESSION_NONE) {
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

            <div class="alert alert-info py-2 px-3 small text-start mb-4 border-0 rounded-3" style="background: #eef2ff; color: #3730a3;">
                <i class="fa-solid fa-circle-info me-1"></i> <strong>Tài khoản mặc định:</strong><br>
                • Email: <code>admin@alicecosmetic.vn</code><br>
                • Mật khẩu: <code>Admin@123</code>
            </div>

            <form id="formAdminLogin" onsubmit="AdminApp.handleLogin(event)">
                <div class="mb-3 text-start">
                    <label class="form-label small fw-semibold text-secondary">Email Quản Trị</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                        <input type="email" id="adminEmail" class="form-control border-start-0" value="admin@alicecosmetic.vn" required placeholder="admin@alicecosmetic.vn">
                    </div>
                </div>

                <div class="mb-4 text-start">
                    <label class="form-label small fw-semibold text-secondary">Mật Khẩu Quản Trị</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" id="adminPassword" class="form-control border-start-0" value="Admin@123" required placeholder="Nhập mật khẩu...">
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
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-coupons" type="button" onclick="AdminApp.loadCoupons()">
                    <i class="fa-solid fa-ticket me-1"></i>Mã Giảm Giá
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
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">DOANH THU ĐÃ THU</span>
                                <div class="stat-icon" style="background: #dcfce7; color: #16a34a;">
                                    <i class="fa-solid fa-coins"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1" id="stat-revenue" style="color: #16a34a;">0₫</h3>
                            <div class="text-muted small">Từ các đơn VietQR & Đã giao</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
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
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">SẢN PHẨM MỸ PHẨM</span>
                                <div class="stat-icon" style="background: #fdf2f8; color: #db2777;">
                                    <i class="fa-solid fa-wand-magic"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1 text-dark" id="stat-products">0</h3>
                            <div class="text-muted small">Son, serum, kem chống nắng...</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">THÀNH VIÊN ĐĂNG KÝ</span>
                                <div class="stat-icon" style="background: #ede9fe; color: #7c3aed;">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-1 text-dark" id="stat-users">0</h3>
                            <div class="text-muted small">Google, Facebook & Web</div>
                        </div>
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
                            <p class="text-muted small mb-0">Thêm mới, đổi giá bán, cập nhật chương trình Flash Sale</p>
                        </div>
                        <button class="btn btn-pink rounded-pill px-4" onclick="AdminApp.openModalProduct()">
                            <i class="fa-solid fa-plus me-2"></i>Thêm Mỹ Phẩm Mới
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="small text-muted">
                                    <th>ẢNH</th>
                                    <th>TÊN SẢN PHẨM</th>
                                    <th>THƯƠNG HIỆU</th>
                                    <th>DANH MỤC</th>
                                    <th>GIÁ BÁN</th>
                                    <th>GIÁ GỐC</th>
                                    <th>FLASH SALE</th>
                                    <th class="text-end">THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-products">
                                <tr><td colspan="8" class="text-center py-4 text-muted">Đang tải sản phẩm...</td></tr>
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
                                    <option value="son-moi">Son Môi</option>
                                    <option value="duong-da">Chăm Sóc Da (Skincare)</option>
                                    <option value="trang-diem">Trang Điểm (Makeup)</option>
                                    <option value="chong-nang">Kem Chống Nắng</option>
                                    <option value="combo-qua">Combo Quà Tặng</option>
                                    <option value="nuoc-hoa">Nước Hoa & Body</option>
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
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Link Ảnh Sản Phẩm (URL) *</label>
                                <input type="url" id="prod_image" class="form-control" required placeholder="https://images.unsplash.com/...">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Mô Tả Sản Phẩm</label>
                                <textarea id="prod_description" class="form-control" rows="3" placeholder="Mô tả công dụng, xuất xứ..."></textarea>
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
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
const AdminApp = {
    allOrders: [],
    banks: [],

    formatVND(num) {
        return new Intl.NumberFormat('vi-VN').format(num) + '₫';
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
                document.getElementById('stat-users').textContent = s.total_users;

                document.getElementById('count-orders-badge').textContent = s.total_orders;
                document.getElementById('count-products-badge').textContent = s.total_products;

                // Render đơn gần đây
                const tbody = document.getElementById('tbody-recent-orders');
                if (data.recent_orders && data.recent_orders.length > 0) {
                    tbody.innerHTML = data.recent_orders.map(o => `
                        <tr>
                            <td class="fw-bold text-dark">#${o.order_code}</td>
                            <td>${o.customer_name}</td>
                            <td>${o.phone}</td>
                            <td class="fw-bold text-danger">${this.formatVND(o.final_amount)}</td>
                            <td><span class="badge bg-light text-dark border">${o.payment_method === 'vietqr' ? '⚡ VietQR' : '💵 COD'}</span></td>
                            <td>
                                <span class="badge badge-status ${o.payment_status === 'paid' ? 'bg-success text-white' : 'bg-warning text-dark'}">
                                    ${o.payment_status === 'paid' ? 'Đã Thanh Toán' : 'Chờ Thanh Toán'}
                                </span>
                            </td>
                            <td class="small text-muted">${o.created_at || 'Mới đây'}</td>
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
            const itemsSummary = items.map(it => `<div>• ${it.name} (x${it.quantity})</div>`).join('');

            return `
                <tr>
                    <td class="fw-bold text-dark">#${o.order_code}</td>
                    <td class="fw-semibold">${o.customer_name}</td>
                    <td>
                        <div class="small fw-semibold text-dark">${o.phone}</div>
                        <div class="small text-muted text-truncate" style="max-width: 200px;">${o.address}</div>
                    </td>
                    <td class="small text-muted" style="max-width: 250px;">${itemsSummary || 'Mỹ phẩm'}</td>
                    <td class="fw-bold text-danger">${this.formatVND(o.final_amount)}</td>
                    <td><span class="badge bg-light text-dark border">${o.payment_method === 'vietqr' ? '⚡ VietQR' : '💵 COD'}</span></td>
                    <td>
                        <select class="form-select form-select-sm rounded-pill fw-semibold ${o.payment_status === 'paid' ? 'text-success border-success' : 'text-warning border-warning'}" onchange="AdminApp.updateOrderStatus(${o.id}, this.value)">
                            <option value="pending" ${o.payment_status === 'pending' ? 'selected' : ''}>⏳ Chờ TT</option>
                            <option value="paid" ${o.payment_status === 'paid' ? 'selected' : ''}>✅ Đã Thanh Toán</option>
                            <option value="shipping" ${o.payment_status === 'shipping' ? 'selected' : ''}>🚚 Đang Giao</option>
                            <option value="cancelled" ${o.payment_status === 'cancelled' ? 'selected' : ''}>❌ Đã Hủy</option>
                        </select>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-outline-danger btn-sm rounded-pill" onclick="AdminApp.quickConfirmPaid(${o.id})" title="Đánh dấu đã thanh toán">
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

    // 3. TẢI & QUẢN LÝ SẢN PHẨM
    async loadProducts() {
        try {
            const res = await fetch('api/admin.php?action=products');
            const data = await res.json();
            if (data.success) {
                const products = data.products || [];
                document.getElementById('count-products-badge').textContent = products.length;
                const tbody = document.getElementById('tbody-products');
                if (products.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5 text-muted">Kho chưa có sản phẩm nào</td></tr>`;
                    return;
                }

                tbody.innerHTML = products.map(p => `
                    <tr>
                        <td><img src="${p.image}" width="48" height="48" class="rounded-3 object-fit-cover border" alt="${p.name}"></td>
                        <td class="fw-semibold text-dark" style="max-width: 250px;">${p.name}</td>
                        <td><span class="badge bg-light text-secondary border">${p.brand || 'ALICE'}</span></td>
                        <td class="small text-muted">${p.category_id}</td>
                        <td class="fw-bold text-danger">${this.formatVND(p.price)}</td>
                        <td class="text-muted text-decoration-line-through small">${this.formatVND(p.original_price)}</td>
                        <td>
                            ${p.is_flash_sale ? '<span class="badge bg-danger rounded-pill">⚡ Bật (' + (p.flash_sale_stock || 0) + ')</span>' : '<span class="badge bg-light text-muted border">Tắt</span>'}
                        </td>
                        <td class="text-end">
                            <button class="btn btn-outline-primary btn-sm rounded-circle me-1" onclick="AdminApp.editProduct(${p.id})" title="Sửa"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn btn-outline-danger btn-sm rounded-circle" onclick="AdminApp.deleteProduct(${p.id}, '${p.name}')" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('');
                this.cachedProducts = products;
            }
        } catch(e) {
            console.error(e);
        }
    },

    openModalProduct() {
        document.getElementById('modalProductTitle').textContent = 'Thêm Mỹ Phẩm Mới';
        document.getElementById('formProductDetail').reset();
        document.getElementById('prod_id').value = '';
        new bootstrap.Modal(document.getElementById('modalAdminProduct')).show();
    },

    editProduct(id) {
        const p = (this.cachedProducts || []).find(it => it.id == id);
        if (!p) return;

        document.getElementById('modalProductTitle').textContent = 'Sửa Sản Phẩm: ' + p.name;
        document.getElementById('prod_id').value = p.id;
        document.getElementById('prod_name').value = p.name;
        document.getElementById('prod_brand').value = p.brand;
        document.getElementById('prod_category_id').value = p.category_id;
        document.getElementById('prod_price').value = p.price;
        document.getElementById('prod_original_price').value = p.original_price;
        document.getElementById('prod_image').value = p.image;
        document.getElementById('prod_description').value = p.description || '';
        document.getElementById('prod_is_flash_sale').checked = (p.is_flash_sale == 1);
        document.getElementById('prod_flash_sale_stock').value = p.flash_sale_stock || 50;

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
            flash_sale_stock: document.getElementById('prod_flash_sale_stock').value
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

    async deleteProduct(id, name) {
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

    // 4. TẢI & QUẢN LÝ MÃ GIẢM GIÁ (COUPONS)
    async loadCoupons() {
        try {
            const res = await fetch('api/admin.php?action=coupons');
            const data = await res.json();
            if (data.success) {
                const coupons = data.coupons || [];
                const tbody = document.getElementById('tbody-coupons');
                if (coupons.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-5 text-muted">Chưa có mã voucher nào</td></tr>`;
                    return;
                }

                tbody.innerHTML = coupons.map(c => `
                    <tr>
                        <td class="fw-bold text-dark"><span class="badge bg-pink text-white rounded-pill px-3 py-1">${c.code}</span></td>
                        <td>${c.discount_type === 'percent' ? 'Giảm %' : 'Số tiền cố định'}</td>
                        <td class="fw-bold text-danger">${c.discount_type === 'percent' ? c.discount_value + '%' : this.formatVND(c.discount_value)}</td>
                        <td>${this.formatVND(c.min_order)}</td>
                        <td>${c.max_discount > 0 ? this.formatVND(c.max_discount) : 'Không giới hạn'}</td>
                        <td class="small text-muted">${c.description || ''}</td>
                        <td class="text-end">
                            <button class="btn btn-outline-danger btn-sm rounded-circle" onclick="AdminApp.deleteCoupon('${c.code}')" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('');
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
        const accountNo = document.getElementById('cfg_account_no').value.trim() || '0869571734';
        const accountName = document.getElementById('cfg_account_name').value.trim() || 'ALICE COSMETIC';

        const qrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-compact2.png?accountName=${encodeURIComponent(accountName)}`;
        document.getElementById('preview-vietqr-img').src = qrUrl;
        document.getElementById('preview-qr-info').innerHTML = `<strong>${bankId}</strong>: ${accountNo} (${accountName})`;
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
