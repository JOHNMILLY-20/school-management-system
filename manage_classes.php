<?php include 'session.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Classes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            color: #5cb85c;
            text-align: center;
        }
        h2 {
            color: #4a4a4a;
        }
        form {
            max-width: 400px;
            margin: 20px auto;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        input[type="number"],
        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
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
        .action-links a {
            margin-left: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this class?');
        }
    </script>
</head>
<body>
    <h1>Manage Classes</h1>

    <form method="POST" action="add_class.php">
        <input type="number" name="id" required placeholder="ID">
        <input type="text" name="name" required placeholder="Name">
        <button type="submit">Add Class</button>
    </form>

    <h2>Class List</h2>
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
            $result = $conn->query("SELECT * FROM classes");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td class='action-links'>
                            <a href='edit_class.php?id={$row['id']}'>Edit</a> | 
                            <a href='delete_class.php?id={$row['id']}' onclick='return confirmDelete();'>Delete</a>
                        </td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>

    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>