<?php
include 'db.php';

$class_id = $_GET['class_id'];

// Fetch all students in the selected class
$query = "SELECT * FROM students WHERE class_id = $class_id";
$students = $conn->query($query);

// Initialize attendance records for the current date
$date = date('Y-m-d');
$attendance_query = "SELECT student_id, status FROM attendance WHERE date = '$date'";
$attendances = [];
if ($attendance_result = $conn->query($attendance_query)) {
    while ($attendance_row = $attendance_result->fetch_assoc()) {
        $attendances[$attendance_row['student_id']] = $attendance_row['status'];
    }
}

// Generate the list of students with attendance status
while ($student = $students->fetch_assoc()) {
    $status = isset($attendances[$student['id']]) ? $attendances[$student['id']] : 'Absent';
    echo "<label>
            <input type='checkbox' name='student_ids[]' value='{$student['id']}' " . ($status == 'Present' ? 'checked' : '') . ">
            {$student['name']} - <strong>Status: {$status}</strong>
          </label>";
}