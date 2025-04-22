<?php
require_once('includes/db.php');

if (!isset($_GET['slug'])) {
    header("Location: blog.php");
    exit();
}

$slug = $_GET['slug'];

// Fetch blog post
$sql = "SELECT b.*, a.username as author_name 
        FROM blogs b 
        LEFT JOIN admins a ON b.author_id = a.id 
        WHERE b.slug = ? AND b.status = 'published'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: blog.php");
    exit();
}

$blog = $result->fetch_assoc();

// Fetch recent posts for sidebar
$sql = "SELECT title, slug, created_at 
        FROM blogs 
        WHERE status = 'published' 
        AND id != ? 
        ORDER BY created_at DESC 
        LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $blog['id']);
$stmt->execute();
$recent_posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - WebCraft Pro</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .blog-post-section {
            padding: 8rem 2rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .blog-post-container {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 2rem;
        }

        .blog-post-content {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .blog-post-header {
            margin-bottom: 2rem;
        }

        .blog-post-title {
            font-size: 2.5rem;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .blog-post-meta {
            display: flex;
            gap: 2rem;
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        .featured-image {
            width: 100%;
            max-height: 400px;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .featured-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .blog-post-body {
            line-height: 1.8;
            color: #374151;
        }

        .blog-post-body h2 {
            color: #1f2937;
            margin: 2rem 0 1rem;
        }

        .blog-post-body p {
            margin-bottom: 1.5rem;
        }

        .blog-sidebar {
            position: sticky;
            top: 2rem;
        }

        .sidebar-section {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .sidebar-section h3 {
            color: #1f2937;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .recent-posts {
            display: grid;
            gap: 1rem;
        }

        .recent-post-item {
            display: grid;
            gap: 0.5rem;
        }

        .recent-post-item a {
            color: #1f2937;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .recent-post-item a:hover {
            color: var(--primary-color);
        }

        .recent-post-date {
            color: #6b7280;
            font-size: 0.875rem;
        }

        @media (max-width: 968px) {
            .blog-post-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">WebCraft Pro</div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#portfolio">Portfolio</a></li>
                <li><a href="blog.php" class="active">Blog</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <section class="blog-post-section">
        <div class="blog-post-container">
            <article class="blog-post-content">
                <header class="blog-post-header">
                    <h1 class="blog-post-title"><?php echo htmlspecialchars($blog['title']); ?></h1>
                    <div class="blog-post-meta">
                        <span>By <?php echo htmlspecialchars($blog['author_name']); ?></span>
                        <span><?php echo date('F d, Y', strtotime($blog['created_at'])); ?></span>
                    </div>
                </header>

                <?php if (!empty($blog['image_url'])): ?>
                <div class="featured-image">
                    <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                </div>
                <?php endif; ?>

                <div class="blog-post-body">
                    <?php echo $blog['content']; ?>
                </div>
            </article>

            <aside class="blog-sidebar">
                <div class="sidebar-section">
                    <h3>Recent Posts</h3>
                    <div class="recent-posts">
                        <?php foreach ($recent_posts as $post): ?>
                        <div class="recent-post-item">
                            <a href="blog-post.php?slug=<?php echo $post['slug']; ?>">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                            <span class="recent-post-date">
                                <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> WebCraft Pro. All rights reserved.</p>
    </footer>
</body>
</html>
