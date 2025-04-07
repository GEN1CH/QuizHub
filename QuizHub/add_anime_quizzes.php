<?php
// Database configuration
require_once __DIR__ . '/config/db.php';

// For initial setup, we'll use a default user ID
// In a production environment, you would want to use proper authentication
$user_id = 1; // Assuming user ID 1 is an anime guru

// Sample anime quizzes
$quizzes = [
    [
        'title' => 'Naruto: The Hidden Leaf Village',
        'description' => 'Test your knowledge about the characters, techniques, and lore of the Naruto series.',
        'anime_series' => 'Naruto',
        'difficulty' => 'intermediate',
        'time_limit' => 30,
        'questions' => [
            [
                'question_text' => 'Who is the main protagonist of the Naruto series?',
                'question_type' => 'text',
                'correct_answer' => 'Naruto Uzumaki',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'What is the name of the technique that Naruto is most famous for?',
                'question_type' => 'text',
                'correct_answer' => 'Shadow Clone Jutsu',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'Who is Naruto\'s rival throughout the series?',
                'question_type' => 'text',
                'correct_answer' => 'Sasuke Uchiha',
                'options' => null,
                'image_url' => null
            ]
        ]
    ],
    [
        'title' => 'Attack on Titan: The Walls of Humanity',
        'description' => 'How well do you know the world of Attack on Titan? Test your knowledge about the titans, the walls, and the characters.',
        'anime_series' => 'Attack on Titan',
        'difficulty' => 'advanced',
        'time_limit' => 45,
        'questions' => [
            [
                'question_text' => 'What are the three walls that protect humanity called?',
                'question_type' => 'text',
                'correct_answer' => 'Maria, Rose, and Sina',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'What is the name of the main protagonist?',
                'question_type' => 'text',
                'correct_answer' => 'Eren Yeager',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'What is the name of the device used by soldiers to navigate through the air?',
                'question_type' => 'text',
                'correct_answer' => 'Vertical Maneuvering Equipment',
                'options' => null,
                'image_url' => null
            ]
        ]
    ],
    [
        'title' => 'Dragon Ball: The Saiyan Saga',
        'description' => 'Test your knowledge about the Dragon Ball series, focusing on the Saiyan saga and beyond.',
        'anime_series' => 'Dragon Ball',
        'difficulty' => 'beginner',
        'time_limit' => 20,
        'questions' => [
            [
                'question_text' => 'What is the name of the main protagonist in Dragon Ball?',
                'question_type' => 'text',
                'correct_answer' => 'Goku',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'What is the name of Goku\'s signature attack?',
                'question_type' => 'text',
                'correct_answer' => 'Kamehameha',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'What is the name of the dragon that grants wishes when all seven Dragon Balls are collected?',
                'question_type' => 'text',
                'correct_answer' => 'Shenron',
                'options' => null,
                'image_url' => null
            ]
        ]
    ],
    [
        'title' => 'My Hero Academia: The World of Heroes',
        'description' => 'How well do you know the world of My Hero Academia? Test your knowledge about quirks, heroes, and villains.',
        'anime_series' => 'My Hero Academia',
        'difficulty' => 'intermediate',
        'time_limit' => 25,
        'questions' => [
            [
                'question_text' => 'What is the name of the main protagonist?',
                'question_type' => 'text',
                'correct_answer' => 'Izuku Midoriya',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'What is the name of the quirk that Izuku inherits from All Might?',
                'question_type' => 'text',
                'correct_answer' => 'One For All',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'What is the name of the school that Izuku attends?',
                'question_type' => 'text',
                'correct_answer' => 'U.A. High School',
                'options' => null,
                'image_url' => null
            ]
        ]
    ],
    [
        'title' => 'Death Note: The Battle of Wits',
        'description' => 'Test your knowledge about the psychological thriller Death Note and the battle between Light and L.',
        'anime_series' => 'Death Note',
        'difficulty' => 'advanced',
        'time_limit' => 30,
        'questions' => [
            [
                'question_text' => 'What is the name of the main protagonist who finds the Death Note?',
                'question_type' => 'text',
                'correct_answer' => 'Light Yagami',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'What is the name of the detective who tries to catch Light?',
                'question_type' => 'text',
                'correct_answer' => 'L',
                'options' => null,
                'image_url' => null
            ],
            [
                'question_text' => 'What happens to a person whose name is written in the Death Note?',
                'question_type' => 'text',
                'correct_answer' => 'They die of a heart attack',
                'options' => null,
                'image_url' => null
            ]
        ]
    ]
];

// Insert quizzes
foreach ($quizzes as $quiz) {
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
                echo "Added question to " . $quiz['title'] . "<br>";
            } else {
                echo "Error adding question: " . $conn->error . "<br>";
            }
        }
    } else {
        echo "Error adding quiz: " . $conn->error . "<br>";
    }
}

echo "All sample anime quizzes have been added successfully!";
?> 