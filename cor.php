<?php
if ($_GET['s'] != "2" || $_GET['t'] != "dn323890nd018")
{
    exit;
}

if (!isset($_SESSION))
    session_start();

// Perform the database update
$servername = "localhost";
$username = "usnamed";
$password = "RTYfGhVbN!3$";
$dbname = "user";

// Create a new PDO instance
$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

// Prepare the SQL statement
$sql = "update users set paid = 1, paid_date = :ts where id = :id;";

$stmt = $pdo->prepare($sql);

// Bind the parameters
$now = date('Y-m-d H:i:s', time() + (60*60*24*31));

// Execute the update
$stmt->execute([':id' => $_SESSION['user_id'], ':ts' => $now]);

// Close the database connection
$pdo = null;

// Send a response back to the webhook provider
http_response_code(200);
echo "Webhook received and processed successfully.";

header("Location: ./dashboard.php")
?>