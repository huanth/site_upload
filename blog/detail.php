<?php include '../header.php'; ?>

<?php

if (isset($_GET['id'])) :

    // Get blog by id
    $sql = "SELECT * FROM blogs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();

    // Get username by id
    $sql = "SELECT username FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $blog['author']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $username = $row['username'];

    // Get comments
    $sql = "SELECT * FROM comments WHERE blog = ? ORDER BY date_create DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = $result->fetch_all(MYSQLI_ASSOC);

    // Submit comment
    if (isset($_POST['submit_comment'])) {
        $blog_id = $_POST['blog_id'];
        $author = $_POST['author'];
        $comment = $_POST['comment'];

        $sql = "INSERT INTO comments (blog, user, comment) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iis', $blog_id, $author, $comment);
        $stmt->execute();
        $stmt->close();

        header("Location: /blog/$blog_id");
    }

?>
    <div class="bg-white shadow-lg rounded-lg p-6 border border-yellow-500">
        <h2 class="text-2xl font-bold text-black-700 border-b pb-3"><?= $blog['title']; ?></h2>
        <p class="text-sm text-gray-500 mb-4 mt-2">Đăng bởi: <a href="/user/<?= $blog['author']; ?>" class="hover:text-primary"><strong class="text-blue-700"><?= $username; ?></strong></a> - <?= date('d/m/Y H:i', strtotime($blog['date_create'])); ?></p>
        <div class="prose prose-lg max-w-none prose-ul:list-disc prose-ul:ml-6 text-gray-800">
            <!-- HTML content -->
            <?= $blog['content']; ?>
        </div>

    </div>

    <!-- Khu vực bình luận -->
    <?php if (isset($user)) : ?>
        <div class="mt-6">
            <h3 class="text-lg font-semibold mb-2">💬 Bình luận</h3>
            <form method="POST" class="space-y-2">
                <input type="hidden" name="blog_id" value="<?= $blog_id; ?>">
                <input type="hidden" name="author" value="<?= $user['id']; ?>">
                <textarea name="comment" required="" rows="3" class="w-full p-2 border border-gray-300 rounded-lg focus:ring focus:ring-green-300" placeholder="Nhập bình luận..."></textarea>
                <button type="submit" name="submit_comment" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Gửi bình luận</button>
            </form>
        </div>
    <?php else : ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative mt-4" role="alert">
            <span class="block sm:inline">Hãy <a href="/login" class="text-black">đăng nhập</a> để bình luận.</span>
        </div>
    <?php endif; ?>

    <!-- Danh sách bình luận -->
    <?php if (count($comments) > 0) : ?>
        <?php foreach ($comments as $comment) : ?>
            <div class="mt-4">
                <div class="bg-gray-100 p-3 rounded-lg mb-2">
                    <p class="text-sm text-gray-600"><strong><?= get_username_by_id($comment['user']); ?>:</strong>
                    <p><?= $comment['comment']; ?></p>
                    </p>
                    <p class="text-xs text-gray-400 text-right"><?= date('d/m/Y H:i', strtotime($comment['date_create'])); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="bg-gray-100 p-3 rounded-lg mb-2 mt-4">
            <p class="text-sm text-gray-600">Chưa có bình luận nào.</p>
        </div>
    <?php endif; ?>

<?php else :
    echo "Không tìm thấy blog.";
endif;
?>

<?php include '../footer.php'; ?>