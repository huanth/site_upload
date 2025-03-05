<?php include '../../header.php'; ?>

<?php if (isset($current_login_user)) :
    if ($current_login_user['role'] == 1 || $current_login_user['role'] == 0) :

        $user = $current_login_user ?? null;

        // Get user files
        $current_page = $_GET['page'] ?? 1;
        $offset = ($current_page - 1) * $limit;

        $sql = "SELECT * FROM files ORDER BY date_create DESC LIMIT $limit OFFSET $offset";
        $result = mysqli_query($conn, $sql);
        $files = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $sql = "SELECT COUNT(*) FROM files";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_row($result);
        $total_records = $row[0];

        $html_pagination = pagination($total_records, $current_page);

?>

        <section class="mb-6">
            <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2 flex items-center">
                <i class="fas fa-folder-open text-primary mr-2"></i> FILE CỦA BẠN <span class="ml-2 px-2.5 py-0.5 bg-primary/10 text-primary rounded-full text-sm"><?= $total_records; ?></span>
            </h2>

            <a href="/admin" class="text-black-600">&larr; Quay lại</a>

            <?php if (empty($files)) : ?>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                    <div class="text-center">
                        <p class="text-gray-500 dark:text-gray-400 mt-4">Không có file nào.</p>
                    </div>
                </div>
            <?php else : ?>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($files as $file) : ?>
                            <div class="py-3 group">
                                <div class="flex items-center justify-between gap-3 p-3 rounded-lg group-hover:bg-gray-50 dark:group-hover:bg-gray-700 transition duration-200">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                                            <i class="fas fa-file-image text-blue-500 dark:text-blue-400"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <?php
                                            $file_url = $home_url . '/file-info/' . $file['id'];
                                            ?>
                                            <a href="https://<?= $file_url; ?>" class="text-gray-800 dark:text-gray-200 hover:text-blue-600 dark:hover:text-blue-400 font-medium block transition-colors">
                                                <div class="truncate max-w-[calc(100%-20px)]" title="images.jpg"><?= $file['name']; ?></div>
                                            </a>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                                <span class="inline-flex items-center mr-3">
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    <?= date('d/m/Y', strtotime($file['date_create'])); ?>
                                                </span>

                                                <span class="inline-flex items-center mr-3">
                                                    <i class="fa-solid fa-user mr-1"></i>
                                                    <?= get_username_by_id($file['user']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 flex items-center space-x-2">
                                        <a href="https://<?= $file_url; ?>" class="text-gray-400 hover:text-blue-500 p-2 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="delete_permanent_file.php?id=<?= $file['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa file này?');" class="text-gray-400 hover:text-red-500 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-wrap justify-center mt-6 gap-2">
                        <?= $html_pagination; ?>
                    </div>
                </div>
            <?php endif; ?>
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