<?php
// Database configuration
require_once __DIR__ . '/config/db.php';

// For initial setup, we'll use a default user ID
$user_id = 1; // Assuming user ID 1 is an anime guru

// One Piece quiz
$quiz = [
    'title' => 'One Piece: Journey to the Grand Line',
    'description' => 'Test your knowledge about the world of One Piece, its characters, Devil Fruits, and epic battles!',
    'anime_series' => 'One Piece',
    'difficulty' => 'intermediate',
    'time_limit' => 30,
    'questions' => [
        [
            'question_text' => 'What is the name of Luffy\'s signature Devil Fruit?',
            'question_type' => 'text',
            'correct_answer' => 'Gomu Gomu no Mi',
            'options' => null,
            'image_url' => null
        ],
        [
            'question_text' => 'Who is the captain of the Straw Hat Pirates?',
            'question_type' => 'text',
            'correct_answer' => 'Monkey D. Luffy',
            'options' => null,
            'image_url' => null
        ],
        [
            'question_text' => 'What is the name of Zoro\'s signature three-sword style technique?',
            'question_type' => 'text',
            'correct_answer' => 'Santoryu',
            'options' => null,
            'image_url' => null
        ],
        [
            'question_text' => 'What is the name of the legendary treasure that all pirates are searching for?',
            'question_type' => 'text',
            'correct_answer' => 'One Piece',
            'options' => null,
            'image_url' => null
        ],
        [
            'question_text' => 'Who is the cook of the Straw Hat Pirates?',
            'question_type' => 'text',
            'correct_answer' => 'Sanji',
            'options' => null,
            'image_url' => null
        ],
        [
            'question_text' => 'What is the name of Nami\'s weapon that can control weather?',
            'question_type' => 'text',
            'correct_answer' => 'Clima-Tact',
            'options' => null,
            'image_url' => null
        ],
        [
            'question_text' => 'What is the name of the organization that governs the world in One Piece?',
            'question_type' => 'text',
            'correct_answer' => 'World Government',
            'options' => null,
            'image_url' => null
        ],
        [
            'question_text' => 'What is the name of the strongest military force in the One Piece world?',
            'question_type' => 'text',
            'correct_answer' => 'Marines',
            'options' => null,
            'image_url' => null
        ],
        [
            'question_text' => 'What is the name of the island where the Straw Hat crew first entered the Grand Line?',
            'question_type' => 'text',
            'correct_answer' => 'Reverse Mountain',
            'options' => null,
            'image_url' => null
        ],
        [
            'question_text' => 'What is the name of the currency used in the One Piece world?',
            'question_type' => 'text',
            'correct_answer' => 'Beli',
            'options' => null,
            'image_url' => null
        ]
    ]
];

// Insert quiz
$stmt = $conn->prepare("INSERT INTO anime_quizzes (title, description, anime_series, difficulty, time_limit, created_by) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssii", $quiz['title'], $quiz['description'], $quiz['anime_series'], $quiz['difficulty'], $quiz['time_limit'], $user_id);

if ($stmt->execute()) {
    $quiz_id = $conn->insert_id;
    echo "Added quiz: " . $quiz['title'] . "<br>";
    
    // Insert questions
    foreach ($quiz['questions'] as $question) {
        $stmt = $conn->prepare("INSERT INTO anime_questions (quiz_id, question_text, question_type, correct_answer, options, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $options = $question['options'] ? json_encode($question['options']) : null;
        $stmt->bind_param("isssss", $quiz_id, $question['question_text'], $question['question_type'], $question['correct_answer'], $options, $question['image_url']);
        
        if ($stmt->execute()) {
            echo "Added question: " . $question['question_text'] . "<br>";
        } else {
            echo "Error adding question: " . $conn->error . "<br>";
        }
    }
} else {
    echo "Error adding quiz: " . $conn->error . "<br>";
}

echo "One Piece quiz has been added successfully!";
?> 