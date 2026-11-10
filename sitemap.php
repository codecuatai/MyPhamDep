<?php
/**
 * DYNAMIC SITEMAP GENERATOR - ALICE COSMETIC
 * Xuất sơ đồ trang web chuẩn XML theo quy chuẩn Google & sitemaps.org
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$pdo = getDbConnection();
$baseUrl = getBaseUrl();

// Lấy danh mục
$categories = $pdo->query("SELECT slug FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_COLUMN);

// Lấy toàn bộ sản phẩm
$products = $pdo->query("SELECT slug, id FROM products ORDER BY id DESC")->fetchAll();

header('Content-Type: application/xml; charset=utf-8');

$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"></urlset>');

// 1. Trang chủ
$urlNode = $xml->addChild('url');
$urlNode->addChild('loc', htmlspecialchars($baseUrl . '/'));
$urlNode->addChild('lastmod', date('Y-m-d'));
$urlNode->addChild('changefreq', 'daily');
$urlNode->addChild('priority', '1.0');

// 2. Danh mục sản phẩm
foreach ($categories as $catSlug) {
    if (empty($catSlug)) continue;
    $urlNode = $xml->addChild('url');
    $urlNode->addChild('loc', htmlspecialchars($baseUrl . '/danh-muc/' . $catSlug));
    $urlNode->addChild('lastmod', date('Y-m-d'));
    $urlNode->addChild('changefreq', 'weekly');
    $urlNode->addChild('priority', '0.8');
}

// 3. Chi tiết từng sản phẩm
foreach ($products as $prod) {
    if (empty($prod['slug'])) continue;
    $urlNode = $xml->addChild('url');
    $urlNode->addChild('loc', htmlspecialchars($baseUrl . '/san-pham/' . $prod['slug']));
    $urlNode->addChild('lastmod', date('Y-m-d'));
    $urlNode->addChild('changefreq', 'daily');
    $urlNode->addChild('priority', '0.9');
}

$output = $xml->asXML();

// Lưu bản sao tĩnh sitemap.xml ở thư mục gốc để tương thích mọi webserver
@file_put_contents(__DIR__ . '/sitemap.xml', $output);

echo $output;
exit;
