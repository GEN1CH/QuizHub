<?php
ob_start();
require_once '../config/db.php';
session_start();

// Check if user is logged in and is a teacher
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: /QuizHub/auth/login.php');
    exit();
}

// Check if quiz ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid quiz ID";
    header('Location: /QuizHub/dashboard/teacher.php');
    exit();
}

$quiz_id = $_GET['id'];
$teacher_id = $_SESSION['user_id'];

// Verify that the quiz belongs to the teacher
$stmt = $conn->prepare("SELECT id FROM quizzes WHERE id = ? AND created_by = ?");
$stmt->bind_param("ii", $quiz_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "You don't have permission to delete this quiz";
    header('Location: /QuizHub/dashboard/teacher.php');
    exit();
}

// Begin transaction to delete quiz and related data
$conn->begin_transaction();

try {
    // Delete answers related to quiz attempts
    $stmt = $conn->prepare("DELETE a FROM answers a 
                           INNER JOIN quiz_attempts qa ON a.attempt_id = qa.id 
                           WHERE qa.quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();

    // Delete quiz attempts
    $stmt = $conn->prepare("DELETE FROM quiz_attempts WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();

    // Delete questions
    $stmt = $conn->prepare("DELETE FROM questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();

    // Delete the quiz
    $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();

    // Commit the transaction
    $conn->commit();
    $_SESSION['success'] = "Quiz deleted successfully";

} catch (Exception $e) {
    // Rollback the transaction if any error occurs
    $conn->rollback();
    $_SESSION['error'] = "Error deleting quiz: " . $e->getMessage();
}

header('Location: /QuizHub/dashboard/teacher.php');
exit(); 