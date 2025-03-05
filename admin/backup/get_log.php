<?php
// header('Content-Type: text/html');  // Đảm bảo trả về HTML
header('Content-Type: application/json');  // Đảm bảo trả về JSON

$log_file = 'files/backup_log_db.txt'; // Đường dẫn tới file log

// Kiểm tra xem file log có tồn tại không
if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    echo nl2br($log_content);  // nl2br() giúp chuyển dòng mới (\n) thành <br> trong HTML
} else {
    echo "Chưa có log nào.";
}
