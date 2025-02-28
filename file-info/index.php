<?php include '../header.php'; ?>

<?php

// Kiểm tra xem người dùng đã nhấn nút tải về chưa
if (isset($_POST['down'])) {
    $password = $_POST['password'] ?? '';
    $file_id = $_POST['file_id'];

    // Kiểm tra xem người dùng đã nhập mật khẩu chưa
    $sql = "SELECT * FROM files WHERE id = ? AND password = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Gắn giá trị tham số vào câu truy vấn
        mysqli_stmt_bind_param($stmt, 'is', $file_id, $password);

        // Thực thi câu truy vấn
        mysqli_stmt_execute($stmt);

        // Lấy kết quả truy vấn
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $file = mysqli_fetch_assoc($result);

            $path = $file['path'];

            // Tải tập tin về
            $file_path = '../' . $path;
            $file_name = basename($file_path);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
            header('Content-Length: ' . filesize($file_path));

            readfile($file_path);
            exit;
        } else {
            echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg text-sm">Mật khẩu không chính xác.</div>';
        }

        // Đóng câu lệnh đã chuẩn bị
        mysqli_stmt_close($stmt);
    } else {
        echo 'Database query failed.';
    }
} else {
}

// Kiểm tra nếu có ID trong URL
if (isset($_GET['id'])) :

    $id = $_GET['id']; // Lấy ID từ URL

    // Kiểm tra xem id có phải là số hay không
    if (!is_numeric($id)) {
        echo "Invalid ID.";
        exit;
    }

    // Sử dụng câu truy vấn chuẩn bị để bảo vệ khỏi SQL Injection
    $sql = "SELECT * FROM files WHERE id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Gắn giá trị tham số vào câu truy vấn
        mysqli_stmt_bind_param($stmt, 'i', $id);

        // Thực thi câu truy vấn
        mysqli_stmt_execute($stmt);

        // Lấy kết quả truy vấn
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) :
            $file = mysqli_fetch_assoc($result);

            // Hiển thị thông tin tập tin
            $name = $file['name'];
            $size = $file['size'];

            // Định dạng lại kích thước file
            if ($size < 1024) {
                $size = $size . ' bytes';
            } elseif ($size < 1048576) {
                $size = round($size / 1024, 2) . ' KB';
            } else {
                $size = round($size / 1048576, 2) . ' MB';
            }

            $type = $file['type'];
            $path = $file['path'];
            $uploaded_at = date('d-m-Y H:i:s', strtotime($file['date_create'])); // Định dạng lại thời gian
            $user_uploaded = $file['user'];

            // Lấy thông tin người dùng đã tải lên file
            $sql = "SELECT * FROM users WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // Gắn giá trị tham số vào câu truy vấn
                mysqli_stmt_bind_param($stmt, 'i', $user_uploaded);

                // Thực thi câu truy vấn
                mysqli_stmt_execute($stmt);

                // Lấy kết quả truy vấn
                $result = mysqli_stmt_get_result($stmt);

                $user = mysqli_fetch_assoc($result);
                $user_uploaded = $user['username'];
            }

            $password = $file['password'];

            // Thay đổi title trang web ở header
            $title = 'Tải về ' . htmlspecialchars($name);
            echo "<script>document.title = '$title';</script>"; // Thay đổi title trang

            // Check xem nếu file là ảnh thì hiển thị nút xem trước
            if (strpos($type, 'image') !== false) {
                $preview = true;

                $pathPreview = 'https://' . $home_url . '/' . htmlspecialchars($path);

                $base64img = base64_encode(file_get_contents($pathPreview));
                $type = pathinfo($pathPreview, PATHINFO_EXTENSION);
                $pathPreview = 'data:image/' . $type . ';base64,' . $base64img;
            } else {
                $preview = false;
            }
?>

            <div class="copy bg-primary text-white p-3 rounded-t-lg text-center font-bold">
                THÔNG TIN TẬP TIN
            </div>

            <div class="tp1 bg-white p-4 rounded-b-lg shadow-md">
                <div class="list4 font-semibold">Tên tập tin: <span class="font-normal"><?= htmlspecialchars($name); ?></span></div>
                <div class="list4 font-semibold">Dung lượng: <span class="font-normal"><?= $size; ?></span></div>
                <div class="list4 font-semibold">Thời gian tải lên: <span class="font-normal"><?= $uploaded_at; ?></span></div>
                <div class="list4 font-semibold">Upload bởi thành viên: <span class="font-normal text-red-700"><?= $user_uploaded; ?></span></div>

                <?php if ($preview == true) : ?>
                    <div class="list4 text-center mt-3">
                        <p>Hình ảnh xem trước:</p>
                        <img src="<?= $pathPreview; ?>" alt="Preview" class=" mt-3" id="previewImage" style="margin-left: auto; margin-right: auto;">
                    </div>
                <?php endif; ?>

                <?php if (!empty($password)) : ?>
                    <div class="glist mt-4">
                        <form method="POST">
                            <input type="hidden" name="file_id" value="<?= $id; ?>">

                            <div class="list4">
                                <label for="password" class="font-semibold">Mật khẩu:</label>
                                <input type="password" id="password" name="password" required="" class="block w-full p-2 border border-gray-300 rounded mt-2"><br>
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg text-sm">
                                    <span class="block">* Tập tin này yêu cầu mật khẩu để tải về.</span>
                                </div>
                            </div>

                            <button class="buttonDownload bg-green-600 text-white px-4 py-2 rounded-lg mt-3 hover:bg-green-700 transition" name="down">Tải Về</button>
                        </form>
                        <br>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg text-sm">
                            <strong class="font-bold">LƯU Ý:</strong>
                            <span class="block">* <?= $home_url; ?> không chịu trách nhiệm về nội dung tải lên.</span>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="glist mt-4">
                        <form method="POST">
                            <input type="hidden" name="file_id" value="<?= $id; ?>">

                            <button class="buttonDownload bg-green-600 text-white px-4 py-2 rounded-lg mt-3 hover:bg-green-700 transition" name="down">Tải Về</button>
                        </form>
                        <br>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg text-sm">
                            <strong class="font-bold">LƯU Ý:</strong>
                            <span class="block">* <?= $home_url; ?> không chịu trách nhiệm về nội dung tải lên.</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="copy bg-primary text-white p-3 mt-6 rounded-t-lg text-center font-bold">CHIA SẺ</div>

            <div class="tp1 bg-white p-4 rounded-b-lg shadow-md">
                <div class="btm">
                    <label class="font-semibold">BBCode:</label>
                    <input type="text" value="[url=<?= 'https://' . $home_url; ?>/file-info/<?= $id; ?>]Tải về <?= $name; ?>[/url]" class="w-full p-2 border border-gray-300 rounded mt-2" readonly="">
                </div>
                <div class="btm mt-3">
                    <label class="font-semibold">URL:</label>
                    <input type="text" value="<?= 'https://' .  $home_url; ?>/file-info/<?= $id; ?>" class="w-full p-2 border border-gray-300 rounded mt-2" readonly="">
                </div>
            </div>

            <script>
                const previewImage = document.getElementById('fileInput');

                previewImage.addEventListener('click', () => {
                    const previewImageElement = document.getElementById('previewImage');
                    previewImageElement.classList.toggle('hidden');
                });
            </script>

<?php else :
            echo "File bị mất hoặc không tồn tại.";
        endif;

        // Đóng câu lệnh đã chuẩn bị
        mysqli_stmt_close($stmt);
    } else {
        echo 'Database query failed.';
    }
else :
    echo "Không tìm thấy tập tin.";
endif;
?>

<?php include '../footer.php'; ?>