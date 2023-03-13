<?php
header('Content-Type: text/html; charset=UTF-8');
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include("function.php");


//Enable error reporting
if ($Sys_config["debug"]) {
    ini_set("display_errors", "On");
    error_reporting(E_ALL);
}

try{
    $conn = new PDO("mysql:host={$Sys_config["db_host"]};dbname={$Sys_config["db_database"]};", $Sys_config["db_user"], $Sys_config["db_password"]);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // 禁用prepared statements的模拟效果
    $conn->exec("set names utf8"); //设置编码
} catch (PDOException $e) {
    die("Không thể kết nối cơ sở dữ liệu, thông tin lỗi: " . $e->getMessage());
}


//检查php_self()是否可用
if (php_self() == "") {
    die("Không thể lấy tên tập tin PHP, vui lòng kiểm tra giá trị của cgi.fix_pathinfo trong tệp php.ini có phải là 1 không");
}


//Initialize session
session_start();
if (!isset($_SESSION["isLogin"])) {
    $_SESSION["isLogin"] = false;
}

include($_SERVER['DOCUMENT_ROOT'] . "/include/user.php");
include($_SERVER['DOCUMENT_ROOT'] . "/include/account.php");
include($_SERVER['DOCUMENT_ROOT'] . "/include/sharepage.php");
include($_SERVER['DOCUMENT_ROOT'] . "/include/proxy.php");

//Initialize CSS
echo '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">
    <script src="../resources/js/bootstrap.min.js"></script>
    <script src="../resources/js/sweetalert2.all.min.js"></script>
    <link href="../resources/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../resources/js/clipboard.min.js"></script>
</head>
';