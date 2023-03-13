<?php
$Sys_config["debug"] = true;
$Sys_config["enable_register"] = true;
$Sys_config["db_host"] = "localhost";
$Sys_config["db_user"] = "root";
$Sys_config["db_password"] = "123456";
$Sys_config["db_database"] = "autoappleid";

$Sys_config["apiurl"] = "http://xxx.xxx"; // Địa chỉ website, không kết thúc bằng dấu /
$Sys_config["apikey"] = "114514"; // Khóa API
$Sys_config["webdriver_url"] = "http://"; // Địa chỉ webdriver, kèm theo cổng
$Sys_config["enable_proxy_pool"] = false; // Bật/tắt tính năng đại diện
$Sys_config["proxy_auto_disable"] = false; // Tự động vô hiệu hóa đại diện khi phía sau báo cáo đại diện không khả dụng
$Sys_config["task_headless"] = false; // Bật/tắt chế độ chạy ẩn của tác vụ, tức là không hiển thị cửa sổ trình duyệt

// Bật/tắt bot Telegram. Dùng để thông báo về tình trạng mở khóa tài khoản. Để trống nếu không bật
$Sys_config["telegram_bot_token"] = "";
$Sys_config["telegram_bot_chatid"] = "";
