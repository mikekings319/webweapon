<?php
session_start();
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebCraft Pro - Professional Web Development Services</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Web weapon</div>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#portfolio">Portfolio</a></li>
                <li><a href="#blogs">blogs</a></li>
                <li><a href="#reviews">reviews</a></li>
                <li><a href="#projects">Projects</a></li>
                <li><a href="#payments">Payments</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="admin/login.php">Admin Login</a></li>
            </ul>
        </nav>
    </header>

    <section id="home" class="hero">
        <div class="hero-content">
        <h1>WEB WEAPON</h1>
            <h2>Transforming Ideas into Digital Reality</h2>
            <p>Professional web development solutions tailored to your needs</p>
            <a href="#contact" class="cta-button">Get Started</a>
        </div>
    </section>
    <section id="portfolio" class="portfolio">
        <div class="portfolio-content">
        <?php
        // Developer information
        $dev = [
            'name' => 'mikekings ',
            'title' => 'Senior Web Developer',
            'bio' => 'Experienced web developer with expertise in PHP, JavaScript, and modern web technologies.',
            'email' => 'mikekingsk@gmail.com',
            'phone' => '+254718874119',
            'address' => 'remote address',
            'resume_link' => 'assets/resume.pdf'
        ];
        ?>
            <h2><?php echo htmlspecialchars($dev['name']); ?></h2>
            <h4><?php echo htmlspecialchars($dev['title']); ?></h4>
            <p><?php echo htmlspecialchars($dev['bio']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($dev['email']); ?> | <strong>Phone:</strong> <?php echo htmlspecialchars($dev['phone']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($dev['address']); ?></p>
            <a href="<?php echo htmlspecialchars($dev['resume_link']); ?>" target="_blank" class="resume-btn">Download Resume</a>
        </div>
    </section>
    <section id="blogs" class="blogs" style="background-color:rgb(13, 13, 13); padding: 4rem 0;">
        <div class="blogs-content" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h2 style="text-align: center; color:rgb(211, 6, 34); font-size: 2.5rem; margin-bottom: 3rem; font-weight: 700;">📝 Latest Blog Posts</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <?php
            // Fetch latest 3 blog posts
            $result = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC LIMIT 3");

            if ($result && $result->num_rows > 0):
                while ($blog = $result->fetch_assoc()):
                    $date = new DateTime($blog['created_at']);
            ?>
                <div class="blog-post" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <?php if (!empty($blog['image'])): ?>
                        <div style="height: 200px; overflow: hidden;">
                            <img src="<?php echo htmlspecialchars($blog['image']); ?>" alt="Blog Image" 
                                style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;"
                                onmouseover="this.style.transform='scale(1.1)'" 
                                onmouseout="this.style.transform='scale(1.0)'">
                        </div>
                    <?php endif; ?>
                    <div style="padding: 1.5rem;">
                        <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 0.5rem;">
                            <?php echo $date->format('M d, Y'); ?>
                        </div>
                        <h3 style="color: #2c3e50; font-size: 1.5rem; margin-bottom: 1rem; font-weight: 600;">
                            <?php echo htmlspecialchars($blog['title']); ?>
                        </h3>
                        <p style="color: #6c757d; line-height: 1.6; margin-bottom: 1.5rem;">
                            <?php echo htmlspecialchars(substr($blog['content'], 0, 150)); ?>...
                        </p>
                        <a href="blog.php?slug=<?php echo urlencode($blog['slug']); ?>" 
                           style="display: inline-block; padding: 0.5rem 1rem; background-color: #3498db; 
                                  color: white; text-decoration: none; border-radius: 5px; 
                                  transition: background-color 0.3s ease;" 
                           onmouseover="this.style.backgroundColor='#2980b9'" 
                           onmouseout="this.style.backgroundColor='#3498db'">Read More</a>
                    </div>
                </div>
            <?php
                endwhile;
            else:
                echo "<div style='text-align: center; padding: 2rem; color: #6c757d;'><p>No blog posts available yet.</p></div>";
            endif;
            ?>
            </div>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="all_blogs.php" style="display: inline-block; padding: 1rem 2rem; 
                                               background-color: #2c3e50; color: white; 
                                               text-decoration: none; border-radius: 5px; 
                                               font-weight: 600; transition: background-color 0.3s ease;" 
                   onmouseover="this.style.backgroundColor='#34495e'" 
                   onmouseout="this.style.backgroundColor='#2c3e50'">📚 View All Blogs</a>
            </div>
    </div>
</section>

    <section id="reviews" class="reviews">
        <h2>What our clients say</h2>
        <div class="reviews-grid">
            <?php
            //fectch reviews from data base
            ?>
        </div>
    </section>

    <section id="services" class="services">
        <h2>Our Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <i class="icon-responsive"></i>
                <h3>Responsive Design</h3>
                <p>Websites that look perfect on all devices</p>
            </div>
            <div class="service-card">
                <i class="icon-ecommerce"></i>
                <h3>E-Commerce Solutions</h3>
                <p>Custom online stores and shopping experiences</p>
            </div>
            <div class="service-card">
                <i class="icon-cms"></i>
                <h3>CMS Development</h3>
                <p>Easy-to-manage content systems</p>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <h2>Let's Work Together</h2>
        <div id="message-status" class="message-status" style="display: none;"></div>
        <form id="contact-form" action="process/contact.php" method="POST" class="contact-form">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your request" required></textarea>
            <button type="submit" class="submit-btn">Send Message</button>
        </form>
    </section>

    <script>
    document.getElementById('contact-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const statusDiv = document.getElementById('message-status');
        const formData = new FormData(form);
        
        // Disable submit button and show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Show status message
            statusDiv.textContent = data.message;
            statusDiv.className = 'message-status ' + (data.success ? 'success' : 'error');
            statusDiv.style.display = 'block';
            
            // Reset form if successful
            if (data.success) {
                form.reset();
            }
        })
        .catch(error => {
            statusDiv.textContent = 'An error occurred. Please try again.';
            statusDiv.className = 'message-status error';
            statusDiv.style.display = 'block';
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
            
            // Hide status message after 5 seconds
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 5000);
        });
    });
    </script>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> WebCraft Pro. All rights reserved.</p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
