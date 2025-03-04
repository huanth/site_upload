<?php
// Start output buffering
ob_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Config database
include 'config/config.php';

// Session check
session_start();

$user_ip = ''; // Khai báo biến chứa địa chỉ IP của người dùng

if (isset($_SESSION['user'])) {
    // Update the User 
    $sql = "SELECT * FROM users WHERE id = " . $_SESSION['user']['id'];
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    $_SESSION['user'] = $user;

    // Add IP to user
    $user_ip = $_SERVER['REMOTE_ADDR']; // Lấy địa chỉ IP của người dùng


    // Kiểm tra nếu địa chỉ IP là từ phía sau proxy
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Nếu có, lấy địa chỉ IP thực sự của người dùng (thường là IP của proxy)
        $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    // Update IP
    $sql_update_ip = "UPDATE users SET ip = ? WHERE id = ?";
    $stmt_update_ip = $conn->prepare($sql_update_ip);
    $stmt_update_ip->bind_param("si", $user_ip, $user['id']);
    $stmt_update_ip->execute();

    // Check ban user để unban
    $sql_ban = "SELECT * FROM ban WHERE user = ? AND time_end < NOW() AND is_ban = 1";
    $stmt_ban = $conn->prepare($sql_ban);
    $stmt_ban->bind_param("i", $user['id']);
    $stmt_ban->execute();
    $result_ban = $stmt_ban->get_result();
    $ban = $result_ban->fetch_assoc();

    if ($ban) {
        // Unban user
        $sql_unban = "UPDATE ban SET is_ban = 0 WHERE id = ?";
        $stmt_unban = $conn->prepare($sql_unban);
        $stmt_unban->bind_param("i", $ban['id']);
        $stmt_unban->execute();
    }


    // Check ban ip để unban
    // $sql_ban_ip = "SELECT * FROM ban WHERE time_end < NOW() AND is_ban = 1 AND type_ban = 2";
    // $stmt_ban_ip = $conn->prepare($sql_ban_ip);
    // $stmt_ban_ip->execute();
    // $result_ban_ip = $stmt_ban_ip->get_result();
    // $ban_ip = $result_ban_ip->fetch_assoc();

    // // Nếu $user_ip hiện tại nằm trong danh sách ip bị ban $ban_ip
    // if ($ban_ip) {
    //     // Unban ip
    //     $sql_unban_ip = "UPDATE ban SET is_ban = 0 WHERE id = ?";
    //     $stmt_unban_ip = $conn->prepare($sql_unban_ip);
    //     $stmt_unban_ip->bind_param("i", $ban_ip['id']);
    //     $stmt_unban_ip->execute();
    // }


    // // Check ban user để ban theo IP
    // $sql_ban_user = "SELECT * FROM ban WHERE time_end > NOW() AND is_ban = 1 AND type_ban = 2";
    // $stmt_ban_user = $conn->prepare($sql_ban_user);
    // $stmt_ban_user->execute();
    // $result_ban_user = $stmt_ban_user->get_result();
    // $ban_user = $result_ban_user->fetch_assoc();

    // if ($ban_user) {
    //     // Ban IP
    //     $time_end = $ban_user['time_end'];
    //     $ban_by = $ban_user['ban_by'];
    //     $li_do = $ban_user['li_do'];

    //     $sql_ban_ip = "INSERT INTO ban (user, ip_ban, time_end, ban_by, li_do, type_ban, is_ban) VALUES (?, ?, ?, ?, ?, 2, 1)";
    //     $stmt_ban_ip = $conn->prepare($sql_ban_ip);
    //     $stmt_ban_ip->bind_param("sssis", $user['id'], $user_ip, $time_end, $ban_by, $li_do);
    //     $stmt_ban_ip->execute();
    // }


    // Nếu ban là admin thì đéo bao giờ bị ban
    if ($user['role'] == 1 || $user['role'] == 0) {
        $sql_unban_admin = "UPDATE ban SET is_ban = 0 WHERE user = ?";
        $stmt_unban_admin = $conn->prepare($sql_unban_admin);
        $stmt_unban_admin->bind_param("i", $user['id']);
        $stmt_unban_admin->execute();
    }
}


// Load config
$sql_config = "SELECT * FROM config WHERE `key` = 'home_url'";
$result_config = mysqli_query($conn, $sql_config);
$config = mysqli_fetch_assoc($result_config);

// Config
$home_url = $config['value'];

$title = "Trang Chủ | " .  $home_url . " - Web/wap upload file miễn phí";
$description = $home_url . " - Nền tảng upload file miễn phí, dễ dàng và nhanh chóng. Hỗ trợ tải lên và chia sẻ tập tin nhanh nhất.";
$keywords = "upload file miễn phí, chia sẻ file, tải lên file nhanh, lưu trữ trực tuyến";
$author = "HuanTH - " . $home_url;


$current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

if (strpos($current_url, "/admin/") !== false || strpos($current_url, "/admin") !== false) {
    $arr_path_admin = explode("/admin/", $_SERVER['REQUEST_URI']);

    if (!empty($arr_path_admin[1])) {
        // Nếu người dùng chưa đăng nhập hoặc không có quyền admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1 && $_SESSION['user']['role'] != 0) {
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
    $title = "Hồ sơ " . $user['username'] . " - " . $home_url;
    $description = "Hồ sơ " . $user['username'] . " - Nền tảng upload file miễn phí, dễ dàng và nhanh chóng. Hỗ trợ tải lên và chia sẻ tập tin nhanh nhất.";
    $keywords = $user['username'];
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

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= $description; ?>">
    <meta name="keywords" content="<?= $keywords; ?>">
    <meta name="author" content="<?= $author; ?>">
    <meta property="og:title" content="<?= $title; ?>">
    <meta property="og:description" content="<?= $description; ?>">
    <meta property="og:url" content="https://<?= $home_url; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://<?= $home_url; ?>/logo.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $title; ?>">
    <meta name="twitter:description" content="<?= $description; ?>">
    <meta name="twitter:image" content="https://<?= $home_url; ?>/logo.png">
    <title><?= $title; ?></title>
    <!-- Sử dụng Tailwind CSS thông qua CDN -->
    <link href="https://unpkg.com/tailwindcss@^2.0/dist/tailwind.min.css" rel="stylesheet">
    <link href="/chat.css" rel="stylesheet">
    <!-- Sử dụng Font Awesome cho các biểu tượng -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1486737668365116"
        crossorigin="anonymous"></script>
    <!-- Sử dụng Alpine.js cho tương tác -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer=""></script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600&amp;display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <style>
        :root {
            --bg-primary: #121212;
            /* Màu nền chính: đen nhạt */
            --text-primary: #e0e0e0;
            /* Màu chữ chính: xám nhạt */
            --bg-secondary: #1e1e1e;
            /* Màu nền phụ: xám đậm */
            --text-secondary: #b0b0b0;
            /* Màu chữ phụ: xám trung bình */
            --bg-accent: #bb86fc;
            /* Màu nhấn: tím nhạt */
            --text-accent: #bb86fc;
            /* Màu chữ nhấn: tím nhạt */
            --bg-dark: #000000;
            /* Màu nền tối: đen */
            --text-dark: #ffffff;
            /* Màu chữ tối: trắng */
            --bg-hover: #333333;
            /* Màu nền khi hover: xám đậm */
            --body-gradient-start: #1e1e1e;
            /* Gradient bắt đầu: xám đậm */
            --body-gradient-end: #121212;
            /* Gradient kết thúc: đen nhạt */
        }

        /* Sử dụng các biến màu trong CSS */
        body {
            background-color: #121212;
            /* Màu nền tối */
            color: #e0e0e0;
            /* Màu chữ sáng để tạo độ tương phản */
            font-family: Quicksand, sans-serif;
            /* Phông chữ dễ đọc */
            margin: 0;
            padding: 0;
        }

        /* Sử dụng biến màu trong CSS */
        .bg-primary {
            background-color: var(--bg-primary);
        }

        .text-primary {
            color: var(--text-primary);
        }

        .bg-secondary {
            background-color: var(--bg-secondary);
        }

        .text-secondary {
            color: var(--text-secondary);
        }

        .bg-accent {
            background-color: var(--bg-accent);
        }

        .text-accent {
            color: var(--text-accent);
        }

        .bg-dark {
            background-color: var(--bg-dark);
        }

        .text-dark {
            color: var(--text-dark);
        }

        /* Hiệu ứng hover cho danh sách */
        .list-item:hover {
            background-color: var(--bg-hover);
        }

        /* Background body với quản lý màu giao diện */
        .prose ul {
            list-style-type: disc !important;
            margin-left: 1.5rem !important;
        }

        @keyframes glowRed {
            0% {
                text-shadow: 0 0 5px #ff0000;
            }

            50% {
                text-shadow: 0 0 15px #ff0000;
            }

            100% {
                text-shadow: 0 0 5px #ff0000;
            }
        }

        @keyframes glowBlue {
            0% {
                text-shadow: 0 0 5px #0000ff;
            }

            50% {
                text-shadow: 0 0 15px #0000ff;
            }

            100% {
                text-shadow: 0 0 5px #0000ff;
            }
        }

        @keyframes glowGreen {
            0% {
                text-shadow: 0 0 5px #00ff00;
            }

            50% {
                text-shadow: 0 0 15px #00ff00;
            }

            100% {
                text-shadow: 0 0 5px #00ff00;
            }
        }

        .animate-glow-red {
            animation: glowRed 1.5s infinite alternate;
        }

        .animate-glow-blue {
            animation: glowBlue 1.5s infinite alternate;
        }

        .animate-glow-green {
            animation: glowGreen 1.5s infinite alternate;
        }
    </style>
    <style type="text/css">
        @font-face {
            font-family: 'Atlassian Sans';
            font-style: normal;
            font-weight: 400 653;
            font-display: swap;
            src:
                local('AtlassianSans'),
                local('Atlassian Sans Text'),
                url('chrome-extension://liecbddmkiiihnedobmlmillhodjkdmb/fonts/AtlassianSans-latin.woff2') format('woff2');
            unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304,
                U+0308, U+0329, U+2000-206F, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900 flex justify-center">
    <div class="container mx-auto px-4 max-w-3xl">
        <header class="bg-primary text-white p-4 shadow-md flex justify-between items-center rounded-t-lg">
            <a href="/">
                <h1 class="text-2xl font-bold"><?= $home_url; ?></h1>
            </a>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center space-x-2 bg-secondary text-white px-4 py-2 rounded-lg focus:outline-none">
                    <span>Tài khoản</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <?php
                if (isset($_SESSION['user'])) : ?>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white text-gray-700 rounded-lg shadow-lg py-2" style="display: none; z-index: 9999;">
                        <div class="flex flex-col space-y-2">
                            <a href="/profile" class="flex items-center px-4 py-2 hover:bg-gray-100 rounded-lg transition" title="Quản lý tập tin">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-5h6v5m0 0V9a2 2 0 00-2-2H7a2 2 0 00-2 2v8h10z"></path>
                                </svg>
                                <b>Trang Cá Nhân</b>
                            </a>

                            <?php if ($_SESSION['user']['role'] == 1 || $_SESSION['user']['role'] == 0) : ?>
                                <a href="/admin" class="flex items-center px-4 py-2 hover:bg-gray-100 rounded-lg transition" title="Trang quản trị">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <b>Trang Quản Trị</b>
                                </a>
                            <?php endif; ?>

                            <a href="/logout" class="flex items-center px-4 py-2 hover:bg-gray-100 rounded-lg transition text-red-500" title="Đăng xuất">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m10 4v1a3 3 0 11-6 0v-1m0-10V5a3 3 0 016 0v1"></path>
                                </svg>
                                <b>Đăng Xuất</b>
                            </a>

                        </div>


                    </div>
                <?php else : ?>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white text-gray-700 rounded-lg shadow-lg py-2" style="z-index: 9999;">
                        <div class="flex flex-col space-y-2">
                            <a href="/login" class="flex items-center px-4 py-2 hover:bg-gray-100 rounded-lg transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <b>Đăng nhập</b>
                            </a>
                            <a href="/register" class="flex items-center px-4 py-2 hover:bg-gray-100 rounded-lg transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 21v-2a4 4 0 00-8 0v2M12 11a4 4 0 100-8 4 4 0 000 8z"></path>
                                </svg>
                                <b>Đăng ký</b>
                            </a>
                        </div>


                    </div>
                <?php endif; ?>
            </div>
        </header>
        <main class="bg-white p-6 shadow-md">
            <section class="bg-gray-100 py-8 rounded-lg mb-4">
                <div class="container mx-auto px-4 max-w-2xl text-center">
                    Khu vực quảng cáo
                </div>
            </section>