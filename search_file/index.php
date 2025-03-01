<?php include '../header.php'; ?>

<?php
// Kiểm tra xem người dùng đã nhấn nút tải về chưa
if (isset($_GET['name'])) :
    $name = $_GET['name'] ?? '';
    echo "Tìm kiếm file: $name";
else :
    echo "Không tìm thấy tập tin.";
endif;
?>

<?php include '../footer.php'; ?>