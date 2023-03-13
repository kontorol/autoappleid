<?php
include("header.php");
global $conn;
if (isset($_POST['submit'])) {
    switch ($_GET['action']) {
        case "add":
        {
            $share_link = $_POST['share_link'];
            $stmt = $conn->prepare("SELECT id FROM share WHERE share_link = :link;");
            $stmt->execute(['link' => $share_link]);
            if ($stmt->rowCount() != 0) {
                alert("error", "Đường dẫn chia sẻ đã tồn tại, không thể thêm trùng lặp", 2000, "share_list.php");
                exit;
            }
            if (!isset($_POST['account_list'])) {
                alert("error", "Vui lòng thêm ít nhất một tài khoản", 2000, "share_list.php");
                exit;
            }
            $accounts = implode(",", $_POST['account_list']);
            $stmt = $conn->prepare("INSERT INTO share (
                   share_link,
                   account_list,
                   owner,
                   html,
                   remark) 
            VALUES (
                    :link,
                    :accounts,
                    :owner,
                    :html,
                    :remark);");
            $stmt->execute(['link' => $share_link,
                'accounts' => $accounts,
                'owner' => $_SESSION['user_id'],
                'html' => htmlspecialchars($_POST['html']),
                'remark' => $_POST['remark']]);
            alert("success", "Thêm thành công", 2000, "share_list.php");
            exit;
        }
        case "edit":
        {
            if (!isset($_GET['id'])) {
                alert("error", "Thiếu tham số", 2000, "share_list.php");
                exit;
            }
            if (!isset($_POST['account_list'])) {
                alert("error", "Vui lòng thêm ít nhất một tài khoản", 2000, "share_list.php");
                exit;
            }
            $sharepage = new sharepage($_GET['id']);
            // Kiểm tra quyền hạn
            if ($sharepage->id == -1) {
                alert("error", "ID trang chia sẻ không tồn tại", 2000, "share_list.php");
                exit;
            } else {
                if ($sharepage->owner != $_SESSION['user_id']) {
                    alert("error", "Không có quyền sửa đổi", 2000, "share_list.php");
                    exit;
                }
            }
            if (check_sharelink_exist($_POST['share_link']) && $_POST['share_link'] != $sharepage->share_link) {
                alert("error", "Đường dẫn chia sẻ đã tồn tại, không thể thêm trùng lặp", 2000, "share_list.php");
                exit;
            }
            $data = array(
                'share_link' => $_POST['share_link'],
                'password' => $_POST['password'],
                'account_list' => $_POST['account_list'],
                'owner' => $_SESSION['user_id'],
                'html' => $_POST['html'],
                'remark' => $_POST['remark']
            );
            $sharepage->update($data);
            alert("success", "Sửa đổi thành công", 2000, "share_list.php");
            exit;
        }
        default:
        {
            alert("error", "Lỗi không xác định", 2000, "share_list.php");
            exit;
        }
    }
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case "delete":
        {
            $sharepage = new sharepage($_GET['id']);
            if ($sharepage->id == -1) {
                alert("lỗi", "ID trang không tồn tại", 2000, "share_list.php");
            } else {
                if ($sharepage->owner == $_SESSION['user_id']) {
                    $sharepage->delete();
                    alert("thành công", "Xóa thành công", 2000, "share_list.php");
                } else {
                    alert("cảnh báo", "Không có quyền truy cập", 2000, "share_list.php");
                }
            }
            exit;
        }

        case "add":
        {
            $share_link = random_string(12);
            $width = isMobile() ? "auto" : "60%";
            $account_list_result = $conn->prepare("SELECT id,username FROM account WHERE owner=:owner;");
            $account_list_result->execute(['owner' => $_SESSION['user_id']]);
            if ($account_list_result->rowCount() == 0) {
                alert("cảnh báo", "Vui lòng thêm tài khoản trước", 2000, "account.php");
                exit;
            } else {
                $account_list = array();
                while ($row = $account_list_result->fetch()) {
                    $account_list[$row['id']] = $row['username'];
                }
            }
            echo "<div class='container' style='margin-top: 2%; width: $width;'>
                    <div class='card border-dark'>
                        <h4 class='card-header bg-primary text-white text-center'>Thêm trang chia sẻ</h4>
                        <form action='' method='post' style='margin: 20px;'>
                            <span class='input-group-text' id='account_list'>Vui lòng chọn tài khoản</span>
                            <div class='form-check mb-3'>";
            foreach ($account_list as $id => $username) {
                echo "$username <input class='form-check-input' type='checkbox' role='switch' name='account_list[]' value='$id'><br>";
            }
            echo "</div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='share_link'>Mã chia sẻ</span>
                                <input type='text' class='form-control' name='share_link' value='$share_link' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='password'>Mật khẩu trang</span>
                                <input type='text' class='form-control' name='password' placeholder='Để trống nếu không sử dụng mật khẩu' autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='remark'>Ghi chú</span>
                                <input type='text' class='form-control' name='remark' autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='html'>Nội dung HTML</span>
                                <textarea name='html' cols='80' rows=4></textarea>
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
                alert("error", "Thiếu tham số", 2000, "share_list.php");
                exit;
            }
            $width = isMobile() ? "auto" : "60%";
            $sharepage = new sharepage($_GET['id']);
            if ($sharepage->id == -1) {
                alert("error", "ID trang chia sẻ không tồn tại", 2000, "share_list.php");
                exit;
            }
            if ($sharepage->owner != $_SESSION['user_id']) {
                alert("error", "Không có quyền chỉnh sửa", 2000, "share_list.php");
                exit;
            }
            $account_list_result = $conn->prepare("SELECT id,username FROM account WHERE owner=:owner;");
            $account_list_result->execute(['owner' => $_SESSION['user_id']]);
            if ($account_list_result->rowCount() == 0) {
                alert("warning", "Vui lòng thêm tài khoản trước", 2000, "account.php");
                exit;
            } else {
                $account_list = array();
                while ($row = $account_list_result->fetch()) {
                    $account_list[$row['id']] = $row['username'];
                }
            }
            echo "<div class='container' style='margin-top: 2%; width: $width;'>
                    <div class='card border-dark'>
                        <h4 class='card-header bg-primary text-white text-center'>Thêm trang chia sẻ</h4>
                        <form action='' method='post' style='margin: 20px;'>
                            <span class='input-group-text' id='account_list'>Vui lòng chọn tài khoản</span>
                            <div class='form-check mb-3'>";
            foreach ($account_list as $id => $username) {
                $selected = in_array($id, $sharepage->account_list) ? "checked" : "";
                echo "$username <input class='form-check-input' type='checkbox' role='switch' name='account_list[]' $selected value='$id'><br>";
            }
            echo "</div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='share_link'>Mã chia sẻ</span>
                                <input type='text' class='form-control' name='share_link' value='$sharepage->share_link' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='password'>Mật khẩu trang</span>
                                <input type='text' class='form-control' name='password' value='$sharepage->password' placeholder='Để trống nếu không sử dụng mật khẩu' autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='remark'>Ghi chú</span>
                                <input type='text' class='form-control' name='remark' value='$sharepage->remark' autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='html'>Nội dung HTML</span>
                                <textarea name='html' cols='80' rows=4>$sharepage->html</textarea>
                            </div>
                            <input type='submit' name='submit' class='btn btn-primary btn-block' value='Lưu'>
                        </form>
                    </div>
                </div>";
            exit;
        }

        default:
        {
            alert("error", "Lỗi không rõ", 2000, "share_list.php");
            exit;
        }
    }
} else {
    alert("error", "Thiếu thông số", 2000, "share_list.php");
    exit;
}