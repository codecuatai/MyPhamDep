<?php
/**
 * ROUTINE TABS & PRODUCT CATALOG - ALICE COSMETIC
 */
?>
<section id="san-pham-section" class="py-4">
    <div class="container">
        <!-- Section Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
            <div>
                <span class="badge rounded-pill px-3 py-1 font-bold" style="background: var(--primary-pastel); color: var(--primary-dark); font-size: 0.78rem;">
                    BỘ SƯU TẬP CHỌN LỌC
                </span>
                <h3 class="font-bold mt-1 mb-0" style="font-family: var(--font-heading); font-size: 1.8rem; color: var(--text-main);">
                    Tất Cả Sản Phẩm Chính Hãng
                </h3>
            </div>

            <!-- Skin Type Filter Tabs (Đặc thù mỹ phẩm) -->
            <div class="routine-nav-tabs">
                <button type="button" class="routine-tab-btn active" data-skin="all">✨ Tất Cả Da</button>
                <button type="button" class="routine-tab-btn" data-skin="oil">🌿 Da Dầu Mụn</button>
                <button type="button" class="routine-tab-btn" data-skin="dry">💧 Da Khô Thiếu Ẩm</button>
                <button type="button" class="routine-tab-btn" data-skin="sensitive">🌸 Da Nhạy Cảm</button>
            </div>
        </div>

        <!-- Product Cards Grid -->
        <div class="row row-cols-2 row-cols-md-4 g-3" id="main-product-grid">
            <?php foreach ($allProducts as $p): ?>
                <div class="col product-grid-item" data-category="<?= htmlspecialchars($p['category_id']) ?>" data-skin="<?= htmlspecialchars($p['skin_type']) ?>">
                    <div class="product-card">
                        <div class="product-thumb-wrap">
                            <span class="badge-mall-official">CHÍNH HÃNG</span>
                            <?php if ($p['discount_percent'] > 0): ?>
                                <span class="badge-discount-tag">-<?= $p['discount_percent'] ?>%</span>
                            <?php endif; ?>
                            <img src="<?= htmlspecialchars($p['image']) ?>" 
                                 alt="<?= htmlspecialchars($p['name']) ?>" 
                                 class="product-thumb-img" 
                                 onclick="AliceApp.openProductDetail(<?= $p['id'] ?>)" 
                                 style="cursor: pointer;"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80';">
                        </div>
                        <div class="product-info-wrap">
                            <div class="product-brand"><?= htmlspecialchars($p['brand']) ?></div>
                            <h4 class="product-name" onclick="AliceApp.openProductDetail(<?= $p['id'] ?>)" style="cursor: pointer;" title="<?= htmlspecialchars($p['name']) ?>">
                                <?= htmlspecialchars($p['name']) ?>
                            </h4>

                            <div class="product-rating-sold">
                                <span class="rating-stars">★ <?= $p['rating'] ?></span>
                                <span>Đã bán <?= number_format($p['sold_count']) ?></span>
                            </div>

                            <div class="product-price-row">
                                <span class="price-current"><?= formatVND($p['price']) ?></span>
                                <?php if ($p['original_price'] > $p['price']): ?>
                                    <span class="price-original"><?= formatVND($p['original_price']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="d-flex gap-2 mt-auto">
                                <button type="button" class="btn-add-cart-pink flex-grow-1" onclick="AliceApp.openProductDetail(<?= $p['id'] ?>)">
                                    <i class="fa-solid fa-eye me-1"></i>Xem Chi Tiết
                                </button>
                                <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: var(--gradient-pink); color: #fff; border: none;" onclick="AliceApp.quickAddToCart(<?= $p['id'] ?>, '<?= addslashes($p['name']) ?>', '<?= $p['image'] ?>', <?= $p['price'] ?>)" title="Thêm nhanh vào giỏ">
                                    <i class="fa-solid fa-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
