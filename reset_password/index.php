<?php
include '../header.php';

// Kiểm tra token
if (!isset($_GET['token'])) {
    die('Token không hợp lệ.');
}

$token = $_GET['token'];

// Kiểm tra token trong database
$stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die('Token đã hết hạn hoặc không hợp lệ.');
}

// Xử lý đặt lại mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if (strlen($newPassword) < 6) {
        die('Mật khẩu phải có ít nhất 6 ký tự.');
    }

    if ($newPassword !== $confirmPassword) {
        die('Mật khẩu xác nhận không khớp.');
    }

    // Mã hóa mật khẩu mới
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $email = $data['email'];

    // Cập nhật mật khẩu trong database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashedPassword, $email);
    $stmt->execute();

    // Xóa token reset mật khẩu
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded relative" role="alert">
            <strong class="font-bold">Thành công!</strong>
            <span class="block sm:inline">Mật khẩu của bạn đã được đặt lại.</span>
        </div>';

    header('Location: /login');
    exit;
}
?>

<div class="container mx-auto px-4 max-w-lg">
    <div class="bg-primary text-white text-center font-bold py-2 rounded-t-lg">
        ĐẶT LẠI MẬT KHẨU
    </div>
    <div class="bg-white p-6 rounded-b-lg shadow-md">
        <form method="POST" class="space-y-4">
            <div>
                <label for="new_password" class="block font-semibold text-gray-700">Mật khẩu mới:</label>
                <input type="password" id="new_password" name="new_password" required class="w-full p-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="confirm_password" class="block font-semibold text-gray-700">Xác nhận mật khẩu:</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="w-full p-2 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-green-700 transition">ĐỔI MẬT KHẨU</button>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>