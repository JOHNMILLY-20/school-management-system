<?php
// Include session and check if the admin is logged in
include 'session.php'; // Ensure session management is already in place
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not logged in or not an admin
    exit();
}

// Include database connection
include 'db.php'; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Fees</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
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
    </style>
</head>
<body>
    <h1>Manage Fees</h1>

    <form method="POST" action="add_fee.php">
        <label for="student_id">Select Student:</label>
        <select name="student_id" id="student_id" required>
            <?php
            $students_result = $conn->query("SELECT * FROM students");
            while ($student = $students_result->fetch_assoc()) {
                echo "<option value='{$student['id']}'>{$student['name']}</option>";
            }
            ?>
        </select>
        <label for="amount">Amount:</label>
        <input type="number" name="amount" required>
        <button type="submit">Add Fee</button>
    </form>

    <h2>Fee Records</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Paid Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetching fees records
            $fees_result = $conn->query("SELECT f.id, s.name AS student_name, f.amount, f.paid_date 
                                         FROM fees f 
                                         JOIN students s ON f.student_id = s.id");
            while ($fee = $fees_result->fetch_assoc()) {
                // Format the paid date for better readability
                $paid_date = $fee['paid_date'] ? date('Y-m-d H:i:s', strtotime($fee['paid_date'])) : 'N/A'; 
                echo "<tr>
                        <td>{$fee['id']}</td>
                        <td>{$fee['student_name']}</td>
                        <td>{$fee['amount']}</td>
                        <td>{$paid_date}</td>
                        <td><a href='delete_fee.php?id={$fee['id']}'>Delete</a></td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>