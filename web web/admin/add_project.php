<?php
session_start();
require_once('../includes/db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $technologies = $_POST['technologies'];
    
    // Handle file upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . $_FILES['image']['name'];
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = 'uploads/' . $file_name;
        }
    }
    
    $sql = "INSERT INTO projects (title, description, technologies, image_url) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $title, $description, $technologies, $image_url);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error adding project: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project - WebCraft Pro</title>
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
            <h2>Add New Project</h2>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="dashboard-card">
            <form method="POST" enctype="multipart/form-data" class="project-form">
                <div class="form-group">
                    <label for="title">Project Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="technologies">Technologies Used</label>
                    <input type="text" id="technologies" name="technologies" placeholder="e.g., HTML, CSS, JavaScript, PHP">
                </div>

                <div class="form-group">
                    <label for="image">Project Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>

                <button type="submit" class="btn-add">Add Project</button>
                <a href="dashboard.php" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
