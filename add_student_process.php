<?php
session_start();
include 'db.php'; // Include database connection

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$successMessage = ""; // Initialize success message variable
$errorMessage = ""; // Initialize error message variable

// Fetch available parents from database
$parents = [];
$parent_sql = "SELECT id, username FROM users WHERE role = 'parent'";
$parent_stmt = $conn->prepare($parent_sql);
$parent_stmt->execute();
$parent_result = $parent_stmt->get_result();

if ($parent_result->num_rows > 0) {
    while ($row = $parent_result->fetch_assoc()) {
        $parents[] = $row;
    }
}
$parent_stmt->close();

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); // Student name
    $parent_id = (int)$_POST['parent_id']; // Parent ID
    $class_id = isset($_POST['class_id']) ? (int)$_POST['class_id'] : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

    // Validate inputs
    if (empty($name)) {
        $errorMessage = "Student name is required.";
    } elseif (empty($parent_id)) {
        $errorMessage = "Parent selection is required.";
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert the new student into the users table first
            $user_sql = "INSERT INTO users (username, password, role) VALUES (?, 'default_password', 'student')";
            $stmt = $conn->prepare($user_sql);
            $stmt->bind_param("s", $name);
            
            if (!$stmt->execute()) {
                throw new Exception("Error creating user account: " . $stmt->error);
            }
            
            $user_id = $stmt->insert_id; // Get newly created user's ID
            $stmt->close();

            // Insert the student into the students table
            $student_sql = "INSERT INTO students (id, name, email, phone, class_id, fee_status) VALUES (?, ?, ?, ?, ?, 'unpaid')";
            $stmt = $conn->prepare($student_sql);
            $stmt->bind_param("isssi", $user_id, $name, $email, $phone, $class_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error creating student record: " . $stmt->error);
            }
            $stmt->close();

            // Link the student with the parent in the parent-child relationship table
            $link_sql = "INSERT INTO parent_child_relationship (parent_id, child_id) VALUES (?, ?)";
            $stmt = $conn->prepare($link_sql);
            $stmt->bind_param("ii", $parent_id, $user_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error linking student to parent: " . $stmt->error);
            }
            $stmt->close();
            
            // Commit transaction
            $conn->commit();
            $successMessage = "New student added successfully. Username: $name, Default password: default_password";
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $errorMessage = $e->getMessage();
        }
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
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
            background-image: url('images/background2.png'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #5cb85c;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="tel"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 12px 20px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .error {
            color: red;
            border: 1px solid red;
            background-color: #ffe6e6;
        }
        .success {
            color: green;
            border: 1px solid green;
            background-color: #e6ffe6;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
    <div class="container">
        <h1>Add New Student</h1>
        
        <?php if (!empty($errorMessage)): ?>
            <div class="message error"><?= htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($successMessage)): ?>
            <div class="message success"><?= htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        
        <form action="" method="POST">
            <div class="form-group">
                <label for="name">Student Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="tel" name="phone" id="phone">
            </div>
            
            <div class="form-group">
                <label for="class_id">Class:</label>
                <select name="class_id" id="class_id">
                    <option value="">Select Class (Optional)</option>
                    <?php 
                    // Fetch available classes
                    $class_sql = "SELECT id, name FROM classes ORDER BY name";
                    $class_stmt = $conn->prepare($class_sql);
                    $class_stmt->execute();
                    $class_result = $class_stmt->get_result();
                    while ($class_row = $class_result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($class_row['id']) . '">' . htmlspecialchars($class_row['name']) . '</option>';
                    }
                    $class_stmt->close();
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="parent_id">Select Parent:</label>
                <select name="parent_id" id="parent_id" required>
                    <option value="">Select Parent</option>
                    <?php foreach ($parents as $parent): ?>
                        <option value="<?= htmlspecialchars($parent['id']); ?>"><?= htmlspecialchars($parent['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">Add Student</button>
        </form>
        
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>