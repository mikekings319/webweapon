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

// Delete message
$sql = "DELETE FROM messages WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $message_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Message deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting message: " . $conn->error;
}

header("Location: messages.php");
exit();
