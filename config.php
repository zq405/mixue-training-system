<?php
// config.php - 支持云数据库环境变量

// 安全启动 session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 获取数据库配置（优先使用环境变量）
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db = getenv('DB_NAME') ?: 'mixue_db';

// Render.com 的 PostgreSQL 支持（如果需要）
if (getenv('DATABASE_URL')) {
    $url = parse_url(getenv('DATABASE_URL'));
    $host = $url['host'];
    $user = $url['user'];
    $pass = $url['pass'];
    $db = ltrim($url['path'], '/');
}

// 创建连接
$conn = new mysqli($host, $user, $pass, $db);

// 检查连接
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Unable to connect to database. Please try again later.");
}

// 设置时区
date_default_timezone_set('Asia/Kuala_Lumpur');

// 设置字符集
$conn->set_charset("utf8mb4");
?>