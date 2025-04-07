<?php
require_once 'db.php';

// Check if admin already exists
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    die("An admin user already exists. For security reasons, please use the existing admin account.");
}

// Create admin user
$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT); // Default password: admin123
$email = "admin@quizhub.com";
$role = "admin";

$stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $password, $email, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<br>Please change these credentials after your first login.";
} else {
    echo "Error creating admin user: " . $conn->error;
}
?> 