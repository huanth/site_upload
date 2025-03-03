<?php include '../header.php'; ?>

<?php

if (isset($_GET['id'])) :

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
            <p class="text-gray-600 text-sm">📧 <?= $user_profile['email'] ?></p>
            <p class="text-gray-600 text-sm">ID: <?= $user_profile['id'] ?></p>

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

        </div>
    </section>
<?php else :
    echo "Không tìm thấy người dùng";
endif;
?>

<?php include '../footer.php'; ?>