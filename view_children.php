<?php
session_start();
include 'db.php'; 
// Checking if user is logged in and is a parent
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent') {
    header('Location: login.php'); // Redirect to login if not logged in or not a parent
    exit();
}
// Get parent user ID from session
$parent_id = $_SESSION['user_id'];

// Fetch children associated with this parent
$children = [];
$sql = "SELECT c.id, c.username FROM users c
        JOIN parent_child_relationship pcr ON c.id = pcr.child_id
        WHERE pcr.parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if there are children and fetch them into an array
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $children[] = $row;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html >
<head>
    <title>Your Children</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        p {
            text-align: center;
            font-size: 1.2em;
        }
        ul {
            list-style-type: none;
            padding: 0;
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        li {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #555;
        }
        li:last-child {
            border-bottom: none; 
        }
        a {
            display: block;
            text-align: center;
            margin: 15px auto;
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Your Children</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</p>

    <?php if (!empty($children)) : ?>
        <ul>
            <?php foreach ($children as $child) : ?>
                <li>
                    <strong>Child ID:</strong> <?= htmlspecialchars($child['id']); ?><br>
                    <strong>Name:</strong> <?= htmlspecialchars($child['username']); ?><br> 
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>You have no children linked to your account.</p>
    <?php endif; ?>

    <a href="dashboard.php">Back to Dashboard</a>
    <a href="logout.php">Logout</a>
</body>
</html>

<?php
$conn->close(); 
?>