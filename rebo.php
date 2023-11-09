<?php
if (!isset($_SESSION))
    session_start();

$_SESSION['page3'] = $_GET['page'];

header("Location: ./dashboard.php?page=". $_SESSION["page3"]);
?>