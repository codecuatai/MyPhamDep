<?php
/**
 * FOOTER COMPONENT - ALICE COSMETIC (BO CONG PASTEL MỘC MIÊN STYLE)
 */
?>
<footer class="site-footer">
    <div class="container">
        <div class="row g-4 mb-4">
            <!-- Cột 1: Thông tin thương hiệu -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="brand-icon-box" style="width: 38px; height: 38px; font-size: 1.1rem;">
                        <i class="fa-solid fa-spa"></i>
                    </div>
                    <div class="brand-name" style="font-size: 1.4rem;">ALICE Cosmetic</div>
                </div>
                <p class="small text-muted mb-3" style="line-height: 1.6;">
                    <?= htmlspecialchars($shopConfig['slogan']) ?>
                </p>
                <div class="small text-muted">
                    <div class="mb-2"><i class="fa-solid fa-location-dot me-2" style="color: var(--primary);"></i><?= htmlspecialchars($shopConfig['address']) ?></div>
                    <div class="mb-2"><i class="fa-solid fa-phone-volume me-2" style="color: var(--primary);"></i>Hotline: <strong><?= htmlspecialchars($shopConfig['hotline']) ?></strong> (8:00 - 22:00)</div>
                    <div class="mb-2"><i class="fa-solid fa-envelope me-2" style="color: var(--primary);"></i>Email: <?= htmlspecialchars($shopConfig['email']) ?></div>
                </div>
            </div>

            <!-- Cột 2: Hỗ trợ khách hàng -->
            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="font-bold mb-3" style="color: var(--primary-dark);">HỖ TRỢ KHÁCH HÀNG</h6>
                <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
                    <li><a href="javascript:void(0)" onclick="AliceApp.openCartDrawer()">Hướng dẫn mua hàng</a></li>
                    <li><a href="javascript:void(0)">Chính sách đổi trả 7 ngày</a></li>
                    <li><a href="javascript:void(0)">Chính sách bảo hành & bồi hoàn</a></li>
                    <li><a href="javascript:void(0)">Tra cứu hành trình đơn hàng</a></li>
                    <li><a href="javascript:void(0)">Tư vấn soi da miễn phí</a></li>
                </ul>
            </div>

            <!-- Cột 3: Về ALICE Cosmetic -->
            <div class="col-lg-3 col-md-6 col-6">
                <h6 class="font-bold mb-3" style="color: var(--primary-dark);">VỀ CHÚNG TÔI</h6>
                <ul class="list-unstyled small text-muted d-flex flex-column gap-2 mb-0">
                    <li><a href="javascript:void(0)">Câu chuyện thương hiệu ALICE</a></li>
                    <li><a href="javascript:void(0)">Cam kết hàng chính hãng 100%</a></li>
                    <li><a href="javascript:void(0)">Dịch vụ đóng hộp quà & viết thiệp</a></li>
                    <li><a href="javascript:void(0)">Tuyển dụng & Đối tác nhượng quyền</a></li>
                    <li><a href="javascript:void(0)">Hệ thống cửa hàng phân phối</a></li>
                </ul>
            </div>

            <!-- Cột 4: Phương thức thanh toán & Kênh MXH -->
            <div class="col-lg-3 col-md-6">
                <h6 class="font-bold mb-3" style="color: var(--primary-dark);">PHƯƠNG THỨC THANH TOÁN</h6>
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge bg-white text-dark border p-2 rounded-3 small font-semibold"><i class="fa-solid fa-qrcode text-danger me-1"></i>VietQR 24/7</span>
                    <span class="badge bg-white text-dark border p-2 rounded-3 small font-semibold"><i class="fa-solid fa-truck-ramp-box text-success me-1"></i>COD Tận Nhà</span>
                    <span class="badge bg-white text-dark border p-2 rounded-3 small font-semibold"><i class="fa-solid fa-building-columns text-primary me-1"></i>MBBank</span>
                    <span class="badge bg-white text-dark border p-2 rounded-3 small font-semibold"><i class="fa-solid fa-credit-card text-warning me-1"></i>Napas</span>
                </div>

                <h6 class="font-bold mb-2 mt-4" style="color: var(--primary-dark); font-size: 0.85rem;">KẾT NỐI VỚI ALICE</h6>
                <div class="d-flex gap-2">
                    <a href="<?= htmlspecialchars($shopConfig['facebook_url']) ?>" target="_blank" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #1877f2; color: #fff;"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="<?= htmlspecialchars($shopConfig['tiktok_url']) ?>" target="_blank" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #000; color: #fff;"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="<?= htmlspecialchars($shopConfig['instagram_url']) ?>" target="_blank" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #e1306c; color: #fff;"><i class="fa-brands fa-instagram"></i></a>
                    <a href="<?= htmlspecialchars($shopConfig['zalo_url']) ?>" target="_blank" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: #0068ff; color: #fff;"><i class="fa-solid fa-comment-dots"></i></a>
                </div>
            </div>
        </div>

        <hr style="border-color: var(--primary-border);">

        <div class="row align-items-center small text-muted pt-2">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                © <?= date('Y') ?> <strong><?= htmlspecialchars($shopConfig['shop_name']) ?></strong>. Giữ toàn quyền sở hữu thương hiệu.
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span>Hệ thống bán hàng mỹ phẩm tự động hóa & Thanh toán VietQR</span>
            </div>
        </div>
    </div>
</footer>
