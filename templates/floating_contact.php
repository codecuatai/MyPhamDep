<?php
/**
 * FLOATING CONTACT BUTTONS - ALICE COSMETIC (MỘC MIÊN STYLE)
 */
?>
<div class="floating-contact-wrap">
    <!-- Nút Chat Zalo -->
    <a href="<?= htmlspecialchars($shopConfig['zalo_url']) ?>" target="_blank" class="btn-float-action btn-float-zalo" title="Chat Zalo tư vấn da liễu">
        <i class="fa-solid fa-comment-dots"></i>
    </a>

    <!-- Nút Chat Messenger -->
    <a href="<?= htmlspecialchars($shopConfig['messenger_url']) ?>" target="_blank" class="btn-float-action btn-float-messenger" title="Nhắn tin Facebook Messenger">
        <i class="fa-brands fa-facebook-messenger"></i>
    </a>

    <!-- Nút Gọi Hotline -->
    <a href="tel:<?= preg_replace('/[^0-9]/', '', $shopConfig['hotline']) ?>" class="btn-float-action btn-float-hotline" title="Gọi Hotline hỗ trợ">
        <i class="fa-solid fa-phone"></i>
    </a>

    <!-- Nút Cuộn lên đầu trang -->
    <a href="#top" class="btn-float-action" style="background: rgba(45, 24, 34, 0.7); display: none;" id="btn-back-to-top" title="Lên đầu trang">
        <i class="fa-solid fa-arrow-up"></i>
    </a>
</div>

<script>
window.addEventListener('scroll', () => {
    const btn = document.getElementById('btn-back-to-top');
    if (window.scrollY > 300) {
        btn.style.display = 'flex';
    } else {
        btn.style.display = 'none';
    }
});
</script>
