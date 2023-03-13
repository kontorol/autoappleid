<?php
include("header.php");
$currentuser = new user($_SESSION['user_id']);
if (isset($_POST['submit'])) {
    if ($_POST['password'] != "") {
        $currentuser->change_password($_POST['password']);
        alert("success", "Thay đổi mật khẩu thành công", 2000,"userindex.php");
    }
}
?>
<title>Thông tin cá nhân</title>
<div class="container" style="margin-top: 2%;width: <?php echo (isMobile()) ? "auto" : "50%"; ?>;">
    <div class='card border-dark'>
        <h4 class='card-header bg-primary text-white text-center'>Thông tin cá nhân</h4>
        <form action='' method='post' style="margin: 20px;">
            <div class="input-group mb-3">
                <span class='input-group-text' id='username'>Tên đăng nhập</span>
                <input type='email' disabled class='form-control' name='username'
                       value='<?php echo $currentuser->username; ?>'>
            </div>
            <div class="input-group mb-3">
                <span class='input-group-text' id='name'>Mật khẩu</span>
                <input type='password' class='form-control' name='password' placeholder='Để trống nếu không muốn thay đổi'>
            </div>
            <input type='submit' class='btn btn-primary' name='submit' value='Lưu'>
        </form>
    </div>
</div>
