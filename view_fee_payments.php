<?php
session_start();
include 'db.php'; 
// Check if user is logged in and is admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not logged in or not an admin
    exit();
}
// Fetch all fee payments
$payments = [];
$sql = "SELECT p.id AS payment_id, p.amount, p.payment_date, p.method, s.name AS student_name, i.amount AS invoice_amount, i.due_date 
        FROM payments p 
        JOIN invoices i ON p.invoice_id = i.id 
        JOIN students s ON i.student_id = s.id 
        ORDER BY p.payment_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are payments and fetch them into an array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $payments[] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html >
<head>
    <title>View Fee Payments</title>
    
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #5cb85c;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tfoot tr {
            font-weight: bold;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>

    <script>
        function confirmDeletion(paymentId) {
            return confirm('Are you sure you want to delete this payment?');
        }
    </script>
</head>
<body>
<h1>Fee Payments Overview</h1>

<table>
    <thead>
        <tr>
            <th>Payment ID</th>
            <th>Student Name</th>
            <th>Invoice Amount</th>
            <th>Paid Amount</th>
            <th>Payment Date</th>
            <th>Payment Method</th>
            <th>Due Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($payments)) : ?>
            <?php foreach ($payments as $payment) : ?>
                <tr>
                    <td><?= htmlspecialchars($payment['payment_id']); ?></td>
                    <td><?= htmlspecialchars($payment['student_name']); ?></td>
                    <td><?= htmlspecialchars(number_format($payment['invoice_amount'], 2)); ?></td>
                    <td><?= htmlspecialchars(number_format($payment['amount'], 2)); ?></td>
                    <td><?= htmlspecialchars(date('Y-m-d', strtotime($payment['payment_date']))); ?></td>
                    <td><?= htmlspecialchars($payment['method']); ?></td>
                    <td><?= htmlspecialchars(date('Y-m-d', strtotime($payment['due_date']))); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center;">No fee payments found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>