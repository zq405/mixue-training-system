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

// 获取所有题目
$questions = [];
$result = $conn->query("SELECT * FROM quiz ORDER BY id");

// 检查是否有数据
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // 根据语言选择题目内容和选项
        $questionText = $row['question'];
        $optionA = $row['A'];
        $optionB = $row['B'];
        $optionC = $row['C'] ?? '';
        $optionD = $row['D'] ?? '';
        
        // 如果数据库中有中英文混合内容，提取对应语言
        if ($lang == 'zh') {
            // 提取中文部分
            if (preg_match('/【中文】(.*?)【English】/s', $questionText, $matches)) {
                $questionText = trim($matches[1]);
            }
            if (preg_match('/【中文】(.*?)【English】/s', $optionA, $matches)) {
                $optionA = trim($matches[1]);
            }
            if (preg_match('/【中文】(.*?)【English】/s', $optionB, $matches)) {
                $optionB = trim($matches[1]);
            }
            if ($optionC && preg_match('/【中文】(.*?)【English】/s', $optionC, $matches)) {
                $optionC = trim($matches[1]);
            }
            if ($optionD && preg_match('/【中文】(.*?)【English】/s', $optionD, $matches)) {
                $optionD = trim($matches[1]);
            }
        } else {
            // 提取英文部分
            if (preg_match('/【English】(.*?)$/s', $questionText, $matches)) {
                $questionText = trim($matches[1]);
            } elseif (preg_match('/【English】(.*?)【/s', $questionText, $matches)) {
                $questionText = trim($matches[1]);
            }
            if (preg_match('/【English】(.*?)$/s', $optionA, $matches)) {
                $optionA = trim($matches[1]);
            } elseif (preg_match('/【English】(.*?)【/s', $optionA, $matches)) {
                $optionA = trim($matches[1]);
            }
            if (preg_match('/【English】(.*?)$/s', $optionB, $matches)) {
                $optionB = trim($matches[1]);
            } elseif (preg_match('/【English】(.*?)【/s', $optionB, $matches)) {
                $optionB = trim($matches[1]);
            }
            if ($optionC) {
                if (preg_match('/【English】(.*?)$/s', $optionC, $matches)) {
                    $optionC = trim($matches[1]);
                } elseif (preg_match('/【English】(.*?)【/s', $optionC, $matches)) {
                    $optionC = trim($matches[1]);
                }
            }
            if ($optionD) {
                if (preg_match('/【English】(.*?)$/s', $optionD, $matches)) {
                    $optionD = trim($matches[1]);
                } elseif (preg_match('/【English】(.*?)【/s', $optionD, $matches)) {
                    $optionD = trim($matches[1]);
                }
            }
        }
        
        $questions[] = [
            'id' => (int)$row['id'],
            'question' => htmlspecialchars($questionText, ENT_QUOTES, 'UTF-8'),
            'A' => htmlspecialchars($optionA, ENT_QUOTES, 'UTF-8'),
            'B' => htmlspecialchars($optionB, ENT_QUOTES, 'UTF-8'),
            'C' => htmlspecialchars($optionC, ENT_QUOTES, 'UTF-8'),
            'D' => htmlspecialchars($optionD, ENT_QUOTES, 'UTF-8'),
            'answer' => htmlspecialchars($row['answer'] ?? '', ENT_QUOTES, 'UTF-8')
        ];
    }
}

$totalQuestions = count($questions);

// 将数据转换为 JSON 并处理特殊字符
$jsonData = json_encode($questions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
if ($jsonData === false) {
    $jsonData = '[]';
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang == 'zh' ? 'zh-CN' : 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizizz Style - <?php echo $lang == 'zh' ? '蜜雪冰城知识挑战赛' : 'Mixue Knowledge Challenge'; ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* 动画效果 */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes confetti {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        /* 主容器 */
        .quiz-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* 头部信息 */
        .quiz-header {
            background: white;
            border-radius: 20px;
            padding: 20px 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .quiz-title {
            font-size: 28px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .quiz-stats {
            display: flex;
            gap: 30px;
        }

        .stat-card {
            text-align: center;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        /* 语言切换按钮 */
        .lang-switch-quiz {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 8px 15px;
            border-radius: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .lang-switch-quiz a {
            color: #667eea;
            text-decoration: none;
            margin: 0 5px;
            font-weight: bold;
        }
        .lang-switch-quiz a.active {
            color: #d6001c;
        }

        /* 题目卡片 */
        .question-card {
            background: white;
            border-radius: 30px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            animation: slideIn 0.5s ease;
        }

        .question-number {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .question-text {
            font-size: 28px;
            font-weight: 600;
            color: #333;
            margin-bottom: 40px;
            line-height: 1.4;
        }

        /* 选项网格 */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .option {
            background: #f8f9fa;
            border: 3px solid #e9ecef;
            border-radius: 20px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .option:hover {
            transform: translateY(-5px);
            border-color: #667eea;
            background: #f0f3ff;
        }

        .option.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea20, #764ba220);
        }

        .option-letter {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }

        .option-text {
            flex: 1;
            font-size: 18px;
            color: #333;
        }

        /* 导航按钮 */
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .nav-btn {
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .prev-btn {
            background: #6c757d;
            color: white;
        }

        .next-btn, .submit-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .nav-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* 进度条 */
        .progress-bar-container {
            background: #e9ecef;
            border-radius: 10px;
            height: 10px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(90deg, #667eea, #764ba2);
            height: 100%;
            transition: width 0.5s ease;
            border-radius: 10px;
        }

        /* 结果页面 */
        .result-card {
            background: white;
            border-radius: 30px;
            padding: 50px;
            text-align: center;
            animation: slideIn 0.5s ease;
        }

        .score-circle {
            width: 200px;
            height: 200px;
            margin: 30px auto;
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
        }

        .result-details {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 20px;
            margin: 30px 0;
            text-align: left;
            max-height: 400px;
            overflow-y: auto;
        }

        .result-item {
            padding: 10px;
            margin: 5px 0;
            border-radius: 10px;
        }

        .result-item.correct {
            background: #d4edda;
            color: #155724;
        }

        .result-item.wrong {
            background: #f8d7da;
            color: #721c24;
        }

        .play-again-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            margin: 10px;
            text-decoration: none;
            display: inline-block;
        }

        /* 响应式 */
        @media (max-width: 768px) {
            .options-grid {
                grid-template-columns: 1fr;
            }
            .question-text {
                font-size: 20px;
            }
            .quiz-header {
                flex-direction: column;
                gap: 15px;
            }
            .question-card {
                padding: 20px;
            }
        }

        /* 加载状态 */
        .loading {
            text-align: center;
            padding: 50px;
            color: white;
            font-size: 20px;
        }
    </style>
</head>
<body>

<div class="quiz-container" id="quizApp">
    <!-- 头部 -->
    <div class="quiz-header">
        <div class="quiz-title">
            🍦 <?php echo $lang == 'zh' ? '蜜雪冰城知识挑战赛' : 'Mixue Knowledge Challenge'; ?>
        </div>
        <div class="quiz-stats">
            <div class="stat-card">
                <div class="stat-value" id="currentScore">0</div>
                <div class="stat-label">🏆 <?php echo $lang == 'zh' ? '得分' : 'Score'; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="questionCounter">0</div>
                <div class="stat-label">📝 <?php echo $lang == 'zh' ? '已完成' : 'Completed'; ?></div>
            </div>
        </div>
    </div>

    <!-- 进度条 -->
    <div class="progress-bar-container">
        <div class="progress-bar" id="progressBar" style="width: 0%"></div>
    </div>

    <!-- 题目区域 -->
    <div id="questionArea">
        <?php if ($totalQuestions == 0): ?>
            <div class="loading">
                <p>⚠️ <?php echo $lang == 'zh' ? '暂无题目数据，请联系管理员添加题目。' : 'No quiz questions available. Please contact administrator.'; ?></p>
                <a href="dashboard.php" class="play-again-btn"><?php echo $lang == 'zh' ? '返回首页' : 'Back to Home'; ?></a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 语言切换按钮 -->
<div class="lang-switch-quiz">
    <a href="?lang=zh" class="<?php echo $lang == 'zh' ? 'active' : ''; ?>">中文</a> | 
    <a href="?lang=en" class="<?php echo $lang == 'en' ? 'active' : ''; ?>">English</a>
</div>

<?php if ($totalQuestions > 0): ?>
<script>
// 测验数据
const questionsData = <?php echo $jsonData; ?>;
const questions = questionsData;
const totalQuestions = <?php echo $totalQuestions; ?>;
const lang = '<?php echo $lang; ?>';

let currentIndex = 0;
let userAnswers = new Array(totalQuestions).fill(null);
let score = 0;
let quizCompleted = false;

// DOM 元素
const questionArea = document.getElementById('questionArea');
const currentScoreEl = document.getElementById('currentScore');
const questionCounterEl = document.getElementById('questionCounter');
const progressBar = document.getElementById('progressBar');

// 初始化
function init() {
    if (totalQuestions > 0 && questions.length > 0) {
        renderQuestion();
        updateStats();
    } else {
        questionArea.innerHTML = '<div class="loading"><p>⚠️ <?php echo $lang == 'zh' ? '无法加载题目数据' : 'Unable to load questions'; ?></p><a href="dashboard.php" class="play-again-btn"><?php echo $lang == 'zh' ? '返回首页' : 'Back to Home'; ?></a></div>';
    }
}

// 渲染当前题目
function renderQuestion() {
    if (quizCompleted) {
        renderResults();
        return;
    }

    const q = questions[currentIndex];
    if (!q) {
        console.error('Question not found at index:', currentIndex);
        return;
    }
    
    const letters = ['A', 'B', 'C', 'D'];
    const options = [
        { letter: 'A', text: q.A },
        { letter: 'B', text: q.B },
        { letter: 'C', text: q.C },
        { letter: 'D', text: q.D }
    ].filter(opt => opt.text && opt.text.trim() !== '');
    
    // 判断是否是最后一题
    const isLastQuestion = (currentIndex === totalQuestions - 1);
    const buttonText = isLastQuestion ? (lang === 'zh' ? '📤 提交答案' : 'Submit Quiz') : (lang === 'zh' ? '下一题 →' : 'Next →');
    const buttonClass = isLastQuestion ? 'submit-btn' : 'next-btn';
    const buttonAction = isLastQuestion ? 'submitQuiz()' : 'nextQuestion()';
    
    const html = `
        <div class="question-card">
            <div class="question-number">
                ${lang === 'zh' ? '第' : 'Question'} ${currentIndex + 1} / ${totalQuestions}
            </div>
            <div class="question-text">
                ${escapeHtml(q.question)}
            </div>
            <div class="options-grid" id="optionsGrid">
                ${options.map(opt => `
                    <div class="option ${userAnswers[currentIndex] === opt.letter ? 'selected' : ''}" 
                         data-letter="${opt.letter}"
                         onclick="selectAnswer('${opt.letter}')">
                        <div class="option-letter">${opt.letter}</div>
                        <div class="option-text">${escapeHtml(opt.text)}</div>
                    </div>
                `).join('')}
            </div>
            <div class="nav-buttons">
                <button class="nav-btn prev-btn" onclick="prevQuestion()" ${currentIndex === 0 ? 'disabled' : ''}>
                    ← ${lang === 'zh' ? '上一题' : 'Previous'}
                </button>
                <button class="nav-btn ${buttonClass}" onclick="${buttonAction}">
                    ${buttonText}
                </button>
            </div>
        </div>
    `;
    
    questionArea.innerHTML = html;
}

// 选择答案
function selectAnswer(letter) {
    if (quizCompleted) return;
    
    userAnswers[currentIndex] = letter;
    
    // 更新UI
    const options = document.querySelectorAll('.option');
    options.forEach(opt => {
        opt.classList.remove('selected');
        if (opt.dataset.letter === letter) {
            opt.classList.add('selected');
        }
    });
}

// 下一题
function nextQuestion() {
    if (userAnswers[currentIndex] === null) {
        showToast(lang === 'zh' ? '请先选择答案！' : 'Please select an answer!', 'warning');
        return;
    }
    
    if (currentIndex < totalQuestions - 1) {
        currentIndex++;
        renderQuestion();
        updateStats();
    }
}

// 上一题
function prevQuestion() {
    if (currentIndex > 0) {
        currentIndex--;
        renderQuestion();
        updateStats();
    }
}

// 提交测验
// 提交测验 - 修改这部分代码
function submitQuiz() {
    if (userAnswers[currentIndex] === null) {
        showToast(lang === 'zh' ? '请先完成当前题目！' : 'Please answer the current question!', 'warning');
        return;
    }
    
    // 计算分数
    calculateScore();
    
    // 准备提交数据
    const correctAnswers = questions.map(q => q.answer);
    
    // 创建表单提交
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'check.php';
    
    // 添加用户答案
    const userAnswersInput = document.createElement('input');
    userAnswersInput.type = 'hidden';
    userAnswersInput.name = 'user_answers';
    userAnswersInput.value = JSON.stringify(userAnswers);
    form.appendChild(userAnswersInput);
    
    // 添加正确答案
    const correctAnswersInput = document.createElement('input');
    correctAnswersInput.type = 'hidden';
    correctAnswersInput.name = 'correct_answers';
    correctAnswersInput.value = JSON.stringify(correctAnswers);
    form.appendChild(correctAnswersInput);
    
    document.body.appendChild(form);
    form.submit();
}

// 计算分数
function calculateScore() {
    score = 0;
    for (let i = 0; i < totalQuestions; i++) {
        if (userAnswers[i] && userAnswers[i] === questions[i].answer) {
            score++;
        }
    }
}

// 显示结果
function renderResults() {
    const percentage = (score / totalQuestions) * 100;
    let message = '';
    let emoji = '';
    
    if (percentage === 100) {
        message = lang === 'zh' ? '🎉 完美！你是蜜雪冰城专家！' : '🎉 Perfect! You are a Mixue expert!';
        emoji = '🏆';
    } else if (percentage >= 80) {
        message = lang === 'zh' ? '🌟 优秀！再复习一下就能满分了！' : '🌟 Excellent! Review a bit and you\'ll get perfect!';
        emoji = '🎖️';
    } else if (percentage >= 60) {
        message = lang === 'zh' ? '📚 不错！建议再学习一下培训内容' : '📚 Good! Consider reviewing the training materials';
        emoji = '📘';
    } else {
        message = lang === 'zh' ? '💪 加油！请重新学习培训内容' : '💪 Keep going! Please review the training content';
        emoji = '💪';
    }
    
    const resultHtml = `
        <div class="result-card">
            <h1>${emoji} ${lang === 'zh' ? '测验完成！' : 'Quiz Completed!'}</h1>
            
            <div class="score-circle">
                <svg width="200" height="200">
                    <circle cx="100" cy="100" r="90" fill="none" stroke="#e9ecef" stroke-width="15"/>
                    <circle cx="100" cy="100" r="90" fill="none" stroke="#667eea" stroke-width="15"
                            stroke-dasharray="${2 * Math.PI * 90}" 
                            stroke-dashoffset="${2 * Math.PI * 90 * (1 - percentage / 100)}"
                            stroke-linecap="round"/>
                </svg>
                <div class="score-text">${score}/${totalQuestions}</div>
            </div>
            
            <div class="result-message">${message}</div>
            
            <div class="result-details">
                <h3>${lang === 'zh' ? '📊 答题详情' : '📊 Answer Details'}</h3>
                ${questions.map((q, i) => `
                    <div class="result-item ${userAnswers[i] === q.answer ? 'correct' : 'wrong'}">
                        <strong>${i + 1}. ${escapeHtml(q.question)}</strong><br>
                        ${lang === 'zh' ? '你的答案' : 'Your answer'}: ${userAnswers[i] || (lang === 'zh' ? '未作答' : 'Not answered')} 
                        ${userAnswers[i] !== q.answer ? `(${lang === 'zh' ? '正确答案' : 'Correct'}: ${q.answer})` : ' ✓'}
                    </div>
                `).join('')}
            </div>
            
            <div>
                <button class="play-again-btn" onclick="restartQuiz()">
                    🔄 ${lang === 'zh' ? '重新开始' : 'Play Again'}
                </button>
                <button class="play-again-btn" onclick="goToDashboard()">
                    🏠 ${lang === 'zh' ? '返回首页' : 'Back to Home'}
                </button>
            </div>
        </div>
    `;
    
    questionArea.innerHTML = resultHtml;
    updateStats();
}

// 重新开始测验
function restartQuiz() {
    currentIndex = 0;
    userAnswers = new Array(totalQuestions).fill(null);
    score = 0;
    quizCompleted = false;
    renderQuestion();
    updateStats();
}

// 更新统计信息
function updateStats() {
    const answered = userAnswers.filter(a => a !== null).length;
    questionCounterEl.textContent = `${answered}/${totalQuestions}`;
    currentScoreEl.textContent = score;
    
    const progress = totalQuestions > 0 ? (answered / totalQuestions) * 100 : 0;
    progressBar.style.width = `${progress}%`;
}

// 跳转到首页
function goToDashboard() {
    window.location.href = 'dashboard.php?lang=' + lang;
}

// 显示提示消息
function showToast(message, type) {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: ${type === 'warning' ? '#ffc107' : '#28a745'};
        color: ${type === 'warning' ? '#333' : 'white'};
        padding: 12px 24px;
        border-radius: 50px;
        font-weight: bold;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}

// 创建彩带效果
function createConfetti() {
    const colors = ['#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe'];
    
    for (let i = 0; i < 100; i++) {
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.left = Math.random() * window.innerWidth + 'px';
        confetti.style.top = '-20px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.width = Math.random() * 10 + 5 + 'px';
        confetti.style.height = Math.random() * 10 + 5 + 'px';
        confetti.style.borderRadius = '50%';
        confetti.style.pointerEvents = 'none';
        confetti.style.zIndex = '9999';
        confetti.style.animation = `confetti ${Math.random() * 3 + 2}s linear forwards`;
        document.body.appendChild(confetti);
        setTimeout(() => confetti.remove(), 5000);
    }
}

// 转义HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// 添加CSS动画
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    @keyframes confetti {
        0% {
            transform: translateY(0) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// 启动
init();
</script>
<?php endif; ?>


</body>
</html>