<?php
session_start();
$_SESSION['page '] = $_GET['page'];
unset($_GET);
header("Location: ./dashboard.php");
?>