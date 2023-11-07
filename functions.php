<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION))
    session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get page from form data
    $_SESSION['page'] = (isset($_POST['page'])) ? $_POST['page'] : "";
    $page = $_SESSION['page'];
    if (isset($_POST['like']) && $_POST['like'] > 0) {
        // Check if user has already disliked the page
        if (hasLikedPage($_SESSION['user_id'], $page)) {
            removeLike($_SESSION['user_id'], $page);
        }

        if (hasUserDislikedPage($_SESSION['user_id'], $page)) {
            removeDislike($_SESSION['user_id'], $page);
        }

        if ($page != "") {
            // Add like to database
            addLike($_SESSION['user_id'], $page);
        }
    } else if (isset($_POST['like']) && $_POST['like'] === 0) {
        // Check if user has already liked the page
        if (hasLikedPage($_SESSION['user_id'], $page)) {
            // User has already liked the page, redirect back to today's page
            removeLike($_SESSION['user_id'], $page);
        }
        if ($page != "") {
            // Add like to database
            addDisLike($_SESSION['user_id'], $page);
        }   
    }
}

// Function to check if user has already disliked the page
function hasUserDislikedPage($userId, $page)
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM dislikes WHERE user_id = ? AND page = ?");
    $stmt->bind_param("is", $userId, $page);
    $stmt->execute();
    $result = $stmt->get_result();
    $disliked = $result->fetch_assoc();
    $stmt->close();
    return $disliked;
}

// Function to add dislike to database
function addDislike($userId, $page)
{
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO dislikes (user_id, page) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $page);
    $stmt->execute();
    $stmt->close();
}

// Function to add dislike to database
function removeDislike($userId, $page)
{
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM dislikes WHERE user_id = ? AND page = ?");
    $stmt->bind_param("is", $userId, $page);
    $stmt->execute();
    $stmt->close();
}

// Function to check if user has already liked the page
function hasLikedPage($userId, $page)
{
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT * FROM likes WHERE user_id = ? AND page = ?");
    $stmt->bind_param("is", $userId, $page);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->num_rows > 0;
}

// Function to add like to database
function addLike($userId, $page)
{
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO likes (user_id, page) VALUES (?, ?)");
    $stmt->bind_param("is", $userId, $page);
    $stmt->execute();
    $stmt->close();
}

// Function to add dislike to database
function removeLike($userId, $page) {
    global $mysqli;
    $stmt = $mysqli->prepare("DELETE FROM likes WHERE user_id = ? AND page = ?");
    $stmt->bind_param("is", $userId, $page);
    $stmt->execute();
    $stmt->close();
}

// function sendResetCode($email) {
//     global $mysqli;

//     // Prepare the SQL statement to fetch the user
//     $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
//     $stmt->bind_param("s", $email);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $user = $result->fetch_assoc();
//     $stmt->close();

//     // Check if user exists
//     if (!$user) {
//         echo "User not found. Try retyping your email.";
//         return false;
//     }

//     // Get the user's email and id
//     $userEmail = $email;
//     $userId = $user['id'];

//     // Generate a 5-digit reset code
//     $resetCode = rand(10000, 99999);

//     // $stmt = $mysqli->prepare("UPDATE users SET code = ? WHERE id = ?");
//     // $stmt->bind_param("ii", $resetCode, $userId);
//     // $stmt->execute();
//     // $stmt->close();

//     // Create the email content
//     $subject = "Fivy.org Password Reset Code";
//     $message = "Your password reset code is: " . $resetCode;
//     $headers = "From: noreply@fivy.org";

//     // Send the email
//     if (mail($userEmail, $subject, $message, $headers)) {
//         echo "Reset code sent to " . $userEmail;
//     } else {
//         echo "Failed to send reset code.";
//         return false;
//     }

//     return true;
// }

?>