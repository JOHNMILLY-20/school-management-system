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
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
            background-image: url('images/background2.png'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #5cb85c;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="number"], input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px 20px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 12px 20px;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background-color: #4cae4c;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add a New Class</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="id">ID:</label>
                <input type="number" name="id" required>
            </div>
            <div class="form-group">
                <label for="name">Class Name:</label>
                <input type="text" name="name" required>
            </div>
            <button type="submit">Add Class</button>
        </form>
        <a href="manage_classes.php" class="back-link">Back to Classes</a>
    </div>
</body>
</html>