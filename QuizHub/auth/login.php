<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Login";
require_once __DIR__ . '/../includes/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = trim($_POST['login'] ?? ''); // This can be either username or email
    $password = $_POST['password'] ?? '';

    if (empty($login)) {
        $errors[] = "Username or email is required";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        // Check if input is email or username
        $is_email = filter_var($login, FILTER_VALIDATE_EMAIL);
        
        if ($is_email) {
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
        } else {
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        }
        
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: ../dashboard/admin.php");
                        break;
                    case 'teacher':
                        header("Location: ../dashboard/teacher.php");
                        break;
                    case 'student':
                        header("Location: ../dashboard/student.php");
                        break;
                    case 'anime_guru':
                        header("Location: ../dashboard/anime_guru.php");
                        break;
                    case 'anime_student':
                        header("Location: ../dashboard/anime_student.php");
                        break;
                    default:
                        header("Location: ../index.php");
                }
                exit();
            } else {
                $errors[] = "Invalid username/email or password";
            }
        } else {
            $errors[] = "Invalid username/email or password";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title mb-0">Login</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">
                        Registration successful! Please login with your credentials.
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
                        <label for="login" class="form-label">Username or Email</label>
                        <input type="text" class="form-control" id="login" name="login" 
                               value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 