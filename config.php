<?php
// config.php - 简化稳定版本

// 启动 session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 数据库配置
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db = getenv('DB_NAME') ?: 'mixue_db';

// 创建连接
$conn = new mysqli($host, $user, $pass, $db);

// 检查连接
if ($conn->connect_error) {
    // 记录错误但不显示详细信息
    error_log("DB Connection failed: " . $conn->connect_error);
    die("系统维护中，请稍后重试。");
}

// 设置字符集
$conn->set_charset("utf8mb4");

// 设置时区
date_default_timezone_set('Asia/Kuala_Lumpur');
?>