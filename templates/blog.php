<?php
/**
 * BEAUTY BLOG & ROUTINE TIPS - ALICE COSMETIC (DYNAMIC & CHUẨN SEO)
 */
$blogPosts = [];
if (isset($pdo)) {
    try {
        $bStmt = $pdo->query("SELECT * FROM posts ORDER BY id ASC LIMIT 3");
        $blogPosts = $bStmt->fetchAll();
    } catch (Exception $e) {
        $blogPosts = [];
    }
}
?>
<section class="py-5" style="background: #fffbfd;">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="badge rounded-pill px-3 py-1 font-bold" style="background: var(--primary-pastel); color: var(--primary-dark); font-size: 0.78rem;">
                    CẨM NANG LÀM ĐẸP
                </span>
                <h2 class="font-bold mt-1 mb-0" style="font-family: var(--font-heading); font-size: 1.8rem; color: var(--text-main);">
                    Bí Quyết Chăm Sóc Da Cùng Chuyên Gia
                </h2>
            </div>
            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/cam-nang" class="small font-bold d-none d-md-inline" style="color: var(--primary-dark);">
                Xem tất cả bài viết <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3">
            <?php if (!empty($blogPosts)): ?>
                <?php foreach ($blogPosts as $bp): ?>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden bg-white d-flex flex-column" style="border: 1px solid var(--primary-pastel) !important;">
                            <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/cam-nang/<?= htmlspecialchars($bp['slug']) ?>" class="d-block overflow-hidden" style="height: 180px;">
                                <img src="<?= htmlspecialchars($bp['image']) ?>" 
                                     alt="<?= htmlspecialchars($bp['title']) ?>" 
                                     class="w-100 h-100" 
                                     style="object-fit: cover; transition: transform 0.3s;" 
                                     loading="lazy" 
                                     decoding="async"
                                     onmouseover="this.style.transform='scale(1.05)'" 
                                     onmouseout="this.style.transform='scale(1)'"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80';">
                            </a>
                            <div class="card-body p-3 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small text-muted"><i class="fa-regular fa-clock me-1"></i><?= $bp['reading_time'] ?> phút đọc • <?= htmlspecialchars($bp['category']) ?></span>
                                </div>
                                <h3 class="card-title font-bold mt-1 mb-2" style="font-size: 0.95rem; line-height: 1.4;">
                                    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/cam-nang/<?= htmlspecialchars($bp['slug']) ?>" class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($bp['title']) ?>
                                    </a>
                                </h3>
                                <p class="card-text small text-muted text-truncate-3 mb-3">
                                    <?= htmlspecialchars($bp['excerpt'] ?: strip_tags($bp['content'])) ?>
                                </p>
                                <div class="mt-auto pt-2 border-top text-end">
                                    <a href="<?= htmlspecialchars($baseUrl ?? '') ?>/cam-nang/<?= htmlspecialchars($bp['slug']) ?>" class="small font-bold text-decoration-none" style="color: var(--primary-dark);">
                                        Đọc tiếp <i class="fa-solid fa-angle-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback nếu chưa tải bài viết -->
                <div class="col-12 text-center text-muted py-4">Đang cập nhật bài viết mới...</div>
            <?php endif; ?>
        </div>
    </div>
</section>
