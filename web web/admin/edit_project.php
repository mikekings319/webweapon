<?php
session_start();
require_once('../includes/db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Check if project ID is provided
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$project_id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $technologies = $_POST['technologies'];
    
    // Handle file upload
    $image_url = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . $_FILES['image']['name'];
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Delete old image if exists
            if (!empty($_POST['current_image'])) {
                $old_file = '../' . $_POST['current_image'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
            }
            $image_url = 'uploads/' . $file_name;
        }
    }
    
    $sql = "UPDATE projects SET title=?, description=?, technologies=?, image_url=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $title, $description, $technologies, $image_url, $project_id);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error updating project: " . $conn->error;
    }
}

// Fetch project data
$sql = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$project = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - WebCraft Pro</title>
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
            <h2>Edit Project</h2>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="dashboard-card">
            <form method="POST" enctype="multipart/form-data" class="project-form">
                <div class="form-group">
                    <label for="title">Project Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($project['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="technologies">Technologies Used</label>
                    <input type="text" id="technologies" name="technologies" value="<?php echo htmlspecialchars($project['technologies']); ?>" placeholder="e.g., HTML, CSS, JavaScript, PHP">
                </div>

                <div class="form-group">
                    <label for="image">Project Image</label>
                    <?php if (!empty($project['image_url'])): ?>
                        <div class="current-image">
                            <img src="../<?php echo htmlspecialchars($project['image_url']); ?>" alt="Current project image" style="max-width: 200px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($project['image_url']); ?>">
                </div>

                <button type="submit" class="btn-edit">Update Project</button>
                <a href="dashboard.php" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
