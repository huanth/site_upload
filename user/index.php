<?php include '../header.php'; ?>

<?php

// Check nếu chọn Đăng nhập bằng tài khoản user khác
if (isset($_POST['login_with'])) {
    // Check lại role
    if ($user['role'] != 1 && $user['id'] != 0) {
        header('Location: /user/' . $user['id']);
    }

    $id_user = (int) $_POST['id_user'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_login = $result->fetch_assoc();

    if ($user) {
        $_SESSION['user'] = $user_login;
        header('Location: /');
    }
}

if (isset($_GET['id'])) :

    // Check user đang xem có phải là user hiện tại không
    if (isset($user) && $user['id'] == $id) {
        header('Location: /profile');
    }

    $role = (int) $user_profile['role'];

    // Lấy thông tin vai trò
    $stmt = $conn->prepare("SELECT position FROM roles WHERE id = ?");
    $stmt->bind_param("i", $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $roleData = $result->fetch_assoc();
    $roleName = $roleData['position'] ?? 'N/A';


    // Lấy cấp bậc dựa trên EXP
    $exp = (int) $user_profile['exp'];

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
    $email = strtolower(trim($user_profile['email']));
    $hash = md5($email);
    $avatar = "https://www.gravatar.com/avatar/$hash?s=200&d=identicon";

?>
    <section class="mb-6">
        <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden mt-6 p-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800">👤 Hồ sơ cá nhân</h2>

            <div class="flex justify-center mt-4">
                <img src="<?= $avatar; ?>" alt="Avatar" class="w-24 h-24 rounded-full border-4 border-blue-500 shadow-lg">
            </div>

            <h3 class="text-xl font-semibold text-gray-800 mt-3"><?= $user_profile['username'] ?></h3>
            <p class="text-gray-600 text-sm">
                <a href="mailto:<?= $user_profile['email'] ?>" class="text-blue-500">📧 <?= $user_profile['email'] ?></a>
            </p>
            <p class="text-gray-600 text-sm">ID: <?= $user_profile['id'] ?></p>

            <div class="mt-3">
                <p class="text-gray-700 font-medium">🔹 Cấp bậc hiện tại: <span class="font-semibold text-blue-700"><?= $cap_bac; ?></span></p>
            </div>

            <?php if (isset($user)) : ?>
                <?php if ($user['role'] == 0 || $user['role'] == 1) : ?>
                    <!-- Dành cho admin và mod -->

                    <!-- Đăng nhập bằng tài khoản user khác -->
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_user" value="<?= $user_profile['id'] ?>">
                        <button type="submit" name="login_with" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition mt-4">Đăng nhập tài khoản này</button>
                    </form>

                    <!-- Ban user -->
                    <div class="flex mt-4 justify-center">
                        <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                            Ban user này
                        </button>
                    </div>

                    <!-- Change Password Modal -->
                    <div id="authentication-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative p-4 w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                                <!-- Modal header -->
                                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Ban user
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

                <?php endif; ?>
            <?php endif; ?>

        </div>
    </section>
<?php else :
    echo "Không tìm thấy người dùng";
endif;
?>

<?php include '../footer.php'; ?>