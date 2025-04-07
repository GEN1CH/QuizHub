<?php
$page_title = "Admin Dashboard";
require_once __DIR__ . '/../includes/header.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle subject management
if (isset($_POST['action']) && $_POST['action'] === 'add_subject') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = isset($_POST['category_id']) && is_numeric($_POST['category_id']) ? intval($_POST['category_id']) : null;
    
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO subjects (name, description, category_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $description, $category_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add subject. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Subject name is required.";
    }
    
    header("Location: admin.php");
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'edit_subject') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = isset($_POST['category_id']) && is_numeric($_POST['category_id']) ? intval($_POST['category_id']) : null;
    
    if (!empty($name) && $id > 0) {
        $stmt = $conn->prepare("UPDATE subjects SET name = ?, description = ?, category_id = ? WHERE id = ?");
        $stmt->bind_param("ssii", $name, $description, $category_id, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update subject. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Subject name is required.";
    }
    
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_subject']) && is_numeric($_GET['delete_subject'])) {
    $id = intval($_GET['delete_subject']);
    
    // Check if any quizzes use this subject
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM quizzes WHERE subject_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['error'] = "Cannot delete subject because it is used by quizzes.";
    } else {
        $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Subject deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete subject. Please try again.";
        }
    }
    
    header("Location: admin.php");
    exit();
}

// Get all users
$stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->get_result();

// Get all categories
$categories = [];
$result = $conn->query("SELECT * FROM categories ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Get all subjects with their categories
$subjects = [];
$result = $conn->query("
    SELECT s.*, c.name as category_name 
    FROM subjects s 
    LEFT JOIN categories c ON s.category_id = c.id 
    ORDER BY c.name, s.name
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}
?>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="../index.php">QuizHub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:history.back()">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-danger" href="../auth/logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Subject Management</h3>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo $_SESSION['success']; 
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php 
                        echo $_SESSION['error']; 
                        unset($_SESSION['error']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Add New Subject</h5>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="add_subject">
                            <div class="mb-3">
                                <label for="subject_name" class="form-label">Subject Name</label>
                                <input type="text" class="form-control" id="subject_name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject_description" class="form-label">Description</label>
                                <textarea class="form-control" id="subject_description" name="description" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select a category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Subject</button>
                        </form>
                    </div>
                </div>

                <h5>Existing Subjects</h5>
                <?php if (count($subjects) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subjects as $subject): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($subject['name']); ?></td>
                                        <td><?php echo htmlspecialchars($subject['description']); ?></td>
                                        <td><?php echo htmlspecialchars($subject['category_name'] ?? 'Uncategorized'); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($subject['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editSubjectModal<?php echo $subject['id']; ?>">
                                                    Edit
                                                </button>
                                                <a href="?delete_subject=<?php echo $subject['id']; ?>" 
                                                   class="btn btn-outline-danger"
                                                   onclick="return confirm('Are you sure you want to delete this subject?')">
                                                    Delete
                                                </a>
                                            </div>
                                            
                                            <!-- Edit Subject Modal -->
                                            <div class="modal fade" id="editSubjectModal<?php echo $subject['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Subject</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form method="POST" action="">
                                                            <div class="modal-body">
                                                                <input type="hidden" name="action" value="edit_subject">
                                                                <input type="hidden" name="id" value="<?php echo $subject['id']; ?>">
                                                                
                                                                <div class="mb-3">
                                                                    <label for="edit_subject_name<?php echo $subject['id']; ?>" class="form-label">Subject Name</label>
                                                                    <input type="text" class="form-control" id="edit_subject_name<?php echo $subject['id']; ?>" 
                                                                           name="name" value="<?php echo htmlspecialchars($subject['name']); ?>" required>
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="edit_subject_description<?php echo $subject['id']; ?>" class="form-label">Description</label>
                                                                    <textarea class="form-control" id="edit_subject_description<?php echo $subject['id']; ?>" 
                                                                              name="description" rows="2"><?php echo htmlspecialchars($subject['description']); ?></textarea>
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="edit_category_id<?php echo $subject['id']; ?>" class="form-label">Category</label>
                                                                    <select class="form-select" id="edit_category_id<?php echo $subject['id']; ?>" name="category_id">
                                                                        <option value="">Select a category</option>
                                                                        <?php foreach ($categories as $category): ?>
                                                                            <option value="<?php echo $category['id']; ?>" 
                                                                                <?php echo $subject['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                                                                <?php echo htmlspecialchars($category['name']); ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No subjects found. Add your first subject above.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">User Management</h3>
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="card-body">
                <?php if ($users->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $user['role'] === 'admin' ? 'danger' : 
                                                    ($user['role'] === 'teacher' ? 'primary' : 'success'); 
                                            ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="../auth/edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-outline-primary">Edit</a>
                                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                    <a href="../auth/delete_user.php?id=<?php echo $user['id']; ?>" 
                                                       class="btn btn-outline-danger"
                                                       onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No users found.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?> 