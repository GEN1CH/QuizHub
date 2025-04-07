<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Grant Retake Permission";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['attempt_id']) && is_numeric($_POST['attempt_id'])) {
        $attempt_id = intval($_POST['attempt_id']);
        
        // Verify that the attempt belongs to a quiz created by this teacher
        $stmt = $conn->prepare("
            SELECT qa.id 
            FROM quiz_attempts qa
            JOIN quizzes q ON qa.quiz_id = q.id
            WHERE qa.id = ? AND q.created_by = ?
        ");
        $stmt->bind_param("ii", $attempt_id, $teacher_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Grant retake permission
            $stmt = $conn->prepare("UPDATE quiz_attempts SET retake_allowed = TRUE WHERE id = ?");
            $stmt->bind_param("i", $attempt_id);
            
            if ($stmt->execute()) {
                $success_message = "Retake permission granted successfully.";
            } else {
                $error_message = "Failed to grant retake permission. Please try again.";
            }
        } else {
            $error_message = "You don't have permission to modify this quiz attempt.";
        }
    } else {
        $error_message = "Invalid attempt ID.";
    }
}

// Get all quiz attempts for quizzes created by this teacher
$stmt = $conn->prepare("
    SELECT qa.*, q.title as quiz_title, u.username as student_name, u.email as student_email
    FROM quiz_attempts qa
    JOIN quizzes q ON qa.quiz_id = q.id
    JOIN users u ON qa.user_id = u.id
    WHERE q.created_by = ? AND qa.completed_at IS NOT NULL
    ORDER BY qa.completed_at DESC
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$attempts = $stmt->get_result();
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
                    <h3 class="card-title mb-0">Grant Retake Permission</h3>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <p class="mb-4">
                        Select a student's quiz attempt to grant them permission to retake the quiz.
                    </p>
                    
                    <?php if ($attempts->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Quiz</th>
                                        <th>Student</th>
                                        <th>Score</th>
                                        <th>Completed</th>
                                        <th>Retake Allowed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($attempt = $attempts->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($attempt['quiz_title']); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($attempt['student_name']); ?>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($attempt['student_email']); ?></small>
                                            </td>
                                            <td><?php echo number_format($attempt['score'], 2); ?>%</td>
                                            <td><?php echo date('M j, Y, g:i a', strtotime($attempt['completed_at'])); ?></td>
                                            <td>
                                                <?php if ($attempt['retake_allowed']): ?>
                                                    <span class="badge bg-success">Yes</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!$attempt['retake_allowed']): ?>
                                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to grant retake permission to this student?');">
                                                        <input type="hidden" name="attempt_id" value="<?php echo $attempt['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i> Grant Permission
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">Permission already granted</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            No completed quiz attempts found.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 