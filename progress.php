<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
include_once 'lang.php';

$username = $_SESSION['user'];
$lang = current_lang();

// 获取总体进度
$overall = $conn->query("SELECT * FROM overall_progress WHERE username='$username'");
if ($overall->num_rows == 0) {
    $conn->query("INSERT INTO overall_progress (username) VALUES ('$username')");
    $overall = $conn->query("SELECT * FROM overall_progress WHERE username='$username'");
}
$overallData = $overall->fetch_assoc();

// 获取培训完成情况
$frontTotal = $conn->query("SELECT COUNT(*) as total FROM training WHERE category='front'")->fetch_assoc()['total'];
$backTotal = $conn->query("SELECT COUNT(*) as total FROM training WHERE category='back'")->fetch_assoc()['total'];

$frontCompleted = $conn->query("SELECT COUNT(DISTINCT p.training_id) as completed 
                                FROM progress p 
                                JOIN training t ON p.training_id = t.id 
                                WHERE p.username='$username' AND t.category='front' AND p.status='completed'")->fetch_assoc()['completed'];

$backCompleted = $conn->query("SELECT COUNT(DISTINCT p.training_id) as completed 
                               FROM progress p 
                               JOIN training t ON p.training_id = t.id 
                               WHERE p.username='$username' AND t.category='back' AND p.status='completed'")->fetch_assoc()['completed'];

// 获取最近测验成绩
$lastQuiz = $conn->query("SELECT * FROM quiz_results WHERE username='$username' ORDER BY completed_at DESC LIMIT 1")->fetch_assoc();

// 获取详细进度列表
$progressList = $conn->query("
    SELECT t.id, t.title, t.category, p.status, p.completed_at, p.score, p.retake_count
    FROM training t
    LEFT JOIN progress p ON t.id = p.training_id AND p.username='$username'
    ORDER BY t.category, t.id
");
?>

<!DOCTYPE html>
<html lang="<?php echo $lang == 'zh' ? 'zh-CN' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'zh' ? '学习进度' : 'Learning Progress'; ?> - Mixue</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .progress-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* 头部卡片 */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        .stat-progress {
            margin-top: 15px;
        }

        /* 进度条 */
        .progress-bar-custom {
            background: #e9ecef;
            border-radius: 10px;
            height: 8px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            background: linear-gradient(90deg, #667eea, #764ba2);
            height: 100%;
            transition: width 0.5s ease;
            border-radius: 10px;
        }

        /* 分类卡片 */
        .category-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .category-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .category-title {
            font-size: 24px;
            font-weight: bold;
        }

        .category-title.front {
            color: #28a745;
        }

        .category-title.back {
            color: #fd7e14;
        }

        .category-badge {
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
        }

        /* 培训项目列表 */
        .training-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .training-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .training-item:hover {
            transform: translateX(5px);
            background: #e9ecef;
        }

        .training-info {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .training-status {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .status-completed {
            background: #d4edda;
            color: #28a745;
        }

        .status-in-progress {
            background: #fff3cd;
            color: #ffc107;
        }

        .status-pending {
            background: #f8d7da;
            color: #dc3545;
        }

        .training-details {
            flex: 1;
        }

        .training-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .training-meta {
            font-size: 12px;
            color: #666;
        }

        .training-score {
            text-align: right;
            min-width: 100px;
        }

        .score-value {
            font-size: 20px;
            font-weight: bold;
            color: #667eea;
        }

        .retake-badge {
            background: #ffc107;
            color: #333;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            margin-left: 10px;
        }

        .action-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }

        .action-btn:hover {
            background: #5a67d8;
            transform: scale(1.05);
        }

        /* 证书区域 */
        .certificate-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            margin-top: 30px;
        }

        .certificate-btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
            transition: 0.3s;
        }

        .certificate-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* 响应式 */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .training-item {
                flex-direction: column;
                text-align: center;
            }
            .training-info {
                flex-direction: column;
                margin-bottom: 15px;
            }
            .training-score {
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

<div class="progress-container">
    <!-- 统计卡片 -->
    <div class="stats-grid">
        <div class="stat-card animate">
            <div class="stat-icon">📚</div>
            <div class="stat-value"><?php echo $frontCompleted + $backCompleted; ?>/<?php echo $frontTotal + $backTotal; ?></div>
            <div class="stat-label"><?php echo $lang == 'zh' ? '已完成培训' : 'Completed Trainings'; ?></div>
            <div class="stat-progress">
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: <?php echo (($frontCompleted + $backCompleted) / ($frontTotal + $backTotal)) * 100; ?>%"></div>
                </div>
            </div>
        </div>

        <div class="stat-card animate" style="animation-delay: 0.1s">
            <div class="stat-icon">⭐</div>
            <div class="stat-value"><?php echo $lastQuiz ? $lastQuiz['score'] . '/' . $lastQuiz['total'] : '0/0'; ?></div>
            <div class="stat-label"><?php echo $lang == 'zh' ? '最新测验成绩' : 'Latest Quiz Score'; ?></div>
        </div>

        <div class="stat-card animate" style="animation-delay: 0.2s">
            <div class="stat-icon">🎯</div>
            <div class="stat-value"><?php 
                $totalProgress = ($frontCompleted + $backCompleted) / ($frontTotal + $backTotal) * 100;
                echo round($totalProgress, 1); ?>%
            </div>
            <div class="stat-label"><?php echo $lang == 'zh' ? '总体进度' : 'Overall Progress'; ?></div>
            <div class="stat-progress">
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: <?php echo $totalProgress; ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 前场培训进度 -->
    <div class="category-card animate" style="animation-delay: 0.3s">
        <div class="category-header">
            <div class="category-title front">🍽️ <?php echo $lang == 'zh' ? '前场培训' : 'Front Area Training'; ?></div>
            <div class="category-badge"><?php echo $frontCompleted; ?>/<?php echo $frontTotal; ?></div>
        </div>
        <div class="training-list">
            <?php
            $frontItems = [];
            while ($row = $progressList->fetch_assoc()) {
                if ($row['category'] == 'front') {
                    $frontItems[] = $row;
                }
            }
            
            foreach ($frontItems as $item):
                $status = $item['status'] ?? 'pending';
                $statusIcon = '';
                $statusClass = '';
                $statusText = '';
                
                if ($status == 'completed') {
                    $statusIcon = '✅';
                    $statusClass = 'status-completed';
                    $statusText = $lang == 'zh' ? '已完成' : 'Completed';
                } elseif ($status == 'in_progress') {
                    $statusIcon = '🔄';
                    $statusClass = 'status-in-progress';
                    $statusText = $lang == 'zh' ? '学习中' : 'In Progress';
                } else {
                    $statusIcon = '⏳';
                    $statusClass = 'status-pending';
                    $statusText = $lang == 'zh' ? '未开始' : 'Not Started';
                }
            ?>
                <div class="training-item" onclick="location.href='training.php?cat=front'">
                    <div class="training-info">
                        <div class="training-status <?php echo $statusClass; ?>">
                            <?php echo $statusIcon; ?>
                        </div>
                        <div class="training-details">
                            <div class="training-title"><?php echo htmlspecialchars($item['title']); ?></div>
                            <div class="training-meta">
                                <?php echo $statusText; ?>
                                <?php if ($item['completed_at']): ?>
                                    • <?php echo date('Y-m-d H:i', strtotime($item['completed_at'])); ?>
                                <?php endif; ?>
                                <?php if ($item['retake_count'] > 0): ?>
                                    <span class="retake-badge">🔄 <?php echo $item['retake_count']; ?> <?php echo $lang == 'zh' ? '次复习' : 'retakes'; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="training-score">
                        <?php if ($status == 'completed'): ?>
                            <div class="score-value">✓</div>
                        <?php else: ?>
                            <button class="action-btn" onclick="event.stopPropagation(); location.href='training.php?cat=front'">
                                <?php echo $lang == 'zh' ? '开始学习' : 'Start'; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 后场培训进度 -->
    <div class="category-card animate" style="animation-delay: 0.4s">
        <div class="category-header">
            <div class="category-title back">🔧 <?php echo $lang == 'zh' ? '后场培训' : 'Back Area Training'; ?></div>
            <div class="category-badge"><?php echo $backCompleted; ?>/<?php echo $backTotal; ?></div>
        </div>
        <div class="training-list">
            <?php
            // 重置指针并获取后场数据
            $progressList->data_seek(0);
            $backItems = [];
            while ($row = $progressList->fetch_assoc()) {
                if ($row['category'] == 'back') {
                    $backItems[] = $row;
                }
            }
            
            foreach ($backItems as $item):
                $status = $item['status'] ?? 'pending';
                $statusIcon = '';
                $statusClass = '';
                $statusText = '';
                
                if ($status == 'completed') {
                    $statusIcon = '✅';
                    $statusClass = 'status-completed';
                    $statusText = $lang == 'zh' ? '已完成' : 'Completed';
                } elseif ($status == 'in_progress') {
                    $statusIcon = '🔄';
                    $statusClass = 'status-in-progress';
                    $statusText = $lang == 'zh' ? '学习中' : 'In Progress';
                } else {
                    $statusIcon = '⏳';
                    $statusClass = 'status-pending';
                    $statusText = $lang == 'zh' ? '未开始' : 'Not Started';
                }
            ?>
                <div class="training-item" onclick="location.href='back_training.php'">
                    <div class="training-info">
                        <div class="training-status <?php echo $statusClass; ?>">
                            <?php echo $statusIcon; ?>
                        </div>
                        <div class="training-details">
                            <div class="training-title"><?php echo htmlspecialchars($item['title']); ?></div>
                            <div class="training-meta">
                                <?php echo $statusText; ?>
                                <?php if ($item['completed_at']): ?>
                                    • <?php echo date('Y-m-d H:i', strtotime($item['completed_at'])); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="training-score">
                        <?php if ($status == 'completed'): ?>
                            <div class="score-value">✓</div>
                        <?php else: ?>
                            <button class="action-btn" onclick="event.stopPropagation(); location.href='back_training.php'">
                                <?php echo $lang == 'zh' ? '开始学习' : 'Start'; ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 证书区域 -->
    <?php
    $allCompleted = ($frontCompleted == $frontTotal && $backCompleted == $backTotal);
    $quizPassed = $lastQuiz && $lastQuiz['percentage'] >= 60;
    
    if ($allCompleted && $quizPassed):
    ?>
    <div class="certificate-card animate" style="animation-delay: 0.5s">
        <div class="stat-icon" style="font-size: 64px;">🏆</div>
        <h2><?php echo $lang == 'zh' ? '恭喜你完成全部培训！' : 'Congratulations! You completed all training!'; ?></h2>
        <p><?php echo $lang == 'zh' ? '你已经掌握了蜜雪冰城前场和后场的核心知识' : 'You have mastered the core knowledge of Mixue front and back areas'; ?></p>
        <button class="certificate-btn" onclick="generateCertificate()">
            📜 <?php echo $lang == 'zh' ? '下载结业证书' : 'Download Certificate'; ?>
        </button>
    </div>
    <?php endif; ?>
</div>

<script>
function generateCertificate() {
    // 创建证书窗口
    const certWindow = window.open('', '_blank', 'width=800,height=600');
    const username = '<?php echo $username; ?>';
    const date = new Date().toLocaleDateString('zh-CN');
    const frontCompleted = <?php echo $frontCompleted; ?>;
    const backCompleted = <?php echo $backCompleted; ?>;
    const quizScore = <?php echo $lastQuiz ? $lastQuiz['score'] : 0; ?>;
    
    certWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>蜜雪冰城培训证书</title>
            <style>
                body {
                    font-family: '楷体', 'Microsoft YaHei', serif;
                    margin: 0;
                    padding: 50px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .certificate {
                    background: white;
                    width: 800px;
                    padding: 60px;
                    border-radius: 20px;
                    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                    text-align: center;
                    border: 10px solid #ffd700;
                }
                .certificate h1 {
                    font-size: 48px;
                    color: #d6001c;
                    margin-bottom: 20px;
                }
                .certificate h2 {
                    font-size: 32px;
                    color: #333;
                    margin-bottom: 30px;
                }
                .certificate p {
                    font-size: 18px;
                    color: #666;
                    line-height: 1.8;
                }
                .certificate .name {
                    font-size: 36px;
                    font-weight: bold;
                    color: #667eea;
                    margin: 30px 0;
                    letter-spacing: 4px;
                }
                .certificate .date {
                    margin-top: 40px;
                    font-size: 14px;
                }
                .stamp {
                    margin-top: 30px;
                    font-size: 24px;
                    color: #d6001c;
                    transform: rotate(-15deg);
                }
                @media print {
                    body {
                        background: white;
                        padding: 0;
                    }
                    .certificate {
                        box-shadow: none;
                        border: 2px solid #ffd700;
                    }
                }
            </style>
        </head>
        <body>
            <div class="certificate">
                <h1>🍦 蜜雪冰城</h1>
                <h2>培训结业证书</h2>
                <p>兹证明</p>
                <div class="name">${username}</div>
                <p>已完成蜜雪冰城新员工培训课程<br>
                包含前场培训 ${frontCompleted} 项、后场培训 ${backCompleted} 项<br>
                最终测验成绩：${quizScore} 分</p>
                <p>特发此证，以资鼓励！</p>
                <div class="stamp">★ 蜜雪冰城培训部 ★</div>
                <div class="date">颁发日期：${date}</div>
            </div>
            <script>
                window.onload = function() {
                    setTimeout(() => {
                        window.print();
                    }, 500);
                };
            <\/script>
        </body>
        </html>
    `);
}

// 添加动画观察器
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

document.querySelectorAll('.animate').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'all 0.5s ease';
    observer.observe(el);
});
</script>

<?php include 'music_player.php'; ?>
</body>
</html>