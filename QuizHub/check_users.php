<?php
// Database configuration
require_once __DIR__ . '/config/db.php';

// Get all users
$sql = "SELECT id, username, email, role FROM users";
$result = $conn->query($sql);

echo "<h2>Users in the Database</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["username"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["role"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No users found</td></tr>";
}

echo "</table>";

// Close the connection
$conn->close();
?> 