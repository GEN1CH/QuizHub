<?php
$page_title = "Add Questions";
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

// Verify that the quiz belongs to the current teacher
$stmt = $conn->prepare("SELECT title FROM quizzes WHERE id = ? AND created_by = ?");
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

// Handle question submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = trim($_POST['question_text'] ?? '');
    $question_type = $_POST['question_type'] ?? 'mcq';
    $correct_answer = trim($_POST['correct_answer'] ?? '');
    $options = [];
    
    // For MCQ, collect options
    if ($question_type === 'mcq') {
        for ($i = 1; $i <= 4; $i++) {
            $option = trim($_POST['option' . $i] ?? '');
            if (!empty($option)) {
                $options[] = $option;
            }
        }
    }
    
    // Validation
    if (empty($question_text)) {
        $errors[] = "Question text is required";
    }
    
    if (empty($correct_answer)) {
        $errors[] = "Correct answer is required";
    }
    
    if ($question_type === 'mcq' && count($options) < 2) {
        $errors[] = "At least two options are required for MCQ";
    }
    
    // If no errors, add the question
    if (empty($errors)) {
        $options_json = $question_type === 'mcq' ? json_encode($options) : null;
        
        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, question_type, correct_answer, options) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $quiz_id, $question_text, $question_type, $correct_answer, $options_json);
        
        if ($stmt->execute()) {
            $success = true;
            // Clear form data
            $_POST = [];
        } else {
            $errors[] = "Failed to add question. Please try again.";
        }
    }
}

// Get existing questions
$stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions = $stmt->get_result();
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title mb-0">Add Questions to: <?php echo htmlspecialchars($quiz['title']); ?></h3>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Question added successfully!
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

                <form method="POST" action="" id="questionForm">
                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question Text</label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="3" required><?php echo isset($_POST['question_text']) ? htmlspecialchars($_POST['question_text']) : ''; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="question_type" class="form-label">Question Type</label>
                        <select class="form-select" id="question_type" name="question_type">
                            <option value="mcq" <?php echo (isset($_POST['question_type']) && $_POST['question_type'] == 'mcq') ? 'selected' : ''; ?>>Multiple Choice</option>
                            <option value="short_answer" <?php echo (isset($_POST['question_type']) && $_POST['question_type'] == 'short_answer') ? 'selected' : ''; ?>>Short Answer</option>
                        </select>
                    </div>

                    <div id="mcqOptions" class="mb-3">
                        <label class="form-label">Options</label>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="option1" placeholder="Option 1" value="<?php echo isset($_POST['option1']) ? htmlspecialchars($_POST['option1']) : ''; ?>">
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="option2" placeholder="Option 2" value="<?php echo isset($_POST['option2']) ? htmlspecialchars($_POST['option2']) : ''; ?>">
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="option3" placeholder="Option 3 (optional)" value="<?php echo isset($_POST['option3']) ? htmlspecialchars($_POST['option3']) : ''; ?>">
                        </div>
                        <div class="mb-2">
                            <input type="text" class="form-control" name="option4" placeholder="Option 4 (optional)" value="<?php echo isset($_POST['option4']) ? htmlspecialchars($_POST['option4']) : ''; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="correct_answer" class="form-label">Correct Answer</label>
                        <input type="text" class="form-control" id="correct_answer" name="correct_answer" 
                               value="<?php echo isset($_POST['correct_answer']) ? htmlspecialchars($_POST['correct_answer']) : ''; ?>" 
                               required>
                        <div class="form-text">
                            For MCQ, enter the exact text of the correct option. For short answer, enter the correct answer text.
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Add Question</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Questions Added</h4>
            </div>
            <div class="card-body">
                <?php if ($questions->num_rows > 0): ?>
                    <div class="list-group">
                        <?php while ($question = $questions->fetch_assoc()): ?>
                            <div class="list-group-item">
                                <h6 class="mb-1"><?php echo htmlspecialchars($question['question_text']); ?></h6>
                                <p class="mb-1">
                                    <small class="text-muted">
                                        Type: <?php echo $question['question_type'] === 'mcq' ? 'Multiple Choice' : 'Short Answer'; ?>
                                    </small>
                                </p>
                                <div class="btn-group btn-group-sm">
                                    <a href="edit_question.php?id=<?php echo $question['id']; ?>" class="btn btn-outline-primary">Edit</a>
                                    <a href="delete_question.php?id=<?php echo $question['id']; ?>" 
                                       class="btn btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this question?')">Delete</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="d-grid gap-2 mt-3">
                        <a href="../dashboard/teacher.php" class="btn btn-success">Finish & Return to Dashboard</a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <p class="mb-0">No questions added yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionType = document.getElementById('question_type');
    const mcqOptions = document.getElementById('mcqOptions');
    
    function toggleMcqOptions() {
        if (questionType.value === 'mcq') {
            mcqOptions.style.display = 'block';
        } else {
            mcqOptions.style.display = 'none';
        }
    }
    
    questionType.addEventListener('change', toggleMcqOptions);
    toggleMcqOptions(); // Initial state
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 