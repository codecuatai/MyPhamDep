<?php
/**
 * SOCIAL AUTH SDK & CONFIG INITIALIZER (Google Identity Services & Facebook SDK)
 * Dùng chung trên mọi trang có #modalAuth để đăng nhập Google/Facebook hoạt động nhất quán.
 */
$googleClientId = trim(defined('GOOGLE_CLIENT_ID') ? (string) GOOGLE_CLIENT_ID : '');
$facebookAppId = trim(defined('FACEBOOK_APP_ID') ? (string) FACEBOOK_APP_ID : '');
?>
<link rel="dns-prefetch" href="https://accounts.google.com">
<script>
    window.GOOGLE_CLIENT_ID = <?= json_encode($googleClientId, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.FACEBOOK_APP_ID = <?= json_encode($facebookAppId, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    window.__ALICE_GSI_LOAD_ERROR = false;
    window.__ALICE_FB_LOAD_ERROR = false;
    window.fbAsyncInit = function() {
        if (window.FACEBOOK_APP_ID && window.FACEBOOK_APP_ID.trim() !== '') {
            FB.init({
                appId: window.FACEBOOK_APP_ID.trim(),
                cookie: true,
                xfbml: false,
                fedCM: false,
                version: 'v19.0'
            });
        }
    };
</script>
<?php if ($googleClientId !== ''): ?>
<!-- Google Identity Services (Sign in with Google) -->
<script src="https://accounts.google.com/gsi/client" async defer data-alice-gsi="1" onerror="window.__ALICE_GSI_LOAD_ERROR = true;"></script>
<?php endif; ?>
<?php if ($facebookAppId !== ''): ?>
<!-- Facebook SDK for JavaScript -->
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js" data-alice-fb="1" onerror="window.__ALICE_FB_LOAD_ERROR = true;"></script>
<?php endif; ?>
