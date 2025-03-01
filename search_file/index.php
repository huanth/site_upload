<?php include '../header.php'; ?>

<?php
// Kiểm tra xem người dùng đã nhấn nút tải về chưa
if (isset($_GET['name'])) :
    $name = $_GET['name'] ?? '';

    // Xử lý phân trang
    $current_page = $_GET['page'] ?? 1;
    $limit = 5;
    $offset = ($current_page - 1) * $limit;

    // Tìm kiếm trong cơ sở dữ liệu dựa trên tên tệp
    $sql = "SELECT * FROM files WHERE name LIKE '%$name%' ORDER BY date_create DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $sql);
    $files = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $html_pagination = '';

    // Tính số lượng trang
    $sql = "SELECT COUNT(*) FROM files WHERE name LIKE '%$name%'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_row($result);
    $total_records = $row[0];
    $total_pages = ceil($total_records / $limit);

    if ($total_pages > 1) {
        $prev = $current_page - 1;
        $next = $current_page + 1;

        // Hiển thị nút "Trước" nếu không phải trang đầu
        if ($current_page > 1) {
            $html_pagination .= '<a href="?page=' . $prev . '&name=' . urlencode($name) . '" class="flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"><i class="fas fa-chevron-left mr-1 text-xs"></i> Trước</a>';
        }

        // Hiển thị các trang (nếu số trang > 5, hiển thị chỉ 2 trang đầu và 2 trang cuối)
        if ($total_pages > 5) {
            for ($i = 1; $i <= 2; $i++) {
                $html_pagination .= ($i == $current_page) ?
                    '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                    '<a href="?page=' . $i . '&name=' . urlencode($name) . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
            }
            if ($current_page > 3) {
                $html_pagination .= '<span class="px-3 py-2">...</span>';
            }
            for ($i = max($current_page - 1, 3); $i <= min($current_page + 1, $total_pages - 2); $i++) {
                $html_pagination .= ($i == $current_page) ?
                    '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                    '<a href="?page=' . $i . '&name=' . urlencode($name) . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
            }
            if ($current_page < $total_pages - 2) {
                $html_pagination .= '<span class="px-3 py-2">...</span>';
            }
            for ($i = $total_pages - 1; $i <= $total_pages; $i++) {
                $html_pagination .= ($i == $current_page) ?
                    '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                    '<a href="?page=' . $i . '&name=' . urlencode($name) . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
            }
        } else {
            for ($i = 1; $i <= $total_pages; $i++) {
                $html_pagination .= ($i == $current_page) ?
                    '<span class="px-3 py-2 bg-blue-500 text-white rounded-lg transition-colors">' . $i . '</span>' :
                    '<a href="?page=' . $i . '&name=' . urlencode($name) . '" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">' . $i . '</a>';
            }
        }

        if ($current_page < $total_pages) {
            $html_pagination .= '<a href="?page=' . $next . '&name=' . urlencode($name) . '" class="flex items-center px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">Tiếp <i class="fas fa-chevron-right ml-1 text-xs"></i></a>';
        }
    }
?>
    <section class="mb-6">
        <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2 flex items-center">
            <i class="fas fa-folder-open text-primary mr-2"></i> TÌM KIẾM: <span><?= $name; ?> </span>
        </h2>

        <p class="mb-4"> Tồng số: <span><?= $total_records; ?></span></p>
        <?php if (!empty($files)) : ?>
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
                                            <div class="truncate max-w-[calc(100%-20px)]" title="<?= $file['name']; ?>"><?= $file['name']; ?></div>
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate">
                                            <span class="inline-flex items-center mr-3">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                <?= date('d/m/Y', strtotime($file['date_create'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 flex items-center space-x-2">
                                    <a href="https://<?= $file_url; ?>" class="text-gray-400 hover:text-blue-500 p-2 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                        <i class="fas fa-download"></i>
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
        <?php else : ?>
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                <div class="text-center">
                    <p class="text-gray-500 dark:text-gray-400 mt-4">Không tìm thấy file nào cả.</p>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php else :
    echo "Không tìm thấy tập tin.";
endif;
?>

<?php include '../footer.php'; ?>