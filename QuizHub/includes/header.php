<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizHub - <?php echo isset($page_title) ? $page_title : 'Welcome'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/QuizHub/index.php">QuizHub</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/QuizHub/index.php">Home</a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['role'] == 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/QuizHub/dashboard/admin.php">Admin Dashboard</a>
                            </li>
                        <?php elseif($_SESSION['role'] == 'teacher'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/QuizHub/dashboard/teacher.php">Teacher Dashboard</a>
                            </li>
                        <?php elseif($_SESSION['role'] == 'student'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/QuizHub/dashboard/student.php">Student Dashboard</a>
                            </li>
                        <?php elseif($_SESSION['role'] == 'anime_guru'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/QuizHub/dashboard/anime_guru.php">Anime Guru Dashboard</a>
                            </li>
                        <?php elseif($_SESSION['role'] == 'anime_student'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/QuizHub/dashboard/anime_student.php">Anime Student Dashboard</a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/QuizHub/auth/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/QuizHub/auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/QuizHub/auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4"> 