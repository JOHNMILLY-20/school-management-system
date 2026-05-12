<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require 'db.php';

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


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
            background-image: url('images/background2.png'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            text-align: center;
            color: #5cb85c;
            margin-bottom: 20px;
        }
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .error {
            color: red;
            border: 1px solid red;
            background-color: #ffe6e6;
        }
        .success {
            color: green;
            border: 1px solid green;
            background-color: #e6ffe6;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            padding: 12px 20px;
            background-color: #5cb85c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #4cae4c;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Maluti Primary School</h1>
        
        <div class="form-section">
            <h2>Login</h2>
            <?php if (!empty($error_message)): ?>
                <div class="message error"><?= htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="message success"><?= htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="login_username">Username:</label>
                <input type="text" id="login_username" name="username" required>
                
                <label for="login_password">Password:</label>
                <input type="password" id="login_password" name="password" required>
                
                <button type="submit" name="login">Login</button>
            </form>
        </div>

        
        <div class="links">
            <a href="register.php">Don't have an account? Sign up</a>
            <a href="change_password.php">Forgot Password? Change Password</a>
        </div>
    </div>

    <script>
        // Form validation for login form
        const loginForm = document.querySelectorAll('form')[0];
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                const username = document.getElementById('login_username').value.trim();
                const password = document.getElementById('login_password').value.trim();
                
                if (username === '') {
                    alert('Please enter your username.');
                    e.preventDefault();
                    return false;
                }
                
                if (password === '') {
                    alert('Please enter your password.');
                    e.preventDefault();
                    return false;
                }
                
                return true;
            });
        }
        
    </script>
</body>
</html>