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
