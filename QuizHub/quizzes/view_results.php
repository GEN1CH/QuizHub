<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Quiz Results";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

// Check if quiz ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../dashboard/teacher.php");
    exit();
}

$quiz_id = intval($_GET['id']);

// Get the quiz and verify ownership
$stmt = $conn->prepare("
    SELECT q.*, s.name as subject_name, c.name as category_name 
    FROM quizzes q
    LEFT JOIN subjects s ON q.subject_id = s.id
    LEFT JOIN categories c ON q.category_id = c.id
    WHERE q.id = ? AND q.created_by = ?
");
$stmt->bind_param("ii", $quiz_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../dashboard/teacher.php");
    exit();
}

$quiz = $result->fetch_assoc();

// Get all attempts for this quiz
$stmt = $conn->prepare("
    SELECT qa.*, u.username, u.email,
           (SELECT COUNT(*) FROM answers a WHERE a.attempt_id = qa.id AND a.is_correct = 1) as correct_answers,
           (SELECT COUNT(*) FROM answers a WHERE a.attempt_id = qa.id) as total_answers
    FROM quiz_attempts qa
    JOIN users u ON qa.user_id = u.id
    WHERE qa.quiz_id = ?
    ORDER BY qa.completed_at DESC
");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$attempts = $stmt->get_result();

// Get all questions for this quiz
$stmt = $conn->prepare("
    SELECT * FROM questions 
    WHERE quiz_id = ? 
    ORDER BY id
");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions = $stmt->get_result();
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
                    <a class="nav-link" href="javascript:history.back()">
                        <i class="fas fa-arrow-left"></i> Back
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

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Quiz Results: <?php echo htmlspecialchars($quiz['title']); ?></h3>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Quiz Information</h5>
                        <table class="table table-sm">
                            <tr>
                                <th>Title:</th>
                                <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td><?php echo htmlspecialchars($quiz['description']); ?></td>
                            </tr>
                            <tr>
                                <th>Subject:</th>
                                <td><?php echo htmlspecialchars($quiz['subject_name'] ?? 'Not specified'); ?></td>
                            </tr>
                            <tr>
                                <th>Category:</th>
                                <td><?php echo htmlspecialchars($quiz['category_name'] ?? 'Not specified'); ?></td>
                            </tr>
                            <tr>
                                <th>Grade Level:</th>
                                <td><?php echo htmlspecialchars($quiz['grade_level'] ?? 'Not specified'); ?></td>
                            </tr>
                            <tr>
                                <th>Time Limit:</th>
                                <td><?php echo $quiz['time_limit'] ? $quiz['time_limit'] . ' minutes' : 'No limit'; ?></td>
                            </tr>
                            <tr>
                                <th>Questions:</th>
                                <td><?php echo $questions->num_rows; ?></td>
                            </tr>
                            <tr>
                                <th>Total Attempts:</th>
                                <td><?php echo $attempts->num_rows; ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Statistics</h5>
                        <?php
                        $total_attempts = $attempts->num_rows;
                        $completed_attempts = 0;
                        $total_score = 0;
                        $highest_score = 0;
                        $lowest_score = 100;
                        $average_score = 0;
                        
                        if ($total_attempts > 0) {
                            while ($attempt = $attempts->fetch_assoc()) {
                                if ($attempt['completed_at']) {
                                    $completed_attempts++;
                                    $score = ($attempt['correct_answers'] / $attempt['total_answers']) * 100;
                                    $total_score += $score;
                                    
                                    if ($score > $highest_score) {
                                        $highest_score = $score;
                                    }
                                    
                                    if ($score < $lowest_score) {
                                        $lowest_score = $score;
                                    }
                                }
                            }
                            
                            $average_score = $completed_attempts > 0 ? $total_score / $completed_attempts : 0;
                        }
                        ?>
                        <table class="table table-sm">
                            <tr>
                                <th>Total Attempts:</th>
                                <td><?php echo $total_attempts; ?></td>
                            </tr>
                            <tr>
                                <th>Completed Attempts:</th>
                                <td><?php echo $completed_attempts; ?></td>
                            </tr>
                            <tr>
                                <th>Highest Score:</th>
                                <td><?php echo number_format($highest_score, 2); ?>%</td>
                            </tr>
                            <tr>
                                <th>Lowest Score:</th>
                                <td><?php echo number_format($lowest_score, 2); ?>%</td>
                            </tr>
                            <tr>
                                <th>Average Score:</th>
                                <td><?php echo number_format($average_score, 2); ?>%</td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <h5>Student Attempts</h5>
                <?php if ($attempts->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Email</th>
                                    <th>Started</th>
                                    <th>Completed</th>
                                    <th>Score</th>
                                    <th>Correct</th>
                                    <th>Retake Allowed</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Reset the result pointer
                                $attempts->data_seek(0);
                                while ($attempt = $attempts->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($attempt['username']); ?></td>
                                        <td><?php echo htmlspecialchars($attempt['email']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($attempt['started_at'])); ?></td>
                                        <td>
                                            <?php 
                                            if ($attempt['completed_at']) {
                                                echo date('M d, Y H:i', strtotime($attempt['completed_at']));
                                            } else {
                                                echo '<span class="badge bg-warning">In Progress</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($attempt['completed_at']) {
                                                $score = ($attempt['correct_answers'] / $attempt['total_answers']) * 100;
                                                echo number_format($score, 2) . '%';
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($attempt['completed_at']) {
                                                echo $attempt['correct_answers'] . ' / ' . $attempt['total_answers'];
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($attempt['retake_allowed']): ?>
                                                <span class="badge bg-success">Yes</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($attempt['completed_at']): ?>
                                                <a href="review.php?attempt_id=<?php echo $attempt['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> Review
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">In Progress</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No attempts have been made for this quiz yet.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 