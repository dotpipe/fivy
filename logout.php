<?php
    // Start or resume the session
    if (!isset($_SESSION))
    session_start();
    
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to the desired page
    header('Location: login.php');
    exit;
?>