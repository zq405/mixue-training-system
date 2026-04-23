<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
include_once 'lang.php';

$username = $_SESSION['user'];
$category = $_GET['cat'] ?? 'front';
$title = ($category == 'front') ? t('front_training') : t('back_training');

// 获取用户已完成的培训
$completedQuery = $conn->query("SELECT training_id FROM progress 
                                WHERE username='$username' 
                                AND status='completed'");
$completedIds = [];
while ($row = $completedQuery->fetch_assoc()) {
    $completedIds[] = $row['training_id'];
}

// 获取培训内容
$result = $conn->query("SELECT * FROM training WHERE category='$category' ORDER BY id");

// 获取总数和已完成数量
$totalCount = $result->num_rows;
$completedCount = 0;
$trainings = [];
while ($row = $result->fetch_assoc()) {
    $trainings[] = $row;
    if (in_array($row['id'], $completedIds)) {
        $completedCount++;
    }
}
$percentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="<?php echo $lang == 'zh' ? 'zh-CN' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $title; ?> - Mixue Training</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .training-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* 页面头部 */
        .page-header {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            font-size: 32px;
            color: #d6001c;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
            font-size: 16px;
        }

        .progress-summary {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 10px;
            display: inline-block;
        }

        /* 语言切换 */
        .lang-switch {
            text-align: right;
            margin-bottom: 20px;
        }
        .lang-btn {
            background: #d6001c;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            text-decoration: none;
            margin-left: 10px;
            font-size: 14px;
        }
        .lang-btn:hover {
            background: #a80016;
        }

        /* 培训卡片 */
        .training-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .training-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background: linear-gradient(135deg, #d6001c, #ff6b6b);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .card-header h2 {
            font-size: 22px;
            margin: 0;
        }

        .status-badge {
            background: rgba(255,255,255,0.3);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
        }

        .status-badge.completed {
            background: #28a745;
        }

        .status-badge.pending {
            background: #6c757d;
        }

        .card-content {
            padding: 25px;
            line-height: 1.8;
            color: #333;
        }

        .card-footer {
            padding: 15px 25px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .complete-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .complete-btn:hover {
            background: #218838;
            transform: scale(1.05);
        }

        .complete-btn.disabled {
            background: #6c757d;
            cursor: not-allowed;
        }

        .complete-btn.disabled:hover {
            transform: none;
        }

        .completed-date {
            color: #28a745;
            font-size: 14px;
        }

        /* 进度条 */
        .progress-bar-container {
            background: #e9ecef;
            border-radius: 10px;
            height: 10px;
            margin: 15px 0;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, #28a745, #d6001c);
            height: 100%;
            transition: width 0.5s ease;
            border-radius: 10px;
        }

        /* 导航按钮 */
        .nav-buttons {
            text-align: center;
            margin-top: 30px;
        }

        .nav-btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin: 0 10px;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* 响应式 */
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }
            .card-footer {
                flex-direction: column;
                text-align: center;
            }
        }

        /* 动画 */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate {
            animation: fadeInUp 0.5s ease forwards;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<?php include 'nav.php'; ?>

<div class="training-container">
    <!-- 语言切换 -->
    <div class="lang-switch">
        <a href="?cat=<?php echo $category; ?>&lang=zh" class="lang-btn">中文</a>
        <a href="?cat=<?php echo $category; ?>&lang=en" class="lang-btn">English</a>
    </div>

    <!-- 页面头部 -->
    <div class="page-header animate">
        <h1>
            <?php if ($category == 'front'): ?>
                🍽️ <?php echo t('front_training'); ?>
            <?php else: ?>
                🔧 <?php echo t('back_training'); ?>
            <?php endif; ?>
        </h1>
        <p>
            <?php if ($category == 'front'): ?>
                <?php echo $lang == 'zh' ? '前台服务 · 点单收银 · 冰淇淋制作 · 饮品SOP' : 'Front Service · Ordering · Ice Cream · Beverage SOP'; ?>
            <?php else: ?>
                <?php echo $lang == 'zh' ? '切柠檬 · 煮珍珠 · 冰淇淋 · 咖啡 · 茶饮 · 后场注意事项' : 'Lemon Cutting · Pearl Cooking · Ice Cream · Coffee · Tea · Precautions'; ?>
            <?php endif; ?>
        </p>
        <div class="progress-summary">
            📊 <?php echo $lang == 'zh' ? '完成进度' : 'Progress'; ?>: <?php echo $completedCount; ?>/<?php echo $totalCount; ?>
            (<?php echo $percentage; ?>%)
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: <?php echo $percentage; ?>%"></div>
            </div>
        </div>
    </div>

    <?php
    $index = 0;
    foreach ($trainings as $row) {
        $index++;
        $isCompleted = in_array($row['id'], $completedIds);
        $content = $row['content'];
        
        // 根据语言显示内容
        if ($lang == 'zh') {
            if (preg_match('/【中文】\s*(.*?)\s*【English】/s', $content, $matches)) {
                $display_content = nl2br(htmlspecialchars(trim($matches[1])));
            } else {
                $display_content = nl2br(htmlspecialchars($content));
            }
        } else {
            if (preg_match('/【English】\s*(.*?)$/s', $content, $matches)) {
                $display_content = nl2br(htmlspecialchars(trim($matches[1])));
            } elseif (preg_match('/【English】\s*(.*?)\s*【/s', $content, $matches)) {
                $display_content = nl2br(htmlspecialchars(trim($matches[1])));
            } else {
                $display_content = nl2br(htmlspecialchars($content));
            }
        }
        ?>
        
        <div class="training-card animate" style="animation-delay: <?php echo $index * 0.05; ?>s">
            <div class="card-header">
                <h2><?php echo $index . '. ' . htmlspecialchars($row['title']); ?></h2>
                <div class="status-badge <?php echo $isCompleted ? 'completed' : 'pending'; ?>">
                    <?php if ($isCompleted): ?>
                        ✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?>
                    <?php else: ?>
                        ⏳ <?php echo $lang == 'zh' ? '未开始' : 'Not Started'; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-content">
                <?php echo $display_content; ?>
            </div>
            <div class="card-footer">
                <?php if ($isCompleted): ?>
                    <div class="completed-date">
                        ✅ <?php echo $lang == 'zh' ? '已于' : 'Completed on'; ?> 
                        <?php 
                        $dateQuery = $conn->query("SELECT completed_at FROM progress WHERE username='$username' AND training_id=" . $row['id']);
                        if ($dateQuery && $dateQuery->num_rows > 0) {
                            $dateRow = $dateQuery->fetch_assoc();
                            echo date('Y-m-d H:i', strtotime($dateRow['completed_at']));
                        }
                        ?>
                    </div>
                    <button class="complete-btn disabled" disabled>
                        ✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?>
                    </button>
                <?php else: ?>
                    <div></div>
                    <a href="mark.php?id=<?php echo $row['id']; ?>&from=training.php&cat=<?php echo $category; ?>" class="complete-btn">
                        ✅ <?php echo $lang == 'zh' ? '标记为已完成' : 'Mark as Completed'; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    ?>

    <!-- 导航按钮 -->
    <div class="nav-buttons">
        <a href="dashboard.php?lang=<?php echo $lang; ?>" class="nav-btn">🏠 <?php echo $lang == 'zh' ? '返回首页' : 'Back to Home'; ?></a>
        <?php if ($category == 'front'): ?>
            <a href="back_training.php?lang=<?php echo $lang; ?>" class="nav-btn">🔧 <?php echo $lang == 'zh' ? '后场培训' : 'Back Training'; ?></a>
        <?php else: ?>
            <a href="training.php?cat=front&lang=<?php echo $lang; ?>" class="nav-btn">🍽️ <?php echo $lang == 'zh' ? '前场培训' : 'Front Training'; ?></a>
        <?php endif; ?>
        <a href="progress.php?lang=<?php echo $lang; ?>" class="nav-btn">📊 <?php echo $lang == 'zh' ? '查看进度' : 'View Progress'; ?></a>
    </div>
</div>


</body>
</html>