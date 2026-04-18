<?php
session_start();
include 'db.php';

$result = $conn->query("SELECT * FROM students");
echo "<h1>Students</h1>";
echo "<table border='1' cellspacing='0' cellpadding='10'>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Fee Status</th>
            <th>Actions</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['fee_status']) . "</td>
            <td>
                <a href='add_fee.php?student_id={$row['id']}'>Add Fee</a> | 
                <a href='view_fees.php?student_id={$row['id']}'>View Fees</a>
            </td>
          </tr>";
}

echo "</table>";
$conn->close();
?>