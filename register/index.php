<?php include '../header.php'; ?>

<?php
// Check if user is logged in
if (isset($_SESSION['user'])) {

    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <span class="block sm:inline">Bạn đã đăng nhập.</span>
        </div>';

    header('Location: /');
}

// Register
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'] ?? 2;

    // Form validation to check xss
    $username = htmlspecialchars($username);
    $password = htmlspecialchars($password);
    $email = htmlspecialchars($email);
    $role = htmlspecialchars($role);

    $md5_password = md5($password);

    // Check if username or email already exists
    $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Tài khoản hoặc email đã tồn tại.</span>
            </div>';
    } else {

        $sql = "INSERT INTO users (username, password, email, role) VALUES ('$username', '$md5_password', '$email', '$role')";
        $result = mysqli_query($conn, $sql);

        if ($result) {
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
}

?>

<div class="container mx-auto px-4 max-w-lg">
    <div class="bg-primary text-white text-center font-bold py-2 rounded-t-lg">
        ĐĂNG KÝ
    </div>
    <div class="bg-white p-6 rounded-b-lg shadow-md">
        <!-- Hiển thị thông báo lỗi nếu có -->

        <form method="POST" class="space-y-4">
            <div>
                <label for="username" class="block font-semibold text-gray-700">Tài khoản:</label>
                <input type="text" id="username" name="username" required="" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300">
            </div>
            <div>
                <label for="password" class="block font-semibold text-gray-700">Mật khẩu:</label>
                <input type="password" id="password" name="password" required="" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300">
            </div>
            <div>
                <label for="email" class="block font-semibold text-gray-700">Email:</label>
                <input type="email" id="email" name="email" required="" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300">
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