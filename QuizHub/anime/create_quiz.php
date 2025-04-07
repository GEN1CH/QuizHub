<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug session information
echo "Session information:<br>";
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Not set') . "<br>";
echo "Role: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'Not set') . "<br>";
echo "Username: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'Not set') . "<br>";

$page_title = "Create Anime Quiz";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is an anime guru
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'anime_guru') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $anime_series = trim($_POST['anime_series']);
    $difficulty = $_POST['difficulty'];
    $time_limit = intval($_POST['time_limit']);

    // Debug output
    echo "Form submitted with values:<br>";
    echo "Title: " . htmlspecialchars($title) . "<br>";
    echo "Description: " . htmlspecialchars($description) . "<br>";
    echo "Anime Series: " . htmlspecialchars($anime_series) . "<br>";
    echo "Difficulty: " . htmlspecialchars($difficulty) . "<br>";
    echo "Time Limit: " . $time_limit . "<br>";

    // Validate required fields
    if (empty($title) || empty($anime_series) || empty($difficulty)) {
        $error = "Please fill in all required fields.";
    } else {
        // Insert quiz
        $stmt = $conn->prepare("INSERT INTO anime_quizzes (title, description, anime_series, difficulty, time_limit, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $title, $description, $anime_series, $difficulty, $time_limit, $user_id);
        
        if ($stmt->execute()) {
            $quiz_id = $conn->insert_id;
            echo "Quiz created successfully with ID: " . $quiz_id . "<br>";
            header("Location: add_questions.php?id=" . $quiz_id);
            exit();
        } else {
            $error = "Error creating quiz: " . $conn->error;
            echo "Database error: " . $conn->error . "<br>";
        }
    }
}
?>

<style>
.anime-create {
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
</style>

<div class="anime-create">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="anime-form-card">
                    <h1 class="anime-title text-center">Create New Anime Quiz</h1>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="title" class="form-label">Quiz Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="anime_series" class="form-label">Anime Series *</label>
                            <input type="text" class="form-control" id="anime_series" name="anime_series" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="difficulty" class="form-label">Difficulty Level *</label>
                            <select class="form-select" id="difficulty" name="difficulty" required>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="time_limit" class="form-label">Time Limit (minutes)</label>
                            <input type="number" class="form-control" id="time_limit" name="time_limit" value="30" min="1">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn anime-btn">Create Quiz</button>
                            <a href="../dashboard/anime_guru.php" class="btn btn-outline-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 