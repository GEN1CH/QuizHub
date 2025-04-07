<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Create Quiz";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

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
    
    // If no errors, create the quiz
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO quizzes (title, description, category_id, subject_id, grade_level, time_limit, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiissi", $title, $description, $category_id, $subject_id, $grade_level, $time_limit, $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $quiz_id = $conn->insert_id;
            $success = true;
            // Redirect to add questions page
            header("Location: add_questions.php?id=" . $quiz_id);
            exit();
        } else {
            $errors[] = "Failed to create quiz. Please try again.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">Create New Quiz</h3>
            </div>
            <div class="card-body">
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
                               value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" 
                                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
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
                                    <?php echo (isset($_POST['subject_id']) && $_POST['subject_id'] == $subject['id']) ? 'selected' : ''; ?>>
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
                                    <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] == $level) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($level); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="time_limit" class="form-label">Time Limit (minutes, 0 for no limit)</label>
                        <input type="number" class="form-control" id="time_limit" name="time_limit" 
                               value="<?php echo isset($_POST['time_limit']) ? intval($_POST['time_limit']) : 0; ?>" 
                               min="0">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Create Quiz</button>
                        <a href="../dashboard/teacher.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 