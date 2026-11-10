<?php
/**
 * TRANG XÁC NHẬN THANH TOÁN DI ĐỘNG (CROSS-DEVICE PAYMENT CONFIRMATION)
 * ALICE COSMETIC - Quét QR từ điện thoại để kích hoạt thanh toán tự động trên máy tính
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$shopConfig = getShopConfig($pdo);

$orderCode = cleanInput($_GET['code'] ?? ($_GET['order_code'] ?? ''));

$order = null;
if (!empty($orderCode)) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = ? LIMIT 1");
    $stmt->execute([$orderCode]);
    $order = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Xác Nhận Thanh Toán — <?= htmlspecialchars($shopConfig['shop_name']) ?></title>
    
    <!-- Bootstrap 5.3 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #ff4d8d;
            --primary-soft: #fff0f5;
            --primary-gradient: linear-gradient(135deg, #ff4d8d 0%, #ff758c 100%);
            --bg-color: #fcf8fa;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-color);
            color: #374151;
            min-height: 100vh;
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pay-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 15px 35px rgba(255, 77, 141, 0.12), 0 5px 15px rgba(0,0,0,0.05);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            border: 1px solid #ffe4ed;
        }
        .pay-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.75rem 1.5rem;
            text-align: center;
            position: relative;
        }
        .btn-confirm-pay {
            background: var(--primary-gradient);
            color: white;
            border: none;
            box-shadow: 0 8px 20px rgba(255, 77, 141, 0.35);
            transition: all 0.2s ease;
        }
        .btn-confirm-pay:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(255, 77, 141, 0.45);
            color: white;
        }
        .btn-confirm-pay:active {
            transform: translateY(0);
        }
        .success-checkmark {
            width: 72px;
            height: 72px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 25px rgba(22, 163, 74, 0.2);
            animation: popIn 0.4s ease-out;
        }
        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .pulsing-dot {
            width: 10px;
            height: 10px;
            background-color: #22c55e;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse 1.6s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
    </style>
</head>
<body>

<div class="pay-card">
    <!-- Header -->
    <div class="pay-header">
        <div class="d-inline-flex p-2 bg-white bg-opacity-25 rounded-circle mb-2">
            <i class="fa-solid fa-shield-heart fs-4"></i>
        </div>
        <h4 class="fw-bold mb-1"><?= htmlspecialchars($shopConfig['shop_name']) ?></h4>
        <div class="small opacity-90"><span class="pulsing-dot me-1"></span> Cổng Thanh Toán Di Động Tự Động</div>
    </div>

    <!-- Body -->
    <div class="p-4" id="payContent">
        <?php if (!$order): ?>
            <!-- Đơn hàng không tồn tại -->
            <div class="text-center py-4">
                <div class="text-danger fs-1 mb-2">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h5 class="fw-bold text-dark">Không tìm thấy đơn hàng</h5>
                <p class="small text-muted mb-3">Mã đơn hàng <code><?= htmlspecialchars($orderCode) ?></code> không tồn tại hoặc đã hết hạn.</p>
                <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-4">Về Trang Chủ</a>
            </div>

        <?php elseif ($order['payment_status'] === 'paid'): ?>
            <!-- Đơn hàng đã được thanh toán từ trước -->
            <div class="text-center py-3">
                <div class="success-checkmark">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Đơn Hàng Đã Thanh Toán!</h5>
                <p class="small text-muted mb-3">Mã đơn: <strong class="text-danger">#<?= htmlspecialchars($order['order_code']) ?></strong></p>
                <div class="p-3 rounded-4 mb-3" style="background: var(--primary-soft); border: 1px dashed var(--primary);">
                    <div class="small text-muted">Số tiền đã thanh toán</div>
                    <div class="fs-4 fw-bold text-danger"><?= formatVND($order['final_amount']) ?></div>
                </div>
                <div class="alert alert-success py-2 px-3 small border-0 rounded-3 mb-0">
                    <i class="fa-solid fa-circle-check me-1"></i> Máy tính của bạn đã nhận diện thành công!
                </div>
            </div>

        <?php else: ?>
            <!-- Đơn hàng đang chờ thanh toán -->
            <div id="paymentPendingView">
                <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-3">
                    <div>
                        <span class="small text-muted d-block">Mã đơn hàng:</span>
                        <strong class="fs-5 text-dark">#<?= htmlspecialchars($order['order_code']) ?></strong>
                    </div>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-3 py-2 small">
                        <i class="fa-solid fa-clock me-1"></i>Chờ thanh toán
                    </span>
                </div>

                <!-- Tóm tắt số tiền -->
                <div class="p-3 rounded-4 text-center mb-3" style="background: #fff8fa; border: 1px solid #ffd4e3;">
                    <span class="small text-muted d-block mb-1">Tổng tiền cần thanh toán</span>
                    <div class="fs-2 fw-bold text-danger mb-0"><?= formatVND($order['final_amount']) ?></div>
                    <span class="small text-muted" style="font-size: 0.75rem;">(Đã áp dụng toàn bộ ưu đãi & voucher)</span>
                </div>

                <!-- Thông tin người nhận -->
                <div class="small text-secondary mb-4 p-3 bg-light rounded-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Khách hàng:</span>
                        <strong class="text-dark"><?= htmlspecialchars($order['customer_name']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Số điện thoại:</span>
                        <strong class="text-dark"><?= htmlspecialchars($order['phone']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Phương thức:</span>
                        <span class="badge bg-white text-dark border">⚡ VietQR Tự Động</span>
                    </div>
                </div>

                <div class="alert alert-light border small text-muted mb-4 py-2 px-3 rounded-3" style="background: #fbfcfe;">
                    <i class="fa-solid fa-shield-check text-success me-1"></i>
                    Thanh toán bảo mật 24/7. Khi bạn bấm nút xác nhận bên dưới, hệ thống trên máy tính của bạn sẽ <strong>tự động hoàn tất giao dịch</strong>.
                </div>

                <!-- Nút Xác Nhận Thanh Toán Di Động -->
                <button type="button" id="btnConfirmPay" class="btn btn-confirm-pay w-100 py-3 rounded-pill fw-bold fs-6 mb-2" onclick="confirmPayment('<?= htmlspecialchars($order['order_code']) ?>')">
                    <i class="fa-solid fa-circle-check me-2"></i>XÁC NHẬN THANH TOÁN
                </button>
            </div>

            <!-- Màn hình thành công sau khi bấm nút (Render qua JS) -->
            <div id="paymentSuccessView" style="display: none;" class="text-center py-3">
                <div class="success-checkmark">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">Thanh Toán Thành Công!</h4>
                <p class="small text-muted mb-3">Đơn hàng <strong class="text-danger">#<?= htmlspecialchars($order['order_code']) ?></strong> đã được xác nhận thanh toán.</p>
                
                <div class="alert alert-success border-0 rounded-4 py-3 px-3 mb-3 text-start shadow-sm" style="background: #eefdf3; color: #15803d;">
                    <div class="d-flex gap-2 align-items-center fw-bold mb-1">
                        <i class="fa-solid fa-laptop text-success fs-5"></i>
                        <span>Hãy nhìn lại màn hình máy tính của bạn!</span>
                    </div>
                    <div class="small opacity-90">
                        Màn hình máy tính của bạn đã tự động chuyển sang trang Hoàn Tất Đơn Hàng. Cảm ơn bạn!
                    </div>
                </div>

                <a href="index.php" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                    <i class="fa-solid fa-store me-1"></i>Vào Trang Cửa Hàng
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function confirmPayment(orderCode) {
    const btn = document.getElementById('btnConfirmPay');
    if (!btn) return;

    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang xác nhận với máy chủ...`;

    try {
        const res = await fetch(`api/orders.php?action=confirm_payment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `order_code=${encodeURIComponent(orderCode)}`
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('paymentPendingView').style.display = 'none';
            document.getElementById('paymentSuccessView').style.display = 'block';

            // Rung phản hồi trên điện thoại nếu thiết bị hỗ trợ
            if (navigator.vibrate) {
                navigator.vibrate([100, 50, 100]);
            }
        } else {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-circle-check me-2"></i>XÁC NHẬN THANH TOÁN`;
            alert(data.message || 'Lỗi xác nhận thanh toán!');
        }
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = `<i class="fa-solid fa-circle-check me-2"></i>XÁC NHẬN THANH TOÁN`;
        alert('Lỗi kết nối máy chủ!');
    }
}
</script>

</body>
</html>
