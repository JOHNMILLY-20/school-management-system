<?php
session_start();
include 'db.php';  

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch parents for the dropdown
$parents = [];
$sql = "SELECT id, username FROM users WHERE role = 'parent'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $parents[] = $row;
    }
}
?>
<!DOCTYPE html>
<html >
<head>
    <title>Add Student</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
    </style>
    <script>
        function validateForm() {
            const nameInput = document.querySelector('input[name="name"]');
            const parentSelect = document.querySelector('select[name="parent_id"]');

            if (nameInput.value.trim() === "") {
                alert("Please enter the student's name.");
                return false; // Prevent form submission
            }

            if (parentSelect.value === "") {
                alert("Please select a parent.");
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <h1>Add New Student</h1>
    <form action="add_student_process.php" method="POST" onsubmit="return validateForm();">
        <label for="name">Student Name:</label>
        <input type="text" name="name" required placeholder="Enter student's name">
        
        <label for="parent_id">Select Parent:</label>
        <select name="parent_id" required>
            <option value="">Select Parent</option>
            <?php foreach ($parents as $parent): ?>
                <option value="<?= htmlspecialchars($parent['id']); ?>"><?= htmlspecialchars($parent['username']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <input type="submit" value="Add Student">
    </form>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>