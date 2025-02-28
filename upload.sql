-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th2 28, 2025 lúc 10:32 AM
-- Phiên bản máy phục vụ: 8.0.30
-- Phiên bản PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `upload`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `config`
--

CREATE TABLE `config` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) NOT NULL,
  `id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `config`
--

INSERT INTO `config` (`key`, `value`, `id`) VALUES
('home_url', 'upload.local', 1),
('url', 'UPLOAD.LOCAL', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `files`
--

CREATE TABLE `files` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` int NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `size` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `files`
--

INSERT INTO `files` (`id`, `name`, `path`, `date_create`, `user`, `password`, `size`, `type`) VALUES
(35, 'Screenshot_2.png', 'files/11740733570_Screenshot_2.png', '2025-02-28 09:06:10', 1, '123', '140971', 'image/png'),
(36, 'images.jpg', 'files/21740733640_images.jpg', '2025-02-28 09:07:20', 2, '', '10695', 'image/jpeg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `position` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `position`) VALUES
(0, 'Khách'),
(1, 'Admin'),
(2, 'Thành viên');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tin_tuc`
--

CREATE TABLE `tin_tuc` (
  `id` int NOT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `author` int NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ghim` int NOT NULL,
  `hot` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tu_luyen`
--

CREATE TABLE `tu_luyen` (
  `id` int NOT NULL,
  `cap-bac` varchar(255) NOT NULL,
  `exp` int NOT NULL,
  `note` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tu_luyen`
--

INSERT INTO `tu_luyen` (`id`, `cap-bac`, `exp`, `note`) VALUES
(1, 'Phàm nhân', 0, ''),
(2, 'Luyện khí kỳ', 10, ''),
(3, 'Trúc cơ kỳ', 20, ''),
(4, 'Kim đan kỳ', 30, ''),
(5, 'Nguyên anh kỳ', 40, ''),
(6, 'Hoá thần kỳ', 50, ''),
(7, 'Luyện hư kỳ', 60, ''),
(8, 'Hợp thể kỳ', 70, ''),
(9, 'Độ kiếp kỳ', 80, ''),
(10, 'Chân tiên kỳ', 100, '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` int NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `exp` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `create_time`, `exp`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'nauhyuh@gmail.com', 1, '2025-02-27 08:51:39', '124'),
(2, 'dev', 'e77989ed21758e78331b20e477fc5582', 'dev@gmail.com', 2, '2025-02-27 09:27:12', '2'),
(3, 'huanth', '0192023a7bbd73250516f069df18b500', 'huan@gmail.com', 2, '2025-02-28 03:51:00', '0'),
(4, 'ahihi', '24dca22fdab7a594baa005d55db4f7bf', 'ahihi@gmail.com', 2, '2025-02-28 03:52:38', '0');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tin_tuc`
--
ALTER TABLE `tin_tuc`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tu_luyen`
--
ALTER TABLE `tu_luyen`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `config`
--
ALTER TABLE `config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `files`
--
ALTER TABLE `files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT cho bảng `tin_tuc`
--
ALTER TABLE `tin_tuc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tu_luyen`
--
ALTER TABLE `tu_luyen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
