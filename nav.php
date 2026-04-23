<?php
// nav.php - 导航栏组件
include_once 'lang.php';
?>
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
</style>

<div class="nav">
    <a href="dashboard.php">🏠 <?php echo t('home'); ?></a>
    <a href="training.php?cat=front">🍽️ <?php echo t('front_training'); ?></a>
    <a href="back_training.php">🔧 <?php echo t('back_training'); ?></a>
    <a href="quiz.php">📝 <?php echo t('quiz'); ?></a>
    <a href="progress.php">📊 <?php echo t('progress'); ?></a>
    <a href="logout.php">🚪 <?php echo t('logout'); ?></a>
</div>