/**
 * APP ENGINE - ALICE COSMETIC (TONE HỒNG PASTEL)
 * Xử lý Giỏ hàng, Voucher, Flash Sale, Biến thể & Thanh toán VietQR tự động
 */

const AliceApp = {
    cart: [],
    wishlist: [],
    currentUser: null,
    appliedCoupon: null,
    currentProductDetail: null,
    selectedVariant: null,
    selectedAddOns: [],
    flashSaleTimer: null,
    vietqrTimer: null,
    freeshipThreshold: 299000,

    init() {
        this.loadStorage();
        this.checkAuth();
        this.handleSocialRedirects();
        this.initHeroCarousel();
        this.initFlashSaleTimer();
        this.bindEvents();
        this.updateCartBadge();
    },

    getGuestCart() {
        try {
            const raw = localStorage.getItem('alice_guest_cart') || localStorage.getItem('alice_cart');
            return raw ? JSON.parse(raw) : [];
        } catch(e) { return []; }
    },

    getGuestCoupon() {
        try {
            const raw = localStorage.getItem('alice_guest_coupon') || localStorage.getItem('alice_coupon');
            return raw ? JSON.parse(raw) : null;
        } catch(e) { return null; }
    },

    clearGuestCart() {
        localStorage.removeItem('alice_guest_cart');
        localStorage.removeItem('alice_guest_coupon');
        localStorage.removeItem('alice_cart');
        localStorage.removeItem('alice_coupon');
    },

    getUserStorageKey() {
        return this.currentUser ? `alice_cart_user_${this.currentUser.id}` : 'alice_guest_cart';
    },

    getUserCouponKey() {
        return this.currentUser ? `alice_coupon_user_${this.currentUser.id}` : 'alice_guest_coupon';
    },

    loadStorage() {
        try {
            const cartKey = this.getUserStorageKey();
            const couponKey = this.getUserCouponKey();
            let savedCart = localStorage.getItem(cartKey);
            if (!savedCart && !this.currentUser) {
                savedCart = localStorage.getItem('alice_cart');
            }
            if (savedCart) this.cart = JSON.parse(savedCart);
            else this.cart = [];

            let savedCoupon = localStorage.getItem(couponKey);
            if (!savedCoupon && !this.currentUser) {
                savedCoupon = localStorage.getItem('alice_coupon');
            }
            if (savedCoupon) this.appliedCoupon = JSON.parse(savedCoupon);
            else this.appliedCoupon = null;
        } catch (e) {
            console.error('Storage error:', e);
        }
    },

    saveCart(syncCloud = true) {
        try {
            const cartKey = this.getUserStorageKey();
            const couponKey = this.getUserCouponKey();
            localStorage.setItem(cartKey, JSON.stringify(this.cart));
            if (this.appliedCoupon) {
                localStorage.setItem(couponKey, JSON.stringify(this.appliedCoupon));
            } else {
                localStorage.removeItem(couponKey);
            }
        } catch(e) {
            console.warn('Save cart local error:', e);
        }

        this.updateCartBadge();
        this.renderCartDrawer();

        if (syncCloud && this.currentUser) {
            this.syncCartWithServer();
        }
    },

    syncCartWithServer() {
        if (!this.currentUser) return;
        clearTimeout(this._syncCartTimer);
        this._syncCartTimer = setTimeout(async () => {
            try {
                await fetch('api/cart.php?action=save', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        cart: this.cart,
                        coupon: this.appliedCoupon
                    })
                });
            } catch(e) {
                console.warn('Sync cart cloud error:', e);
            }
        }, 400);
    },

    async fetchUserCart() {
        if (!this.currentUser) return;
        try {
            const res = await fetch('api/cart.php?action=get');
            const data = await res.json();
            if (data.authenticated && Array.isArray(data.cart)) {
                this.cart = data.cart;
                this.appliedCoupon = data.coupon || null;
                this.saveCart(false);
            }
        } catch(e) {
            console.warn('Fetch user cart error:', e);
        }
    },

    async onLoginSuccess(user, message) {
        this.currentUser = user;
        this.updateAuthUI();
        if (message) this.showToast(message, 'success');
        this.closeModal('modalAuth');

        const guestCart = this.getGuestCart();
        const guestCoupon = this.getGuestCoupon();

        if (guestCart && guestCart.length > 0) {
            this.showToast('Đang đồng bộ giỏ hàng cá nhân...', 'info');
            try {
                const res = await fetch('api/cart.php?action=merge', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        guest_cart: guestCart,
                        guest_coupon: guestCoupon
                    })
                });
                const mergeData = await res.json();
                if (mergeData.success && Array.isArray(mergeData.cart)) {
                    this.cart = mergeData.cart;
                    this.appliedCoupon = mergeData.coupon || null;
                    this.clearGuestCart();
                }
            } catch(e) {
                console.error('Merge cart error:', e);
            }
        } else {
            await this.fetchUserCart();
        }

        this.saveCart(false);
        this.updateCartBadge();
        this.renderCartDrawer();
    },

    formatVND(num) {
        return new Intl.NumberFormat('vi-VN').format(num) + '₫';
    },

    showToast(message, type = 'success') {
        const container = document.getElementById('alice-toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'alice-toast';
        toast.innerHTML = `
            <i class="fa-solid ${type === 'success' ? 'fa-circle-check text-pink-500' : 'fa-circle-exclamation text-amber-500'}" style="color: var(--primary); font-size: 1.2rem;"></i>
            <div>${message}</div>
        `;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3200);
    },

    closeModal(btnOrModalId) {
        let modalEl = null;
        if (typeof btnOrModalId === 'string') {
            modalEl = document.getElementById(btnOrModalId);
        } else if (btnOrModalId && btnOrModalId.closest) {
            modalEl = btnOrModalId.closest('.modal');
        }
        if (!modalEl) {
            modalEl = document.querySelector('.modal.show');
        }
        if (modalEl) {
            if (modalEl.id === 'modalVietQR') {
                if (this.paymentPollingTimer) clearInterval(this.paymentPollingTimer);
                if (this.vietqrTimer) clearInterval(this.vietqrTimer);
            }
            try {
                const instance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                if (instance) instance.hide();
            } catch(e) {}

            // Dọn dẹp DOM ngay và dọn lần nữa sau 180ms để đảm bảo không bao giờ bị kẹt backdrop mờ
            const cleanup = () => {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                modalEl.setAttribute('aria-hidden', 'true');
                modalEl.removeAttribute('aria-modal');
                modalEl.removeAttribute('role');
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');
            };

            cleanup();
            setTimeout(cleanup, 180);
        }
    },

    updateCartBadge() {
        const count = this.cart.reduce((sum, it) => sum + (it.quantity || 1), 0);
        const badges = document.querySelectorAll('.cart-badge-count');
        badges.forEach(b => {
            b.textContent = count;
            b.style.display = count > 0 ? 'flex' : 'none';
        });
    },

    // ==========================================
    // 0. HERO LUXE BANNER AUTOPLAY (5s CONTINUOUS LOOP)
    // ==========================================
    initHeroCarousel() {
        const carouselEl = document.getElementById('heroBannerCarousel');
        if (!carouselEl || !window.bootstrap) return;

        // Khởi tạo Bootstrap Carousel với chu kỳ 5 giây, quay vòng liên tục
        const carousel = bootstrap.Carousel.getOrCreateInstance(carouselEl, {
            interval: 5000,
            ride: 'carousel',
            pause: false, // Tiếp tục quay vòng kể cả khi rê chuột
            wrap: true
        });
        carousel.cycle();
    },

    // ==========================================
    // 1. FLASH SALE TIMER
    // ==========================================
    initFlashSaleTimer() {
        const hEl = document.getElementById('fs-hours');
        const mEl = document.getElementById('fs-minutes');
        const sEl = document.getElementById('fs-seconds');
        if (!hEl || !mEl || !sEl) return;

        // Set mốc kết thúc sale vào cuối ngày
        const now = new Date();
        const endOfDay = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);

        const updateTimer = () => {
            const current = new Date();
            const diff = Math.max(0, endOfDay - current);
            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            hEl.textContent = String(hours).padStart(2, '0');
            mEl.textContent = String(minutes).padStart(2, '0');
            sEl.textContent = String(seconds).padStart(2, '0');
        };

        updateTimer();
        this.flashSaleTimer = setInterval(updateTimer, 1000);
    },

    // ==========================================
    // 2. MODAL CHI TIẾT SẢN PHẨM & BIẾN THỂ (PDP)
    // ==========================================
    async openProductDetail(productId) {
        try {
            const res = await fetch(`api/products.php?action=detail&id=${productId}`);
            const product = await res.json();
            if (!product || product.error) {
                this.showToast('Không thể tải thông tin sản phẩm!', 'warning');
                return;
            }

            this.currentProductDetail = product;
            this.selectedVariant = (product.variants && product.variants.length > 0) ? product.variants[0] : null;
            this.selectedAddOns = [];

            // Populate Modal
            document.getElementById('pdp-name').textContent = product.name;
            document.getElementById('pdp-brand').textContent = product.brand;
            document.getElementById('pdp-rating').textContent = product.rating;
            document.getElementById('pdp-sold').textContent = product.sold_count;
            document.getElementById('pdp-desc').textContent = product.description;
            document.getElementById('pdp-ingredients').textContent = product.ingredients || 'Thành phần an toàn, lành tính theo chuẩn quốc tế.';
            document.getElementById('pdp-howtouse').textContent = product.how_to_use || 'Sử dụng đều đặn mỗi ngày sau bước làm sạch.';

            // Populate Specs & Inventory
            const stockQty = (typeof product.stock_quantity !== 'undefined') ? parseInt(product.stock_quantity) : 50;
            const origin = product.origin || 'Chính hãng nhập khẩu';
            const capacity = product.capacity || 'Tiêu chuẩn';
            const expiry = product.expiry || '36 tháng kể từ NSX';

            const elStockQty = document.getElementById('pdp-stock-qty');
            if (elStockQty) elStockQty.textContent = stockQty;
            const elOrigin = document.getElementById('pdp-origin');
            if (elOrigin) elOrigin.textContent = origin;
            const elCapacity = document.getElementById('pdp-capacity');
            if (elCapacity) elCapacity.textContent = capacity;
            const elExpiry = document.getElementById('pdp-expiry');
            if (elExpiry) elExpiry.textContent = expiry;

            const elSpecBrand = document.getElementById('pdp-spec-brand');
            if (elSpecBrand) elSpecBrand.textContent = product.brand || 'ALICE';
            const elSpecOrigin = document.getElementById('pdp-spec-origin');
            if (elSpecOrigin) elSpecOrigin.textContent = origin;
            const elSpecCap = document.getElementById('pdp-spec-capacity');
            if (elSpecCap) elSpecCap.textContent = capacity;
            const elSpecExp = document.getElementById('pdp-spec-expiry');
            if (elSpecExp) elSpecExp.textContent = expiry;
            const elSpecStock = document.getElementById('pdp-spec-stock');
            if (elSpecStock) {
                elSpecStock.innerHTML = stockQty > 0 
                    ? `<span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i>Còn hàng (${stockQty} sản phẩm)</span>` 
                    : `<span class="text-danger fw-bold"><i class="fa-solid fa-circle-xmark me-1"></i>Tạm hết hàng</span>`;
            }

            // Stock Badge
            const elStockBadge = document.getElementById('pdp-stock-badge');
            if (elStockBadge) {
                if (stockQty > 10) {
                    elStockBadge.innerHTML = `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>Còn hàng (${stockQty})</span>`;
                } else if (stockQty > 0) {
                    elStockBadge.innerHTML = `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="fa-solid fa-fire me-1"></i>Sắp hết hàng (${stockQty})</span>`;
                } else {
                    elStockBadge.innerHTML = `<span class="badge bg-danger text-white px-2 py-1"><i class="fa-solid fa-circle-xmark me-1"></i>Hết hàng (0)</span>`;
                }
            }

            // Disable Add to Cart button if out of stock
            const btnAddToCart = document.getElementById('pdp-btn-add-cart');
            if (btnAddToCart) {
                if (stockQty <= 0) {
                    btnAddToCart.disabled = true;
                    btnAddToCart.style.opacity = '0.55';
                    btnAddToCart.style.cursor = 'not-allowed';
                    btnAddToCart.innerHTML = `<i class="fa-solid fa-circle-xmark me-2"></i>Tạm Hết Hàng Trong Kho`;
                } else {
                    btnAddToCart.disabled = false;
                    btnAddToCart.style.opacity = '1';
                    btnAddToCart.style.cursor = 'pointer';
                    btnAddToCart.innerHTML = `<i class="fa-solid fa-cart-shopping me-2"></i>Thêm Vào Giỏ Hàng`;
                }
            }

            // Image Gallery
            const mainImg = document.getElementById('pdp-main-img');
            mainImg.src = product.image;
            mainImg.onerror = () => { mainImg.src = 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80'; };

            const galleryContainer = document.getElementById('pdp-gallery-thumbs');
            galleryContainer.innerHTML = '';
            const allImages = [product.image, ...(product.gallery || [])];
            allImages.forEach((imgUrl, idx) => {
                const thumb = document.createElement('img');
                thumb.src = imgUrl;
                thumb.onerror = () => { thumb.src = 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80'; };
                thumb.className = `pdp-thumb-item ${idx === 0 ? 'active' : ''}`;
                thumb.style.cssText = 'width: 55px; height: 55px; border-radius: 8px; object-fit: cover; cursor: pointer; border: 2px solid ' + (idx === 0 ? 'var(--primary)' : 'var(--primary-border)');
                thumb.onclick = () => {
                    mainImg.src = imgUrl;
                    document.querySelectorAll('.pdp-thumb-item').forEach(t => t.style.borderColor = 'var(--primary-border)');
                    thumb.style.borderColor = 'var(--primary)';
                };
                galleryContainer.appendChild(thumb);
            });

            // Update Price & Variants
            this.renderVariantsUI(product);
            this.renderAddOnDealsUI(product);
            this.renderReviewsUI(product.reviews || []);

            // Open Bootstrap Modal
            const modalEl = document.getElementById('modalProductDetail');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();

        } catch (err) {
            console.error('Error fetching product detail:', err);
        }
    },

    renderVariantsUI(product) {
        const container = document.getElementById('pdp-variants-container');
        container.innerHTML = '';

        if (!product.variants || product.variants.length === 0) {
            document.getElementById('pdp-price-current').textContent = this.formatVND(product.price);
            document.getElementById('pdp-price-original').textContent = this.formatVND(product.original_price);
            return;
        }

        const currentPrice = this.selectedVariant ? this.selectedVariant.price : product.price;
        const originalPrice = this.selectedVariant ? this.selectedVariant.original_price : product.original_price;

        document.getElementById('pdp-price-current').textContent = this.formatVND(currentPrice);
        document.getElementById('pdp-price-original').textContent = this.formatVND(originalPrice);

        product.variants.forEach((v, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            const isSelected = (this.selectedVariant && this.selectedVariant.name === v.name) || (!this.selectedVariant && idx === 0);
            btn.className = `btn btn-sm ${isSelected ? 'btn-danger' : 'btn-outline-secondary'} me-2 mb-2 rounded-pill px-3 py-1 font-semibold`;
            btn.style.borderColor = 'var(--primary)';
            if (isSelected) {
                btn.style.background = 'var(--gradient-pink)';
                btn.style.color = '#fff';
            } else {
                btn.style.color = 'var(--text-main)';
            }
            btn.textContent = v.name;
            btn.onclick = () => {
                this.selectedVariant = v;
                this.renderVariantsUI(product);
            };
            container.appendChild(btn);
        });
    },

    renderAddOnDealsUI(product) {
        const wrap = document.getElementById('pdp-addon-wrap');
        const container = document.getElementById('pdp-addon-container');
        container.innerHTML = '';

        if (!product.add_on_deals || product.add_on_deals.length === 0) {
            wrap.style.display = 'none';
            return;
        }

        wrap.style.display = 'block';
        product.add_on_deals.forEach(deal => {
            const isChecked = this.selectedAddOns.some(a => a.id === deal.id);
            const row = document.createElement('div');
            row.className = 'd-flex align-items-center justify-content-between p-2 mb-2 rounded-3';
            row.style.background = 'var(--primary-soft)';
            row.style.border = '1px solid var(--primary-border)';
            row.innerHTML = `
                <div class="form-check d-flex align-items-center gap-2 mb-0">
                    <input class="form-check-input" type="checkbox" id="addon-${deal.id}" ${isChecked ? 'checked' : ''}>
                    <label class="form-check-label small font-semibold mb-0" for="addon-${deal.id}" style="cursor: pointer;">
                        ${deal.name}
                    </label>
                </div>
                <div class="text-end">
                    <span class="small font-bold" style="color: var(--primary-dark);">${this.formatVND(deal.price)}</span>
                    <span class="small text-muted text-decoration-line-through d-block" style="font-size:0.7rem;">${this.formatVND(deal.original_price)}</span>
                </div>
            `;
            const checkbox = row.querySelector('input');
            checkbox.onchange = (e) => {
                if (e.target.checked) {
                    this.selectedAddOns.push(deal);
                } else {
                    this.selectedAddOns = this.selectedAddOns.filter(a => a.id !== deal.id);
                }
            };
            container.appendChild(row);
        });
    },

    renderReviewsUI(reviews) {
        const container = document.getElementById('pdp-reviews-container');
        container.innerHTML = '';

        if (reviews.length === 0) {
            container.innerHTML = `<p class="text-muted small">Chưa có đánh giá nào. Hãy là người đầu tiên trải nghiệm!</p>`;
            return;
        }

        reviews.forEach(r => {
            const card = document.createElement('div');
            card.className = 'p-3 mb-2 rounded-3 border-0';
            card.style.background = '#fff8fa';
            card.style.border = '1px solid var(--primary-pastel)';
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <strong class="small" style="color: var(--text-main);"><i class="fa-solid fa-circle-user me-1 text-pink-500" style="color: var(--primary);"></i>${r.author_name}</strong>
                    <span class="small text-warning font-bold">★ ${r.rating}.0</span>
                </div>
                <div class="mb-1">
                    <span class="badge" style="background: var(--primary-pastel); color: var(--primary-dark); font-size: 0.7rem;">
                        <i class="fa-solid fa-sparkles me-1"></i>Loại da: ${r.skin_type || 'Tất cả loại da'}
                    </span>
                </div>
                <p class="small text-muted mb-0" style="font-size: 0.82rem; line-height: 1.4;">${r.comment}</p>
            `;
            container.appendChild(card);
        });
    },

    // ==========================================
    // 3. GIỎ HÀNG MINI & VOUCHER (Cart Engine)
    // ==========================================
    addToCartCurrentProduct() {
        if (!this.currentProductDetail) return;
        const p = this.currentProductDetail;
        
        // Kiểm tra tồn kho trước khi thêm vào giỏ
        if (typeof p.stock_quantity !== 'undefined' && parseInt(p.stock_quantity) <= 0) {
            this.showToast('Sản phẩm "' + p.name + '" tạm thời đã hết hàng trong kho!', 'warning');
            return;
        }

        const variant = this.selectedVariant;
        const variantName = variant ? variant.name : '';
        const price = variant ? variant.price : p.price;

        // Thêm sản phẩm chính
        this.addItemToCart({
            id: p.id,
            name: p.name,
            image: p.image,
            price: price,
            variant_name: variantName,
            quantity: 1
        });

        // Thêm deal sốc mua kèm nếu có
        if (this.selectedAddOns.length > 0) {
            this.selectedAddOns.forEach(addon => {
                this.addItemToCart({
                    id: 'addon-' + addon.id,
                    name: '🎁 [Mua Kèm Deal Sốc] ' + addon.name,
                    image: p.image,
                    price: addon.price,
                    variant_name: 'Deal sốc',
                    quantity: 1
                });
            });
        }

        // Đóng modal PDP & mở Giỏ hàng
        const modalEl = document.getElementById('modalProductDetail');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        this.showToast(`Đã thêm "${p.name}" vào giỏ hàng!`, 'success');
        this.openCartDrawer();
    },

    quickAddToCart(productId, productName, productImg, productPrice) {
        this.addItemToCart({
            id: productId,
            name: productName,
            image: productImg,
            price: productPrice,
            variant_name: '',
            quantity: 1
        });
        this.showToast(`Đã thêm "${productName}" vào giỏ hàng!`, 'success');
        this.openCartDrawer();
    },

    addItemToCart(item) {
        const existing = this.cart.find(it => it.id === item.id && it.variant_name === item.variant_name);
        if (existing) {
            existing.quantity = (existing.quantity || 1) + 1;
        } else {
            this.cart.push(item);
        }
        this.saveCart();
    },

    updateItemQty(index, delta) {
        if (!this.cart[index]) return;
        this.cart[index].quantity += delta;
        if (this.cart[index].quantity <= 0) {
            this.cart.splice(index, 1);
        }
        this.saveCart();
    },

    removeItem(index) {
        this.cart.splice(index, 1);
        this.saveCart();
        this.showToast('Đã xóa sản phẩm khỏi giỏ hàng', 'warning');
    },

    openCartDrawer() {
        document.getElementById('cart-drawer-overlay').classList.add('active');
        document.getElementById('cart-drawer').classList.add('active');
        this.renderCartDrawer();
    },

    closeCartDrawer() {
        document.getElementById('cart-drawer-overlay').classList.remove('active');
        document.getElementById('cart-drawer').classList.remove('active');
    },

    renderCartDrawer() {
        const bodyEl = document.getElementById('cart-drawer-items');
        bodyEl.innerHTML = '';

        if (this.cart.length === 0) {
            bodyEl.innerHTML = `
                <div class="text-center py-5">
                    <i class="fa-solid fa-basket-shopping fa-3x mb-3" style="color: var(--primary-border);"></i>
                    <h6 class="font-bold">Giỏ hàng của bạn đang trống</h6>
                    <p class="small text-muted">Hãy chọn những món mỹ phẩm yêu thích tại ALICE Cosmetic nhé!</p>
                </div>
            `;
            this.updateFreeshipProgress(0);
            this.updateCartSummary(0);
            return;
        }

        let subtotal = 0;
        this.cart.forEach((it, idx) => {
            const itemTotal = it.price * (it.quantity || 1);
            subtotal += itemTotal;

            const row = document.createElement('div');
            row.className = 'cart-item-row';
            row.innerHTML = `
                <img src="${it.image}" class="cart-item-img" alt="${it.name}" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80';">
                <div class="cart-item-details">
                    <div class="d-flex justify-content-between">
                        <div class="cart-item-title text-truncate" style="max-width: 220px;">${it.name}</div>
                        <button class="btn btn-sm text-danger p-0 border-0" onclick="AliceApp.removeItem(${idx})"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                    ${it.variant_name ? `<span class="cart-item-variant">${it.variant_name}</span>` : ''}
                    <div class="cart-item-price-qty mt-2">
                        <span class="font-bold" style="color: var(--primary-dark); font-size: 0.88rem;">${this.formatVND(it.price)}</span>
                        <div class="qty-control-group">
                            <button class="qty-btn" onclick="AliceApp.updateItemQty(${idx}, -1)">-</button>
                            <span class="qty-input">${it.quantity || 1}</span>
                            <button class="qty-btn" onclick="AliceApp.updateItemQty(${idx}, 1)">+</button>
                        </div>
                    </div>
                </div>
            `;
            bodyEl.appendChild(row);
        });

        this.updateFreeshipProgress(subtotal);
        this.updateCartSummary(subtotal);
    },

    updateFreeshipProgress(subtotal) {
        const fillEl = document.getElementById('freeship-progress-fill');
        const textEl = document.getElementById('freeship-progress-text');
        if (!fillEl || !textEl) return;

        const remain = this.freeshipThreshold - subtotal;
        const percent = Math.min(100, Math.round((subtotal / this.freeshipThreshold) * 100));
        fillEl.style.width = percent + '%';

        if (remain <= 0) {
            textEl.innerHTML = `🎉 <strong>Chúc mừng!</strong> Bạn đã được <span class="badge bg-danger rounded-pill">Freeship toàn quốc</span>!`;
        } else {
            textEl.innerHTML = `Chỉ cần mua thêm <strong style="color: var(--primary-dark);">${this.formatVND(remain)}</strong> để được <strong>Freeship</strong>!`;
        }
    },

    updateCartSummary(subtotal) {
        const subtotalEl = document.getElementById('cart-subtotal');
        const discountEl = document.getElementById('cart-discount');
        const finalEl = document.getElementById('cart-final-total');

        let discount = 0;
        if (this.appliedCoupon) {
            discount = this.appliedCoupon.discount_amount || 0;
        }

        const finalTotal = Math.max(0, subtotal - discount);

        if (subtotalEl) subtotalEl.textContent = this.formatVND(subtotal);
        if (discountEl) discountEl.textContent = '-' + this.formatVND(discount);
        if (finalEl) finalEl.textContent = this.formatVND(finalTotal);
    },

    async applyCouponCode() {
        const input = document.getElementById('coupon-input');
        const code = input ? input.value.trim() : '';
        if (!code) {
            this.showToast('Vui lòng nhập mã giảm giá!', 'warning');
            return;
        }

        const subtotal = this.cart.reduce((sum, it) => sum + it.price * (it.quantity || 1), 0);
        if (subtotal === 0) {
            this.showToast('Giỏ hàng trống, chưa thể áp dụng voucher!', 'warning');
            return;
        }

        try {
            const res = await fetch(`api/coupon.php?code=${encodeURIComponent(code)}&subtotal=${subtotal}`);
            const data = await res.json();

            if (data.success) {
                this.appliedCoupon = data;
                this.saveCart();
                this.showToast(data.message, 'success');
                this.updateCartSummary(subtotal);
            } else {
                this.showToast(data.message || 'Mã voucher không hợp lệ', 'warning');
            }
        } catch (err) {
            this.showToast('Lỗi kết nối máy chủ voucher!', 'warning');
        }
    },

    // ==========================================
    // 4. CHECKOUT & THANH TOÁN VIETQR
    // ==========================================
    openCheckoutModal() {
        if (this.cart.length === 0) {
            this.showToast('Giỏ hàng của bạn đang trống!', 'warning');
            return;
        }
        this.closeCartDrawer();

        const subtotal = this.cart.reduce((sum, it) => sum + it.price * (it.quantity || 1), 0);
        let discount = this.appliedCoupon ? (this.appliedCoupon.discount_amount || 0) : 0;
        const finalTotal = Math.max(0, subtotal - discount);

        document.getElementById('checkout-items-summary').innerHTML = this.cart.map(it => `
            <div class="d-flex justify-content-between small mb-1">
                <span class="text-truncate" style="max-width: 240px;">${it.name} ${it.variant_name ? '(' + it.variant_name + ')' : ''} x${it.quantity}</span>
                <span class="font-bold">${this.formatVND(it.price * it.quantity)}</span>
            </div>
        `).join('');

        document.getElementById('checkout-final-amount').textContent = this.formatVND(finalTotal);

        // Tự động điền thông tin nếu đã đăng nhập
        if (this.currentUser) {
            const form = document.getElementById('formCheckout');
            if (form) {
                if (form.customer_name && !form.customer_name.value) {
                    form.customer_name.value = this.currentUser.fullname || '';
                }
                if (form.phone && !form.phone.value) {
                    form.phone.value = this.currentUser.phone || '';
                }
            }
        }

        const modalEl = document.getElementById('modalCheckout');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    },

    async submitOrder(event) {
        event.preventDefault();
        const form = document.getElementById('formCheckout');
        const customerName = form.customer_name.value.trim();
        const phone = form.phone.value.trim();
        const address = form.address.value.trim();
        const note = form.note ? form.note.value.trim() : '';
        const paymentMethod = form.payment_method.value;

        if (!customerName || !phone || !address) {
            this.showToast('Vui lòng điền đầy đủ họ tên, SĐT và địa chỉ!', 'warning');
            return;
        }

        const subtotal = this.cart.reduce((sum, it) => sum + it.price * (it.quantity || 1), 0);
        const discount = this.appliedCoupon ? (this.appliedCoupon.discount_amount || 0) : 0;
        const finalTotal = Math.max(0, subtotal - discount);

        const payload = {
            customer_name: customerName,
            phone: phone,
            address: address,
            note: note,
            payment_method: paymentMethod,
            coupon_code: this.appliedCoupon ? this.appliedCoupon.code : '',
            total_amount: subtotal,
            discount_amount: discount,
            final_amount: finalTotal,
            items: this.cart
        };

        const btnSubmit = document.getElementById('btn-submit-order');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang xử lý đơn hàng...`;

        try {
            const res = await fetch('api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            btnSubmit.disabled = false;
            btnSubmit.innerHTML = `<i class="fa-solid fa-bag-shopping me-2"></i>Xác Nhận Đặt Hàng`;

            if (data.success) {
                // Xóa giỏ hàng
                this.cart = [];
                this.appliedCoupon = null;
                this.clearGuestCart();
                if (this.currentUser) {
                    try {
                        localStorage.removeItem(this.getUserStorageKey());
                        localStorage.removeItem(this.getUserCouponKey());
                        fetch('api/cart.php?action=clear', { method: 'POST' }).catch(() => {});
                    } catch(e) {}
                }
                this.saveCart(false);

                // Đóng modal Checkout
                const modalCheckout = bootstrap.Modal.getInstance(document.getElementById('modalCheckout'));
                if (modalCheckout) modalCheckout.hide();

                if (paymentMethod === 'vietqr' && data.vietqr) {
                    this.showVietQRPaymentModal(data);
                } else {
                    this.showCodSuccessModal(data);
                }
            } else {
                this.showToast(data.message || 'Lỗi khi tạo đơn hàng!', 'warning');
            }
        } catch (err) {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = `<i class="fa-solid fa-bag-shopping me-2"></i>Xác Nhận Đặt Hàng`;
            this.showToast('Lỗi kết nối máy chủ đặt hàng!', 'warning');
        }
    },

    currentActiveOrder: null,
    paymentPollingTimer: null,

    showVietQRPaymentModal(orderData) {
        this.currentActiveOrder = orderData;
        const vqr = orderData.vietqr;
        document.getElementById('vqr-order-code').textContent = orderData.order_code;
        document.getElementById('vqr-amount').textContent = vqr.amount_formatted;
        document.getElementById('vqr-bank-name').textContent = vqr.bank_name;
        document.getElementById('vqr-account-no').textContent = vqr.account_no;
        document.getElementById('vqr-account-name').textContent = vqr.account_name;
        document.getElementById('vqr-add-info').textContent = vqr.add_info;

        // Cập nhật thông tin trên khung VietQR chuẩn mẫu
        const elCardBank = document.getElementById('vqr-card-bank');
        if (elCardBank) elCardBank.textContent = vqr.bank_id || 'MB';

        const elCardAccName = document.getElementById('vqr-card-accname');
        if (elCardAccName) elCardAccName.textContent = vqr.account_name || 'ALICE COSMETIC';

        const elCardAccNo = document.getElementById('vqr-card-accno');
        if (elCardAccNo) elCardAccNo.textContent = vqr.account_no || '0869571734';

        const elCardAmountVal = document.getElementById('vqr-card-amount-val');
        if (elCardAmountVal) elCardAmountVal.textContent = vqr.amount_formatted || (new Intl.NumberFormat('vi-VN').format(orderData.final_amount) + ' VND');

        // Sinh link xác nhận thanh toán quét bằng Camera / Zalo điện thoại (mã QR như cũ)
        const payUrl = window.location.origin + '/pay.php?code=' + encodeURIComponent(orderData.order_code);
        const smartQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=8&data=' + encodeURIComponent(payUrl);
        const vqrImg = document.getElementById('vqr-image');
        if (vqrImg) {
            vqrImg.src = smartQrUrl;
        }

        // Reset trạng thái hiển thị
        const statusText = document.getElementById('vqr-status-text');
        if (statusText) statusText.innerHTML = 'Hệ thống đang tự động nhận diện thanh toán...';

        // Bắt đầu lắng nghe tự động (Polling từ điện thoại)
        this.startPaymentPolling(orderData.order_code);

        // Bắt đầu đếm ngược 10 phút thanh toán
        this.startQRCountdown(600);

        const modalEl = document.getElementById('modalVietQR');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    },

    startPaymentPolling(orderCode) {
        if (this.paymentPollingTimer) clearInterval(this.paymentPollingTimer);

        this.paymentPollingTimer = setInterval(async () => {
            try {
                const res = await fetch(`api/orders.php?action=check_status&order_code=${encodeURIComponent(orderCode)}`);
                const data = await res.json();
                if (data.success && data.is_paid) {
                    clearInterval(this.paymentPollingTimer);
                    this.paymentPollingTimer = null;

                    const statusText = document.getElementById('vqr-status-text');
                    if (statusText) {
                        statusText.innerHTML = '<i class="fa-solid fa-circle-check text-success me-1"></i>Đã nhận diện thanh toán thành công!';
                    }

                    setTimeout(() => {
                        this.onPaymentAutoSuccess(orderCode);
                    }, 500);
                }
            } catch(e) {
                console.warn('Polling error:', e);
            }
        }, 1500);
    },

    onPaymentAutoSuccess(orderCode) {
        if (this.vietqrTimer) clearInterval(this.vietqrTimer);
        if (this.paymentPollingTimer) clearInterval(this.paymentPollingTimer);

        // Đóng modal VietQR
        const modalVqr = bootstrap.Modal.getInstance(document.getElementById('modalVietQR'));
        if (modalVqr) modalVqr.hide();

        // Gán thông tin vào modalPaymentSuccess
        const codeEl = document.getElementById('vqr-success-order-code');
        const amountEl = document.getElementById('vqr-success-amount');
        if (codeEl) codeEl.textContent = '#' + orderCode;
        if (amountEl && this.currentActiveOrder) {
            amountEl.textContent = this.currentActiveOrder.final_amount_formatted || (this.currentActiveOrder.vietqr ? this.currentActiveOrder.vietqr.amount_formatted : '');
        }

        // Mở modal Thành Công Rực Rỡ
        const modalSuccess = bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPaymentSuccess'));
        modalSuccess.show();

        this.showToast('🎉 Thanh toán thành công! ALICE Cosmetic đang chuẩn bị đóng gói đơn hàng cho bạn.', 'success');
    },

    async simulatePhoneScan() {
        if (!this.currentActiveOrder || !this.currentActiveOrder.order_code) {
            this.showToast('Không tìm thấy thông tin đơn hàng hiện tại!', 'warning');
            return;
        }

        const statusText = document.getElementById('vqr-status-text');
        if (statusText) statusText.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-danger me-1"></i>Đang mô phỏng xác nhận từ điện thoại...';

        try {
            const res = await fetch(`api/orders.php?action=confirm_payment`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `order_code=${encodeURIComponent(this.currentActiveOrder.order_code)}`
            });
            const data = await res.json();
            if (data.success) {
                this.onPaymentAutoSuccess(this.currentActiveOrder.order_code);
            }
        } catch(e) {
            this.showToast('Lỗi mô phỏng!', 'warning');
        }
    },

    startQRCountdown(seconds) {
        if (this.vietqrTimer) clearInterval(this.vietqrTimer);
        const timerEl = document.getElementById('vqr-countdown');
        let remain = seconds;

        const updateTimer = () => {
            const m = Math.floor(remain / 60);
            const s = remain % 60;
            if (timerEl) {
                timerEl.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
            }
            if (remain <= 0) {
                clearInterval(this.vietqrTimer);
                if (timerEl) timerEl.textContent = 'Hết hạn';
            }
            remain--;
        };

        updateTimer();
        this.vietqrTimer = setInterval(updateTimer, 1000);
    },

    copyText(text, label = 'nội dung') {
        navigator.clipboard.writeText(text).then(() => {
            this.showToast(`Đã sao chép ${label}!`, 'success');
        }).catch(() => {
            this.showToast('Không thể sao chép tự động', 'warning');
        });
    },

    async confirmPaymentManual(orderCode) {
        try {
            const res = await fetch(`api/orders.php?action=confirm_payment`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `order_code=${encodeURIComponent(orderCode)}`
            });
            const data = await res.json();
            if (data.success) {
                this.onPaymentAutoSuccess(orderCode);
            }
        } catch (e) {
            this.showToast('Lỗi xác nhận!', 'warning');
        }
    },

    showCodSuccessModal(orderData) {
        document.getElementById('cod-order-code').textContent = orderData.order_code;
        document.getElementById('cod-final-amount').textContent = orderData.final_amount_formatted;
        const modalEl = document.getElementById('modalCodSuccess');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    },

    // ==========================================
    // 5. BỘ LỌC DANH MỤC & LOẠI DA (Filter Engine)
    // ==========================================
    filterProducts(skinType = 'all', category = 'all') {
        const cards = document.querySelectorAll('.product-grid-item');
        cards.forEach(card => {
            const cardSkin = card.getAttribute('data-skin');
            const cardCat = card.getAttribute('data-category');

            let matchSkin = (skinType === 'all' || cardSkin === skinType || cardSkin === 'all');
            let matchCat = (category === 'all' || cardCat === category);

            if (matchSkin && matchCat) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    },

    // ==========================================
    // 6. HỆ THỐNG XÁC THỰC (AUTH: LOGIN, REGISTER, LOGOUT)
    // ==========================================
    async checkAuth() {
        try {
            const res = await fetch('api/auth.php?action=me');
            const data = await res.json();
            if (data.authenticated && data.user) {
                this.currentUser = data.user;
                await this.fetchUserCart();
            } else {
                this.currentUser = null;
                this.loadStorage();
            }
            this.updateAuthUI();
            this.updateCartBadge();
            this.renderCartDrawer();
        } catch(e) {
            console.error('Check auth error:', e);
            this.loadStorage();
        }
    },

    updateAuthUI() {
        const btnLogin = document.getElementById('btn-header-login');
        const dropLogged = document.getElementById('dropdown-user-logged');
        const mobileLabel = document.getElementById('mobile-nav-user-label');
        const adminLink = document.getElementById('dropdown-admin-link');

        if (this.currentUser) {
            if (btnLogin) btnLogin.style.display = 'none';
            if (dropLogged) dropLogged.style.display = 'block';

            if (this.currentUser.role === 'admin') {
                if (adminLink) adminLink.style.display = 'block';
            } else {
                if (adminLink) adminLink.style.display = 'none';
            }

            const nameEl = document.getElementById('header-user-name');
            const avatarEl = document.getElementById('header-user-avatar');
            const fullEl = document.getElementById('dropdown-user-fullname');
            const emailEl = document.getElementById('dropdown-user-email');

            const firstName = this.currentUser.fullname.split(' ').pop();
            if (nameEl) nameEl.textContent = firstName;
            if (avatarEl) avatarEl.src = this.currentUser.avatar || 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80';
            if (fullEl) fullEl.textContent = this.currentUser.fullname;
            if (emailEl) emailEl.textContent = this.currentUser.email || this.currentUser.phone || '';

            if (mobileLabel) mobileLabel.textContent = firstName;
        } else {
            if (btnLogin) btnLogin.style.display = 'flex';
            if (dropLogged) dropLogged.style.display = 'none';
            if (mobileLabel) mobileLabel.textContent = 'Tài Khoản';
        }
    },

    async handleLogin(event) {
        event.preventDefault();
        const form = document.getElementById('formLogin');
        const identifier = form.identifier.value.trim();
        const password = form.password.value.trim();
        const btn = document.getElementById('btn-submit-login');

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i>Đang đăng nhập...`;

        try {
            const res = await fetch('api/auth.php?action=login', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ identifier, password })
            });
            const data = await res.json();
            btn.disabled = false;
            btn.innerHTML = `Đăng Nhập Ngay`;

            if (data.success) {
                await this.onLoginSuccess(data.user, data.message);
                form.reset();
            } else {
                this.showToast(data.message || 'Đăng nhập không thành công', 'warning');
            }
        } catch(e) {
            btn.disabled = false;
            btn.innerHTML = `Đăng Nhập Ngay`;
            this.showToast('Lỗi kết nối máy chủ đăng nhập', 'warning');
        }
    },

    checkRegisterPasswordMatch() {
        const pwd = document.getElementById('register-pwd');
        const confirmPwd = document.getElementById('register-confirm-pwd');
        const msg = document.getElementById('register-pwd-match-msg');
        if (!pwd || !confirmPwd || !msg) return;

        if (!confirmPwd.value) {
            msg.style.display = 'none';
            return;
        }

        msg.style.display = 'block';
        if (pwd.value === confirmPwd.value) {
            msg.className = 'small mt-1 text-success font-semibold';
            msg.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i>Mật khẩu trùng khớp';
        } else {
            msg.className = 'small mt-1 text-danger font-semibold';
            msg.innerHTML = '<i class="fa-solid fa-circle-xmark me-1"></i>Mật khẩu chưa khớp nhau';
        }
    },

    async handleRegister(event) {
        event.preventDefault();
        const form = document.getElementById('formRegister');
        const fullname = form.fullname.value.trim();
        const email = form.email.value.trim();
        const phone = form.phone.value.trim();
        const password = form.password.value.trim();
        const confirmPassword = form.confirm_password ? form.confirm_password.value.trim() : '';

        if (password !== confirmPassword) {
            this.showToast('Mật khẩu nhập lại không trùng khớp!', 'warning');
            if (form.confirm_password) form.confirm_password.focus();
            return;
        }

        const btn = document.getElementById('btn-submit-register');

        btn.disabled = true;
        btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i>Đang tạo tài khoản...`;

        try {
            const res = await fetch('api/auth.php?action=register', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ fullname, email, phone, password, confirm_password: confirmPassword })
            });
            const data = await res.json();
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-sparkles me-1"></i>Đăng Ký Thành Viên`;

            if (data.success) {
                await this.onLoginSuccess(data.user, data.message);
                form.reset();
                const msg = document.getElementById('register-pwd-match-msg');
                if (msg) msg.style.display = 'none';
            } else {
                this.showToast(data.message || 'Đăng ký không thành công', 'warning');
            }
        } catch(e) {
            btn.disabled = false;
            btn.innerHTML = `<i class="fa-solid fa-sparkles me-1"></i>Đăng Ký Thành Viên`;
            this.showToast('Lỗi kết nối máy chủ đăng ký', 'warning');
        }
    },

    async logout() {
        try {
            if (this.currentUser) {
                await fetch('api/cart.php?action=save', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ cart: this.cart, coupon: this.appliedCoupon })
                }).catch(() => {});
            }

            const res = await fetch('api/auth.php?action=logout');
            const data = await res.json();

            this.currentUser = null;
            this.cart = [];
            this.appliedCoupon = null;
            this.clearGuestCart();
            this.updateCartBadge();
            this.renderCartDrawer();
            this.updateAuthUI();
            this.showToast(data.message || 'Đã đăng xuất! Giỏ hàng đã lưu an toàn trên tài khoản.', 'success');
        } catch(e) {
            this.currentUser = null;
            this.cart = [];
            this.appliedCoupon = null;
            this.clearGuestCart();
            this.updateCartBadge();
            this.renderCartDrawer();
            this.updateAuthUI();
        }
    },

    async sendOAuthRequest(provider, fullname, email, avatar) {
        try {
            const res = await fetch('api/auth.php?action=oauth', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ provider, fullname, email, avatar })
            });
            const data = await res.json();
            if (data.success) {
                await this.onLoginSuccess(data.user, data.message);
            } else {
                this.showToast(data.message || 'Lỗi đăng nhập mạng xã hội', 'warning');
            }
        } catch(e) {
            this.showToast('Lỗi kết nối mạng xã hội!', 'warning');
        }
    },

    async handleSocialRedirects() {
        // 1. Kiểm tra access_token từ Facebook OAuth Redirect trong URL hash
        if (window.location.hash && window.location.hash.includes('access_token=')) {
            try {
                const hash = window.location.hash.substring(1);
                const params = new URLSearchParams(hash);
                const accessToken = params.get('access_token');
                if (accessToken) {
                    // Xóa token trên thanh URL ngay lập tức để giữ URL sạch
                    window.history.replaceState(null, null, window.location.pathname + window.location.search);
                    this.showToast('Đang kết nối thông tin tài khoản Facebook...', 'info');

                    const fbRes = await fetch(`https://graph.facebook.com/me?fields=id,name,email,picture.width(150).height(150)&access_token=${accessToken}`);
                    const profile = await fbRes.json();

                    if (profile && profile.name) {
                        const fbName = profile.name;
                        const fbEmail = profile.email || (`fb_${profile.id}@social.alice`);
                        const fbAvatar = profile.picture?.data?.url || 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80';
                        await this.sendOAuthRequest('facebook', fbName, fbEmail, fbAvatar);
                    } else if (profile && profile.error) {
                        this.showToast('Lỗi Facebook: ' + (profile.error.message || 'Không thể lấy thông tin'), 'warning');
                    }
                }
            } catch (err) {
                console.error('Facebook redirect error:', err);
            }
        }

        // 2. Kiểm tra nếu người dùng bấm Hủy trên Facebook Dialog
        if (window.location.search && (window.location.search.includes('error=access_denied') || window.location.search.includes('error_reason=user_denied'))) {
            this.showToast('Bạn đã từ chối cấp quyền đăng nhập Facebook!', 'info');
            window.history.replaceState(null, null, window.location.pathname);
        }
    },

    async loginWithSocial(provider = 'google') {
        const isGoogle = (provider === 'google');

        // 1. NẾU LÀ GOOGLE VÀ ĐÃ CÓ GOOGLE_CLIENT_ID
        if (isGoogle && window.GOOGLE_CLIENT_ID && window.GOOGLE_CLIENT_ID.trim() !== '') {
            const clientId = window.GOOGLE_CLIENT_ID.trim();

            if (!window.google || !window.google.accounts) {
                this.showToast('Đang kết nối dịch vụ Google, vui lòng thử lại sau 1 giây...', 'info');
                return;
            }

            if (window.google.accounts.oauth2) {
                try {
                    this.showToast('Đang mở cửa sổ đăng nhập Google...', 'info');
                    const tokenClient = google.accounts.oauth2.initTokenClient({
                        client_id: clientId,
                        scope: 'email profile openid',
                        callback: async (tokenResponse) => {
                            if (tokenResponse && tokenResponse.access_token) {
                                await this.handleGoogleTokenResponse(tokenResponse.access_token);
                            } else if (tokenResponse && tokenResponse.error) {
                                console.warn('Google OAuth error:', tokenResponse);
                                if (tokenResponse.error !== 'access_denied') {
                                    this.showToast('Lỗi xác thực Google: ' + (tokenResponse.error_description || tokenResponse.error), 'warning');
                                }
                            }
                        }
                    });
                    tokenClient.requestAccessToken({ prompt: 'select_account' });
                    return;
                } catch (err) {
                    console.warn('OAuth2 Token Client initialization failed:', err);
                }
            }

            if (window.google.accounts.id) {
                google.accounts.id.initialize({
                    client_id: clientId,
                    callback: (response) => this.handleGoogleCredentialResponse(response)
                });
                google.accounts.id.prompt((notification) => {
                    if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                        console.log('Google Prompt suppressed:', notification.getNotDisplayedReason());
                    }
                });
                return;
            }
        }

        // 2. NẾU LÀ FACEBOOK VÀ ĐÃ CÓ FACEBOOK_APP_ID
        if (!isGoogle && window.FACEBOOK_APP_ID && window.FACEBOOK_APP_ID.trim() !== '') {
            const appId = window.FACEBOOK_APP_ID.trim();
            // Chuẩn hóa redirectUri luôn có dấu / ở cuối theo chuẩn Meta
            const redirectUri = window.location.origin.replace(/\/+$/, '') + '/';
            const fbOAuthUrl = `https://www.facebook.com/v19.0/dialog/oauth?client_id=${encodeURIComponent(appId)}&redirect_uri=${encodeURIComponent(redirectUri)}&response_type=token&scope=public_profile,email`;

            const redirectToFacebook = () => {
                this.showToast('Đang chuyển hướng sang đăng nhập Facebook...', 'info');
                setTimeout(() => {
                    window.location.href = fbOAuthUrl;
                }, 300);
            };

            if (typeof FB !== 'undefined') {
                this.showToast('Đang mở cửa sổ đăng nhập Facebook...', 'info');
                const startTime = Date.now();

                try {
                    FB.login((response) => {
                        const elapsed = Date.now() - startTime;
                        if (response && response.authResponse) {
                            FB.api('/me', { fields: 'name,email,picture.width(150).height(150)' }, async (profile) => {
                                if (profile && profile.name) {
                                    const fbName = profile.name;
                                    const fbEmail = profile.email || (`fb_${profile.id}@social.alice`);
                                    const fbAvatar = profile.picture?.data?.url || 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80';
                                    await this.sendOAuthRequest('facebook', fbName, fbEmail, fbAvatar);
                                } else {
                                    this.showToast('Không thể lấy thông tin trang cá nhân Facebook!', 'warning');
                                }
                            });
                        } else {
                            // Nếu callback chạy dưới 1200ms, chắc chắn trình duyệt đã chặn popup cửa sổ
                            if (elapsed < 1200) {
                                console.warn(`Popup bị trình duyệt chặn (thời gian: ${elapsed}ms). Tự động chuyển hướng sang Facebook OAuth...`);
                                redirectToFacebook();
                            } else {
                                this.showToast('Bạn đã đóng hoặc hủy đăng nhập Facebook!', 'info');
                            }
                        }
                    }, { scope: 'public_profile,email', return_scopes: true });
                } catch (err) {
                    console.error('FB.login exception:', err);
                    redirectToFacebook();
                }
                return;
            } else {
                // Nếu Facebook SDK bị trình chặn quảng cáo chặn hoặc chưa tải xong, chuyển hướng trực tiếp
                redirectToFacebook();
                return;
            }
        }

        // 3. CHẾ ĐỘ MẶC ĐỊNH / CHƯA ĐIỀN APP ID
        const defaultName = isGoogle ? 'Thu Trang (Google)' : 'Ngọc Anh (Facebook)';
        const defaultEmail = isGoogle ? 'thutrang.beauty@gmail.com' : 'ngocanh.fb@facebook.com';
        const defaultAvatar = isGoogle 
            ? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80'
            : 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&q=80';

        if (!isGoogle && (!window.FACEBOOK_APP_ID || window.FACEBOOK_APP_ID.trim() === '')) {
            this.showToast('Chưa cấu hình FACEBOOK_APP_ID trong config.php! Đang đăng nhập tài khoản mẫu...', 'info');
        } else if (isGoogle && (!window.GOOGLE_CLIENT_ID || window.GOOGLE_CLIENT_ID.trim() === '')) {
            this.showToast('Chưa cấu hình GOOGLE_CLIENT_ID trong config.php! Đang đăng nhập tài khoản mẫu...', 'info');
        } else {
            this.showToast(`Đang kết nối tài khoản ${isGoogle ? 'Google' : 'Facebook'}...`, 'info');
        }

        await this.sendOAuthRequest(provider, defaultName, defaultEmail, defaultAvatar);
    },

    async loginWithSampleSocial(provider = 'facebook') {
        const isGoogle = (provider === 'google');
        const defaultName = isGoogle ? 'Thu Trang (Google Demo)' : 'Ngọc Anh (Facebook Demo)';
        const defaultEmail = isGoogle ? 'thutrang.beauty@gmail.com' : 'ngocanh.fb@facebook.com';
        const defaultAvatar = isGoogle 
            ? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80'
            : 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&w=150&q=80';
        this.showToast(`Đang đăng nhập nhanh tài khoản ${isGoogle ? 'Google' : 'Facebook'} mẫu...`, 'info');
        await this.sendOAuthRequest(provider, defaultName, defaultEmail, defaultAvatar);
    },

    async handleGoogleTokenResponse(accessToken) {
        if (!accessToken) {
            this.showToast('Không nhận được mã xác thực từ Google!', 'warning');
            return;
        }

        this.showToast('Đang xác thực thông tin tài khoản Google của bạn...', 'info');

        try {
            const res = await fetch('api/auth.php?action=google_login', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ access_token: accessToken })
            });
            const data = await res.json();
            if (data.success) {
                await this.onLoginSuccess(data.user, data.message);
            } else {
                this.showToast(data.message || 'Xác thực Google thất bại', 'warning');
            }
        } catch(e) {
            this.showToast('Lỗi kết nối máy chủ xác thực Google', 'warning');
        }
    },

    async handleGoogleCredentialResponse(response) {
        if (!response || !response.credential) {
            this.showToast('Không nhận được mã xác thực từ Google!', 'warning');
            return;
        }

        this.showToast('Đang xác thực tài khoản Google của bạn...', 'info');

        try {
            const res = await fetch('api/auth.php?action=google_login', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ credential: response.credential })
            });
            const data = await res.json();
            if (data.success) {
                await this.onLoginSuccess(data.user, data.message);
            } else {
                this.showToast(data.message || 'Xác thực Google thất bại', 'warning');
            }
        } catch(e) {
            this.showToast('Lỗi kết nối máy chủ xác thực Google', 'warning');
        }
    },

    openAccountOrAuth() {
        if (this.currentUser) {
            const modalEl = document.getElementById('modalOrderLookup');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        } else {
            const modalEl = document.getElementById('modalAuth');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    },

    bindEvents() {
        // Bắt sự kiện click vào bất kỳ nút đóng modal nào
        document.addEventListener('click', (e) => {
            const closeBtn = e.target.closest('[data-bs-dismiss="modal"], .btn-close');
            if (closeBtn && closeBtn.closest('.modal')) {
                AliceApp.closeModal(closeBtn);
            }
        });

        // Bắt sự kiện phím ESC để thoát modal hoặc đóng drawer
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) AliceApp.closeModal(openModal);
                AliceApp.closeCartDrawer();
            }
        });

        // Skincare Routine Tab clicks
        document.querySelectorAll('.routine-tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                document.querySelectorAll('.routine-tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const skinType = btn.getAttribute('data-skin');
                this.filterProducts(skinType, 'all');
            });
        });

        // Search Input Enter
        const searchInput = document.getElementById('header-search-input');
        if (searchInput) {
            searchInput.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') {
                    const kw = searchInput.value.toLowerCase().trim();
                    const cards = document.querySelectorAll('.product-grid-item');
                    cards.forEach(card => {
                        const title = card.querySelector('.product-name').textContent.toLowerCase();
                        const brand = card.querySelector('.product-brand').textContent.toLowerCase();
                        if (title.includes(kw) || brand.includes(kw)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            });
        }
    }
};

// Khởi tạo Facebook SDK dự phòng nếu chưa khai báo ở header
if (!window.fbAsyncInit) {
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
}

// Khởi chạy khi DOM sẵn sàng
document.addEventListener('DOMContentLoaded', () => {
    AliceApp.init();
});
