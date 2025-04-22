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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    
    // Create URL-friendly slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    // Handle file upload
    $image_url = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../uploads/blog/';
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
            $image_url = 'uploads/blog/' . $file_name;
        }
    }
    
    $sql = "UPDATE blogs SET title=?, slug=?, content=?, image_url=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $slug, $content, $image_url, $status, $blog_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Blog post updated successfully!";
        header("Location: blogs.php");
        exit();
    } else {
        $error = "Error updating blog post: " . $conn->error;
    }
}

// Fetch blog data
$sql = "SELECT * FROM blogs WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: blogs.php");
    exit();
}

$blog = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog Post - WebCraft Pro Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            height: 500,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | bold italic backcolor | \
                     alignleft aligncenter alignright alignjustify | \
                     bullist numlist outdent indent | removeformat | help'
        });
    </script>
</head>
<body class="admin-dashboard">
    <nav class="admin-nav">
        <div class="admin-logo">WebCraft Pro - Admin</div>
        <div class="admin-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="projects.php">Projects</a>
            <a href="blogs.php">Blogs</a>
            <a href="messages.php">Messages</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h2>Edit Blog Post</h2>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="dashboard-card">
            <form method="POST" enctype="multipart/form-data" class="blog-form">
                <div class="form-group">
                    <label for="title">Blog Title</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Featured Image</label>
                    <?php if (!empty($blog['image_url'])): ?>
                        <div class="current-image">
                            <img src="../<?php echo htmlspecialchars($blog['image_url']); ?>" alt="Current blog image" style="max-width: 200px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" accept="image/*">
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($blog['image_url']); ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="draft" <?php echo $blog['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $blog['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-edit">Update Blog Post</button>
                    <a href="blogs.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .blog-form {
            display: grid;
            gap: 1.5rem;
        }
        
        .form-group {
            display: grid;
            gap: 0.5rem;
        }
        
        .form-group label {
            font-weight: 500;
        }
        
        .form-group input[type="text"],
        .form-group select {
            padding: 0.75rem;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            font-size: 1rem;
        }
        
        .current-image {
            margin: 1rem 0;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .tox-tinymce {
            border-radius: 6px;
        }
    </style>
</body>
</html>
