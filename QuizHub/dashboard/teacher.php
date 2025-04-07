<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Teacher Dashboard";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

// Get teacher's quizzes
$stmt = $conn->prepare("
    SELECT q.*, s.name as subject_name, c.name as category_name,
           (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id) as attempt_count,
           (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) as question_count
    FROM quizzes q
    LEFT JOIN subjects s ON q.subject_id = s.id
    LEFT JOIN categories c ON s.category_id = c.id
    WHERE q.created_by = ?
    ORDER BY q.created_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$quizzes = $stmt->get_result();
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
                <li class="nav-item">
                    <a class="nav-link" href="../quizzes/grant_retake.php">
                        <i class="fas fa-redo"></i> Grant Retake Permission
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

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">My Quizzes</h3>
                <a href="../quizzes/create.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Quiz
                </a>
            </div>
            <div class="card-body">
                <?php if ($quizzes->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Category</th>
                                    <th>Grade Level</th>
                                    <th>Time Limit</th>
                                    <th>Questions</th>
                                    <th>Attempts</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['subject_name'] ?? 'Uncategorized'); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['category_name'] ?? 'Uncategorized'); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['grade_level'] ?? 'Not specified'); ?></td>
                                        <td><?php echo $quiz['time_limit'] ? $quiz['time_limit'] . ' minutes' : 'No limit'; ?></td>
                                        <td><?php echo $quiz['question_count']; ?></td>
                                        <td><?php echo $quiz['attempt_count']; ?></td>
                                        <td><?php echo date('M d, Y', strtotime($quiz['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="../quizzes/edit.php?id=<?php echo $quiz['id']; ?>" 
                                                   class="btn btn-outline-primary" title="Edit Quiz">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="../quizzes/view_results.php?id=<?php echo $quiz['id']; ?>" 
                                                   class="btn btn-outline-info" title="View Results">
                                                    <i class="fas fa-chart-bar"></i>
                                                </a>
                                                <a href="../quizzes/delete.php?id=<?php echo $quiz['id']; ?>" 
                                                   class="btn btn-outline-danger" title="Delete Quiz"
                                                   onclick="return confirm('Are you sure you want to delete this quiz?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        You haven't created any quizzes yet. Click the "Create New Quiz" button to get started.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 