<?php
require_once 'db.php';

$db = Database::getInstance();
$categories = $db->getCategories();
$featuredArticles = $db->getFeaturedArticles(4);
$breakingNews = $db->getBreakingNews(3);
$latestArticles = $db->getLatestArticles(8);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CNN Clone - Breaking News, Latest News and Videos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, #cc0000, #ff0000);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-menu a:hover {
            background-color: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        /* Breaking News Banner */
        .breaking-banner {
            background: linear-gradient(90deg, #ff4444, #cc0000);
            color: white;
            padding: 0.5rem 0;
            overflow: hidden;
        }

        .breaking-content {
            display: flex;
            align-items: center;
            animation: scroll 30s linear infinite;
        }

        .breaking-label {
            background: white;
            color: #cc0000;
            padding: 0.3rem 0.8rem;
            font-weight: bold;
            margin-right: 1rem;
            border-radius: 3px;
            flex-shrink: 0;
        }

        @keyframes scroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        /* Main Content */
        .main-content {
            padding: 2rem 0;
        }

        .featured-section {
            margin-bottom: 3rem;
        }

        .section-title {
            font-size: 2rem;
            color: #cc0000;
            margin-bottom: 1.5rem;
            border-bottom: 3px solid #cc0000;
            padding-bottom: 0.5rem;
        }

        .featured-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .main-featured {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

        .main-featured:hover {
            transform: translateY(-5px);
        }

        .main-featured img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .featured-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 2rem;
        }

        .featured-category {
            background: #cc0000;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 0.5rem;
        }

        .featured-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .featured-excerpt {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .side-featured {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .side-article {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .side-article:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .side-article img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .side-content h3 {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .side-meta {
            font-size: 0.7rem;
            color: #666;
        }

        /* Latest News Grid */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .news-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .news-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .news-card-content {
            padding: 1.5rem;
        }

        .news-card-category {
            background: #007bff;
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 3px;
            font-size: 0.7rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 0.8rem;
        }

        .news-card-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.8rem;
            color: #333;
            line-height: 1.4;
        }

        .news-card-excerpt {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .news-card-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 0.8rem;
        }

        /* Footer */
        .footer {
            background: #333;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            color: #cc0000;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: white;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
                gap: 1rem;
            }

            .featured-grid {
                grid-template-columns: 1fr;
            }

            .main-featured img {
                height: 250px;
            }

            .side-article {
                flex-direction: column;
            }

            .side-article img {
                width: 100%;
                height: 150px;
            }

            .news-grid {
                grid-template-columns: 1fr;
            }

            .logo {
                font-size: 2rem;
            }
        }

        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">CNN</a>
                <nav>
                    <ul class="nav-menu">
                        <li><a href="index.php">Home</a></li>
                        <?php foreach($categories as $category): ?>
                            <li><a href="#" onclick="navigateToCategory('<?php echo $category['slug']; ?>')"><?php echo $category['name']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Breaking News Banner -->
    <?php if(!empty($breakingNews)): ?>
    <div class="breaking-banner">
        <div class="container">
            <div class="breaking-content">
                <span class="breaking-label">BREAKING</span>
                <?php foreach($breakingNews as $news): ?>
                    <span onclick="navigateToArticle('<?php echo $news['slug']; ?>')" style="cursor: pointer; margin-right: 3rem;">
                        <?php echo $news['title']; ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Featured Section -->
            <section class="featured-section">
                <h2 class="section-title">Featured News</h2>
                <?php if(!empty($featuredArticles)): ?>
                <div class="featured-grid">
                    <div class="main-featured" onclick="navigateToArticle('<?php echo $featuredArticles[0]['slug']; ?>')">
                        <img src="<?php echo $featuredArticles[0]['image_url']; ?>" alt="<?php echo htmlspecialchars($featuredArticles[0]['title']); ?>">
                        <div class="featured-overlay">
                            <span class="featured-category"><?php echo $featuredArticles[0]['category_name']; ?></span>
                            <h3 class="featured-title"><?php echo htmlspecialchars($featuredArticles[0]['title']); ?></h3>
                            <p class="featured-excerpt"><?php echo truncateText($featuredArticles[0]['excerpt'], 120); ?></p>
                        </div>
                    </div>
                    <div class="side-featured">
                        <?php for($i = 1; $i < count($featuredArticles) && $i < 4; $i++): ?>
                        <div class="side-article" onclick="navigateToArticle('<?php echo $featuredArticles[$i]['slug']; ?>')">
                            <img src="<?php echo $featuredArticles[$i]['image_url']; ?>" alt="<?php echo htmlspecialchars($featuredArticles[$i]['title']); ?>">
                            <div class="side-content">
                                <h3><?php echo htmlspecialchars($featuredArticles[$i]['title']); ?></h3>
                                <div class="side-meta">
                                    <span><?php echo $featuredArticles[$i]['category_name']; ?></span> • 
                                    <span><?php echo timeAgo($featuredArticles[$i]['created_at']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </section>

            <!-- Latest News -->
            <section>
                <h2 class="section-title">Latest News</h2>
                <div class="news-grid">
                    <?php foreach($latestArticles as $article): ?>
                    <div class="news-card" onclick="navigateToArticle('<?php echo $article['slug']; ?>')">
                        <img src="<?php echo $article['image_url']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <div class="news-card-content">
                            <span class="news-card-category"><?php echo $article['category_name']; ?></span>
                            <h3 class="news-card-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                            <p class="news-card-excerpt"><?php echo truncateText($article['excerpt'], 100); ?></p>
                            <div class="news-card-meta">
                                <span><?php echo $article['author']; ?></span>
                                <span><?php echo timeAgo($article['created_at']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>CNN Clone</h3>
                    <p>Your trusted source for breaking news, latest updates, and in-depth analysis from around the world.</p>
                </div>
                <div class="footer-section">
                    <h3>Categories</h3>
                    <ul>
                        <?php foreach($categories as $category): ?>
                            <li><a href="#" onclick="navigateToCategory('<?php echo $category['slug']; ?>')"><?php echo $category['name']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>About</h3>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Navigation functions
        function navigateToArticle(slug) {
            window.location.href = 'article.php?slug=' + slug;
        }

        function navigateToCategory(slug) {
            window.location.href = 'category.php?category=' + slug;
        }

        // Smooth scrolling for internal links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading animation for navigation
        function showLoading() {
            const loading = document.querySelector('.loading');
            if (loading) {
                loading.style.display = 'block';
            }
        }

        // Add click animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.news-card, .side-article, .main-featured');
            cards.forEach(card => {
                card.addEventListener('mousedown', function() {
                    this.style.transform = 'scale(0.98)';
                });
                card.addEventListener('mouseup', function() {
                    this.style.transform = '';
                });
                card.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                });
            });
        });

        // Auto-refresh breaking news
        setInterval(function() {
            // This would typically fetch new breaking news via AJAX
            console.log('Checking for breaking news updates...');
        }, 60000); // Check every minute
    </script>
</body>
</html>
