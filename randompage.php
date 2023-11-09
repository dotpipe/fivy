<?php
session_start();
$json = file_get_contents('all_tickers/nasdaq.json');
$data = json_decode($json, true);
srand(time());
$randomIndex = rand(0, count($data) - 1);
$randomEntry = $data[$randomIndex];
$_SESSION['page'] = $randomEntry['Symbol'];
$page = $randomEntry['Symbol'];

header("Location: ./dashboard.php?page=$page");
?>