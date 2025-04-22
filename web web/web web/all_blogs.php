<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Blog Posts - WebCraft Pro</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .blogs-container {
            background-color: rgb(13, 13, 13);
            min-height: 100vh;
            padding: 2rem;
        }
        .blogs-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .page-title {
            text-align: center;
            color: rgb(211, 6, 34);
            font-size: 2.5rem;
            margin-bottom: 2rem;
            font-weight: 700;
        }
        .blogs-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            margin-top: 1rem;
        }
        .blogs-table th {
            background-color: rgb(211, 6, 34);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
        }
        .blogs-table tr {
            background: rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .blogs-table tr:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.15);
        }
        .blogs-table td {
            padding: 1rem;
            color: #fff;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .blogs-table td:first-child {
            border-left: 1px solid rgba(255, 255, 255, 0.1);
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .blogs-table td:last-child {
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        .read-more-btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: rgb(211, 6, 34);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .read-more-btn:hover {
            background-color: rgb(180, 5, 29);
        }
        .back-btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 1rem;
            transition: background-color 0.3s ease;
        }
        .back-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .pagination {
            margin-top: 2rem;
            text-align: center;
        }
        .pagination a {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .pagination a:hover {
            background-color: rgb(211, 6, 34);
        }
        .pagination .active {
            background-color: rgb(211, 6, 34);
        }
    </style>
</head>
<body>
    <div class="blogs-container">
        <div class="blogs-wrapper">
            <a href="index.php" class="back-btn">← Back to Home</a>
            <h1 class="page-title">📚 All Blog Posts</h1>
            
            <?php
            // Pagination settings
            $posts_per_page = 10;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $posts_per_page;

            // Get total number of posts
            $total_query = $conn->query("SELECT COUNT(*) as count FROM blogs");
            $total_posts = $total_query->fetch_assoc()['count'];
            $total_pages = ceil($total_posts / $posts_per_page);

            // Fetch posts for current page
            $query = "SELECT * FROM blogs ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $posts_per_page, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <table class="blogs-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Preview</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($blog = $result->fetch_assoc()): 
                            $date = new DateTime($blog['created_at']);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($blog['title']); ?></td>
                            <td><?php echo $date->format('M d, Y'); ?></td>
                            <td><?php echo htmlspecialchars(substr($blog['content'], 0, 100)) . '...'; ?></td>
                            <td>
                                <a href="blog.php?slug=<?php echo urlencode($blog['slug']); ?>" 
                                   class="read-more-btn">Read More</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No blog posts available yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" 
                       class="<?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>">Next</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
