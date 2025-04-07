<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Anime Student Dashboard";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is an anime student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anime_student') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get available anime quizzes
$stmt = $conn->prepare("
    SELECT aq.*, 
           u.username as guru_name,
           COUNT(aq_attempts.id) as total_attempts,
           AVG(aq_attempts.score) as average_score,
           (SELECT COUNT(*) FROM anime_quiz_attempts WHERE quiz_id = aq.id AND user_id = ?) as user_attempts,
           (SELECT score FROM anime_quiz_attempts WHERE quiz_id = aq.id AND user_id = ? ORDER BY started_at DESC LIMIT 1) as user_score,
           (SELECT id FROM anime_quiz_attempts WHERE quiz_id = aq.id AND user_id = ? ORDER BY started_at DESC LIMIT 1) as last_attempt_id
    FROM anime_quizzes aq
    JOIN users u ON aq.created_by = u.id
    LEFT JOIN anime_quiz_attempts aq_attempts ON aq.id = aq_attempts.quiz_id
    GROUP BY aq.id
    ORDER BY aq.created_at DESC
");
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$quizzes = $stmt->get_result();

// Get user's recent quiz attempts
$stmt = $conn->prepare("
    SELECT aq.title, aq.anime_series, aq_attempts.*, u.username as guru_name
    FROM anime_quiz_attempts aq_attempts
    JOIN anime_quizzes aq ON aq_attempts.quiz_id = aq.id
    JOIN users u ON aq.created_by = u.id
    WHERE aq_attempts.user_id = ? AND aq_attempts.completed_at IS NOT NULL
    ORDER BY aq_attempts.started_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recent_attempts = $stmt->get_result();
?>

<style>
.anime-dashboard {
    background: #050510;
    min-height: 100vh;
    color: #fff;
    padding: 2rem 0;
    position: relative;
    overflow: hidden;
}

.anime-dashboard::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 25px 25px, rgba(255, 255, 255, 0.05) 2%, transparent 0%),
        radial-gradient(circle at 75px 75px, rgba(255, 255, 255, 0.05) 2%, transparent 0%);
    background-size: 100px 100px;
    opacity: 0.5;
    z-index: 0;
}

.anime-dashboard::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 200px;
    background: linear-gradient(to bottom, rgba(255, 107, 107, 0.1), transparent);
    z-index: 0;
}

.anime-dashboard .container {
    position: relative;
    z-index: 2;
}

.anime-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 2rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    z-index: 1;
    overflow: hidden;
    color: #fff;
}

.anime-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.anime-title {
    font-family: 'Nunito', sans-serif;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    margin-bottom: 2rem;
}

.anime-stats {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    z-index: 1;
    color: #fff;
}

.anime-icon {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #ff6b6b;
}

.difficulty-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
    position: relative;
    overflow: hidden;
}

.difficulty-badge::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
}

.difficulty-badge:hover::after {
    transform: translateX(100%);
}

.difficulty-beginner {
    background-color: #4caf50;
    color: white;
}

.difficulty-intermediate {
    background-color: #ff9800;
    color: white;
}

.difficulty-advanced {
    background-color: #f44336;
    color: white;
}

.score-badge {
    font-size: 1.2rem;
    font-weight: bold;
}

.score-badge.high {
    color: #4caf50;
}

.score-badge.medium {
    color: #ff9800;
}

.score-badge.low {
    color: #f44336;
}

.text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
}

.card-title, .card-text, .list-group-item {
    color: #fff;
}

.list-group-item {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    margin-bottom: 0.5rem;
    border-radius: 10px !important;
}

.list-group-item:hover {
    background: rgba(255, 255, 255, 0.15);
}

.list-group-item small {
    color: rgba(255, 255, 255, 0.7);
}

.btn-primary {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #ff8e8e, #ff6b6b);
    transform: scale(1.05);
    color: white;
}

.btn-outline-primary {
    border: 2px solid #ff6b6b;
    color: #ff6b6b;
    background: transparent;
    padding: 10px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border-color: transparent;
    color: white;
    transform: scale(1.05);
}
</style>

<div class="anime-dashboard">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="anime-title mb-0">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <a href="../index.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="anime-stats text-center">
                    <i class="fas fa-book anime-icon"></i>
                    <h3>
                        <?php
                        $stmt = $conn->prepare("SELECT COUNT(*) as completed FROM anime_quiz_attempts WHERE user_id = ? AND completed_at IS NOT NULL");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        echo $stmt->get_result()->fetch_assoc()['completed'];
                        ?>
                    </h3>
                    <p>Completed Quizzes</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="anime-stats text-center">
                    <i class="fas fa-star anime-icon"></i>
                    <h3>
                        <?php
                        $stmt = $conn->prepare("SELECT AVG(score) as avg_score FROM anime_quiz_attempts WHERE user_id = ? AND completed_at IS NOT NULL");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $avg_score = $stmt->get_result()->fetch_assoc()['avg_score'];
                        echo $avg_score ? number_format($avg_score, 1) : '0';
                        ?>%
                    </h3>
                    <p>Average Score</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="anime-stats text-center">
                    <i class="fas fa-trophy anime-icon"></i>
                    <h3>
                        <?php
                        $stmt = $conn->prepare("SELECT MAX(score) as high_score FROM anime_quiz_attempts WHERE user_id = ? AND completed_at IS NOT NULL");
                        $stmt->bind_param("i", $user_id);
                        $stmt->execute();
                        $high_score = $stmt->get_result()->fetch_assoc()['high_score'];
                        echo $high_score ? number_format($high_score, 1) : '0';
                        ?>%
                    </h3>
                    <p>Highest Score</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <h2 class="anime-title mb-4">Available Quizzes</h2>
                <?php if ($quizzes->num_rows > 0): ?>
                    <div class="row">
                        <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                            <div class="col-md-6 mb-4">
                                <div class="anime-card">
                                    <h3 class="h4 mb-3"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                                    <p class="text-muted mb-2"><?php echo htmlspecialchars($quiz['anime_series']); ?></p>
                                    <p class="text-muted mb-3">By: <?php echo htmlspecialchars($quiz['guru_name']); ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="difficulty-badge difficulty-<?php echo $quiz['difficulty']; ?>">
                                            <?php echo ucfirst($quiz['difficulty']); ?>
                                        </span>
                                        <span class="text-muted"><?php echo $quiz['time_limit']; ?> minutes</span>
                                    </div>

                                    <?php if ($quiz['user_attempts'] > 0): ?>
                                        <div class="mb-3">
                                            <span class="text-muted">Your Score: </span>
                                            <span class="score-badge <?php 
                                                if ($quiz['user_score'] >= 80) echo 'high';
                                                elseif ($quiz['user_score'] >= 60) echo 'medium';
                                                else echo 'low';
                                            ?>">
                                                <?php echo number_format($quiz['user_score'], 1); ?>%
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">Total Attempts: <?php echo $quiz['total_attempts']; ?></small>
                                            <br>
                                            <small class="text-muted">
                                                Avg Score: <?php echo $quiz['average_score'] ? number_format($quiz['average_score'], 1) : '0'; ?>%
                                            </small>
                                        </div>
                                        <div>
                                            <?php if ($quiz['user_attempts'] > 0): ?>
                                                <a href="../anime/results.php?id=<?php echo $quiz['last_attempt_id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-chart-bar"></i> View Results
                                                </a>
                                            <?php else: ?>
                                                <a href="../anime/take_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-play"></i> Take Quiz
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="anime-card text-center">
                        <h3 class="h4 mb-3">No Quizzes Available</h3>
                        <p>Check back later for new anime quizzes!</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <h2 class="anime-title mb-4">Recent Attempts</h2>
                <?php if ($recent_attempts->num_rows > 0): ?>
                    <?php while ($attempt = $recent_attempts->fetch_assoc()): ?>
                        <div class="anime-card mb-3">
                            <h4 class="h5 mb-2"><?php echo htmlspecialchars($attempt['title']); ?></h4>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($attempt['anime_series']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="score-badge <?php 
                                        if ($attempt['score'] >= 80) echo 'high';
                                        elseif ($attempt['score'] >= 60) echo 'medium';
                                        else echo 'low';
                                    ?>">
                                        <?php echo number_format($attempt['score'], 1); ?>%
                                    </span>
                                    <small class="text-muted d-block">
                                        <?php echo date('M d, Y', strtotime($attempt['completed_at'])); ?>
                                    </small>
                                </div>
                                <a href="../anime/results.php?id=<?php echo $attempt['id']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="anime-card text-center">
                        <p class="mb-0">You haven't taken any quizzes yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 