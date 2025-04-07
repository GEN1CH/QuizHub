-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2025 at 03:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quizhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `anime_answers`
--

CREATE TABLE `anime_answers` (
  `id` int(11) NOT NULL,
  `attempt_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `user_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anime_answers`
--

INSERT INTO `anime_answers` (`id`, `attempt_id`, `question_id`, `user_answer`, `is_correct`) VALUES
(1, 1, 17, 'gum gum fruit', 0),
(2, 1, 18, 'luffy', 0),
(3, 1, 19, 'santoryu', 1),
(4, 1, 20, 'one piece', 1),
(5, 1, 21, 'sanji', 1),
(6, 1, 22, 'minus tempo', 0),
(7, 1, 23, 'marines', 0),
(8, 1, 24, 'buster call', 0),
(9, 1, 25, 'sabaody archipelago', 0),
(10, 1, 26, 'zeni', 0),
(11, 2, 16, '', 0),
(12, 3, 1, 'uzumaki naruto', 0),
(13, 3, 2, 'rasengan', 0),
(14, 3, 3, 'sasuke', 0),
(15, 4, 4, 'walls', 0),
(16, 4, 5, 'eren yeager', 1),
(17, 4, 6, 'odm gear', 0),
(18, 5, 7, 'son goku', 0),
(19, 5, 8, 'kamehameha', 1),
(20, 5, 9, 'shenron', 1),
(21, 6, 13, 'light yagami', 1),
(22, 6, 14, 'L', 1),
(23, 6, 15, 'dies', 0),
(24, 7, 10, 'midoriya izuku', 0),
(25, 7, 11, 'one for all', 1),
(26, 7, 12, 'my hero academia', 0);

-- --------------------------------------------------------

--
-- Table structure for table `anime_questions`
--

CREATE TABLE `anime_questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('mcq','short_answer','image_question') NOT NULL,
  `correct_answer` text NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anime_questions`
--

INSERT INTO `anime_questions` (`id`, `quiz_id`, `question_text`, `question_type`, `correct_answer`, `options`, `image_url`) VALUES
(1, 1, 'Who is the main protagonist of the Naruto series?', '', 'Naruto Uzumaki', NULL, NULL),
(2, 1, 'What is the name of the technique that Naruto is most famous for?', '', 'Shadow Clone Jutsu', NULL, NULL),
(3, 1, 'Who is Naruto\'s rival throughout the series?', '', 'Sasuke Uchiha', NULL, NULL),
(4, 2, 'What are the three walls that protect humanity called?', '', 'Maria, Rose, and Sina', NULL, NULL),
(5, 2, 'What is the name of the main protagonist?', '', 'Eren Yeager', NULL, NULL),
(6, 2, 'What is the name of the device used by soldiers to navigate through the air?', '', 'Vertical Maneuvering Equipment', NULL, NULL),
(7, 3, 'What is the name of the main protagonist in Dragon Ball?', '', 'Goku', NULL, NULL),
(8, 3, 'What is the name of Goku\'s signature attack?', '', 'Kamehameha', NULL, NULL),
(9, 3, 'What is the name of the dragon that grants wishes when all seven Dragon Balls are collected?', '', 'Shenron', NULL, NULL),
(10, 4, 'What is the name of the main protagonist?', '', 'Izuku Midoriya', NULL, NULL),
(11, 4, 'What is the name of the quirk that Izuku inherits from All Might?', '', 'One For All', NULL, NULL),
(12, 4, 'What is the name of the school that Izuku attends?', '', 'U.A. High School', NULL, NULL),
(13, 5, 'What is the name of the main protagonist who finds the Death Note?', '', 'Light Yagami', NULL, NULL),
(14, 5, 'What is the name of the detective who tries to catch Light?', '', 'L', NULL, NULL),
(15, 5, 'What happens to a person whose name is written in the Death Note?', '', 'They die of a heart attack', NULL, NULL),
(16, 6, '123', 'mcq', '123', '[\"\",\"\",\"\",\"\"]', ''),
(17, 7, 'What is the name of Luffy\'s signature Devil Fruit?', '', 'Gomu Gomu no Mi', NULL, NULL),
(18, 7, 'Who is the captain of the Straw Hat Pirates?', '', 'Monkey D. Luffy', NULL, NULL),
(19, 7, 'What is the name of Zoro\'s signature three-sword style technique?', '', 'Santoryu', NULL, NULL),
(20, 7, 'What is the name of the legendary treasure that all pirates are searching for?', '', 'One Piece', NULL, NULL),
(21, 7, 'Who is the cook of the Straw Hat Pirates?', '', 'Sanji', NULL, NULL),
(22, 7, 'What is the name of Nami\'s weapon that can control weather?', '', 'Clima-Tact', NULL, NULL),
(23, 7, 'What is the name of the organization that governs the world in One Piece?', '', 'World Government', NULL, NULL),
(24, 7, 'What is the name of the strongest military force in the One Piece world?', '', 'Marines', NULL, NULL),
(25, 7, 'What is the name of the island where the Straw Hat crew first entered the Grand Line?', '', 'Reverse Mountain', NULL, NULL),
(26, 7, 'What is the name of the currency used in the One Piece world?', '', 'Beli', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `anime_quizzes`
--

CREATE TABLE `anime_quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `anime_series` varchar(255) NOT NULL,
  `difficulty` enum('beginner','intermediate','advanced') NOT NULL,
  `time_limit` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anime_quizzes`
--

INSERT INTO `anime_quizzes` (`id`, `title`, `description`, `anime_series`, `difficulty`, `time_limit`, `created_by`, `created_at`) VALUES
(1, 'Naruto: The Hidden Leaf Village', 'Test your knowledge about the characters, techniques, and lore of the Naruto series.', 'Naruto', 'intermediate', 30, 1, '2025-04-07 09:12:39'),
(2, 'Attack on Titan: The Walls of Humanity', 'How well do you know the world of Attack on Titan? Test your knowledge about the titans, the walls, and the characters.', 'Attack on Titan', 'advanced', 45, 1, '2025-04-07 09:12:39'),
(3, 'Dragon Ball: The Saiyan Saga', 'Test your knowledge about the Dragon Ball series, focusing on the Saiyan saga and beyond.', 'Dragon Ball', 'beginner', 20, 1, '2025-04-07 09:12:39'),
(4, 'My Hero Academia: The World of Heroes', 'How well do you know the world of My Hero Academia? Test your knowledge about quirks, heroes, and villains.', 'My Hero Academia', 'intermediate', 25, 1, '2025-04-07 09:12:39'),
(5, 'Death Note: The Battle of Wits', 'Test your knowledge about the psychological thriller Death Note and the battle between Light and L.', 'Death Note', 'advanced', 30, 1, '2025-04-07 09:12:39'),
(6, '123', '123', '123', 'beginner', 30, 4, '2025-04-07 09:14:17'),
(7, 'One Piece: Journey to the Grand Line', 'Test your knowledge about the world of One Piece, its characters, Devil Fruits, and epic battles!', 'One Piece', 'intermediate', 30, 1, '2025-04-07 09:16:55');

-- --------------------------------------------------------

--
-- Table structure for table `anime_quiz_attempts`
--

CREATE TABLE `anime_quiz_attempts` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `retake_allowed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anime_quiz_attempts`
--

INSERT INTO `anime_quiz_attempts` (`id`, `quiz_id`, `user_id`, `score`, `started_at`, `completed_at`, `retake_allowed`) VALUES
(1, 7, 5, 30, '2025-04-07 09:38:52', '2025-04-07 09:38:52', 0),
(2, 6, 5, 0, '2025-04-07 09:42:42', '2025-04-07 09:42:42', 0),
(3, 1, 5, 0, '2025-04-07 09:43:09', '2025-04-07 09:43:09', 0),
(4, 2, 5, 33.3333, '2025-04-07 09:43:47', '2025-04-07 09:43:47', 0),
(5, 3, 5, 66.6667, '2025-04-07 09:44:18', '2025-04-07 09:44:18', 0),
(6, 5, 5, 66.6667, '2025-04-07 09:44:47', '2025-04-07 09:44:47', 0),
(7, 4, 5, 33.3333, '2025-04-07 09:45:33', '2025-04-07 09:45:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `attempt_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `user_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `attempt_id`, `question_id`, `user_answer`, `is_correct`) VALUES
(1, 1, 1, 'John Steinbeck', 1),
(2, 2, 12, 'hypertext markup language', 1),
(3, 2, 13, 'Python', 1),
(4, 2, 14, 'i dont know', 0),
(5, 2, 15, 'central processing unit', 1),
(6, 2, 16, 'stack', 1),
(7, 2, 17, 'O(logn)', 0),
(8, 2, 18, 'standard query language', 0),
(9, 2, 19, 'Database', 1),
(10, 2, 20, 'for branding', 0),
(11, 2, 21, 'hyper text transfer protocol', 0),
(12, 3, 2, 'donatello', 0),
(13, 3, 3, 'steven van gogh', 0),
(14, 3, 4, 'blue', 0),
(15, 3, 5, 'beethoven', 0),
(16, 3, 6, 'mona lisa', 1),
(17, 3, 7, 'abstract', 0),
(18, 3, 8, 'leonardo da vinci', 0),
(19, 3, 9, 'the thinker', 1),
(20, 3, 10, 'vivaldi', 0),
(21, 3, 11, 'mosaic', 1),
(22, 4, 72, 'au', 1),
(23, 4, 73, 'jupiter', 1),
(24, 4, 74, 'photosynthesis', 1),
(25, 4, 75, 'c', 0),
(26, 4, 76, '1000000m', 0),
(27, 4, 77, 'dust', 0),
(28, 4, 78, 'h2o', 1),
(29, 4, 79, 'i dont know', 0),
(30, 4, 80, 'dissentigration', 0),
(31, 4, 81, 'gravity', 1),
(32, 5, 52, '12', 1),
(33, 5, 53, '5', 0),
(34, 5, 54, '5', 1),
(35, 5, 55, '?', 0),
(36, 5, 56, 'x', 0),
(37, 5, 57, '?', 0),
(38, 5, 58, '17%', 0),
(39, 5, 59, '18', 0),
(40, 5, 60, '?', 0),
(41, 5, 61, '?', 0),
(42, 6, 62, '10', 0),
(43, 6, 63, '?', 0),
(44, 6, 64, '5', 0),
(45, 6, 65, 'goal', 0),
(46, 6, 66, '3', 0),
(47, 6, 67, 'butterfly', 1),
(48, 6, 68, '10', 0),
(49, 6, 69, 'Serve', 0),
(50, 6, 70, '16', 0),
(51, 6, 71, 'discuss throw', 0),
(52, 7, 32, 'hola', 1),
(53, 7, 33, 'merci', 1),
(54, 7, 34, 'heil', 0),
(55, 7, 35, '?', 0),
(56, 7, 36, 'arigatou', 1),
(57, 7, 37, '?', 0),
(58, 7, 38, 'da', 1),
(59, 7, 39, '?', 0),
(60, 7, 40, 'kamsamida', 0),
(61, 7, 41, 'salam alaykum', 0),
(62, 8, 22, 'Joyful', 1),
(63, 8, 23, 'adjective', 0),
(64, 8, 24, 'The cat and the dog are playing in the yard.', 1),
(65, 8, 25, 'gone', 0),
(66, 8, 26, 'London', 1),
(67, 8, 27, 'children', 1),
(68, 8, 28, '\'', 0),
(69, 8, 29, 'cold', 1),
(70, 8, 30, 'And', 1),
(71, 8, 31, 'better', 1),
(72, 9, 42, '1947', 0),
(73, 9, 43, 'george washington', 1),
(74, 9, 44, 'egyptians', 1),
(75, 9, 45, '1986', 0),
(76, 9, 46, 'leonardo da vinci', 1),
(77, 9, 47, 'rome', 0),
(78, 9, 48, '1765', 0),
(79, 9, 49, 'amelia heart', 0),
(80, 9, 50, 'civil war', 1),
(81, 9, 51, 'neil armstrong', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'First Year', 'Courses for first-year students', '2025-04-06 13:28:09'),
(2, 'Second Year', 'Courses for second-year students', '2025-04-06 13:28:09'),
(3, 'Third Year', 'Courses for third-year students', '2025-04-06 13:28:09'),
(4, 'Fourth Year', 'Courses for fourth-year students', '2025-04-06 13:28:09'),
(5, 'Graduate', 'Graduate-level courses', '2025-04-06 13:28:09'),
(6, 'General', 'General courses applicable to all levels', '2025-04-06 13:28:09');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('mcq','short_answer') NOT NULL,
  `correct_answer` text NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `question_type`, `correct_answer`, `options`) VALUES
(1, 2, 'He is the author of the novel \"Of Mice and Men\", which explores themes of friendship, dreams, and loneliness during the Great Depression. Who is he?', 'short_answer', 'John Steinbeck', NULL),
(2, 3, 'Who painted the Sistine Chapel ceiling?', 'short_answer', 'Michelangelo', NULL),
(3, 3, 'Which artist cut off his own ear?', 'short_answer', 'Vincent van Gogh', NULL),
(4, 3, 'What is the primary color that is not a primary color in light?', 'short_answer', 'Yellow', NULL),
(5, 3, 'Who composed the Ninth Symphony?', 'short_answer', 'Ludwig van Beethoven', NULL),
(6, 3, 'What is the name of Leonardo da Vinci\'s famous painting of a woman?', 'short_answer', 'Mona Lisa', NULL),
(7, 3, 'Which art movement is characterized by dreamlike and fantastical imagery?', 'short_answer', 'Surrealism', NULL),
(8, 3, 'Who is known as the \"Father of Modern Art\"?', 'short_answer', 'Paul Cézanne', NULL),
(9, 3, 'What is the name of the famous sculpture by Auguste Rodin depicting a man in deep thought?', 'short_answer', 'The Thinker', NULL),
(10, 3, 'Which composer wrote \"The Four Seasons\"?', 'short_answer', 'Antonio Vivaldi', NULL),
(11, 3, 'What is the technique of creating images by arranging small colored pieces of glass or stone?', 'short_answer', 'Mosaic', NULL),
(12, 4, 'What does HTML stand for?', 'short_answer', 'HyperText Markup Language', NULL),
(13, 4, 'Which of the following is a programming language?', 'mcq', 'Python', '[\"Python\",\"Excel\",\"Windows\",\"Linux\"]'),
(14, 4, 'What is the binary representation of the decimal number 10?', 'short_answer', '1010', NULL),
(15, 4, 'What does CPU stand for?', 'short_answer', 'Central Processing Unit', NULL),
(16, 4, 'Which data structure uses the LIFO principle?', 'short_answer', 'Stack', NULL),
(17, 4, 'What is the time complexity of binary search?', 'short_answer', 'O(log n)', NULL),
(18, 4, 'What does SQL stand for?', 'short_answer', 'Structured Query Language', NULL),
(19, 4, 'Which of the following is not a programming paradigm?', 'mcq', 'Database', '[\"Object-Oriented\",\"Functional\",\"Procedural\",\"Database\"]'),
(20, 4, 'What is the purpose of an operating system?', 'short_answer', 'To manage hardware and software resources', NULL),
(21, 4, 'What does HTTP stand for?', 'short_answer', 'HyperText Transfer Protocol', NULL),
(22, 5, 'Which of the following is a synonym for \"happy\"?', 'mcq', 'Joyful', '[\"Sad\",\"Joyful\",\"Angry\",\"Tired\"]'),
(23, 5, 'Identify the part of speech for the word \"quickly\" in the sentence: \"She quickly ran to the store.\"', 'short_answer', 'Adverb', NULL),
(24, 5, 'Which of the following sentences is grammatically correct?', 'mcq', 'The cat and the dog are playing in the yard.', '[\"The cat and the dog is playing in the yard.\",\"The cat and the dog are playing in the yard.\",\"The cat and the dog was playing in the yard.\",\"The cat and the dog were playing in the yard.\"]'),
(25, 5, 'What is the past tense of the verb \"go\"?', 'short_answer', 'Went', NULL),
(26, 5, 'Which of the following is a proper noun?', 'mcq', 'London', '[\"City\",\"London\",\"Book\",\"Tree\"]'),
(27, 5, 'What is the plural form of \"child\"?', 'short_answer', 'Children', NULL),
(28, 5, 'Which punctuation mark is used to indicate possession?', 'short_answer', 'Apostrophe', NULL),
(29, 5, 'What is the opposite of \"hot\"?', 'short_answer', 'Cold', NULL),
(30, 5, 'Which of the following is a conjunction?', 'mcq', 'And', '[\"And\",\"Happy\",\"Run\",\"Book\"]'),
(31, 5, 'What is the comparative form of \"good\"?', 'short_answer', 'Better', NULL),
(32, 6, 'What is the Spanish word for \"hello\"?', 'short_answer', 'Hola', NULL),
(33, 6, 'What is the French word for \"thank you\"?', 'short_answer', 'Merci', NULL),
(34, 6, 'What is the German word for \"goodbye\"?', 'short_answer', 'Auf Wiedersehen', NULL),
(35, 6, 'What is the Italian word for \"please\"?', 'short_answer', 'Per favore', NULL),
(36, 6, 'What is the Japanese word for \"thank you\"?', 'short_answer', 'Arigatou', NULL),
(37, 6, 'What is the Chinese word for \"hello\"?', 'short_answer', 'Nǐ hǎo', NULL),
(38, 6, 'What is the Russian word for \"yes\"?', 'short_answer', 'Da', NULL),
(39, 6, 'What is the Portuguese word for \"good morning\"?', 'short_answer', 'Bom dia', NULL),
(40, 6, 'What is the Korean word for \"thank you\"?', 'short_answer', 'Gamsahamnida', NULL),
(41, 6, 'What is the Arabic word for \"welcome\"?', 'short_answer', 'Marhaba', NULL),
(42, 7, 'In which year did World War II end?', 'short_answer', '1945', NULL),
(43, 7, 'Who was the first President of the United States?', 'short_answer', 'George Washington', NULL),
(44, 7, 'Which ancient civilization built the pyramids?', 'short_answer', 'Egyptians', NULL),
(45, 7, 'In which year did the Titanic sink?', 'short_answer', '1912', NULL),
(46, 7, 'Who painted the Mona Lisa?', 'short_answer', 'Leonardo da Vinci', NULL),
(47, 7, 'Which empire was ruled by Emperor Augustus?', 'short_answer', 'Roman Empire', NULL),
(48, 7, 'In which year did Christopher Columbus reach the Americas?', 'short_answer', '1492', NULL),
(49, 7, 'Who was the first woman to fly solo across the Atlantic Ocean?', 'short_answer', 'Amelia Earhart', NULL),
(50, 7, 'Which war was fought between the North and South regions of the United States?', 'short_answer', 'Civil War', NULL),
(51, 7, 'Who was the first human to walk on the moon?', 'short_answer', 'Neil Armstrong', NULL),
(52, 8, 'What is the square root of 144?', 'short_answer', '12', NULL),
(53, 8, 'What is the area of a circle with radius 5 units?', 'short_answer', '78.54', NULL),
(54, 8, 'Solve for x: 2x + 5 = 15', 'short_answer', '5', NULL),
(55, 8, 'What is the slope of the line passing through points (2,3) and (4,7)?', 'short_answer', '2', NULL),
(56, 8, 'What is the derivative of f(x) = x²?', 'short_answer', '2x', NULL),
(57, 8, 'What is the value of sin(90°)?', 'short_answer', '1', NULL),
(58, 8, 'What is the probability of rolling a 6 on a fair six-sided die?', 'short_answer', '1/6', NULL),
(59, 8, 'What is the volume of a cube with side length 3 units?', 'short_answer', '27', NULL),
(60, 8, 'What is the sum of the first 10 positive integers?', 'short_answer', '55', NULL),
(61, 8, 'What is the value of log₁₀(100)?', 'short_answer', '2', NULL),
(62, 9, 'How many players are there in a standard basketball team on the court?', 'short_answer', '5', NULL),
(63, 9, 'What is the name of the line that marks the middle of a basketball court?', 'short_answer', 'Half-court line', NULL),
(64, 9, 'How many points is a touchdown worth in American football?', 'short_answer', '6', NULL),
(65, 9, 'What is the name of the area in soccer where the goalkeeper can use their hands?', 'short_answer', 'Penalty area', NULL),
(66, 9, 'How many innings are there in a standard baseball game?', 'short_answer', '9', NULL),
(67, 9, 'What is the name of the stroke in swimming where the arms move in a circular motion?', 'short_answer', 'Butterfly', NULL),
(68, 9, 'How many players are there in a standard volleyball team on the court?', 'short_answer', '6', NULL),
(69, 9, 'What is the name of the area in tennis where the server must stand when serving?', 'short_answer', 'Service box', NULL),
(70, 9, 'How many players are there in a standard soccer team on the field?', 'short_answer', '11', NULL),
(71, 9, 'What is the name of the area in track and field where athletes throw the discus?', 'short_answer', 'Discus circle', NULL),
(72, 10, 'What is the chemical symbol for gold?', 'short_answer', 'Au', NULL),
(73, 10, 'What is the largest planet in our solar system?', 'short_answer', 'Jupiter', NULL),
(74, 10, 'What is the process by which plants convert light energy into chemical energy?', 'short_answer', 'Photosynthesis', NULL),
(75, 10, 'What is the atomic number of carbon?', 'short_answer', '6', NULL),
(76, 10, 'What is the speed of light in meters per second?', 'short_answer', '299792458', NULL),
(77, 10, 'What is the main component of the Sun?', 'short_answer', 'Hydrogen', NULL),
(78, 10, 'What is the chemical formula for water?', 'short_answer', 'H2O', NULL),
(79, 10, 'What is the unit of force in the SI system?', 'short_answer', 'Newton', NULL),
(80, 10, 'What is the process by which a solid changes directly into a gas?', 'short_answer', 'Sublimation', NULL),
(81, 10, 'What is the name of the force that pulls objects toward the center of the Earth?', 'short_answer', 'Gravity', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `time_limit` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `description`, `subject_id`, `category_id`, `grade_level`, `time_limit`, `created_by`, `created_at`) VALUES
(2, 'Understanding Literary Elements in Fiction', 'This quiz assesses students\' knowledge of basic literary elements such as setting, character, plot, theme, and tone through multiple-choice, identification, and short-answer questions based on classic short stories and novels.', 3, 2, 'Grade 8', 0, 1, '2025-04-06 13:46:45'),
(3, 'Arts Quiz', 'A quiz about Arts concepts.', 7, 3, 'College Year 2', 25, 1, '2025-04-06 14:35:46'),
(4, 'Computer Science Quiz', 'A quiz about Computer Science concepts.', 5, 2, 'College Year 1', 29, 1, '2025-04-06 14:35:47'),
(5, 'English Quiz', 'A quiz about English concepts.', 3, 3, 'College Year 1', 28, 1, '2025-04-06 14:35:47'),
(6, 'Foreign Languages Quiz', 'A quiz about Foreign Languages concepts.', 6, 6, 'College Year 1', 14, 1, '2025-04-06 14:35:47'),
(7, 'History Quiz', 'A quiz about History concepts.', 4, 4, 'Grade 4', 19, 1, '2025-04-06 14:35:47'),
(8, 'Mathematics Quiz', 'A quiz about Mathematics concepts.', 1, 6, 'Grade 11', 18, 1, '2025-04-06 14:35:47'),
(9, 'Physical Education Quiz', 'A quiz about Physical Education concepts.', 8, 1, 'College Year 4', 21, 1, '2025-04-06 14:35:47'),
(10, 'Science Quiz', 'A quiz about Science concepts.', 2, 3, 'Grade 7', 15, 1, '2025-04-06 14:35:47');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempts`
--

CREATE TABLE `quiz_attempts` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `score` float DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `retake_allowed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_attempts`
--

INSERT INTO `quiz_attempts` (`id`, `quiz_id`, `user_id`, `score`, `started_at`, `completed_at`, `retake_allowed`) VALUES
(1, 2, 3, 100, '2025-04-06 13:51:08', '2025-04-06 13:51:08', 0),
(2, 4, 3, 50, '2025-04-06 14:39:22', '2025-04-06 14:39:22', 0),
(3, 3, 3, 30, '2025-04-06 14:43:44', '2025-04-06 14:43:44', 0),
(4, 10, 3, 50, '2025-04-06 14:46:37', '2025-04-06 14:46:37', 0),
(5, 8, 3, 20, '2025-04-06 14:49:24', '2025-04-06 14:49:24', 0),
(6, 9, 3, 10, '2025-04-06 14:51:35', '2025-04-06 14:51:35', 0),
(7, 6, 3, 40, '2025-04-06 14:53:10', '2025-04-06 14:53:10', 0),
(8, 5, 3, 70, '2025-04-06 14:56:07', '2025-04-06 14:56:07', 0),
(9, 7, 3, 50, '2025-04-06 14:58:17', '2025-04-06 14:58:17', 0);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`, `category_id`, `created_at`) VALUES
(1, 'Mathematics', 'Math courses including algebra, calculus, geometry, etc.', NULL, '2025-04-06 13:26:23'),
(2, 'Science', 'Science courses including physics, chemistry, biology, etc.', NULL, '2025-04-06 13:26:23'),
(3, 'English', 'English language and literature courses', NULL, '2025-04-06 13:26:23'),
(4, 'History', 'History and social studies courses', NULL, '2025-04-06 13:26:23'),
(5, 'Computer Science', 'Programming, algorithms, and computer systems', NULL, '2025-04-06 13:26:23'),
(6, 'Foreign Languages', 'Languages other than English', NULL, '2025-04-06 13:26:23'),
(7, 'Arts', 'Visual arts, music, and performing arts', NULL, '2025-04-06 13:26:23'),
(8, 'Physical Education', 'Sports, fitness, and health education', NULL, '2025-04-06 13:26:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','teacher','student','anime_guru','anime_student') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'Genich', '$2y$10$6ouej/6cak46T1XrdafMH.JphxP0JOtEABhAScMvOUiArMfTYRPQi', 'gedemorales5@gmail.com', 'teacher', '2025-04-06 13:17:57'),
(2, 'admin', '$2y$10$4dTfKC5PDgU7.gopDeKkl.3fiNTK/TZtQyeRxSmMOC8KTA93flCOW', 'admin@quizhub.com', 'admin', '2025-04-06 13:32:53'),
(3, 'luther', '$2y$10$6TKcWEIh2ZMFaUQ4lqY/ke8Q0Iy2wZ6Amvuv6Idhi7aQ49UpA.e3u', 'l@gmail.com', 'student', '2025-04-06 13:47:59'),
(4, 'sage', '$2y$10$2Nu0I/MPwS7Ce9WNHw3nfO9gljKUYDl89rfI/CsYyH5FvIcbQv4HW', 'gede@gmail.com', 'teacher', '2025-04-06 15:07:25'),
(5, 'gen', '$2y$10$e1MSi6vuQUmxF9XJcokKsu9JF6TIQ9aDQVKrBQRSKlQRugAmA7uYa', 'gedenicolas360@gmail.com', 'anime_student', '2025-04-07 09:26:27'),
(6, 'kage', '$2y$10$GxplApFiK/.COCasTxi1zu0lxT7mRitZL4tVlKyotQqlMe0WAt9KW', '123@gmail.com', 'anime_guru', '2025-04-07 12:58:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anime_answers`
--
ALTER TABLE `anime_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `anime_questions`
--
ALTER TABLE `anime_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `anime_quizzes`
--
ALTER TABLE `anime_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `anime_quiz_attempts`
--
ALTER TABLE `anime_quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attempt_id` (`attempt_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anime_answers`
--
ALTER TABLE `anime_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `anime_questions`
--
ALTER TABLE `anime_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `anime_quizzes`
--
ALTER TABLE `anime_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `anime_quiz_attempts`
--
ALTER TABLE `anime_quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anime_answers`
--
ALTER TABLE `anime_answers`
  ADD CONSTRAINT `anime_answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `anime_quiz_attempts` (`id`),
  ADD CONSTRAINT `anime_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `anime_questions` (`id`);

--
-- Constraints for table `anime_questions`
--
ALTER TABLE `anime_questions`
  ADD CONSTRAINT `anime_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `anime_quizzes` (`id`);

--
-- Constraints for table `anime_quizzes`
--
ALTER TABLE `anime_quizzes`
  ADD CONSTRAINT `anime_quizzes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `anime_quiz_attempts`
--
ALTER TABLE `anime_quiz_attempts`
  ADD CONSTRAINT `anime_quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `anime_quizzes` (`id`),
  ADD CONSTRAINT `anime_quiz_attempts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`),
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `quizzes_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `quizzes_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `quiz_attempts`
--
ALTER TABLE `quiz_attempts`
  ADD CONSTRAINT `quiz_attempts_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  ADD CONSTRAINT `quiz_attempts_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
