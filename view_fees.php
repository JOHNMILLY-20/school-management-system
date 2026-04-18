<?php
session_start();
include 'db.php'; 
// Check if the user is authenticated and has the role 'parent'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    echo "You need to log in as a parent to view this page.";
    exit; // Exit if not logged in as a parent
}
// Get the user ID from the session
$user_id = $_SESSION['user_id'];
try {
    // Fetch the children (students) associated with the logged-in parent
    $studentQuery = "SELECT s.id AS child_id, s.name AS child_name FROM students s 
                     JOIN parent_child_relationship pcr ON s.id = pcr.child_id 
                     WHERE pcr.parent_id = ?";
    $studentStmt = $conn->prepare($studentQuery);
    $studentStmt->bind_param("i", $user_id); // Assuming user ID is an integer
    $studentStmt->execute();
    $studentResult = $studentStmt->get_result();

    // Check if there are any children
    if ($studentResult->num_rows === 0) {
        echo "No students found for this parent.";
        exit;
    }

    // Display a dropdown to select a child
    echo "<form method='POST' action=''>";
    echo "<select name='child_id'>";
    while ($row = $studentResult->fetch_assoc()) {
        echo "<option value='" . htmlspecialchars($row['child_id']) . "'>" . htmlspecialchars($row['child_name']) . "</option>";
    }
    echo "</select>";
    echo "<input type='submit' value='View Fees'>";
    echo "</form>";

    // If a child has been selected, fetch the fee records for that child
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['child_id'])) {
        $child_id = $_POST['child_id'];

        // Prepare a SQL query to fetch fees for the selected child
        $query = "SELECT f.id, s.name AS student_name, f.amount, f.paid_date 
                  FROM fees f 
                  JOIN students s ON f.student_id = s.id 
                  WHERE s.id = ?";
        
        // Prepare the statement
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $child_id); // bind the selected child's ID

        // Execute the statement
        $stmt->execute();
        
        // Get the result set from the statement
        $result = $stmt->get_result();

        // HTML output for the fees overview based on selected child
        echo "<h1>Fees Overview for " . htmlspecialchars($child_id) . "</h1>";
        echo "<table border='1' cellspacing='0' cellpadding='10'>
                <tr>
                    <th>Fee ID</th>
                    <th>Student Name</th>
                    <th>Amount</th>
                    <th>Paid Date</th>
                </tr>";

        // Fetch and display the results
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['student_name']) . "</td>
                        <td>" . htmlspecialchars($row['amount']) . "</td>
                        <td>" . htmlspecialchars($row['paid_date']) . "</td> 
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No fees records found for the selected student.</td></tr>";
        }

        echo "</table>";

    }
} catch (mysqli_sql_exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
} finally {
    // Close the statement and connection
    if (isset($studentStmt)) {
        $studentStmt->close();
    }
    if (isset($stmt)) {
        $stmt->close();
    }

    $conn->close();
}
?>
 <a href="dashboard.php">Back to Dashboard</a>