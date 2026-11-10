<?php
/**
 * MODALS & DRAWER COMPONENT - ALICE COSMETIC
 */
?>
<!-- =========================================================================
     1. SLIDE-OVER MINI CART DRAWER (Mộc Miên Books Style)
     ========================================================================= -->
<div id="cart-drawer-overlay" class="cart-drawer-overlay" onclick="AliceApp.closeCartDrawer()"></div>

<div id="cart-drawer" class="cart-drawer">
    <div class="cart-drawer-header">
        <div class="cart-drawer-title">
            <i class="fa-solid fa-bag-shopping" style="color: var(--primary);"></i>
            <span>Giỏ Hàng Của Bạn</span>
        </div>
        <button type="button" class="btn-close" onclick="AliceApp.closeCartDrawer()"></button>
    </div>

    <!-- Thanh tiến trình Freeship Mộc Miên Style -->
    <div class="freeship-progress-banner">
        <div id="freeship-progress-text" class="small text-muted mb-1 font-semibold">
            Chỉ cần mua thêm để được <strong>Freeship toàn quốc</strong>!
        </div>
        <div class="freeship-progress-bar-wrap">
            <div id="freeship-progress-fill" class="freeship-progress-bar-fill" style="width: 0%;"></div>
        </div>
    </div>

    <!-- Danh sách sản phẩm trong giỏ -->
    <div class="cart-drawer-body" id="cart-drawer-items">
        <!-- JS Render items -->
    </div>

    <!-- Voucher & Tóm tắt -->
    <div class="cart-drawer-footer">
        <!-- Khung áp mã Voucher Shopee Style -->
        <div class="mb-3">
            <div class="input-group input-group-sm">
                <input type="text" id="coupon-input" class="form-control" placeholder="Nhập mã: ALICEPINK, FREESHIP..." style="text-transform: uppercase;">
                <button class="btn btn-danger font-bold px-3" type="button" style="background: var(--gradient-pink); border: none;" onclick="AliceApp.applyCouponCode()">
                    Áp Dụng
                </button>
            </div>
            <div class="d-flex gap-2 mt-1">
                <span class="badge bg-white text-danger border small" style="cursor: pointer;" onclick="document.getElementById('coupon-input').value='ALICEPINK'; AliceApp.applyCouponCode();">🏷️ ALICEPINK (-50k)</span>
                <span class="badge bg-white text-danger border small" style="cursor: pointer;" onclick="document.getElementById('coupon-input').value='FREESHIP'; AliceApp.applyCouponCode();">🚚 FREESHIP</span>
            </div>
        </div>

        <div class="d-flex justify-content-between small text-muted mb-1">
            <span>Tạm tính:</span>
            <span id="cart-subtotal" class="font-bold">0₫</span>
        </div>
        <div class="d-flex justify-content-between small text-danger mb-2">
            <span>Voucher giảm giá:</span>
            <span id="cart-discount" class="font-bold">-0₫</span>
        </div>
        <div class="d-flex justify-content-between align-items-baseline mb-3">
            <span class="font-bold" style="color: var(--text-main);">Tổng thanh toán:</span>
            <span id="cart-final-total" class="fs-4 font-bold" style="color: var(--primary-dark);">0₫</span>
        </div>

        <button type="button" class="btn-checkout-now" onclick="AliceApp.openCheckoutModal()">
            <i class="fa-solid fa-lock me-1"></i>Tiến Hành Đặt Hàng
        </button>
    </div>
</div>

<!-- =========================================================================
     2. PRODUCT DETAIL MODAL (PDP & BIẾN THỂ DUNG TÍCH/MÀU SON & DEAL SỐC)
     ========================================================================= -->
<div class="modal fade" id="modalProductDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 overflow-hidden shadow-lg">
            <div class="modal-header border-0 pb-0" style="background: var(--primary-soft);">
                <span class="badge rounded-pill bg-danger px-3 py-1 font-bold">MALL CHÍNH HÃNG 100%</span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 pt-2">
                <div class="row g-4">
                    <!-- Gallery ảnh sản phẩm -->
                    <div class="col-md-5">
                        <div class="text-center mb-3">
                            <img id="pdp-main-img" src="" alt="Sản phẩm" class="img-fluid rounded-4 shadow-sm border border-pink-100" style="width: 100%; max-height: 280px; object-fit: cover;">
                        </div>
                        <div id="pdp-gallery-thumbs" class="d-flex gap-2 justify-content-center flex-wrap">
                            <!-- JS Render Thumbs -->
                        </div>
                    </div>

                    <!-- Thông tin & Chọn biến thể -->
                    <div class="col-md-7">
                        <div id="pdp-brand" class="small font-bold text-uppercase" style="color: var(--primary);"></div>
                        <h4 id="pdp-name" class="font-bold mt-1 mb-2" style="font-family: var(--font-heading); color: var(--text-main); font-size: 1.3rem;"></h4>
                        
                        <div class="d-flex align-items-center gap-3 small text-muted mb-3">
                            <span class="text-warning font-bold">★ <span id="pdp-rating">5.0</span></span>
                            <span>|</span>
                            <span>Đã bán <strong id="pdp-sold">0</strong></span>
                        </div>

                        <!-- Khung giá -->
                        <div class="p-3 rounded-3 mb-3 d-flex align-items-baseline gap-3" style="background: #fff0f5;">
                            <span id="pdp-price-current" class="fs-3 font-bold" style="color: var(--primary-dark);"></span>
                            <span id="pdp-price-original" class="small text-muted text-decoration-line-through"></span>
                            <span class="badge bg-danger rounded-pill">Tiết kiệm lớn</span>
                        </div>

                        <!-- Phân loại biến thể (Shopee Multi-variants) -->
                        <div class="mb-3">
                            <label class="small font-bold text-muted mb-2 d-block">CHỌN PHÂN LOẠI / DUNG TÍCH / TONE MÀU:</label>
                            <div id="pdp-variants-container">
                                <!-- JS Render Variants -->
                            </div>
                        </div>

                        <!-- Mua kèm Deal sốc (Shopee Add-on Deal) -->
                        <div id="pdp-addon-wrap" class="mb-3" style="display: none;">
                            <label class="small font-bold text-danger mb-2 d-block">
                                <i class="fa-solid fa-gift me-1"></i>MUA KÈM DEAL SỐC (Ưu đãi độc quyền):
                            </label>
                            <div id="pdp-addon-container">
                                <!-- JS Render Add-ons -->
                            </div>
                        </div>

                        <!-- Nút Mua ngay -->
                        <div class="d-flex gap-2 pt-2">
                            <button type="button" class="btn btn-danger flex-grow-1 rounded-pill py-2 font-bold" style="background: var(--gradient-pink); border: none;" onclick="AliceApp.addToCartCurrentProduct()">
                                <i class="fa-solid fa-cart-shopping me-2"></i>Thêm Vào Giỏ Hàng
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabs: Mô tả chi tiết, Thành phần, Review -->
                <div class="mt-4 pt-3 border-top">
                    <ul class="nav nav-pills mb-3 gap-2" id="pdp-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-pill px-3 py-1 font-bold small" data-bs-toggle="pill" data-bs-target="#tab-desc" type="button">Công Dụng</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill px-3 py-1 font-bold small" data-bs-toggle="pill" data-bs-target="#tab-ingredients" type="button">Bảng Thành Phần</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill px-3 py-1 font-bold small" data-bs-toggle="pill" data-bs-target="#tab-reviews" type="button">Đánh Giá (Feedback)</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="pdp-tab-content">
                        <div class="tab-pane fade show active small text-muted" id="tab-desc">
                            <p id="pdp-desc" style="line-height: 1.6;"></p>
                            <h6 class="font-bold text-dark mt-2 mb-1">Hướng dẫn sử dụng:</h6>
                            <p id="pdp-howtouse" style="line-height: 1.6;"></p>
                        </div>
                        <div class="tab-pane fade small text-muted" id="tab-ingredients">
                            <p id="pdp-ingredients" style="line-height: 1.6;"></p>
                        </div>
                        <div class="tab-pane fade" id="tab-reviews">
                            <div id="pdp-reviews-container">
                                <!-- JS Render Reviews -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     3. CHECKOUT MODAL (ĐIỀN ĐỊA CHỈ & CHỌN VIETQR HOẶC COD)
     ========================================================================= -->
<div class="modal fade" id="modalCheckout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0" style="background: var(--primary-soft);">
                <h5 class="modal-title font-bold" style="color: var(--primary-dark); font-family: var(--font-heading);">
                    <i class="fa-solid fa-bag-shopping me-2"></i>Thông Tin Đặt Hàng ALICE
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <form id="formCheckout" onsubmit="AliceApp.submitOrder(event)">
                    <!-- Tóm tắt đơn hàng -->
                    <div class="p-3 rounded-3 mb-3" style="background: #fff8fa; border: 1px dashed var(--primary-border);">
                        <h6 class="small font-bold text-muted mb-2">ĐƠN HÀNG CỦA BẠN:</h6>
                        <div id="checkout-items-summary" class="mb-2"></div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <strong style="color: var(--text-main);">Tổng thanh toán:</strong>
                            <strong id="checkout-final-amount" class="fs-5 text-danger font-bold">0₫</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small font-bold">Họ và tên người nhận (*)</label>
                        <input type="text" name="customer_name" class="form-control rounded-3" placeholder="Ví dụ: Nguyễn Ngọc Linh" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small font-bold">Số điện thoại (*)</label>
                        <input type="tel" name="phone" class="form-control rounded-3" placeholder="Ví dụ: 0987654321" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small font-bold">Địa chỉ giao hàng chi tiết (*)</label>
                        <textarea name="address" rows="2" class="form-control rounded-3" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small font-bold">Lời nhắn / Yêu cầu viết thiệp tặng quà</label>
                        <textarea name="note" rows="2" class="form-control rounded-3" placeholder="Ghi nội dung thiệp chúc mừng hoặc ghi chú giao hàng..."></textarea>
                    </div>

                    <!-- Phương thức thanh toán -->
                    <div class="mb-4">
                        <label class="form-label small font-bold d-block">Phương thức thanh toán:</label>
                        <div class="form-check p-3 rounded-3 mb-2 border" style="background: var(--primary-soft); border-color: var(--primary-border) !important;">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay_vietqr" value="vietqr" checked>
                            <label class="form-check-label font-bold small d-block" for="pay_vietqr" style="cursor: pointer;">
                                <i class="fa-solid fa-qrcode text-danger me-1"></i>Chuyển khoản VietQR Tự Động (Khuyên dùng - Nhanh 24/7)
                            </label>
                            <span class="small text-muted" style="font-size: 0.75rem;">Mở app ngân hàng quét mã QR, hệ thống tự điền số tiền và nội dung</span>
                        </div>
                        <div class="form-check p-3 rounded-3 border">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay_cod" value="cod">
                            <label class="form-check-label font-bold small d-block" for="pay_cod" style="cursor: pointer;">
                                <i class="fa-solid fa-truck-ramp-box text-success me-1"></i>Thanh toán khi nhận hàng (COD)
                            </label>
                            <span class="small text-muted" style="font-size: 0.75rem;">Kiểm tra hàng trước khi thanh toán tiền mặt cho shipper</span>
                        </div>
                    </div>

                    <button type="submit" id="btn-submit-order" class="btn btn-danger w-100 rounded-pill py-3 font-bold" style="background: var(--gradient-pink); border: none;">
                        <i class="fa-solid fa-bag-shopping me-2"></i>Xác Nhận Đặt Hàng
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     4. VIETQR PAYMENT MODAL (TỰ ĐỘNG SINH MÃ QR KÈM ĐẾM NGƯỢC)
     ========================================================================= -->
<div class="modal fade" id="modalVietQR" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-2xl">
            <div class="modal-header border-0 pb-0 text-center d-block position-relative" style="background: var(--primary-soft);">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                <div class="brand-icon-box mx-auto mb-2" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h5 class="font-bold mb-1" style="color: var(--primary-dark); font-family: var(--font-heading);">
                    Thanh Toán VietQR 24/7
                </h5>
                <p class="small text-muted mb-0">Quét mã bằng bất kỳ App Ngân hàng nào để hoàn tất</p>
            </div>

            <div class="modal-body p-4 text-center">
                <!-- Đồng hồ đếm ngược -->
                <div class="badge rounded-pill px-3 py-2 mb-2" style="background: #fff0f5; color: var(--primary-dark); font-weight: 700;">
                    <i class="fa-regular fa-clock me-1"></i>Mã thanh toán giữ hàng trong: <span id="vqr-countdown" class="text-danger">10:00</span>
                </div>

                <!-- Ảnh QR Code -->
                <div>
                    <img id="vqr-image" src="" alt="Mã VietQR" class="qr-code-img">
                </div>

                <!-- Chi tiết chuyển khoản & Nút Copy -->
                <div class="text-start mt-3">
                    <div class="copy-pill-box">
                        <div>
                            <span class="small text-muted d-block" style="font-size: 0.72rem;">NGÂN HÀNG THỤ HƯỞNG:</span>
                            <strong id="vqr-bank-name" class="small">MBBank</strong>
                        </div>
                    </div>

                    <div class="copy-pill-box">
                        <div>
                            <span class="small text-muted d-block" style="font-size: 0.72rem;">SỐ TÀI KHOẢN:</span>
                            <strong id="vqr-account-no" class="fs-6 font-bold" style="color: var(--primary-dark);"></strong>
                        </div>
                        <button type="button" class="btn-copy-code" onclick="AliceApp.copyText(document.getElementById('vqr-account-no').textContent, 'Số tài khoản')">
                            <i class="fa-regular fa-copy me-1"></i>Copy
                        </button>
                    </div>

                    <div class="copy-pill-box">
                        <div>
                            <span class="small text-muted d-block" style="font-size: 0.72rem;">CHỦ TÀI KHOẢN:</span>
                            <strong id="vqr-account-name" class="small">ALICE COSMETIC</strong>
                        </div>
                    </div>

                    <div class="copy-pill-box">
                        <div>
                            <span class="small text-muted d-block" style="font-size: 0.72rem;">SỐ TIỀN:</span>
                            <strong id="vqr-amount" class="fs-6 text-danger font-bold"></strong>
                        </div>
                    </div>

                    <div class="copy-pill-box" style="border: 2px dashed var(--primary); background: #fff0f5;">
                        <div>
                            <span class="small text-muted d-block" style="font-size: 0.72rem;">NỘI DUNG CHUYỂN KHOẢN (Bắt buộc giữ nguyên):</span>
                            <strong id="vqr-add-info" class="font-bold text-danger"></strong>
                        </div>
                        <button type="button" class="btn-copy-code" onclick="AliceApp.copyText(document.getElementById('vqr-add-info').textContent, 'Nội dung chuyển khoản')">
                            <i class="fa-regular fa-copy me-1"></i>Copy
                        </button>
                    </div>
                </div>

                <!-- Nút Tôi đã chuyển khoản -->
                <div class="mt-3">
                    <button type="button" class="btn btn-success w-100 rounded-pill py-2 font-bold mb-2 shadow-sm" onclick="AliceApp.confirmPaymentManual(document.getElementById('vqr-order-code').textContent)">
                        <i class="fa-solid fa-circle-check me-2"></i>Tôi Đã Chuyển Khoản Thành Công
                    </button>
                    <span class="small text-muted d-block">Mã đơn: <strong id="vqr-order-code"></strong></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     5. COD ORDER SUCCESS MODAL
     ========================================================================= -->
<div class="modal fade" id="modalCodSuccess" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 p-4 text-center shadow-lg">
            <div class="text-success fs-1 mb-2">
                <i class="fa-solid fa-circle-check text-pink-500" style="color: var(--primary);"></i>
            </div>
            <h4 class="font-bold mb-1" style="font-family: var(--font-heading); color: var(--text-main);">Đặt Hàng Thành Công!</h4>
            <p class="small text-muted mb-3">Cảm ơn bạn đã lựa chọn <strong>ALICE Cosmetic</strong>. Đơn hàng của bạn đang được chuyên viên kiểm tra và chuẩn bị đóng gói nơ hồng gửi đi.</p>
            <div class="p-3 rounded-3 mb-3" style="background: #fff8fa; border: 1px solid var(--primary-pastel);">
                <div class="small">Mã đơn hàng: <strong id="cod-order-code" class="text-danger"></strong></div>
                <div class="small mt-1">Tổng tiền thu COD: <strong id="cod-final-amount" class="text-danger fs-6"></strong></div>
            </div>
            <button type="button" class="btn btn-danger rounded-pill px-4 py-2 font-bold" style="background: var(--gradient-pink); border: none;" data-bs-dismiss="modal">
                Tiếp Tục Mua Sắm
            </button>
        </div>
    </div>
</div>

<!-- =========================================================================
     6. ORDER LOOKUP MODAL (TRA CỨU ĐƠN HÀNG)
     ========================================================================= -->
<div class="modal fade" id="modalOrderLookup" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0" style="background: var(--primary-soft);">
                <h5 class="modal-title font-bold" style="color: var(--primary-dark); font-family: var(--font-heading);">
                    <i class="fa-solid fa-truck-fast me-2"></i>Tra Cứu Đơn Hàng Của Bạn
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="input-group mb-3">
                    <input type="text" id="order-lookup-query" class="form-control rounded-start-pill" placeholder="Nhập Số điện thoại hoặc Mã đơn hàng...">
                    <button class="btn btn-danger rounded-end-pill px-3 font-bold" style="background: var(--gradient-pink); border: none;" onclick="lookupOrders()">
                        Tra Cứu
                    </button>
                </div>
                <div id="order-lookup-results">
                    <!-- JS render search results -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
async function lookupOrders() {
    const q = document.getElementById('order-lookup-query').value.trim();
    const resBox = document.getElementById('order-lookup-results');
    resBox.innerHTML = '<div class="text-center py-3"><i class="fa-solid fa-spinner fa-spin text-pink-500"></i> Đang tìm...</div>';

    try {
        const res = await fetch(`api/orders.php?q=${encodeURIComponent(q)}`);
        const data = await res.json();
        if (!data.orders || data.orders.length === 0) {
            resBox.innerHTML = '<div class="alert alert-light text-center small">Không tìm thấy đơn hàng nào khớp với thông tin trên.</div>';
            return;
        }

        resBox.innerHTML = data.orders.map(o => `
            <div class="p-3 mb-2 rounded-3" style="background: #fff8fa; border: 1px solid var(--primary-pastel);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="small text-danger">${o.order_code}</strong>
                    <span class="badge ${o.payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark'} rounded-pill" style="font-size: 0.7rem;">
                        ${o.payment_status === 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán'}
                    </span>
                </div>
                <div class="small text-muted">Khách hàng: <strong>${o.customer_name}</strong> - SĐT: ${o.phone}</div>
                <div class="small text-muted">Tổng tiền: <strong class="text-danger">${o.final_amount_formatted}</strong> (${o.payment_method === 'vietqr' ? 'VietQR' : 'COD'})</div>
                <div class="small text-muted" style="font-size: 0.72rem;">Ngày đặt: ${o.created_at}</div>
            </div>
        `).join('');
    } catch(e) {
        resBox.innerHTML = '<div class="alert alert-danger small">Lỗi kết nối tra cứu!</div>';
    }
}
</script>

<!-- =========================================================================
     7. MODAL AUTHENTICATION (ĐĂNG NHẬP & ĐĂNG KÝ THÀNH VIÊN)
     ========================================================================= -->
<div class="modal fade" id="modalAuth" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content rounded-4 border-0 shadow-2xl overflow-hidden">
            <!-- Modal Header Tone Hồng -->
            <div class="modal-header border-0 pb-2 text-center d-block position-relative" style="background: linear-gradient(135deg, #fff0f5 0%, #ffe4ec 100%);">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                <div class="brand-icon-box mx-auto mb-2" style="width: 48px; height: 48px; font-size: 1.4rem;">
                    <i class="fa-solid fa-spa"></i>
                </div>
                <h5 class="font-bold mb-1" style="color: var(--primary-dark); font-family: var(--font-heading); font-size: 1.35rem;">
                    ALICE Cosmetic
                </h5>
                <p class="small text-muted mb-0">Đăng nhập để nhận ưu đãi thành viên & voucher tích điểm</p>
            </div>

            <!-- Tabs: Đăng Nhập / Đăng Ký -->
            <div class="p-3 pb-0 bg-white">
                <ul class="nav nav-pills nav-fill rounded-pill p-1 border" id="authTabs" role="tablist" style="background: var(--primary-soft); border-color: var(--primary-border) !important;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill font-bold py-2 small" id="tab-login-btn" data-bs-toggle="pill" data-bs-target="#pane-login" type="button" role="tab">
                            <i class="fa-solid fa-right-to-bracket me-1"></i>Đăng Nhập
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill font-bold py-2 small" id="tab-register-btn" data-bs-toggle="pill" data-bs-target="#pane-register" type="button" role="tab">
                            <i class="fa-solid fa-user-plus me-1"></i>Đăng Ký
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4 pt-3">
                <div class="tab-content" id="authTabsContent">
                    <!-- TAB 1: FORM ĐĂNG NHẬP -->
                    <div class="tab-pane fade show active" id="pane-login" role="tabpanel">
                        <form id="formLogin" onsubmit="AliceApp.handleLogin(event)">
                            <div class="mb-3">
                                <label class="form-label small font-bold">Email hoặc Số điện thoại</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                                    <input type="text" name="identifier" class="form-control border-start-0 ps-0" placeholder="ngoclinh@gmail.com hoặc 0987..." required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label small font-bold">Mật khẩu</label>
                                    <a href="javascript:void(0)" class="small text-muted" onclick="AliceApp.showToast('Vui lòng liên hệ Hotline shop để cấp lại mật khẩu!', 'info')">Quên mật khẩu?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" name="password" id="login-pwd" class="form-control border-start-0 ps-0" placeholder="Nhập mật khẩu..." required>
                                    <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" type="button" onclick="const p = document.getElementById('login-pwd'); p.type = p.type === 'password' ? 'text' : 'password';">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tài khoản gợi ý sẵn tiện test -->
                            <div class="p-2 mb-3 rounded-3 small" style="background: var(--primary-soft); border: 1px dashed var(--primary-border);">
                                <span class="d-block font-semibold" style="color: var(--primary-dark); font-size: 0.76rem;">
                                    💡 <strong>Tài khoản mẫu:</strong> ngoclinh@gmail.com | <strong>MK:</strong> 123456
                                </span>
                            </div>

                            <button type="submit" id="btn-submit-login" class="btn btn-danger w-100 rounded-pill py-2 font-bold mb-3 shadow-sm" style="background: var(--gradient-pink); border: none;">
                                Đăng Nhập Ngay
                            </button>

                            <div class="text-center small text-muted mb-2">Hoặc đăng nhập nhanh bằng:</div>
                            <button type="button" class="btn btn-light w-100 rounded-pill py-2 border small font-bold d-flex align-items-center justify-content-center gap-2" onclick="AliceApp.quickGoogleLogin()">
                                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" width="18" height="18">
                                <span>Tiếp tục với Google</span>
                            </button>
                        </form>
                    </div>

                    <!-- TAB 2: FORM ĐĂNG KÝ -->
                    <div class="tab-pane fade" id="pane-register" role="tabpanel">
                        <form id="formRegister" onsubmit="AliceApp.handleRegister(event)">
                            <div class="mb-3">
                                <label class="form-label small font-bold">Họ và tên (*)</label>
                                <input type="text" name="fullname" class="form-control" placeholder="Ví dụ: Hoàng Thu Trang" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small font-bold">Địa chỉ Email (*)</label>
                                <input type="email" name="email" class="form-control" placeholder="thutrang@gmail.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small font-bold">Số điện thoại</label>
                                <input type="tel" name="phone" class="form-control" placeholder="0912345678">
                            </div>

                            <div class="mb-3">
                                <label class="form-label small font-bold">Mật khẩu (tối thiểu 6 ký tự) (*)</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" minlength="6" required>
                            </div>

                            <button type="submit" id="btn-submit-register" class="btn btn-danger w-100 rounded-pill py-2 font-bold shadow-sm" style="background: var(--gradient-pink); border: none;">
                                <i class="fa-solid fa-sparkles me-1"></i>Đăng Ký Thành Viên
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
