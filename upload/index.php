<?php
if (isset($_POST['submit'])) {
    // Kiểm tra xem có file được tải lên chưa
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        // Đường dẫn file tạm
        $fileTmpPath = $file['tmp_name'];
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileType = mime_content_type($fileTmpPath);
        $uploadDirectory = 'files/'; // Thư mục để lưu trữ file

        // Chuyển tên file thành chữ thường và thay thế các ký tự không hợp lệ
        $fileName = strtolower($fileName); // Chuyển tên file thành chữ thường
        $fileName = preg_replace('/[^a-z0-9_\-\.]/', '_', $fileName); // Thay thế các ký tự không hợp lệ bằng dấu gạch dưới (_)
        $fileName = preg_replace('/_+/', '_', $fileName); // Thay thế nhiều dấu gạch dưới thành một dấu gạch dưới
        $fileName = trim($fileName, '_'); // Loại bỏ dấu gạch dưới ở đầu và cuối nếu có

        // Kiểm tra thư mục uploads có tồn tại chưa, nếu không thì tạo mới
        if (!file_exists($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);
        }

        // Kiểm tra loại file được phép (ảnh, video, file nén, JAR/JAD)
        $allowedFileTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',    // Ảnh
            'video/mp4',
            'video/mpeg',
            'video/avi',    // Video
            'application/zip',
            'application/x-rar-compressed',
            'application/x-tar', // File nén
            'application/java-archive',
            'application/x-java-archive',  // JAR, JAD
        ];

        if (in_array($fileType, $allowedFileTypes)) {
            // Kiểm tra kích thước file (max 30MB)
            if ($fileSize <= 30 * 1024 * 1024) { // 30MB
                $current_user_id = $current_login_user['id'];

                // Đặt tên file duy nhất để tránh trùng lặp
                $targetFilePath = $uploadDirectory . $current_user_id . time() . "_" . $fileName;

                // Thêm dữ liệu vào database
                $password = isset($_POST['password']) ? $_POST['password'] : '';
                $sql = "INSERT INTO files (name, size, type, path, password, user) VALUES ('$fileName', $fileSize, '$fileType', '$targetFilePath', '$password', $current_user_id)";
                $result = mysqli_query($conn, $sql);

                // Add exp to user
                $current_user_exp = (int)$current_login_user['exp'];
                $new_current_user_exp = $current_user_exp + 1;
                $sql_new_exp = "UPDATE users SET exp = $new_current_user_exp WHERE id = $current_user_id";
                $result_exp = mysqli_query($conn, $sql_new_exp);

                // Di chuyển file từ thư mục tạm vào thư mục uploads
                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    // echo "The file " . $fileName . " has been uploaded successfully.";

                    // Get id file vừa tải lên
                    $sql_file_new_uploaded = "SELECT * FROM files WHERE path = '$targetFilePath'";
                    $result_file_new_uploaded = mysqli_query($conn, $sql_file_new_uploaded);
                    $file_new_uploaded = mysqli_fetch_assoc($result_file_new_uploaded);

                    // Chuyển hướng về trang thông tin file
                    $id_file = $file_new_uploaded['id'];

                    header('Location: file-info/' . $id_file);
                } else {
                    echo "There was an error uploading your file.";
                }
            } else {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                    <strong class="font-bold">Lỗi!</strong>
                    <span class="block sm:inline">Dung lượng file phải nhỏ hơn 30MB</span>
                </div>';
            }
        } else {
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">Loại file không được hỗ trợ, hãy thử lại.</span>
            </div>';
        }
    } else {
        echo "No file uploaded.";
    }
}

$banned = check_user_is_ban($current_login_user['id']);
?>
<?php if ($banned) : ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
        <strong class="font-bold">Lỗi!</strong>
        <span class="block sm:inline">Bạn không thể tải lên tệp tin.</span><br>
        <span class="block sm:inline">Tài khoản của bạn đã bị khóa đến <?= $banned['time_end'] ?> với lý do: <span class="italic"><?= $banned['li_do'] ?></span>.</span><br>
        <span class="block sm:inline">Nếu bạn cảm thấy điều này không đúng, vui lòng liên hệ quản trị viên.</span>
    </div>
<?php else : ?>
    <section class="mb-6">
        <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2 flex items-center">
            <svg class="w-6 h-6 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8"></path>
            </svg>
            TẢI LÊN TỆP TIN <span class="text-sm font-normal text-gray-500 ml-2">(MAX: 30MB)</span>
        </h2>

        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
            <form action="" method="post" enctype="multipart/form-data" class="space-y-6 relative">
                <!-- Custom File Input -->
                <div class="mb-6">
                    <label for="file" id="file-label-container" class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-blue-300 rounded-lg cursor-pointer hover:border-blue-400 transition bg-blue-50 hover:bg-blue-100">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-12 h-12 mb-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v8"></path>
                            </svg>
                            <p class="mb-2 text-sm text-blue-600 font-semibold">Kéo thả tệp tin hoặc nhấp để chọn</p>
                            <p id="file-label" class="text-xs text-gray-500">Chọn tệp tin cần tải lên</p>
                        </div>
                    </label>
                    <input type="file" id="file" name="file" required="" class="hidden">
                </div>

                <!-- Password Input -->
                <div class="relative">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        MẬT KHẨU <span class="text-xs font-normal text-gray-500 ml-1">(nếu muốn)</span>
                    </label>
                    <input type="password" id="password" name="password" class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition-all" placeholder="Nhập mật khẩu nếu cần bảo vệ tệp tin">
                </div>

                <!-- Submit Button -->
                <button name="submit" type="submit" id="upload_file" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-300 font-medium flex items-center justify-center cursor-not-allowed opacity-50" disabled>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"></path>
                    </svg>
                    TẢI LÊN TỆP TIN
                </button>
            </form>
        </div>
    </section>
    <script>
        const fileInput = document.getElementById('file');
        const fileLabel = document.getElementById('file-label');
        const fileLabelContainer = document.getElementById('file-label-container');
        const btnUpload = document.getElementById('upload_file');

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0]; // Lấy tệp đã chọn
            if (!file) return; // Kiểm tra nếu không có tệp nào được chọn

            const fileName = file.name;
            const size = file.size;

            // Format size to kb or mb or gb
            const kb = 1024;
            const mb = kb * 1024;
            const gb = mb * 1024;

            let formattedSize = '';
            if (size >= gb) {
                formattedSize = (size / gb).toFixed(2) + ' GB';
            } else if (size >= mb) {
                formattedSize = (size / mb).toFixed(2) + ' MB';
            } else if (size >= kb) {
                formattedSize = (size / kb).toFixed(2) + ' KB';
            } else {
                formattedSize = size + ' bytes';
            }

            // Kiểm tra kích thước tệp
            const maxSize = 30 * mb; // 30MB
            if (size > maxSize) {
                alert("File quá lớn, không vượt quá 30MB.");
                e.target.value = '';
                fileLabel.textContent = 'Chọn tệp tin cần tải lên';

                // Vô hiệu hóa nút upload
                btnUpload.classList.add('cursor-not-allowed', 'opacity-50');
                btnUpload.setAttribute('disabled', true); // Thêm thuộc tính disabled

                return; // Dừng xử lý nếu tệp quá lớn
            }

            // Kiểm tra loại tệp hợp lệ
            const validTypes = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', // Ảnh
                'video/mp4', 'video/mpeg', 'video/avi', // Video
                'application/zip', 'application/x-rar-compressed', 'application/x-tar', // File nén
                'application/java-archive', 'application/x-java-archive', // JAR, JAD
            ];

            if (!validTypes.includes(file.type)) {
                alert("Tệp không hợp lệ. Vui lòng chọn tệp ảnh, video, hoặc tệp nén.");
                e.target.value = '';
                fileLabel.textContent = 'Chọn tệp tin cần tải lên';

                // Vô hiệu hóa nút upload
                btnUpload.classList.add('cursor-not-allowed', 'opacity-50');
                btnUpload.setAttribute('disabled', true); // Thêm thuộc tính disabled

                return; // Dừng xử lý nếu tệp không hợp lệ
            }

            // Hiển thị tên tệp và kích thước
            fileLabel.textContent = fileName + ' (' + formattedSize + ')';

            // Xóa lớp CSS không cho phép nhấp và kích hoạt nút upload
            btnUpload.classList.remove('cursor-not-allowed', 'opacity-50');
            btnUpload.removeAttribute('disabled');

        });

        // Sự kiện khi tệp được kéo vào khu vực
        fileLabelContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileLabelContainer.classList.add('bg-blue-100'); // Thêm màu nền khi tệp đang được kéo vào
        });

        // Sự kiện khi tệp bị thả vào khu vực
        fileLabelContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            fileLabelContainer.classList.remove('bg-blue-100'); // Xóa màu nền khi tệp được thả

            // Lấy tệp bị thả
            const file = e.dataTransfer.files[0];
            if (file) {
                // Gán tệp vào input file
                fileInput.files = e.dataTransfer.files;

                const size = file.size;
                const kb = 1024;
                const mb = kb * 1024;
                const gb = mb * 1024;

                let formattedSize = '';
                if (size >= gb) {
                    formattedSize = (size / gb).toFixed(2) + ' GB';
                } else if (size >= mb) {
                    formattedSize = (size / mb).toFixed(2) + ' MB';
                } else if (size >= kb) {
                    formattedSize = (size / kb).toFixed(2) + ' KB';
                } else {
                    formattedSize = size + ' bytes';
                }

                // Kiểm tra kích thước tệp
                const maxSize = 30 * mb; // 30MB
                if (size > maxSize) {
                    alert("File quá lớn, không vượt quá 30MB.");
                    e.target.value = '';
                    fileLabel.textContent = 'Chọn tệp tin cần tải lên';

                    // Vô hiệu hóa nút upload
                    btnUpload.classList.add('cursor-not-allowed', 'opacity-50');
                    btnUpload.setAttribute('disabled', true); // Thêm thuộc tính disabled

                    return; // Dừng xử lý nếu tệp quá lớn
                }

                // Kiểm tra loại tệp hợp lệ
                const validTypes = [
                    'image/jpeg', 'image/jpg', 'image/png', 'image/gif', // Ảnh
                    'video/mp4', 'video/mpeg', 'video/avi', // Video
                    'application/zip', 'application/x-rar-compressed', 'application/x-tar', // File nén
                    'application/java-archive', 'application/x-java-archive' // JAR, JAD
                ];

                if (!validTypes.includes(file.type)) {
                    alert("Tệp không hợp lệ. Vui lòng chọn tệp ảnh, video, hoặc tệp nén.");
                    e.target.value = '';
                    fileLabel.textContent = 'Chọn tệp tin cần tải lên';

                    // Vô hiệu hóa nút upload
                    btnUpload.classList.add('cursor-not-allowed', 'opacity-50');
                    btnUpload.setAttribute('disabled', true); // Thêm thuộc tính disabled

                    return; // Dừng xử lý nếu tệp không hợp lệ
                }

                // Hiển thị tên tệp đã chọn
                fileLabel.textContent = file.name + ' (' + formattedSize + ')';

                // Xóa lớp CSS không cho phép nhấp và kích hoạt nút upload
                btnUpload.classList.remove('cursor-not-allowed', 'opacity-50');
                btnUpload.removeAttribute('disabled');
            }
        });
    </script>
<?php endif; ?>