<?php
session_start();
include 'db.php'; 
// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php'); 
    exit();
}
$role = $_SESSION['role'];
// Get the user ID from session
$user_id = $_SESSION['user_id']; // Assuming 'user_id' is stored in session

// Fetch notifications for the logged-in user
$notifications = [];
$sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY sent_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are notifications and fetch them into an array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}
$stmt->close();

// Fetch fee statistics for admin overview
$feeStats = [];
if ($role === 'admin') {
    $sqlFees = "SELECT COUNT(*) AS total_invoices, SUM(amount) AS total_collected FROM invoices WHERE status = 'Paid'";
    $stmtFees = $conn->prepare($sqlFees);
    $stmtFees->execute();
    $resultFees = $stmtFees->get_result();

    if ($rowFees = $resultFees->fetch_assoc()) {
        $feeStats = $rowFees;
    }
    $stmtFees->close();
}

// Fetch invoices for student role
$invoices = [];
if ($role === 'student') {
    $sqlInvoices = "SELECT * FROM invoices WHERE student_id = ? ORDER BY due_date ASC";
    $stmtInvoices = $conn->prepare($sqlInvoices);
    $stmtInvoices->bind_param("i", $user_id); // Assuming user_id corresponds to student_id
    $stmtInvoices->execute();
    $resultInvoices = $stmtInvoices->get_result();

    if ($resultInvoices->num_rows > 0) {
        while ($rowInvoices = $resultInvoices->fetch_assoc()) {
            $invoices[] = $rowInvoices;
        }
    }
    $stmtInvoices->close();
} elseif ($role === 'parent') {
    // Fetch invoices for the parent's children
    $sqlInvoices = "SELECT i.* FROM invoices i 
                    JOIN parent_child_relationship pcr ON i.student_id = pcr.child_id 
                    WHERE pcr.parent_id = ? ORDER BY i.due_date ASC";
    $stmtInvoices = $conn->prepare($sqlInvoices);
    $stmtInvoices->bind_param("i", $user_id); // Use parent user_id
    $stmtInvoices->execute();
    $resultInvoices = $stmtInvoices->get_result();

    if ($resultInvoices->num_rows > 0) {
        while ($rowInvoices = $resultInvoices->fetch_assoc()) {
            $invoices[] = $rowInvoices;
        }
    }
    $stmtInvoices->close();
}

// Closing the database connection
$conn->close();
?>

<!DOCTYPE html>
<html >
<head>
    <title>Dashboard - <?= htmlspecialchars($_SESSION['username']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            background-image: url('images/background2.png'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
        }
        h1 {
            color: #333;
        }
        h2 {
            margin-top: 40px;
            color: #5cb85c;
        }
        h2 + ul {
            background-color: rgba(255, 255, 255, 0.8); 
            padding: 15px; 
            border-radius: 5px; 
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        a {
            text-decoration: none;
            color: blue; 
            font-weight: bold; 
        }
        a:hover {
            text-decoration: underline;
            color: blue; 
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Welcome to Maluti Primary School Management System</h1>
    <p>You are logged in as <?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($role); ?>)</p>

    <h2>Navigation</h2>
    <ul>
        <?php if ($role === 'admin'): ?>
            <!-- Admin links -->
            <li><a href="manage_classes.php">Manage Classes</a></li>
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a href="manage_teachers.php">Manage Teachers</a></li>
            <li><a href="manage_subjects.php">Manage Subjects</a></li>
            <li><a href="manage_fees.php">Manage Fees</a></li>
            <li><a href="view_fees.php">View Fees Overview</a></li>
            <li><a href="report_card.php">Generate Report</a></li>
            <li><a href="send_notification.php">Send Notification</a></li>
            <li><a href="add_user.php">Add User</a></li>
            <li><a href="add_student.php">Add Student</a></li>
            <li><a href="view_fee_payments.php">Track Fee Payments</a></li>
            <li><a href="generate_invoice.php">Generate Invoices</a></li>
            
        <?php endif; ?>

        <?php if ($role === 'teacher'): ?>
            <!-- Teacher links -->
            <li><a href="manage_students.php">Manage Students</a></li>
            <li><a href="manage_grades.php">Manage Grades</a></li>
            <li><a href="manage_attendance.php">Manage Attendance</a></li>
            <li><a href="manage_classes.php">Manage Classes</a></li>
            <li><a href="send_notification.php">Send Notification</a></li>
            <li><a href="report_card.php">Generate Report</a></li>
        <?php endif; ?>

        <?php if ($role === 'parent'): ?>
            <!-- Parent links -->
            <li><a href="view_children.php">View Children</a></li>
            <li><a href="pay_fee.php">Pay Fees</a></li>
            <li><a href="view_fees.php">View Fees Overview</a></li>
            <li><a href="report_card.php">View Report Cards &attendance</a></li>
            
        <?php endif; ?>

        <?php if ($role === 'student'): ?>
            <li><a href="report_card.php">View Report Card & attendance</a></li>
        <?php endif; ?>
    </ul>

    <h2>Your Notifications</h2>
    <?php if (!empty($notifications)) : ?>
        <ul>
            <?php foreach ($notifications as $notification) : ?>
                <li>
                    <strong><?= htmlspecialchars($notification['message']); ?></strong><br>
                    <small>Sent at: <?= htmlspecialchars($notification['sent_at']); ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No notifications.</p>
    <?php endif; ?>

    <?php if ($role === 'student' && !empty($invoices)) : ?>
        <h2>Your Invoices</h2>
        <table>
            <tr>
                <th>Invoice ID</th>
                <th>Amount</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
            <?php foreach ($invoices as $invoice) : ?>
                <tr>
                    <td><?= htmlspecialchars($invoice['id']); ?></td>
                    <td>$<?= htmlspecialchars(number_format($invoice['amount'], 2)); ?></td>
                    <td><?= htmlspecialchars($invoice['invoice_date']); ?></td>
                    <td><?= htmlspecialchars($invoice['due_date']); ?></td>
                    <td><?= htmlspecialchars($invoice['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($role === 'parent' && !empty($invoices)) : ?>
        <h2>Your Children's Invoices</h2>
        <table>
            <tr>
                <th>Invoice ID</th>
                <th>Amount</th>
                <th>Invoice Date</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
            <?php foreach ($invoices as $invoice) : ?>
                <tr>
                    <td><?= htmlspecialchars($invoice['id']); ?></td>
                    <td>$<?= htmlspecialchars(number_format($invoice['amount'], 2)); ?></td>
                    <td><?= htmlspecialchars($invoice['invoice_date']); ?></td>
                    <td><?= htmlspecialchars($invoice['due_date']); ?></td>
                    <td><?= htmlspecialchars($invoice['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else : ?>
        <p>You have no invoices at this time.</p>
    <?php endif; ?>

    <?php if ($role === 'admin' && !empty($feeStats)) : ?>
        <h2>Fee Payment Overview</h2>
        <p>Total Invoices: <?= htmlspecialchars($feeStats['total_invoices']); ?></p>
        <p>Total Amount Collected: $<?= htmlspecialchars(number_format($feeStats['total_collected'], 2)); ?></p>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</body>
</html>