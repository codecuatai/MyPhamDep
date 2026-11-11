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
                <h2 class="font-bold mt-1 mb-0" style="font-family: var(--font-heading); font-size: 1.8rem; color: var(--text-main);">
                    Tất Cả Sản Phẩm Chính Hãng
                </h2>
            </div>

            <!-- Skin Type Filter Tabs (Đặc thù mỹ phẩm) -->
            <div class="routine-nav-tabs">
                <button type="button" class="routine-tab-btn active" data-skin="all">✨ Tất Cả Da</button>
                <button type="button" class="routine-tab-btn" data-skin="oil">🌿 Da Dầu Mụn</button>
                <button type="button" class="routine-tab-btn" data-skin="dry">💧 Da Khô Thiếu Ẩm</button>
                <button type="button" class="routine-tab-btn" data-skin="sensitive">🌸 Da Nhạy Cảm</button>
            </div>
        </div>

        <!-- Category Filter Status Bar (Hiển thị trực quan khi bấm danh mục tròn hoặc tìm kiếm) -->
        <div id="category-filter-status" class="alert alert-light border rounded-pill py-2 px-3 px-md-4 mb-3 d-none align-items-center justify-content-between shadow-sm" style="background: linear-gradient(90deg, #fff5f8 0%, #ffffff 100%); border-color: #ffd1dc !important;">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="badge rounded-pill bg-danger text-white px-2 py-1"><i class="fa-solid fa-filter me-1"></i>Đang xem</span>
                <span class="text-dark small"><span id="filter-label-prefix">Danh mục:</span> <strong id="filter-cat-name" class="text-danger fw-bold fs-6"></strong> (<span id="filter-cat-count" class="fw-bold">0</span> sản phẩm)</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 font-bold" onclick="AliceApp.resetCategoryFilter()">
                <i class="fa-solid fa-xmark me-1"></i>Xem Tất Cả Mỹ Phẩm
            </button>
        </div>

        <!-- Empty state when no products match -->
        <div id="category-empty-state" class="text-center py-5 d-none">
            <div class="mb-3 text-muted"><i class="fa-solid fa-box-open fa-3x"></i></div>
            <h5 id="category-empty-title" class="text-muted">Chưa có sản phẩm nào thuộc danh mục này</h5>
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-4 mt-2" style="background: var(--gradient-pink); border: none;" onclick="AliceApp.resetCategoryFilter()">
                Xem tất cả sản phẩm
            </button>
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
                            <?php 
                            $stock = (int)($p['stock_quantity'] ?? 50);
                            if ($stock <= 0): ?>
                                <span class="badge bg-secondary position-absolute bottom-0 start-0 m-2 z-2"><i class="fa-solid fa-ban me-1"></i>Hết hàng</span>
                            <?php elseif ($stock <= 10): ?>
                                <span class="badge bg-warning text-dark position-absolute bottom-0 start-0 m-2 z-2"><i class="fa-solid fa-fire me-1"></i>Còn <?= $stock ?> sp</span>
                            <?php endif; ?>
                            <a href="<?= $baseUrl ?>/san-pham/<?= htmlspecialchars($p['slug']) ?>" class="d-block w-100 h-100" title="<?= htmlspecialchars($p['name']) ?>">
                                <img src="<?= htmlspecialchars($p['image']) ?>" 
                                     alt="<?= htmlspecialchars($p['name']) ?>" 
                                     class="product-thumb-img" 
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80';">
                            </a>
                        </div>
                        <div class="product-info-wrap">
                            <div class="product-brand"><?= htmlspecialchars($p['brand']) ?></div>
                            <h3 class="product-name" title="<?= htmlspecialchars($p['name']) ?>">
                                <a href="<?= $baseUrl ?>/san-pham/<?= htmlspecialchars($p['slug']) ?>" class="text-decoration-none text-dark d-block">
                                    <?= htmlspecialchars($p['name']) ?>
                                </a>
                            </h3>

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
                                <a href="<?= $baseUrl ?>/san-pham/<?= htmlspecialchars($p['slug']) ?>" class="btn-add-cart-pink flex-grow-1 text-center text-decoration-none d-flex align-items-center justify-content-center">
                                    Chi Tiết
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" onclick="AliceApp.openProductDetail(<?= $p['id'] ?>)" title="Xem nhanh sản phẩm">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <?php if ($stock > 0): ?>
                                    <button type="button" class="btn btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: var(--gradient-pink); color: #fff; border: none;" onclick="AliceApp.quickAddToCart(<?= (int)$p['id'] ?>, <?= htmlspecialchars(json_encode($p['name'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($p['image']), ENT_QUOTES, 'UTF-8') ?>, <?= (float)$p['price'] ?>)" title="Thêm nhanh vào giỏ">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn btn-sm btn-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; opacity: 0.55; cursor: not-allowed;" disabled title="Sản phẩm tạm hết hàng">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
