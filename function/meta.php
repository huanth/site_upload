<?php

// Config
$home_url = home_url();

$title = "Trang Chủ | " .  $home_url . " - Web/wap upload file miễn phí";
$description = $home_url . " - Nền tảng upload file miễn phí, dễ dàng và nhanh chóng. Hỗ trợ tải lên và chia sẻ tập tin nhanh nhất.";
$keywords = "upload file miễn phí, chia sẻ file, tải lên file nhanh, lưu trữ trực tuyến";
$author = "HuanTH - " . $home_url;


$current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (strpos($current_url, "/admin/") !== false || strpos($current_url, "/admin") !== false) {
    $arr_path_admin = explode("/admin/", $_SERVER['REQUEST_URI']);

    if (!empty($arr_path_admin[1])) {
        // Nếu người dùng chưa đăng nhập hoặc không có quyền admin
        if (!isset($current_login_user) || $current_login_user['role'] != 1 && $current_login_user['role'] != 0) {
            header('Location: /admin'); // Redirect về trang /admin
            exit(); // Ngừng thực thi mã sau khi redirect
        }
    }
}

if (strpos($current_url, "/file-info") !== false) {
    $file_id = isset($_GET['id']) ? $_GET['id'] : 0;

    $sql = "SELECT * FROM files WHERE id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $file_id);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $file = mysqli_fetch_assoc($result);

            // Hiển thị thông tin tập tin
            $name = $file['name'];
            $size = $file['size'];

            // Định dạng lại kích thước file
            if ($size < 1024) {
                $size = $size . ' bytes';
            } elseif ($size < 1048576) {
                $size = round($size / 1024, 2) . ' KB';
            } else {
                $size = round($size / 1048576, 2) . ' MB';
            }

            $title = "Tải về " . $name . "(" . $size . ")";
            $description = "Tải về " . $name . "(" . $size . ") được upload tại " . $home_url;
            $keywords = $name;
        }
    }
}

if (strpos($current_url, "/profile") !== false) {
    $title = "Hồ sơ " . $current_login_user['username'] . " - " . $home_url;
    $description = "Hồ sơ " . $current_login_user['username'] . " - Nền tảng upload file miễn phí, dễ dàng và nhanh chóng. Hỗ trợ tải lên và chia sẻ tập tin nhanh nhất.";
    $keywords = $current_login_user['username'];
}

if (strpos($current_url, "/blog") !== false) {
    $sql = "SELECT * FROM blogs WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        $blog_id = $_GET['id'] ?? 0;
        mysqli_stmt_bind_param($stmt, 'i', $blog_id);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $blog = mysqli_fetch_assoc($result);

            $title = $blog['title'] . " - " . $home_url;
            $description = $blog['title'] . " - " . $home_url;
            $keywords = $blog['title'];
        }
    }
}

if (strpos($current_url, "/search_file") !== false) {
    if (isset($_GET['name'])) {
        $name = $_GET['name'] ?? '';

        $title = "Tìm kiếm: " . $name;
        $description = "Tìm kiếm: " . $name;
        $keywords = $name;
    }
}

if (strpos($current_url, "/user/") !== false && strpos($current_url, "/admin/user") == 0) {

    if (isset($_GET['id'])) :
        $id = $_GET['id'] ?? '';

        $sql = "SELECT * FROM users WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, 'i', $id);

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $user_profile = mysqli_fetch_assoc($result);

                $title = "Hồ sơ " . $user_profile['username'] . " - " . $home_url;
                $description = "Hồ sơ " . $user_profile['username'] . " - Nền tảng upload file miễn phí, dễ dàng và nhanh chóng. Hỗ trợ tải lên và chia sẻ tập tin nhanh nhất.";
                $keywords = $user_profile['username'];
            }
        }

    endif;
}
