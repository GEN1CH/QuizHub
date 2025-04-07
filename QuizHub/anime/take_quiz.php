<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Take Anime Quiz";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is an anime student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anime_student') {
    header("Location: ../auth/login.php");
    exit();
}

// Check if quiz ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../dashboard/anime_student.php");
    exit();
}

$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get quiz details
$stmt = $conn->prepare("
    SELECT aq.*, u.username as guru_name
    FROM anime_quizzes aq
    JOIN users u ON aq.created_by = u.id
    WHERE aq.id = ?
");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
    header("Location: ../dashboard/anime_student.php");
    exit();
}

// Check if user has already completed this quiz
$stmt = $conn->prepare("
    SELECT * FROM anime_quiz_attempts 
    WHERE quiz_id = ? AND user_id = ? AND completed_at IS NOT NULL
    ORDER BY started_at DESC
    LIMIT 1
");
$stmt->bind_param("ii", $quiz_id, $user_id);
$stmt->execute();
$previous_attempt = $stmt->get_result()->fetch_assoc();

// Get quiz questions
$stmt = $conn->prepare("SELECT * FROM anime_questions WHERE quiz_id = ? ORDER BY id");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions = $stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify session is still valid
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anime_student') {
        // Try to restore session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = 'anime_student';
    }
    
    // Create a new quiz attempt
    $stmt = $conn->prepare("INSERT INTO anime_quiz_attempts (quiz_id, user_id, started_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $quiz_id, $user_id);
    
    if ($stmt->execute()) {
        $attempt_id = $conn->insert_id;
        
        // Process answers
        $correct_count = 0;
        $total_questions = $questions->num_rows;
        
        while ($question = $questions->fetch_assoc()) {
            $answer = isset($_POST['answer_' . $question['id']]) ? trim($_POST['answer_' . $question['id']]) : '';
            $is_correct = strtolower($answer) === strtolower($question['correct_answer']);
            
            if ($is_correct) {
                $correct_count++;
            }
            
            // Save answer
            $stmt = $conn->prepare("INSERT INTO anime_answers (attempt_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iisi", $attempt_id, $question['id'], $answer, $is_correct);
            $stmt->execute();
        }
        
        // Calculate score
        $score = ($correct_count / $total_questions) * 100;
        
        // Update attempt with score and completion time
        $stmt = $conn->prepare("UPDATE anime_quiz_attempts SET score = ?, completed_at = NOW() WHERE id = ?");
        $stmt->bind_param("di", $score, $attempt_id);
        $stmt->execute();
        
        // Redirect to results page with user_id parameter
        header("Location: results.php?id=" . $attempt_id . "&user_id=" . $user_id);
        exit();
    } else {
        $error = "Error starting quiz: " . $conn->error;
    }
}
?>

<style>
.anime-quiz {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    min-height: 100vh;
    color: #fff;
    padding: 2rem 0;
}

.anime-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 2rem;
}

.anime-title {
    font-family: 'Nunito', sans-serif;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    margin-bottom: 2rem;
}

.anime-btn {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.anime-btn:hover {
    background: linear-gradient(45deg, #ff8e8e, #ff6b6b);
    transform: scale(1.05);
    color: white;
}

.form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #ff6b6b;
    color: #fff;
    box-shadow: 0 0 0 0.25rem rgba(255, 107, 107, 0.25);
}

.form-label {
    color: #fff;
}

.form-check-input {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
}

.form-check-input:checked {
    background-color: #ff6b6b;
    border-color: #ff6b6b;
}

.form-check-label {
    color: #fff;
}

.timer-container {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    min-width: 120px;
}

.timer-label {
    font-size: 0.8rem;
    color: #ccc;
    margin-bottom: 5px;
}

.timer-display {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ff6b6b;
}

.question-image {
    max-width: 100%;
    border-radius: 10px;
    margin: 1rem 0;
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

/* Add new styles for questions */
.card {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    margin-bottom: 1.5rem;
}

.card-title {
    color: #fff;
    font-weight: bold;
    margin-bottom: 1rem;
}

.card-text {
    color: #fff;
    font-size: 1.1rem;
    line-height: 1.6;
}

.text-muted {
    color: rgba(255, 255, 255, 0.7) !important;
}

.alert {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
}

.alert-info {
    background: rgba(23, 162, 184, 0.2);
    border-color: rgba(23, 162, 184, 0.3);
}

.alert-warning {
    background: rgba(255, 193, 7, 0.2);
    border-color: rgba(255, 193, 7, 0.3);
}
</style>

<div class="anime-quiz">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="anime-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="anime-title h2 mb-0"><?php echo htmlspecialchars($quiz['title']); ?></h1>
                            <p class="text-muted mb-0">
                                By: <?php echo htmlspecialchars($quiz['guru_name']); ?> | 
                                <span class="difficulty-badge difficulty-<?php echo $quiz['difficulty']; ?>">
                                    <?php echo ucfirst($quiz['difficulty']); ?>
                                </span>
                            </p>
                        </div>
                        <?php if ($quiz['time_limit'] > 0): ?>
                        <div class="timer-container">
                            <div class="timer-label">Time Remaining:</div>
                            <div id="timer" class="timer-display"><?php echo $quiz['time_limit']; ?>:00</div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($quiz['description']): ?>
                        <div class="alert alert-info">
                            <?php echo nl2br(htmlspecialchars($quiz['description'])); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($previous_attempt): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> You have already completed this quiz.
                            Your last score: <?php echo number_format($previous_attempt['score'], 1); ?>%
                            <?php if ($previous_attempt['retake_allowed']): ?>
                                <br>
                                <strong>You have been granted permission to retake this quiz.</strong>
                            <?php else: ?>
                                <br>
                                <strong>You don't have permission to retake this quiz.</strong>
                                <br>
                                <a href="results.php?id=<?php echo $previous_attempt['id']; ?>" class="btn btn-sm anime-btn mt-2">View Results</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" id="quizForm">
                        <?php 
                        $question_number = 1;
                        while ($question = $questions->fetch_assoc()): 
                        ?>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Question <?php echo $question_number; ?></h5>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($question['question_text'])); ?></p>

                                    <?php if ($question['question_type'] === 'image_question' && $question['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($question['image_url']); ?>" alt="Question Image" class="question-image">
                                    <?php endif; ?>

                                    <?php if ($question['question_type'] === 'mcq' && $question['options']): ?>
                                        <?php 
                                        $options = json_decode($question['options'], true);
                                        foreach ($options as $index => $option): 
                                        ?>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" 
                                                       name="answer_<?php echo $question['id']; ?>" 
                                                       value="<?php echo htmlspecialchars($option); ?>" 
                                                       id="option_<?php echo $question['id']; ?>_<?php echo $index; ?>" 
                                                       required>
                                                <label class="form-check-label" for="option_<?php echo $question['id']; ?>_<?php echo $index; ?>">
                                                    <?php echo htmlspecialchars($option); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="form-group">
                                            <input type="text" class="form-control" 
                                                   name="answer_<?php echo $question['id']; ?>" 
                                                   placeholder="Enter your answer" 
                                                   required>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php $question_number++; ?>
                        <?php endwhile; ?>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn anime-btn btn-lg">Submit Quiz</button>
                            <a href="../dashboard/anime_student.php" class="btn btn-outline-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($quiz['time_limit'] > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const timeLimit = <?php echo $quiz['time_limit']; ?> * 60; // Convert to seconds
    let timeLeft = timeLimit;
    const form = document.getElementById('quizForm');
    const timerDisplay = document.getElementById('timer');
    
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        // Change color when time is running low (less than 25% remaining)
        if (timeLeft < timeLimit * 0.25) {
            timerDisplay.style.color = '#f44336'; // Red color
        }
        
        if (timeLeft <= 0) {
            form.submit();
        } else {
            timeLeft--;
            setTimeout(updateTimer, 1000);
        }
    }
    
    updateTimer();
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 