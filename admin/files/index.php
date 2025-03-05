<?php include '../../header.php'; ?>

<?php if (isset($current_login_user)) :
    if ($current_login_user['role'] == 1 || $current_login_user['role'] == 0) :

        $user = $current_login_user ?? null;

        // Get user files
        $current_page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($current_page - 1) * $limit;

        $sql = "SELECT * FROM files ORDER BY date_create DESC LIMIT $limit OFFSET $offset";
        $result = mysqli_query($conn, $sql);
        $files = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $html_pagination = '';

        $sql = "SELECT COUNT(*) FROM files";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_row($result);
        $total_records = $row[0];
        $total_pages = ceil($total_records / $limit);

        if ($total_pages > 1) {
            $prev = $current_page - 1;
            $next = $current_page + 1;

            // Hiển thị nút "Trước" nếu không phải trang đầu
            if ($current_page > 1) {
                $html_pagination .= '<a href="?page=' . $prev . '" class="flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"><i class="fas fa-chevron-left mr-1 text-xs"></i> Trước</a>';
            }

            // Nếu tổng số trang > 5, chỉ hiển thị 2 trang đầu và 2 trang cuối với dấu "..."
            if ($total_pages > 5) {
                // Hiển thị 2 trang đầu
                for ($i = 1; $i <= 2; $i++) {
                    $html_pagination .= ($i == $current_page) ?
                        '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                        '<a href="?page=' . $i . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
                }

                // Dấu "..." ở giữa
                if ($current_page > 3) {
                    $html_pagination .= '<span class="px-3 py-2">...</span>';
                }

                // Hiển thị các trang gần trang hiện tại
                for ($i = max($current_page - 1, 3); $i <= min($current_page + 1, $total_pages - 2); $i++) {
                    $html_pagination .= ($i == $current_page) ?
                        '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                        '<a href="?page=' . $i . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
                }

                // Dấu "..." trước các trang cuối
                if ($current_page < $total_pages - 2) {
                    $html_pagination .= '<span class="px-3 py-2">...</span>';
                }

                // Hiển thị 2 trang cuối
                for ($i = $total_pages - 1; $i <= $total_pages; $i++) {
                    $html_pagination .= ($i == $current_page) ?
                        '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                        '<a href="?page=' . $i . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
                }
            } else {
                // Nếu số trang <= 5, hiển thị tất cả các trang
                for ($i = 1; $i <= $total_pages; $i++) {
                    $html_pagination .= ($i == $current_page) ?
                        '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                        '<a href="?page=' . $i . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
                }
            }

            // Hiển thị nút "Tiếp" nếu không phải trang cuối
            if ($current_page < $total_pages) {
                $html_pagination .= '<a href="?page=' . $next . '" class="flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Tiếp <i class="fas fa-chevron-right ml-1 text-xs"></i></a>';
            }
        }

?>

        <section class="mb-6">
            <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2 flex items-center">
                <i class="fas fa-folder-open text-primary mr-2"></i> FILE CỦA BẠN <span class="ml-2 px-2.5 py-0.5 bg-primary/10 text-primary rounded-full text-sm"><?= $total_records; ?></span>
            </h2>

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