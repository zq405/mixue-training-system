<?php
// login_process.php - 修复版本
session_start();

// 开启错误显示（调试用）
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 包含数据库配置
include 'config.php';

// 获取表单数据
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// 验证输入
if (empty($username) || empty($password)) {
    die("请填写用户名和密码。");
}

// 查询数据库（使用预处理语句防止 SQL 注入）
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // 注意：生产环境应该使用 password_verify()
    if ($password === $user['password']) {
        $_SESSION['user'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "密码错误，<a href='login.php'>返回重试</a>";
    }
} else {
    echo "用户名不存在，<a href='login.php'>返回重试</a>";
}

$stmt->close();
$conn->close();
?>