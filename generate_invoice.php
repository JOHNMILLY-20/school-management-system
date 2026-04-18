<?php
session_start();
include 'db.php'; 
// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not logged in or not an admin
    exit();
}
// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
  // Insert invoice into the database
    $sql = "INSERT INTO invoices (student_id, amount, invoice_date, due_date) VALUES (?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ids", $student_id, $amount, $due_date);
  
    if ($stmt->execute()) {
        echo "<p>Invoice generated successfully!</p>";
    } else {
        echo "<p>Error generating invoice: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html >
<head>
    <title>Generate Invoice</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="date"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>

    <script>
        function validateForm() {
            const studentId = document.querySelector('input[name="student_id"]');
            const amount = document.querySelector('input[name="amount"]');
            const dueDate = document.querySelector('input[name="due_date"]');

            if (studentId.value.trim() === "") {
                alert("Please enter a valid Student ID.");
                return false; // Prevent form submission
            }

            if (amount.value.trim() === "" || isNaN(amount.value) || parseFloat(amount.value) <= 0) {
                alert("Please enter a valid amount greater than 0.");
                return false; // Prevent form submission
            }

            if (dueDate.value.trim() === "") {
                alert("Please select a due date.");
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>
</head>
<body>
<h1>Generate Invoice</h1>
<form method="POST" action="" onsubmit="return validateForm();">
    <label for="student_id">Student ID:</label>
    <input type="text" name="student_id" required>

    <label for="amount">Amount:</label>
    <input type="text" name="amount" required>

    <label for="due_date">Due Date:</label>
    <input type="date" name="due_date" required>

    <input type="submit" value="Generate Invoice">
</form>

<center><a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>