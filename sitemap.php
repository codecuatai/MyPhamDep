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

header('Content-Type: application/xml; charset=utf-8');

$output = regenerateSitemapXml($pdo, $baseUrl, true);
echo $output ?: '';
exit;
