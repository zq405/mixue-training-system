<?php
// login_process.php
// 安全启动 session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

$username = $_POST['username'];
$password = $_POST['password'];

// 注意：生产环境应使用密码哈希验证
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $_SESSION['user'] = $username;
    header("Location: dashboard.php");
} else {
    echo "登录失败，<a href='login.php'>返回重试</a>";
}
?>