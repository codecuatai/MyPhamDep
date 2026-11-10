<?php
/**
 * KẾT NỐI DATABASE VÀ KHỞI TẠO TỰ ĐỘNG - ALICE COSMETIC
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/seed_data.php';

function getDbConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        if (DB_DRIVER === 'sqlite') {
            $dbDir = dirname(DB_SQLITE_PATH);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }
            $pdo = new PDO('sqlite:' . DB_SQLITE_PATH);
        } else {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
        }

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Khởi tạo bảng nếu chưa có
        initDatabaseTables($pdo);

        return $pdo;
    } catch (PDOException $e) {
        die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
    }
}

function initDatabaseTables(PDO $pdo) {
    // 1. Bảng Categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        icon VARCHAR(100),
        image TEXT,
        count INT DEFAULT 0,
        description TEXT
    )");

    // 2. Bảng Products
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL,
        brand VARCHAR(150),
        category_id VARCHAR(50),
        price DECIMAL(12,2) NOT NULL,
        original_price DECIMAL(12,2) NOT NULL,
        discount_percent INT DEFAULT 0,
        rating DECIMAL(3,2) DEFAULT 5.0,
        review_count INT DEFAULT 0,
        sold_count INT DEFAULT 0,
        image TEXT NOT NULL,
        gallery TEXT,
        description TEXT,
        ingredients TEXT,
        how_to_use TEXT,
        is_flash_sale INT DEFAULT 0,
        flash_sale_stock INT DEFAULT 100,
        flash_sale_sold INT DEFAULT 0,
        skin_type VARCHAR(50) DEFAULT 'all',
        variants TEXT,
        add_on_deals TEXT
    )");

    // 3. Bảng Coupons
    $pdo->exec("CREATE TABLE IF NOT EXISTS coupons (
        code VARCHAR(50) PRIMARY KEY,
        discount_type VARCHAR(20) DEFAULT 'fixed',
        discount_value DECIMAL(12,2) NOT NULL,
        min_order DECIMAL(12,2) DEFAULT 0,
        max_discount DECIMAL(12,2) DEFAULT 0,
        description TEXT
    )");

    // 4. Bảng Orders
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_code VARCHAR(50) UNIQUE NOT NULL,
        customer_name VARCHAR(150) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        address TEXT NOT NULL,
        note TEXT,
        payment_method VARCHAR(50) DEFAULT 'vietqr',
        total_amount DECIMAL(12,2) NOT NULL,
        discount_amount DECIMAL(12,2) DEFAULT 0,
        final_amount DECIMAL(12,2) NOT NULL,
        coupon_code VARCHAR(50),
        items_json TEXT NOT NULL,
        payment_status VARCHAR(50) DEFAULT 'pending',
        sync_status VARCHAR(50) DEFAULT 'pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 5. Bảng Reviews
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INT NOT NULL,
        author_name VARCHAR(150) NOT NULL,
        rating INT DEFAULT 5,
        skin_type VARCHAR(100),
        comment TEXT NOT NULL,
        created_at DATE
    )");

    // 6. Bảng Settings
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        `key` VARCHAR(100) PRIMARY KEY,
        `value` TEXT
    )");

    // 7. Bảng Users (Hệ thống thành viên Đăng ký / Đăng nhập & Phân quyền)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        fullname VARCHAR(150) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        phone VARCHAR(50),
        password VARCHAR(255) NOT NULL,
        avatar TEXT,
        role VARCHAR(20) DEFAULT 'customer',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Đảm bảo cột role tồn tại nếu DB đã tạo từ trước
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'customer'");
    } catch (Exception $e) {
        // Cột đã tồn tại, bỏ qua
    }

    // 8. Bảng User Carts (Đồng bộ giỏ hàng cá nhân đa thiết bị)
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_carts (
        user_id INTEGER PRIMARY KEY,
        cart_json TEXT NOT NULL DEFAULT '[]',
        coupon_json TEXT DEFAULT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 9. Bảng Banners (Quản lý Banner quảng cáo & Slider Trang chủ)
    $pdo->exec("CREATE TABLE IF NOT EXISTS banners (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(255) NOT NULL,
        subtitle VARCHAR(255),
        badge_text VARCHAR(100) DEFAULT 'ƯU ĐÃI ĐẶC BIỆT',
        image_url TEXT NOT NULL,
        link_url TEXT DEFAULT '#san-pham-section',
        button_text VARCHAR(100) DEFAULT 'Khám Phá Ngay',
        sort_order INT DEFAULT 0,
        is_active INT DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 10. Bảng Posts (Cẩm nang làm đẹp & Bài viết chuẩn SEO)
    $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        category VARCHAR(100) DEFAULT 'Skincare',
        excerpt TEXT,
        content TEXT NOT NULL,
        image TEXT,
        author VARCHAR(150) DEFAULT 'Chuyên Gia Da Liễu ALICE',
        reading_time INT DEFAULT 5,
        related_products TEXT DEFAULT '[]',
        views INT DEFAULT 150,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Đảm bảo các cột Quản lý Tồn kho & Chi tiết mỹ phẩm tồn tại trong bảng products
    $productNewCols = [
        'stock_quantity' => 'INT DEFAULT 100',
        'origin'         => "VARCHAR(150) DEFAULT 'Chính hãng nhập khẩu'",
        'capacity'       => "VARCHAR(100) DEFAULT 'Tiêu chuẩn'",
        'expiry'         => "VARCHAR(100) DEFAULT '36 tháng kể từ NSX (12 tháng sau mở nắp)'"
    ];
    foreach ($productNewCols as $col => $colDef) {
        try {
            $pdo->exec("ALTER TABLE products ADD COLUMN $col $colDef");
        } catch (Exception $e) {
            // Cột đã tồn tại
        }
    }

    // Tự động thêm Banner mẫu nếu bảng banners đang trống
    $bannerCount = (int)$pdo->query("SELECT COUNT(*) FROM banners")->fetchColumn();
    if ($bannerCount === 0) {
        $sampleBanners = [
            [
                'title' => 'Vẻ Đẹp Rạng Ngời Từ Làn Da Thuần Khiết',
                'subtitle' => 'Bộ sưu tập mỹ phẩm skincare & makeup chuẩn Hàn, cam kết chính hãng 100%, freeship từ 299k kèm hộp quà nơ hồng.',
                'badge_text' => '🌸 MỪNG KHAI TRƯƠNG ALICE',
                'image_url' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1200&q=80',
                'link_url' => '#san-pham-section',
                'button_text' => 'Khám Phá Sản Phẩm',
                'sort_order' => 1
            ],
            [
                'title' => 'Săn Flash Sale Mỹ Phẩm Giảm Tới 50%',
                'subtitle' => 'Serum phục hồi B5, kem chống nắng phổ rộng & son kem lì chính hãng giá sốc mỗi ngày.',
                'badge_text' => '⚡ FLASH SALE CHỚP NHOÁNG',
                'image_url' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?auto=format&fit=crop&w=1200&q=80',
                'link_url' => '#flash-sale-section',
                'button_text' => 'Săn Deal Ngay',
                'sort_order' => 2
            ],
            [
                'title' => 'Set Quà Tặng Pink Box Cao Cấp',
                'subtitle' => 'Combo Skincare đóng hộp nơ hồng pastel sang trọng, tặng kèm nến thơm & thiệp viết tay ý nghĩa.',
                'badge_text' => '🎁 QUÀ TẶNG YÊU THƯƠNG',
                'image_url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80',
                'link_url' => '#combo-routine',
                'button_text' => 'Xem Set Quà',
                'sort_order' => 3
            ]
        ];
        $bStmt = $pdo->prepare("INSERT INTO banners (title, subtitle, badge_text, image_url, link_url, button_text, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        foreach ($sampleBanners as $b) {
            $bStmt->execute([$b['title'], $b['subtitle'], $b['badge_text'], $b['image_url'], $b['link_url'], $b['button_text'], $b['sort_order']]);
        }
    }

    // Tự động thêm tài khoản khách hàng mẫu nếu chưa có user nào
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount == 0) {
        $stmtUser = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, avatar, role) VALUES (?, ?, ?, ?, ?, 'customer')");
        $stmtUser->execute([
            'Ngọc Linh',
            'ngoclinh@gmail.com',
            '0987654321',
            password_hash('123456', PASSWORD_DEFAULT),
            'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80'
        ]);
    }

    // Tự động khởi tạo tài khoản Quản Trị Viên (Admin)
    $adminCheck = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $adminCheck->execute(['admin@alicecosmetic.vn']);
    if (!$adminCheck->fetch()) {
        $adminStmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, avatar, role) VALUES (?, ?, ?, ?, ?, 'admin')");
        $adminStmt->execute([
            'Quản Trị Viên ALICE',
            'admin@alicecosmetic.vn',
            '0869571734',
            password_hash('Admin@123', PASSWORD_DEFAULT),
            'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=150&q=80'
        ]);
    }

    // Tự động Seed Data mẫu nếu chưa có sản phẩm nào
    $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count == 0) {
        seedInitialData($pdo);
    }

    // Tự động thêm bài viết cẩm nang chuẩn SEO mẫu nếu chưa có bài nào
    try {
        $postCount = (int)$pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
        if ($postCount == 0) {
            seedInitialPosts($pdo);
        }
    } catch (Exception $e) {
        // Bỏ qua lỗi nếu bảng đang khởi tạo
    }
}

function seedInitialData(PDO $pdo) {
    // Seed Categories
    $categories = getInitialCategories();
    $catStmt = $pdo->prepare("INSERT OR REPLACE INTO categories (id, name, slug, icon, image, count, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($categories as $c) {
        $catStmt->execute([$c['id'], $c['name'], $c['slug'], $c['icon'], $c['image'], $c['count'], $c['description']]);
    }

    // Seed Products
    $products = getInitialProducts();
    $prodStmt = $pdo->prepare("INSERT INTO products 
        (id, name, slug, brand, category_id, price, original_price, discount_percent, rating, review_count, sold_count, image, gallery, description, ingredients, how_to_use, is_flash_sale, flash_sale_stock, flash_sale_sold, skin_type, variants, add_on_deals) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $p) {
        $prodStmt->execute([
            $p['id'], $p['name'], $p['slug'], $p['brand'], $p['category_id'],
            $p['price'], $p['original_price'], $p['discount_percent'], $p['rating'],
            $p['review_count'], $p['sold_count'], $p['image'], $p['gallery'],
            $p['description'], $p['ingredients'], $p['how_to_use'],
            $p['is_flash_sale'], $p['flash_sale_stock'], $p['flash_sale_sold'],
            $p['skin_type'], $p['variants'], $p['add_on_deals']
        ]);
    }

    // Seed Coupons
    $coupons = getInitialCoupons();
    $cpnStmt = $pdo->prepare("INSERT OR REPLACE INTO coupons (code, discount_type, discount_value, min_order, max_discount, description) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($coupons as $cp) {
        $cpnStmt->execute([$cp['code'], $cp['discount_type'], $cp['discount_value'], $cp['min_order'], $cp['max_discount'], $cp['description']]);
    }

    // Seed Reviews
    $reviews = getInitialReviews();
    $revStmt = $pdo->prepare("INSERT INTO reviews (product_id, author_name, rating, skin_type, comment, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($reviews as $r) {
        $revStmt->execute([$r['product_id'], $r['author_name'], $r['rating'], $r['skin_type'], $r['comment'], $r['created_at']]);
    }
}

/**
 * SEED DỮ LIỆU CẨM NANG LÀM ĐẸP CHUẨN SEO
 */
function seedInitialPosts(PDO $pdo) {
    $posts = [
        [
            'title' => 'Routine 4 Bước Giảm Mụn Phục Hồi Hàng Rào Bảo Vệ Da Cho Người Mới',
            'slug' => 'routine-4-buoc-giam-mun-phuc-hoi-da',
            'category' => 'Skincare',
            'reading_time' => 5,
            'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=800&q=80',
            'related_products' => json_encode([1, 4, 11]),
            'excerpt' => 'Làm sạch dịu nhẹ, cân bằng ẩm với B5 và bảo vệ tuyệt đối trước tia UV là chìa khóa vàng giúp phục hồi làn da dầu mụn nhạy cảm chỉ sau 4 tuần.',
            'content' => '<p class="lead">Khi mới bắt đầu chăm sóc da mụn, sai lầm phổ biến nhất của các bạn là lạm dụng quá nhiều hoạt chất treatment mạnh (như BHA, Retinol, AHA nồng độ cao) trong khi hàng rào bảo vệ da đang bị tổn thương nghiêm trọng. Kết quả là da đỏ rát, mụn viêm bùng phát nhiều hơn.</p>

<h2>1. Nguyên nhân khiến da dầu mụn dễ tổn thương</h2>
<p>Làn da có một lớp màng lipid tự nhiên (Skin Barrier) bảo vệ khỏi vi khuẩn <em>C.acnes</em> và bụi mịn. Khi lớp màng này suy yếu do rửa mặt quá kỹ hoặc dùng sản phẩm có cồn/hương liệu nồng độ cao, da sẽ mất nước và phản ứng bằng cách tiết nhiều dầu hơn để bù đắp, dẫn đến bít tắc lỗ chân lông.</p>

<h2>2. Routine 4 Bước Tối Giản Nhưng Hiệu Quả Chuẩn Y Khoa</h2>

<h3>Bước 1: Làm sạch kép dịu nhẹ (Double Cleansing)</h3>
<p>Dù không trang điểm, bạn vẫn bắt buộc phải tẩy trang mỗi tối để loại bỏ kem chống nắng kháng nước và bã nhờn. Ưu tiên dòng micellar water dịu nhẹ không cồn kết hợp cùng sữa rửa mặt có độ pH chuẩn 5.5.</p>

<h3>Bước 2: Cân bằng và Phục hồi với Tinh chất Vitamin B5 & Rau má</h3>
<p>Vitamin B5 (Panthenol) kết hợp chiết xuất Centella Asiatica giúp làm dịu nốt mụn sưng viêm, kích thích sản sinh nguyên bào sợi và đẩy nhanh quá trình liền sẹo. Sau bước làm sạch, thoa 3-4 giọt serum phục hồi lên nền da còn ẩm.</p>

<h3>Bước 3: Khóa ẩm củng cố hàng rào bảo vệ da</h3>
<p>Nhiều bạn có quan niệm sai lầm: "Da dầu mụn thì không cần kem dưỡng". Thiếu ẩm là lý do chính khiến da tiết dầu không kiểm soát. Hãy lựa chọn kem dưỡng dạng gel hoặc kem phục hồi chứa Madecassoside và Niacinamide giúp làm dịu và phục hồi màng ẩm tự nhiên.</p>

<h3>Bước 4: Bảo vệ tuyệt đối trước tia UV vào ban ngày</h3>
<p>Tia cực tím UVA/UVB sẽ khiến các vết thâm sau mụn trở nên sậm màu và phá hủy liên kết collagen. Luôn thoa kem chống nắng phổ rộng có chỉ số SPF 50+ PA++++ mỗi sáng trước khi ra ngoài 20 phút.</p>

<blockquote>
  <p><strong>Lời khuyên từ chuyên gia ALICE:</strong> Kiên trì thực hiện đều đặn routine tối giản này trong 3 đến 4 tuần trước khi nghĩ đến việc thêm các hoạt chất treatment đặc trị. Một làn da khỏe là tiền đề của một làn da sạch mụn!</p>
</blockquote>'
        ],
        [
            'title' => 'Sai Lầm Khi Dùng Kem Chống Nắng Khiến Da Bị Lên Mụn Ẩn Mà Nhiều Người Mắc',
            'slug' => 'sai-lam-dung-kem-chong-nang-len-mun-an',
            'category' => 'Chống nắng',
            'reading_time' => 4,
            'image' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&w=800&q=80',
            'related_products' => json_encode([2, 4, 13]),
            'excerpt' => 'Không tẩy trang kỹ cuối ngày, bôi lượng kem chưa đủ chuẩn hoặc chọn sai kết cấu màng lọc là nguyên nhân hàng đầu khiến mụn ẩn bùng phát liên tục.',
            'content' => '<p class="lead">Kem chống nắng là bước quan trọng nhất trong mọi routine skincare. Tuy nhiên, rất nhiều bạn phàn nàn rằng "cứ bôi kem chống nắng là bị mọc mụn ẩn li ti ở cằm và trán". Dưới đây là 3 sai lầm cốt tử mà 90% người dùng mắc phải.</p>

<h2>1. Sai lầm 1: Chỉ rửa mặt bằng nước hoặc sữa rửa mặt, bỏ qua bước tẩy trang</h2>
<p>Hầu hết các dòng kem chống nắng thế hệ mới hiện nay (đặc biệt là dạng sữa chống thấm nước) đều chứa các gốc polyme bám rất chặt trên da để kháng trôi mồ hôi. Sữa rửa mặt thông thường chỉ có thể làm sạch bụi bẩn trên bề mặt, hoàn toàn không thể hòa tan hết các màng lọc UV. Lớp cặn này tích tụ ngày qua ngày trong lỗ chân lông sẽ sinh ra mụn ẩn cứng đầu.</p>

<h2>2. Sai lầm 2: Bôi kem chống nắng trực tiếp lên da chưa ráo dưỡng</h2>
<p>Sau khi thoa toner và serum, nếu bạn vội vàng thoa kem chống nắng ngay khi dưỡng chất chưa kịp thẩm thấu, hai lớp này sẽ bị vón cục (pilling), tạo thành hỗn hợp dính nhớp bít tắc cổ nang lông. Hãy chờ từ 3 - 5 phút cho các bước dưỡng ráo mịn hoàn toàn rồi mới tán kem chống nắng.</p>

<h2>3. Sai lầm 3: Chọn sai kết cấu (Texture) cho loại da của mình</h2>
<ul>
  <li><strong>Da dầu nhờn:</strong> Tránh xa các sản phẩm có gốc dầu khoáng (Mineral Oil) nặng hoặc chỉ số dưỡng ẩm quá cao. Nên chọn dạng sữa (Milk/Fluid) mỏng nhẹ, có chứa các hạt phấn kiềm dầu Silica.</li>
  <li><strong>Da khô thiếu nước:</strong> Ưu tiên dạng kem (Cream) hoặc gel bổ sung Hyaluronic Acid và Ceramide để ngăn ngừa tình trạng mốc phấn (cakey).</li>
</ul>

<h2>4. Cách khắc phục nhanh chóng</h2>
<p>Hãy bắt đầu thói quen tẩy trang sạch sẽ mỗi tối bằng nước tẩy trang Micellar chuyên dụng, thoa đủ lượng 2 đốt ngón tay cho mặt và cổ, vỗ nhẹ thay vì chà xát mạnh.</p>'
        ],
        [
            'title' => 'Top 3 Tone Son MLBB Hồng Đào Tôn Da Sáng Răng Hot Nhất Năm 2026',
            'slug' => 'top-3-tone-son-mlbb-hong-dao-ton-da',
            'category' => 'Trang điểm',
            'reading_time' => 3,
            'image' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?auto=format&fit=crop&w=800&q=80',
            'related_products' => json_encode([3, 14, 16]),
            'excerpt' => 'Sắc son tự nhiên My Lips But Better vừa tôn làn da châu Á vừa tạo nét tươi tắn trẻ trung cho các bạn học sinh sinh viên và quý cô công sở.',
            'content' => '<p class="lead">Xu hướng makeup Clean Girl và phong cách trong trẻo Douyin Hàn Quốc vẫn tiếp tục thống trị năm 2026. Những tone son đỏ thuần hay hồng cánh sen chói lóa đã nhường chỗ cho trào lưu son MLBB (My Lips But Better) – tự nhiên như môi thật nhưng tràn đầy sức sống.</p>

<h2>1. Rom&nd Zero Velvet Tint – Tone Hồng Đào MLBB Khí Chất</h2>
<p>Dòng son huyền thoại của nhà Rom&nd ghi điểm tuyệt đối nhờ chất son xốp mịn như bông, làm mờ rãnh môi hoàn hảo mà không hề gây khô căng. Tone hồng đào pha chút đất nhẹ cực kỳ tôn men răng và phù hợp với cả tone da ngăm lẫn da trắng sáng.</p>

<h2>2. 3CE Velvet Lip Tint – Sắc Hồng San Hô Pha Nude Thanh Lịch</h2>
<p>Nếu bạn đang tìm kiếm một thỏi son dùng đi học, đi làm hàng ngày mà không sợ bị thầy cô hay sếp để ý, các tone nude san hô của 3CE chính là sự lựa chọn số 1. Chất son mềm mượt lướt êm, tạo hiệu ứng môi căng mọng tự nhiên.</p>

<h2>3. Bí quyết đánh son MLBB chuẩn idol Hàn Quốc</h2>
<ul>
  <li><strong>Tẩy tế bào chết môi 2 lần/tuần:</strong> Giúp son lên màu chuẩn xác và không bị đọng vảy da chết.</li>
  <li><strong>Thoa một lớp mặt nạ ngủ môi mỏng trước khi makeup:</strong> Sử dụng mặt nạ ngủ môi Laneige trước 10 phút, sau đó dùng khăn giấy lau nhẹ rồi thoa son.</li>
  <li><strong>Tán viền môi mờ ảo (Blur Lip):</strong> Dùng đầu ngón tay hoặc cọ tán nhẹ viền môi ra ngoài để tạo hiệu ứng môi đầy đặn, quyến rũ.</li>
</ul>'
        ]
    ];

    $stmt = $pdo->prepare("INSERT OR REPLACE INTO posts (title, slug, category, reading_time, image, related_products, excerpt, content) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($posts as $p) {
        $stmt->execute([
            $p['title'], $p['slug'], $p['category'], $p['reading_time'],
            $p['image'], $p['related_products'], $p['excerpt'], $p['content']
        ]);
    }
}
