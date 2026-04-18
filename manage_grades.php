<?php include 'session.php'; ?>
<!DOCTYPE html>
<html >
<head>
    <title>Manage Grades</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1, h2 {
            color: #5cb85c;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        form {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="number"], select {
            width: calc(100% - 16px); 
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #4cae4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f0f0f0;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .error {
            color: red;
            background-color: #ffe6e6;
        }
        .success {
            color: green;
            background-color: #e6ffe6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Grades</h1>

        <form method="POST" action="add_grade.php" onsubmit="return validateForm()">
            <label for="student_id">Select Student:</label>
            <select name="student_id" id="student_id" required>
                <option value="">Select Student</option>
                <?php
                include 'db.php';

                // Fetch registered students from the database
                $result = $conn->query("SELECT * FROM students");

                // Check for the possibility of registered students
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                    }
                } else {
                    echo "<option value=''>No registered students found.</option>";
                }
                ?>
            </select>
            
            <label for="subject">Subject:</label>
            <input type="text" name="subject" id="subject" required>
            
            <label for="grade">Grade:</label>
            <input type="number" name="grade" id="grade" step="0.1" required min="0" max="100">
            
            <button type="submit">Add Grade</button>
        </form>

        <h2>Grade Records</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch grade records along with student names
                $result = $conn->query("SELECT g.id, s.name AS student_name, g.subject, g.grade FROM grades g JOIN students s ON g.student_id = s.id ORDER BY g.id DESC");

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['student_name']}</td>
                                <td>{$row['subject']}</td>
                                <td>{$row['grade']}</td>
                                <td>
                                    <a href='edit_grade.php?id={$row['id']}'>Edit</a> |
                                    <a href='delete_grade.php?id={$row['id']}'>Delete</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No grade records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <script>
        function validateForm() {
            const studentId = document.getElementById('student_id').value;
            const subject = document.getElementById('subject').value.trim();
            const grade = parseFloat(document.getElementById('grade').value);

            if (!studentId) {
                alert("Please select a student.");
                return false;
            }
            if (!subject) {
                alert("Please enter a subject.");
                return false;
            }
            if (isNaN(grade) || grade < 0 || grade > 100) {
                alert("Grade must be a number between 0 and 100.");
                return false;
            }
            return true; // Allow form submission
        }
    </script>
</body>
</html>