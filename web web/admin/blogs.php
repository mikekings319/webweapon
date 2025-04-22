<?php
session_start();
require_once('../includes/db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch all blogs
$sql = "SELECT b.*, a.username as author_name 
        FROM blogs b 
        LEFT JOIN admins a ON b.author_id = a.id 
        ORDER BY b.created_at DESC";
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
    <title>Blog Management - WebCraft Pro Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin-dashboard">
    <nav class="admin-nav">
        <div class="admin-logo">WebCraft Pro - Admin</div>
        <div class="admin-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="projects.php">Projects</a>
            <a href="blogs.php" class="active">Blogs</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Blog Management</h2>
            <a href="add_blog.php" class="btn-add">Add New Blog Post</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <div class="blogs-grid">
            <?php foreach ($blogs as $blog): ?>
            <div class="blog-card">
                <div class="blog-image">
                    <?php if (!empty($blog['image_url'])): ?>
                        <img src="../<?php echo htmlspecialchars($blog['image_url']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                    <?php else: ?>
                        <div class="no-image">No Image</div>
                    <?php endif; ?>
                </div>
                <div class="blog-content">
                    <h3><?php echo htmlspecialchars($blog['title']); ?></h3>
                    <div class="blog-meta">
                        <span class="author">By <?php echo htmlspecialchars($blog['author_name']); ?></span>
                        <span class="date"><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                        <span class="status <?php echo $blog['status']; ?>"><?php echo ucfirst($blog['status']); ?></span>
                    </div>
                    <div class="blog-preview">
                        <?php echo substr(strip_tags($blog['content']), 0, 150) . '...'; ?>
                    </div>
                    <div class="blog-actions">
                        <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="btn-edit">Edit</a>
                        <a href="delete_blog.php?id=<?php echo $blog['id']; ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Are you sure you want to delete this blog post?')">Delete</a>
                        <a href="../blog.php?slug=<?php echo $blog['slug']; ?>" 
                           class="btn-view" 
                           target="_blank">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <style>
        .blogs-grid {
            display: grid;
            gap: 2rem;
            padding: 1rem;
        }
        
        .blog-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
        }
        
        .blog-image {
            width: 200px;
            background: #f3f4f6;
        }
        
        .blog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .no-image {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
        }
        
        .blog-content {
            padding: 1.5rem;
            flex: 1;
        }
        
        .blog-meta {
            display: flex;
            gap: 1rem;
            color: #6b7280;
            font-size: 0.875rem;
            margin: 0.5rem 0 1rem;
        }
        
        .status {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
        }
        
        .status.draft {
            background: #f3f4f6;
            color: #374151;
        }
        
        .status.published {
            background: #dcfce7;
            color: #166534;
        }
        
        .blog-preview {
            color: #4b5563;
            margin-bottom: 1rem;
        }
        
        .blog-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-view {
            background: #6b7280;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .blog-card {
                flex-direction: column;
            }
            
            .blog-image {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</body>
</html>
