<?php include '../header.php'; ?>

<?php
if (isset($_GET['id'])) {
    $file_id = $_GET['id'];

    // Check page is from the /profile/ directory
    $previous = $_SERVER['HTTP_REFERER'] ?? '';

    if (strpos($previous, "https://" . $_SERVER['HTTP_HOST'] . "/profile/") !== false) {
        $sql_file = "SELECT * FROM files WHERE id='$file_id'";
        $result_file = mysqli_query($conn, $sql_file);
        $row_file = mysqli_fetch_assoc($result_file);

        if (!$row_file) {
            echo '<div class="alert alert-danger" role="alert">File not found.</div>';
            exit();
        }

        if ($row_file['user'] != $_SESSION['user']['id']) {
            echo '<div class="alert alert-danger" role="alert">You are not allowed to delete files from this page.</div>';
            exit();
        }

        $sql = "DELETE FROM files WHERE id='$file_id'";
        $result = mysqli_query($conn, $sql);

        // Delete file from the server
        $file_path = "../" . $row_file['path'];

        // Check if file exists
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        if ($result) {
            echo '<div class="alert alert-success" role="alert">File deleted successfully.</div>';

            header("Location: " . $previous);
        } else {
            echo '<div class="alert alert-danger" role="alert">Error deleting file.</div>';
        }
    } else {
        echo '<div class="alert alert-danger" role="alert">You are not allowed to delete files from this page.</div>';
    }
}
?>

<div class="container">
    <a href="javascript:history.go(-1)" class="btn btn-primary mt-3">Go back</a>
</div>

<?php include '../footer.php'; ?>