<?php
$page_title = "Take Quiz";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

// Check if quiz ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../dashboard/student.php");
    exit();
}

$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get quiz information
$stmt = $conn->prepare("
    SELECT q.*, s.name as subject_name, u.username as teacher_name
    FROM quizzes q
    LEFT JOIN subjects s ON q.subject_id = s.id
    LEFT JOIN users u ON q.created_by = u.id
    WHERE q.id = ?
");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
    header("Location: ../dashboard/student.php");
    exit();
}

// Check if student has already completed this quiz
$stmt = $conn->prepare("
    SELECT id, score, completed_at, retake_allowed 
    FROM quiz_attempts 
    WHERE quiz_id = ? AND user_id = ? AND completed_at IS NOT NULL
    ORDER BY started_at DESC 
    LIMIT 1
");
$stmt->bind_param("ii", $quiz_id, $user_id);
$stmt->execute();
$previous_attempt = $stmt->get_result()->fetch_assoc();

// If student has completed the quiz and doesn't have retake permission, redirect to results
if ($previous_attempt && !$previous_attempt['retake_allowed']) {
    $_SESSION['error'] = "You have already completed this quiz and don't have permission to retake it.";
    header("Location: results.php?id=" . $previous_attempt['id']);
    exit();
}

// Get questions
$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions = $stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Start a new attempt
    $stmt = $conn->prepare("INSERT INTO quiz_attempts (quiz_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $quiz_id, $user_id);
    $stmt->execute();
    $attempt_id = $conn->insert_id;

    $total_questions = $questions->num_rows;
    $correct_answers = 0;

    // Process each answer
    while ($question = $questions->fetch_assoc()) {
        $answer = trim($_POST['answer_' . $question['id']] ?? '');
        $is_correct = false;

        if ($question['question_type'] === 'mcq') {
            $is_correct = strtolower($answer) === strtolower($question['correct_answer']);
        } else {
            // For short answer, allow for some flexibility in matching
            $is_correct = strtolower(trim($answer)) === strtolower(trim($question['correct_answer']));
        }

        if ($is_correct) {
            $correct_answers++;
        }

        // Save the answer
        $stmt = $conn->prepare("INSERT INTO answers (attempt_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $attempt_id, $question['id'], $answer, $is_correct);
        $stmt->execute();
    }

    // Calculate and save the score
    $score = ($correct_answers / $total_questions) * 100;
    $stmt = $conn->prepare("UPDATE quiz_attempts SET score = ?, completed_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("di", $score, $attempt_id);
    $stmt->execute();

    // Redirect to results page
    header("Location: results.php?id=" . $attempt_id);
    exit();
}
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../index.php">QuizHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard/student.php">Dashboard</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="../auth/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="card-title mb-0"><?php echo htmlspecialchars($quiz['title']); ?></h3>
                        <p class="text-muted mb-0">
                            Subject: <?php echo htmlspecialchars($quiz['subject_name']); ?> |
                            Teacher: <?php echo htmlspecialchars($quiz['teacher_name']); ?>
                        </p>
                    </div>
                    <?php if ($quiz['time_limit'] > 0): ?>
                    <div class="timer-container">
                        <div class="timer-label">Time Remaining:</div>
                        <div id="timer" class="timer-display"><?php echo $quiz['time_limit']; ?>:00</div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if ($quiz['description']): ?>
                        <div class="alert alert-info">
                            <?php echo nl2br(htmlspecialchars($quiz['description'])); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($previous_attempt): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle"></i> You have already completed this quiz.
                            Your last score: <?php echo number_format($previous_attempt['score'], 2); ?>%
                            <?php if ($previous_attempt['retake_allowed']): ?>
                                <br>
                                <strong>You have been granted permission to retake this quiz.</strong>
                            <?php else: ?>
                                <br>
                                <strong>You don't have permission to retake this quiz.</strong>
                                <br>
                                <a href="results.php?id=<?php echo $previous_attempt['id']; ?>" class="btn btn-sm btn-primary mt-2">View Results</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" id="quizForm">
                        <?php 
                        $question_number = 1;
                        while ($question = $questions->fetch_assoc()): 
                        ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Question <?php echo $question_number; ?></h5>
                                    <p class="card-text"><?php echo nl2br(htmlspecialchars($question['question_text'])); ?></p>

                                    <?php if ($question['question_type'] === 'mcq'): ?>
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
                            <button type="submit" class="btn btn-primary btn-lg">Submit Quiz</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timer-container {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 10px;
    text-align: center;
    min-width: 120px;
}
.timer-label {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 5px;
}
.timer-display {
    font-size: 1.5rem;
    font-weight: bold;
    color: #0d6efd;
}
</style>

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
            timerDisplay.style.color = '#dc3545'; // Bootstrap danger color
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