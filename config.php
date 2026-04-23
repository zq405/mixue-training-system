<?php
// config.php - 简化版
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 数据库配置（优先使用环境变量）
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$db = getenv('DB_NAME') ?: 'mixue_db';

// 支持 DATABASE_URL 格式
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
    die("数据库连接失败: " . $conn->connect_error);
}

// 设置字符集
$conn->set_charset("utf8mb4");

// 设置时区
date_default_timezone_set('Asia/Kuala_Lumpur');
?>