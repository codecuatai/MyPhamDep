<?php
/**
 * API AUTHENTICATION (ĐĂNG KÝ, ĐĂNG NHẬP, ĐĂNG XUẤT) - ALICE COSMETIC
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = getDbConnection();
$action = $_GET['action'] ?? ($_POST['action'] ?? 'me');

// 1. Lấy thông tin phiên hiện tại
if ($action === 'me') {
    if (!empty($_SESSION['user'])) {
        jsonResponse(['authenticated' => true, 'user' => $_SESSION['user']]);
    } else {
        jsonResponse(['authenticated' => false, 'user' => null]);
    }
}

// 2. Đăng xuất
if ($action === 'logout') {
    unset($_SESSION['user']);
    session_destroy();
    jsonResponse(['success' => true, 'message' => 'Đã đăng xuất tài khoản thành công!']);
}

// 3. Đăng nhập
if ($action === 'login') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $identifier = trim($input['identifier'] ?? ''); // Email hoặc SĐT
    $password = trim($input['password'] ?? '');

    if (empty($identifier) || empty($password)) {
        jsonResponse(['success' => false, 'message' => 'Vui lòng nhập Email/Số điện thoại và Mật khẩu!'], 400);
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(['success' => false, 'message' => 'Tài khoản hoặc mật khẩu không chính xác!'], 401);
    }

    // Không lưu hash password vào session
    unset($user['password']);
    $_SESSION['user'] = $user;

    jsonResponse([
        'success' => true,
        'message' => 'Đăng nhập thành công! Chào mừng ' . $user['fullname'],
        'user' => $user
    ]);
}

// 4. Đăng ký
if ($action === 'register') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $fullname = cleanInput($input['fullname'] ?? '');
    $email = cleanInput($input['email'] ?? '');
    $phone = cleanInput($input['phone'] ?? '');
    $password = trim($input['password'] ?? '');
    $confirmPassword = trim($input['confirm_password'] ?? '');

    if (empty($fullname) || empty($email) || empty($password)) {
        jsonResponse(['success' => false, 'message' => 'Vui lòng điền họ tên, email và mật khẩu!'], 400);
    }

    if (!empty($confirmPassword) && $password !== $confirmPassword) {
        jsonResponse(['success' => false, 'message' => 'Mật khẩu nhập lại không trùng khớp!'], 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['success' => false, 'message' => 'Địa chỉ email không hợp lệ!'], 400);
    }

    if (strlen($password) < 6) {
        jsonResponse(['success' => false, 'message' => 'Mật khẩu phải từ 6 ký tự trở lên!'], 400);
    }

    // Kiểm tra email trùng
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR (phone != '' AND phone = ?)");
    $checkStmt->execute([$email, $phone]);
    if ($checkStmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Email hoặc Số điện thoại này đã được đăng ký!'], 400);
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $defaultAvatar = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80';

    try {
        $insertStmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, avatar) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->execute([$fullname, $email, $phone, $hashedPassword, $defaultAvatar]);
        $newUserId = $pdo->lastInsertId();

        $user = [
            'id' => $newUserId,
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
            'avatar' => $defaultAvatar,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $_SESSION['user'] = $user;

        jsonResponse([
            'success' => true,
            'message' => 'Đăng ký tài khoản ALICE Cosmetic thành công!',
            'user' => $user
        ]);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Lỗi tạo tài khoản: ' . $e->getMessage()], 500);
    }
}

// 5. Đăng nhập qua mạng xã hội (Google, Facebook)
if ($action === 'oauth') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $provider = strtolower(cleanInput($input['provider'] ?? 'google'));
    $fullname = cleanInput($input['fullname'] ?? ($provider === 'google' ? 'Khách Hàng Google' : 'Khách Hàng Facebook'));
    $email = cleanInput($input['email'] ?? '');
    $avatar = cleanInput($input['avatar'] ?? '');

    if (empty($email)) {
        $email = $provider . '_' . substr(md5(uniqid()), 0, 8) . '@social.alice';
    }

    if (empty($avatar)) {
        $avatar = ($provider === 'google')
            ? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80'
            : 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=150&q=80';
    }

    // Kiểm tra xem user đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        jsonResponse([
            'success' => true,
            'message' => 'Đăng nhập thành công với ' . ucfirst($provider) . '! Chào mừng ' . $user['fullname'],
            'user' => $user
        ]);
    } else {
        // Tạo tài khoản mới cho user mạng xã hội
        $randomPwd = password_hash(bin2hex(random_bytes(10)), PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, avatar) VALUES (?, ?, '', ?, ?)");
        $insert->execute([$fullname, $email, $randomPwd, $avatar]);
        $newId = $pdo->lastInsertId();

        $newUser = [
            'id' => $newId,
            'fullname' => $fullname,
            'email' => $email,
            'phone' => '',
            'avatar' => $avatar,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $_SESSION['user'] = $newUser;
        jsonResponse([
            'success' => true,
            'message' => 'Đăng nhập thành công với ' . ucfirst($provider) . '! Chào mừng ' . $fullname,
            'user' => $newUser
        ]);
    }
}

// 6. Đăng nhập bằng Google (Hỗ trợ cả ID Token và Access Token từ Google Identity Services)
if ($action === 'google_login') {
    $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $credential = trim($input['credential'] ?? '');
    $accessToken = trim($input['access_token'] ?? '');

    if (empty($credential) && empty($accessToken)) {
        jsonResponse(['success' => false, 'message' => 'Mã xác thực Google không hợp lệ!'], 400);
    }

    $googlePayload = null;

    if (!empty($accessToken)) {
        // Lấy thông tin user bằng Access Token thông qua endpoint UserInfo của Google
        $userInfoUrl = "https://www.googleapis.com/oauth2/v3/userinfo";
        if (function_exists('curl_init')) {
            $ch = curl_init($userInfoUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            $opts = [
                'http' => [
                    'method' => 'GET',
                    'header' => "Authorization: Bearer " . $accessToken . "\r\n"
                ]
            ];
            $context = stream_context_create($opts);
            $response = @file_get_contents($userInfoUrl, false, $context);
        }
        $googlePayload = $response ? json_decode($response, true) : null;
    } elseif (!empty($credential)) {
        // Xác thực ID Token qua Google API
        $verifyUrl = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($credential);
        $response = @file_get_contents($verifyUrl);
        
        // Fallback với cURL nếu allow_url_fopen bị tắt
        if ($response === false && function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $verifyUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            curl_close($ch);
        }

        $googlePayload = $response ? json_decode($response, true) : null;

        // Kiểm tra Client ID nếu đã cấu hình
        if (defined('GOOGLE_CLIENT_ID') && !empty(GOOGLE_CLIENT_ID)) {
            if (isset($googlePayload['aud']) && $googlePayload['aud'] !== GOOGLE_CLIENT_ID) {
                jsonResponse(['success' => false, 'message' => 'Client ID Google không khớp với cấu hình hệ thống!'], 401);
            }
        }
    }

    if (empty($googlePayload) || empty($googlePayload['email'])) {
        jsonResponse(['success' => false, 'message' => 'Xác thực Google thất bại hoặc mã token đã hết hạn!'], 401);
    }

    $email = cleanInput($googlePayload['email']);
    $fullname = cleanInput($googlePayload['name'] ?? 'Khách Hàng Google');
    $avatar = cleanInput($googlePayload['picture'] ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=150&q=80');

    // Kiểm tra xem email đã tồn tại trong DB chưa
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        if (!empty($avatar)) {
            $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$avatar, $user['id']]);
            $user['avatar'] = $avatar;
        }
        unset($user['password']);
        $_SESSION['user'] = $user;

        jsonResponse([
            'success' => true,
            'message' => 'Đăng nhập Google thành công! Chào mừng ' . $user['fullname'],
            'user' => $user
        ]);
    } else {
        $randomPwd = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO users (fullname, email, phone, password, avatar) VALUES (?, ?, '', ?, ?)");
        $insert->execute([$fullname, $email, $randomPwd, $avatar]);
        $newId = $pdo->lastInsertId();

        $newUser = [
            'id' => $newId,
            'fullname' => $fullname,
            'email' => $email,
            'phone' => '',
            'avatar' => $avatar,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $_SESSION['user'] = $newUser;

        jsonResponse([
            'success' => true,
            'message' => 'Đăng nhập và tạo tài khoản Google thành công! Chào mừng ' . $fullname,
            'user' => $newUser
        ]);
    }
}

jsonResponse(['success' => false, 'message' => 'Yêu cầu không hợp lệ'], 400);
