<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'quizhub');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    $conn->select_db(DB_NAME);
} else {
    die("Error creating database: " . $conn->error);
}

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'teacher', 'student', 'anime_guru', 'anime_student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating users table: " . $conn->error);
}

// Create quizzes table
$sql = "CREATE TABLE IF NOT EXISTS quizzes (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    time_limit INT(11), -- in minutes
    created_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating quizzes table: " . $conn->error);
}

// Create questions table
$sql = "CREATE TABLE IF NOT EXISTS questions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT(11),
    question_text TEXT NOT NULL,
    question_type ENUM('mcq', 'short_answer') NOT NULL,
    correct_answer TEXT NOT NULL,
    options JSON, -- for MCQ questions
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating questions table: " . $conn->error);
}

// Create quiz_attempts table
$sql = "CREATE TABLE IF NOT EXISTS quiz_attempts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT(11),
    user_id INT(11),
    score FLOAT,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    retake_allowed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating quiz_attempts table: " . $conn->error);
}

// Create answers table
$sql = "CREATE TABLE IF NOT EXISTS answers (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT(11),
    question_id INT(11),
    user_answer TEXT,
    is_correct BOOLEAN,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating answers table: " . $conn->error);
}

// Create subjects table
$sql = "CREATE TABLE IF NOT EXISTS subjects (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating subjects table: " . $conn->error);
}

// Insert default subjects if the table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM subjects");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $default_subjects = [
        ['Mathematics', 'Math courses including algebra, calculus, geometry, etc.'],
        ['Science', 'Science courses including physics, chemistry, biology, etc.'],
        ['English', 'English language and literature courses'],
        ['History', 'History and social studies courses'],
        ['Computer Science', 'Programming, algorithms, and computer systems'],
        ['Foreign Languages', 'Languages other than English'],
        ['Arts', 'Visual arts, music, and performing arts'],
        ['Physical Education', 'Sports, fitness, and health education']
    ];
    
    $stmt = $conn->prepare("INSERT INTO subjects (name, description) VALUES (?, ?)");
    foreach ($default_subjects as $subject) {
        $stmt->bind_param("ss", $subject[0], $subject[1]);
        $stmt->execute();
    }
}

// Add subject_id to quizzes table if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM quizzes LIKE 'subject_id'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE quizzes ADD COLUMN subject_id INT(11) AFTER description, ADD FOREIGN KEY (subject_id) REFERENCES subjects(id)";
    if ($conn->query($sql) !== TRUE) {
        die("Error adding subject_id to quizzes table: " . $conn->error);
    }
}

// Create categories table
$sql = "CREATE TABLE IF NOT EXISTS categories (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating categories table: " . $conn->error);
}

// Insert default categories if the table is empty
$result = $conn->query("SELECT COUNT(*) as count FROM categories");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $default_categories = [
        ['First Year', 'Courses for first-year students'],
        ['Second Year', 'Courses for second-year students'],
        ['Third Year', 'Courses for third-year students'],
        ['Fourth Year', 'Courses for fourth-year students'],
        ['Graduate', 'Graduate-level courses'],
        ['General', 'General courses applicable to all levels']
    ];
    
    $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
    foreach ($default_categories as $category) {
        $stmt->bind_param("ss", $category[0], $category[1]);
        $stmt->execute();
    }
}

// Add category_id to subjects table if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM subjects LIKE 'category_id'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE subjects ADD COLUMN category_id INT(11) AFTER description, ADD FOREIGN KEY (category_id) REFERENCES categories(id)";
    if ($conn->query($sql) !== TRUE) {
        die("Error adding category_id to subjects table: " . $conn->error);
    }
}

// Add grade_level to quizzes table if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM quizzes LIKE 'grade_level'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE quizzes ADD COLUMN grade_level VARCHAR(50) AFTER subject_id";
    if ($conn->query($sql) !== TRUE) {
        die("Error adding grade_level to quizzes table: " . $conn->error);
    }
}

// Add retake_allowed column to quiz_attempts table if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM quiz_attempts LIKE 'retake_allowed'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE quiz_attempts ADD COLUMN retake_allowed BOOLEAN DEFAULT FALSE AFTER completed_at";
    if ($conn->query($sql) !== TRUE) {
        die("Error adding retake_allowed to quiz_attempts table: " . $conn->error);
    }
}

// Add category_id to quizzes table if it doesn't exist
$result = $conn->query("SHOW COLUMNS FROM quizzes LIKE 'category_id'");
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE quizzes ADD COLUMN category_id INT(11) AFTER subject_id, ADD FOREIGN KEY (category_id) REFERENCES categories(id)";
    if ($conn->query($sql) !== TRUE) {
        die("Error adding category_id to quizzes table: " . $conn->error);
    }
}

// Create anime_quizzes table
$sql = "CREATE TABLE IF NOT EXISTS anime_quizzes (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    anime_series VARCHAR(255) NOT NULL,
    difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    time_limit INT(11),
    created_by INT(11),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating anime_quizzes table: " . $conn->error);
}

// Create anime_questions table
$sql = "CREATE TABLE IF NOT EXISTS anime_questions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT(11),
    question_text TEXT NOT NULL,
    question_type ENUM('mcq', 'short_answer', 'image_question') NOT NULL,
    correct_answer TEXT NOT NULL,
    options JSON,
    image_url VARCHAR(255),
    FOREIGN KEY (quiz_id) REFERENCES anime_quizzes(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating anime_questions table: " . $conn->error);
}

// Create anime_quiz_attempts table
$sql = "CREATE TABLE IF NOT EXISTS anime_quiz_attempts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT(11),
    user_id INT(11),
    score FLOAT,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    retake_allowed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (quiz_id) REFERENCES anime_quizzes(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating anime_quiz_attempts table: " . $conn->error);
}

// Create anime_answers table
$sql = "CREATE TABLE IF NOT EXISTS anime_answers (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT(11),
    question_id INT(11),
    user_answer TEXT,
    is_correct BOOLEAN,
    FOREIGN KEY (attempt_id) REFERENCES anime_quiz_attempts(id),
    FOREIGN KEY (question_id) REFERENCES anime_questions(id)
)";

if ($conn->query($sql) !== TRUE) {
    die("Error creating anime_answers table: " . $conn->error);
}
?> 