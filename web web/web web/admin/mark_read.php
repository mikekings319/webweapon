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

// Mark message as read
$sql = "UPDATE messages SET status = 'read' WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $message_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Message marked as read!";
} else {
    $_SESSION['error'] = "Error updating message: " . $conn->error;
}

header("Location: messages.php");
exit();
