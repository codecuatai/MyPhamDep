<?php
/**
 * BUBBLE CATEGORIES - ALICE COSMETIC (MỘC MIÊN BOOKS BUBBLE STYLE)
 */
?>
<section class="section-bubble-categories">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge rounded-pill px-3 py-2" style="background: var(--primary-pastel); color: var(--primary-dark); font-size: 0.78rem; font-weight: 700;">
                <i class="fa-solid fa-sparkles me-1"></i>DANH MỤC LÀM ĐẸP
            </span>
            <h3 class="font-bold mt-2" style="font-family: var(--font-heading); color: var(--text-main); font-size: 1.8rem;">
                Khám Phá Theo Nhu Cầu Làn Da
            </h3>
        </div>

        <div class="row row-cols-3 row-cols-md-6 g-3 justify-content-center">
            <?php foreach ($categories as $cat):
                $actualCount = isset($cat['actual_product_count']) ? (int)$cat['actual_product_count'] : (int)($cat['count'] ?? 0);
                $catSlug = !empty($cat['slug']) ? $cat['slug'] : $cat['id'];
                $catUrl = ($baseUrl ?? '') . '/danh-muc/' . $catSlug;
            ?>
                <div class="col">
                    <a href="<?= htmlspecialchars($catUrl) ?>" onclick="event.preventDefault(); AliceApp.filterByCategory(<?= htmlspecialchars(json_encode($cat['id'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars(json_encode($cat['name'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>);" class="bubble-category-item" data-cat-id="<?= htmlspecialchars($cat['id']) ?>" data-cat-slug="<?= htmlspecialchars($catSlug) ?>" title="<?= htmlspecialchars($cat['name']) ?>">
                        <div class="bubble-circle-wrap">
                            <img src="<?= htmlspecialchars($cat['image']) ?>" 
                                 alt="<?= htmlspecialchars($cat['name']) ?>" 
                                 class="bubble-img-inner"
                                 onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=500&q=80';">
                            <span class="bubble-badge-count"><?= $actualCount ?></span>
                        </div>
                        <div class="bubble-name"><?= htmlspecialchars($cat['name']) ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
