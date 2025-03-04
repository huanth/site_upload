<?php include '../header.php'; ?>

<?php
$current_page = $_GET['page'] ?? 1;
$limit = 10;
$sql = "SELECT * FROM blogs ORDER BY pin DESC, date_create DESC LIMIT $limit OFFSET " . ($current_page - 1) * $limit;
$result = mysqli_query($conn, $sql);
$blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);

function get_username_by_id($id)
{
    global $conn;
    $sql = "SELECT username FROM users WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    return $row['username'];
}

$html_pagination = '';
$sql = "SELECT COUNT(*) FROM blogs";
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
    <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2">📌 BLOG</h2>

    <?php if (count($blogs) > 0) : ?>
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <?php foreach ($blogs as $blog) : ?>
                <div class="news-container space-y-4 mb-4">
                    <div class="news-item bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 relative">

                        <?php if ($blog['pin'] == 1) : ?>
                            <div class="bg-red-500 text-white text-xs font-bold px-2 py-1 absolute right-0 top-0">Ghim</div>
                        <?php endif; ?>

                        <div class="p-4">
                            <a href="/blog/<?= $blog['id'] ?>" class="news-title block text-xl font-bold text-success hover:text-accent mb-3 transition-colors border-primary py-1 bg-gray-50">
                                <p class="flex items-center">
                                    <?php if ($blog['hot']) : ?>
                                        <i class="fa-solid fa-fire text-red-500 mr-2"></i>
                                    <?php endif; ?>
                                    <?= $blog['title']; ?>
                                </p>
                            </a>

                            <!-- Get 50 first characters of content -->
                            <p class="text-gray-600 text-sm mb-3">
                                <?= substr($blog['content'], 0, 250); ?> ...
                            </p>
                            <div class="news-meta text-xs text-gray-500 mb-3 flex justify-between">
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <a href="/user/<?= $blog['author'] ?>" class="hover:text-primary"><?= get_username_by_id($blog['author']); ?></a>
                                </span>
                                <span class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <?php echo date('d/m/Y H:i', strtotime($blog['date_create'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <div class="flex flex-wrap justify-center mt-6 gap-2">

            <?= $html_pagination; ?>
        </div>
    <?php else : ?>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
            <div class="text-center">
                <p class="text-gray-500 dark:text-gray-400 mt-4">Không có blog nào.</p>
            </div>
        </div>
    <?php endif; ?>

</section>

<?php include '../footer.php'; ?>