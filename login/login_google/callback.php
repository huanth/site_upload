<?php
// login_google/callback.php
require_once '../../vendor/autoload.php'; // Tải thư viện Google API Client
session_start();
require_once 'config.php';
require_once '../../config/config.php';

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);

if (isset($_GET['code'])) {
    // Nhận mã xác thực từ Google
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        // Lưu trữ token vào session
        $_SESSION['access_token'] = $token;

        // Tạo đối tượng Google OAuth2 service
        $oauth2 = new Google_Service_Oauth2($client);

        // Lấy thông tin người dùng
        $userData = $oauth2->userinfo->get();

        $username = $userData['email'];
        $email = $userData['email'];
        $role = 2;
        $exp = 0;

        // Kiểm tra xem username hoặc email đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("ss", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Kiểm tra user trong CSDL
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Remove password from user data
            unset($user['password']);

            $_SESSION['user'] = $user;

            // Redirect to index
            header('Location: /');
            exit;
        } else {
            // Thêm user vào CSDL
            $stmt = $conn->prepare("INSERT INTO users (username, email, role, exp) VALUES (?, ?, ?, 0)");
            $stmt->bind_param("ssi", $username, $email, $role);
            $success = $stmt->execute();

            if ($success) {
                $_SESSION['user'] = [
                    'id' => $stmt->insert_id,
                    'username' => $username,
                    'email' => $email,
                    'role' => $role,
                    'exp' => $exp
                ];

                // Redirect to index
                header('Location: /');
                exit;
            }


        }

        // Chuyển hướng người dùng về trang chính sau khi đăng nhập thành công
        header('Location: /');
        exit();
    } else {
        // Nếu có lỗi xảy ra, hiển thị thông báo
        echo 'Error: ' . $token['error'];
    }
} else {
    // Nếu không có mã xác thực (code), người dùng có thể chưa đăng nhập
    echo 'Error: No code received';
}
