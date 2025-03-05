<?php include '../../header.php'; ?>

<?php if (isset($current_login_user)) :
    if ($current_login_user['role'] == 1 || $current_login_user['role'] == 0) :

        // Update blog
        if (isset($_POST['create_blog'])) {
            $title = $_POST['title'];
            $content = $_POST['content'];
            $hot = $_POST['hot'];
            $pin = $_POST['pin'];

            $sql = "INSERT INTO blogs (title, content, hot, pin, author) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssiis', $title, $content, $hot, $pin, $current_login_user['id']);
            if ($stmt->execute()) {
                echo '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded relative" role="alert">
                        <strong class="font-bold">Thành công!</strong>
                        <span class="block sm:inline">Tạo mới blog thành công.</span>
                    </div>';

                // Redirect to blogs page
                header('Location: /admin/blogs');
            } else {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
                        <strong class="font-bold">Lỗi!</strong>
                        <span class="block sm:inline">Tạo mới blog thất bại.</span>
                    </div>';
            }
        }
?>

        <section class="mb-6">
            <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2 flex items-center">
                EDIT BLOG
            </h2>

            <a href="/admin/blogs" class="text-black-600">&larr; Quay lại</a>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                <form action="" method="POST">
                    <div class="mb-4">
                        <label for="title" class="block text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Tiêu đề</label>
                        <input type="text" id="title" name="title" required class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" placeholder="Title" value="">
                    </div>
                    <div class="mb-4">
                        <label for="content" class="block text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Nội dung</label>
                        <textarea name="content" id="text_area_content" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" rows="10" placeholder="Nội dung"></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="hot" class="block text-gray-600 dark:text-gray-400 text-sm font-medium mb-2">Hot</label>
                        <select name="hot" id="hot" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300">
                            <option value="0" selected>Không</option>
                            <option value="1">Có</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="pin" class="block text-gray-600 dark:text-gray-400 text-sm font-medium mb-2 mr-2">Có phải là tin Hot</label>
                        <select name="pin" id="pin" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300">
                            <option value="0" selected>Không</option>
                            <option value="1">Có</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <button type="submit" name="create_blog" class="bg-primary text-white px-4 py-2 rounded-lg">Tạo mới</button>
                    </div>
                </form>
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