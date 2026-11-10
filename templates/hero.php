<?php
/**
 * HERO BANNER SLIDER & TRUST BADGES - ALICE COSMETIC (TONE HỒNG PASTEL)
 * Tự động đồng bộ với danh sách Banner quản lý trong Admin
 */
$banners = !empty($activeBanners) ? $activeBanners : [];
?>
<section class="hero-pink-section position-relative overflow-hidden py-4">
    <div class="container">
        <?php if (!empty($banners)): ?>
            <!-- BANNER CAROUSEL SLIDER DYNAMIC -->
            <div id="heroBannerCarousel" class="carousel slide carousel-fade mb-4" data-bs-ride="carousel" data-bs-interval="4500">
                <!-- Indicators -->
                <?php if (count($banners) > 1): ?>
                    <div class="carousel-indicators mb-0" style="bottom: -24px;">
                        <?php foreach ($banners as $idx => $b): ?>
                            <button type="button" data-bs-target="#heroBannerCarousel" data-bs-slide-to="<?= $idx ?>" class="<?= $idx === 0 ? 'active' : '' ?>" aria-label="Banner Slide <?= $idx + 1 ?>" style="width: 28px; height: 6px; border-radius: 4px; background-color: var(--primary);"></button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="carousel-inner rounded-4 shadow-sm" style="background: linear-gradient(135deg, #fff0f5 0%, #ffe4ec 100%); border: 1px solid #ffd6e0;">
                    <?php foreach ($banners as $idx => $b): ?>
                        <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?> p-3 p-md-5">
                            <div class="row align-items-center g-4">
                                <!-- Text Content -->
                                <div class="col-lg-7">
                                    <div class="hero-pink-card p-2 p-md-0">
                                        <div class="hero-badge-tag mb-3 d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill" style="background: #ffffff; color: var(--primary-dark); font-weight: 700; font-size: 0.8rem; box-shadow: 0 2px 8px rgba(255, 105, 145, 0.15); border: 1px solid #ffd6e0;">
                                            <i class="fa-solid fa-crown text-warning"></i>
                                            <span><?= htmlspecialchars($b['badge_text'] ?: 'ƯU ĐÃI ĐẶC BIỆT') ?></span>
                                        </div>
                                        <?php if ($idx === 0): ?>
                                            <h1 class="hero-title fw-bold" style="font-family: 'Playfair Display', serif; color: #2d3748; font-size: calc(1.5rem + 1.2vw); line-height: 1.25;">
                                                <?= htmlspecialchars($b['title']) ?>
                                            </h1>
                                        <?php else: ?>
                                            <h2 class="hero-title fw-bold" style="font-family: 'Playfair Display', serif; color: #2d3748; font-size: calc(1.5rem + 1.2vw); line-height: 1.25;">
                                                <?= htmlspecialchars($b['title']) ?>
                                            </h2>
                                        <?php endif; ?>
                                        <p class="hero-desc mt-3 text-muted" style="font-size: 1rem; line-height: 1.6; max-width: 580px;">
                                            <?= htmlspecialchars($b['subtitle'] ?: 'Mỹ phẩm chính hãng cao cấp, chăm sóc da khoa học và phong cách makeup ngọt ngào cùng ALICE Cosmetic.') ?>
                                        </p>
                                        <div class="d-flex flex-wrap gap-3 align-items-center mt-4">
                                            <a href="<?= htmlspecialchars($b['link_url'] ?: '#san-pham-section') ?>" class="btn-hero-cta text-decoration-none">
                                                <i class="fa-solid fa-sparkles text-warning me-1"></i>
                                                <span><?= htmlspecialchars($b['button_text'] ?: 'Khám Phá Ngay') ?></span>
                                            </a>
                                            <a href="#san-pham-section" class="btn btn-outline-danger rounded-pill px-4 py-2 font-bold" style="border-color: var(--primary); color: var(--primary-dark); background: #ffffff;">
                                                <span>Xem Toàn Bộ Mỹ Phẩm</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Visual Image / Promo Cards -->
                                <div class="col-lg-5">
                                    <div class="position-relative text-center">
                                        <img src="<?= htmlspecialchars($b['image_url']) ?>" alt="<?= htmlspecialchars($b['title']) ?>" class="img-fluid rounded-4 shadow-lg border border-3 border-white w-100" style="max-height: 380px; object-fit: cover;">
                                        <div class="position-absolute bottom-0 start-0 m-3 p-3 bg-white rounded-3 shadow-sm border text-start" style="max-width: 240px; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px); border-color: #ffd6e0 !important;">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-danger rounded-pill">CAM KẾT</span>
                                                <span class="small font-bold" style="color: var(--primary-dark);">100% Chính Hãng</span>
                                            </div>
                                            <p class="small text-muted mb-0" style="font-size: 0.76rem;">Tặng hộp quà Pink Box nơ hồng pastel & thiệp viết tay</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Controls -->
                <?php if (count($banners) > 1): ?>
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="prev" style="width: 42px; height: 42px; top: 50%; transform: translateY(-50%); left: 10px; background: rgba(255, 255, 255, 0.9); border-radius: 50%; border: 1px solid #ffd6e0; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                        <i class="fa-solid fa-chevron-left" style="color: var(--primary); font-size: 1rem;"></i>
                        <span class="visually-hidden">Trước</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="next" style="width: 42px; height: 42px; top: 50%; transform: translateY(-50%); right: 10px; background: rgba(255, 255, 255, 0.9); border-radius: 50%; border: 1px solid #ffd6e0; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                        <i class="fa-solid fa-chevron-right" style="color: var(--primary); font-size: 1rem;"></i>
                        <span class="visually-hidden">Sau</span>
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- BANNER DEFAULT NẾU CHƯA CÓ BANNER -->
            <div class="hero-pink-card p-4 rounded-4" style="background: linear-gradient(135deg, #fff0f5 0%, #ffe4ec 100%);">
                <h1 class="hero-title">Vẻ Đẹp Rạng Ngời Cùng ALICE Cosmetic</h1>
                <p class="hero-desc">Mỹ phẩm chính hãng 100%, bảo hành kích ứng 7 ngày và đóng gói hộp quà nơ hồng pastel yêu thương.</p>
            </div>
        <?php endif; ?>

        <!-- Trust Badges Bar (Mộc Miên Style) -->
        <div class="trust-badges-bar mt-4 pt-2">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="fa-solid fa-shield-check"></i></div>
                        <div class="trust-text">
                            <h6>100% Chính Hãng</h6>
                            <p>Đầy đủ tem phụ & hóa đơn VAT</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="fa-solid fa-truck-fast"></i></div>
                        <div class="trust-text">
                            <h6>Freeship Toàn Quốc</h6>
                            <p>Đơn hàng từ 299.000đ</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="fa-solid fa-gift"></i></div>
                        <div class="trust-text">
                            <h6>Tặng Quà & Thiệp Tay</h6>
                            <p>Đóng gói nơ hồng pastel xinh xắn</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="trust-item">
                        <div class="trust-icon"><i class="fa-solid fa-rotate-left"></i></div>
                        <div class="trust-text">
                            <h6>Đổi Trả 7 Ngày</h6>
                            <p>Miễn phí đổi hàng nếu kích ứng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
