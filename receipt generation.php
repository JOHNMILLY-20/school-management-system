<?php
// Example receipt display (you would typically want to format this nicely)

if (isset($_GET['receipt_id'])) {
    $receipt_id = $_GET['receipt_id']; // You may want to fetch a receipt based on an invoice or payment ID

    // Fetch the payment record
    try {
        $query = "SELECT p.id, p.amount, p.payment_date, p.method, s.name AS student_name 
                  FROM payments p 
                  JOIN invoices i ON p.invoice_id = i.id 
                  JOIN students s ON i.student_id = s.id 
                  WHERE p.id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $receipt_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            echo "<h2>Payment Receipt</h2>";
            echo "<p>Receipt ID: " . htmlspecialchars($row['id']) . "</p>";
            echo "<p>Student: " . htmlspecialchars($row['student_name']) . "</p>";
            echo "<p>Payment Amount: " . htmlspecialchars($row['amount']) . "</p>";
            echo "<p>Payment Date: " . htmlspecialchars($row['payment_date']) . "</p>";
            echo "<p>Payment Method: " . htmlspecialchars($row['method']) . "</p>";
        } else {
            echo "Receipt not found.";
        }
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
}
?>