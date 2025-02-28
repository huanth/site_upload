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

// Login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Form validation to check xss
    $username = htmlspecialchars($username);
    $password = htmlspecialchars($password);

    $md5_password = md5($password);

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$md5_password'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        $_SESSION['user'] = $user;

        // Redirect to index 
        header('Location: /');
    } else {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Tài khoản hoặc mật khẩu không đúng.</span>
            </div>';
    }
}
?>

<div class="container mx-auto px-4 max-w-lg">
    <div class="bg-primary text-white text-center font-bold py-2 rounded-t-lg">
        ĐĂNG NHẬP
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
            <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-green-700 transition">Đăng nhập</button>

            <div class="text-center mt-4">
                <a href="/register" class="text-blue-500 hover:underline">Chưa có tài khoản? Đăng ký ngay</a>
            </div>
        </form>
    </div>
</div>

<?php include '../footer.php'; ?>