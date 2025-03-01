<?php

try {

    // Check thư mục có chưa, chưa có thì tạo
    $base_path_page = __DIR__ . "/page"; // Đường dẫn thư mục cần kiểm tra

    if (!file_exists($base_path_page)) {
        mkdir($base_path_page, 0777, true);
    }

    // Tạo XML pages
    $sitemap = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

    // Thêm trang chủ vào sitemap
    $url = $sitemap->addChild('url');
    $url->addChild('loc', "https://" . $home_url);
    $date = new DateTime("now", new DateTimeZone("Asia/Bangkok"));
    $url->addChild('lastmod', $date->format("Y-m-d\TH:i:sP"));
    $url->addChild('changefreq', 'daily');
    $url->addChild('priority', '1.0');

    // Ghi XML ra file
    $sitemap->asXML(__DIR__ . "./page/sitemap-pages.xml");
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
