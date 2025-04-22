<?php
session_start();
require_once('../includes/db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if message ID is provided
if (!isset($_GET['id'])) {
    header("Location: messages.php");
    exit();
}

$message_id = $_GET['id'];

// Fetch message data
$sql = "SELECT * FROM messages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $message_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: messages.php");
    exit();
}

$message = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = $_POST['response'];
    $to = $message['email'];
    $subject = "Re: Website Inquiry";
    
    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . $_SERVER['SERVER_NAME'] . "\r\n";
    
    // Email content
    $email_content = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .original-message { background: #f3f4f6; padding: 15px; margin: 20px 0; border-left: 4px solid #3b82f6; }
        </style>
    </head>
    <body>
        <div class='container'>
            <p>Dear " . htmlspecialchars($message['name']) . ",</p>
            
            " . nl2br(htmlspecialchars($response)) . "
            
            <div class='original-message'>
                <strong>Your original message:</strong><br>
                " . nl2br(htmlspecialchars($message['message'])) . "
            </div>
            
            <p>Best regards,<br>WebCraft Pro Team</p>
        </div>
    </body>
    </html>";
    
    // Send email
    if (mail($to, $subject, $email_content, $headers)) {
        // Update message status to 'read'
        $sql = "UPDATE messages SET status = 'read' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        
        $_SESSION['success'] = "Response sent successfully!";
        header("Location: messages.php");
        exit();
    } else {
        $error = "Error sending email. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Message - WebCraft Pro Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-dashboard">
    <nav class="admin-nav">
        <div class="admin-logo">WebCraft Pro - Admin</div>
        <div class="admin-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="projects.php">Projects</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Respond to Message</h2>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="dashboard-card">
            <div class="original-message">
                <h3>Original Message</h3>
                <p><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></p>
                <p><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></p>
                <div class="message-content">
                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                </div>
            </div>

            <form method="POST" class="response-form">
                <div class="form-group">
                    <label for="response">Your Response</label>
                    <textarea id="response" name="response" rows="6" required></textarea>
                </div>

                <button type="submit" class="btn-respond">Send Response</button>
                <a href="messages.php" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>

    <style>
        .original-message {
            background: #f3f4f6;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        
        .response-form {
            display: grid;
            gap: 1.5rem;
        }
        
        .response-form textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 1rem;
            font-family: inherit;
        }
        
        .message-content {
            margin-top: 1rem;
            white-space: pre-wrap;
        }
    </style>
</body>
</html>
