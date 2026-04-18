<?php
include 'db.php'; 
session_start(); 

// Function to send a notification
function sendNotification($recipient_id, $message, $sender_id = null) {
    global $conn;

    // Sanitize the message to prevent SQL injection
    $message = $conn->real_escape_string($message);
    $sql = "INSERT INTO notifications (user_id, message, sent_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Error preparing statement: " . $conn->error);
        return false;
    }

    // Bind parameters
    $stmt->bind_param("is", $recipient_id, $message);

    // Execute the statement
    if ($stmt->execute()) {
        return true;
    } else {
        error_log("Error sending notification: " . $stmt->error);
        return false;
    }

    $stmt->close();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $recipient_type = $_POST['recipient_type'];
    $recipient_id = intval($_POST['recipient_id']); //  an integer
    $message = $_POST['message'];
    $sender_id = isset($_POST['sender_id']) ? intval($_POST['sender_id']) : null; // Optional sender ID

    // Send the notification
    if (sendNotification($recipient_id, $message, $sender_id)) {
        $feedback = "Notification sent successfully!";
    } else {
        $feedback = "Failed to send notification.";
    }
}

// Close the database connection 
$conn->close();
?>

<!DOCTYPE html>
<html >
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label, select, input, textarea {
            display: block;
            margin-bottom: 10px;
            width: 100%;
        }
        button {
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .feedback {
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h1>Send Notification</h1>

<!-- Feedback message -->
<?php if (isset($feedback)): ?>
    <div class="feedback"><?php echo $feedback; ?></div>
<?php endif; ?>

<!-- Notification form -->
<form method="post">
    <label for="recipient_type">Recipient Type:</label>
    <select name="recipient_type" id="recipient_type" required>
        <option value="parent">Parent</option>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
    </select>

    <label for="recipient_id">Recipient ID:</label>
    <input type="number" name="recipient_id" id="recipient_id" required>

    <label for="message">Message:</label>
    <textarea name="message" id="message" rows="4" required></textarea>

    <label for="sender_id">Sender ID (optional):</label>
    <input type="number" name="sender_id" id="sender_id">

    <button type="submit">Send Notification</button>
</form>
<a href="dashboard.php">Back to Dashboard</a>
</body>
</html>