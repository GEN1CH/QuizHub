<?php
ob_start();
session_start();
$page_title = "Anime Guru Dashboard";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is an anime guru
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anime_guru') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all anime quizzes, not just the ones created by the current user
$stmt = $conn->prepare("
    SELECT aq.*, 
           u.username as created_by_username,
           COUNT(aq_attempts.id) as total_attempts,
           AVG(aq_attempts.score) as average_score
    FROM anime_quizzes aq
    LEFT JOIN users u ON aq.created_by = u.id
    LEFT JOIN anime_quiz_attempts aq_attempts ON aq.id = aq_attempts.quiz_id
    GROUP BY aq.id
    ORDER BY aq.created_at DESC
");
$stmt->execute();
$quizzes = $stmt->get_result();
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

.anime-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255, 107, 107, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.anime-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.anime-card:hover::before {
    opacity: 1;
}

.anime-title {
    font-family: 'Nunito', sans-serif;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    margin-bottom: 2rem;
}

.anime-title::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 50%;
    height: 3px;
    background: linear-gradient(to right, #ff6b6b, transparent);
    border-radius: 3px;
}

.anime-btn {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
    cursor: pointer;
}

.anime-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.5s ease;
}

.anime-btn:hover {
    background: linear-gradient(45deg, #ff8e8e, #ff6b6b);
    transform: scale(1.05);
    color: white;
}

.anime-btn:hover::before {
    left: 100%;
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
    transition: transform 0.3s ease;
}

.anime-stats:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.anime-stats:hover .anime-icon {
    transform: scale(1.1);
}

.text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
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

/* Add animation for page load */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.anime-card, .anime-stats {
    animation: fadeIn 0.5s ease forwards;
}

.anime-card:nth-child(2) {
    animation-delay: 0.1s;
}

.anime-card:nth-child(3) {
    animation-delay: 0.2s;
}

.anime-card:nth-child(4) {
    animation-delay: 0.3s;
}

.anime-stats:nth-child(2) {
    animation-delay: 0.1s;
}

.anime-stats:nth-child(3) {
    animation-delay: 0.2s;
}

/* Add new styles for the header section */
.dashboard-header {
    position: relative;
    z-index: 2;
    margin-bottom: 2rem;
}
</style>

<div class="anime-dashboard">
    <div class="container">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <h1 class="anime-title">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <a href="/QuizHub/anime/create_quiz.php" class="btn anime-btn">
                <i class="fas fa-plus"></i> Create New Quiz
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="anime-stats text-center">
                    <i class="fas fa-book anime-icon"></i>
                    <h3><?php echo $quizzes->num_rows; ?></h3>
                    <p>Total Quizzes</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="anime-stats text-center">
                    <i class="fas fa-users anime-icon"></i>
                    <h3>
                        <?php
                        $stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id) as total_students FROM anime_quiz_attempts");
                        $stmt->execute();
                        echo $stmt->get_result()->fetch_assoc()['total_students'];
                        ?>
                    </h3>
                    <p>Total Students</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="anime-stats text-center">
                    <i class="fas fa-star anime-icon"></i>
                    <h3>
                        <?php
                        $stmt = $conn->prepare("SELECT AVG(score) as avg_score FROM anime_quiz_attempts");
                        $stmt->execute();
                        $avg_score = $stmt->get_result()->fetch_assoc()['avg_score'];
                        echo $avg_score ? number_format($avg_score, 1) : '0';
                        ?>%
                    </h3>
                    <p>Average Score</p>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if ($quizzes->num_rows > 0): ?>
                <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="anime-card">
                            <h3 class="h4 mb-3"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($quiz['anime_series']); ?></p>
                            <p class="text-muted mb-2">Created by: <?php echo htmlspecialchars($quiz['created_by_username']); ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="difficulty-badge difficulty-<?php echo $quiz['difficulty']; ?>">
                                    <?php echo ucfirst($quiz['difficulty']); ?>
                                </span>
                                <span class="text-muted"><?php echo $quiz['time_limit']; ?> minutes</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">Attempts: <?php echo $quiz['total_attempts']; ?></small>
                                    <br>
                                    <small class="text-muted">
                                        Avg Score: <?php echo $quiz['average_score'] ? number_format($quiz['average_score'], 1) : '0'; ?>%
                                    </small>
                                </div>
                                <div>
                                    <a href="../anime/edit_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm anime-btn me-2">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="../anime/view_results.php?id=<?php echo $quiz['id']; ?>" class="btn btn-sm anime-btn">
                                        <i class="fas fa-chart-bar"></i> Results
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="anime-card text-center">
                        <h3 class="h4 mb-4">No Quizzes Yet</h3>
                        <p class="mb-4">Start creating anime quizzes to share your knowledge with others!</p>
                        <a href="../anime/create_quiz.php" class="btn anime-btn">
                            <i class="fas fa-plus"></i> Create Your First Quiz
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 