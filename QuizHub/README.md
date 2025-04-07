# QuizHub - Quiz Management System

QuizHub is a comprehensive quiz management system that allows teachers to create, manage, and analyze quizzes, while students can take quizzes and track their progress. The system includes both standard educational quizzes and anime-specific quizzes (which can be removed for professional use).

## Features

### Core Features
- User authentication (login, register, logout)
- Role-based access control (admin, teacher, student)
- Quiz creation and management
- Quiz taking and scoring
- Results viewing and analysis
- Responsive design with Bootstrap
- Secure database operations

### Anime-specific Features (Optional)
- Anime quiz creation and management
- Anime quiz taking interface
- Death Note themed UI for anime section
- Anime guru and anime student roles

## Installation

1. Clone the repository to your web server directory
2. Import the database schema from `database/quizhub.sql`
3. Configure database connection in `config/db.php`
4. Access the application through your web browser

## Database Configuration

Edit `config/db.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'quizhub');
```

## User Roles

1. **Admin**
   - Manage users
   - View all quizzes and results
   - System configuration

2. **Teacher**
   - Create and manage quizzes
   - View student results
   - Grant quiz retake permissions

3. **Student**
   - Take quizzes
   - View personal results
   - Track progress

## Removing Anime-specific Features

To remove the anime-specific features for professional use, follow these steps:

1. **Remove Anime-specific Files**
   Delete the following files and directories:
   - `/anime/` directory and all its contents
   - `/dashboard/anime_guru.php`
   - `/dashboard/anime_student.php`

2. **Modify Database Schema**
   Run the following SQL queries to remove anime-specific tables:
   ```sql
   DROP TABLE IF EXISTS anime_quiz_attempts;
   DROP TABLE IF EXISTS anime_questions;
   DROP TABLE IF EXISTS anime_quizzes;
   ```

3. **Update User Roles**
   Modify the users table to remove anime-specific roles:
   ```sql
   ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'teacher', 'student') NOT NULL;
   ```

4. **Update Navigation**
   Edit `includes/header.php` and remove the anime-specific navigation items:
   ```php
   // Remove these conditions from the navigation
   <?php elseif($_SESSION['role'] == 'anime_guru'): ?>
   <?php elseif($_SESSION['role'] == 'anime_student'): ?>
   ```

5. **Update Login Redirects**
   Edit `auth/login.php` and remove anime-specific role cases:
   ```php
   // Remove these cases from the switch statement
   case 'anime_guru':
   case 'anime_student':
   ```

## File Structure

```
QuizHub/
├── anime/                  # Anime-specific features (can be removed)
├── auth/                   # Authentication files
├── config/                 # Configuration files
├── dashboard/              # User dashboards
├── includes/               # Common includes
├── quizzes/                # Quiz management files
└── index.php              # Main entry point
```

## Security Features

- Password hashing using PHP's password_hash()
- Prepared statements for all database queries
- Session management with proper security measures
- Input validation and sanitization
- Role-based access control

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please open an issue in the GitHub repository or contact the development team. 