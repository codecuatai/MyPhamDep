<?php
/**
 * Smoke Test cho CI Pipeline
 * Kiểm tra kết nối DB, nạp dữ liệu mẫu và các hàm cốt lõi
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

echo "=== KIỂM TRA HỆ THỐNG TRƯỚC KHI DEPLOY ===\n";

// 1. Kiểm tra kết nối CSDL
$pdo = getDbConnection();
if (!$pdo) {
    echo "❌ LỖI: Không thể kết nối cơ sở dữ liệu.\n";
    exit(1);
}
echo "✅ Kết nối cơ sở dữ liệu thành công.\n";

// 2. Kiểm tra bảng Categories
$catCount = $pdo->query("SELECT count(*) FROM categories")->fetchColumn();
echo "✅ Bảng Categories: {$catCount} danh mục.\n";

// 3. Kiểm tra bảng Products
$prodCount = $pdo->query("SELECT count(*) FROM products")->fetchColumn();
echo "✅ Bảng Products: {$prodCount} sản phẩm.\n";

// 4. Kiểm tra bảng Coupons
$couponCount = $pdo->query("SELECT count(*) FROM coupons")->fetchColumn();
echo "✅ Bảng Coupons: {$couponCount} mã giảm giá.\n";

if ($catCount == 0 || $prodCount == 0) {
    echo "❌ LỖI: Dữ liệu mẫu chưa được nạp đúng.\n";
    exit(1);
}

echo "🎉 TẤT CẢ KIỂM TRA ĐỀU VƯỢT QUA! SẴN SÀNG DEPLOY.\n";
exit(0);
