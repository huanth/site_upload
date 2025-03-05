<?php

$user_ip = ''; // Khai báo biến chứa địa chỉ IP của người dùng

if (isset($current_login_user)) {

    update_user_ip($current_login_user['id']);

    $ban = check_user_is_ban($user['id']);

    if ($ban) {
        // Unban user
        unban_user($ban['id']);
    }


    // Nếu ban là admin thì đéo bao giờ bị ban
    if ($user['role'] == 1 || $user['role'] == 0) {
        $sql_unban_admin = "UPDATE ban SET is_ban = 0 WHERE user = ?";
        $stmt_unban_admin = $conn->prepare($sql_unban_admin);
        $stmt_unban_admin->bind_param("i", $user['id']);
        $stmt_unban_admin->execute();
    }
}
