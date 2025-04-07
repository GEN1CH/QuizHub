<?php
// This script adds sample questions for each subject in the database
require_once __DIR__ . '/../config/db.php';

// Check if the script is being run from the command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

echo "Starting to add sample questions...\n";

// Get all subjects
$subjects = [];
$result = $conn->query("SELECT id, name FROM subjects ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

// Sample questions for each subject
$sample_questions = [
    'Mathematics' => [
        [
            'question_text' => 'What is the square root of 144?',
            'question_type' => 'short_answer',
            'correct_answer' => '12',
            'options' => null
        ],
        [
            'question_text' => 'What is the area of a circle with radius 5 units?',
            'question_type' => 'short_answer',
            'correct_answer' => '78.54',
            'options' => null
        ],
        [
            'question_text' => 'Solve for x: 2x + 5 = 15',
            'question_type' => 'short_answer',
            'correct_answer' => '5',
            'options' => null
        ],
        [
            'question_text' => 'What is the slope of the line passing through points (2,3) and (4,7)?',
            'question_type' => 'short_answer',
            'correct_answer' => '2',
            'options' => null
        ],
        [
            'question_text' => 'What is the derivative of f(x) = x²?',
            'question_type' => 'short_answer',
            'correct_answer' => '2x',
            'options' => null
        ],
        [
            'question_text' => 'What is the value of sin(90°)?',
            'question_type' => 'short_answer',
            'correct_answer' => '1',
            'options' => null
        ],
        [
            'question_text' => 'What is the probability of rolling a 6 on a fair six-sided die?',
            'question_type' => 'short_answer',
            'correct_answer' => '1/6',
            'options' => null
        ],
        [
            'question_text' => 'What is the volume of a cube with side length 3 units?',
            'question_type' => 'short_answer',
            'correct_answer' => '27',
            'options' => null
        ],
        [
            'question_text' => 'What is the sum of the first 10 positive integers?',
            'question_type' => 'short_answer',
            'correct_answer' => '55',
            'options' => null
        ],
        [
            'question_text' => 'What is the value of log₁₀(100)?',
            'question_type' => 'short_answer',
            'correct_answer' => '2',
            'options' => null
        ]
    ],
    'Science' => [
        [
            'question_text' => 'What is the chemical symbol for gold?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Au',
            'options' => null
        ],
        [
            'question_text' => 'What is the largest planet in our solar system?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Jupiter',
            'options' => null
        ],
        [
            'question_text' => 'What is the process by which plants convert light energy into chemical energy?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Photosynthesis',
            'options' => null
        ],
        [
            'question_text' => 'What is the atomic number of carbon?',
            'question_type' => 'short_answer',
            'correct_answer' => '6',
            'options' => null
        ],
        [
            'question_text' => 'What is the speed of light in meters per second?',
            'question_type' => 'short_answer',
            'correct_answer' => '299792458',
            'options' => null
        ],
        [
            'question_text' => 'What is the main component of the Sun?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Hydrogen',
            'options' => null
        ],
        [
            'question_text' => 'What is the chemical formula for water?',
            'question_type' => 'short_answer',
            'correct_answer' => 'H2O',
            'options' => null
        ],
        [
            'question_text' => 'What is the unit of force in the SI system?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Newton',
            'options' => null
        ],
        [
            'question_text' => 'What is the process by which a solid changes directly into a gas?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Sublimation',
            'options' => null
        ],
        [
            'question_text' => 'What is the name of the force that pulls objects toward the center of the Earth?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Gravity',
            'options' => null
        ]
    ],
    'English' => [
        [
            'question_text' => 'Which of the following is a synonym for "happy"?',
            'question_type' => 'mcq',
            'correct_answer' => 'Joyful',
            'options' => json_encode(['Sad', 'Joyful', 'Angry', 'Tired'])
        ],
        [
            'question_text' => 'Identify the part of speech for the word "quickly" in the sentence: "She quickly ran to the store."',
            'question_type' => 'short_answer',
            'correct_answer' => 'Adverb',
            'options' => null
        ],
        [
            'question_text' => 'Which of the following sentences is grammatically correct?',
            'question_type' => 'mcq',
            'correct_answer' => 'The cat and the dog are playing in the yard.',
            'options' => json_encode([
                'The cat and the dog is playing in the yard.',
                'The cat and the dog are playing in the yard.',
                'The cat and the dog was playing in the yard.',
                'The cat and the dog were playing in the yard.'
            ])
        ],
        [
            'question_text' => 'What is the past tense of the verb "go"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Went',
            'options' => null
        ],
        [
            'question_text' => 'Which of the following is a proper noun?',
            'question_type' => 'mcq',
            'correct_answer' => 'London',
            'options' => json_encode(['City', 'London', 'Book', 'Tree'])
        ],
        [
            'question_text' => 'What is the plural form of "child"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Children',
            'options' => null
        ],
        [
            'question_text' => 'Which punctuation mark is used to indicate possession?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Apostrophe',
            'options' => null
        ],
        [
            'question_text' => 'What is the opposite of "hot"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Cold',
            'options' => null
        ],
        [
            'question_text' => 'Which of the following is a conjunction?',
            'question_type' => 'mcq',
            'correct_answer' => 'And',
            'options' => json_encode(['And', 'Happy', 'Run', 'Book'])
        ],
        [
            'question_text' => 'What is the comparative form of "good"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Better',
            'options' => null
        ]
    ],
    'History' => [
        [
            'question_text' => 'In which year did World War II end?',
            'question_type' => 'short_answer',
            'correct_answer' => '1945',
            'options' => null
        ],
        [
            'question_text' => 'Who was the first President of the United States?',
            'question_type' => 'short_answer',
            'correct_answer' => 'George Washington',
            'options' => null
        ],
        [
            'question_text' => 'Which ancient civilization built the pyramids?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Egyptians',
            'options' => null
        ],
        [
            'question_text' => 'In which year did the Titanic sink?',
            'question_type' => 'short_answer',
            'correct_answer' => '1912',
            'options' => null
        ],
        [
            'question_text' => 'Who painted the Mona Lisa?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Leonardo da Vinci',
            'options' => null
        ],
        [
            'question_text' => 'Which empire was ruled by Emperor Augustus?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Roman Empire',
            'options' => null
        ],
        [
            'question_text' => 'In which year did Christopher Columbus reach the Americas?',
            'question_type' => 'short_answer',
            'correct_answer' => '1492',
            'options' => null
        ],
        [
            'question_text' => 'Who was the first woman to fly solo across the Atlantic Ocean?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Amelia Earhart',
            'options' => null
        ],
        [
            'question_text' => 'Which war was fought between the North and South regions of the United States?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Civil War',
            'options' => null
        ],
        [
            'question_text' => 'Who was the first human to walk on the moon?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Neil Armstrong',
            'options' => null
        ]
    ],
    'Computer Science' => [
        [
            'question_text' => 'What does HTML stand for?',
            'question_type' => 'short_answer',
            'correct_answer' => 'HyperText Markup Language',
            'options' => null
        ],
        [
            'question_text' => 'Which of the following is a programming language?',
            'question_type' => 'mcq',
            'correct_answer' => 'Python',
            'options' => json_encode(['Python', 'Excel', 'Windows', 'Linux'])
        ],
        [
            'question_text' => 'What is the binary representation of the decimal number 10?',
            'question_type' => 'short_answer',
            'correct_answer' => '1010',
            'options' => null
        ],
        [
            'question_text' => 'What does CPU stand for?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Central Processing Unit',
            'options' => null
        ],
        [
            'question_text' => 'Which data structure uses the LIFO principle?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Stack',
            'options' => null
        ],
        [
            'question_text' => 'What is the time complexity of binary search?',
            'question_type' => 'short_answer',
            'correct_answer' => 'O(log n)',
            'options' => null
        ],
        [
            'question_text' => 'What does SQL stand for?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Structured Query Language',
            'options' => null
        ],
        [
            'question_text' => 'Which of the following is not a programming paradigm?',
            'question_type' => 'mcq',
            'correct_answer' => 'Database',
            'options' => json_encode(['Object-Oriented', 'Functional', 'Procedural', 'Database'])
        ],
        [
            'question_text' => 'What is the purpose of an operating system?',
            'question_type' => 'short_answer',
            'correct_answer' => 'To manage hardware and software resources',
            'options' => null
        ],
        [
            'question_text' => 'What does HTTP stand for?',
            'question_type' => 'short_answer',
            'correct_answer' => 'HyperText Transfer Protocol',
            'options' => null
        ]
    ],
    'Foreign Languages' => [
        [
            'question_text' => 'What is the Spanish word for "hello"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Hola',
            'options' => null
        ],
        [
            'question_text' => 'What is the French word for "thank you"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Merci',
            'options' => null
        ],
        [
            'question_text' => 'What is the German word for "goodbye"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Auf Wiedersehen',
            'options' => null
        ],
        [
            'question_text' => 'What is the Italian word for "please"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Per favore',
            'options' => null
        ],
        [
            'question_text' => 'What is the Japanese word for "thank you"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Arigatou',
            'options' => null
        ],
        [
            'question_text' => 'What is the Chinese word for "hello"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Nǐ hǎo',
            'options' => null
        ],
        [
            'question_text' => 'What is the Russian word for "yes"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Da',
            'options' => null
        ],
        [
            'question_text' => 'What is the Portuguese word for "good morning"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Bom dia',
            'options' => null
        ],
        [
            'question_text' => 'What is the Korean word for "thank you"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Gamsahamnida',
            'options' => null
        ],
        [
            'question_text' => 'What is the Arabic word for "welcome"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Marhaba',
            'options' => null
        ]
    ],
    'Arts' => [
        [
            'question_text' => 'Who painted the Sistine Chapel ceiling?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Michelangelo',
            'options' => null
        ],
        [
            'question_text' => 'Which artist cut off his own ear?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Vincent van Gogh',
            'options' => null
        ],
        [
            'question_text' => 'What is the primary color that is not a primary color in light?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Yellow',
            'options' => null
        ],
        [
            'question_text' => 'Who composed the Ninth Symphony?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Ludwig van Beethoven',
            'options' => null
        ],
        [
            'question_text' => 'What is the name of Leonardo da Vinci\'s famous painting of a woman?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Mona Lisa',
            'options' => null
        ],
        [
            'question_text' => 'Which art movement is characterized by dreamlike and fantastical imagery?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Surrealism',
            'options' => null
        ],
        [
            'question_text' => 'Who is known as the "Father of Modern Art"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Paul Cézanne',
            'options' => null
        ],
        [
            'question_text' => 'What is the name of the famous sculpture by Auguste Rodin depicting a man in deep thought?',
            'question_type' => 'short_answer',
            'correct_answer' => 'The Thinker',
            'options' => null
        ],
        [
            'question_text' => 'Which composer wrote "The Four Seasons"?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Antonio Vivaldi',
            'options' => null
        ],
        [
            'question_text' => 'What is the technique of creating images by arranging small colored pieces of glass or stone?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Mosaic',
            'options' => null
        ]
    ],
    'Physical Education' => [
        [
            'question_text' => 'How many players are there in a standard basketball team on the court?',
            'question_type' => 'short_answer',
            'correct_answer' => '5',
            'options' => null
        ],
        [
            'question_text' => 'What is the name of the line that marks the middle of a basketball court?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Half-court line',
            'options' => null
        ],
        [
            'question_text' => 'How many points is a touchdown worth in American football?',
            'question_type' => 'short_answer',
            'correct_answer' => '6',
            'options' => null
        ],
        [
            'question_text' => 'What is the name of the area in soccer where the goalkeeper can use their hands?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Penalty area',
            'options' => null
        ],
        [
            'question_text' => 'How many innings are there in a standard baseball game?',
            'question_type' => 'short_answer',
            'correct_answer' => '9',
            'options' => null
        ],
        [
            'question_text' => 'What is the name of the stroke in swimming where the arms move in a circular motion?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Butterfly',
            'options' => null
        ],
        [
            'question_text' => 'How many players are there in a standard volleyball team on the court?',
            'question_type' => 'short_answer',
            'correct_answer' => '6',
            'options' => null
        ],
        [
            'question_text' => 'What is the name of the area in tennis where the server must stand when serving?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Service box',
            'options' => null
        ],
        [
            'question_text' => 'How many players are there in a standard soccer team on the field?',
            'question_type' => 'short_answer',
            'correct_answer' => '11',
            'options' => null
        ],
        [
            'question_text' => 'What is the name of the area in track and field where athletes throw the discus?',
            'question_type' => 'short_answer',
            'correct_answer' => 'Discus circle',
            'options' => null
        ]
    ]
];

// Create quizzes for each subject and add questions
foreach ($subjects as $subject) {
    echo "Processing subject: " . $subject['name'] . "\n";
    
    // Check if we have sample questions for this subject
    if (!isset($sample_questions[$subject['name']])) {
        echo "No sample questions found for " . $subject['name'] . ". Skipping.\n";
        continue;
    }
    
    // Create a quiz for this subject
    $quiz_title = $subject['name'] . " Quiz";
    $quiz_description = "A quiz about " . $subject['name'] . " concepts.";
    
    // Get a random category
    $result = $conn->query("SELECT id FROM categories ORDER BY RAND() LIMIT 1");
    $category = $result->fetch_assoc();
    $category_id = $category['id'];
    
    // Get a random grade level
    $grade_levels = [
        'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
        'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12',
        'College Year 1', 'College Year 2', 'College Year 3', 'College Year 4',
        'Graduate', 'General'
    ];
    $grade_level = $grade_levels[array_rand($grade_levels)];
    
    // Set a random time limit (10-30 minutes)
    $time_limit = rand(10, 30);
    
    // Insert the quiz
    $stmt = $conn->prepare("INSERT INTO quizzes (title, description, subject_id, category_id, grade_level, time_limit, created_by) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param("ssiisi", $quiz_title, $quiz_description, $subject['id'], $category_id, $grade_level, $time_limit);
    
    if ($stmt->execute()) {
        $quiz_id = $conn->insert_id;
        echo "Created quiz: " . $quiz_title . " (ID: " . $quiz_id . ")\n";
        
        // Add questions to the quiz
        $questions = $sample_questions[$subject['name']];
        foreach ($questions as $question) {
            $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, question_type, correct_answer, options) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $quiz_id, $question['question_text'], $question['question_type'], $question['correct_answer'], $question['options']);
            
            if ($stmt->execute()) {
                echo "  Added question: " . substr($question['question_text'], 0, 30) . "...\n";
            } else {
                echo "  Failed to add question: " . $stmt->error . "\n";
            }
        }
    } else {
        echo "Failed to create quiz: " . $stmt->error . "\n";
    }
}

echo "Sample questions added successfully!\n";
?> 