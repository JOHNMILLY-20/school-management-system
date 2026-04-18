<?php include 'session.php'; ?>
<!DOCTYPE html>
<html >
<head>
    <title>Manage Attendance</title>
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
        label {
            display: block;
            margin: 10px 0 5px;
        }
        select, input[type="date"], button {
            width: calc(100% - 16px); /* Adjust width for padding */
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #5cb85c;
            color: white;
            border: none;
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
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f0f0f0;
        }
    </style>
    <script>
        function updateStudentList() {
            const classId = document.getElementById('class_id').value;

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_students.php?class_id=' + classId, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById('student_list').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Manage Attendance</h1>

        <form method="POST" action="mark_attendance.php">
            <label for="class_id">Select Class:</label>
            <select name="class_id" id="class_id" onchange="updateStudentList()">
                <option value="">Select Class</option>
                <?php
                include 'db.php';
                $result = $conn->query("SELECT * FROM classes");
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>

            <label for="date">Date:</label>
            <input type="date" name="date" required>

            <label for="student_ids">Select Students:</label>
            <div id="student_list"></div>

            <button type="submit">Mark Attendance</button>
        </form>

        <h2>Attendance Records</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT a.id, s.name AS student_name, a.date, a.status FROM attendance a JOIN students s ON a.student_id = s.id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['student_name']}</td>
                            <td>{$row['date']}</td>
                            <td>{$row['status']}</td>
                            <td>
                                <a href='edit_attendance.php?id={$row['id']}'>Edit</a> |
                                <a href='delete_attendance.php?id={$row['id']}'>Delete</a>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>