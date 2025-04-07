<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Add Questions";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is an anime guru
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anime_guru') {
    header("Location: ../auth/login.php");
    exit();
}

// Check if quiz ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../dashboard/anime_guru.php");
    exit();
}

$quiz_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Verify quiz ownership
$stmt = $conn->prepare("SELECT * FROM anime_quizzes WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii", $quiz_id, $user_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if (!$quiz) {
    header("Location: ../dashboard/anime_guru.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = trim($_POST['question_text']);
    $question_type = $_POST['question_type'];
    $correct_answer = trim($_POST['correct_answer']);
    $options = isset($_POST['options']) ? json_encode($_POST['options']) : null;
    $image_url = trim($_POST['image_url']);

    // Validate required fields
    if (empty($question_text) || empty($correct_answer)) {
        $error = "Please fill in all required fields.";
    } else {
        // Insert question
        $stmt = $conn->prepare("INSERT INTO anime_questions (quiz_id, question_text, question_type, correct_answer, options, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $quiz_id, $question_text, $question_type, $correct_answer, $options, $image_url);
        
        if ($stmt->execute()) {
            $success = "Question added successfully!";
        } else {
            $error = "Error adding question: " . $conn->error;
        }
    }
}

// Get existing questions
$stmt = $conn->prepare("SELECT * FROM anime_questions WHERE quiz_id = ? ORDER BY id");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions = $stmt->get_result();
?>

<style>
.anime-questions {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    min-height: 100vh;
    color: #fff;
    padding: 2rem 0;
}

.anime-form-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 2rem;
}

.anime-title {
    font-family: 'Nunito', sans-serif;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    margin-bottom: 2rem;
}

.anime-btn {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
    border: none;
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.anime-btn:hover {
    background: linear-gradient(45deg, #ff8e8e, #ff6b6b);
    transform: scale(1.05);
    color: white;
}

.form-control {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
}

.form-control:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #ff6b6b;
    color: #fff;
    box-shadow: 0 0 0 0.25rem rgba(255, 107, 107, 0.25);
}

.form-label {
    color: #fff;
}

.form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
}

.form-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #ff6b6b;
    color: #fff;
    box-shadow: 0 0 0 0.25rem rgba(255, 107, 107, 0.25);
}

.question-list {
    margin-top: 2rem;
}

.question-item {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.question-image {
    max-width: 200px;
    border-radius: 10px;
    margin: 1rem 0;
}
</style>

<div class="anime-questions">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="anime-form-card">
                    <h1 class="anime-title text-center">Add Questions to <?php echo htmlspecialchars($quiz['title']); ?></h1>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="" id="questionForm">
                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text *</label>
                            <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="question_type" class="form-label">Question Type *</label>
                            <select class="form-select" id="question_type" name="question_type" required>
                                <option value="mcq">Multiple Choice</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="image_question">Image Question</option>
                            </select>
                        </div>

                        <div class="mb-3" id="optionsContainer" style="display: none;">
                            <label class="form-label">Options *</label>
                            <div id="optionsList">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 1">
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 2">
                                </div>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 3">
                                    <input type="text" class="form-control" name="options[]" placeholder="Option 4">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="correct_answer" class="form-label">Correct Answer *</label>
                            <input type="text" class="form-control" id="correct_answer" name="correct_answer" required>
                        </div>

                        <div class="mb-3" id="imageUrlContainer" style="display: none;">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn anime-btn">Add Question</button>
                            <a href="../dashboard/anime_guru.php" class="btn btn-outline-light">Finish</a>
                        </div>
                    </form>

                    <div class="question-list">
                        <h3 class="anime-title">Existing Questions</h3>
                        <?php while ($question = $questions->fetch_assoc()): ?>
                            <div class="question-item">
                                <p class="mb-2"><?php echo nl2br(htmlspecialchars($question['question_text'])); ?></p>
                                <?php if ($question['question_type'] === 'image_question' && $question['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($question['image_url']); ?>" alt="Question Image" class="question-image">
                                <?php endif; ?>
                                <?php if ($question['question_type'] === 'mcq' && $question['options']): ?>
                                    <div class="options-list">
                                        <?php foreach (json_decode($question['options']) as $option): ?>
                                            <div class="option"><?php echo htmlspecialchars($option); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <p class="mt-2"><strong>Correct Answer:</strong> <?php echo htmlspecialchars($question['correct_answer']); ?></p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('question_type').addEventListener('change', function() {
    const optionsContainer = document.getElementById('optionsContainer');
    const imageUrlContainer = document.getElementById('imageUrlContainer');
    
    if (this.value === 'mcq') {
        optionsContainer.style.display = 'block';
        imageUrlContainer.style.display = 'none';
    } else if (this.value === 'image_question') {
        optionsContainer.style.display = 'none';
        imageUrlContainer.style.display = 'block';
    } else {
        optionsContainer.style.display = 'none';
        imageUrlContainer.style.display = 'none';
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 