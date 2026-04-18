<?php
include 'db.php';

$fee_id = $_GET['id'];
if ($fee_id) {
    $stmt = $conn->prepare("DELETE FROM fees WHERE id=?");
    $stmt->bind_param("i", $fee_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: manage_fees.php");
?>