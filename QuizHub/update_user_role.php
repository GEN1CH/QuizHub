<?php
// Database configuration
require_once __DIR__ . '/config/db.php';

// Update the user with ID 4 to have the 'anime_guru' role
$sql = "UPDATE users SET role = 'anime_guru' WHERE id = 4";

if ($conn->query($sql) === TRUE) {
    echo "User with ID 4 updated successfully to have the 'anime_guru' role.<br>";
} else {
    echo "Error updating user: " . $conn->error . "<br>";
}

// Verify the update
$stmt = $conn->prepare("SELECT id, username, email, role FROM users WHERE id = 4");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo "User details after update:<br>";
echo "ID: " . $row["id"] . "<br>";
echo "Username: " . $row["username"] . "<br>";
echo "Email: " . $row["email"] . "<br>";
echo "Role: " . $row["role"] . "<br>";

// Close the connection
$conn->close();
?> 