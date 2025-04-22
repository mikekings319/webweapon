<?php
require_once('includes/db.php');

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

// Get total number of published blogs
$sql = "SELECT COUNT(*) as total FROM blogs WHERE status = 'published'";
$result = $conn->query($sql);
$total_blogs = $result->fetch_assoc()['total'];
$total_pages = ceil($total_blogs / $per_page);

// Fetch published blogs with pagination
$sql = "SELECT b.*, a.username as author_name 
        FROM blogs b 
        LEFT JOIN admins a ON b.author_id = a.id 
        WHERE b.status = 'published' 
        ORDER BY b.created_at DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
$blogs = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - WebCraft Pro</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .blog-section {
            padding: 8rem 2rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .blog-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .blog-card:hover {
            transform: translateY(-5px);
        }

        .blog-image {
            width: 100%;
            height: 200px;
            background: #f3f4f6;
            position: relative;
        }

        .blog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .blog-content {
            padding: 1.5rem;
        }

        .blog-meta {
            display: flex;
            justify-content: space-between;
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .blog-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .blog-excerpt {
            color: #4b5563;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .read-more {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .read-more:hover {
            background: var(--secondary-color);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }

        .pagination a {
            padding: 0.5rem 1rem;
            background: white;
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .pagination a:hover,
        .pagination a.active {
            background: var(--primary-color);
            color: white;
        }

        .blog-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .blog-header h1 {
            font-size: 2.5rem;
            color: var(--text-color);
            margin-bottom: 1rem;
        }

        .blog-header p {
            color: #6b7280;
            max-width: 600px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .blog-grid {
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

    <section class="blog-section">
        <div class="blog-header">
            <h1>Our Blog</h1>
            <p>Stay updated with the latest web development trends, tips, and best practices.</p>
        </div>

        <div class="blog-grid">
            <?php foreach ($blogs as $blog): ?>
            <article class="blog-card">
                <?php if (!empty($blog['image_url'])): ?>
                <div class="blog-image">
                    <img src="<?php echo htmlspecialchars($blog['image_url']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>">
                </div>
                <?php endif; ?>
                <div class="blog-content">
                    <div class="blog-meta">
                        <span>By <?php echo htmlspecialchars($blog['author_name']); ?></span>
                        <span><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                    </div>
                    <h2 class="blog-title"><?php echo htmlspecialchars($blog['title']); ?></h2>
                    <div class="blog-excerpt">
                        <?php echo substr(strip_tags($blog['content']), 0, 150) . '...'; ?>
                    </div>
                    <a href="blog-post.php?slug=<?php echo $blog['slug']; ?>" class="read-more">Read More</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php echo $i === $page ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </section>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> WebCraft Pro. All rights reserved.</p>
    </footer>
</body>
</html>
