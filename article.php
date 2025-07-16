<?php
require_once 'db.php';

$articleSlug = $_GET['slug'] ?? '';
if (empty($articleSlug)) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance();
$article = $db->getArticle($articleSlug);

if (!$article) {
    header('Location: index.php');
    exit;
}

// Update article views
$db->updateViews($article['id']);

// Get related articles
$relatedArticles = $db->getRelatedArticles($article['category_id'], $article['id'], 4);
$categories = $db->getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - CNN Clone</title>
    <meta name="description" content="<?php echo htmlspecialchars($article['excerpt']); ?>">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.7;
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

        /* Breadcrumb */
        .breadcrumb {
            background: white;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .breadcrumb-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #666;
        }

        .breadcrumb a {
            color: #cc0000;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        /* Main Content */
        .main-content {
            padding: 2rem 0;
        }

        .article-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 3rem;
        }

        .article-main {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .article-header {
            padding: 2rem 2rem 1rem;
        }

        .article-category {
            background: #cc0000;
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .article-title {
            font-size: 2.5rem;
            font-weight: bold;
            line-height: 1.3;
            margin-bottom: 1rem;
            color: #333;
        }

        .article-excerpt {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 1.5rem;
            font-style: italic;
            line-height: 1.6;
        }

        .article-meta {
            display: flex;
            align-items: center;
            gap: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #eee;
            font-size: 0.9rem;
            color: #666;
        }

        .article-author {
            font-weight: bold;
            color: #cc0000;
        }

        .article-image {
            width: 100%;
            margin-bottom: 2rem;
        }

        .article-image img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .article-content {
            padding: 0 2rem 2rem;
        }

        .article-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
        }

        .article-text p {
            margin-bottom: 1.5rem;
        }

        /* Sidebar */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .sidebar-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .sidebar-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #cc0000;
            margin-bottom: 1rem;
            border-bottom: 2px solid #cc0000;
            padding-bottom: 0.5rem;
        }

        .related-article {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .related-article:last-child {
            border-bottom: none;
        }

        .related-article:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .related-article img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .related-content h4 {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            color: #333;
            line-height: 1.4;
        }

        .related-meta {
            font-size: 0.8rem;
            color: #666;
        }

        /* Social Share */
        .social-share {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #eee;
        }

        .share-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            color: white;
        }

        .share-facebook {
            background: #3b5998;
        }

        .share-twitter {
            background: #1da1f2;
        }

        .share-linkedin {
            background: #0077b5;
        }

        .share-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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

            .article-layout {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .article-title {
                font-size: 2rem;
            }

            .article-header {
                padding: 1.5rem;
            }

            .article-content {
                padding: 0 1.5rem 1.5rem;
            }

            .logo {
                font-size: 2rem;
            }

            .social-share {
                flex-wrap: wrap;
            }

            .related-article {
                flex-direction: column;
            }

            .related-article img {
                width: 100%;
                height: 150px;
            }
        }

        /* Print Styles */
        @media print {
            .header, .sidebar, .footer, .social-share, .breadcrumb {
                display: none;
            }
            
            .article-layout {
                grid-template-columns: 1fr;
            }
            
            .article-main {
                box-shadow: none;
                border: 1px solid #ddd;
            }
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

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <div class="container">
            <div class="breadcrumb-content">
                <a href="index.php">Home</a>
                <span>›</span>
                <a href="#" onclick="navigateToCategory('<?php echo $article['category_slug']; ?>')"><?php echo $article['category_name']; ?></a>
                <span>›</span>
                <span><?php echo truncateText($article['title'], 50); ?></span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="article-layout">
                <article class="article-main">
                    <div class="article-header">
                        <span class="article-category"><?php echo $article['category_name']; ?></span>
                        <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                        <p class="article-excerpt"><?php echo htmlspecialchars($article['excerpt']); ?></p>
                        <div class="article-meta">
                            <span>By <span class="article-author"><?php echo $article['author']; ?></span></span>
                            <span><?php echo date('F j, Y \a\t g:i A', strtotime($article['created_at'])); ?></span>
                            <span><?php echo number_format($article['views'] + 1); ?> views</span>
                        </div>
                    </div>
                    
                    <div class="article-image">
                        <img src="<?php echo $article['image_url']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                    </div>
                    
                    <div class="article-content">
                        <div class="article-text">
                            <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                        </div>
                        
                        <div class="social-share">
                            <a href="#" class="share-btn share-facebook" onclick="shareOnFacebook()">Share on Facebook</a>
                            <a href="#" class="share-btn share-twitter" onclick="shareOnTwitter()">Share on Twitter</a>
                            <a href="#" class="share-btn share-linkedin" onclick="shareOnLinkedIn()">Share on LinkedIn</a>
                        </div>
                    </div>
                </article>

                <aside class="sidebar">
                    <?php if(!empty($relatedArticles)): ?>
                    <div class="sidebar-section">
                        <h3 class="sidebar-title">Related Articles</h3>
                        <?php foreach($relatedArticles as $related): ?>
                        <div class="related-article" onclick="navigateToArticle('<?php echo $related['slug']; ?>')">
                            <img src="<?php echo $related['image_url']; ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                            <div class="related-content">
                                <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                                <div class="related-meta">
                                    <span><?php echo $related['category_name']; ?></span> • 
                                    <span><?php echo timeAgo($related['created_at']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="sidebar-section">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul style="list-style: none;">
                            <?php foreach($categories as $category): ?>
                                <li style="margin-bottom: 0.8rem;">
                                    <a href="#" onclick="navigateToCategory('<?php echo $category['slug']; ?>')" 
                                       style="color: #666; text-decoration: none; font-weight: 500; transition: color 0.3s ease;"
                                       onmouseover="this.style.color='#cc0000'" 
                                       onmouseout="this.style.color='#666'">
                                        <?php echo $category['name']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </aside>
            </div>
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

        // Social sharing functions
        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, 'facebook-share', 'width=580,height=296');
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, 'twitter-share', 'width=550,height=235');
        }

        function shareOnLinkedIn() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent(document.title);
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, 'linkedin-share', 'width=550,height=235');
        }

        // Print article function
        function printArticle() {
            window.print();
        }

        // Add reading progress indicator
        window.addEventListener('scroll', function() {
            const article = document.querySelector('.article-text');
            if (article) {
                const articleTop = article.offsetTop;
                const articleHeight = article.offsetHeight;
                const windowHeight = window.innerHeight;
                const scrollTop = window.pageYOffset;
                
                const progress = Math.min(100, Math.max(0, 
                    ((scrollTop + windowHeight - articleTop) / articleHeight) * 100
                ));
                
                // You could add a progress bar here
                console.log('Reading progress:', Math.round(progress) + '%');
            }
        });

        // Add click animations
        document.addEventListener('DOMContentLoaded', function() {
            const relatedArticles = document.querySelectorAll('.related-article');
            relatedArticles.forEach(article => {
                article.addEventListener('mousedown', function() {
                    this.style.transform = 'scale(0.98) translateX(5px)';
                });
                article.addEventListener('mouseup', function() {
                    this.style.transform = 'translateX(5px)';
                });
                article.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                });
            });

            // Add smooth scrolling for internal links
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
        });

        // Add keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.history.back();
            }
        });
    </script>
</body>
</html>
