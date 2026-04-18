<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $subject = $_POST['subject'];
    $grade = $_POST['grade'];

    $stmt = $conn->prepare("INSERT INTO grades (student_id, subject, grade) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $student_id, $subject, $grade);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_grades.php");
}
?>