<?php
function logout()
{
    $_SESSION['isLogin'] = false;
    unset($_SESSION['user_id']);
    alert("success","Đăng xuất thành công!",2000,"index.php");
    exit;
}

function random_string($length): string
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

function isadmin($id): bool
{
    global $conn;
    $stmt = $conn->prepare("SELECT is_admin FROM user WHERE id=:id;");
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() == 0) {
        return false;
    } else {
        return $stmt->fetch()["is_admin"] == 1;
    }
}

function get_account_username($id): string
{
    global $conn;
    $stmt = $conn->prepare("SELECT username FROM account WHERE id=:id;");
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() == 0) {
        return "";
    } else {
        return $stmt->fetch()["username"];
    }
}

function get_id_by_username($username): int
{
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM user WHERE username=:username;");
    $stmt->execute(['username' => $username]);
    if ($stmt->rowCount() == 0) {
        return -1;
    } else {
        return $stmt->fetch()["id"];
    }
}

function get_account_id($username): int
{
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM account WHERE username=:username;");
    $stmt->execute(['username' => $username]);
    if ($stmt->rowCount() == 0) {
        return -1;
    } else {
        return $stmt->fetch()["id"];
    }
}

function get_username_by_id($id): string
{
    global $conn;
    $stmt = $conn->prepare("SELECT username FROM user WHERE id=:id;");
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() == 0) {
        return "";
    } else {
        return $stmt->fetch()["username"];
    }
}

function register($username, $password): array
{
    global $conn;
    if (get_id_by_username($username) != -1) {
        return array(false, "Người dùng đã tồn tại");
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO user (username, password) VALUES (:username, :password);");
        $stmt->execute(['username' => $username, 'password' => $password]);
        return array(true, "Đăng ký thành công");
    }
}

function login($username, $password): array
{
    global $conn;
    if (get_id_by_username($username) != -1) {
        $stmt = $conn->prepare("SELECT password FROM user WHERE username=:username;");
        $stmt->execute(['username' => $username]);
        $password_result = $stmt->fetch()["password"];
        if (password_verify($password, $password_result)) {
            return array(true, "Đăng nhập thành công");
        } else {
            return array(false, "Mật khẩu không đúng");
        }
    } else {
        return array(false, "Người dùng không tồn tại");
    }
}

function get_share_account_id($share_link): int
{
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM account WHERE share_link=:share_link;");
    $stmt->execute(['share_link' => $share_link]);
    if ($stmt->rowCount() == 0) {
        return -1;
    } else {
        return $stmt->fetch()["id"];
    }
}

function get_time()
{
    #date_default_timezone_set('Europe/London');
    return date('Y-m-d H:i:s');
}

function check_sharelink_exist($share_link): bool
{
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM share WHERE share_link = :share_link;");
    $stmt->execute(['share_link' => $share_link]);
    return $stmt->rowCount() != 0;
}

function get_share_id($share_link): int
{
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM share WHERE share_link = :share_link;");
    $stmt->execute(['share_link' => $share_link]);
    if ($stmt->rowCount() == 0) {
        return -1;
    } else {
        return $stmt->fetch()["id"];
    }
}

function isMobile(): bool
{
    // Nếu có HTTP_X_WAP_PROFILE thì chắc chắn là thiết bị di động
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return true;
    }
    // Nếu thông tin via chứa wap thì chắc chắn là thiết bị di động, một số nhà cung cấp dịch vụ sẽ chặn thông tin này
    if (isset($_SERVER['HTTP_VIA'])) {
        // Nếu không tìm thấy thì trả về false, ngược lại trả về true
        return (bool)stristr($_SERVER['HTTP_VIA'], "wap");
    }
    // Phương pháp ngu ngốc, kiểm tra từ khóa khách hàng gửi, tính tương thích còn cần cải thiện. Trong đó 'MicroMessenger' là WeChat trên máy tính
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel',
            'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi',
            'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile', 'MicroMessenger');
        // Tìm kiếm từ khóa của trình duyệt di động trong HTTP_USER_AGENT
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    // Phương pháp giao thức, vì có thể không chính xác, nên đặt ở cuối cùng để kiểm tra
    if (isset ($_SERVER['HTTP_ACCEPT'])) {
        // Nếu chỉ hỗ trợ wml và không hỗ trợ html thì chắc chắn là thiết bị di động
        // Nếu hỗ trợ cả wml và html nhưng wml đứng trước html thì chắc chắn là thiết bị di động
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') ===
                false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

function php_self()
{
    return substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1);
}

function alert($type, $message, $delay, $dest)
{
    switch ($type) {
        case "success":
            $title = "Thành công";
            break;
        case "error":
            $title = "Lỗi";
            break;
        case "warning":
            $title = "Cảnh báo";
            break;
        case "info":
            $title = "Thông tin";
            break;
        case "question":
            $title = "Vui lòng kiểm tra";
            break;
        default:
            $title = "";
            break;
    }
    echo "<script>Swal.fire({icon: '$type',title: '$title',text: '$message',timer:$delay,showConfirmButton: false,timerProgressBar: true});setTimeout(\"javascript:location.href='$dest'\", $delay);</script>";
}

function get_random_proxy($owner): proxy
{
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM proxy WHERE owner=:owner AND status=1 ORDER BY RAND() LIMIT 1;");
    $stmt->execute(['owner' => $owner]);
    return new proxy($stmt->rowCount() == 0?-1:$stmt->fetch()["id"]);
}