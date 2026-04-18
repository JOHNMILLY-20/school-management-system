<?php
include 'db.php';

$student_id = $_GET['id'];
if ($student_id) {
    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: manage_students.php");
?>