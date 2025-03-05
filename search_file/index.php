<?php include '../header.php'; ?>

<?php
// Kiểm tra xem người dùng đã nhấn nút tải về chưa
if (isset($_GET['name'])) :
    $name = $_GET['name'] ?? '';

    // Xử lý phân trang
    $current_page = $_GET['page'] ?? 1;
    $offset = ($current_page - 1) * $limit;

    // Tìm kiếm trong cơ sở dữ liệu dựa trên tên tệp
    $sql = "SELECT * FROM files WHERE name LIKE '%$name%' ORDER BY date_create DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $sql);
    $files = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Tính số lượng trang
    $sql = "SELECT COUNT(*) FROM files WHERE name LIKE '%$name%'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_row($result);
    $total_records = $row[0];

    $html_pagination = pagination($total_records, $current_page);

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