<?php
session_start();
include 'db.php'; 
// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not logged in or not an admin
    exit();
}
// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $amount = trim($_POST['amount']);
    $due_date = $_POST['due_date'];
    
    // Validate inputs
    $error_message = "";
    
    if (empty($student_id)) {
        $error_message = "Student ID is required.";
    } elseif (empty($amount)) {
        $error_message = "Amount is required.";
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error_message = "Amount must be a valid number greater than 0.";
    } elseif (empty($due_date)) {
        $error_message = "Due date is required.";
    } else {
        // Check if student exists
        $student_check_sql = "SELECT id, name FROM students WHERE id = ?";
        $student_check_stmt = $conn->prepare($student_check_sql);
        $student_check_stmt->bind_param("i", $student_id);
        $student_check_stmt->execute();
        $student_result = $student_check_stmt->get_result();
        
        if ($student_result->num_rows === 0) {
            $error_message = "Student with ID $student_id does not exist.";
        } else {
            // Insert invoice into the database
            $student_data = $student_result->fetch_assoc();
            $sql = "INSERT INTO invoices (student_id, amount, invoice_date, due_date) VALUES (?, ?, NOW(), ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ids", $student_id, $amount, $due_date);
            
            if ($stmt->execute()) {
                $success_message = "Invoice generated successfully for {$student_data['name']}! Amount: $amount";
            } else {
                $error_message = "Error generating invoice: " . htmlspecialchars($stmt->error);
            }
            
            $stmt->close();
        }
        $student_check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html >
<head>
    <title>Generate Invoice</title>
    
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
            max-width: 500px;
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
        input[type="text"], input[type="number"], input[type="date"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 12px 20px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        input[type="submit"]:hover {
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
            padding: 12px 20px;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background-color: #4cae4c;
            text-decoration: none;
        }
    </style>

    <script>
        function validateForm() {
            const studentId = document.getElementById('student_id');
            const amount = document.getElementById('amount');
            const dueDate = document.getElementById('due_date');

            if (studentId.value.trim() === "") {
                alert("Please select a student.");
                return false;
            }

            if (amount.value.trim() === "" || isNaN(amount.value) || parseFloat(amount.value) <= 0) {
                alert("Please enter a valid amount greater than 0.");
                return false;
            }

            if (dueDate.value.trim() === "") {
                alert("Please select a due date.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Generate Invoice</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?= htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?= htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" onsubmit="return validateForm();">
            <div class="form-group">
                <label for="student_id">Student:</label>
                <select name="student_id" id="student_id" required>
                    <option value="">-- Select Student --</option>
                    <?php 
                    $students_result = $conn->query("SELECT id, name FROM students ORDER BY name ASC");
                    while ($student = $students_result->fetch_assoc()) {
                        echo "<option value='{$student['id']}'>{$student['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" name="amount" id="amount" step="0.01" min="0.01" required placeholder="Enter amount">
            </div>
            
            <div class="form-group">
                <label for="due_date">Due Date:</label>
                <input type="date" name="due_date" id="due_date" required>
            </div>
            
            <button type="submit">Generate Invoice</button>
        </form>
        
        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>