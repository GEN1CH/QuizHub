<?php
$page_title = "Welcome to QuizHub";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-md-12 text-center">
            <h1 class="display-4 mb-4">Welcome to QuizHub</h1>
            <p class="lead mb-4">Create, take, and manage quizzes with ease</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="d-flex justify-content-center gap-3">
                    <a href="auth/register.php" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="auth/login.php" class="btn btn-outline-primary btn-lg">Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row mb-5">
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chalkboard-teacher fa-3x text-primary mb-3"></i>
                    <h3 class="card-title">For Teachers</h3>
                    <p class="card-text">Create and manage quizzes, track student progress, and analyze results.</p>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="auth/register.php?role=teacher" class="btn btn-outline-primary">Register as Teacher</a>
                    <?php elseif ($_SESSION['role'] === 'teacher'): ?>
                        <a href="dashboard/teacher.php" class="btn btn-primary">Go to Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-user-graduate fa-3x text-primary mb-3"></i>
                    <h3 class="card-title">For Students</h3>
                    <p class="card-text">Take quizzes, track your progress, and improve your knowledge.</p>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="auth/register.php?role=student" class="btn btn-outline-primary">Register as Student</a>
                    <?php elseif ($_SESSION['role'] === 'student'): ?>
                        <a href="dashboard/student.php" class="btn btn-primary">Go to Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-dragon fa-3x text-danger mb-3"></i>
                    <h3 class="card-title">For Anime Gurus</h3>
                    <p class="card-text">Create anime-themed quizzes and share your knowledge with anime fans.</p>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="auth/register.php?role=anime_guru" class="btn btn-outline-danger">Register as Anime Guru</a>
                    <?php elseif ($_SESSION['role'] === 'anime_guru'): ?>
                        <a href="dashboard/anime_guru.php" class="btn btn-danger">Go to Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-tv fa-3x text-info mb-3"></i>
                    <h3 class="card-title">For Anime Students</h3>
                    <p class="card-text">Test your anime knowledge with quizzes created by anime experts.</p>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="auth/register.php?role=anime_student" class="btn btn-outline-info">Register as Anime Student</a>
                    <?php elseif ($_SESSION['role'] === 'anime_student'): ?>
                        <a href="dashboard/anime_student.php" class="btn btn-info">Go to Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="row mb-5">
        <div class="col-md-12 text-center">
            <h2 class="mb-4">How It Works</h2>
        </div>
        <div class="col-md-3 mb-4">
            <div class="text-center">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-user-plus fa-2x text-primary"></i>
                </div>
                <h4>1. Register</h4>
                <p>Create your account as a teacher, student, anime guru, or anime student</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="text-center">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-edit fa-2x text-primary"></i>
                </div>
                <h4>2. Create/Take</h4>
                <p>Teachers and anime gurus create quizzes, students take them</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="text-center">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-check-circle fa-2x text-primary"></i>
                </div>
                <h4>3. Submit</h4>
                <p>Complete and submit your quizzes</p>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="text-center">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="fas fa-chart-bar fa-2x text-primary"></i>
                </div>
                <h4>4. Analyze</h4>
                <p>View results and track progress</p>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="row mb-5">
            <div class="col-md-12 text-center">
                <h2 class="mb-4">Ready to Get Started?</h2>
                <p class="lead mb-4">Join QuizHub today and start creating or taking quizzes!</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="auth/register.php" class="btn btn-primary btn-lg">Register Now</a>
                    <a href="auth/login.php" class="btn btn-outline-primary btn-lg">Login</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?> 