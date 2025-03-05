<?php include '../header.php'; ?>

<?php
$user = $current_login_user ?? null;

if (!$user) {
    header('Location: ../login');
    exit;
}

$ban = check_user_is_ban($user['id']);

$role = (int) $user['role'];

// Lấy thông tin vai trò
$stmt = $conn->prepare("SELECT position FROM roles WHERE role_id = ?");
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

<?php if ($ban) : ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
        <strong class="font-bold">Lỗi!</strong>
        <span class="block sm:inline">Tài khoản của bạn đã bị khóa đến <?= date('d/m/Y H:i:s', strtotime($ban['time_end'])); ?>.</span> bởi <span class="italic"><?= get_username_by_id($ban['ban_by']); ?></span>
        <br>
        <span class="block sm:inline">Lý do: <span class="italic"><?= $ban['li_do']; ?></span></span>
    </div>
<?php endif; ?>

<section class="mb-6">
    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden mt-6 p-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800">👤 Hồ sơ cá nhân</h2>

        <div class="flex justify-center mt-4">
            <img src="<?= $avatar; ?>" alt="Avatar" class="w-24 h-24 rounded-full border-4 border-blue-500 shadow-lg">
        </div>

        <h3 class="text-xl font-semibold text-gray-800 mt-3"><?= $user['username'] ?></h3>
        <p class="text-gray-600 text-sm">
            <a href="mailto:<?= $user['email'] ?>" class="text-blue-500">📧 <?= $user['email'] ?></a>
        </p>
        <p class="text-gray-600 text-sm">ID: <?= $user['id'] ?></p>

        <div class="mt-3">
            <p class="text-gray-700 font-medium">🔹 Cấp bậc hiện tại: <span class="font-semibold text-blue-700"><?= $cap_bac; ?></span></p>
            <?php if ($pro_vip == 0) : ?>
                <p class="text-gray-700 font-medium">🔸 Cấp bậc tiếp theo: <span class="font-semibold text-red-700"><?= $next_cap_bac['cap_bac']; ?> (<?= $next_cap_bac['exp']; ?> Exp )</span>
                <?php else : ?>
                <p class="text-gray-700 font-medium">Bạn đã đứng ở đỉnh cao thế gian rồi, chúc mừng bạn!</p>
            <?php endif; ?>
        </div>

        <!-- Show ip user, danh cho admin -->
        <?php if (isset($user) && $user['role'] == 0) : ?>
            <div class="mt-3">
                <p class="text-gray-700 font-medium">🔹 IP: <a href="https://whatismyipaddress.com/ip/<?= $user['ip'] ?>" target="_blank" class="text-blue-700"><?= $user['ip']; ?></a></p>
            </div>
        <?php endif; ?>

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
            <!-- <a href="javascript:void(0)" class="bg-yellow-500 text-white px-4 py-2 rounded-lg shadow hover:bg-yellow-600 transition" onclick="document.getElementById('changePassword').style.display='block'">Đổi mật khẩu</a> -->
            <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                Đổi mật khẩu
            </button>


            <a href="../logout" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition">Đăng xuất</a>
        </div>

        <!-- Change Password Modal -->
        <div id="authentication-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-md max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                            Đổi mật khẩu
                        </h3>
                        <button type="button" class="end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="authentication-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <div class="p-4 md:p-5">
                        <form class="space-y-4" action="" method="POST">
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
</section>

<?php
// Get user files
$current_page = $_GET['page'] ?? 1;
$offset = ($current_page - 1) * $limit;

$sql = "SELECT * FROM files WHERE user = $user[id] ORDER BY date_create DESC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
$files = mysqli_fetch_all($result, MYSQLI_ASSOC);

$sql = "SELECT COUNT(*) FROM files WHERE user = $user[id]";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_row($result);
$total_records = $row[0];

$html_pagination = pagination($total_records, $current_page);

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
    <?php else : ?>

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
    <?php endif; ?>
</section>

<?php include '../footer.php'; ?>