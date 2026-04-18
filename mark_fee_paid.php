<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fee_id = $_POST['fee_id'];
    $date_paid = date("Y-m-d");

    $stmt = $conn->prepare("UPDATE fees SET date_paid = ?, status = 'Paid' WHERE id = ?");
    $stmt->bind_param("si", $date_paid, $fee_id);

    if ($stmt->execute()) {
        echo "Fee marked as paid.";
    } else {
        echo "Error occurred: " . $conn->error;
    }

    $stmt->close();
}
?>

<form method="POST" action="">
    <select name="fee_id">
        <?php
        $result = $conn->query("SELECT id, amount FROM fees WHERE status = 'Pending'");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>Fee ID: {$row['id']} - Amount: {$row['amount']}</option>";
        }
        ?>
    </select>
    <input type="submit" value="Mark as Paid">
</form>