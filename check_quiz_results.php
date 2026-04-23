<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$username = $_SESSION['user'];

echo "<h2>测验结果调试 - 用户: $username</h2>";

// 查看 quiz_results 表
echo "<h3>quiz_results 表记录：</h3>";
$result = $conn->query("SELECT * FROM quiz_results WHERE username='$username' ORDER BY completed_at DESC");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>得分</th><th>总分</th><th>百分比</th><th>完成时间</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['score']}</td>";
        echo "<td>{$row['total']}</td>";
        echo "<td>{$row['percentage']}%</td>";
        echo "<td>{$row['completed_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>没有找到测验记录</p>";
}

// 查看 overall_progress 表
echo "<h3>overall_progress 表记录：</h3>";
$result = $conn->query("SELECT * FROM overall_progress WHERE username='$username'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "<p>没有找到总体进度记录</p>";
}

echo "<br><a href='quizizz.php'>返回测验</a> | ";
echo "<a href='progress.php'>查看进度</a>";
?>