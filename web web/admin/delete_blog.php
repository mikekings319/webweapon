<?php
session_start();
require_once('../includes/db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if blog ID is provided
if (!isset($_GET['id'])) {
    header("Location: blogs.php");
    exit();
}

$blog_id = $_GET['id'];

// Fetch blog data to get image URL
$sql = "SELECT image_url FROM blogs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $blog = $result->fetch_assoc();
    
    // Delete blog image if exists
    if (!empty($blog['image_url'])) {
        $image_path = '../' . $blog['image_url'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete blog from database
    $sql = "DELETE FROM blogs WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $blog_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Blog post deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting blog post: " . $conn->error;
    }
}

header("Location: blogs.php");
exit();
