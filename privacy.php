<?php
/**
 * CHÍNH SÁCH BẢO MẬT & QUYỀN RIÊNG TƯ - ALICE COSMETIC
 * URL hợp lệ phục vụ đăng ký & xác thực Facebook App & Google OAuth
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$shopConfig = getShopConfig($pdo);
$baseUrl = getBaseUrl();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính Sách Bảo Mật & Quyền Riêng Tư — <?= htmlspecialchars($shopConfig['shop_name']) ?></title>
    <meta name="description" content="Chính sách bảo mật thông tin khách hàng, quyền riêng tư và quy định bảo vệ dữ liệu khi đăng nhập qua Facebook/Google tại ALICE Cosmetic.">
    <link rel="canonical" href="<?= $baseUrl ?>/privacy.php">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌸</text></svg>">
    
    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css?v=<?= time() ?>" rel="stylesheet">
</head>
<body style="background: #fff5f7; color: #374151; font-family: 'Segoe UI', system-ui, sans-serif;">

    <!-- Header đơn giản -->
    <header class="py-3 bg-white shadow-sm sticky-top border-bottom" style="border-color: #ffd6e0 !important;">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="index.php" class="d-flex align-items-center gap-2 text-decoration-none">
                <span class="fs-3">🌸</span>
                <div>
                    <h1 class="h5 m-0 font-bold" style="color: #ff4d88; font-family: 'Playfair Display', serif;"><?= htmlspecialchars($shopConfig['shop_name']) ?></h1>
                    <small class="text-muted" style="font-size: 0.75rem;">Mỹ Phẩm & Routine Skincare Chính Hãng</small>
                </div>
            </a>
            <a href="index.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay về Cửa Hàng
            </a>
        </div>
    </header>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border" style="border-color: #ffe3ec !important;">
                    <div class="text-center mb-4 pb-3 border-bottom">
                        <span class="badge rounded-pill px-3 py-2 text-uppercase mb-2" style="background: #ffe3ec; color: #ff3377; font-weight: 700; font-size: 0.75rem;">Chính Sách Bảo Mật</span>
                        <h2 class="font-bold h3" style="color: #1f2937;">Chính Sách Bảo Mật & Quyền Riêng Tư (Privacy Policy)</h2>
                        <p class="text-muted small">Cập nhật lần cuối: 10/11/2026 — Áp dụng cho website và tính năng Đăng nhập Facebook / Google</p>
                    </div>

                    <div class="content" style="line-height: 1.8; font-size: 0.95rem;">
                        <h4 class="h5 font-bold mt-4" style="color: #ff3377;"><i class="fa-solid fa-shield-halved me-2"></i>1. Giới thiệu & Cam kết</h4>
                        <p>Chào mừng bạn đến với <strong><?= htmlspecialchars($shopConfig['shop_name']) ?></strong>. Chúng tôi tôn trọng và cam kết bảo vệ quyền riêng tư cũng như dữ liệu cá nhân của mọi khách hàng khi truy cập và mua sắm trên hệ thống của chúng tôi.</p>

                        <h4 class="h5 font-bold mt-4" style="color: #ff3377;"><i class="fa-solid fa-database me-2"></i>2. Dữ liệu chúng tôi thu thập khi Đăng nhập Facebook / Google</h4>
                        <p>Khi bạn sử dụng tính năng <strong>Đăng nhập bằng Facebook</strong> hoặc <strong>Đăng nhập bằng Google</strong>, hệ thống chỉ yêu cầu các thông tin công khai cơ bản được bạn đồng ý cấp quyền, bao gồm:</p>
                        <ul>
                            <li><strong>Họ và tên hiển thị:</strong> Nhằm mục đích xưng hô, hiển thị tài khoản thành viên và in trên phiếu giao hàng.</li>
                            <li><strong>Địa chỉ Email:</strong> Dùng để xác thực tài khoản duy nhất, gửi thông báo xác nhận đơn hàng mỹ phẩm và mã vận đơn.</li>
                            <li><strong>Ảnh đại diện (Avatar):</strong> Hiển thị biểu tượng người dùng trên góc điều hướng website.</li>
                        </ul>
                        <p class="text-muted fst-italic">Chúng tôi <strong>KHÔNG</strong> thu thập danh sách bạn bè, tin nhắn riêng tư, bài viết hay bất kỳ dữ liệu nhạy cảm nào khác từ trang cá nhân Facebook của bạn.</p>

                        <h4 class="h5 font-bold mt-4" style="color: #ff3377;"><i class="fa-solid fa-bullseye me-2"></i>3. Mục đích sử dụng thông tin</h4>
                        <p>Thông tin thu thập được sử dụng duy nhất cho các mục đích:</p>
                        <ul>
                            <li>Quản lý tài khoản khách hàng thân thiết, tích điểm voucher quà tặng khai trương.</li>
                            <li>Xử lý và giao hàng tận nơi đối với các đơn hàng mỹ phẩm chính hãng.</li>
                            <li>Cung cấp hỗ trợ khách hàng nhanh chóng qua Hotline <strong>0869.571.734</strong> và Zalo.</li>
                        </ul>

                        <h4 class="h5 font-bold mt-4" style="color: #ff3377;"><i class="fa-solid fa-lock me-2"></i>4. Cam kết bảo mật & Không chia sẻ bên thứ ba</h4>
                        <p>Chúng tôi tuyệt đối <strong>không bán, cho thuê hoặc chia sẻ</strong> thông tin cá nhân của bạn cho bất kỳ bên thứ ba nào vì mục đích quảng cáo thương mại. Dữ liệu được mã hóa và lưu trữ an toàn trên máy chủ đạt chuẩn.</p>

                        <h4 class="h5 font-bold mt-4" style="color: #ff3377;"><i class="fa-solid fa-trash-can me-2"></i>5. Hướng dẫn Yêu cầu Xóa Dữ Liệu (Data Deletion Instructions)</h4>
                        <p>Theo Quy định của Nền tảng Facebook và quyền riêng tư người dùng, bạn có toàn quyền yêu cầu xóa bỏ tài khoản và toàn bộ dữ liệu liên quan tại <?= htmlspecialchars($shopConfig['shop_name']) ?> bất cứ lúc nào bằng các cách sau:</p>
                        <ol>
                            <li><strong>Cách 1 (Tự gỡ quyền trên Facebook):</strong> Truy cập <em>Cài đặt & Quyền riêng tư &gt; Cài đặt &gt; Ứng dụng và trang web</em> trên Facebook cá nhân &gt; Tìm ứng dụng <strong>ALICE Cosmetic</strong> &gt; Nhấn <strong>Gỡ</strong>.</li>
                            <li><strong>Cách 2 (Gửi yêu cầu trực tiếp):</strong> Liên hệ trực tiếp quản trị viên qua Hotline/Zalo: <strong>0869.571.734</strong> hoặc email: <strong>ngoclinh@gmail.com</strong>. Dữ liệu của bạn sẽ được xóa vĩnh viễn khỏi cơ sở dữ liệu trong vòng 24 giờ làm việc.</li>
                        </ol>

                        <h4 class="h5 font-bold mt-4" style="color: #ff3377;"><i class="fa-solid fa-phone me-2"></i>6. Thông tin liên hệ</h4>
                        <p class="mb-1"><strong>Cửa hàng Mỹ Phẩm ALICE Cosmetic</strong></p>
                        <p class="mb-1">📍 Địa chỉ: 175 Tây Sơn, Đống Đa, Hà Nội</p>
                        <p class="mb-1">📞 Hotline / Zalo: <strong>0869.571.734</strong></p>
                        <p class="mb-0">🌐 Website: <a href="<?= $baseUrl ?>" class="text-pink-600 font-bold"><?= $baseUrl ?></a></p>
                    </div>

                    <div class="mt-5 pt-3 border-top text-center">
                        <a href="index.php" class="btn btn-danger rounded-pill px-4 py-2" style="background: linear-gradient(135deg, #ff4d88, #ff3377); border: none;">
                            <i class="fa-solid fa-shopping-bag me-1"></i> Khám Phá Sản Phẩm ALICE Cosmetic
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer đơn giản -->
    <footer class="py-4 text-center text-muted small bg-white border-top">
        <p class="m-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($shopConfig['shop_name']) ?> — All rights reserved.</p>
    </footer>

</body>
</html>
