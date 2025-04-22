<?php
session_start();
require_once('../includes/db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all messages
$sql = "SELECT * FROM messages ORDER BY created_at DESC";
$result = $conn->query($sql);
$messages = [];
if ($result && $result->num_rows > 0) {
    $messages = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - WebCraft Pro Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-dashboard">
    <nav class="admin-nav">
        <div class="admin-logo">WebCraft Pro - Admin</div>
        <div class="admin-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="projects.php">Projects</a>
            <a href="messages.php" class="active">Messages</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Messages</h2>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <div class="messages-grid">
            <?php foreach ($messages as $message): ?>
            <div class="message-card <?php echo $message['status'] == 'read' ? 'read' : ''; ?>">
                <div class="message-header">
                    <h3><?php echo htmlspecialchars($message['name']); ?></h3>
                    <span class="message-date"><?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></span>
                </div>
                <div class="message-email">
                    <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                        <?php echo htmlspecialchars($message['email']); ?>
                    </a>
                </div>
                <div class="message-content">
                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                </div>
                <div class="message-actions">
                    <a href="respond_message.php?id=<?php echo $message['id']; ?>" class="btn-respond">Respond</a>
                    <a href="delete_message.php?id=<?php echo $message['id']; ?>" 
                       class="btn-delete"
                       onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                    <?php if ($message['status'] !== 'read'): ?>
                    <a href="mark_read.php?id=<?php echo $message['id']; ?>" class="btn-mark-read">Mark as Read</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
        .messages-grid {
            display: grid;
            gap: 1.5rem;
            padding: 1rem;
        }
        
        .message-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #3b82f6;
        }
        
        .message-card.read {
            border-left-color: #9ca3af;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .message-date {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .message-email {
            margin-bottom: 1rem;
        }
        
        .message-email a {
            color: #3b82f6;
            text-decoration: none;
        }
        
        .message-content {
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .message-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .btn-respond {
            background: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
        }
        
        .btn-mark-read {
            background: #6b7280;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
        }
    </style>
</body>
</html>
