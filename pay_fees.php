<?php
session_start();
include 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $student_id = $_POST['student_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];

    // First, validate that this student ID belongs to the logged-in parent
    $user_id = $_SESSION['user_id'];
    
    // Check if the student belongs to the parent
    $checkStudentQuery = "SELECT * FROM parent_child_relationship WHERE child_id = ? AND parent_id = ?";
    $checkStmt = $conn->prepare($checkStudentQuery);
    $checkStmt->bind_param("ii", $student_id, $user_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // The student belongs to this parent, proceed to record the fee
        $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, payment_method, payment_date) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ids", $student_id, $amount, $payment_method); // Adjust binding types as needed

        // Execute the query and check for success
        if ($stmt->execute()) {
            echo "Fee recorded successfully for child ID: $student_id. Amount: $amount. Payment Method: $payment_method.";
        } else {
            echo "Error recording fee: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        // Student ID does not belong to this parent
        echo "Error: The selected student does not belong to you.";
    }
    
    $checkStmt->close();
}
?>