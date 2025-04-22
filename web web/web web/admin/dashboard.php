<?php
session_start();
require_once('../includes/db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Initialize arrays
$projects = [];
$messages = [];

// Fetch projects
$sql = "SELECT * FROM projects ORDER BY created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $projects = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch messages
$sql = "SELECT * FROM messages ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $messages = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch recent blogs
$sql = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT 5";
$result = $conn->query($sql);
$blogs = [];
if ($result && $result->num_rows > 0) {
    $blogs = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - WebCraft Pro</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-dashboard">
    <nav class="admin-nav">
        <div class="admin-logo">WebCraft Pro - Admin</div>
        <div class="admin-menu">
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="projects.php">Projects</a>
            <a href="blogs.php">Blogs</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h2>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Recent Projects</h3>
                <div class="project-list">
                    <?php foreach ($projects as $project): ?>
                    <div class="project-item">
                        <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                        <p><?php echo substr(htmlspecialchars($project['description']), 0, 100); ?>...</p>
                        <div class="project-actions">
                            <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete_project.php?id=<?php echo $project['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="add_project.php" class="btn-add">Add New Project</a>
            </div>

            <div class="dashboard-card">
                <h3>Recent Blog Posts</h3>
                <div class="blog-list">
                    <?php foreach ($blogs as $blog): ?>
                    <div class="blog-item">
                        <h4><?php echo htmlspecialchars($blog['title']); ?></h4>
                        <p class="blog-meta">
                            <span class="status <?php echo $blog['status']; ?>"><?php echo ucfirst($blog['status']); ?></span>
                            <span class="date"><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                        </p>
                        <div class="blog-actions">
                            <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete_blog.php?id=<?php echo $blog['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="blogs.php" class="btn-view-all">View All Blog Posts</a>
            </div>

            <div class="dashboard-card">
                <h3>Recent Messages</h3>
                <div class="message-list">
                    <?php foreach ($messages as $message): ?>
                    <div class="message-item">
                        <h4><?php echo htmlspecialchars($message['name']); ?></h4>
                        <p class="message-email"><?php echo htmlspecialchars($message['email']); ?></p>
                        <p><?php echo substr(htmlspecialchars($message['message']), 0, 100); ?>...</p>
                        <span class="message-date"><?php echo date('M d, Y', strtotime($message['created_at'])); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="messages.php" class="btn-view-all">View All Messages</a>
            </div>
        </div>
    </div>
</body>
</html>
