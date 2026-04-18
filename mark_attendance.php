<?php
include 'db.php';

$class_id = $_POST['class_id'];
$date = $_POST['date'];
$checked_students = isset($_POST['student_ids']) ? $_POST['student_ids'] : [];

// Fetch all students in the class
$query = "SELECT * FROM students WHERE class_id = $class_id";
$students = $conn->query($query);

while ($student = $students->fetch_assoc()) {
    $status = in_array($student['id'], $checked_students) ? 'Present' : 'Absent';
    
    // Insert/Update attendance for each student
    $sql = "INSERT INTO attendance (student_id, date, status) VALUES ('{$student['id']}', '$date', '$status') 
            ON DUPLICATE KEY UPDATE status='$status'";
    $conn->query($sql);
}

// Redirect or display a success message
header('Location: manage_attendance.php');
exit();
?>