<?php
/**
 * HERO BANNER SLIDER & TRUST BADGES - ALICE COSMETIC (LUXURY ROSE PASTEL)
 * Tự động xoay vòng 5s (5000ms) liên tục, thiết kế đồng bộ chuẩn Flagship Beauty Store
 */
$banners = !empty($activeBanners) ? $activeBanners : [];
?>
<section class="hero-luxe-section position-relative py-3 py-lg-4">
    <div class="container position-relative" style="z-index: 2;">
        <?php if (!empty($banners)): ?>
            <!-- BANNER CAROUSEL SLIDER DYNAMIC -->
            <div id="heroBannerCarousel" 
                 class="carousel slide carousel-fade mb-4 position-relative" 
                 data-bs-ride="carousel" 
                 data-bs-interval="5000" 
                 data-bs-pause="false"
                 data-bs-wrap="true">
                 
                <div class="carousel-inner rounded-4 rounded-lg-5 overflow-hidden shadow-sm" style="border: 1px solid rgba(255, 192, 203, 0.45);">
                    <?php foreach ($banners as $idx => $b): ?>
                        <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                            <div class="hero-slide-stage position-relative overflow-hidden">
                                <!-- Background Ambient Glows -->
                                <div class="hero-ambient-glow glow-pink position-absolute top-0 end-0"></div>
                                <div class="hero-ambient-glow glow-gold position-absolute bottom-0 start-0"></div>

                                <div class="container-fluid px-3 px-sm-4 px-lg-5 py-4 py-lg-5 position-relative" style="z-index: 2;">
                                    <div class="row align-items-center g-4 g-lg-5">
                                        <!-- Left Column: Editorial Content & CTAs -->
                                        <div class="col-12 col-lg-7">
                                            <div class="hero-editorial-wrap pe-lg-3">
                                                <!-- Eyebrow Pill Tag -->
                                                <div class="hero-eyebrow-chip d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3">
                                                    <i class="fa-solid fa-sparkles text-danger"></i>
                                                    <span><?= htmlspecialchars($b['badge_text'] ?: 'ALICE LUXE COLLECTION') ?></span>
                                                </div>

                                                <!-- Headline -->
                                                <?php if ($idx === 0): ?>
                                                    <h1 class="hero-luxe-title fw-bold mb-3">
                                                        <?= htmlspecialchars($b['title']) ?>
                                                    </h1>
                                                <?php else: ?>
                                                    <h2 class="hero-luxe-title fw-bold mb-3">
                                                        <?= htmlspecialchars($b['title']) ?>
                                                    </h2>
                                                <?php endif; ?>

                                                <!-- Subtitle -->
                                                <p class="hero-luxe-desc mb-4 text-secondary">
                                                    <?= htmlspecialchars($b['subtitle'] ?: 'Mỹ phẩm chính hãng cao cấp, nâng niu làn da thuần khiết và tỏa sáng tự tin cùng ALICE Cosmetic.') ?>
                                                </p>

                                                <!-- Action Buttons -->
                                                <div class="d-flex flex-wrap gap-3 align-items-center mb-4">
                                                    <a href="<?= htmlspecialchars($b['link_url'] ?: '#san-pham-section') ?>" class="btn-luxe-primary">
                                                        <span><?= htmlspecialchars($b['button_text'] ?: 'Khám Phá Ngay') ?></span>
                                                        <i class="fa-solid fa-arrow-right ms-2"></i>
                                                    </a>
                                                    <a href="#san-pham-section" class="btn-luxe-secondary">
                                                        <span>Xem Toàn Bộ Mỹ Phẩm</span>
                                                    </a>
                                                </div>

                                                <!-- Micro Trust Perks -->
                                                <div class="hero-perks-row d-flex flex-wrap align-items-center gap-3 pt-2">
                                                    <div class="hero-perk-item">
                                                        <i class="fa-solid fa-circle-check text-success"></i>
                                                        <span>100% Chính Hãng</span>
                                                    </div>
                                                    <div class="hero-perk-item">
                                                        <i class="fa-solid fa-truck-fast text-danger"></i>
                                                        <span>Freeship từ 299K</span>
                                                    </div>
                                                    <div class="hero-perk-item">
                                                        <i class="fa-solid fa-gift" style="color: var(--primary);"></i>
                                                        <span>Tặng Hộp Nơ Hồng Pastel</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Right Column: Visual Product Showcase -->
                                        <div class="col-12 col-lg-5">
                                            <div class="hero-visual-frame position-relative">
                                                <!-- Radiant Aura -->
                                                <div class="hero-halo-aura"></div>
                                                
                                                <!-- Image Card -->
                                                <div class="hero-image-wrap">
                                                    <img src="<?= htmlspecialchars($b['image_url']) ?>" 
                                                         alt="<?= htmlspecialchars($b['title']) ?>" 
                                                         class="hero-img-showcase" 
                                                         loading="<?= $idx === 0 ? 'eager' : 'lazy' ?>">
                                                </div>

                                                <!-- Floating Glassmorphism Tag -->
                                                <div class="hero-floating-glass-tag">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="hero-tag-icon">
                                                            <i class="fa-solid fa-award"></i>
                                                        </div>
                                                        <div>
                                                            <div class="hero-tag-title">Cam Kết 100% Chính Hãng</div>
                                                            <div class="hero-tag-desc">Đóng hộp Pink Box nơ hồng & thiệp tay</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigation Controls -->
                <?php if (count($banners) > 1): ?>
                    <button class="carousel-control-prev hero-nav-btn prev-btn" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="prev" aria-label="Slide trước">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="carousel-control-next hero-nav-btn next-btn" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="next" aria-label="Slide tiếp theo">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <!-- Indicators with Expanding Smooth Active Pill -->
                    <div class="carousel-indicators hero-indicators-wrap">
                        <?php foreach ($banners as $idx => $b): ?>
                            <button type="button" 
                                    data-bs-target="#heroBannerCarousel" 
                                    data-bs-slide-to="<?= $idx ?>" 
                                    class="hero-indicator-dot <?= $idx === 0 ? 'active' : '' ?>" 
                                    aria-current="<?= $idx === 0 ? 'true' : 'false' ?>" 
                                    aria-label="Chuyển đến banner <?= $idx + 1 ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- BANNER DEFAULT NẾU CHƯA CÓ BANNER -->
            <div class="hero-slide-stage p-4 rounded-4" style="background: linear-gradient(135deg, #fff0f5 0%, #ffe4ec 100%);">
                <h1 class="hero-luxe-title">Vẻ Đẹp Rạng Ngời Cùng ALICE Cosmetic</h1>
                <p class="hero-luxe-desc">Mỹ phẩm chính hãng 100%, bảo hành kích ứng 7 ngày và đóng gói hộp quà nơ hồng pastel yêu thương.</p>
            </div>
        <?php endif; ?>

        <!-- Trust Badges Bar (Luxe Style) -->
        <div class="trust-badges-bar mt-3 pt-2">
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
