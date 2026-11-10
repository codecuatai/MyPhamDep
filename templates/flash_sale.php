<?php
/**
 * FLASH SALE COMPONENT - ALICE COSMETIC (CHUẨN SHOPEE FLASH SALE TONE HỒNG)
 */
?>
<section id="flash-sale-section" class="section-flash-sale">
    <div class="container">
        <!-- Flash Sale Header -->
        <div class="flash-sale-header">
            <div class="flash-sale-title">
                <span class="flame-icon fs-3">🔥</span>
                <h3>FLASH SALE GIỜ VÀNG</h3>
                <div class="flash-sale-timer ms-md-3">
                    <span class="timer-box" id="fs-hours">02</span>
                    <span class="text-danger font-bold">:</span>
                    <span class="timer-box" id="fs-minutes">45</span>
                    <span class="text-danger font-bold">:</span>
                    <span class="timer-box" id="fs-seconds">18</span>
                </div>
            </div>
            <div>
                <a href="#san-pham-section" class="small font-bold" style="color: var(--primary-dark);">
                    Xem tất cả deal sốc <i class="fa-solid fa-angle-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Flash Sale Grid -->
        <div class="row row-cols-2 row-cols-md-4 g-3">
            <?php foreach ($flashSaleProducts as $p): ?>
                <?php 
                    $percentSold = ($p['flash_sale_stock'] > 0) ? min(100, round(($p['flash_sale_sold'] / $p['flash_sale_stock']) * 100)) : 80;
                ?>
                <div class="col">
                    <div class="product-card">
                        <div class="product-thumb-wrap">
                            <span class="badge-mall-official">MALL CHÍNH HÃNG</span>
                            <span class="badge-discount-tag">-<?= $p['discount_percent'] ?>%</span>
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
                                <span class="price-original"><?= formatVND($p['original_price']) ?></span>
                            </div>

                            <!-- Shopee Sold Progress Bar -->
                            <div class="sold-progress-box">
                                <div class="sold-progress-fill" style="width: <?= $percentSold ?>%;"></div>
                                <div class="sold-progress-text">
                                    <i class="fa-solid fa-fire me-1"></i>ĐÃ BÁN <?= $p['flash_sale_sold'] ?>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn-add-cart-pink" onclick="AliceApp.openProductDetail(<?= $p['id'] ?>)">
                                    <i class="fa-solid fa-cart-plus"></i>
                                    <span>Chọn Mua</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
