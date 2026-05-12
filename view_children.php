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

// Fetch children associated with this parent with complete student information
$children = [];
$sql = "SELECT s.id, s.name, s.email, s.phone, s.fee_status, c.name as class_name, u.username
        FROM students s
        JOIN parent_child_relationship pcr ON s.id = pcr.child_id
        LEFT JOIN classes c ON s.class_id = c.id
        LEFT JOIN users u ON s.id = u.id
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
            padding: 20px;
            background-color: #f4f4f4;
            background-image: url('images/background2.png'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #5cb85c;
            margin-bottom: 20px;
        }
        .welcome {
            text-align: center;
            font-size: 1.2em;
            margin-bottom: 30px;
            color: #333;
        }
        .children-list {
            list-style-type: none;
            padding: 0;
        }
        .child-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #5cb85c;
        }
        .child-header {
            font-size: 1.3em;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .child-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 15px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 0.9em;
            margin-bottom: 3px;
        }
        .info-value {
            color: #333;
        }
        .fee-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .fee-status.paid {
            background-color: #d4edda;
            color: #155724;
        }
        .fee-status.unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
        .no-children {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            color: #666;
        }
        .nav-links {
            text-align: center;
            margin-top: 30px;
        }
        .nav-links a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #5cb85c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .nav-links a:hover {
            background-color: #4cae4c;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Children</h1>
        <p class="welcome">Welcome, <?= htmlspecialchars($_SESSION['username']); ?>!</p>

        <?php if (!empty($children)) : ?>
            <ul class="children-list">
                <?php foreach ($children as $child) : ?>
                    <li class="child-card">
                        <div class="child-header">
                            <?= htmlspecialchars($child['name']); ?>
                        </div>
                        <div class="child-info">
                            <div class="info-item">
                                <span class="info-label">Student ID:</span>
                                <span class="info-value"><?= htmlspecialchars($child['id']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Username:</span>
                                <span class="info-value"><?= htmlspecialchars($child['username'] ?: 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Class:</span>
                                <span class="info-value"><?= htmlspecialchars($child['class_name'] ?: 'Not Assigned'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?= htmlspecialchars($child['email'] ?: 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone:</span>
                                <span class="info-value"><?= htmlspecialchars($child['phone'] ?: 'N/A'); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Fee Status:</span>
                                <span class="info-value">
                                    <span class="fee-status <?= htmlspecialchars($child['fee_status']); ?>">
                                        <?= htmlspecialchars(ucfirst($child['fee_status'])); ?>
                                    </span>
                                </span>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <div class="no-children">
                <p>You have no children linked to your account.</p>
                <p>Please contact the school administrator to link your children to your account.</p>
            </div>
        <?php endif; ?>

        <div class="nav-links">
            <a href="dashboard.php">Back to Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</body>
</html>

<?php
$conn->close(); 
?>