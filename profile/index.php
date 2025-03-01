<?php include '../header.php'; ?>

<?php
$user = $_SESSION['user'] ?? null;

if (!$user) {
    header('Location: ../login');
    exit;
}

$role = (int) $user['role'];

// Lấy thông tin vai trò
$stmt = $conn->prepare("SELECT position FROM roles WHERE id = ?");
$stmt->bind_param("i", $role);
$stmt->execute();
$result = $stmt->get_result();
$roleData = $result->fetch_assoc();
$roleName = $roleData['position'] ?? 'N/A';


// Lấy cấp bậc dựa trên EXP
$exp = (int) $user['exp'];

$stmt = $conn->prepare("SELECT cap_bac, exp FROM tu_luyen WHERE exp <= ? ORDER BY exp DESC LIMIT 1");
$stmt->bind_param("i", $exp);
$stmt->execute();
$result = $stmt->get_result();
$tu_luyen = $result->fetch_assoc();
$cap_bac = $tu_luyen['cap_bac'] ?? 'Chưa có cấp bậc';

$stmt = $conn->prepare("SELECT cap_bac, exp FROM tu_luyen WHERE exp > ? ORDER BY exp ASC LIMIT 1");
$stmt->bind_param("i", $exp);
$stmt->execute();
$result = $stmt->get_result();
$next_cap_bac = $result->fetch_assoc();

$pro_vip = 0;
if ($next_cap_bac) {
    $exp_next = $next_cap_bac['exp'];
    $exp_current = $tu_luyen['exp'];
    $exp_diff = $exp_next - $exp_current;
    $exp_user = $exp - $exp_current;
    $percent = ($exp_user / $exp_diff) * 100;
} else {
    $percent = 100;
    $pro_vip = 1;
}


// Get Gravatar
$email = strtolower(trim($user['email']));
$hash = md5($email);
$avatar = "https://www.gravatar.com/avatar/$hash?s=200&d=identicon";

// Change password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {

    // Kiểm tra CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token không hợp lệ.');
    }

    // Lấy dữ liệu từ form và lọc dữ liệu
    $oldPassword = trim($_POST['old_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Kiểm tra mật khẩu có đủ dài không
    if (strlen($newPassword) < 6) {
        die('Mật khẩu mới phải có ít nhất 6 ký tự.');
    }

    // Kiểm tra mật khẩu xác nhận có trùng khớp không
    if ($newPassword !== $confirmPassword) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Mật khẩu xác nhận không khớp.</span>
            </div>';
    } else {
        // Lấy mật khẩu đã băm từ database
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();

        // Kiểm tra mật khẩu cũ có đúng không
        if ($userData && password_verify($oldPassword, $userData['password'])) {
            // Băm mật khẩu mới
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            // Cập nhật mật khẩu mới vào database
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $newPasswordHash, $user['id']);
            if ($stmt->execute()) {
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded relative" role="alert">
                        <strong class="font-bold">Thành công!</strong>
                        <span class="block sm:inline">Đổi mật khẩu thành công.</span>
                    </div>';
            } else {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                        <strong class="font-bold">Lỗi!</strong>
                        <span class="block sm:inline">Đổi mật khẩu thất bại.</span>
                    </div>';
            }
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                    <strong class="font-bold">Lỗi!</strong>
                    <span class="block sm:inline">Mật khẩu cũ không đúng.</span>
                </div>';
        }
    }
}

// CSRF Token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

?>

<section class="mb-6">
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden mt-6 p-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">👤 Hồ sơ cá nhân</h2>

        <div class="flex justify-center mt-4">
            <img src="<?= $avatar; ?>" alt="Avatar" class="w-24 h-24 rounded-full border-4 border-blue-500 shadow-lg">
        </div>

        <h3 class="text-xl font-semibold text-gray-800 mt-3"><?= $user['username'] ?></h3>
        <p class="text-gray-600 text-sm">📧 <?= $user['email'] ?></p>
        <p class="text-gray-600 text-sm">ID: <?= $user['id'] ?></p>

        <div class="mt-3">
            <p class="text-gray-700 font-medium">🔹 Cấp bậc hiện tại: <span class="font-semibold text-blue-700"><?= $cap_bac; ?></span></p>
            <?php if ($pro_vip == 0) : ?>
                <p class="text-gray-700 font-medium">🔸 Cấp bậc tiếp theo: <span class="font-semibold text-red-700"><?= $next_cap_bac['cap_bac']; ?> (<?= $next_cap_bac['exp']; ?> Exp )</span>
                <?php else : ?>
                <p class="text-gray-700 font-medium">Bạn đã đứng ở đỉnh cao thế gian rồi, chúc mừng bạn!</p>
            <?php endif; ?>
        </div>

        <!-- Rank Progress Bar -->
        <div class=" w-full bg-gray-200 rounded-full h-4 mt-3">
            <div class="bg-blue-500 h-4 rounded-full" style="width: <?= $percent ?>%"></div>
        </div>
        <p class="text-gray-600 text-sm mt-1">Tiến trình: <?= $percent ?>%</p>

        <!-- User Statistics -->
        <div class="grid grid-cols-2 gap-4 mt-6 text-sm">
            <div class="bg-blue-100 p-4 rounded-lg shadow-sm">
                <p class="font-medium text-gray-700">⭐ Kinh nghiệm:</p>
                <p class="text-blue-600 text-lg font-bold"><?= $exp . ' Exp' ?></p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg shadow-sm">
                <p class="font-medium text-gray-700">🏆 Quyền hạn:</p>
                <p class="text-green-600 text-lg font-bold"><?= $roleName ?></p>
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex justify-between">
            <!-- <a href="edit_profile.php" class="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow hover:bg-yellow-600 transition">Chỉnh sửa</a>-->
            <!-- Popup đổi mật khẩu -->
            <a href="javascript:void(0)" class="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow hover:bg-yellow-600 transition" onclick="document.getElementById('changePassword').style.display='block'">Đổi mật khẩu</a>
            <a href="../logout" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">Đăng xuất</a>
        </div>

        <!-- Change Password Modal -->
        <div id="changePassword" class="fixed z-10 inset-0 overflow-y-auto hidden">
            <div class="flex items" style="min-height: 100vh;">
                <div class="flex items" style="min-height: 100vh;">
                    <div class="relative p-8 w-full max-w-md m-auto bg-white rounded-md shadow-lg">
                        <div class="w-full">
                            <div class="flex items" style="min-height: 100vh;">
                                <div class="relative p-8 w-full max-w-md m-auto bg-white rounded-md shadow-lg">
                                    <div class="w-full">
                                        <div class="flex justify-between items-center">
                                            <h1 class="text-xl font-bold">Đổi mật khẩu</h1>
                                            <button onclick="document.getElementById('changePassword').style.display='none'" class="text-gray-400 hover:text-gray-600">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <form method="POST" class="mt-4">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

                                            <div class="mt-4">
                                                <label for="old_password" class="block text-sm font-medium text-gray-700">Mật khẩu cũ</label>
                                                <input type="password" name="old_password" id="old_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                            </div>
                                            <div class="mt-4">
                                                <label for="new_password" class="block text-sm font-medium text-gray-700">Mật khẩu mới</label>
                                                <input type="password" name="new_password" id="new_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                            </div>
                                            <div class="mt-4">
                                                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Xác nhận mật khẩu mới</label>
                                                <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:
                                                outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm
                                                " required>
                                            </div>
                                            <div class="mt-6">
                                                <button type="submit" name="change_password" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition">Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Get user files

$current_page = $_GET['page'] ?? 1;
$limit = 5;
$offset = ($current_page - 1) * $limit;

$sql = "SELECT * FROM files WHERE user = $user[id] ORDER BY date_create DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
$files = mysqli_fetch_all($result, MYSQLI_ASSOC);

$html_pagination = '';

$sql = "SELECT COUNT(*) FROM files WHERE user = $user[id]";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_row($result);
$total_records = $row[0];
$total_pages = ceil($total_records / $limit);

if ($total_pages > 1) {
    $prev = $current_page - 1;
    $next = $current_page + 1;

    // Hiển thị nút "Trước" nếu không phải trang đầu
    if ($current_page > 1) {
        $html_pagination .= '<a href="?page=' . $prev . '" class="flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"><i class="fas fa-chevron-left mr-1 text-xs"></i> Trước</a>';
    }

    // Nếu tổng số trang > 5, chỉ hiển thị 2 trang đầu và 2 trang cuối với dấu "..."
    if ($total_pages > 5) {
        // Hiển thị 2 trang đầu
        for ($i = 1; $i <= 2; $i++) {
            $html_pagination .= ($i == $current_page) ?
                '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                '<a href="?page=' . $i . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
        }

        // Dấu "..." ở giữa
        if ($current_page > 3) {
            $html_pagination .= '<span class="px-3 py-2">...</span>';
        }

        // Hiển thị các trang gần trang hiện tại
        for ($i = max($current_page - 1, 3); $i <= min($current_page + 1, $total_pages - 2); $i++) {
            $html_pagination .= ($i == $current_page) ?
                '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                '<a href="?page=' . $i . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
        }

        // Dấu "..." trước các trang cuối
        if ($current_page < $total_pages - 2) {
            $html_pagination .= '<span class="px-3 py-2">...</span>';
        }

        // Hiển thị 2 trang cuối
        for ($i = $total_pages - 1; $i <= $total_pages; $i++) {
            $html_pagination .= ($i == $current_page) ?
                '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                '<a href="?page=' . $i . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
        }
    } else {
        // Nếu số trang <= 5, hiển thị tất cả các trang
        for ($i = 1; $i <= $total_pages; $i++) {
            $html_pagination .= ($i == $current_page) ?
                '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                '<a href="?page=' . $i . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
        }
    }

    // Hiển thị nút "Tiếp" nếu không phải trang cuối
    if ($current_page < $total_pages) {
        $html_pagination .= '<a href="?page=' . $next . '" class="flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Tiếp <i class="fas fa-chevron-right ml-1 text-xs"></i></a>';
    }
}

?>

<section class="mb-6">
    <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2 flex items-center">
        <i class="fas fa-folder-open text-primary mr-2"></i> FILE CỦA BẠN <span class="ml-2 px-2.5 py-0.5 bg-primary/10 text-primary rounded-full text-sm"><?= $total_records; ?></span>
    </h2>

    <?php if (empty($files)) : ?>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
            <div class="text-center">
                <p class="text-gray-500 dark:text-gray-400 mt-4">Không có file nào.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php foreach ($files as $file) : ?>
                <div class="py-3 group">
                    <div class="flex items-center justify-between gap-3 p-3 rounded-lg group-hover:bg-gray-50 dark:group-hover:bg-gray-700 transition duration-200">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                                <i class="fas fa-file-image text-blue-500 dark:text-blue-400"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <?php
                                $file_url = $home_url . '/file-info/' . $file['id'];
                                ?>
                                <a href="https://<?= $file_url; ?>" class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium block transition-colors">
                                    <div class="truncate max-w-[calc(100%-20px)]" title="images.jpg"><?= $file['name']; ?></div>
                                </a>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                    <span class="inline-flex items-center mr-3">
                                        <i class="far fa-calendar-alt mr-1"></i>
                                        <?= date('d/m/Y', strtotime($file['date_create'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-shrink-0 flex items-center space-x-2">
                            <a href="https://<?= $file_url; ?>" class="text-gray-400 hover:text-blue-500 p-2 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="delete_permanent_file.php?id=<?= $file['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa file này?');" class="text-gray-400 hover:text-red-500 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="flex flex-wrap justify-center mt-6 gap-2">

            <?= $html_pagination; ?>
        </div>
    </div>
</section>

<?php include '../footer.php'; ?>