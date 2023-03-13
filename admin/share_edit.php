<?php
include("header.php");
global $conn;
if (isset($_POST['submit'])) {
    switch ($_GET['action']) {
        case "edit":
        {
            if (!isset($_GET['id'])) {
                alert("error", "Thiếu tham số", 2000, "shares.php");
                exit;
            }
            if (!isset($_POST['account_list'])) {
                alert("error", "Vui lòng thêm ít nhất một tài khoản", 2000, "shares.php");
                exit;
            }
            $sharepage = new sharepage($_GET['id']);
            $new_share_link = $_POST['share_link'];
            if ($sharepage->id==-1) {
                alert("error", "ID trang chia sẻ không tồn tại", 2000, "shares.php");
                exit;
            }
            if ($sharepage->share_link != $new_share_link && check_sharelink_exist($new_share_link)) {
                alert("error", "Liên kết chia sẻ đã tồn tại, không thể thêm mới", 2000, "shares.php");
                exit;
            }
            $data = array(
                'share_link' => $_POST['share_link'],
                'password' => $_POST['password'],
                'account_list' => $_POST['account_list'],
                'owner' => $_POST['owner'],
                'html' => $_POST['html'],
                'remark' => $_POST['remark']
            );
            $sharepage->update($data);
            alert("success", "Chỉnh sửa thành công", 2000, "shares.php");
            exit;
        }
        default:
        {
            alert("error", "Lỗi không xác định", 2000, "shares.php");
            exit;
        }
    }
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case "delete":
        {
            if (!isset($_GET['id'])) {
                alert("error", "Thiếu tham số", 2000, "shares.php");
                exit;
            }
            $sharepage = new sharepage($_GET['id']);
            if ($sharepage->id == -1) {
                alert("error", "ID trang chia sẻ không tồn tại", 2000, "shares.php");
                exit;
            }else{
                $sharepage->delete();
            }
            alert("success", "Xóa thành công", 2000, "shares.php");
            exit;
        }
        case "edit":
        {
            if (!isset($_GET['id'])) {
                alert("error", "Thiếu tham số", 2000, "shares.php");
                exit;
            }
            $sharepage = new sharepage($_GET['id']);
            $width = isMobile() ? "auto" : "60%";
            if ($sharepage->id == -1) {
                alert("error", "ID trang chia sẻ không tồn tại", 2000, "shares.php");
                exit;
            }
            $account_list_result = $conn->prepare("SELECT id,username FROM account WHERE owner=:owner;");
            $account_list_result->execute(['owner' => $sharepage->owner]);
            if ($account_list_result->rowCount() == 0) {
                alert("warning", "Người dùng không có tài khoản", 2000, "account.php");
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
                                <span class='input-group-text' id='owner'>ID người dùng</span>
                                <input type='text' class='form-control' name='owner' value='$sharepage->owner' required autocomplete='off'>
                            </div>
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
            alert("error", "Lỗi không xác định", 2000, "shares.php");
            exit;
        }
    }
} else {
    alert("error", "Thiếu tham số", 2000, "shares.php");
    exit;
}
