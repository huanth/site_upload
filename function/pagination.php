<?php

// Set limit of records per page
$limit = 10;

/**
 * Hiển thị phân trang
 * 
 * @param int $total_pages Tổng số trang
 * 
 * @return string
 */
function pagination($total_records, $current_page)
{
    global $limit;

    $html_pagination = '';

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

    return $html_pagination;
}
