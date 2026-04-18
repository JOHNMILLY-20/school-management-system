<?php
session_start();
include 'db.php'; // Include database connection

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form fields
    $student_id = (int)$_POST['student_id']; // Cast to int for safety
    $amount = $_POST['amount'];

    // Validate input (simple validation)
    if (empty($student_id) || empty($amount)) {
        die('Invalid input. Please provide student ID and amount.');
    }

    // Prepare and bind statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, paid_date) VALUES (?, ?, NOW())");
    $stmt->bind_param("id", $student_id, $amount); // 'i' for integer, 'd' for decimal

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to manage_fees.php after adding
        header('Location: manage_fees.php?success=Fee added successfully!');
        exit();
    } else {
        echo "Error adding fee: " . $stmt->error; 
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Fee</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        input {
            padding: 10px;
            margin: 5px 0;
            width: calc(100% - 20px);
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
    </style>
</head>
<body>

<h1>Add Fee for <?= htmlspecialchars($student_row['name']) ?></h1>
<form method="POST" action="">
    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student_id) ?>">
    <input type="number" name="amount" placeholder="Fee Amount" required>
    <input type="submit" value="Add Fee">
</form>

</body>
</html>