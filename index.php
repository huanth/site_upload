<?php include 'header.php'; ?>

<?php include 'tin-tuc.php'; ?>

<?php if (isset($_SESSION['user'])) : ?>
    <?php include 'upload/index.php'; ?>
<?php else : ?>
    <!-- Thông báo đăng nhập để tải lên -->
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded relative" role="alert">
        <span class="block sm:inline">Hãy <a href="/login" class="text-black">đăng nhập</a> để tải lên tập tin.</span>
    </div>
<?php endif; ?>

<?php include 'tim-kiem-file.php'; ?>

<?php include 'thong-ke.php'; ?>

<?php include 'footer.php'; ?>