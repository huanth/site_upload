<?php
// login_google/callback.php
require_once '../../vendor/autoload.php'; // Tải thư viện Google API Client
session_start();
require_once 'config.php';

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

        // Lưu thông tin người dùng vào session hoặc cơ sở dữ liệu
        $_SESSION['user'] = [
            'id' => $userData['id'],
            'name' => $userData['name'],
            'email' => $userData['email'],
            'picture' => $userData['picture']
        ];

        $_SESSION['info'] = $userData;

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
