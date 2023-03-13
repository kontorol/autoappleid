<h1 align="center">Apple ID -Công cụ mở khóa một phím</h1>
<p align="center">
    <a href="https://github.com/pplulee/appleid_auto/issues" style="text-decoration:none">
        <img src="https://img.shields.io/github/issues/pplulee/appleid_auto.svg" alt="GitHub issues"/>
    </a>
    <a href="https://github.com/pplulee/appleid_auto/stargazers" style="text-decoration:none" >
        <img src="https://img.shields.io/github/stars/pplulee/appleid_auto.svg" alt="GitHub stars"/>
    </a>
    <a href="https://github.com/pplulee/appleid_auto/network" style="text-decoration:none" >
        <img src="https://img.shields.io/github/forks/pplulee/appleid_auto.svg" alt="GitHub forks"/>
    </a>
    <a href="https://github.com/pplulee/apple_auto/blob/main/LICENSE" style="text-decoration:none" >
        <img src="https://img.shields.io/github/license/pplulee/appleid_auto" alt="GitHub license"/>
    </a>
</p>
<h3 align="center">Vui lòng đọc kỹ tài liệu này cùng với tài liệu Wiki mà chúng tôi sẽ phát hành trong tương lai trước khi sử dụng.</h3>
<h3 align="center">Dự án này hiện vẫn đang được cập nhật.</h3>

# Giới thiệu cơ bản

"Quản lý Apple ID của bạn bằng cách hoàn toàn mới" - Đây là một ứng dụng tự động kiểm tra và mở khóa Apple ID dựa trên câu hỏi bảo mật.

Front-end được sử dụng để quản lý tài khoản, hỗ trợ thêm nhiều tài khoản và cung cấp trang hiển thị tài khoản;

Hỗ trợ tạo trang chia sẻ bao gồm nhiều tài khoản và có thể thiết lập mật khẩu cho trang chia sẻ.

Backend định kỳ kiểm tra xem tài khoản có bị khóa hay không, nếu bị khóa hoặc bật xác thực hai bước thì sẽ tự động mở khóa, thay đổi mật khẩu và báo cáo mật khẩu cho API.

Đăng nhập vào Apple ID và tự động xóa thiết bị trong Apple ID.

Bật pool proxy và Selenium cluster, tăng tỷ lệ mở khóa thành công và tránh rủi ro kiểm soát.

### Lưu ý:

1. Hiện tại, **backend chạy trên docker**, vui lòng đảm bảo máy tính đã cài đặt docker;
2. unblocker_manager là **chương trình quản lý backend**, sẽ định kỳ lấy danh sách nhiệm vụ từ API và triển khai các container docker (mỗi tài khoản tương ứng với một container);
3. Chương trình **cần sử dụng Chrome webdriver**, Đề nghị sử dụng phiên bản Docker [selenium/standalone-chrome](https://hub.docker.com/r/selenium/standalone-chrome), hướng dẫn triển khai docker như sau, vui lòng chỉnh sửa tham số theo nhu cầu.


```bash
docker run -d --name=webdriver --log-opt max-size=1m --log-opt max-file=1 --shm-size="2g" --restart=always -e SE_NODE_MAX_SESSIONS=10 -e SE_NODE_OVERRIDE_MAX_SESSIONS=true -e SE_SESSION_RETRY_INTERVAL=1 -e SE_VNC_VIEW_ONLY=1 -p 4444:4444 -p 5900:5900 selenium/standalone-chrome
```

# Cách sử dụng

**Vui lòng triển khai frontend trước, sau đó cài đặt backend. Kịch bản cài đặt backend cung cấp cài đặt webdriver một cách dễ dàng** \
Nếu bạn muốn biết về Selenium Grid cluster, vui lòng truy cập [sahuidhsu/selenium-grid-docker](https://github.com/sahuidhsu/selenium-grid-docker) \
Môi trường chạy trên web được đề nghị là php7.4 & MySQL8.0, lý thuyết hỗ trợ MySQL5.x, các phiên bản php khác có thể không được hỗ trợ.

1. Tải xuống mã nguồn trang web từ Release và triển khai, nhập cơ sở dữ liệu (`sql/db.sql`), sao chép tệp cấu hình `config.bak.php` thành `config.php`, và điền các mục cài đặt \
   Tài khoản mặc định: `admin` Mật khẩu: `admin`
2. Sau khi đăng nhập vào trang web, thêm tài khoản Apple và điền thông tin tài khoản
3. Triển khai `backend\unblocker_manager.py` (cung cấp kịch bản triển khai một cú nhấp chuột, xem bên dưới)
4. Kiểm tra xem `unblocker_manager` đã lấy được danh sách nhiệm vụ chưa
5. Kiểm tra xem các container đã được triển khai và hoạt động bình thường chưa

### Giải thích về câu hỏi bảo mật:

Chỉ cần điền các từ khóa vào cột câu hỏi, ví dụ như "Ngày sinh", "Công việc", nhưng hãy chú ý đến **ngôn ngữ của câu hỏi bảo mật**.

# Cập nhật Frontend

Tải xuống mã nguồn trang web từ Release và ghi đè các tệp hiện có, điền lại config.php, nhập cơ sở dữ liệu mới (tệp bắt đầu bằng update_) và hoàn tất.

# Cập nhật Backend

Nếu đó là phiên bản mới nhất của tập lệnh quản lý Backend, chỉ cần khởi động dịch vụ appleauto lại là được. Nếu không thể cập nhật, hãy chạy lại tập lệnh cài đặt

# Phản hồi về vấn đề và giao tiếp

Khả năng và trình độ phát triển của nhà phát triển có giới hạn, chương trình có thể tồn tại nhiều lỗi, chúng tôi hoan nghênh việc đưa ra ý kiến hoặc yêu cầu Pull Request, chúng tôi cũng hoan nghênh sự tham gia của các chuyên gia trong dự án! \
Nhóm Telegram: [@appleunblocker](https://t.me/appleunblocker)


### Triển khai unblocker_manager (Backend + Webdriver) trong một lần nhấn:


### Triển khai unblocker_manager (Backend + Webdriver) trong một lần nhấn:

```bash
bash <(curl -Ls https://raw.githubusercontent.com/pplulee/appleid_auto/main/backend/install_unblocker.sh)
```

# Giải thích về các tệp

- `backend\unblocker_manager.py` chương trình quản lý Backend \
  Giải thích: Được sử dụng để định kỳ lấy danh sách nhiệm vụ từ API và triển khai các container Docker tương ứng với các nhiệm vụ \
  Tham số khởi động: `-api_url <Địa chỉ API> -api_key <API key> ` (Địa chỉ API có định dạng http://xxx.xxx, không cần dấu gạch chéo cuối cùng và đường dẫn)

- `backend\unlocker\main.py` chương trình mở khóa Backend \
  Giải thích: Sử dụng Webdriver để cập nhật mật khẩu tài khoản và gửi mật khẩu mới cho API. **Chương trình này phụ thuộc vào API** \
  Tham số khởi động: `-api_url <Địa chỉ API> -api_key <API key> -taskid <ID nhiệm vụ>`

Chỉ cần triển khai **chương trình quản lý Backend** và nó sẽ tự động lấy danh sách nhiệm vụ từ trang web API và triển khai các container Docker tương ứng với các nhiệm vụ. Thời gian đồng bộ mặc định là 10 phút (đồng bộ thủ công có thể khởi động lại dịch vụ) \
Nếu không muốn sử dụng đồng bộ tự động, bạn có thể triển khai **chương trình mở khóa Backend** trực tiếp, phiên bản Docker [sahuidhsu/appleid_auto](https://hub.docker.com/r/sahuidhsu/appleid_auto)

---
# Tài trợ cho nhà phát triển
[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/baiyimiao) \
USDT-TRC20: TV1su1RnQny27YEF9WG4DbC8AAz3udt6d4 \
ETH-ERC20：0xea8fbe1559b1eb4b526c3bb69285203969b774c5 \
Quảng cáo: Nếu bạn cần sử dụng dịch vụ Gói thư, hãy liên hệ với nhà phát triển.

---

# Giải thích về API

Đường dẫn: `/api/` \
Phương thức: `GET` \
Tất cả các hoạt động đều cần truyền tham số `key`, giá trị là `apikey` trong `config.php` \
Kiểu trả về: `JSON` \
Các tham số trả về chung

| Tham số        | Giá trị/Loại             | Giải thích      |
|-----------|------------------|---------|
| `status`  | `success`/`fail` | Thành công/Thất bại |
| `message` | `String`         | Thông báo    |

Hoạt động: `random_sharepage_password` \
Giải thích: Tạo mật khẩu chia sẻ ngẫu nhiên \
Tham số đầu vào:

| Tham số       | Giá trị/Loại                        | Giải thích    |
|----------|-----------------------------|-------|
| `action` | `random_sharepage_password` | Hoạt động    |
| `id`     | `Int`                       | ID trang chia sẻ |

Các tham số trả về:

| Tham số         | Giá trị/Loại     | Giải thích  |
|------------|----------|-----|
| `password` | `String` | Mật khẩu mới |

… Các tham số khác đang chờ được thêm vào

---

# Giao diện JSON API

Hỗ trợ lấy thông tin tài khoản bằng JSON thông qua liên kết trang chia sẻ, để tích hợp với ứng dụng khác \
Liên kết trang chia sẻ chỉ đến mã trang, không phải URL đầy đủ

Đường dẫn API: `/api/share.php` \
Phương thức yêu cầu: `GET` \
Tham số đầu vào:

| Tham số           | Giá trị/Loại     | Giải thích                |
|--------------|----------|-------------------|
| `share_link` | `String` | Mã trang chia sẻ             |
| `password`   | `String` | Mật khẩu trang chia sẻ (nếu không có mật khẩu thì không cần thiết) |

Tham số trả về:

| Tham số         | Giá trị/Loại             | Giải thích            |
|------------|------------------|---------------|
| `status`   | `success`/`fail` | Thành công/Thất bại       |
| `message`  | `String`         | Thông báo              |
| `accounts` | `Array`          | Danh sách thông tin tài khoản (chi tiết xem bảng dưới đây) |

Thông tin tài khoản:

| Tham số           | Giá trị/Loại     | Giải thích      |
|--------------|----------|--------|
| `id`         | `Int`    | ID tài khoản |
| `username`   | `String` | Tài khoản   |
| `password`   | `String` | Mật khẩu   |
| `status`     | `Bool`   | Trạng thái tài khoản |
| `last_check` | `String` | Thời gian kiểm tra lần cuối |
| `remark`     | `String` | Chú thích tài khoản tại front-end |


---
# Danh sách việc cần làm

- [x] Tự động nhận dạng Captcha
- [x] Kiểm tra tài khoản bị khóa
- [x] Kiểm tra xác thực 2 bước
- [x] Trang chia sẻ hỗ trợ nhiều tài khoản
- [x] Trang chia sẻ có thể mở mật khẩu
- [x] Kiểm tra mật khẩu chính xác
- [x] Xóa thiết bị
- [x] Định kỳ thay đổi mật khẩu
- [x] Báo cáo mật khẩu
- [x] Hỗ trợ proxy
- [x] Thông báo qua Telegram Bot
- [x] Giao diện JSON API để lấy thông tin tài khoản
