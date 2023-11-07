<?php
require_once './config.php';
require_once './db.php';
require_once 'functions.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['like'])) {
    // Get page from form data
    $_SESSION['page'] = (isset($_POST['page'])) ? $_POST['page'] : $_GET['page'];
    $page = $_GET['page'];
    if (isset($_GET['like']) && $_GET['like'] == "1") {
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
    } else if (isset($_GET['like']) && $_GET['like'] == "0") {
        // Check if user has already liked the page
        if (hasLikedPage($_SESSION['user_id'], $page)) {
            // User has already liked the page, redirect back to today's page
            removeLike($_SESSION['user_id'], $page);
        }
        
        if (hasUserDislikedPage($_SESSION['user_id'], $page)) {
            removeDislike($_SESSION['user_id'], $page);
        }

        if ($page != "") {
            // Add like to database
            addDisLike($_SESSION['user_id'], $page);
        }   
    }
    header("Location: ./dashboard.php?page=$page");
}
// else header("Location: ./dashboard.php?page=$page");

