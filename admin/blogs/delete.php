<?php include '../../header.php'; ?>

<?php
if (isset($_GET['id'])) {
    $blog_id = $_GET['id'];

    // Kiểm tra session nếu người dùng đã đăng nhập vai trò admin
    if (!isset($user['id']) && $user['role'] != 1 && $user['id'] != 0) {
        echo '<div class="alert alert-danger" role="alert">Có lỗi xảy ra.</div>';
        exit();
    }

    // Kiểm tra page là từ thư mục /blogs/
    $previous = $_SERVER['HTTP_REFERER'] ?? '';
    if (strpos($previous, "https://" . $_SERVER['HTTP_HOST'] . "/admin/blogs/") !== false) {

        // Sử dụng prepared statement để bảo vệ khỏi SQL Injection
        $stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->bind_param("i", $blog_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $blog = $result->fetch_assoc();
        $stmt->close();

        if ($blog) {
            $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
            $stmt->bind_param("i", $blog_id);
            $stmt->execute();
            $stmt->close();

            echo '<div class="alert alert-success" role="alert">Xóa blog thành công.</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Không tìm thấy blog.</div>';
        }

        header("Location: /admin/blogs");
    } else {
        echo '<div class="alert alert-danger" role="alert">You are not allowed to delete files from this page.</div>';
    }
}
?>

<div class="container">
    <a href="javascript:history.go(-1)" class="btn btn-primary mt-3">Go back</a>
</div>

<?php include '../../footer.php'; ?>