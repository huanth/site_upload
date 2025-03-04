<?php include 'header.php'; ?>

<?php
$user_ip = $_SERVER['REMOTE_ADDR']; // Lấy địa chỉ IP của người dùng

// Kiểm tra nếu địa chỉ IP là từ phía sau proxy
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    // Nếu có, lấy địa chỉ IP thực sự của người dùng (thường là IP của proxy)
    $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

echo "IP của bạn: " . $user_ip;

?>

<?php if (isset($_SESSION['user'])) : ?>
    <?php include 'upload/index.php'; ?>
<?php else : ?>
    <!-- Thông báo đăng nhập để tải lên -->
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
        <span class="block sm:inline">Hãy <a href="/login" class="text-black">đăng nhập</a> để tải lên tập tin.</span>
    </div>
<?php endif; ?>

<?php include 'blogs.php'; ?>

<?php include 'tim-kiem-file.php'; ?>

<?php include 'thong-ke.php'; ?>

<?php include 'footer.php'; ?>