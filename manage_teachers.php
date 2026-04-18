<?php
session_start();
require 'db.php'; 

// Check if the user is logged in and has admin privileges
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not logged in as admin
    exit();
}

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gather form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $class_assigned = trim($_POST['class_assigned']);

    // Basic validation to ensure fields are filled
    if (empty($name) || empty($email) || empty($subject) || empty($class_assigned)) {
        $error = "All fields are required.";
    } else {
        // Prepare and execute the insertion statement
        $stmt = $conn->prepare("INSERT INTO teachers (name, email, subject, class_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $email, $subject, $class_assigned);
        
        if ($stmt->execute()) {
            $success = "Teacher added successfully.";
        } else {
            $error = "Error adding teacher: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch classes for the dropdown menu
$class_query = "SELECT * FROM classes";
$class_result = $conn->query($class_query);
if ($class_result === FALSE) {
    $error = "Could not fetch classes: " . $conn->error;
} else {
    $classes = $class_result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form div {
            margin-bottom: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <h1>Add Teacher</h1>

    <!-- Display error or success messages -->
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <p style="color:green;"><?= htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <div>
            <label for="name">Teacher Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="email">Teacher Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>
        </div>
        <div>
            <label for="class_assigned">Assign Class:</label>
            <select id="class_assigned" name="class_assigned" required>
                <option value="">Select a class</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= htmlspecialchars($class['id']); ?>"><?= htmlspecialchars($class['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit">Add Teacher</button>
    </form>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>