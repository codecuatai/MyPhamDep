<?php
/**
 * CẤU HÌNH HỆ THỐNG - ALICE COSMETIC
 * Giao diện Tone Hồng Pastel ngọt ngào & sang trọng
 * Tự động hóa bán hàng mỹ phẩm, thanh toán VietQR & lưu Google Sheets
 */

define('APP_NAME', 'ALICE Cosmetic');
define('APP_VERSION', '2.6.6');
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Cấu hình Google OAuth 2.0 (Google Identity Services)
// Nhập Client ID lấy từ https://console.cloud.google.com/apis/credentials
define('GOOGLE_CLIENT_ID', '960032265898-lph5fv3cefc0g327k62qidrsqnnvon10.apps.googleusercontent.com'); 

// Cấu hình Facebook Login (Meta for Developers)
// Nhập App ID lấy từ https://developers.facebook.com/apps/
define('FACEBOOK_APP_ID', '885141314536567'); 

// Cấu hình Database (Mặc định SQLite - Chạy ngay với PHP không cần cài MySQL)
define('DB_DRIVER', 'sqlite'); 
define('DB_SQLITE_PATH', __DIR__ . '/database/mypham.sqlite');

// Cấu hình MySQL (nếu muốn dùng thay cho SQLite)
define('DB_HOST', 'localhost');
define('DB_NAME', 'alice_cosmetic');
define('DB_USER', 'root');
define('DB_PASS', '');

// Thông tin cửa hàng mặc định
define('SHOP_CONFIG', [
    'shop_name'     => 'ALICE Cosmetic',
    'legal_name'    => 'ALICE COSMETIC VIỆT NAM — THẾ GIỚI MỸ PHẨM CHÍNH HÃNG',
    'slogan'        => 'ALICE Cosmetic — Đánh thức vẻ đẹp rạng rỡ của làn da bạn!',
    'hotline'       => '0869.571.734',
    'zalo'          => '0869571734',
    'email'         => 'cskh@alicecosmetic.vn',
    'address'       => '175 Tây Sơn, Đống Đa, Hà Nội',
    'freeship_min'  => 299000, // Freeship từ 299.000đ
    
    // Mạng xã hội & Kênh liên hệ
    'facebook_url'  => 'https://www.facebook.com/mduong79#',
    'messenger_url' => 'https://m.me/mduong79',
    'zalo_url'      => 'https://zaloapp.com/qr/p/12waxvomw9fms?src=qr',
    'zalo_qr_image' => 'assets/images/zalo-qr.png',
    'tiktok_url'    => 'https://tiktok.com/@alicecosmetic',
    'instagram_url' => 'https://instagram.com/alicecosmetic',

    // Cấu hình VietQR mặc định (Có thể thay đổi trong modal Admin Cài Đặt)
    'bank_id'       => 'MB',
    'bank_name'     => 'MBBank - Ngân hàng TMCP Quân Đội',
    'account_no'    => '0869571734',
    'account_name'  => 'ALICE COSMETIC',
    'qr_template'   => 'qr_only',

    // Đồng bộ đơn hàng về Google Sheets (qua Google Apps Script WebApp)
    'google_sheet_webhook' => 'https://script.google.com/macros/s/AKfycbx4yXl8viaogZ-tzrm247NSQyfcIIzQKr0zqlxkSghHuctyleDjWRG2fMPKDZxX8uVD_g/exec'
]);

// Danh sách ngân hàng VietQR hỗ trợ
define('VIETQR_BANKS', [
    ['id' => 'MB',   'name' => 'MBBank - Ngân hàng TMCP Quân Đội', 'bin' => '970422'],
    ['id' => 'VCB',  'name' => 'Vietcombank - Ngoại Thương VN',     'bin' => '970436'],
    ['id' => 'TCB',  'name' => 'Techcombank - Kỹ Thương VN',       'bin' => '970407'],
    ['id' => 'BIDV', 'name' => 'BIDV - Đầu tư & Phát triển VN',    'bin' => '970418'],
    ['id' => 'ICB',  'name' => 'VietinBank - Công Thương VN',      'bin' => '970415'],
    ['id' => 'ACB',  'name' => 'ACB - Ngân hàng Á Châu',           'bin' => '970416'],
    ['id' => 'VPB',  'name' => 'VPBank - Việt Nam Thịnh Vượng',    'bin' => '970432'],
    ['id' => 'TPB',  'name' => 'TPBank - Tiên Phong',              'bin' => '970423'],
    ['id' => 'VIB',  'name' => 'VIB - Quốc Tế',                    'bin' => '970441']
]);
