/**
 * COMPREHENSIVE END-TO-END (E2E) TEST SUITE FOR DEPLOYED ALICE COSMETIC
 * Target: https://myphamdep.c3-app-018.io.vn & https://myphamdep-39f8ff3a515d.herokuapp.com/
 */

const https = require('https');
const http = require('http');

const TARGETS = [
    'https://myphamdep.c3-app-018.io.vn',
    'https://myphamdep-39f8ff3a515d.herokuapp.com'
];

function request(url, options = {}) {
    return new Promise((resolve, reject) => {
        const u = new URL(url);
        const mod = u.protocol === 'https:' ? https : http;
        const opt = {
            hostname: u.hostname,
            port: u.port || (u.protocol === 'https:' ? 443 : 80),
            path: u.pathname + u.search,
            method: options.method || 'GET',
            headers: options.headers || {},
            rejectUnauthorized: false,
            timeout: 15000
        };

        const req = mod.request(opt, (res) => {
            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => resolve({
                status: res.statusCode,
                headers: res.headers,
                body: data
            }));
        });

        req.on('error', reject);
        req.on('timeout', () => {
            req.destroy();
            reject(new Error('Request timed out'));
        });

        if (options.body) {
            req.write(typeof options.body === 'string' ? options.body : JSON.stringify(options.body));
        }
        req.end();
    });
}

async function runTestsForTarget(baseUrl) {
    console.log(`\n===============================================================`);
    console.log(`🚀 STARTING COMPREHENSIVE E2E TESTS FOR: ${baseUrl}`);
    console.log(`===============================================================`);

    let passed = 0;
    let failed = 0;

    function assert(name, condition, extra = '') {
        if (condition) {
            console.log(`  ✅ [PASS] ${name}`);
            passed++;
        } else {
            console.error(`  ❌ [FAIL] ${name} ${extra}`);
            failed++;
        }
    }

    // -------------------------------------------------------------
    // TEST SUITE 1: HOMEPAGE & CRITICAL DOM CHECKS
    // -------------------------------------------------------------
    console.log(`\n--- [SUITE 1] Homepage & Critical Assets ---`);
    try {
        const homeRes = await request(`${baseUrl}/`);
        assert('Homepage HTTP 200 OK', homeRes.status === 200, `(Status: ${homeRes.status})`);
        
        const html = homeRes.body;
        assert('Contains Shop Name ALICE Cosmetic', html.includes('ALICE Cosmetic'));
        assert('Contains Hotline 0869.571.734', html.includes('0869.571.734'));
        assert('Contains Zalo QR link', html.includes('https://zaloapp.com/qr/p/12waxvomw9fms?src=qr'));
        assert('Contains Facebook link mduong79', html.includes('https://www.facebook.com/mduong79#'));
        assert('Contains Messenger link mduong79', html.includes('https://m.me/mduong79'));
        assert('Contains Facebook App ID 885141314536567', html.includes('885141314536567'));
        assert('Contains Facebook SDK script tag', html.includes('connect.facebook.net/vi_VN/sdk.js'));
        assert('Contains Google Identity Services SDK', html.includes('accounts.google.com/gsi/client'));
        assert('Contains Zalo official vector SVG', html.includes('assets/images/zalo-chat.svg'));
        assert('Modal close buttons have AliceApp.closeModal', (html.match(/AliceApp\.closeModal\(this\)/g) || []).length >= 5);
        assert('Contains Register Confirm Password Field', html.includes('name="confirm_password"') && html.includes('register-confirm-pwd'));
    } catch (e) {
        assert('Homepage load', false, e.message);
    }

    // -------------------------------------------------------------
    // TEST SUITE 2: STATIC ASSETS ACCESSIBILITY
    // -------------------------------------------------------------
    console.log(`\n--- [SUITE 2] Static Assets (CSS, JS, SVG) ---`);
    const assets = [
        '/assets/css/style.css',
        '/assets/js/app.js',
        '/assets/images/zalo-chat.svg',
        '/assets/images/zalo-icon.svg'
    ];

    for (const a of assets) {
        try {
            const res = await request(`${baseUrl}${a}`);
            assert(`Asset ${a} HTTP 200`, res.status === 200, `(Status: ${res.status})`);
        } catch (e) {
            assert(`Asset ${a}`, false, e.message);
        }
    }

    // -------------------------------------------------------------
    // TEST SUITE 3: PRODUCTS API & CATALOG
    // -------------------------------------------------------------
    console.log(`\n--- [SUITE 3] Products API & Data Quality ---`);
    try {
        const prodRes = await request(`${baseUrl}/api/products.php`);
        assert('GET /api/products.php HTTP 200', prodRes.status === 200);
        const prodData = JSON.parse(prodRes.body);
        assert('Products array is returned', Array.isArray(prodData.products));
        assert(`Total products is 16 (Got: ${prodData.products.length})`, prodData.products.length === 16);

        // Check product detail
        const detailRes = await request(`${baseUrl}/api/products.php?id=1`);
        const detailData = JSON.parse(detailRes.body);
        assert('GET /api/products.php?id=1 returns valid product', detailData.product && detailData.product.id == 1);
        assert('Product 1 has variants', Array.isArray(detailData.product.variants) && detailData.product.variants.length > 0);
        assert('Product 1 has add-on deals', Array.isArray(detailData.product.add_ons));

        // Verify all 16 product images load with HTTP 200
        console.log(`  Verifying all 16 product images on CDN...`);
        let imageErrors = 0;
        for (const p of prodData.products) {
            try {
                const imgRes = await request(p.image);
                if (imgRes.status !== 200) {
                    console.error(`    ❌ Broken image on product #${p.id} (${p.name}): ${p.image} Status: ${imgRes.status}`);
                    imageErrors++;
                }
            } catch (err) {
                console.error(`    ❌ Image network error #${p.id}: ${err.message}`);
                imageErrors++;
            }
        }
        assert('All 16 product images return HTTP 200 OK', imageErrors === 0, `(${imageErrors} broken images found)`);
    } catch (e) {
        assert('Products API suite', false, e.message);
    }

    // -------------------------------------------------------------
    // TEST SUITE 4: COUPON & VOUCHER VALIDATION API
    // -------------------------------------------------------------
    console.log(`\n--- [SUITE 4] Coupon & Voucher API ---`);
    try {
        const validRes = await request(`${baseUrl}/api/coupon.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: { code: 'ALICEPINK', subtotal: 350000 }
        });
        const validData = JSON.parse(validRes.body);
        assert('Coupon ALICEPINK is valid (-50.000₫)', validData.valid === true && validData.discount === 50000);

        const invalidRes = await request(`${baseUrl}/api/coupon.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: { code: 'NOT_EXIST_CODE', subtotal: 350000 }
        });
        const invalidData = JSON.parse(invalidRes.body);
        assert('Invalid coupon returns valid: false', invalidData.valid === false);
    } catch (e) {
        assert('Coupon API suite', false, e.message);
    }

    // -------------------------------------------------------------
    // TEST SUITE 5: AUTHENTICATION FLOW (E2E)
    // -------------------------------------------------------------
    console.log(`\n--- [SUITE 5] Authentication & Security (E2E) ---`);
    const testEmail = `e2e_test_${Date.now()}@alice.vn`;
    let sessionCookie = '';

    try {
        // 1. Password mismatch validation
        const mismatchRes = await request(`${baseUrl}/api/auth.php?action=register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: {
                fullname: 'E2E Tester',
                email: testEmail,
                phone: '0988776655',
                password: 'password123',
                confirm_password: 'password_WRONG'
            }
        });
        const mismatchData = JSON.parse(mismatchRes.body);
        assert('Rejects registration when confirm_password mismatches', mismatchData.success === false && mismatchData.message.includes('khớp'));

        // 2. Successful registration
        const regRes = await request(`${baseUrl}/api/auth.php?action=register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: {
                fullname: 'E2E Tester',
                email: testEmail,
                phone: '0988776655',
                password: 'password123',
                confirm_password: 'password123'
            }
        });
        const regData = JSON.parse(regRes.body);
        assert('Valid registration succeeds and returns user', regData.success === true && regData.user.email === testEmail);

        if (regRes.headers['set-cookie']) {
            sessionCookie = regRes.headers['set-cookie'].map(c => c.split(';')[0]).join('; ');
        }

        // 3. Login with registered user
        const loginRes = await request(`${baseUrl}/api/auth.php?action=login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: {
                identifier: testEmail,
                password: 'password123'
            }
        });
        const loginData = JSON.parse(loginRes.body);
        assert('Login with correct credentials succeeds', loginData.success === true && loginData.user.email === testEmail);

        // 4. Test OAuth / Social login (Google & Facebook)
        const fbOAuthRes = await request(`${baseUrl}/api/auth.php?action=oauth`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: {
                provider: 'facebook',
                fullname: 'Thu Hằng (Facebook E2E)',
                email: `fb_tester_${Date.now()}@social.alice`,
                avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80'
            }
        });
        const fbOAuthData = JSON.parse(fbOAuthRes.body);
        assert('OAuth Facebook login successfully creates/authenticates user', fbOAuthData.success === true && fbOAuthData.user.fullname.includes('Thu Hằng'));

        // 5. Logout
        const logoutRes = await request(`${baseUrl}/api/auth.php?action=logout`);
        const logoutData = JSON.parse(logoutRes.body);
        assert('Logout succeeds', logoutData.success === true);
    } catch (e) {
        assert('Auth suite', false, e.message);
    }

    // -------------------------------------------------------------
    // TEST SUITE 6: CHECKOUT & AUTOMATED VIETQR FLOW (E2E)
    // -------------------------------------------------------------
    console.log(`\n--- [SUITE 6] Checkout, VietQR & Order Lookup (E2E) ---`);
    let createdOrderCode = '';
    const orderPhone = '0987112233';

    try {
        // 1. Create VietQR order
        const checkoutRes = await request(`${baseUrl}/api/checkout.php`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: {
                customer_name: 'Nguyễn Thu Trang (E2E)',
                phone: orderPhone,
                address: '175 Tây Sơn, Đống Đa, Hà Nội',
                note: 'Đóng hộp quà nơ hồng pastel giúp mình',
                payment_method: 'vietqr',
                coupon_code: 'ALICEPINK',
                cart: [
                    { id: 1, name: 'Serum Phục Hồi Rau Má Skin1004', price: 285000, quantity: 1, variant_name: '100ml' },
                    { id: 2, name: 'Kem Chống Nắng Anessa Gold Milk', price: 395000, quantity: 1, variant_name: '60ml' }
                ]
            }
        });
        const checkoutData = JSON.parse(checkoutRes.body);
        assert('VietQR Checkout succeeds and creates order', checkoutData.success === true);
        assert('Returns valid order_code (e.g. ALICE-...)', checkoutData.order_code && checkoutData.order_code.startsWith('ALICE-'));
        assert('Returns dynamic Napas 24/7 VietQR URL', checkoutData.vietqr_url && checkoutData.vietqr_url.includes('vietqr.io'));
        assert('Calculates correct final amount with coupon discount', checkoutData.final_amount === (285000 + 395000 - 50000));

        createdOrderCode = checkoutData.order_code;

        // 2. Lookup order by order_code
        const lookupCodeRes = await request(`${baseUrl}/api/orders.php?q=${encodeURIComponent(createdOrderCode)}`);
        const lookupCodeData = JSON.parse(lookupCodeRes.body);
        assert('Order Lookup by order_code returns created order', lookupCodeData.orders && lookupCodeData.orders.some(o => o.order_code === createdOrderCode));

        // 3. Lookup order by phone number
        const lookupPhoneRes = await request(`${baseUrl}/api/orders.php?q=${encodeURIComponent(orderPhone)}`);
        const lookupPhoneData = JSON.parse(lookupPhoneRes.body);
        assert('Order Lookup by customer phone number returns order list', lookupPhoneData.orders && lookupPhoneData.orders.length > 0);

        // 4. Test VietQR image URL accessibility
        const qrImgRes = await request(checkoutData.vietqr_url);
        assert('VietQR image URL is reachable and returns HTTP 200', qrImgRes.status === 200, `(Status: ${qrImgRes.status})`);

        // 5. Confirm payment status
        const confirmRes = await request(`${baseUrl}/api/orders.php?action=confirm_payment`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: { order_code: createdOrderCode }
        });
        const confirmData = JSON.parse(confirmRes.body);
        assert('Payment confirmation marks order as paid', confirmData.success === true && confirmData.order.payment_status === 'paid');
    } catch (e) {
        assert('Checkout & VietQR suite', false, e.message);
    }

    console.log(`\n===============================================================`);
    console.log(`📊 RESULTS FOR ${baseUrl}:`);
    console.log(`   PASSED: ${passed}`);
    console.log(`   FAILED: ${failed}`);
    console.log(`   SUCCESS RATE: ${Math.round((passed / (passed + failed)) * 100)}%`);
    console.log(`===============================================================\n`);

    return { passed, failed };
}

async function main() {
    let allPassed = true;
    for (const target of TARGETS) {
        try {
            const res = await runTestsForTarget(target);
            if (res.failed > 0) allPassed = false;
        } catch (e) {
            console.error(`Target ${target} crashed:`, e);
            allPassed = false;
        }
    }

    if (allPassed) {
        console.log('🎉 ALL END-TO-END TESTS PASSED ON ALL DEPLOYED SERVERS!');
        process.exit(0);
    } else {
        console.error('⚠️ SOME TESTS FAILED. PLEASE REVIEW LOGS.');
        process.exit(1);
    }
}

main();
