<?php
// header.php - 统一的页面头部组件
// 注意：不要在这里再次调用 session_start()，因为已经在 lang.php 中处理了
include_once 'lang.php';
?>
<style>
/* Header 样式 */
.main-header {
    background: #d6001c;
    color: white;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.logo-area {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo-img {
    height: 60px;
    width: auto;
    border-radius: 10px;
    transition: transform 0.3s ease;
}

.logo-img:hover {
    transform: scale(1.05);
}

.logo-text h1 {
    margin: 0;
    font-size: 24px;
    letter-spacing: 1px;
}

.logo-text p {
    margin: 5px 0 0;
    font-size: 12px;
    opacity: 0.9;
}

.welcome-area {
    text-align: right;
}

.welcome-area .user-name {
    font-size: 14px;
    background: rgba(255,255,255,0.2);
    padding: 5px 12px;
    border-radius: 20px;
    display: inline-block;
}

.lang-selector {
    margin-top: 8px;
}

.lang-link {
    color: white;
    text-decoration: none;
    font-size: 12px;
    background: rgba(255,255,255,0.2);
    padding: 3px 8px;
    border-radius: 15px;
    margin: 0 3px;
    transition: 0.3s;
}

.lang-link:hover, .lang-link.active {
    background: white;
    color: #d6001c;
}

@media (max-width: 768px) {
    .main-header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    .logo-area {
        justify-content: center;
    }
    .welcome-area {
        text-align: center;
    }
    .logo-img {
        height: 45px;
    }
    .logo-text h1 {
        font-size: 18px;
    }
}
</style>

<header class="main-header">
    <div class="logo-area">
        <img src="images/Mixue-Logo.jpg" alt="Mixue Logo" class="logo-img" onerror="this.src='https://placehold.co/60x60/d6001c/white?text=MIXUE'">
        <div class="logo-text">
            <h1>🍦 <?php echo t('brand_name'); ?></h1>
            <p><?php echo t('brand_slogan'); ?></p>
        </div>
    </div>
    <div class="welcome-area">
        <?php if(isset($_SESSION['user'])): ?>
            <div class="user-name">👋 <?php echo t('welcome'); ?>, <?php echo htmlspecialchars($_SESSION['user']); ?></div>
            <div class="lang-selector">
                <a href="?lang=zh" class="lang-link <?php echo current_lang() == 'zh' ? 'active' : ''; ?>">中文</a>
                <a href="?lang=en" class="lang-link <?php echo current_lang() == 'en' ? 'active' : ''; ?>">English</a>
            </div>
        <?php endif; ?>
    </div>
</header>