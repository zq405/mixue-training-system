<?php
// mark.php - 完整的更新版本
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$username = $_SESSION['user'];
$training_id = intval($_GET['id']);
$redirect_from = isset($_GET['from']) ? $_GET['from'] : 'dashboard.php';
$category = isset($_GET['cat']) ? $_GET['cat'] : '';

// 如果没传递category，从数据库获取
if (empty($category)) {
    $catQuery = $conn->query("SELECT category FROM training WHERE id = $training_id");
    if ($catQuery && $catQuery->num_rows > 0) {
        $category = $catQuery->fetch_assoc()['category'];
    }
}

// 验证培训是否存在
$checkTraining = $conn->query("SELECT id, category FROM training WHERE id = $training_id");
if (!$checkTraining || $checkTraining->num_rows == 0) {
    header("Location: $redirect_from");
    exit();
}

// 检查是否已存在记录
$checkProgress = $conn->query("SELECT * FROM progress WHERE username='$username' AND training_id=$training_id");

if ($checkProgress && $checkProgress->num_rows > 0) {
    // 更新为已完成
    $sql = "UPDATE progress 
            SET status = 'completed', 
                completed_at = NOW(),
                retake_count = retake_count + 1 
            WHERE username='$username' AND training_id=$training_id";
} else {
    // 插入新记录
    $sql = "INSERT INTO progress (username, training_id, status, completed_at, retake_count) 
            VALUES ('$username', $training_id, 'completed', NOW(), 0)";
}

if ($conn->query($sql)) {
    // 更新总体进度 - 计算前场完成数量
    $frontCount = $conn->query("SELECT COUNT(*) as cnt FROM progress p 
                                JOIN training t ON p.training_id = t.id 
                                WHERE p.username='$username' AND t.category='front' AND p.status='completed'")->fetch_assoc()['cnt'];
    
    // 计算后场完成数量
    $backCount = $conn->query("SELECT COUNT(*) as cnt FROM progress p 
                               JOIN training t ON p.training_id = t.id 
                               WHERE p.username='$username' AND t.category='back' AND p.status='completed'")->fetch_assoc()['cnt'];
    
    // 更新或插入总体进度
    $overallResult = $conn->query("SELECT * FROM overall_progress WHERE username='$username'");
    if ($overallResult && $overallResult->num_rows > 0) {
        $conn->query("UPDATE overall_progress 
                      SET front_completed = $frontCount,
                          back_completed = $backCount,
                          last_updated = NOW() 
                      WHERE username='$username'");
    } else {
        $conn->query("INSERT INTO overall_progress (username, front_completed, back_completed, last_updated) 
                      VALUES ('$username', $frontCount, $backCount, NOW())");
    }
}

// 重定向回来源页面
header("Location: $redirect_from");
exit();
?>