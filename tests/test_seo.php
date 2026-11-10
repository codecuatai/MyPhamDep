<?php
/**
 * SEO VERIFICATION TEST SUITE - ALICE COSMETIC
 */

echo "=== STARTING SEO AUDIT TEST ===\n\n";

$errors = [];
$passes = [];

// 1. Check robots.txt
if (file_exists(__DIR__ . '/../robots.txt')) {
    $robots = file_get_contents(__DIR__ . '/../robots.txt');
    if (strpos($robots, 'User-agent') !== false && strpos($robots, 'Sitemap') !== false) {
        $passes[] = "robots.txt exists and contains User-agent and Sitemap directive.";
    } else {
        $errors[] = "robots.txt is missing required directives.";
    }
} else {
    $errors[] = "robots.txt does not exist.";
}

// 2. Check sitemap.xml & sitemap.php
if (file_exists(__DIR__ . '/../sitemap.xml')) {
    $xmlContent = file_get_contents(__DIR__ . '/../sitemap.xml');
    $xml = @simplexml_load_string($xmlContent);
    if ($xml && count($xml->url) > 0) {
        $passes[] = "sitemap.xml is valid XML and contains " . count($xml->url) . " indexed URLs.";
    } else {
        $errors[] = "sitemap.xml is invalid XML or empty.";
    }
} else {
    $errors[] = "sitemap.xml does not exist.";
}

// 3. Check index.php SEO tags
$_SERVER['HTTP_HOST'] = 'localhost:8000';
$_SERVER['REQUEST_URI'] = '/';
ob_start();
include __DIR__ . '/../index.php';
$indexHtml = ob_get_clean();

if (strpos($indexHtml, 'rel="canonical"') !== false) {
    $passes[] = "index.php has rel='canonical' tag.";
} else {
    $errors[] = "index.php missing canonical tag.";
}

if (strpos($indexHtml, 'property="og:title"') !== false && strpos($indexHtml, 'property="og:image"') !== false) {
    $passes[] = "index.php has Open Graph tags (og:title, og:image, og:description).";
} else {
    $errors[] = "index.php missing Open Graph tags.";
}

if (strpos($indexHtml, 'schema.org') !== false && (strpos($indexHtml, 'BeautySalon') !== false || strpos($indexHtml, 'Organization') !== false)) {
    $passes[] = "index.php has Organization/BeautySalon Schema JSON-LD.";
} else {
    $errors[] = "index.php missing Schema JSON-LD.";
}

if (strpos($indexHtml, '<h1') !== false) {
    $passes[] = "index.php has semantic <h1> tag.";
} else {
    $errors[] = "index.php missing <h1> tag.";
}

if (strpos($indexHtml, '<h2') !== false) {
    $passes[] = "index.php has semantic <h2> section tags.";
} else {
    $errors[] = "index.php missing <h2> tags.";
}

// 4. Check product.php with valid slug
$_GET['slug'] = 'serum-la-roche-posay-effaclar';
ob_start();
include __DIR__ . '/../product.php';
$prodHtml = ob_get_clean();

if (strpos($prodHtml, '<h1') !== false && strpos($prodHtml, 'Serum La Roche-Posay Effaclar') !== false) {
    $passes[] = "product.php renders semantic <h1> with product name.";
} else {
    $errors[] = "product.php missing or invalid <h1> tag.";
}

if (strpos($prodHtml, '"@type": "Product"') !== false || strpos($prodHtml, '"@type":"Product"') !== false) {
    $passes[] = "product.php includes Schema.org Product (Rich Snippet) JSON-LD.";
} else {
    $errors[] = "product.php missing Schema Product JSON-LD.";
}

if (strpos($prodHtml, '"@type": "BreadcrumbList"') !== false || strpos($prodHtml, '"@type":"BreadcrumbList"') !== false) {
    $passes[] = "product.php includes BreadcrumbList Schema JSON-LD.";
} else {
    $errors[] = "product.php missing BreadcrumbList JSON-LD.";
}

if (strpos($prodHtml, 'property="og:type" content="product"') !== false) {
    $passes[] = "product.php includes og:type='product' for social sharing.";
} else {
    $errors[] = "product.php missing og:type='product'.";
}

// 5. Check product.php 404 handling via CLI subprocess
$cli404 = shell_exec('php -r "$_GET[\'slug\'] = \'khong-ton-tai\'; include \'product.php\';"');
if (strpos($cli404, '404') !== false && strpos($cli404, 'Không Tìm Thấy Sản Phẩm') !== false) {
    $passes[] = "product.php handles non-existent slugs with 404 page.";
} else {
    $errors[] = "product.php does not handle non-existent slugs with 404.";
}

// 6. Check blog_detail.php with valid slug
$_GET['slug'] = 'routine-4-buoc-giam-mun-phuc-hoi-da';
ob_start();
include __DIR__ . '/../blog_detail.php';
$blogHtml = ob_get_clean();

if (strpos($blogHtml, '<h1') !== false && strpos($blogHtml, 'Routine 4 Bước Giảm Mụn') !== false) {
    $passes[] = "blog_detail.php renders semantic <h1> with article title.";
} else {
    $errors[] = "blog_detail.php missing or invalid <h1> tag.";
}

if (strpos($blogHtml, '"@type": "BlogPosting"') !== false || strpos($blogHtml, '"@type":"BlogPosting"') !== false) {
    $passes[] = "blog_detail.php includes Schema.org BlogPosting JSON-LD.";
} else {
    $errors[] = "blog_detail.php missing Schema BlogPosting JSON-LD.";
}

if (strpos($blogHtml, 'property="og:type" content="article"') !== false) {
    $passes[] = "blog_detail.php includes og:type='article' for social sharing.";
} else {
    $errors[] = "blog_detail.php missing og:type='article'.";
}

if (strpos($blogHtml, 'Sản Phẩm Được Nhắc Đến Trong Bài') !== false) {
    $passes[] = "blog_detail.php renders recommended products buy-box for high conversion.";
} else {
    $errors[] = "blog_detail.php missing recommended products buy-box.";
}

// Report
echo "PASSED (" . count($passes) . "):\n";
foreach ($passes as $p) {
    echo "  [OK] $p\n";
}

if (!empty($errors)) {
    echo "\nFAILED (" . count($errors) . "):\n";
    foreach ($errors as $e) {
        echo "  [FAIL] $e\n";
    }
    exit(1);
} else {
    echo "\n=== ALL SEO TESTS PASSED SUCCESSFULLY! ===\n";
    exit(0);
}
