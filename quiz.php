<?php
// quiz.php - 重定向到 Quizizz 风格页面
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
header("Location: quizizz.php");
exit();
?>