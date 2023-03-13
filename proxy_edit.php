<?php
include("header.php");
global $conn;
if (isset($_POST['submit'])) {
    switch ($_GET['action']) {
        case "add":
        {
            $stmt = $conn->prepare("INSERT INTO proxy (protocol, content, status, owner) VALUES (:protocol, :content, :status, :owner)");
            $stmt->execute([
                'protocol' => $_POST['protocol'],
                'content' => $_POST['content'],
                'status' => isset($_POST['status']) ? 1 : 0,
                'owner' => $_SESSION['user_id']
            ]);
            alert("success", "Thêm thành công", 2000, "proxy.php");
            exit;
        }
        case "edit":
        {
            $proxy = new proxy($_GET['id']);
            if ($proxy->owner == $_SESSION['user_id'] || $proxy->id) {
                $proxy->update(
                    $_POST['protocol'],
                    $_POST['content'],
                    $_SESSION['user_id'],
                    isset($_POST['status']));
                alert("success", "Sửa thành công", 2000, "proxy.php");
            } else {
                alert("error", "Sửa thất bại", 2000, "proxy.php");
            }
            exit;
        }
        default:
        {
            alert("error", "Lỗi không xác định", 2000, "proxy.php");
            exit;
        }
    }
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case "delete":
        {
            if (!isset($_GET['id'])) {
                alert("error", "Thiếu tham số", 2000, "proxy.php");
                exit;
            }
            $proxy = new proxy($_GET['id']);
            if ($proxy->owner == $_SESSION['user_id'] || $proxy->id) {
                $proxy->delete();
                alert("success", "Xóa thành công", 2000, "proxy.php");
            } else {
                alert("error", "Xóa thất bại", 2000, "proxy.php");
            }
            exit;
        }

        case "add":
        {
            $width = isMobile() ? "auto" : "60%";
            echo "<div class='container' style='margin-top: 2%; width: $width;'>
                    <div class='card border-dark'>
                        <h4 class='card-header bg-primary text-white text-center'>Thêm proxy</h4>
                        <form action='' method='post' style='margin: 20px;'>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='protocol'>Giao thức</span>
                                <select class='form-select' name='protocol'>
                                  <option value='http'>http</option>
                                  <option value='socks5'>socks5</option>
                                </select>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='content'>Địa chỉ</span>
                                <input type='text' class='form-control' name='content'>
                            </div>
                            <div class='input-group mb-3'>
                                <div class='form-check form-switch'>
                                  Kích hoạt<input class='form-check-input' type='checkbox' name='status'>
                                </div>
                            </div>
                            <input type='submit' name='submit' class='btn btn-primary btn-block' value='Thêm'>
                        </form>
                    </div>
                </div>";
            exit;
        }

        case "edit":
        {
            if (!isset($_GET['id'])) {
                alert("error", "Thiếu tham số", 2000, "account.php");
                exit;
            }
            $proxy = new proxy($_GET['id']);
            if ($proxy->id == -1) {
                alert("error", "Tài khoản không tồn tại", 2000, "account.php");
                exit;
            }
            if ($proxy->owner == $_SESSION['user_id']) {
                $width = isMobile() ? "auto" : "60%";
                $status = $proxy->status == 1 ? "checked" : "";
                $http_checked = $proxy->protocol == "http" ? "checked" : "";
                $socks5_checked = $proxy->protocol == "socks5" ? "checked" : "";
                echo "<div class='container' style='margin-top: 2%; width: $width;'>
                    <div class='card border-dark'>
                        <h4 class='card-header bg-primary text-white text-center'>Thêm proxy</h4>
                        <form action='' method='post' style='margin: 20px;'>
                            <div class='input-group mb-3'>
                                <div class='input-group mb-3'>
                                    <span class='input-group-text' id='protocol'>Giao thức</span>
                                    <select class='form-select' name='protocol'>
                                      <option value='http' $http_checked>http</option>
                                      <option value='socks5' $socks5_checked>socks5</option>
                                    </select>
                                </div>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='content'>Địa chỉ</span>
                                <input type='text' class='form-control' name='content' value='$proxy->content'>
                            </div>
                            <div class='input-group mb-3'>
                                <div class='form-check form-switch'>
                                  Kích hoạt<input class='form-check-input' type='checkbox' name='status' $status>
                                </div>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='last_use'>Lần sử dụng cuối</span>
                                <input type='text' class='form-control' name='last_use' value='$proxy->last_use' disabled>
                            </div>
                            <input type='submit' name='submit' class='btn btn-primary btn-block' value='Lưu'>
                        </form>
                    </div>
                </div>";
            } else {
                alert("error", "Không thể chỉnh sửa", 2000, "account.php");
            }
            exit;
        }
        default:
        {
            alert("error", "Lỗi không rõ", 2000, "account.php");
            exit;
        }
    }
} else {
    alert("error", "Thiếu tham số", 2000, "account.php");
}
