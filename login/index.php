<?php
include '../header.php';

// Kiểm tra nếu user đã đăng nhập
if (isset($current_login_user)) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <span class="block sm:inline">Bạn đã đăng nhập.</span>
        </div>';
    header('Location: /');
    exit; // Chặn thực thi tiếp
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token không hợp lệ.');
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Kiểm tra độ dài đầu vào
    if (strlen($username) < 3 || strlen($username) > 50) {
        die('Tên tài khoản phải có độ dài từ 3 đến 50 ký tự.');
    }
    if (strlen($password) < 6) {
        die('Mật khẩu phải có ít nhất 6 ký tự.');
    }

    // Kiểm tra user trong CSDL
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Kiểm tra mật khẩu
    if ($user && password_verify($password, $user['password'])) {

        // Remove password from user data
        unset($user['password']);

        $_SESSION['user'] = $user;

        // Redirect to index 
        header('Location: /');
        exit;
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Tài khoản hoặc mật khẩu không đúng.</span>
            </div>';
    }
}

// Tạo CSRF Token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="container mx-auto px-4 max-w-lg">
    <div class="bg-primary text-white text-center font-bold py-2 rounded-t-lg">
        ĐĂNG NHẬP
    </div>
    <div class="bg-white p-6 rounded-b-lg shadow-md">
        <!-- Login with Google -->
        <a href="/login/login_google/google-login.php" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
            <i class="fab fa-google mr-2"></i>
            Đăng nhập bằng Google
        </a>

        <div id="g_id_onload"
            data-client_id="YOUR_GOOGLE_CLIENT_ID"
            data-login_uri="https://your.domain/your_login_endpoint"
            data-auto_select="true">
        </div>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <div>
                <label for="username" class="block font-semibold text-gray-700">Tài khoản:</label>
                <input type="text" id="username" name="username" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" placeholder="Nhập tài khoản">
            </div>
            <div>
                <label for="password" class="block font-semibold text-gray-700">Mật khẩu:</label>
                <input type="password" id="password" name="password" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" placeholder="********">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-green-700 transition">Đăng nhập</button>

            <div class="text-center mt-4">
                <a href="/register" class="text-blue-500 hover:underline">Chưa có tài khoản? Đăng ký ngay</a>
            </div>

            <div class="text-center mt-4">
                <a href="/forgot_passowrd" class="text-blue-500 hover:underline">Bạn quên mật khẩu? Lấy lại tại đây</a>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>