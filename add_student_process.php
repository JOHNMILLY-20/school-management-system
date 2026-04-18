<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$successMessage = ""; // Initialize success message variable

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name']; // Student name
    $parent_id = $_POST['parent_id']; // Parent ID

    // Insert the new student into the users table
    $sql = "INSERT INTO users (username, password, role) VALUES (?, 'default_password', 'student')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        $child_id = $stmt->insert_id; // Get newly created student's ID
        $stmt->close();

        // Link the student with the parent in the parent-child relationship table
        $link_sql = "INSERT INTO parent_child_relationship (parent_id, child_id) VALUES (?, ?)";
        $stmt = $conn->prepare($link_sql);
        $stmt->bind_param("ii", $parent_id, $child_id);
        
        if ($stmt->execute()) {
            // Set success message
            $successMessage = "New student added successfully.";
        } else {
            $successMessage = "Error linking student to parent: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $successMessage = "Error adding student: " . $stmt->error;
    }
}

// Optional: Include a standard header and navigation here if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Student</title>
</head>
<body>
    <h1>Add New Student</h1>
    <form action="" method="POST"> <!-- Use the current script for form action -->
        <label for="name">Student Name:</label>
        <input type="text" name="name" required>
        <br>
        <label for="parent_id">Select Parent:</label>
        <select name="parent_id" required>
            <option value="">Select Parent</option>
            <?php foreach ($parents as $parent): ?>
                <option value="<?= htmlspecialchars($parent['id']); ?>"><?= htmlspecialchars($parent['username']); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" value="Add Student">
    </form>
    
    <?php if (!empty($successMessage)): ?>
        <p style="color: green;"><?= htmlspecialchars($successMessage); ?></p>
    <?php endif; ?>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>