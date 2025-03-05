<?php include '../../header.php'; ?>

<?php if (isset($current_login_user)) :
    if ($current_login_user['role'] == 1 || $current_login_user['role'] == 0) :

        // Check thư mục files có tồn tại không, nếu không thì tạo mới
        if (!file_exists('files')) {
            mkdir('files', 0777, true);
        }

        // Xoá file log cũ (xoá all file trong thư mục files)
        $files = glob('files/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $backup_file = 'files/backup_' . $db_name . '_' . date('Y-m-d_H-i-s') . '.sql';
        $log_file = 'files/backup_log_db.txt'; // Tạo file log

        // Mở file log
        $log_handle = fopen($log_file, 'w');
        fwrite($log_handle, "Bắt đầu tạo backup vào: " . date('Y-m-d H:i:s') . "\n");

        // Mở file để ghi backup
        $handle = fopen($backup_file, 'w');
        fwrite($log_handle, "Đang tạo backup file: $backup_file\n");

        // Lấy danh sách các bảng trong cơ sở dữ liệu
        $tables_result = $conn->query('SHOW TABLES');
        if (!$tables_result) {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi khi lấy danh sách bảng.']);
            exit;
        }

        while ($table_row = $tables_result->fetch_row()) {
            $table = $table_row[0];

            // Ghi vào log đang xử lý bảng
            fwrite($log_handle, "Đang xử lý bảng: $table\n");

            // Thêm lệnh CREATE TABLE vào file backup
            $create_table_result = $conn->query("SHOW CREATE TABLE $table");
            if (!$create_table_result) {
                echo json_encode(['status' => 'error', 'message' => "Lỗi khi tạo bảng $table."]);
                exit;
            }
            $create_table_row = $create_table_result->fetch_row();
            fwrite($handle, "\n\n" . $create_table_row[1] . ";\n\n");

            // Lấy tất cả các dữ liệu từ bảng
            $data_result = $conn->query("SELECT * FROM $table");
            while ($row = $data_result->fetch_assoc()) {
                $columns = array_keys($row);
                $values = array_map(function ($value) {
                    return "'" . $value . "'";
                }, array_values($row));

                // Thêm lệnh INSERT INTO vào file backup
                fwrite($handle, "INSERT INTO $table (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n");
            }

            // Cập nhật log sau khi xử lý xong mỗi bảng
            fwrite($log_handle, "Đã hoàn thành bảng: $table\n");
        }

        // Đóng file và kết nối
        fclose($handle);
        fclose($log_handle);
        $conn->close();

        // Trả về đường dẫn tải file backup
        $url_download = 'https://' . $home_url . '/admin/backup/' . $backup_file;
        $name_file = explode('/', $backup_file);
        $backup_file_name = end($name_file);

        $size = filesize($backup_file);

        // Format size
        $size = $size / 1024; // KB
        if ($size > 1024) {
            $size = $size / 1024; // MB
            if ($size > 1024) {
                $size = $size / 1024; // GB
                $size = round($size, 2) . ' GB';
            } else {
                $size = round($size, 2) . ' MB';
            }
        } else {
            $size = round($size, 2) . ' KB';
        }
?>
        <section class="mb-6">
            <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2 flex items-center">
                FILE BACKUP CỦA BẠN
            </h2>

            <a href="/admin" class="text-black-600">&larr; Quay lại</a>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                <div class="">
                    <div id="downloadLink" class="py-2 mt-4">
                        <a download="" id="downloadBtn" class="bg-primary text-white px-4 py-2 rounded-lg" href="<?php echo $url_download; ?>">Tải Backup</a>
                    </div>
                    <span class="text-sm text-gray-500 mt-4"><?= $backup_file_name; ?> (<?= $size; ?>)</span>
                </div>
            </div>
        </section>


    <?php else : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
            <strong class="font-bold">Lỗi!</strong>
            <span class="block sm:inline">Bạn không có quyền truy cập trang này.</span>
        </div>
    <?php endif; ?>
<?php else : ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
        <strong class="font-bold">Lỗi!</strong>
        <span class="block sm:inline">Bạn chưa đăng nhập.</span>
    </div>

    <a href="/login" class="bg-primary text-white px-4 py-2 rounded-lg">Đăng nhập</a>
<?php endif; ?>

<?php include '../../footer.php'; ?>