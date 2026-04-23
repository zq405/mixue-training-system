<?php
// dashboard.php
// 先包含 lang.php（它会处理 session_start）
include_once 'lang.php';

// 检查登录状态
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?php echo current_lang() == 'zh' ? 'zh-CN' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mixue Training - <?php echo t('home'); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            background: #ffccd5;
            padding: 10px;
            flex-wrap: wrap;
        }
        .nav a {
            padding: 8px 15px;
            background: white;
            border-radius: 25px;
            text-decoration: none;
            color: #d6001c;
            font-weight: bold;
            transition: 0.3s;
        }
        .nav a:hover {
            background: #d6001c;
            color: white;
            transform: translateY(-2px);
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 20px auto;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        button {
            background: #d6001c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background: #a80016;
        }
        footer {
            background: #d6001c;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 30px;
        }
        .step-list {
            margin: 15px 0;
            padding-left: 20px;
        }
        .step-list li {
            margin: 8px 0;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="nav">
    <a href="dashboard.php">🏠 <?php echo t('home'); ?></a>
    <a href="training.php?cat=front">🍽️ <?php echo t('front_training'); ?></a>
    <a href="back_training.php">🔧 <?php echo t('back_training'); ?></a>
    <a href="quiz.php">📝 <?php echo t('quiz'); ?></a>
    <a href="progress.php">📊 <?php echo t('progress'); ?></a>
    <a href="logout.php">🚪 <?php echo t('logout'); ?></a>
</div>

<div class="container">
    <div class="card">
        <h2>👋 <?php echo t('welcome_message'); ?></h2>
        <p><?php echo t('welcome_subtitle'); ?></p>
        <ul class="step-list">
            <li>✅ <?php echo t('front_training'); ?> - <?php echo t('front_desc'); ?></li>
            <li>✅ <?php echo t('back_training'); ?> - <?php echo t('back_desc'); ?></li>
            <li>✅ <?php echo t('quiz'); ?> - <?php echo t('quiz_desc'); ?></li>
        </ul>
    </div>

    <div class="card">
        <h3>🍽️ <?php echo t('front_training'); ?></h3>
        <p><?php echo t('front_desc'); ?></p>
        <a href="training.php?cat=front"><button><?php echo t('start_learning'); ?> →</button></a>
    </div>

    <div class="card">
        <h3>🔧 <?php echo t('back_training'); ?></h3>
        <p><?php echo t('back_desc'); ?></p>
        <a href="back_training.php"><button><?php echo t('start_learning'); ?> →</button></a>
    </div>

    <div class="card">
        <h3>📝 <?php echo t('quiz'); ?></h3>
        <p><?php echo t('quiz_desc'); ?></p>
        <a href="quiz.php"><button><?php echo t('start_quiz'); ?> →</button></a>
    </div>

    <div class="card">
        <h3>📊 <?php echo t('progress'); ?></h3>
        <p><?php echo t('progress_desc'); ?></p>
        <a href="progress.php"><button><?php echo t('view_progress'); ?> →</button></a>
    </div>
</div>

<footer>
    <p><?php echo t('copyright'); ?></p>
</footer>


</body>
</html>