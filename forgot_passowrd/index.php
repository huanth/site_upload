<?php
include '../header.php';

// Kiểm tra nếu user đã đăng nhập
if (isset($current_login_user)) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <span class="block sm:inline">Bạn đã đăng nhập.</span>
        </div>';
    header('Location: /');
    exit;
}

// Xử lý yêu cầu quên mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Email không hợp lệ.</span>
            </div>';
    } else {
        // Kiểm tra xem email có trong CSDL không
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Tạo token đặt lại mật khẩu
            $token = bin2hex(random_bytes(50));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour")); // Token hết hạn sau 1 giờ

            // Lưu token vào CSDL
            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
            $stmt->bind_param("sss", $email, $token, $expires);
            $stmt->execute();

            // Gửi email chứa link đặt lại mật khẩu
            $reset_link = "https://" . $home_url . "/reset_password?token=$token";
            $subject = "Khôi phục mật khẩu của bạn";
            $message = "Bạn đã yêu cầu đặt lại mật khẩu.\n\nHãy nhấp vào liên kết dưới đây để đặt lại mật khẩu:\n$reset_link\n\nLưu ý: Liên kết này sẽ hết hạn sau 1 giờ.\n";
            $message .= "Nếu không phải bạn yêu cầu reset mật khẩu, hãy bỏ qua tin nhắn này";

            $headers = "From: no-reply@" . $home_url . "\r\n";

            mail($email, $subject, $message, $headers);

            echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded relative" role="alert">
                    <strong class="font-bold">Thành công!</strong>
                    <span class="block sm:inline">Email đặt lại mật khẩu đã được gửi.</span>
                </div>';
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                    <strong class="font-bold">Lỗi!</strong>
                    <span class="block sm:inline">Email không tồn tại trong hệ thống.</span>
                </div>';
        }
    }
}
?>

<div class="container mx-auto px-4 max-w-lg">
    <div class="bg-primary text-white text-center font-bold py-2 rounded-t-lg">
        LẤY LẠI MẬT KHẨU
    </div>
    <div class="bg-white p-6 rounded-b-lg shadow-md">
        <form method="POST" class="space-y-4">
            <div>
                <label for="email" class="block font-semibold text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" placeholder="Nhập email">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-green-700 transition">GỬI MAIL</button>
            <div class="text-center mt-4">
                <a href="../login" class="text-blue-500 hover:underline">Đã nhớ lại mật khẩu? Đăng nhập ngay</a>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>