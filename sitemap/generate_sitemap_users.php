<?php

try {

    // Check thư mục có chưa, chưa có thì tạo
    $base_path_page = __DIR__ . "/user"; // Đường dẫn thư mục cần kiểm tra

    if (!file_exists($base_path_page)) {
        mkdir($base_path_page, 0777, true);
    }

    $users_sql = "SELECT * FROM users ORDER BY create_time DESC";
    $result = mysqli_query($conn, $users_sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $sitemap_users = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

    // Thêm user profile
    foreach ($users as $user) {
        $url = $sitemap_users->addChild('url');
        $url->addChild('loc', "https://" . $home_url . "/user/" . htmlspecialchars($user['id']));
        $url->addChild('lastmod', date('Y-m-d', strtotime($user['create_time'])));
        $url->addChild('changefreq', 'monthly');
        $url->addChild('priority', '0.5');
    }

    // Ghi ra file sitemap.xml
    $sitemap_users->asXML(__DIR__ . "/user/sitemap.xml");

    echo "Sitemap generated successfully!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
