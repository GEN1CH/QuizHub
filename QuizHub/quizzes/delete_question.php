<?php
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
    SELECT q.quiz_id, qz.title as quiz_title 
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

// Delete the question
$stmt = $conn->prepare("DELETE FROM questions WHERE id = ?");
$stmt->bind_param("i", $question_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Question deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete question. Please try again.";
}

// Redirect back to the questions page
header("Location: add_questions.php?id=" . $question['quiz_id']);
exit(); 