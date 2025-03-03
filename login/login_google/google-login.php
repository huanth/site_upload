<?php
// login_google/google-login.php
require_once '../../vendor/autoload.php'; // Tải thư viện Google API Client

session_start();
require_once 'config.php';

// Tạo đối tượng client của Google
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope("email");

// Tạo URL để người dùng đăng nhập với Google
$authUrl = $client->createAuthUrl();

// Chuyển hướng người dùng đến URL đăng nhập của Google
header('Location: ' . $authUrl);
exit();
