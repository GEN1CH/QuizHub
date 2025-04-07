<?php
$page_title = "Quiz Results";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if attempt ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../dashboard/student.php");
    exit();
}

$attempt_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Get attempt information
$stmt = $conn->prepare("
    SELECT qa.*, q.title as quiz_title, q.description as quiz_description,
           s.name as subject_name, u.username as teacher_name
    FROM quiz_attempts qa
    JOIN quizzes q ON qa.quiz_id = q.id
    LEFT JOIN subjects s ON q.subject_id = s.id
    LEFT JOIN users u ON q.created_by = u.id
    WHERE qa.id = ? AND qa.user_id = ?
");
$stmt->bind_param("ii", $attempt_id, $user_id);
$stmt->execute();
$attempt = $stmt->get_result()->fetch_assoc();

if (!$attempt) {
    header("Location: ../dashboard/student.php");
    exit();
}

// Get answers
$stmt = $conn->prepare("
    SELECT a.*, q.question_text, q.question_type, q.correct_answer, q.options
    FROM answers a
    JOIN questions q ON a.question_id = q.id
    WHERE a.attempt_id = ?
    ORDER BY q.id
");
$stmt->bind_param("i", $attempt_id);
$stmt->execute();
$answers = $stmt->get_result();
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">QuizHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard/student.php">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-danger" href="../auth/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Quiz Results</h3>
                    <p class="text-muted mb-0">
                        <?php echo htmlspecialchars($attempt['quiz_title']); ?> |
                        Subject: <?php echo htmlspecialchars($attempt['subject_name']); ?> |
                        Teacher: <?php echo htmlspecialchars($attempt['teacher_name']); ?>
                    </p>
                </div>
                <div class="card-body">
                    <div class="alert <?php echo $attempt['score'] >= 70 ? 'alert-success' : 'alert-warning'; ?>">
                        <h4 class="alert-heading">Your Score: <?php echo number_format($attempt['score'], 2); ?>%</h4>
                        <p class="mb-0">
                            Completed on: <?php echo date('F j, Y, g:i a', strtotime($attempt['completed_at'])); ?>
                        </p>
                    </div>

                    <?php if ($attempt['quiz_description']): ?>
                        <div class="alert alert-info">
                            <?php echo nl2br(htmlspecialchars($attempt['quiz_description'])); ?>
                        </div>
                    <?php endif; ?>

                    <h4 class="mb-3">Question Review</h4>
                    <?php 
                    $question_number = 1;
                    while ($answer = $answers->fetch_assoc()): 
                    ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">
                                    Question <?php echo $question_number; ?>
                                    <?php if ($answer['is_correct']): ?>
                                        <span class="badge bg-success">Correct</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Incorrect</span>
                                    <?php endif; ?>
                                </h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($answer['question_text'])); ?></p>

                                <?php if ($answer['question_type'] === 'mcq'): ?>
                                    <?php 
                                    $options = json_decode($answer['options'], true);
                                    foreach ($options as $index => $option): 
                                        $is_selected = $option === $answer['user_answer'];
                                        $is_correct = $option === $answer['correct_answer'];
                                    ?>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" 
                                                   <?php echo $is_selected ? 'checked' : ''; ?> 
                                                   disabled>
                                            <label class="form-check-label <?php echo $is_correct ? 'text-success fw-bold' : ($is_selected ? 'text-danger' : ''); ?>">
                                                <?php echo htmlspecialchars($option); ?>
                                                <?php if ($is_correct): ?>
                                                    <i class="fas fa-check"></i>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="mb-2">
                                        <strong>Your Answer:</strong>
                                        <span class="<?php echo $answer['is_correct'] ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo htmlspecialchars($answer['user_answer']); ?>
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Correct Answer:</strong>
                                        <span class="text-success">
                                            <?php echo htmlspecialchars($answer['correct_answer']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php $question_number++; ?>
                    <?php endwhile; ?>

                    <div class="d-grid gap-2">
                        <a href="../dashboard/student.php" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 