<?php
// Database configuration
require_once __DIR__ . '/config/db.php';

// Update the users table to include the new roles
$sql = "ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student', 'anime_guru', 'anime_student') NOT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Users table updated successfully to include anime_guru and anime_student roles.<br>";
} else {
    echo "Error updating users table: " . $conn->error . "<br>";
}

// Check if there are any users with the anime_guru or anime_student role
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role IN ('anime_guru', 'anime_student')");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo "Number of users with anime_guru or anime_student role: " . $row['count'] . "<br>";

// Close the connection
$conn->close();
?> 