<?php
// Config database
include '../config/config.php';

$base_url = "https://" . $home_url . "/sitemap"; // Đường dẫn gốc của sitemap
$sitemap_file = __DIR__ . "/sitemap.xml"; // Đường dẫn lưu file sitemap.xml

// Lấy thời gian hiện tại theo UTC+7
$date = new DateTime("now", new DateTimeZone("Asia/Bangkok"));
$lastmod = $date->format("Y-m-d");

// Tạo XML pages
$sitemapIndex = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>');

// Danh sách các sitemap con
$sitemaps = [
    "sitemap-pages.xml",
    "user/sitemap.xml",
    "file/sitemap.xml"
];

// Thêm từng sitemap con vào sitemap index
foreach ($sitemaps as $sitemap) {
    $sitemapItem = $sitemapIndex->addChild('sitemap');
    $sitemapItem->addChild('loc', "$base_url/$sitemap");
    $sitemapItem->addChild('lastmod', $lastmod);
}

// Ghi XML ra file
$sitemapIndex->asXML($sitemap_file);

// XML Pages
include './generate_sitemap_pages.php';

// XML Users
include './generate_sitemap_users.php';


header('Location: /');
