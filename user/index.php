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

// Check nếu chọn Ban user
if (isset($_POST['ban_user'])) {
    // Check lại role
    if ($current_login_user['role'] != 1 && $current_login_user['role'] != 0) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Bạn không có quyền ban user.</span>
            </div>';
    }

    $id_user_admin = (int) $_POST['id_user_admin']; // ban by
    $id_user_ban = (int) $_POST['id_user_ban']; // user
    $type_ban = (int) $_POST['type_ban']; // type ban
    $ban_time_end = $_POST['ban_time_end']; // time end
    $li_do = $_POST['li_do']; // lí do
    $is_ban = 1;
    $ip_ban = $_POST['ban_ip'] ?? '';

    if ($type_ban == 2) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Chức năng ban theo IP đang bị tạm khóa.</span>
            </div>';
        // exit();
    } else {

        $stmt = $conn->prepare("INSERT INTO ban (user, is_ban, type_ban, time_end, li_do, ban_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisss", $id_user_ban, $is_ban, $type_ban, $ban_time_end, $li_do, $id_user_admin);
        $stmt->execute();

        header('Location: /user/' . $id_user_ban);
    }
}

// Check nếu chọn Unban user
if (isset($_POST['unban_user'])) {

    // Check lại role
    if ($current_login_user['role'] != 1 && $current_login_user['role'] != 0) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Bạn không có quyền unban user.</span>
            </div>';
    }

    $id_user = (int) $_POST['id_user'];

    $stmt = $conn->prepare("UPDATE ban SET is_ban = 0 WHERE user = ?");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();

    header('Location: /user/' . $id_user);
}

// Check nếu chọn Unban IP
if (isset($_POST['unban_user_ip'])) {
    // Check lại role
    if ($current_login_user['role'] != 1 && $current_login_user['role'] != 0) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Bạn không có quyền unban user.</span>
            </div>';
    }

    $id_user = (int) $_POST['id_user'];

    $stmt = $conn->prepare("UPDATE ban SET is_ban = 0 WHERE user = ? AND type_ban = 2");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();

    header('Location: /user/' . $id_user);
}

$ban = check_user_is_ban($id);


if (isset($_GET['id'])) :

    // Check user đang xem có phải là user hiện tại không
    if (isset($user) && $current_login_user['id'] == $id) {
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

            <!-- Show ip user, danh cho admin -->
            <?php if (isset($user) && $current_login_user['role'] == 0) : ?>

                <!-- Lấy thông tin IP -->
                <div class="mt-3">
                    <p class="text-gray-700 font-medium">🔹 IP: <a href="https://whatismyipaddress.com/ip/<?= $user_profile['ip'] ?>" target="_blank" class="text-blue-700"><?= $user_profile['ip']; ?></a></p>
                </div>

                <!-- Lấy thông tin vị trí -->
                <?php if ($user_profile['ip'] != '127.0.0.1') : ?>
                    <?php
                    $ip = $user_profile['ip'];
                    $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
                    $city = $details->city;
                    $region = $details->region;
                    $country = $details->country;
                    $loc = $details->loc;
                    $org = $details->org;
                    $postal = $details->postal;
                    $timezone = $details->timezone;
                    ?>
                    <div class="mt-3">
                        <p class="text-gray-700 font-medium">🔹 Vị trí: <span class="font-semibold text-blue-700"><?= $city . ', ' . $region . ', ' . $country; ?></span></p>
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            <?php if (isset($user)) : ?>
                <?php if ($current_login_user['role'] == 0 || $current_login_user['role'] == 1) : ?>
                    <!-- Dành cho admin và mod -->

                    <!-- Đăng nhập bằng tài khoản user khác -->
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_user" value="<?= $user_profile['id'] ?>">
                        <button type="submit" name="login_with" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition mt-4">Đăng nhập tài khoản này</button>
                    </form>

                    <?php if ($current_login_user['id'] != $user_profile['id']) : ?>
                        <!-- Ban user -->
                        <?php if ($ban) : ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative mt-4" role="alert">
                                <strong class="font-bold">Lỗi!</strong>
                                <span class="block sm:inline">User này đã bị ban </span>
                                <?php if ($ban['type_ban'] == 2) : ?>
                                    <span class="block sm:inline">(ban theo ip: <?= $ban['ip_ban'] ?>)</span>
                                <?php endif; ?>
                                <br>
                                <span class="block sm:inline">Lí do: <?= $ban['li_do'] ?></span>
                            </div>

                            <span class="time_end text-sm">Hết hạn: <?= $ban['time_end'] ?></span>

                            <!-- Unban user -->
                            <form action="" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id_user" value="<?= $user_profile['id'] ?>">
                                <button type="submit" name="unban_user" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition mt-4">Unban user này</button>

                                <?php if ($ban['type_ban'] == 2) : ?>
                                    <button type="submit" name="unban_user_ip" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow hover:bg-green-600 transition mt-4">Unban IP này</button>
                                <?php endif; ?>
                            </form>

                        <?php else : ?>
                            <div class="flex mt-4 justify-center">
                                <button data-modal-target="authentication-modal" data-modal-toggle="authentication-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                                    Ban user này
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Ban user modal -->
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
                                            <input type="hidden" name="id_user_admin" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="id_user_ban" value="<?= $user_profile['id'] ?>">
                                            <input type="hidden" name="ban_ip" value="<?= $user_profile['ip'] ?>">

                                            <div class="mt-4">
                                                <label for="ban_time_end" class="block text-sm font-medium text-gray-700">Thời hạn</label>
                                                <input type="datetime-local" name="ban_time_end" id="ban_time_end" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" required>
                                            </div>

                                            <div class="mt-4">
                                                <label for="type_ban" class="block text-sm font-medium text-gray-700">Loại ban</label>
                                                <select name="type_ban" id="type_ban" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" required>
                                                    <option value="1" selected>Ban theo username</option>
                                                    <option value="2">Ban theo IP(<?= $user_profile['ip'] ?>)</option>
                                                </select>
                                            </div>

                                            <div class="mt-4">
                                                <label for="li_do" class="block text-sm font-medium text-gray-700">Lí do</label>
                                                <textarea name="li_do" id="li_do" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" required></textarea>
                                            </div>

                                            <div class="mt-6">
                                                <button type="submit" name="ban_user" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition">Ban thôi</button>
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