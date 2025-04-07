<?php
$page_title = "Student Dashboard";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../auth/login.php");
    exit();
}

// Get filter parameters
$subject_filter = isset($_GET['subject']) && is_numeric($_GET['subject']) ? intval($_GET['subject']) : null;
$grade_filter = isset($_GET['grade']) ? $_GET['grade'] : '';

// Get all subjects for the filter dropdown
$subjects = [];
$result = $conn->query("SELECT id, name FROM subjects ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

// Get all unique grade levels for the filter dropdown
$grade_levels = [];
$result = $conn->query("SELECT DISTINCT grade_level FROM quizzes WHERE grade_level IS NOT NULL AND grade_level != '' ORDER BY grade_level");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $grade_levels[] = $row['grade_level'];
    }
}

// Build the query for available quizzes
$query = "
    SELECT q.*, s.name as subject_name, u.username as teacher_name,
           (SELECT COUNT(*) FROM quiz_attempts WHERE quiz_id = q.id AND user_id = ?) as attempt_count
    FROM quizzes q
    LEFT JOIN subjects s ON q.subject_id = s.id
    LEFT JOIN users u ON q.created_by = u.id
    WHERE 1=1
";

$params = [$_SESSION['user_id']];
$types = "i";

// Add subject filter if provided
if ($subject_filter) {
    $query .= " AND q.subject_id = ?";
    $params[] = $subject_filter;
    $types .= "i";
}

// Add grade level filter if provided
if ($grade_filter) {
    $query .= " AND q.grade_level = ?";
    $params[] = $grade_filter;
    $types .= "s";
}

$query .= " ORDER BY q.created_at DESC";

// Execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$quizzes = $stmt->get_result();

// Get student's quiz attempts
$stmt = $conn->prepare("
    SELECT qa.*, q.title as quiz_title, q.subject_id, s.name as subject_name
    FROM quiz_attempts qa
    JOIN quizzes q ON qa.quiz_id = q.id
    LEFT JOIN subjects s ON q.subject_id = s.id
    WHERE qa.user_id = ?
    ORDER BY qa.started_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
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

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Available Quizzes</h3>
                <div class="d-flex align-items-center">
                    <form method="GET" action="" class="d-flex">
                        <select name="subject" class="form-select form-select-sm me-2">
                            <option value="">All Subjects</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['id']; ?>" 
                                    <?php echo $subject_filter == $subject['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($subject['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <select name="grade" class="form-select form-select-sm me-2">
                            <option value="">All Grade Levels</option>
                            <?php foreach ($grade_levels as $level): ?>
                                <option value="<?php echo $level; ?>" 
                                    <?php echo $grade_filter == $level ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($level); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <?php if ($quizzes->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Grade Level</th>
                                    <th>Teacher</th>
                                    <th>Time Limit</th>
                                    <th>Attempts</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($quiz['title']); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['subject_name'] ?? 'Uncategorized'); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['grade_level'] ?? 'Not specified'); ?></td>
                                        <td><?php echo htmlspecialchars($quiz['teacher_name']); ?></td>
                                        <td><?php echo $quiz['time_limit'] > 0 ? $quiz['time_limit'] . ' minutes' : 'No limit'; ?></td>
                                        <td><?php echo $quiz['attempt_count']; ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="../quizzes/take.php?id=<?php echo $quiz['id']; ?>" class="btn btn-primary">Take Quiz</a>
                                                <?php if ($quiz['attempt_count'] > 0): ?>
                                                    <a href="../quizzes/review.php?id=<?php echo $quiz['id']; ?>" class="btn btn-outline-info">Review</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="mb-0">No quizzes available<?php echo $subject_filter || $grade_filter ? ' with the selected filters' : ''; ?>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">My Quiz Attempts</h4>
            </div>
            <div class="card-body">
                <?php if ($attempts->num_rows > 0): ?>
                    <div class="list-group">
                        <?php while ($attempt = $attempts->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <h6 class="mb-1"><?php echo htmlspecialchars($attempt['quiz_title']); ?></h6>
                                <p class="mb-1">
                                    <small class="text-muted">
                                        Subject: <?php echo htmlspecialchars($attempt['subject_name'] ?? 'Uncategorized'); ?><br>
                                        Score: <?php echo $attempt['score'] ?? 'Not completed'; ?><br>
                                        Date: <?php echo date('M d, Y H:i', strtotime($attempt['started_at'])); ?>
                                    </small>
                                </p>
                                <div class="btn-group btn-group-sm">
                                    <a href="../quizzes/review.php?id=<?php echo $attempt['quiz_id']; ?>" class="btn btn-outline-primary">Review</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="mb-0">You haven't attempted any quizzes yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 