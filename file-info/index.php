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
            $name = $file['name'];

            // Tải tập tin về
            $file_path = '../' . $path;
            $file_name = basename($name);

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
            $user_uploaded_id = $file['user'];

            // Lấy thông tin người dùng đã tải lên file
            $sql = "SELECT * FROM users WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                // Gắn giá trị tham số vào câu truy vấn
                mysqli_stmt_bind_param($stmt, 'i', $user_uploaded_id);

                // Thực thi câu truy vấn
                mysqli_stmt_execute($stmt);

                // Lấy kết quả truy vấn
                $result = mysqli_stmt_get_result($stmt);

                $user_upload = mysqli_fetch_assoc($result);
                $user_uploaded = $user_upload['username'];
            }

            $password = $file['password'];

            // Check xem nếu file là ảnh thì hiển thị nút xem trước
            if (strpos($type, 'image') !== false) {
                $preview = true;
                $is_exists = false;

                $pathPreview = 'https://' . $home_url . '/' . htmlspecialchars($path);

                // Kiểm tra xem hình ảnh có tồn tại không
                if (@getimagesize($pathPreview)) {
                    $is_exists = true;
                    $base64img = base64_encode(file_get_contents($pathPreview));
                    $type = pathinfo($pathPreview, PATHINFO_EXTENSION);
                    $pathPreview = 'data:image/' . $type . ';base64,' . $base64img;
                } else {
                    $is_exists = false;
                }
            } else {
                $preview = false;
            }
?>

            <div class="copy bg-primary text-white p-3 rounded-t-lg text-center font-bold">
                THÔNG TIN TẬP TIN
            </div>

            <div class="tp1 bg-white p-4 rounded-b-lg shadow-md">
                <div class="list4 font-semibold truncate max-w-[calc(100%-20px)]">Tên tập tin: <span class="font-normal"><?= htmlspecialchars($name); ?></span></div>
                <div class="list4 font-semibold">Dung lượng: <span class="font-normal"><?= $size; ?></span></div>
                <div class="list4 font-semibold">Thời gian tải lên: <span class="font-normal"><?= $uploaded_at; ?></span></div>
                <div class="list4 font-semibold">
                    Upload bởi thành viên:
                    <a href="
                        <?php if (isset($user)) : ?>
                            <?php if ($user['username'] === $user_uploaded) : ?>
                                /profile
                            <?php else : ?>
                                https://<?= $home_url; ?>/user/<?= $user_uploaded_id; ?>
                            <?php endif; ?>
                        <?php else : ?>
                            https://<?= $home_url; ?>/user/<?= $user_uploaded_id; ?>
                        <?php endif; ?>
                    " class="text-blue-500 hover:underline">
                        <?= $user_uploaded; ?>
                        <?php if (isset($user) && $user['username'] === $user_uploaded) : ?>
                            (Bạn)
                        <?php endif; ?>
                    </a>
                </div>



                <?php if ($preview == true) : ?>
                    <?php if ($is_exists == false) : ?>
                        <div class="list4 text-center mt-3">
                            <p class="text-red-700"><i class="fa-solid fa-triangle-exclamation"></i> Xin lỗi, chúng tôi đã làm mất hình ảnh của bạn!</p>
                        </div>

                        <?php if (isset($user)) : ?>
                            <?php if ($user['username'] === $user_uploaded || $user['role'] == 1 || $user['role'] == 0) : ?>
                                <a href="delete_permanent_file.php?id=<?= $file['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa file này?');" class="text-gray-400 hover:text-red-500 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                    <i class="fas fa-trash"></i> Xóa Tập Tin
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="list4 text-center mt-3">
                            <p>Hình ảnh xem trước:</p>
                            <img src="<?= $pathPreview; ?>" alt="Preview" class=" mt-3" id="previewImage" style="margin-left: auto; margin-right: auto;">
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($is_exists == true) : ?>
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

                                <?php if (isset($user)) : ?>
                                    <?php if ($user['username'] === $user_uploaded || $user['role'] == 1 || $user['role'] == 0) : ?>
                                        <a href="delete_permanent_file.php?id=<?= $file['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa file này?');" class="text-gray-400 hover:text-red-500 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <i class="fas fa-trash"></i> Xóa Tập Tin
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>

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

                                <?php if (isset($user)) : ?>
                                    <?php if ($user['username'] === $user_uploaded || $user['role'] == 1 || $user['role'] == 0) : ?>
                                        <a href="delete_permanent_file.php?id=<?= $file['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa file này?');" class="text-gray-400 hover:text-red-500 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <i class="fas fa-trash"></i> Xóa Tập Tin
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </form>
                            <br>
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg text-sm">
                                <strong class="font-bold">LƯU Ý:</strong>
                                <span class="block">* <?= $home_url; ?> không chịu trách nhiệm về nội dung tải lên.</span>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="copy bg-primary text-white p-3 mt-6 rounded-t-lg text-center font-bold">CHIA SẺ</div>

            <div class="tp1 bg-white p-4 rounded-b-lg shadow-md">
                <div class="btm">
                    <label class="font-semibold">BBCode:</label>
                    <input type="text" id="bbcode-input" value="[url=<?= 'https://' . $home_url; ?>/file-info/<?= $id; ?>]Tải về <?= $name; ?>[/url]" class="w-full p-2 border border-gray-300 rounded mt-2" readonly="">
                </div>
                <div class="btm mt-3">
                    <label class="font-semibold">URL:</label>
                    <input type="text" id="url-input" value="<?= 'https://' .  $home_url; ?>/file-info/<?= $id; ?>" class="w-full p-2 border border-gray-300 rounded mt-2" readonly="">
                </div>
            </div>

            <!-- Toast container -->
            <div id="toast-container" style="position: fixed; bottom: 10px; right: 10px; z-index: 9999;"></div>

            <script>
                // Function to copy text to clipboard
                function copyToClipboard(id) {
                    var copyText = document.getElementById(id);

                    // Select the text field
                    copyText.select();
                    copyText.setSelectionRange(0, 99999); // For mobile devices

                    // Copy the text inside the text field
                    document.execCommand('copy');

                    // Show toast notification
                    showToast("Đã sao chép: " + copyText.value);
                }

                // Function to show toast notification
                function showToast(message) {
                    var toast = document.createElement('div');
                    toast.textContent = message;
                    toast.style.backgroundColor = "#333";
                    toast.style.color = "white";
                    toast.style.padding = "10px";
                    toast.style.borderRadius = "5px";
                    toast.style.marginBottom = "10px";
                    toast.style.fontSize = "14px";
                    toast.style.boxShadow = "0px 4px 6px rgba(0, 0, 0, 0.1)";
                    toast.style.animation = "fadeInOut 3s forwards"; // Fade-out effect

                    // Append the toast to the toast container
                    document.getElementById("toast-container").appendChild(toast);

                    // Remove the toast after 3 seconds
                    setTimeout(function() {
                        toast.remove();
                    }, 3000);
                }

                // Add event listeners to the input fields to copy text on click
                document.getElementById('bbcode-input').addEventListener('click', function() {
                    copyToClipboard('bbcode-input');
                });

                document.getElementById('url-input').addEventListener('click', function() {
                    copyToClipboard('url-input');
                });
            </script>

            <style>
                @keyframes fadeInOut {
                    0% {
                        opacity: 0;
                        transform: translateY(-10px);
                    }

                    10% {
                        opacity: 1;
                        transform: translateY(0);
                    }

                    90% {
                        opacity: 1;
                        transform: translateY(0);
                    }

                    100% {
                        opacity: 0;
                        transform: translateY(-10px);
                    }
                }
            </style>

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