<?php include '../header.php'; ?>

<?php

// Kiểm tra xem người dùng đã nhấn nút tải về chưa
if (isset($_GET['id'])) :
    $id = $_GET['id'] ?? '';
    echo "ID: $id";
else :
    echo "Không tìm thấy tập tin.";
endif;
?>

<?php include '../footer.php'; ?>