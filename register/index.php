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

// Đăng ký tài khoản
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token không hợp lệ.');
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);
    $role = $_POST['role'] ?? 2;

    // Kiểm tra độ dài đầu vào
    if (strlen($username) < 3 || strlen($username) > 50) {
        die('Tên tài khoản phải có độ dài từ 3 đến 50 ký tự.');
    }
    if (strlen($password) < 6) {
        die('Mật khẩu phải có ít nhất 6 ký tự.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Email không hợp lệ.');
    }

    // Mã hóa mật khẩu an toàn
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Kiểm tra xem username hoặc email đã tồn tại chưa
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Tài khoản hoặc email đã tồn tại.</span>
            </div>';
    } else {
        // Thêm user vào CSDL
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role, exp) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("sssi", $username, $hashed_password, $email, $role);
        $success = $stmt->execute();

        if ($success) {
            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Thành công!</strong>
                <span class="block sm:inline">Đăng ký thành công.</span>
            </div>';
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Đăng ký thất bại.</span>
            </div>';
        }
    }
    $stmt->close();
}

// Tạo CSRF Token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<div class="container mx-auto px-4 max-w-lg">
    <div class="bg-primary text-white text-center font-bold py-2 rounded-t-lg">
        ĐĂNG KÝ
    </div>
    <div class="bg-white p-6 rounded-b-lg shadow-md">
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
            <div>
                <label for="username" class="block font-semibold text-gray-700">Tài khoản:</label>
                <input type="text" id="username" name="username" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" placeholder="Nhập tài khoản">
            </div>
            <div>
                <label for="password" class="block font-semibold text-gray-700">Mật khẩu:</label>
                <input type="password" id="password" name="password" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" placeholder="Nhập mật khẩu">
            </div>
            <div>
                <label for="email" class="block font-semibold text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" placeholder="Nhập email">
            </div>
            <input type="hidden" name="role" value="2">
            <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-green-700 transition">Đăng ký</button>
            <div class="text-center mt-4">
                <a href="../login" class="text-blue-500 hover:underline">Đã có tài khoản? Đăng nhập ngay</a>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>