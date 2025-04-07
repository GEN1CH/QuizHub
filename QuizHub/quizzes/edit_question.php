<?php
$page_title = "Edit Question";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../auth/login.php");
    exit();
}

// Check if question ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../dashboard/teacher.php");
    exit();
}

$question_id = intval($_GET['id']);

// Get the question and verify ownership
$stmt = $conn->prepare("
    SELECT q.*, qz.title as quiz_title 
    FROM questions q 
    JOIN quizzes qz ON q.quiz_id = qz.id 
    WHERE q.id = ? AND qz.created_by = ?
");
$stmt->bind_param("ii", $question_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../dashboard/teacher.php");
    exit();
}

$question = $result->fetch_assoc();
$options = $question['options'] ? json_decode($question['options'], true) : [];
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = trim($_POST['question_text'] ?? '');
    $question_type = $_POST['question_type'] ?? 'mcq';
    $correct_answer = trim($_POST['correct_answer'] ?? '');
    $new_options = [];
    
    // For MCQ, collect options
    if ($question_type === 'mcq') {
        for ($i = 1; $i <= 4; $i++) {
            $option = trim($_POST['option' . $i] ?? '');
            if (!empty($option)) {
                $new_options[] = $option;
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
    
    if ($question_type === 'mcq' && count($new_options) < 2) {
        $errors[] = "At least two options are required for MCQ";
    }
    
    // If no errors, update the question
    if (empty($errors)) {
        $options_json = $question_type === 'mcq' ? json_encode($new_options) : null;
        
        $stmt = $conn->prepare("UPDATE questions SET question_text = ?, question_type = ?, correct_answer = ?, options = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $question_text, $question_type, $correct_answer, $options_json, $question_id);
        
        if ($stmt->execute()) {
            $success = true;
            // Update local variables
            $question['question_text'] = $question_text;
            $question['question_type'] = $question_type;
            $question['correct_answer'] = $correct_answer;
            $options = $new_options;
        } else {
            $errors[] = "Failed to update question. Please try again.";
        }
    }
}
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title mb-0">Edit Question</h3>
                <p class="text-muted mb-0">Quiz: <?php echo htmlspecialchars($question['quiz_title']); ?></p>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Question updated successfully!
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
                        <textarea class="form-control" id="question_text" name="question_text" rows="3" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="question_type" class="form-label">Question Type</label>
                        <select class="form-select" id="question_type" name="question_type">
                            <option value="mcq" <?php echo $question['question_type'] === 'mcq' ? 'selected' : ''; ?>>Multiple Choice</option>
                            <option value="short_answer" <?php echo $question['question_type'] === 'short_answer' ? 'selected' : ''; ?>>Short Answer</option>
                        </select>
                    </div>

                    <div id="mcqOptions" class="mb-3">
                        <label class="form-label">Options</label>
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <div class="mb-2">
                                <input type="text" class="form-control" name="option<?php echo $i; ?>" 
                                       placeholder="Option <?php echo $i; ?><?php echo $i > 2 ? ' (optional)' : ''; ?>"
                                       value="<?php echo isset($options[$i-1]) ? htmlspecialchars($options[$i-1]) : ''; ?>">
                            </div>
                        <?php endfor; ?>
                    </div>

                    <div class="mb-3">
                        <label for="correct_answer" class="form-label">Correct Answer</label>
                        <input type="text" class="form-control" id="correct_answer" name="correct_answer" 
                               value="<?php echo htmlspecialchars($question['correct_answer']); ?>" 
                               required>
                        <div class="form-text">
                            For MCQ, enter the exact text of the correct option. For short answer, enter the correct answer text.
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Question</button>
                        <a href="add_questions.php?id=<?php echo $question['quiz_id']; ?>" class="btn btn-outline-secondary">Back to Questions</a>
                    </div>
                </form>
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