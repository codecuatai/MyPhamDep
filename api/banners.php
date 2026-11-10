<?php
/**
 * API LẤY DANH SÁCH BANNER QUẢNG CÁO - ALICE COSMETIC
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();
$stmt = $pdo->query("SELECT id, title, subtitle, badge_text, image_url, link_url, button_text, sort_order FROM banners WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
$banners = $stmt->fetchAll();

jsonResponse([
    'success' => true,
    'total' => count($banners),
    'banners' => $banners
]);
