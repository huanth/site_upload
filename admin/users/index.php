<?php include '../../header.php'; ?>

<?php if (isset($current_login_user)) :
    if ($current_login_user['role'] == 1 || $current_login_user['role'] == 0) :

        // Get all users, limit 10 users per page
        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($current_page - 1) * $limit;

        $users = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC LIMIT $limit OFFSET $offset");
        $result_users = mysqli_fetch_all($users, MYSQLI_ASSOC);

        $total_users = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users");
        $total_users = mysqli_fetch_assoc($total_users)['total'];

        $html_pagination = pagination($total_users, $current_page);

        function getRoleById($role_id)
        {
            global $conn;
            // Lấy thông tin vai trò
            $stmt = $conn->prepare("SELECT position FROM roles WHERE role_id = ?");
            $stmt->bind_param("i", $role_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $roleData = $result->fetch_assoc();
            $roleName = $roleData['position'] ?? 'N/A';

            return $roleName;
        }

        function getCapBac($exp)
        {
            global $conn;

            $stmt = $conn->prepare("SELECT cap_bac, exp FROM tu_luyen WHERE exp <= ? ORDER BY exp DESC LIMIT 1");
            $stmt->bind_param("i", $exp);
            $stmt->execute();
            $result = $stmt->get_result();
            $tu_luyen = $result->fetch_assoc();
            $cap_bac = $tu_luyen['cap_bac'] ?? 'Chưa có cấp bậc';

            return $cap_bac;
        }
?>
        <section class="mb-6">
            <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2 flex items-center">
                <i class="fas fa-folder-open text-primary mr-2"></i> THÀNH VIÊN <span class="ml-2 px-2.5 py-0.5 bg-primary/10 text-primary rounded-full text-sm"><?= $total_users; ?></span>
            </h2>

            <a href="/admin" class="text-black-600">&larr; Quay lại</a>

            <?php if (empty($result_users)) : ?>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                    <div class="text-center">
                        <p class="text-gray-500 dark:text-gray-400 mt-4">Không có thành viên nào.</p>
                    </div>
                </div>
            <?php else : ?>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($result_users as $result_user) : ?>
                            <?php
                            // Get Gravatar
                            $email = strtolower(trim($result_user['email']));
                            $hash = md5($email);
                            $avatar = "https://www.gravatar.com/avatar/$hash?s=200&d=identicon";

                            // Link profile
                            $profile_url = '/user/' . $result_user['id'];
                            ?>
                            <div class="py-3 group">
                                <div class="flex items-center justify-between gap-3 p-3 rounded-lg group-hover:bg-gray-50 dark:group-hover:bg-gray-700 transition duration-200">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                                            <img src="<?= $avatar; ?>" alt="<?= $result_user['username']; ?>" class="w-8 h-8 rounded-full">
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <a href="<?= $profile_url; ?>" class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium block transition-colors">
                                                <div class="truncate max-w-[calc(100%-20px)]" title="images.jpg"><?= $result_user['username']; ?></div>
                                            </a>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                <span class="inline-flex items-center mr-3">
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    <?= date('d/m/Y', strtotime($result_user['create_time'])); ?>
                                                </span>

                                                <span class="inline-flex items-center mr-3">
                                                    <i class="fa-solid fa-user mr-1"></i>
                                                    <?= getRoleById($result_user['role']); ?>
                                                </span>

                                                <span class="inline-flex items-center mr-3">
                                                    <i class="fa-solid fa-wand-magic mr-1"></i>
                                                    <?= getCapBac($result_user['exp']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 flex items-center space-x-2">
                                        <a href="<?= $profile_url; ?>" class="text-gray-400 hover:text-blue-500 p-2 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                            <i class="fa-solid fa-right-to-bracket"></i>
                                        </a>
                                        <a href="delete_permanent_file.php?id=<?= $file['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa thành viên này?');" class="text-gray-400 hover:text-red-500 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
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
    <?php else : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <span class="block sm:inline">Bạn không có quyền truy cập trang này.</span>
        </div>
    <?php endif; ?>
<?php else : ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
        <strong class="font-bold">Lỗi!</strong>
        <span class="block sm:inline">Bạn chưa đăng nhập.</span>
    </div>

    <a href="/login" class="bg-primary text-white px-4 py-2 rounded-lg">Đăng nhập</a>
<?php endif; ?>

<?php include '../../footer.php'; ?>