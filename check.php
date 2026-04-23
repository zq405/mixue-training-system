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

// 获取用户提交的答案
$answers = $_POST['ans'] ?? [];
$corrects = $_POST['correct'] ?? [];

// 如果是从 quizizz.php 提交的，使用不同的参数名
if (empty($answers) && isset($_POST['user_answers'])) {
    $answers = json_decode($_POST['user_answers'], true);
}
if (empty($corrects) && isset($_POST['correct_answers'])) {
    $corrects = json_decode($_POST['correct_answers'], true);
}

$score = 0;
$total = count($corrects);

// 计算得分
foreach ($corrects as $i => $correct_answer) {
    if (isset($answers[$i]) && $answers[$i] == $correct_answer) {
        $score++;
    }
}

$percentage = ($total > 0) ? round(($score / $total) * 100) : 0;

// 保存测验结果到数据库
$insertSql = "INSERT INTO quiz_results (username, score, total, percentage, completed_at) 
              VALUES ('$username', $score, $total, $percentage, NOW())";
if ($conn->query($insertSql)) {
    // 更新总体进度中的测验状态
    $passed = $percentage >= 60 ? 1 : 0;
    $conn->query("UPDATE overall_progress 
                  SET quiz_score = $score, 
                      quiz_passed = $passed,
                      last_updated = NOW() 
                  WHERE username = '$username'");
}

// 更新学习进度表中的测验记录
$conn->query("INSERT INTO progress (username, training_id, status, completed_at, score) 
              VALUES ('$username', 999, 'completed', NOW(), $score)
              ON DUPLICATE KEY UPDATE 
              status = 'completed', 
                      completed_at = NOW(), 
                      score = $score");

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $lang == 'zh' ? '测验结果' : 'Quiz Result'; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .result-card {
            background: white;
            border-radius: 30px;
            padding: 50px;
            text-align: center;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideIn 0.5s ease;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .score-circle {
            width: 200px;
            height: 200px;
            margin: 20px auto;
            position: relative;
        }
        .score-circle svg {
            transform: rotate(-90deg);
        }
        .score-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
        }
        .result-message {
            font-size: 24px;
            margin: 20px 0;
            color: #333;
        }
        .result-stats {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 15px;
            margin: 20px 0;
        }
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-secondary {
            background: #6c757d;
        }
        .correct-answer {
            color: #28a745;
            font-weight: bold;
        }
        .wrong-answer {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="result-card">
        <h1>📊 <?php echo $lang == 'zh' ? '测验结果' : 'Quiz Result'; ?></h1>
        
        <div class="score-circle">
            <svg width="200" height="200">
                <circle cx="100" cy="100" r="90" fill="none" stroke="#e9ecef" stroke-width="15"/>
                <circle cx="100" cy="100" r="90" fill="none" stroke="#667eea" stroke-width="15"
                        stroke-dasharray="<?php echo 2 * M_PI * 90; ?>" 
                        stroke-dashoffset="<?php echo 2 * M_PI * 90 * (1 - $percentage / 100); ?>"
                        stroke-linecap="round"/>
            </svg>
            <div class="score-text"><?php echo $score; ?>/<?php echo $total; ?></div>
        </div>
        
        <div class="result-message">
            <?php 
            if ($percentage >= 80) {
                echo $lang == 'zh' ? '🎉 太棒了！继续加油！' : '🎉 Excellent! Keep it up!';
            } elseif ($percentage >= 60) {
                echo $lang == 'zh' ? '👍 不错！再复习一下会更好' : '👍 Good! Review to do better';
            } else {
                echo $lang == 'zh' ? '💪 加油！建议重新学习培训内容' : '💪 Keep going! Review the training materials';
            }
            ?>
        </div>
        
        <div class="result-stats">
            <p><strong><?php echo $lang == 'zh' ? '得分' : 'Score'; ?>:</strong> <?php echo $score; ?> / <?php echo $total; ?></p>
            <p><strong><?php echo $lang == 'zh' ? '正确率' : 'Percentage'; ?>:</strong> <?php echo $percentage; ?>%</p>
            <?php if ($percentage >= 60): ?>
                <p style="color: #28a745;">✅ <?php echo $lang == 'zh' ? '恭喜你通过测验！' : 'Congratulations! You passed the quiz!'; ?></p>
            <?php else: ?>
                <p style="color: #dc3545;">⚠️ <?php echo $lang == 'zh' ? '未通过，需要达到60%才能获得证书' : 'Not passed. Need 60% to get certificate'; ?></p>
            <?php endif; ?>
        </div>
        
        <div class="button-group">
            <a href="quizizz.php?lang=<?php echo $lang; ?>" class="btn">🔄 <?php echo $lang == 'zh' ? '重新测验' : 'Retake Quiz'; ?></a>
            <a href="progress.php?lang=<?php echo $lang; ?>" class="btn btn-secondary">📊 <?php echo $lang == 'zh' ? '查看进度' : 'View Progress'; ?></a>
            <a href="dashboard.php?lang=<?php echo $lang; ?>" class="btn btn-secondary">🏠 <?php echo $lang == 'zh' ? '返回首页' : 'Back to Home'; ?></a>
        </div>
    </div>
</body>
</html>