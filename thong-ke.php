<?php
// Check thư mục /files(nơi lưu trữ tập tin)
$dir = './files';
if (!file_exists($dir)) {
    mkdir($dir);
}

// Đếm số lượng tập tin
$files = scandir($dir);
$totalFiles = count($files) - 2;

// Tính tổng dung lượng tập tin
$totalSize = 0;
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $totalSize += filesize($dir . '/' . $file);
    }
}

// Định dạng lại dung lượng
if ($totalSize < 1024) {
    $totalSize = $totalSize . ' B';
} elseif ($totalSize < 1048576) {
    $totalSize = round($totalSize / 1024, 2) . ' KB';
} elseif ($totalSize < 1073741824) {
    $totalSize = round($totalSize / 1048576, 2) . ' MB';
} else {
    $totalSize = round($totalSize / 1073741824, 2) . ' GB';
}


// Đếm sô lượng user
$sql = "SELECT COUNT(*) AS total FROM users";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$totalUsers = $row['total'];
?>
<section class="mb-6">
    <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2">
        📊 THỐNG KÊ HỆ THỐNG
    </h2>
    <div class="bg-white p-6 rounded-lg shadow-md mx-auto border border-gray-100">

        <!-- Tổng số tập tin -->
        <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition-colors duration-300 mb-4 hover:shadow-md">
            <div class="flex items-center w-full">
                <div class="bg-blue-50 p-3 rounded-full border border-blue-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 11-4 0m2 0v6m-2-6V6a2 2 0 00-2-2H8a2 2 0 00-2 2v10a2 2 0 002 2h4a2 2 0 002-2z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-grow">
                    <p class="text-gray-700 font-medium">📂 Tổng số tập tin</p>
                    <p class="text-2xl font-semibold text-blue-600">
                        <?= $totalFiles; ?> <span class="text-gray-400 text-lg">|</span>
                        <span class="text-blue-500"><?= $totalSize; ?></span>
                    </p>
                </div>
                <div class="bg-blue-50 rounded-full p-2 hidden md:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Tổng số thành viên -->
        <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200 hover:border-green-300 transition-colors duration-300 hover:shadow-md">
            <div class="flex items-center w-full">
                <div class="bg-green-50 p-3 rounded-full border border-green-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4.992 4.992 0 006 21h12a4.992 4.992 0 00.879-3.196M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-grow">
                    <p class="text-gray-700 font-medium">👥 Tổng thành viên</p>
                    <p class="text-2xl font-semibold text-green-600">
                        <?= $totalUsers; ?> <span class="text-sm text-gray-500 ml-2">thành viên</span>
                    </p>
                </div>
                <div class="bg-green-50 rounded-full p-2 hidden md:block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>