<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Quiz Results";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is an anime student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anime_student') {
    // Try to restore session from URL parameter
    if (isset($_GET['user_id'])) {
        $_SESSION['user_id'] = intval($_GET['user_id']);
        $_SESSION['role'] = 'anime_student';
    } else {
        header("Location: ../auth/login.php");
        exit();
    }
}

// Check if attempt ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../dashboard/anime_student.php");
    exit();
}

$attempt_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get attempt details
$stmt = $conn->prepare("
    SELECT aqa.*, aq.title as quiz_title, aq.description as quiz_description, 
           aq.anime_series, aq.difficulty, u.username as guru_name
    FROM anime_quiz_attempts aqa
    JOIN anime_quizzes aq ON aqa.quiz_id = aq.id
    JOIN users u ON aq.created_by = u.id
    WHERE aqa.id = ? AND aqa.user_id = ?
");
$stmt->bind_param("ii", $attempt_id, $user_id);
$stmt->execute();
$attempt = $stmt->get_result()->fetch_assoc();

if (!$attempt) {
    header("Location: ../dashboard/anime_student.php");
    exit();
}

// Get answers
$stmt = $conn->prepare("
    SELECT a.*, q.question_text, q.question_type, q.correct_answer, q.options, q.image_url
    FROM anime_answers a
    JOIN anime_questions q ON a.question_id = q.id
    WHERE a.attempt_id = ?
    ORDER BY q.id
");
$stmt->bind_param("i", $attempt_id);
$stmt->execute();
$answers = $stmt->get_result();
?>

<style>
.results-page {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    min-height: 100vh;
    color: #fff;
    padding: 2rem 0;
}

.results-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 2rem;
}

.results-title {
    font-family: 'Nunito', sans-serif;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    margin-bottom: 2rem;
}

.score-display {
    font-size: 3rem;
    font-weight: bold;
    text-align: center;
    margin: 2rem 0;
    color: #ff6b6b;
}

.score-label {
    font-size: 1.2rem;
    color: #ccc;
    text-align: center;
    margin-bottom: 1rem;
}

.answer-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    margin-bottom: 1rem;
    padding: 1.5rem;
}

.answer-card.correct {
    border-left: 4px solid #4caf50;
}

.answer-card.incorrect {
    border-left: 4px solid #f44336;
}

.answer-status {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 1rem;
}

.answer-status.correct {
    color: #4caf50;
}

.answer-status.incorrect {
    color: #f44336;
}

.question-image {
    max-width: 100%;
    border-radius: 10px;
    margin: 1rem 0;
}

.answer-details {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.answer-label {
    color: #ccc;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.answer-text {
    color: #fff;
    font-weight: 500;
}

.btn-back {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: linear-gradient(45deg, #ff8e8e, #ff6b6b);
    transform: scale(1.05);
    color: white;
}

.difficulty-badge {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: bold;
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
</style>

<div class="results-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="results-card">
                    <h1 class="results-title">Quiz Results</h1>
                    
                    <div class="text-center mb-4">
                        <h2><?php echo htmlspecialchars($attempt['quiz_title']); ?></h2>
                        <p class="text-muted">
                            By: <?php echo htmlspecialchars($attempt['guru_name']); ?> | 
                            <span class="difficulty-badge difficulty-<?php echo $attempt['difficulty']; ?>">
                                <?php echo ucfirst($attempt['difficulty']); ?>
                            </span>
                        </p>
                    </div>

                    <div class="score-label">Your Score</div>
                    <div class="score-display"><?php echo number_format($attempt['score'], 1); ?>%</div>

                    <?php if ($attempt['quiz_description']): ?>
                        <div class="alert alert-info">
                            <?php echo nl2br(htmlspecialchars($attempt['quiz_description'])); ?>
                        </div>
                    <?php endif; ?>

                    <div class="answers-section mt-4">
                        <h3 class="mb-4">Question Review</h3>
                        <?php 
                        $question_number = 1;
                        while ($answer = $answers->fetch_assoc()): 
                        ?>
                            <div class="answer-card <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
                                <div class="answer-status <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
                                    <i class="fas fa-<?php echo $answer['is_correct'] ? 'check' : 'times'; ?>-circle"></i>
                                    Question <?php echo $question_number; ?>
                                </div>

                                <p class="card-text"><?php echo nl2br(htmlspecialchars($answer['question_text'])); ?></p>

                                <?php if ($answer['question_type'] === 'image_question' && $answer['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($answer['image_url']); ?>" alt="Question Image" class="question-image">
                                <?php endif; ?>

                                <div class="answer-details">
                                    <div class="mb-3">
                                        <div class="answer-label">Your Answer:</div>
                                        <div class="answer-text"><?php echo htmlspecialchars($answer['user_answer']); ?></div>
                                    </div>

                                    <div>
                                        <div class="answer-label">Correct Answer:</div>
                                        <div class="answer-text"><?php echo htmlspecialchars($answer['correct_answer']); ?></div>
                                    </div>

                                    <?php if ($answer['question_type'] === 'mcq' && $answer['options']): ?>
                                        <div class="mt-3">
                                            <div class="answer-label">Options:</div>
                                            <?php 
                                            $options = json_decode($answer['options'], true);
                                            foreach ($options as $option): 
                                            ?>
                                                <div class="answer-text">
                                                    <?php echo htmlspecialchars($option); ?>
                                                    <?php if ($option === $answer['correct_answer']): ?>
                                                        <i class="fas fa-check text-success"></i>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php $question_number++; ?>
                        <?php endwhile; ?>
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="../dashboard/anime_student.php" class="btn btn-back">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 