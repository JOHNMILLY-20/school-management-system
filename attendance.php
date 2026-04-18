<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}
require 'db.php';

$error_message = "";
$success_message = "";

// Handle Attendance Marking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $status = $_POST['status'];
    $date = date('Y-m-d');

    // Check if attendance for that student on that date already exists
    $check_sql = "SELECT * FROM attendance WHERE student_id = ? AND date = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("is", $student_id, $date);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $error_message = "Attendance for this student has already been marked for today.";
    } else {
        $sql = "INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $student_id, $date, $status);
        if ($stmt->execute()) {
            $success_message = "Attendance marked!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    }
}

$sql = "SELECT * FROM students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
</head>
<body>
    <h1>Mark Attendance</h1>
    <?php if ($error_message): ?>
        <div><?= htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div><?= htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="student_id">Select Student:</label>
        <select name="student_id" required>
            <?php while ($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select>
        <label for="status">Attendance Status:</label>
        <select name="status" required>
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="late">Late</option>
        </select>
        <input type="submit" value="Mark Attendance">
    </form>
</body>
</html>