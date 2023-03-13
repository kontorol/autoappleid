<?php
include($_SERVER['DOCUMENT_ROOT'] . "/config.php");
include($_SERVER['DOCUMENT_ROOT'] . "/include/function.php");
include($_SERVER['DOCUMENT_ROOT'] . "/include/user.php");
include($_SERVER['DOCUMENT_ROOT'] . "/include/account.php");
include($_SERVER['DOCUMENT_ROOT'] . "/include/sharepage.php");
include($_SERVER['DOCUMENT_ROOT'] . "/include/proxy.php");
header('Content-type:text/json');
global $Sys_config;
try {
    $conn = new PDO("mysql:host={$Sys_config["db_host"]};dbname={$Sys_config["db_database"]};", $Sys_config["db_user"], $Sys_config["db_password"]);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Disable emulation of prepared statements
    $conn->exec("set names utf8"); // Set character set to utf8
} catch (PDOException $e) {
    die("Failed to connect to database, error message: " . $e->getMessage());
}

global $conn;
if (!isset($_GET['key'])) {
    $data = array(
        'status' => 'fail',
        'message' => 'key không thể để trống'
    );
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
} else if ($_GET['key'] != $Sys_config['apikey']) {
    $data = array(
        'status' => 'fail',
        'message' => 'key không đúng'
    );
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
switch ($_GET["action"]) {
    case "get_task_list":
    {
        $result = $conn->prepare("SELECT id FROM account;");
        $result->execute();
        $task_list = [];
        while ($row = $result->fetch()) {
            $task_list[] = $row['id'];
        }
        $data = array(
            'status' => 'success',
            'message' => 'Thành công',
            'data' => implode(",", $task_list)
        );
        break;
    }
    case "get_task_info":
    {
        if (!isset($_GET['id'])) {
            $data = array(
                'status' => 'fail',
                'message' => 'ID không thể để trống'
            );
        } else {
            $account = new account($_GET['id']);
            if ($account->id == -1) {
                $data = array(
                    'status' => 'fail',
                    'message' => 'Nhiệm vụ không tồn tại'
                );
            } else {
                $question1 = array_keys($account->question)[0];
                $question2 = array_keys($account->question)[1];
                $question3 = array_keys($account->question)[2];
                $answer1 = $account->question[$question1];
                $answer2 = $account->question[$question2];
                $answer3 = $account->question[$question3];
                $data = array(
                    'status' => 'success',
                    'message' => 'Thành công',
                    'username' => $account->username,
                    'password' => $account->password,
                    'dob' => $account->dob,
                    'q1' => array_keys($account->question)[0],
                    'q2' => array_keys($account->question)[1],
                    'q3' => array_keys($account->question)[2],
                    'a1' => $account->question[$question1],
                    'a2' => $account->question[$question2],
                    'a3' => $account->question[$question3],
                    'check_interval' => $account->check_interval,
                    'tgbot_token' => $Sys_config['telegram_bot_token'],
                    'tgbot_chatid' => $Sys_config["telegram_bot_chatid"],
                    'API_key' => $Sys_config['apikey'],
                    'API_url' => $Sys_config['apiurl'],
                    'webdriver' => $Sys_config['webdriver_url'],
                );
                if ($account->enable_check_password_correct) $data['check_password_correct'] = true;
                if ($account->enable_delete_devices) $data['delete_devices'] = true;
                if ($account->enable_auto_update_password) $data['auto_update_password'] = true;
                if ($Sys_config["task_headless"]) $data['headless'] = true;
                if ($Sys_config['enable_proxy_pool']){
                    $proxy = get_random_proxy($account->owner);
                    if ($proxy->id != -1){
                        $proxy->update_use();
                        $data['proxy_id'] = $proxy->id;
                        $data['proxy'] = $proxy->protocol . "://" . $proxy->content;
                    }
                }
                break;
            }
        }
        break;
    }
    case "update_message":
    {
        if (!isset($_GET['username'])) {
            $data = array(
                'status' => 'fail',
                'message' => 'Username không được để trống'
            );
        } else {
            $account = new account(get_account_id($_GET['username']));
            if ($account->id == -1) {
                $data = array(
                    'status' => 'fail',
                    'message' => 'Tài khoản không tồn tại'
                );
            } else {
                $account->update_message($_GET['message']);
                $data = array(
                    'status' => 'success',
                    'message' => 'Hoàn thành cập nhật'
                );
            }
        }
        break;
    }
    case "get_password":
    {
        if (!isset($_GET['username'])) {
            $data = array(
                'status' => 'fail',
                'message' => 'Tên ngươi dùng không được để trống'
            );
        } else {
            $result = $conn->prepare("SELECT password FROM account WHERE username = :username;");
            $result->execute([':username' => $_GET['username']]);
            if ($result->rowCount() == 0) {
                $data = array(
                    'status' => 'fail',
                    'message' => 'Tài khoản không tồn tại'
                );
            } else {
                $row = $result->fetch();
                $data = array(
                    'status' => 'success',
                    'message' => 'Thành công lấy mật khẩu',
                    'password' => $row['password']
                );
            }
        }
        break;
    }
    case "update_password":
    {
        if (!isset($_GET['username']) || !isset($_GET['password'])) {
            $data = array(
                'status' => 'fail',
                'message' => 'ID hoặc mật khẩu không được để trống'
            );
        } else {
            $account = new account(get_account_id($_GET['username']));
            if ($account->id == -1) {
                $data = array(
                    'status' => 'fail',
                    'message' => 'Tài khoản không tồn tại'
                );
            } else {
                $account->update_password($_GET['password']);
                $data = array(
                    'status' => 'success',
                    'message' => 'Hoàn thành cập nhật'
                );
            }
        }
        break;
    }
    case "check_api":
    {
        $data = array(
            'status' => 'success',
            'message' => 'API là bình thường'
        );
        break;
    }
    case "random_sharepage_password":
    {
        if (!isset($_GET['id'])) {
            $data = array(
                'status' => 'fail',
                'message' => 'ID trang không được để trống'
            );
        }
        $sharepage = new sharepage($_GET['id']);
        if ($sharepage->id == -1) {
            $data = array(
                'status' => 'fail',
                'message' => 'Trang không tồn tại'
            );
        } else {
            $sharepage->randomPassword();
            $data = array(
                'status' => 'success',
                'password' => $sharepage->password,
                'message' => 'Hoàn thành cập nhật'
            );
        }
        break;
    }
    case "report_proxy_error":
    {
        if (!$Sys_config['proxy_auto_disable']||!$Sys_config['enable_proxy_pool']){
            $data = array(
                'status' => 'fail',
                'message' => 'Nhóm proxy không được bật hoặc proxy tự động bị tắt'
            );
            break;
        }
        if (!isset($_GET['id'])) {
            $data = array(
                'status' => 'fail',
                'message' => 'ID không được để trống'
            );
        } else {
            $proxy = new proxy($_GET['id']);
            if ($proxy->id == -1) {
                $data = array(
                    'status' => 'fail',
                    'message' => 'ID ủy quyền không tồn tại'
                );
            } else {
                $proxy->update_use();
                $proxy->set_disable();
                $data = array(
                    'status' => 'success',
                    'message' => 'Báo cáo thành công'
                );            }
        }
        break;
    }
    default:
    {
        $data = array(
            'status' => 'fail',
            'message' => 'action错误'
        );
        break;
    }
}
echo json_encode($data, JSON_UNESCAPED_UNICODE);