# Project Description

This project is a PHP-based password entry gateway that uses PDO for database connection. It also includes 2FA authorization, Plaid connections, and email functionality. The project uses a MySQLi database to store user information and a separate MySQLi database to store payment history. The project consists of several pages, including a user registration page, a login page, a password reset page, a dashboard page, a today's page, and like/dislike functionality for each page.

## File Structure

The project has the following file structure:

- index.php
- config.php
- db.php
- functions.php
- login.php
- register.php
- password_reset.php
- dashboard.php
- today.php
- like.php
- dislike.php
- ./
- pages/
- database/
- README.md

## Code Snippets

Here are some code snippets from the already completed files:

### index.php

```php
<?php
require_once 'config.php';
require_once 'db.php';


if (!isset($_SESSION))
    session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user details from database
$user = getUserById($_SESSION['user_id']);

// Get today's page
$todayPage = getTodayPage();

// Get like and dislike counts for today's page
$likeCount = getLikeCount($todayPage);
$dislikeCount = getDislikeCount($todayPage);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Fivy</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
</head>
<body>
    <header>
        <h1>Welcome, <?php echo $user['username']; ?>!</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="today.php">Todays Page</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2><?php echo $todayPage['headline']; ?></h2>
        <p><?php echo $todayPage['content']; ?></p>
        <div class="like-dislike">
            <form action="like.php" method="post">
                <input type="hidden" name="page" value="<?php echo $todayPage['directory']; ?>">
                <button type="submit">Like (<?php echo $likeCount; ?>)</button>
            </form>
            <form action="dislike.php" method="post">
                <input type="hidden" name="page" value="<?php echo $todayPage['directory']; ?>">
                <button type="submit">Dislike (<?php echo $dislikeCount; ?>)</button>
            </form>
        </div>
    </main>
    <footer>
        ...
    </footer>
</body>
</html>
```

### config.php

```php
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_username');
define('DB_PASSWORD', 'your_password');

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
    $stmt = $pdo->prepare("SELECT * FROM pages WHERE date = CURDATE()");
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
```

### db.php

```php
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_username');
define('DB_PASSWORD', 'your_password');

// MySQLi connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Function to create user in database
function createUser($name, $username, $password, $email, $phone) {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO users (name, username, password, email, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $username, $password, $email, $phone);
    $stmt->execute();
    $stmt->close();
}

// Function to get user by username
function getUserByUsername($username) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}
```

### functions.php

```php
<?php
require_once 'db.php';

// Function to create user in database
function createUser($name, $username, $password, $email, $phone) {
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO users (name, username, password, email, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $username, $password, $email, $phone);
    $stmt->execute();
    $stmt->close();
}

// Function to get user by username
function getUserByUsername($username) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Function to update user password
function updateUserPassword($userId, $password) {
    ...
}
```

### login.php

```php
<?php
require_once 'config.php';
require_once 'db.php';


if (!isset($_SESSION))
    session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    ...
}
```

### register.php

```php
<?php
require_once 'config.php';
require_once 'db.php';

...

