<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'dbajqviqkswolr');
define('DB_USER', 'ulnrcogla9a1t');
define('DB_PASS', 'yolpwow1mwr2');

class Database {
    private $connection;
    private static $instance = null;
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Get all categories
    public function getCategories() {
        $stmt = $this->connection->prepare("SELECT * FROM categories ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Get featured articles
    public function getFeaturedArticles($limit = 5) {
        $stmt = $this->connection->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            WHERE a.is_featured = 1 
            ORDER BY a.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    // Get breaking news
    public function getBreakingNews($limit = 3) {
        $stmt = $this->connection->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            WHERE a.is_breaking = 1 
            ORDER BY a.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    // Get articles by category
    public function getArticlesByCategory($categorySlug, $limit = 10) {
        $stmt = $this->connection->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            WHERE c.slug = ? 
            ORDER BY a.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$categorySlug, $limit]);
        return $stmt->fetchAll();
    }
    
    // Get single article
    public function getArticle($slug) {
        $stmt = $this->connection->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            WHERE a.slug = ?
        ");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    // Get latest articles
    public function getLatestArticles($limit = 10) {
        $stmt = $this->connection->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            ORDER BY a.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    // Update article views
    public function updateViews($articleId) {
        $stmt = $this->connection->prepare("UPDATE articles SET views = views + 1 WHERE id = ?");
        $stmt->execute([$articleId]);
    }
    
    // Get related articles
    public function getRelatedArticles($categoryId, $currentArticleId, $limit = 4) {
        $stmt = $this->connection->prepare("
            SELECT a.*, c.name as category_name, c.slug as category_slug 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            WHERE a.category_id = ? AND a.id != ? 
            ORDER BY a.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$categoryId, $currentArticleId, $limit]);
        return $stmt->fetchAll();
    }
}

// Helper functions
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    
    return date('M j, Y', strtotime($datetime));
}

function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}
?>
