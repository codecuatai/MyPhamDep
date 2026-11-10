<?php
/**
 * HELPER FUNCTIONS CHO ALICE COSMETIC
 */

function formatVND($number) {
    return number_format((float)$number, 0, ',', '.') . '₫';
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function generateOrderCode() {
    return 'ALICE-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
}

function getShopConfig($pdo = null) {
    $config = SHOP_CONFIG;
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT `key`, `value` FROM settings");
            $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            if ($rows) {
                $config = array_merge($config, $rows);
            }
        } catch (Exception $e) {
            // Keep default
        }
    }
    return $config;
}

function cleanInput($data) {
    if (is_array($data)) {
        return array_map('cleanInput', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * ====================================================
 * CÁC HÀM HỖ TRỢ CHUẨN SEO (SEO HELPER FUNCTIONS)
 * ====================================================
 */

/**
 * Lấy URL gốc của website (hỗ trợ cả localhost, sub-folder và domain chính)
 */
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') {
        $basePath = '';
    } else {
        $basePath = rtrim($scriptDir, '/');
    }
    
    return rtrim($protocol . $host . $basePath, '/');
}

/**
 * Chuyển chuỗi tiếng Việt có dấu thành Slug chuẩn SEO (URL-friendly)
 */
function slugify($str) {
    if (!$str) return '';
    $unicode = [
        'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd' => 'đ',
        'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i' => 'í|ì|ỉ|ĩ|ị',
        'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
        'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D' => 'Đ',
        'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
        'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    ];
    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    $str = preg_replace('/[^a-zA-Z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return strtolower(trim($str, '-'));
}

/**
 * Cắt ngắn văn bản mô tả phục vụ thẻ meta description (chuẩn 150-160 ký tự)
 */
function truncateSeoDescription($text, $limit = 160) {
    $text = strip_tags((string)$text);
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    
    $len = function_exists('mb_strlen') ? mb_strlen($text, 'UTF-8') : strlen($text);
    if ($len <= $limit) {
        return $text;
    }
    
    if (function_exists('mb_substr') && function_exists('mb_strrpos')) {
        $sub = mb_substr($text, 0, $limit - 3, 'UTF-8');
        $lastSpace = mb_strrpos($sub, ' ', 0, 'UTF-8');
        if ($lastSpace !== false) {
            $sub = mb_substr($sub, 0, $lastSpace, 'UTF-8');
        }
        return $sub . '...';
    } else {
        $sub = substr($text, 0, $limit - 3);
        $lastSpace = strrpos($sub, ' ');
        if ($lastSpace !== false) {
            $sub = substr($sub, 0, $lastSpace);
        }
        return $sub . '...';
    }
}

/**
 * Tạo dữ liệu có cấu trúc Schema JSON-LD cho Cửa hàng / Tổ chức
 */
function generateShopJsonLd($shopConfig, $baseUrl) {
    return [
        "@context" => "https://schema.org",
        "@type" => "BeautySalon",
        "name" => $shopConfig['shop_name'] ?? 'ALICE Cosmetic',
        "legalName" => $shopConfig['legal_name'] ?? ($shopConfig['shop_name'] ?? 'ALICE Cosmetic'),
        "url" => $baseUrl,
        "logo" => $baseUrl . "/assets/images/logo.png",
        "image" => "https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=700&q=80",
        "description" => $shopConfig['slogan'] ?? 'Mỹ Phẩm & Skincare Chuẩn Hàn',
        "telephone" => $shopConfig['hotline'] ?? '',
        "email" => $shopConfig['email'] ?? '',
        "address" => [
            "@type" => "PostalAddress",
            "streetAddress" => $shopConfig['address'] ?? '175 Tây Sơn',
            "addressLocality" => "Đống Đa",
            "addressRegion" => "Hà Nội",
            "addressCountry" => "VN"
        ],
        "priceRange" => "100.000đ - 1.500.000đ",
        "sameAs" => array_values(array_filter([
            $shopConfig['facebook_url'] ?? null,
            $shopConfig['tiktok_url'] ?? null,
            $shopConfig['instagram_url'] ?? null,
            $shopConfig['zalo_url'] ?? null
        ]))
    ];
}

/**
 * Tạo dữ liệu có cấu trúc Schema JSON-LD cho Sản phẩm (Rich Snippet Google)
 */
function generateProductJsonLd($product, $shopConfig, $baseUrl) {
    $rating = !empty($product['rating']) ? (float)$product['rating'] : 5.0;
    $reviewCount = !empty($product['review_count']) ? (int)$product['review_count'] : max(1, (int)($product['sold_count'] / 5));
    if ($reviewCount < 1) $reviewCount = 5;

    $price = (float)$product['price'];
    $imageUrl = (strpos($product['image'], 'http') === 0) ? $product['image'] : ($baseUrl . '/' . ltrim($product['image'], '/'));
    $productUrl = $baseUrl . '/san-pham/' . $product['slug'];

    $schema = [
        "@context" => "https://schema.org/",
        "@type" => "Product",
        "name" => $product['name'],
        "image" => [ $imageUrl ],
        "description" => truncateSeoDescription($product['description'] ?: ($product['name'] . ' - Mỹ phẩm chính hãng từ ALICE Cosmetic')),
        "sku" => "ALICE-SKU-" . $product['id'],
        "mpn" => (string)$product['id'],
        "brand" => [
            "@type" => "Brand",
            "name" => !empty($product['brand']) ? $product['brand'] : ($shopConfig['shop_name'] ?? 'ALICE Cosmetic')
        ],
        "offers" => [
            "@type" => "Offer",
            "url" => $productUrl,
            "priceCurrency" => "VND",
            "price" => $price,
            "priceValidUntil" => date('Y-12-31', strtotime('+1 year')),
            "itemCondition" => "https://schema.org/NewCondition",
            "availability" => "https://schema.org/InStock",
            "seller" => [
                "@type" => "Organization",
                "name" => $shopConfig['shop_name'] ?? 'ALICE Cosmetic'
            ]
        ],
        "aggregateRating" => [
            "@type" => "AggregateRating",
            "ratingValue" => number_format($rating, 1, '.', ''),
            "bestRating" => "5",
            "worstRating" => "1",
            "ratingCount" => (string)$reviewCount
        ]
    ];

    return $schema;
}

/**
 * Tạo dữ liệu Breadcrumb Schema
 */
function generateBreadcrumbJsonLd($items, $baseUrl) {
    $list = [];
    $pos = 1;
    foreach ($items as $name => $url) {
        $fullUrl = (strpos($url, 'http') === 0) ? $url : ($baseUrl . '/' . ltrim($url, '/'));
        $list[] = [
            "@type" => "ListItem",
            "position" => $pos++,
            "name" => $name,
            "item" => $fullUrl
        ];
    }
    return [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $list
    ];
}
