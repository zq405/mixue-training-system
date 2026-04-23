<?php
// lang.php - 多语言配置文件
require_once 'config.php';

// 获取当前语言，默认中文
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'zh';

// 保存语言到 session
$_SESSION['lang'] = $lang;

// 定义所有翻译文本
$translations = [
    'zh' => [
        // 通用
        'welcome' => '欢迎',
        'home' => '首页',
        'front_training' => '前场培训',
        'back_training' => '后场培训',
        'quiz' => '知识测验',
        'progress' => '学习进度',
        'logout' => '退出登录',
        'language' => '语言',
        
        // Header
        'brand_name' => '蜜雪冰城',
        'brand_slogan' => 'SINCE 1997 · ICE CREAM & TEA',
        
        // Dashboard
        'welcome_message' => '欢迎加入蜜雪冰城培训系统',
        'welcome_subtitle' => '恭喜你成为蜜雪冰城大家庭的一员！请完成以下培训：',
        'front_desc' => '',
        'back_desc' => '切柠檬 · 煮珍珠 · 冰淇淋 · 咖啡 · 茶饮制作 · 后场注意事项',
        'quiz_desc' => '测试你对前场和后场知识的掌握程度',
        'progress_desc' => '查看你已经完成的培训模块',
        'start_learning' => '开始学习',
        'start_quiz' => '开始测验',
        'view_progress' => '查看进度',
        
        // Footer
        'copyright' => '© 2026 蜜雪冰城培训系统 | 甜蜜蜜，你笑得甜蜜蜜~ 🎵',
        
        // 其他
        'back_to_dashboard' => '返回仪表板',
        'mark_completed' => '标记为已完成',
        'my_progress' => '我的学习进度',
        'completed' => '已完成',
        'no_progress' => '还没有完成任何培训，请先去学习。',
        'training_content' => '培训内容',
        'complete_training' => '完成培训'
    ],
    'en' => [
        // 通用
        'welcome' => 'Welcome',
        'home' => 'Home',
        'front_training' => 'Front Training',
        'back_training' => 'Back Training',
        'quiz' => 'Quiz',
        'progress' => 'Progress',
        'logout' => 'Logout',
        'language' => 'Language',
        
        // Header
        'brand_name' => 'Mixue',
        'brand_slogan' => 'SINCE 1997 · ICE CREAM & TEA',
        
        // Dashboard
        'welcome_message' => 'Welcome to Mixue Training System',
        'welcome_subtitle' => 'Congratulations on joining the Mixue family! Please complete the training:',
        'front_desc' => '',
        'back_desc' => 'Lemon cutting · Pearl cooking · Ice cream · Coffee · Tea making · Back area precautions',
        'quiz_desc' => 'Test your knowledge of front and back area operations',
        'progress_desc' => 'Check your completed training modules',
        'start_learning' => 'Start Learning',
        'start_quiz' => 'Start Quiz',
        'view_progress' => 'View Progress',
        
        // Footer
        'copyright' => '© 2026 Mixue Training System | Sweet as honey~ 🎵',
        
        // 其他
        'back_to_dashboard' => 'Back to Dashboard',
        'mark_completed' => 'Mark as Completed',
        'my_progress' => 'My Learning Progress',
        'completed' => 'Completed',
        'no_progress' => 'No training completed yet. Please start learning.',
        'training_content' => 'Training Content',
        'complete_training' => 'Complete Training'
    ]
];

// 获取翻译函数
function t($key) {
    global $lang, $translations;
    return $translations[$lang][$key] ?? $key;
}

// 获取当前语言
function current_lang() {
    global $lang;
    return $lang;
}
?>