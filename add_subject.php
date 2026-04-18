<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = $_POST['subject_name'];

    $stmt = $conn->prepare("INSERT INTO subjects (name) VALUES (?)");
    $stmt->bind_param("s", $subject_name);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_subjects.php");
}
?>