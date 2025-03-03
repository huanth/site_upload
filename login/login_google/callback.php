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
    $authCode = $_GET['code'];

    try {
        $token = $client->fetchAccessTokenWithAuthCode($authCode);

        // Kiểm tra nếu có lỗi xảy ra
        if (isset($token['error'])) {
            echo 'Error: ' . $token['error'];
            exit();
        }

        // Lưu trữ token vào session
        $_SESSION['access_token'] = $token;

        // Tạo đối tượng Google OAuth2 service
        $oauth2 = new Google_Service_Oauth2($client);

        // Lấy thông tin người dùng
        $userData = $oauth2->userinfo->get();

        // Kiểm tra xem thông tin người dùng có hợp lệ không
        if ($userData && isset($userData['id'])) {
            $username = $userData['email'];
            $email = $userData['email'];
            $password = rand(0, 1000000);
            $role = 2;
            $exp = 0;

            // Kiểm tra xem username hoặc email đã tồn tại chưa
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
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
            } else {
                // Thêm user vào CSDL
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, role, exp, password) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssiss", $username, $email, $role, $exp, $hashed_password);
                $success = $stmt->execute();

                if ($success) {
                    $_SESSION['user'] = [
                        'id' => $stmt->insert_id,
                        'username' => $username,
                        'email' => $email,
                        'role' => $role,
                        'exp' => $exp
                    ];
                }
            }
        } else {
            echo "Không thể lấy thông tin người dùng.";
            exit();
        }

        // Redirect to index
        header('Location: /');
    } catch (Exception $e) {
        // Nếu có lỗi, hiển thị thông báo
        echo 'Lỗi: ' . $e->getMessage();
        exit();
    }
} else {
    // Nếu không có mã xác thực (code), người dùng có thể chưa đăng nhập
    echo 'Lỗi: Mã xác thực không có sẵn';
}
