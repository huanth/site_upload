<?php include '../../header.php'; ?>

<?php if (isset($current_login_user)) :
    if ($current_login_user['role'] == 1 || $current_login_user['role'] == 0) :
        $current_page = $_GET['page'] ?? 1;
        $sql = "SELECT * FROM blogs ORDER BY pin DESC, date_create DESC LIMIT $limit OFFSET " . ($current_page - 1) * $limit;
        $result = mysqli_query($conn, $sql);
        $blogs = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $sql = "SELECT COUNT(*) FROM blogs";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_row($result);
        $total_records = $row[0];

        $html_pagination = pagination($total_records, $current_page);

?>

        <section class="mb-6">
            <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2 flex items-center">
                <i class="fas fa-folder-open text-primary mr-2"></i>BLOG <span class="ml-2 px-2.5 py-0.5 bg-primary/10 text-primary rounded-full text-sm"><?= $total_records; ?></span>
            </h2>

            <a href="/admin" class="text-black-600">&larr; Quay lại</a>

            <div class="flex justify-between items-center mt-4">
                <a href="/admin/blogs/add.php" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow hover:bg-red-600 transition mb-4">Thêm blog</a>
            </div>

            <?php if (empty($blogs)) : ?>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-2xl mx-auto">
                    <div class="text-center">
                        <p class="text-gray-500 dark:text-gray-400 mt-4">Không có blog nào.</p>
                    </div>
                </div>
            <?php else : ?>
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

                                <!-- Action buttons -->
                                <div class="flex justify-end p-4">
                                    <a href="/admin/blogs/edit.php?id=<?= $blog['id'] ?>" class="bg-primary text-white px-4 py-2 rounded-lg mr-2">Sửa</a>
                                    <a href="/admin/blogs/delete.php?id=<?= $blog['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa blog này?');" class="bg-red-500 text-white px-4 py-2 rounded-lg">Xóa</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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