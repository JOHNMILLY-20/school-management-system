<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection setup
$servername = "localhost";
$username = "root";
$password = ""; // Your database password
$database = "maluti_primary_school"; // Your database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for handling messages
$error_message = "";
$success_message = "";

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Get the input values
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prepare SQL statement to retrieve user
    $sql = "SELECT id, username, role, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if any user found
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($pass, $row['password'])) {
            // Password is correct, set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Redirect to dashboard after successful login
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Invalid password!";
        }
    } else {
        $error_message = "No user found with that username!";
    }
    $stmt->close();
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];

    // Check if username exists
    $sql = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username exists, update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password = ? WHERE username = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ss", $hashed_password, $username);

        if ($update_stmt->execute()) {
            $success_message = "Password updated successfully!";
        } else {
            $error_message = "Error updating password: " . $update_stmt->error;
        }

        $update_stmt->close();
    } else {
        $error_message = "No user found with that username!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Main body styles */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0; /* Reset margins */
            position: relative; /* Allow absolute positioning of children */
        }
        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -1; /* Place the background behind the content */
            opacity: 0.5; /* Makes the image semi-transparent */
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            z-index: 1; /* Place the form above the background */
            margin-top: 50px; /* Add margin to move the container lower */
        }
        h2 {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        input[type="submit"] {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 3px;
            cursor: pointer;
            width: 100%;
        }
        input[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            margin-bottom: 10px;
        }
        .links {
            margin-top: 15px;
            text-align: center;
        }
        .links a {
            display: block;
            margin: 5px 0;
            color: #007bff;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <img src="images/login.png" alt="Background Image" class="background-image" />
    
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error_message)) { echo "<div class='error'>{$error_message}</div>"; } ?>
        <?php if (!empty($success_message)) { echo "<div class='success'>{$success_message}</div>"; } ?>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" name="password" required>
            <br>
            <input type="submit" name="login" value="Login">
        </form>

        <h3>Change Password</h3>
        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <br>
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required>
            <br>
            <input type="submit" name="change_password" value="Change Password">
        </form>

        <div class="links">
            <a href="register.php">Don't have an account? Sign up</a>
        </div>
    </div>
</body>
</html>