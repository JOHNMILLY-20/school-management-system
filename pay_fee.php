<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session

// Include database connection
include 'db.php';

// Check if the database connection was successful
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// --- Logic to handle form submission or display the form ---

// Check if the request method is GET and if the required data is present in the URL
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['student_id']) && isset($_GET['amount'])) {
    // --- PROCESS FORM SUBMISSION ---

    // Get the form data from $_GET
    $student_id = $_GET['student_id'];
    $amount = $_GET['amount'];

    // Validate input (basic validation)
    if (empty($student_id) || empty($amount)) {
        $message = "Error: Student ID and Amount are required.";
    } elseif (!is_numeric($amount) || $amount < 0) {
         $message = "Error: Amount must be a non-negative number.";
    } else {
        // Prepare the insert statement to record the fees
        $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, paid_date) VALUES (?, ?, NOW())");

        // Check if the prepare statement was successful
        if ($stmt === false) {
            $message = "Error preparing statement: " . $conn->error;
        } else {
            // Bind parameters
            // Assuming student_id is integer, amount is decimal/double
            $bind_success = $stmt->bind_param("id", $student_id, $amount);

            // Check if binding parameters was successful
            if ($bind_success === false) {
                 $message = "Error binding parameters: " . $stmt->error;
            } else {
                // Execute the query and check for success
                if ($stmt->execute()) {
                    $message = "Fee recorded successfully for child ID: " . htmlspecialchars($student_id) . ". Amount: " . htmlspecialchars($amount) . ".";
                } else {
                    $message = "Error recording fee: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
} else {
    // --- DISPLAY THE FORM ---
    // This happens when the user first visits the page or if the required GET parameters are missing
    $message = ""; // Initialize message for the form view
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Record Fee</title>
</head>
<body>

    <h2>Record Fee Payment</h2>

    <?php
    // Display messages (success, error, or empty)
    if (!empty($message)) {
        echo "<p>" . $message . "</p>";
    }
    ?>

    <!-- The form action points to THIS SAME PHP FILE -->
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="get">
        <label for="student_id">Student ID:</label><br>
        <input type="text" id="student_id" name="student_id" required><br><br>

        <label for="amount">Amount:</label><br>
        <input type="number" id="amount" name="amount" step="0.01" required><br><br>

        <input type="submit" value="Record Fee">
    </form>

    <a href="dashboard.php">Back to Dashboard</a>

</body>
</html>