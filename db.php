<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'user');
define('DB_USER', 'usnamed');
define('DB_PASSWORD', 'RTYfGhVbN!3$');

// MySQLi connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Function to create user in database
function createUser($name, $username, $password, $email, $phone)
{
    global $mysqli;
    // Generate a random salt
    $salt = password_hash($password, PASSWORD_DEFAULT);

    // Hash the password with the salt
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['salt' => $salt]);

    $stmt = $mysqli->prepare("INSERT INTO users (name, username, password, email, phone) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $username, $hashedPassword, $email, $phone);
    $stmt->execute();
    $stmt->close();
}

// Function to get user by username
function getUserByUsername($username)
{
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
function updateUserPassword($userId, $password)
{
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $password, $userId);
    $stmt->execute();
    $stmt->close();
}

// Function to create password reset code
function createPasswordResetCode($userId, $code)
{
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO password_reset (user_id, code) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $code);
    $stmt->execute();
    $stmt->close();
}

// Function to get user by password reset code
function getUserByPasswordResetCode($code)
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM password_reset WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Function to delete password reset code
function deletePasswordResetCode($code)
{
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM password_reset WHERE code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $stmt->close();
}

// Function to create like for a page
function createLike($page)
{
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO likes (page) VALUES (?)");
    $stmt->bind_param("s", $page);
    $stmt->execute();
    $stmt->close();
}

// Function to create dislike for a page
function createDislike($page)
{
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO dislikes (page) VALUES (?)");
    $stmt->bind_param("s", $page);
    $stmt->execute();
    $stmt->close();
}

// // Function to get like count for a page
// function getLikeCount($page) {
//     global $mysqli;
//     $stmt = $mysqli->prepare("SELECT COUNT(*) FROM likes WHERE page = ?");
//     $stmt->bind_param("s", $page);
//     $stmt->execute();
//     $stmt->bind_result($count);
//     $stmt->fetch();
//     $stmt->close();
//     return $count;
// }

// // Function to get dislike count for a page
// function getDislikeCount($page) {
//     global $mysqli;
//     $stmt = $mysqli->prepare("SELECT COUNT(*) FROM dislikes WHERE page = ?");
//     $stmt->bind_param("s", $page);
//     $stmt->execute();
//     $stmt->bind_result($count);
//     $stmt->fetch();
//     $stmt->close();
//     return $count;
// }

// Function to get page by directory
function getPageByDirectory($directory)
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM pages WHERE directory = ?");
    $stmt->bind_param("s", $directory);
    $stmt->execute();
    $result = $stmt->get_result();
    $page = $result->fetch_assoc();
    $stmt->close();
    return $page;
}

// Function to get all pages
function getAllPages()
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM pages");
    $stmt->execute();
    $result = $stmt->get_result();
    $pages = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $pages;
}

// Function to get paid status for a user
function getPaidStatus($userId)
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT paid FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($paid);
    $stmt->fetch();
    $stmt->close();
    return $paid;
}

// Function to update paid status for a user
function updatePaidStatus($userId, $paid)
{
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE users SET paid = ? WHERE id = ?");
    $stmt->bind_param("ii", $paid, $userId);
    $stmt->execute();
    $stmt->close();
}

// Function to get payment history for a user
function getPaymentHistory($userId)
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM payment_history WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $history;
}

// Function to add payment to history for a user
function addPaymentToHistory($userId, $amount)
{
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO payment_history (user_id, amount) VALUES (?, ?)");
    $stmt->bind_param("id", $userId, $amount);
    $stmt->execute();
    $stmt->close();
}
?>