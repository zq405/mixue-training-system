<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

$username = $_SESSION['user'];
$lang = $_GET['lang'] ?? 'zh';

// 获取用户已完成的后场培训
$completedQuery = $conn->query("SELECT training_id FROM progress 
                                WHERE username='$username' 
                                AND status='completed'");
$completedIds = [];
while ($row = $completedQuery->fetch_assoc()) {
    $completedIds[] = $row['training_id'];
}

// 定义培训ID映射（与下方section对应）
$trainingIds = [
    1 => 4,   // 切柠檬 - 对应数据库中的id
    2 => 5,   // 煮珍珠
    3 => 6,   // 冰淇淋
    4 => 7,   // 咖啡
    5 => 8,   // 浓缩奶
    6 => 9,   // 茶类
    7 => 10   // 注意事项
];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang == 'zh' ? 'zh-CN' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo $lang == 'zh' ? '后场培训 - 蜜雪冰城' : 'Back Area Training - Mixue'; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* 保留原有的 back_training.php 样式 */
        .back-training { max-width: 1200px; margin: 0 auto; }
        .section { background: white; border-radius: 15px; margin-bottom: 30px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); position: relative; }
        .section-header { background: #d6001c; color: white; padding: 15px 20px; font-size: 22px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .section-content { padding: 20px; }
        .step { background: #f8f9fa; padding: 15px; margin: 15px 0; border-radius: 10px; border-left: 4px solid #d6001c; }
        .step-number { background: #d6001c; color: white; display: inline-block; width: 30px; height: 30px; text-align: center; line-height: 30px; border-radius: 50%; margin-right: 10px; font-weight: bold; }
        .warning { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 15px 0; border-radius: 8px; }
        .ingredient-list { background: #e8f4f8; padding: 15px; border-radius: 10px; margin: 10px 0; }
        .lang-switch { text-align: right; margin-bottom: 20px; }
        .lang-btn { background: #d6001c; color: white; padding: 5px 12px; border-radius: 20px; text-decoration: none; margin-left: 10px; }
        .nav { display: flex; justify-content: center; gap: 20px; background: #ffccd5; padding: 10px; flex-wrap: wrap; }
        .nav a { padding: 8px 15px; background: white; border-radius: 25px; text-decoration: none; color: #d6001c; font-weight: bold; }
        .nav a:hover { background: #d6001c; color: white; }
        .container { width: 90%; max-width: 1200px; margin: 20px auto; }
        
        /* 完成按钮样式 */
        .complete-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .complete-btn:hover {
            background: #218838;
            transform: scale(1.05);
        }
        .complete-btn.completed {
            background: #6c757d;
            cursor: default;
        }
        .complete-btn.completed:hover {
            transform: none;
        }
        .completed-badge {
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }
        .section-footer {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            text-align: right;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<?php include 'nav.php'; ?>

<div class="container back-training">
    <div class="lang-switch">
        <a href="?lang=zh" class="lang-btn">中文</a>
        <a href="?lang=en" class="lang-btn">English</a>
    </div>

    <!-- 1. 切柠檬 -->
    <div class="section" id="section1">
        <div class="section-header">
            <span>1. 🍋 <?php echo $lang == 'zh' ? '切柠檬' : 'Lemon Cutting'; ?></span>
            <?php if (in_array(4, $completedIds)): ?>
                <span class="completed-badge">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php endif; ?>
        </div>
        <div class="section-content">
            <div class="step">
                <span class="step-number">1</span> <?php echo $lang == 'zh' ? '可选择掐头去尾后用柠檬机切（请小心手）' : 'Cut off both ends, then use lemon machine (be careful with your hands)'; ?>
            </div>
            <div class="step">
                <span class="step-number">2</span> <?php echo $lang == 'zh' ? '或者用手切柠檬' : 'Or cut lemons by hand'; ?>
            </div>
            <div class="step">
                <span class="step-number">3</span> <?php echo $lang == 'zh' ? '使用后记得清洗柠檬机' : 'Remember to clean the lemon machine after use'; ?>
            </div>
        </div>
        <div class="section-footer">
            <?php if (in_array(4, $completedIds)): ?>
                <span class="complete-btn completed">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php else: ?>
                <a href="mark.php?id=4&from=back_training.php&cat=back" class="complete-btn">✅ 标记为已完成</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 2. 煮珍珠 -->
    <div class="section" id="section2">
        <div class="section-header">
            <span>2. ⚫ <?php echo $lang == 'zh' ? '煮珍珠' : 'Cooking Pearls'; ?></span>
            <?php if (in_array(5, $completedIds)): ?>
                <span class="completed-badge">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php endif; ?>
        </div>
        <div class="section-content">
            <div class="step"><span class="step-number">1</span> <?php echo $lang == 'zh' ? '倒入4000ml热水, 按1号键开始煮' : 'Pour 4000ml hot water, press button 1 to heat'; ?></div>
            <div class="step"><span class="step-number">2</span> <?php echo $lang == 'zh' ? '听见珍珠锅响后倒入珍珠' : 'When pearl cooker beeps, add pearls'; ?></div>
            <div class="step"><span class="step-number">3</span> <?php echo $lang == 'zh' ? '煮35分钟, 焖30分钟, 每次用前请检查时间' : 'Cook 35 min, simmer 30 min. Check time before each use'; ?></div>
            <div class="step"><span class="step-number">4</span> <?php echo $lang == 'zh' ? '珍珠煮好后过滤，用过滤水清洗至粒粒分明' : 'After cooking, filter and rinse with filtered water until separate'; ?></div>
            <div class="step"><span class="step-number">5</span> <?php echo $lang == 'zh' ? '清洗完成加入糖浆搅拌均匀即可使用' : 'Add syrup and mix well'; ?></div>
            <div class="ingredient-list">
                📦 <?php echo $lang == 'zh' ? '1包珍珠(500g)配1包糖浆' : '1 bag pearls (500g) with 1 bag syrup'; ?>
            </div>
        </div>
        <div class="section-footer">
            <?php if (in_array(5, $completedIds)): ?>
                <span class="complete-btn completed">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php else: ?>
                <a href="mark.php?id=5&from=back_training.php&cat=back" class="complete-btn">✅ <?php echo $lang == 'zh' ? '标记为已完成' : 'Mark as Completed'; ?></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 3. 冰淇淋 -->
    <div class="section" id="section3">
        <div class="section-header">
            <span>3. 🍦 <?php echo $lang == 'zh' ? '冰淇淋制作' : 'Ice Cream Making'; ?></span>
            <?php if (in_array(6, $completedIds)): ?>
                <span class="completed-badge">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php endif; ?>
        </div>
        <div class="section-content">
            <h3><?php echo $lang == 'zh' ? '原味冰淇淋' : 'Original Flavor'; ?></h3>
            <div class="ingredient-list">
                📊 <?php echo $lang == 'zh' ? '8000ml过滤水 + 3kg奶昔粉' : '8000ml filtered water + 3kg milkshake powder'; ?><br>
                📊 <?php echo $lang == 'zh' ? '4000ml水 + 1.5kg粉' : '4000ml water + 1.5kg powder'; ?><br>
                📊 <?php echo $lang == 'zh' ? '2000ml水 + 750g粉' : '2000ml water + 750g powder'; ?>
            </div>
            <h3><?php echo $lang == 'zh' ? '香芋冰淇淋' : 'Taro Flavor'; ?></h3>
            <div class="ingredient-list">
                📊 <?php echo $lang == 'zh' ? '8000ml过滤水 + 3kg香芋奶昔粉' : '8000ml filtered water + 3kg taro powder'; ?><br>
                📊 <?php echo $lang == 'zh' ? '4000ml水 + 1.5kg粉' : '4000ml water + 1.5kg powder'; ?><br>
                📊 <?php echo $lang == 'zh' ? '2000ml水 + 750g粉' : '2000ml water + 750g powder'; ?>
            </div>
            <div class="step">
                ⏲️ <?php echo $lang == 'zh' ? '计时器选18分钟开始搅拌, 3分钟后计时器响就静止, 计时器每响一次就搅拌一下' : 'Set timer for 18 min and start mixing. When timer rings after 3 min, let rest. Stir each time timer rings'; ?>
            </div>
        </div>
        <div class="section-footer">
            <?php if (in_array(6, $completedIds)): ?>
                <span class="complete-btn completed">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php else: ?>
                <a href="mark.php?id=6&from=back_training.php&cat=back" class="complete-btn">✅ <?php echo $lang == 'zh' ? '标记为已完成' : 'Mark as Completed'; ?></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 4. 咖啡 -->
    <div class="section" id="section4">
        <div class="section-header">
            <span>4. ☕ <?php echo $lang == 'zh' ? '咖啡制作' : 'Coffee Making'; ?></span>
            <?php if (in_array(7, $completedIds)): ?>
                <span class="completed-badge">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php endif; ?>
        </div>
        <div class="section-content">
            <div class="ingredient-list">
                📊 <?php echo $lang == 'zh' ? '600g热水 + 100g咖啡粉 + 600g冰' : '600g hot water + 100g coffee powder + 600g ice'; ?><br>
                📊 <?php echo $lang == 'zh' ? '1200g热水 + 200g咖啡粉 + 1200g冰(以此类推）' : '1200g hot water + 200g coffee powder + 1200g ice (and so on)'; ?>
            </div>
            <div class="step">
                🥄 <?php echo $lang == 'zh' ? '加入咖啡粉之后搅拌至咖啡粉融化' : 'After adding coffee powder, stir until dissolved'; ?>
            </div>
        </div>
        <div class="section-footer">
            <?php if (in_array(7, $completedIds)): ?>
                <span class="complete-btn completed">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php else: ?>
                <a href="mark.php?id=7&from=back_training.php" class="complete-btn">✅ <?php echo $lang == 'zh' ? '标记为已完成' : 'Mark as Completed'; ?></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 5. 浓缩奶 -->
    <div class="section" id="section5">
        <div class="section-header">
            <span>5. 🥛 <?php echo $lang == 'zh' ? '浓缩奶制作' : 'Concentrated Milk'; ?></span>
            <?php if (in_array(8, $completedIds)): ?>
                <span class="completed-badge">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php endif; ?>
        </div>
        <div class="section-content">
            <div class="ingredient-list">
                📐 <?php echo $lang == 'zh' ? '奶茶粉 : 热水 : 冰 = 1 : 1 : 1' : 'Milk tea powder : Hot water : Ice = 1 : 1 : 1'; ?>
            </div>
            <div class="step">1️⃣ <?php echo $lang == 'zh' ? '加入热水后加奶粉' : 'Add hot water, then add milk powder'; ?></div>
            <div class="step">2️⃣ 🌀 <?php echo $lang == 'zh' ? '用搅拌器搅拌至奶粉融化' : 'Use mixer to stir until dissolved'; ?></div>
            <div class="step">3️⃣ 🧊 <?php echo $lang == 'zh' ? '最后加冰搅拌至冰块融化' : 'Finally add ice and stir until ice dissolved'; ?></div>
        </div>
        <div class="section-footer">
            <?php if (in_array(8, $completedIds)): ?>
                <span class="complete-btn completed">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php else: ?>
                <a href="mark.php?id=8&from=back_training.php" class="complete-btn">✅ <?php echo $lang == 'zh' ? '标记为已完成' : 'Mark as Completed'; ?></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 6. 茶类 -->
    <div class="section" id="section6">
        <div class="section-header">
            <span>6. 🫖 <?php echo $lang == 'zh' ? '茶类制作' : 'Tea Making'; ?></span>
            <?php if (in_array(9, $completedIds)): ?>
                <span class="completed-badge">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php endif; ?>
        </div>
        <div class="section-content">
            <h3>🥤 <?php echo $lang == 'zh' ? '奶茶' : 'Milk Tea'; ?></h3>
            <div class="step">📦 <?php echo $lang=='zh'?'1包: 1400g水 → 650g奶茶粉 + 900g冰':'1 Pack: 1400g boiling water → 650g Milk tea powder + 900g ice'; ?></div>
            <div class="step">📦 <?php echo $lang=='zh'?'2包: 2800g水 → 1300g奶茶粉 + 1800g冰':'2 Pack: 2800g boiling water → 1300g Milk tea powder + 1800g ice'; ?></div>
            <div class="step">📦 <?php echo $lang=='zh'?'3包: 4200g水 → 1950g奶茶粉 + 2700g冰':'3 Pack: 4200 boiling water → 1950g Milk tea powder(whole pack of milk tea powder) + 2700g ice';?></div>
            
            <hr>
            
            <h3>🍵 <?php echo $lang == 'zh' ? '绿茶' : 'Green Tea'; ?></h3>
            <div class="step">📦 1包: 1500g热水 + 1000g冰</div>
            <div class="step">⚠️ <?php echo $lang == 'zh' ? '注意：茶叶在热水上面，不是在底下' : 'Note: Tea leaves on top of water, not at bottom'; ?></div>
            <div class="step">⏲️ <?php echo $lang == 'zh' ? '计时6分钟' : 'Timer: 6 minutes'; ?></div>
            
            <hr>
            
            <h3>🫖 <?php echo $lang == 'zh' ? '红茶' : 'Black Tea'; ?></h3>
            <div class="step">📦 1包: 1800g热水 + 1000g冰</div>
            <div class="step">📦 2包: 3600g热水 + 2000g冰</div>
            <div class="step">⏲️ <?php echo $lang == 'zh' ?'煮沸腾后焖12分钟' : 'Boil then simmer for 12 minutes'; ?></div>
        </div>
        <div class="section-footer">
            <?php if (in_array(9, $completedIds)): ?>
                <span class="complete-btn completed">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php else: ?>
                <a href="mark.php?id=9&from=back_training.php" class="complete-btn">✅ <?php echo $lang == 'zh' ? '标记为已完成' : 'Mark as Completed'; ?></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 7. 注意事项 -->
    <div class="section" id="section7">
        <div class="section-header">
            <span>7. ⚠️ <?php echo $lang == 'zh' ? '后场注意事项' : 'Back Area Precautions'; ?></span>
            <?php if (in_array(10, $completedIds)): ?>
                <span class="completed-badge">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php endif; ?>
        </div>
        <div class="section-content">
            <div class="warning">
                <span class="warning-icon">⚠️</span> <strong><?php echo $lang == 'zh' ? '随时注意前场是否缺料，及时补充' : 'Always monitor front area supplies, replenish promptly'; ?></strong>
            </div>
            <div class="warning">
                <span class="warning-icon">📦</span> <strong><?php echo $lang == 'zh' ? '货架空了请立即补货，切勿将货物放在地上（总部会扣分）' : 'Restock immediately when shelves empty. Never place goods on floor (Headquarters deducts points)'; ?></strong>
            </div>
            <div class="warning">
                <span class="warning-icon">🧹</span> <strong><?php echo $lang == 'zh' ? '保持后场整洁，空闲时间多抹货架' : 'Keep back area clean, wipe shelves in free time'; ?></strong>
            </div>
        </div>
        <div class="section-footer">
            <?php if (in_array(10, $completedIds)): ?>
                <span class="complete-btn completed">✅ <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></span>
            <?php else: ?>
                <a href="mark.php?id=10&from=back_training.php" class="complete-btn">✅ <?php echo $lang == 'zh' ? '标记为已完成' : 'Mark as Completed'; ?></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- 进度总结 -->
    <div class="section" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">
        <div class="section-header" style="background: rgba(0,0,0,0.2);">
            <span>📊 <?php echo $lang == 'zh' ? '学习进度' : 'Learning Progress'; ?></span>
        </div>
        <div class="section-content" style="text-align: center;">
            <?php
            $totalBack = 7;
            $completedBack = count(array_intersect($completedIds, [4,5,6,7,8,9,10]));
            $percentage = round(($completedBack / $totalBack) * 100);
            ?>
            <div style="font-size: 48px; font-weight: bold;"><?php echo $completedBack; ?>/<?php echo $totalBack; ?></div>
            <div style="font-size: 18px; margin: 10px 0;"><?php echo $lang == 'zh' ? '已完成后场培训模块' : 'Back area modules completed'; ?></div>
            <div style="background: rgba(255,255,255,0.3); border-radius: 10px; height: 10px; margin: 20px 0;">
                <div style="background: #28a745; width: <?php echo $percentage; ?>%; height: 10px; border-radius: 10px;"></div>
            </div>
            <div><?php echo $percentage; ?>% <?php echo $lang == 'zh' ? '完成' : 'Complete'; ?></div>
        </div>
    </div>

    <div style="text-align: center; margin: 30px 0;">
        <a href="dashboard.php" class="lang-btn" style="background: #28a745; padding: 12px 30px; font-size: 16px;">
            ✅ <?php echo $lang == 'zh' ? '完成学习，返回首页' : 'Complete Learning, Return Home'; ?>
        </a>
        <a href="progress.php" class="lang-btn" style="background: #667eea; padding: 12px 30px; font-size: 16px;">
            📊 <?php echo $lang == 'zh' ? '查看详细进度' : 'View Detailed Progress'; ?>
        </a>
    </div>
</div>


</body>
</html>