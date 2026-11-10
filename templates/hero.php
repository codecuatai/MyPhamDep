<?php
/**
 * HERO BANNER SLIDER & TRUST BADGES - ALICE COSMETIC (LUXURY ROSE PASTEL)
 * Tự động xoay vòng 5s (5000ms) liên tục, chiều cao cố định đồng bộ & hình ảnh giới hạn kích thước gọn gàng
 */
$banners = !empty($activeBanners) ? $activeBanners : [];
?>
<section class="hero-luxe-section position-relative py-3 py-lg-4">
    <style>
    /* KHÓA CHIỀU CAO BANNER ĐỒNG NHẤT 100% (KHÔNG BỊ NHẢY CHIỀU CAO) */
    #heroBannerCarousel .carousel-inner {
        height: 340px;
        max-height: 340px;
        border-radius: 26px;
        overflow: hidden;
        background: linear-gradient(135deg, #ffffff 0%, #fff5f8 50%, #ffe9f2 100%);
        border: 1px solid rgba(255, 192, 203, 0.45);
        position: relative;
    }
    #heroBannerCarousel .carousel-item {
        height: 340px;
        max-height: 340px;
    }
    #heroBannerCarousel .hero-slide-stage {
        height: 340px;
        max-height: 340px;
        overflow: hidden;
        display: flex;
        align-items: center;
        position: relative;
    }

    /* GIỚI HẠN KÍCH THƯỚC HÌNH ẢNH GỌN GÀNG, KHÔNG BỊ TRÀN HAY QUÁ TO */
    #heroBannerCarousel .hero-visual-frame {
        position: relative;
        padding: 6px;
        max-width: 320px;
        margin: 0 auto;
    }
    #heroBannerCarousel .hero-image-wrap {
        max-width: 310px;
        width: 100%;
        height: 250px;
        max-height: 250px;
        margin: 0 auto;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 14px 28px -8px rgba(255, 77, 141, 0.22), 0 0 0 3px #ffffff;
        background: #ffffff;
        position: relative;
        z-index: 2;
    }
    #heroBannerCarousel .hero-img-showcase {
        width: 100%;
        height: 250px;
        max-height: 250px;
        object-fit: cover;
        object-position: center;
        display: block;
        transition: transform 0.4s ease;
    }
    #heroBannerCarousel .hero-image-wrap:hover .hero-img-showcase {
        transform: scale(1.03);
    }
    #heroBannerCarousel .hero-halo-aura {
        position: absolute;
        inset: -10px;
        background: radial-gradient(circle, rgba(255, 160, 193, 0.35) 0%, rgba(255, 255, 255, 0) 70%);
        filter: blur(20px);
        z-index: 1;
        pointer-events: none;
    }
    #heroBannerCarousel .hero-floating-glass-tag {
        position: absolute;
        bottom: -6px;
        left: 12px;
        z-index: 3;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 204, 218, 0.8);
        border-radius: 14px;
        padding: 5px 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    /* TYPOGRAPHY & BUTTONS */
    #heroBannerCarousel .hero-eyebrow-chip {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(255, 182, 193, 0.6);
        color: var(--primary-dark);
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        box-shadow: 0 3px 10px rgba(255, 77, 141, 0.1);
    }
    #heroBannerCarousel .hero-luxe-title {
        font-family: 'Playfair Display', Georgia, serif;
        font-size: clamp(1.45rem, 2.3vw, 1.95rem);
        font-weight: 700;
        color: #1f1b24;
        line-height: 1.25;
        margin-bottom: 8px;
    }
    #heroBannerCarousel .hero-luxe-desc {
        font-size: 0.88rem;
        line-height: 1.5;
        color: #5c5563 !important;
        max-width: 500px;
        margin-bottom: 14px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    #heroBannerCarousel .btn-luxe-primary {
        background: linear-gradient(135deg, #ff4081 0%, #ff6b8b 100%);
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 9px 22px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        box-shadow: 0 8px 20px rgba(255, 64, 129, 0.3);
        transition: all 0.25s ease;
        border: none;
    }
    #heroBannerCarousel .btn-luxe-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(255, 64, 129, 0.4);
        color: #ffffff !important;
    }
    #heroBannerCarousel .btn-luxe-secondary {
        background: rgba(255, 255, 255, 0.9);
        border: 1.5px solid rgba(255, 182, 193, 0.7);
        color: #e62564 !important;
        font-weight: 700;
        font-size: 0.88rem;
        padding: 8px 20px;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.25s ease;
    }
    #heroBannerCarousel .btn-luxe-secondary:hover {
        background: #ffffff;
        border-color: #ff4081;
        color: #ff4081 !important;
        transform: translateY(-2px);
    }
    #heroBannerCarousel .hero-perks-row {
        gap: 16px;
    }
    #heroBannerCarousel .hero-perk-item {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.76rem;
        font-weight: 600;
        color: #635b6b;
    }

    /* ĐIỀU HƯỚNG MŨI TÊN & CHẤM TRÒN */
    #heroBannerCarousel .hero-nav-btn {
        width: 42px;
        height: 42px;
        background: rgba(255, 255, 255, 0.92) !important;
        border-radius: 50% !important;
        border: 1px solid rgba(255, 182, 193, 0.6) !important;
        box-shadow: 0 6px 18px rgba(255, 77, 141, 0.18) !important;
        color: var(--primary) !important;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0.85;
        transition: all 0.3s ease !important;
        z-index: 10;
    }
    #heroBannerCarousel .hero-nav-btn:hover {
        background: var(--primary) !important;
        color: #ffffff !important;
        opacity: 1;
        transform: translateY(-50%) scale(1.08);
    }
    #heroBannerCarousel .hero-indicators-wrap {
        bottom: -28px !important;
        margin-bottom: 0;
        z-index: 10;
        gap: 8px;
    }
    #heroBannerCarousel .hero-indicator-dot {
        width: 9px !important;
        height: 9px !important;
        border-radius: 50% !important;
        background: rgba(255, 142, 180, 0.45) !important;
        border: none !important;
        padding: 0;
        transition: all 0.4s ease !important;
        opacity: 0.85 !important;
    }
    #heroBannerCarousel .hero-indicator-dot.active {
        width: 32px !important;
        border-radius: 999px !important;
        background: linear-gradient(90deg, #ff4d8d, #ff758c) !important;
        opacity: 1 !important;
        box-shadow: 0 2px 8px rgba(255, 77, 141, 0.4);
    }

    /* RESPONSIVE TRÊN DI ĐỘNG */
    @media (max-width: 991.98px) {
        #heroBannerCarousel .carousel-inner,
        #heroBannerCarousel .carousel-item,
        #heroBannerCarousel .hero-slide-stage {
            height: auto !important;
            max-height: none !important;
            min-height: 300px;
        }
        #heroBannerCarousel .hero-image-wrap {
            max-width: 240px;
            height: 180px;
            max-height: 180px;
            margin-top: 10px;
        }
        #heroBannerCarousel .hero-img-showcase {
            height: 180px;
            max-height: 180px;
        }
        #heroBannerCarousel .hero-indicators-wrap {
            bottom: -24px !important;
        }
    }
    </style>

    <div class="container position-relative" style="z-index: 2;">
        <?php if (!empty($banners)): ?>
            <!-- BANNER CAROUSEL SLIDER DYNAMIC -->
            <div id="heroBannerCarousel" 
                 class="carousel slide carousel-fade mb-4 position-relative" 
                 data-bs-ride="carousel" 
                 data-bs-interval="5000" 
                 data-bs-pause="false"
                 data-bs-wrap="true">
                 
                <div class="carousel-inner shadow-sm">
                    <?php foreach ($banners as $idx => $b): ?>
                        <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                            <div class="hero-slide-stage position-relative overflow-hidden">
                                <!-- Background Ambient Glows -->
                                <div class="hero-ambient-glow glow-pink position-absolute top-0 end-0"></div>
                                <div class="hero-ambient-glow glow-gold position-absolute bottom-0 start-0"></div>

                                <div class="container-fluid px-3 px-sm-4 px-lg-5 py-3 py-lg-4 position-relative" style="z-index: 2;">
                                    <div class="row align-items-center g-3 g-lg-4">
                                        <!-- CỘT TRÁI: TIÊU ĐỀ, MÔ TẢ & NÚT BẤM -->
                                        <div class="col-12 col-md-7 col-lg-7 col-xl-8">
                                            <div class="hero-editorial-wrap pe-lg-3">
                                                <!-- Eyebrow Pill Tag -->
                                                <div class="hero-eyebrow-chip d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2">
                                                    <i class="fa-solid fa-sparkles text-danger"></i>
                                                    <span><?= htmlspecialchars($b['badge_text'] ?: 'ALICE LUXE COLLECTION') ?></span>
                                                </div>

                                                <!-- Headline -->
                                                <?php if ($idx === 0): ?>
                                                    <h1 class="hero-luxe-title fw-bold mb-2">
                                                        <?= htmlspecialchars($b['title']) ?>
                                                    </h1>
                                                <?php else: ?>
                                                    <h2 class="hero-luxe-title fw-bold mb-2">
                                                        <?= htmlspecialchars($b['title']) ?>
                                                    </h2>
                                                <?php endif; ?>

                                                <!-- Subtitle -->
                                                <p class="hero-luxe-desc mb-3">
                                                    <?= htmlspecialchars($b['subtitle'] ?: 'Mỹ phẩm chính hãng cao cấp, nâng niu làn da thuần khiết và tỏa sáng tự tin cùng ALICE Cosmetic.') ?>
                                                </p>

                                                <!-- Action Buttons -->
                                                <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                                    <a href="<?= htmlspecialchars($b['link_url'] ?: '#san-pham-section') ?>" class="btn-luxe-primary">
                                                        <span><?= htmlspecialchars($b['button_text'] ?: 'Khám Phá Ngay') ?></span>
                                                        <i class="fa-solid fa-arrow-right ms-2"></i>
                                                    </a>
                                                    <a href="#san-pham-section" class="btn-luxe-secondary">
                                                        <span>Xem Toàn Bộ Mỹ Phẩm</span>
                                                    </a>
                                                </div>

                                                <!-- Micro Trust Perks -->
                                                <div class="hero-perks-row d-flex flex-wrap align-items-center pt-1">
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

                                        <!-- CỘT PHẢI: HÌNH ẢNH MỸ PHẨM GỌN GÀNG, CỐ ĐỊNH KÍCH THƯỚC -->
                                        <div class="col-12 col-md-5 col-lg-5 col-xl-4 text-center">
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
                                                        <i class="fa-solid fa-award text-danger" style="font-size: 0.85rem;"></i>
                                                        <span class="fw-bold text-dark" style="font-size: 0.72rem;">Cam Kết 100% Chính Hãng</span>
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
                    <button class="carousel-control-prev hero-nav-btn prev-btn" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="prev" aria-label="Slide trước" style="left: 12px;">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="carousel-control-next hero-nav-btn next-btn" type="button" data-bs-target="#heroBannerCarousel" data-bs-slide="next" aria-label="Slide tiếp theo" style="right: 12px;">
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
            <div class="hero-slide-stage p-4 rounded-4" style="background: linear-gradient(135deg, #fff0f5 0%, #ffe4ec 100%); height: 340px;">
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
