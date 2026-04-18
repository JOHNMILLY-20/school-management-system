<?php
include 'db.php';

$class_id = $_GET['id'];
if ($class_id) {
    $stmt = $conn->prepare("DELETE FROM classes WHERE id=?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: manage_classes.php");
?>