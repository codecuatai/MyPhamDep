# ALICE COSMETIC — HỆ THỐNG WEBSITE & APP BÁN MỸ PHẨM CHÍNH HÃNG (PHP)

> **Phong cách thiết kế:** Tone Hồng Pastel (`#ff5c8a`, `#fff0f5`, `#ffe4ec`) ngọt ngào, thời thượng, chuẩn thẩm mỹ mỹ phẩm cao cấp.  
> **Kiến trúc:** PHP 8.x + SQLite (tự động khởi tạo & seed dữ liệu, không cần cài MySQL) + Bootstrap 5.3 + FontAwesome 6 + Tích hợp VietQR 24/7 & Google Sheets Automation.

---

## 🌸 1. ĐẶC ĐIỂM NỔI BẬT (LAI GHÉP SHOPEE x MỘC MIÊN BOOKS)

1. **Từ Mộc Miên Books (Thẩm mỹ & Cảm xúc):**
   - **Tone Hồng Pastel ngọt ngào**: Dịu mắt, thanh lịch, tạo cảm giác an tâm về chất lượng và độ lành tính.
   - **Danh mục tròn Bubble Style**: Bong bóng tròn dễ thương, có đếm số lượng sản phẩm.
   - **Set Quà Routine Pink Box Luxury**: Tặng kèm nến thơm tinh dầu hoa hồng, đóng hộp nơ hồng và viết thiệp tay yêu thương.
   - **Slide-over Mini-Cart Drawer**: Giỏ hàng trượt bên phải có thanh tiến trình *"Chỉ cần mua thêm để được Freeship toàn quốc"*.
   - **Thanh toán VietQR 24/7**: Tự động sinh mã QR Napas 24/7 kèm số tiền và nội dung chuyển khoản, không tốn phí duy trì.
   - **Đồng bộ Google Sheets tự động**: Đơn hàng tự động lưu về Google Drive quản lý trên điện thoại.

2. **Từ Shopee (Tối ưu chuyển đổi & Chốt đơn):**
   - **Flash Sale Giờ Vàng**: Đồng hồ đếm ngược, ngọn lửa 🔥, thanh trạng thái *"ĐÃ BÁN X%"*.
   - **Chọn phân loại hàng chuyên sâu**: Dung tích (30ml, 50ml, 100ml), Tone màu son swatch (#02 Hồng đào, #05 Cam cháy, #06 Đỏ nâu), phân loại loại da.
   - **Mua kèm Deal Sốc (Add-on Deal)**: Mua kèm bông tẩy trang `+15k`, mặt nạ tràm trà `+12k`.
   - **Hệ thống Voucher Shopee-style**: Mã `ALICEPINK` giảm 50k, `FREESHIP` miễn phí ship, `ALICE10` giảm 10%.
   - **Đánh giá thực tế (Beauty Reviews)**: Review có gắn tag loại da (*"Da dầu mụn nhạy cảm"*, *"Hỗn hợp thiên dầu"*).
   - **Mobile Bottom Navigation Bar**: Thanh điều hướng chuẩn App Shopee ở đáy màn hình điện thoại.

---

## 🚀 2. HƯỚNG DẪN CHẠY DỰ ÁN

### Cách 1: Chạy ngay với PHP Built-in Server (Khuyên dùng - Nhanh nhất)
Mở PowerShell hoặc Command Prompt tại thư mục dự án và chạy:
```bash
php -S localhost:8000
```
Sau đó mở trình duyệt truy cập:
👉 **`http://localhost:8000`**

*(Cơ sở dữ liệu SQLite tại `database/mypham.sqlite` đã được tự động khởi tạo và nạp đầy đủ sản phẩm, danh mục, voucher và đánh giá mẫu).*

### Cách 2: Chạy trên XAMPP hoặc Laragon
1. Sao chép toàn bộ thư mục `MyPham` vào thư mục `htdocs` của XAMPP (`C:\xampp\htdocs\MyPham`) hoặc `www` của Laragon.
2. Khởi động Apache.
3. Mở trình duyệt truy cập: `http://localhost/MyPham`.

---

## 📁 3. CẤU TRÚC DỰ ÁN

```
d:\Laptrinh\MyPham/
  ├── config.php                 # Cấu hình cửa hàng ALICE, ngân hàng VietQR & Google Sheets
  ├── index.php                  # File chạy chính của website
  ├── includes/
  │   ├── db.php                 # Kết nối PDO SQLite & Tự động tạo bảng/seed dữ liệu
  │   ├── functions.php          # Các hàm tiện ích: format tiền tệ VND, mã đơn ALICE, làm sạch dữ liệu
  │   └── seed_data.php          # Dữ liệu mỹ phẩm phong phú (La Roche-Posay, Anessa, Rom&nd, Bioderma, Torriden...)
  ├── api/
  │   ├── products.php           # API lấy sản phẩm, lọc theo loại da, flash sale, tìm kiếm
  │   ├── checkout.php           # API đặt hàng, tạo VietQR động, gửi Webhook Google Sheets
  │   ├── coupon.php             # API kiểm tra & áp dụng mã giảm giá (ALICEPINK, FREESHIP)
  │   ├── orders.php             # API tra cứu đơn hàng & xác nhận thanh toán
  │   └── admin.php              # API lưu cấu hình ngân hàng & cài đặt shop
  ├── templates/
  │   ├── header.php             # Topbar Freeship, Logo ALICE, Search bar Shopee-style
  │   ├── hero.php               # Banner chính, cam kết 100% chính hãng, đổi trả 7 ngày
  │   ├── bubble_categories.php  # Danh mục tròn Bubble Style (Mộc Miên)
  │   ├── flash_sale.php         # Flash Sale Shopee với đồng hồ đếm ngược & thanh đã bán
  │   ├── product_grid.php       # Lưới sản phẩm & bộ lọc loại da (Dầu mụn, khô, nhạy cảm)
  │   ├── gift_combos.php        # Set quà Routine Pink Box Luxury kèm nến thơm & thiệp viết tay
  │   ├── feedback.php           # Đánh giá khách hàng thực tế có tag loại da
  │   ├── blog.php               # Cẩm nang làm đẹp & mẹo skincare
  │   ├── footer.php             # Footer bo cong tone hồng pastel mềm mại
  │   ├── modals.php             # Modal chi tiết biến thể, Giỏ hàng mini, Popup VietQR, Tra cứu đơn
  │   ├── mobile_nav.php         # Thanh điều hướng di động cố định dưới đáy màn hình
  │   └── floating_contact.php   # Nút nổi Zalo, Messenger, Hotline gọi ngay
  ├── assets/
  │   ├── css/style.css          # Toàn bộ stylesheet Tone Hồng Pastel cao cấp
  │   └── js/app.js              # Engine JavaScript điều khiển giỏ hàng, VietQR, đếm ngược, lọc
  └── database/
      └── mypham.sqlite          # CSDL SQLite lưu trữ toàn bộ dữ liệu tự động
```

---

## 💳 4. CẤU HÌNH TÀI KHOẢN NGÂN HÀNG & GOOGLE SHEETS

1. Click vào biểu tượng **Bánh răng (Cài đặt)** ở góc phải trên thanh Header của website.
2. Bạn có thể thay đổi ngay trên giao diện web:
   - Ngân hàng thụ hưởng (MBBank, Vietcombank, Techcombank, v.v.)
   - Số tài khoản và Tên chủ tài khoản
   - Số điện thoại Hotline / Zalo
   - Đường dẫn **Google Sheets WebApp URL** để đơn hàng tự động đổ về Google Drive.
3. Bấm **Lưu Cấu Hình** là hệ thống cập nhật tức thì.
