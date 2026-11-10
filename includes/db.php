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

    // 7. Bảng Users (Hệ thống thành viên Đăng ký / Đăng nhập)
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        fullname VARCHAR(150) NOT NULL,
        email VARCHAR(150) UNIQUE NOT NULL,
        phone VARCHAR(50),
        password VARCHAR(255) NOT NULL,
        avatar TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // 8. Bảng User Carts (Đồng bộ giỏ hàng cá nhân đa thiết bị)
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_carts (
        user_id INTEGER PRIMARY KEY,
        cart_json TEXT NOT NULL DEFAULT '[]',
        coupon_json TEXT DEFAULT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Tự động thêm tài khoản mẫu nếu chưa có user nào
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount == 0) {
        $stmtUser = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, avatar) VALUES (?, ?, ?, ?, ?)");
        $stmtUser->execute([
            'Ngọc Linh',
            'ngoclinh@gmail.com',
            '0987654321',
            password_hash('123456', PASSWORD_DEFAULT),
            'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80'
        ]);
    }

    // Tự động Seed Data mẫu nếu chưa có sản phẩm nào
    $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count == 0) {
        seedInitialData($pdo);
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
