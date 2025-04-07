<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Edit Quiz";
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
$stmt = $conn->prepare("SELECT * FROM quizzes WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii", $quiz_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../dashboard/teacher.php");
    exit();
}

$quiz = $result->fetch_assoc();
$errors = [];
$success = false;

// Get all categories for the dropdown
$categories = [];
$result = $conn->query("SELECT id, name FROM categories ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get all subjects for the dropdown
$subjects = [];
$result = $conn->query("SELECT id, name FROM subjects ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

// Define grade levels
$grade_levels = [
    'Grade 1',
    'Grade 2',
    'Grade 3',
    'Grade 4',
    'Grade 5',
    'Grade 6',
    'Grade 7',
    'Grade 8',
    'Grade 9',
    'Grade 10',
    'Grade 11',
    'Grade 12',
    'College Year 1',
    'College Year 2',
    'College Year 3',
    'College Year 4',
    'Graduate',
    'General'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = isset($_POST['category_id']) && is_numeric($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $subject_id = isset($_POST['subject_id']) && is_numeric($_POST['subject_id']) ? intval($_POST['subject_id']) : null;
    $grade_level = trim($_POST['grade_level'] ?? '');
    $time_limit = intval($_POST['time_limit'] ?? 0);
    
    // Validation
    if (empty($title)) {
        $errors[] = "Quiz title is required";
    }
    
    if ($time_limit < 0) {
        $errors[] = "Time limit cannot be negative";
    }
    
    if (empty($category_id)) {
        $errors[] = "Please select a category";
    }
    
    if (empty($subject_id)) {
        $errors[] = "Please select a subject";
    }
    
    if (empty($grade_level)) {
        $errors[] = "Please select a grade level";
    }
    
    // If no errors, update the quiz
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ?, category_id = ?, subject_id = ?, grade_level = ?, time_limit = ? WHERE id = ?");
        $stmt->bind_param("ssiissi", $title, $description, $category_id, $subject_id, $grade_level, $time_limit, $quiz_id);
        
        if ($stmt->execute()) {
            $success = true;
            // Update local variables
            $quiz['title'] = $title;
            $quiz['description'] = $description;
            $quiz['category_id'] = $category_id;
            $quiz['subject_id'] = $subject_id;
            $quiz['grade_level'] = $grade_level;
            $quiz['time_limit'] = $time_limit;
        } else {
            $errors[] = "Failed to update quiz. Please try again.";
        }
    }
}
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
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Edit Quiz</h3>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Quiz updated successfully!
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="title" class="form-label">Quiz Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo htmlspecialchars($quiz['title']); ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                    <?php echo $quiz['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">Select a subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['id']; ?>" 
                                    <?php echo $quiz['subject_id'] == $subject['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($subject['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="grade_level" class="form-label">Grade Level</label>
                        <select class="form-select" id="grade_level" name="grade_level" required>
                            <option value="">Select a grade level</option>
                            <?php foreach ($grade_levels as $level): ?>
                                <option value="<?php echo $level; ?>" 
                                    <?php echo $quiz['grade_level'] == $level ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($level); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="time_limit" class="form-label">Time Limit (minutes, 0 for no limit)</label>
                        <input type="number" class="form-control" id="time_limit" name="time_limit" 
                               value="<?php echo intval($quiz['time_limit']); ?>" 
                               min="0">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Quiz</button>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 