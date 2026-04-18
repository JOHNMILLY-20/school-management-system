<?php
include 'db.php';

$subject_id = $_GET['id'];
if ($subject_id) {
    $stmt = $conn->prepare("DELETE FROM subjects WHERE id=?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: manage_subjects.php");
?>