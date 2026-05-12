<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $subject = trim($_POST['subject']);
    $grade = trim($_POST['grade']);
    
    if (!empty($student_id) && !empty($subject) && !empty($grade)) {
        $stmt = $conn->prepare("INSERT INTO grades (student_id, subject, grade) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $student_id, $subject, $grade);
        $stmt->execute();
        $stmt->close();
        
        header("Location: manage_grades.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Grade</title>
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
        input[type="text"], input[type="number"], select {
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
        <h2>Add Grade</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="student_id">Student ID:</label>
                <input type="number" name="student_id" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject:</label>
                <input type="text" name="subject" required>
            </div>
            <div class="form-group">
                <label for="grade">Grade:</label>
                <input type="number" name="grade" min="0" max="100" required>
            </div>
            <button type="submit">Add Grade</button>
        </form>
        <a href="manage_grades.php" class="back-link">Back to Grades</a>
    </div>
</body>
</html>