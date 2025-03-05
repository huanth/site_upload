<?php include '../header.php'; ?>

<?php
if (isset($_GET['id'])) {
    $file_id = $_GET['id'];

    // Kiểm tra session nếu người dùng đã đăng nhập
    if (!isset($current_login_user['id'])) {
        echo '<div class="alert alert-danger" role="alert">Bạn cần đăng nhập trước.</div>';
        exit();
    }

    // Kiểm tra page là từ thư mục /file-info/
    $previous = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($previous, "https://" . $_SERVER['HTTP_HOST'] . "/file-info/") !== false) {

        // Sử dụng prepared statement để bảo vệ khỏi SQL Injection
        $sql_file = "SELECT * FROM files WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql_file)) {
            mysqli_stmt_bind_param($stmt, 'i', $file_id);  // Liên kết tham số
            mysqli_stmt_execute($stmt);
            $result_file = mysqli_stmt_get_result($stmt);
            $row_file = mysqli_fetch_assoc($result_file);

            if (!$row_file) {
                echo '<div class="alert alert-danger" role="alert">File không tồn tại.</div>';
                exit();
            }

            // Kiểm tra quyền của người dùng
            if ($row_file['user'] != $current_login_user['id']) {
                if ($current_login_user['role'] != 1 && $current_login_user['role'] != 0) {
                    echo '<div class="alert alert-danger" role="alert">Bạn không có quyền xoá file này.</div>';
                    exit();
                }
            }

            // Thực hiện xóa file trong cơ sở dữ liệu
            $sql = "DELETE FROM files WHERE id = ?";
            if ($delete_stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($delete_stmt, 'i', $file_id);  // Liên kết tham số
                $delete_result = mysqli_stmt_execute($delete_stmt);

                // Xóa file trên server
                $file_path = "../" . $row_file['path'];

                // Kiểm tra nếu file tồn tại và xóa
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                if ($delete_result) {
                    echo '<div class="alert alert-success" role="alert">File đã được xoá thành công.</div>';
                    // header("Location: " . $previous);
                    // exit();  // Dừng mọi xử lý sau khi chuyển hướng
                } else {
                    echo '<div class="alert alert-danger" role="alert">Lỗi khi xoá file.</div>';
                }
            }
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">Bạn không có quyền xoá file từ trang này.</div>';
    }
}
?>

<div class="container">
    <a href="javascript:history.go(-2)" class="btn btn-primary mt-3">Trở lại</a>
</div>

<?php include '../footer.php'; ?>