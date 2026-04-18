<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];
    
    $stmt = $conn->prepare("INSERT INTO grades (student_id, subject_id, grade) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $student_id, $subject_id, $grade);
    $stmt->execute();
    echo "Grades entered successfully.";
    $stmt->close();
}
?>

<form method="POST" action="">
    <select name="student_id">
        <!-- Student options -->
    </select>
    <select name="subject_id">
        <!-- Subject options -->
    </select>
    <input type="text" name="grade" placeholder="Grade" required>
    <input type="submit" value="Enter Grade">
</form>