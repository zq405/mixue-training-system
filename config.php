// config.php 示例 (支持环境变量)
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 优先读取 Render 上设置的环境变量，如果没有则使用本地默认值
$host = getenv('DB_HOST') ?: 'mixuetrianingsystem';
$user = getenv('DB_USER') ?: 'admin405';
$pass = getenv('DB_PASS') ?: 'mixuesk';
$db = getenv('DB_NAME') ?: 'mixue_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
date_default_timezone_set('Asia/Kuala_Lumpur');
?>