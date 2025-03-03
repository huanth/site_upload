<?php include '../header.php'; ?>

<?php

if (isset($_GET['id'])) :
    $id = $_GET['id'] ?? '';
    echo "ID: $id";
else :
    echo "Không tìm thấy tập tin.";
endif;
?>

<?php include '../footer.php'; ?>