<?php
include("header.php");

if (isset($_POST['submit'])) {
    $currentuser = new user($_POST['userid']);
    if (($currentuser->user_id) == -1) {
        alert("error", "Người dùng không tồn tại!", 2000, "user.php");
        exit;
    }
    $currentuser->update($_POST['username'], $_POST['isadmin']);
    if ($_POST['password'] != "") {
        $currentuser->change_password($_POST['password']);
    }
    alert("success", "Đã cập nhật thông tin người dùng thành công!", 2000, "user.php");
    exit;
}


if (isset($_GET['action'])) {
    if (!isset($_GET["id"])) {
        alert("error", "Lỗi tham số!", 2000, "user.php");
        exit;
    }
    $currentuser = new user($_GET["id"]);
    if ($currentuser->user_id == 0) {
        alert("error", "Người dùng không tồn tại!", 2000, "user.php");
        exit;
    }
    switch ($_GET["action"]) {
        case "edit":
        {
            // Code xử lý khi người dùng chọn chức năng "Chỉnh sửa"
            break;
        }
        case "delete":
        {
            // Xóa tài khoản người dùng
            $currentuser->delete_account();
            alert("success", "Đã xóa tài khoản người dùng thành công!", 2000, "user.php");
            exit;
        }
        default:
        {
            alert("error", "Lỗi tham số!", 2000, "user.php");
            exit;
        }
    }
}


?>
<div class="container" style="margin-top: 2%;width: <?php echo (isMobile()) ? "auto" : "30%"; ?>;">
    <div class='card border-dark'>
        <h4 class='card-header bg-primary text-white text-center'>Chỉnh sửa thông tin người dùng</h4>
        <form action='' method='post' style="margin: 20px;">
            <div class="input-group mb-3">
                <span class='input-group-text' id='userid'>ID người dùng</span>
                <input type='text' class='form-control' name='userid'
                       autocomplete='off' <?php echo "value='$currentuser->user_id'"; ?>
                       readonly>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='username'>Tên đăng nhập</span>
                <input type='text' class='form-control' name='username'
                       autocomplete='off' <?php echo "value='$currentuser->username'"; ?>
                       required>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='password'>Mật khẩu</span>
                <input type='password' class='form-control' name='password' autocomplete='off'
                       placeholder='Để trống nếu không muốn thay đổi'>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='isadmin'>Quản trị viên</span>
                <select class="btn btn-info dropdown-toggle" name='isadmin' required>
                    <option value=0>Không</option>
                    <option value=1 <?php if ($currentuser->is_admin) echo "selected" ?>>Có</option>
                </select>
            </div>
            <input type='submit' class='btn btn-primary' name='submit' value='Lưu'>
        </form>
    </div>
</div>
