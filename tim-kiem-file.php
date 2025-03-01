<section class="mb-6">
    <h2 class="text-xl font-semibold mb-4 border-b-2 border-primary pb-2">TÌM TỆP TIN:</h2>
    <form action="" method="post" enctype="multipart/form-data" id="searchForm">
        <input type="text" id="namefile" name="namefile" required="" class="block w-full text-sm text-gray-700 mb-4 border border-gray-300 rounded-lg p-2">
        <button name="search_file" type="submit" class="w-full bg-secondary text-white py-2 rounded-lg hover:bg-accent transition duration-300" id="searchButton">
            <a href="search_file?name=demo" id="searchLink">TÌM TỆP</a>
        </button>
    </form>
</section>

<script>
    // Xử lý sự kiện submit form (khi nhấn Enter hoặc nút button)
    document.getElementById("searchForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Ngừng gửi form mặc định

        // Lấy giá trị từ input
        var searchKeyword = document.getElementById("namefile").value.trim(); // trim() để loại bỏ khoảng trắng

        // Kiểm tra nếu input không trống
        if (searchKeyword === "") {
            alert("Vui lòng nhập từ khóa tìm kiếm.");
            return; // Dừng lại nếu input trống
        }

        // Lấy thẻ a và thay đổi thuộc tính href
        var searchLink = document.getElementById("searchLink");
        searchLink.href = "search_file?name=" + encodeURIComponent(searchKeyword);

        // Điều hướng đến URL mới với từ khóa tìm kiếm
        window.location.href = searchLink.href;
    });

    // Xử lý sự kiện khi người dùng nhấn vào nút button
    document.getElementById("searchButton").addEventListener("click", function(event) {
        // Kiểm tra xem người dùng có nhập từ khóa không
        var searchKeyword = document.getElementById("namefile").value.trim();
        if (searchKeyword === "") {
            alert("Vui lòng nhập từ khóa tìm kiếm.");
            return; // Nếu trống, không làm gì cả
        }

        // Thay đổi href của thẻ a với từ khóa nhập vào
        var searchLink = document.getElementById("searchLink");
        searchLink.href = "search_file?name=" + encodeURIComponent(searchKeyword);

        // Điều hướng đến URL với từ khóa tìm kiếm
        window.location.href = searchLink.href;
    });
</script>