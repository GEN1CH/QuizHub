<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'quizhub';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($database);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'teacher', 'student', 'anime_guru', 'anime_student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully or already exists<br>";
} else {
    die("Error creating users table: " . $conn->error);
}

// Check if admin exists
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Create admin user
    $username = "admin";
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    $email = "admin@quizhub.com";
    $role = "admin";

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $email, $role);

    if ($stmt->execute()) {
        echo "<br>Admin user created successfully!<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "<br>Please change these credentials after your first login.";
    } else {
        echo "Error creating admin user: " . $conn->error;
    }
} else {
    echo "<br>Admin user already exists. Please use the existing admin account.";
}

$conn->close();
?> 