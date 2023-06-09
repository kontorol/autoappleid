<?php
include("header.php");
global $conn;
if (isset($_POST['submit'])) {
    switch ($_GET['action']) {
        case "add":
        {
            if (get_account_id($_POST['username']) != -1) {
                alert("warning", "Tài khoản đã tồn tại", 2000, "account.php");
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO account (
                     username, 
                     password, 
                     remark, 
                     dob, 
                     question1, 
                     answer1,
                     question2,
                     answer2,
                     question3,
                     answer3,
                     owner,
                     share_link,
                     check_interval,
                     frontend_remark,
                     message,
                     enable_check_password_correct, 
                     enable_delete_devices,
                     enable_auto_update_password) 
                     VALUES 
                         (:username,
                          :password,
                          :remark,
                          :dob,
                          :question1,
                          :answer1,
                          :question2,
                          :answer2,
                          :question3,
                          :answer3,
                          :owner,
                          :share_link,
                          :check_interval,
                          :frontend_remark,
                          :message,
                          :enable_check_password_correct,
                          :enable_delete_devices,
                          :enable_auto_update_password);");
            $stmt->execute([
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'remark' => $_POST['remark'],
                'dob' => $_POST['dob'],
                'question1' => $_POST['question1'],
                'answer1' => $_POST['answer1'],
                'question2' => $_POST['question2'],
                'answer2' => $_POST['answer2'],
                'question3' => $_POST['question3'],
                'answer3' => $_POST['answer3'],
                'owner' => $_SESSION['user_id'],
                'share_link' => $_POST['share_link'],
                'check_interval' => $_POST['check_interval'],
                'frontend_remark' => $_POST['frontend_remark'],
                'message' => "Nhiệm vụ chưa thực hiện",
                'enable_check_password_correct' => isset($_POST['enable_check_password_correct']) ? 1 : 0,
                'enable_delete_devices' => isset($_POST['enable_delete_devices']) ? 1 : 0,
                'enable_auto_update_password' => isset($_POST['enable_auto_update_password']) ? 1 : 0
            ]);
            alert("success", "Thêm thành công", 2000, "account.php");
            exit;
        }
        case "edit":
        {
            $account = new account($_GET['id']);
            if ($account->owner == $_SESSION['user_id'] || $account->id) {
                $data = array(
                    'username' => $_POST['username'],
                    'password' => $_POST['password'],
                    'remark' => $_POST['remark'],
                    'dob' => $_POST['dob'],
                    'question1' => $_POST['question1'],
                    'answer1' => $_POST['answer1'],
                    'question2' => $_POST['question2'],
                    'answer2' => $_POST['answer2'],
                    'question3' => $_POST['question3'],
                    'answer3' => $_POST['answer3'],
                    'owner' => $_SESSION['user_id'],
                    'share_link' => $_POST['share_link'],
                    'check_interval' => $_POST['check_interval'],
                    'frontend_remark' => $_POST['frontend_remark'],
                    'enable_check_password_correct' => isset($_POST['enable_check_password_correct']),
                    'enable_delete_devices' => isset($_POST['enable_delete_devices']),
                    'enable_auto_update_password' => isset($_POST['enable_auto_update_password'])
                );
                $account->update($data);
                alert("success", "Sửa đổi thành công", 2000, "account.php");
            } else {
                alert("error", "Không thể chỉnh sửa", 2000, "account.php");
            }
            exit;
        }
        default:
        {
            alert("error", "lỗi không rõ", 2000, "account.php");
            exit;
        }
    }
}
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case "delete":
        {
            if (!isset($_GET['id'])) {
                alert("error", "Thiếu thông số", 2000, "account.php");
                exit;
            }
            $account = new account($_GET['id']);
            if ($account->owner == $_SESSION['user_id'] || $account->id) {
                $account->delete();
                alert("success", "Xóa thành công", 2000, "account.php");
            } else {
                alert("error", "Không thể xóa", 2000, "account.php");
            }
            exit;
        }
        case "add":
        {
            $share_link = random_string(12);
            $width = isMobile() ? "auto" : "60%";
            echo "<div class='container' style='margin-top: 2%; width: $width;'>
                    <div class='card border-dark'>
                        <h4 class='card-header bg-primary text-white text-center'>Thêm tài khoản</h4>
                        <form action='' method='post' style='margin: 20px;'>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='id'>Tên đăng nhập</span>
                                <input type='text' class='form-control' name='username' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='password'>Password</span>
                                <input type='text' class='form-control' name='password' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='remark'>Ghi chú</span>
                                <input type='text' class='form-control' name='remark' autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='frontend_remark'>Ghi chú phía trước</span>
                                <input type='text' class='form-control' name='frontend_remark' placeholder='Mô tả tài khoản, được hiển thị trên trang chia sẻ' autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='dob'>Ngày sinh</span>
                                <input type='text' class='form-control' name='dob' placeholder='Ngày sinh：mmddyyyy' required autocomplete='off' maxlength='8' minlength='8'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='question1'>Câu hỏi 1</span>
                                <input type='text' class='form-control' name='question1' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='answer1'>Câu trả lời 1</span>
                                <input type='text' class='form-control' name='answer1' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='question2'>Câu hỏi 2</span>
                                <input type='text' class='form-control' name='question2' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='answer2'>Câu trả lời 2</span>
                                <input type='text' class='form-control' name='answer2' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='question3'>Câu hỏi 3</span>
                                <input type='text' class='form-control' name='question3' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='answer3'>Câu trả lời 3</span>
                                <input type='text' class='form-control' name='answer3' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='question2'>Mã chia sẻ</span>
                                <input type='text' class='form-control' name='share_link' value='$share_link' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='check_interval'>Khoảng thời gian kiểm tra</span>
                                <input type='number' class='form-control' name='check_interval' required autocomplete='off' placeholder='Đơn vị: phút' value='10'>
                            </div>
                            <div class='input-group mb-3'>
                                <div class='form-check form-switch'>
                                  Bật chức năng kiểm tra mật khẩu đúng<input class='form-check-input' type='checkbox' name='enable_check_password_correct'>
                                  Nếu mật khẩu sai thì tự động thay đổi mật khẩu
                                </div>
                            </div>
                            <div class='input-group mb-3'>
                                <div class='form-check form-switch'>
                                  Bật chức năng xóa thiết bị<input class='form-check-input' type='checkbox' name='enable_delete_devices'>
                                </div>
                            </div>
                            <div class='input-group mb-3'>
                                <div class='form-check form-switch'>
                                  Bật chức năng tự động thay đổi mật khẩu<input class='form-check-input' type='checkbox' name='enable_auto_update_password'>
                                </div>
                            </div>
                            <input type='submit' name='submit' class='btn btn-primary btn-block' value='Thêm vào'>
                        </form>
                    </div>
                </div>";
            exit;
        }
        case "edit":
        {
            if (!isset($_GET['id'])) {
                alert("error", "Thiếu thông số", 2000, "account.php");
                exit;
            }
            $account = new account($_GET['id']);
            if ($account->id == -1) {
                alert("error", "Tài khoản không tồn tại", 2000, "account.php");
                exit;
            }
            if ($account->owner == $_SESSION['user_id']) {
                $width = isMobile() ? "auto" : "60%";
                $question1 = array_keys($account->question)[0];
                $question2 = array_keys($account->question)[1];
                $question3 = array_keys($account->question)[2];
                $answer1 = $account->question[$question1];
                $answer2 = $account->question[$question2];
                $answer3 = $account->question[$question3];
                $check_interval = $account->check_interval;
                $check_password_checked = $account->enable_check_password_correct ? "checked" : "";
                $delete_devices_checked = $account->enable_delete_devices ? "checked" : "";
                $auto_update_password_checked = $account->enable_auto_update_password ? "checked" : "";
                echo "<div class='container' style='margin-top: 2%; width: $width;'>
                    <div class='card border-dark'>
                        <h4 class='card-header bg-primary text-white text-center'>Chỉnh sửa tài khoản</h4>
                        <form action='' method='post' style='margin: 20px;'>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='username'>Tên tài khoản</span>
                                <input type='text' class='form-control' name='username' required autocomplete='off' value='$account->username'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='password'>Mật Khẩu</span>
                                <input type='text' class='form-control' name='password' required autocomplete='off' value='$account->password'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='remark'>Ghi chú</span>
                                <input type='text' class='form-control' name='remark' autocomplete='off' value='$account->remark'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='frontend_remark'>Nhận xét đầu cuối</span>
                                <input type='text' class='form-control' name='frontend_remark' placeholder='账号的说明，在分享页显示' autocomplete='off' value='$account->frontend_remark'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='dob'>Ngày Sinh</span>
                                <input type='text' class='form-control' name='dob' placeholder='Ngày sinh：mmddyyyy' required autocomplete='off' value='$account->dob' maxlength='8' minlength='8'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='question1'>Câu hỏi 1</span>
                                <input type='text' class='form-control' name='question1' required autocomplete='off' value='$question1'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='answer1'>Câu trả lời 1</span>
                                <input type='text' class='form-control' name='answer1' required autocomplete='off' value='$answer1'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='question2'>Câu hỏi 2</span>
                                <input type='text' class='form-control' name='question2' required autocomplete='off' value='$question2'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='answer2'>Câu trả lời 2</span>
                                <input type='text' class='form-control' name='answer2' required autocomplete='off' value='$answer2'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='question3'>Câu hỏi 3</span>
                                <input type='text' class='form-control' name='question3' required autocomplete='off' value='$question3'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='answer3'>Câu trả lời 3</span>
                                <input type='text' class='form-control' name='answer3' required autocomplete='off' value='$answer3'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='share_link'>Link chia sẽ</span>
                                <input type='text' class='form-control' name='share_link' value='$account->share_link' required autocomplete='off'>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='last_check'>Kiểm tra cuối cùng</span>
                                <input type='text' class='form-control' name='share_link' value='$account->last_check' required disabled>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='message'>Tình trạng</span>
                                <input type='text' class='form-control' name='message' value='$account->message' required autocomplete='off' disabled>
                            </div>
                            <div class='input-group mb-3'>
                                <div class='form-check form-switch'>
                                  Kích hoạt phát hiện mật khẩu chính xác<input class='form-check-input' type='checkbox' name='enable_check_password_correct' $check_password_checked>
                                </div>
                            </div>
                            <div class='input-group mb-3'>
                                <div class='form-check form-switch'>
                                  Bật xóa thiết bị<input class='form-check-input' type='checkbox' name='enable_delete_devices' $delete_devices_checked>
                                </div>
                            </div>
                            <div class='input-group mb-3'>
                                <div class='form-check form-switch'>
                                  Bật thay đổi mật khẩu tự động<input class='form-check-input' type='checkbox' name='enable_auto_update_password' $auto_update_password_checked>
                                </div>
                            </div>
                            <div class='input-group mb-3'>
                                <span class='input-group-text' id='check_interval'>Kiểm tra khoảng thời gian</span>
                                <input type='number' class='form-control' name='check_interval' placeholder='单位：分钟' required autocomplete='off' value='$check_interval'>
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
            alert("error", "Sai lầm không rõ", 2000, "account.php");
            exit;
        }
    }
} else {
    alert("error", "Thiếu thông số", 2000, "account.php");
}