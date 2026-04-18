<?php include 'session.php'; ?>
<!DOCTYPE html>
<html >
<head>
    <title>Manage Subjects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1, h2 {
            text-align: center;
        }
        form {
            margin: 20px auto;
            width: 300px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #4cae4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #5bc0de;
            color: white;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
    <script>
        function validateForm() {
            const subjectInput = document.querySelector('input[name="subject_name"]');
            if (subjectInput.value.trim() === "") {
                alert("Subject name is required.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <h1>Manage Subjects</h1>

    <form method="POST" action="add_subject.php" onsubmit="return validateForm();">
        <input type="text" name="subject_name" required placeholder="Subject Name">
        <button type="submit">Add Subject</button>
    </form>

    <h2>Subject List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'db.php';

            $result = $conn->query("SELECT * FROM subjects");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td><a href='delete_subject.php?id={$row['id']}'>Delete</a></td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>