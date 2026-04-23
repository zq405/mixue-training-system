<?php
// login.php - 确保没有输出错误
session_start();

// 如果已经登录，跳转到仪表盘
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - Mixue Training</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="card">
        <h2>🍦 Mixue Staff Training System</h2>
        <h3>Login</h3>
        <form method="POST" action="login_process.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</div>
</body>
</html>