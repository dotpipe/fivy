<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'user');
define('DB_USER', 'usnamed');
define('DB_PASSWORD', 'RTYfGhVbN!3$');

// PDO connection
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Function to get user details by ID
function getUserById($userId) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get today's page
function getTodayPage() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE created_at = " . date("Y-m-d", time()));
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to get like count for a page
function getLikeCount($page) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE page = :page");
    $stmt->bindParam(':page', $page);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Function to get dislike count for a page
function getDislikeCount($page) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM dislikes WHERE page = :page");
    $stmt->bindParam(':page', $page);
    $stmt->execute();
    return $stmt->fetchColumn();
}
?>
