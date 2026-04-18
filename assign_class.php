<?php
// Include the database connection
include 'db.php';

// Initialize variables for form processing
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $student_id = $_POST['student_id'];
    $class_id = $_POST['class_id'];

    // Validate the inputs
    if (empty($student_id) || empty($class_id)) {
        $message = 'Please select both a student and a class.';
    } else {
        // Prepare the SQL statement to assign the class to the student
        $stmt = $conn->prepare("UPDATE students SET class_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $class_id, $student_id); // Assume both IDs are integers

        if ($stmt->execute()) {
            $message = 'Class assigned successfully!';
        } else {
            $message = 'Error: ' . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Fetch students and classes from the database
$studentsResult = $conn->query("SELECT * FROM students");
$classesResult = $conn->query("SELECT * FROM classes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Class to Student</title>
    <style>
        /* Basic styling for the form */
        body {
            font-family: Arial, sans-serif;
        }

        form {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 300px;
        }

        label, select, input {
            display: block;
            margin-bottom: 10px;
        }

        .message {
            margin: 20px;
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>
<body>

<h1>Assign Class to Student</h1>

<?php if (!empty($message)): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<form method="POST" action="">
    <label for="student_id">Select Student:</label>
    <select name="student_id" required>
        <option value="">Select a student</option>
        <?php while ($student = $studentsResult->fetch_assoc()): ?>
            <option value="<?= $student['id']; ?>"><?= htmlspecialchars($student['name']); ?></option>
        <?php endwhile; ?>
    </select>

    <label for="class_id">Select Class:</label>
    <select name="class_id" required>
        <option value="">Select a class</option>
        <?php while ($class = $classesResult->fetch_assoc()): ?>
            <option value="<?= $class['id']; ?>"><?= htmlspecialchars($class['name']); ?></option>
        <?php endwhile; ?>
    </select>

    <input type="submit" value="Assign Class">
</form>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>