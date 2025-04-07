<?php
$page_title = "Review Quiz Attempt";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

// Check if attempt ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../dashboard/teacher.php");
    exit();
}

$attempt_id = intval($_GET['id']);
$teacher_id = $_SESSION['user_id'];

// Get attempt information
$stmt = $conn->prepare("
    SELECT qa.*, q.title as quiz_title, q.description as quiz_description,
           s.name as subject_name, u.username as student_name, u.email as student_email
    FROM quiz_attempts qa
    JOIN quizzes q ON qa.quiz_id = q.id
    LEFT JOIN subjects s ON q.subject_id = s.id
    LEFT JOIN users u ON qa.user_id = u.id
    WHERE qa.id = ? AND q.created_by = ?
");
$stmt->bind_param("ii", $attempt_id, $teacher_id);
$stmt->execute();
$attempt = $stmt->get_result()->fetch_assoc();

if (!$attempt) {
    header("Location: ../dashboard/teacher.php");
    exit();
}

// Handle form submission for retake permission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'grant_retake') {
        // Grant retake permission
        $stmt = $conn->prepare("UPDATE quiz_attempts SET retake_allowed = TRUE WHERE id = ?");
        $stmt->bind_param("i", $attempt_id);
        
        if ($stmt->execute()) {
            $success_message = "Retake permission granted successfully.";
            // Refresh attempt data
            $attempt['retake_allowed'] = true;
        } else {
            $error_message = "Failed to grant retake permission. Please try again.";
        }
    } elseif ($_POST['action'] == 'revoke_retake') {
        // Revoke retake permission
        $stmt = $conn->prepare("UPDATE quiz_attempts SET retake_allowed = FALSE WHERE id = ?");
        $stmt->bind_param("i", $attempt_id);
        
        if ($stmt->execute()) {
            $success_message = "Retake permission revoked successfully.";
            // Refresh attempt data
            $attempt['retake_allowed'] = false;
        } else {
            $error_message = "Failed to revoke retake permission. Please try again.";
        }
    }
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

// Get other attempts by this student for the same quiz
$stmt = $conn->prepare("
    SELECT id, score, started_at, completed_at, retake_allowed
    FROM quiz_attempts
    WHERE quiz_id = ? AND user_id = ? AND id != ?
    ORDER BY started_at DESC
");
$stmt->bind_param("iii", $attempt['quiz_id'], $attempt['user_id'], $attempt_id);
$stmt->execute();
$other_attempts = $stmt->get_result();
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
                    <a class="nav-link" href="../dashboard/teacher.php">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../quizzes/grant_retake.php">
                        <i class="fas fa-redo"></i> Manage Retake Permissions
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
        <div class="col-md-10">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Quiz Review</h3>
                    <p class="text-muted mb-0">
                        <?php echo htmlspecialchars($attempt['quiz_title']); ?> |
                        Subject: <?php echo htmlspecialchars($attempt['subject_name']); ?>
                    </p>
                </div>
                <div class="card-body">
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Student Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> <?php echo htmlspecialchars($attempt['student_name']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($attempt['student_email']); ?></p>
                                    <p><strong>Started:</strong> <?php echo date('F j, Y, g:i a', strtotime($attempt['started_at'])); ?></p>
                                    <p><strong>Completed:</strong> <?php echo date('F j, Y, g:i a', strtotime($attempt['completed_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">Attempt Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert <?php echo $attempt['score'] >= 70 ? 'alert-success' : 'alert-warning'; ?>">
                                        <h4 class="alert-heading">Score: <?php echo number_format($attempt['score'], 2); ?>%</h4>
                                    </div>
                                    <p><strong>Retake Permission:</strong> 
                                        <?php if ($attempt['retake_allowed']): ?>
                                            <span class="badge bg-success">Granted</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Not Granted</span>
                                        <?php endif; ?>
                                    </p>
                                    <div class="d-grid gap-2">
                                        <?php if ($attempt['retake_allowed']): ?>
                                            <form method="POST" action="">
                                                <input type="hidden" name="action" value="revoke_retake">
                                                <button type="submit" class="btn btn-warning btn-sm w-100">
                                                    <i class="fas fa-ban"></i> Revoke Retake Permission
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="">
                                                <input type="hidden" name="action" value="grant_retake">
                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    <i class="fas fa-check"></i> Grant Retake Permission
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($other_attempts->num_rows > 0): ?>
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">Previous Attempts</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Score</th>
                                                <th>Retake Allowed</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($other = $other_attempts->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo date('M j, Y, g:i a', strtotime($other['completed_at'])); ?></td>
                                                    <td><?php echo number_format($other['score'], 2); ?>%</td>
                                                    <td>
                                                        <?php if ($other['retake_allowed']): ?>
                                                            <span class="badge bg-success">Yes</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">No</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="review.php?id=<?php echo $other['id']; ?>" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
                                        <strong>Student's Answer:</strong>
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
                        <a href="../dashboard/teacher.php" class="btn btn-primary">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 