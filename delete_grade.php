<?php
include 'db.php';

$grade_id = $_GET['id'];
if ($grade_id) {
    $stmt = $conn->prepare("DELETE FROM grades WHERE id=?");
    $stmt->bind_param("i", $grade_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: manage_grades.php");
?>