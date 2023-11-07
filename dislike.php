<?php
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION))
    session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get page from form data
    $_SESSION['page'] = isset($_POST['page']) ? $_POST['page'] : "";
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
    header("Location: dashboard.php?page=$page");
    exit;
}

// Redirect back to today's page
header("Location: dashboard.php?page=$page");
exit;

?>