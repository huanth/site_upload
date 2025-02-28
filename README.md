# TUYETVOI.XZY - Website upload miễn phí
Demo: https://tuyetvoi.xyz/

## Giới thiệu
Nền tảng upload file miễn phí, dễ dàng và nhanh chóng. Hỗ trợ tải lên và chia sẻ tập tin nhanh nhất.

## Cài đặt
 Các bước cài đặt site

```bash
# Clone the repository
git clone https://github.com/huanth/site_upload
cd site_upload

# Sửa thông tin database ở config/config.php
$db_host = 'localhost';
$db_user = '<root>';
$db_pass = '<password>';
$db_name = '<database name>';

# Cập nhật database upload.sql, Có thể nhập thủ công hoặc dùng các lệnh sau:
mysql -u <username> -p<password> <databasename> < <filename.sql>

# Tìm bảng config trong database, thay thế giá trị home_url thành domain của site hiện tại hoặc dùng sql sau:
update config set `home_url`=`<home_url>`
# Lưu ý: home_url chỉ gồm url thuần, không gồm protocol(https://, http://) và gạch chéo ở cuối. VD: domain.local
```

## Sử dụng
Đường dẫn vào admin: 
```bash
# Start the project
domain.com/admin

# Tài khoản demo: admin/admin
```
## Đóng góp
Hướng dẫn đóng góp cho dự án của bạn.

1. Phân nhánh kho lưu trữ.
2. Tạo một nhánh mới (`gitcheck -b feature-branch`).
3. Thực hiện các thay đổi của bạn.
4. Cam kết các thay đổi của bạn (`git commit -m 'Thêm một số tính năng'`).
5. Đẩy tới nhánh (`git Push Origin feature-branch`).
6. Mở yêu cầu kéo.

## Giấy phép
Bao gồm giấy phép mà dự án của bạn được phân phối.

Dự án này được cấp phép theo Giấy phép MIT - xem tệp [LICENSE](LICENSE) để biết chi tiết.
