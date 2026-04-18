<?php
include 'db.php';  // Include your database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if 'id' and 'name' keys exist in the posted data
    if (isset($_POST['id']) && isset($_POST['name'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];

        // Prepare the SQL statement for inserting a new class
        $stmt = $conn->prepare("INSERT INTO classes (id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $name);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to manage_classes.php upon successful insertion
            header("Location: manage_classes.php");
            exit; // Ensure no further code is executed
        } else {
            // Handle execution error
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Missing id or name.";
    }
}

// HTML Form for Adding a New Class
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Class</title>
</head>
<body>
    <h2>Add a New Class</h2>
    <form method="POST" action=""> <!-- Form submits to the same page -->
        <label for="id">ID:</label>
        <input type="number" name="id" required> <!-- Correct field name for ID -->
        <br>
        <label for="name">Class Name:</label>
        <input type="text" name="name" required> <!-- Correct field name for Name -->
        <br>
        <input type="submit" value="Add Class">
    </form>
</body>
</html>