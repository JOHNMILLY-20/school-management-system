<?php
session_start();
if (!isset($_SESSION['loggedin']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'teacher')) {
    header('Location: login.php');
    exit();
}

require 'db.php'; 

$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $name = $_POST['name'];
    $class_id = $_POST['class'];  // Ensure variable name matches form input
    $fee_status = $_POST['fee_status'] ?? 'unpaid'; // Default to 'unpaid'

    // Correct SQL syntax
    $sql = "INSERT INTO students (name, class_id, fee_status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Verify that the statement was prepared successfully
    if ($stmt) {
        $stmt->bind_param("sss", $name, $class_id, $fee_status);
        if ($stmt->execute()) {
            $success_message = "Student added successfully!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
        $stmt->close(); // Close the statement to free resources
    } else {
        $error_message = "Error preparing statement: " . $conn->error;
    }
}

$conn->close(); // Close the database connection
?>

<!DOCTYPE html>
<html >
<head>
    <title>Manage Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #5cb85c;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="radio"] {
            margin-right: 5px;
        }
        button {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Manage Students</h1>
    <?php if ($error_message): ?>
        <div class="message error"><?= htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="message success"><?= htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    <form method="POST" onsubmit="return validateForm()">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        
        <label for="class">Class ID:</label>
        <input type="text" id="class" name="class" required>
        
        <label>Fee Status:</label>
        <label>
            <input type="radio" name="fee_status" value="paid" required> Paid
        </label>
        <label>
            <input type="radio" name="fee_status" value="unpaid" required> Unpaid
        </label>
        
        <button type="submit" name="add_student">Add Student</button>
    </form>
</div>

<script>
    function validateForm() {
        const name = document.getElementById('name').value;
        const classId = document.getElementById('class').value;

        if (name.trim() === "" || classId.trim() === "") {
            alert("Name and Class ID cannot be empty.");
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }
</script>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>