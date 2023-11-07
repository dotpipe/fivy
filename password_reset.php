<?php
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION))
    session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

function sendResetCode($email)
{
    global $mysqli;

    // Prepare the SQL statement to fetch the user
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // Check if user exists
    if (!$user) {
        echo "User not found. Try retyping your email.";
        return false;
    }

    // Get the user's email and id
    $userEmail = $user['email'];
    $userId = $user['id'];

    // Generate a 5-digit reset code
    $resetCode = rand(10000, 99999);

    $stmt = $mysqli->prepare("UPDATE users SET code = ? WHERE id = ?");
    $stmt->bind_param("ii", $resetCode, $userId);
    $stmt->execute();
    $stmt->close();

    // Create the email content
    $subject = "Fivy.org Password Reset Code";
    $message = "Your password reset code is: " . $resetCode;
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: <noreply@fivy.org>' . "\r\n";

    // Send the email
    if (mail($userEmail, $subject, $message, $headers)) {
        echo "Reset code sent to " . $userEmail;
    } else {
        echo "Failed to send reset code.";
        return false;
    }

    return true;
}
$error = "";
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    // Get form data
    $email = $_POST['email'];

    // Create and send reset code
    if (sendResetCode($email)) {
        header('Location: reset_with_code.php');
        exit;
    } else {
        // Email not found in database
        $error = $email . 'Email not found';
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Password Reset</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
</head>

<body>
    <header>
        <h1>Password Reset</h1>
    </header>
    <main>
        <form action="password_reset.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <button type="submit">Reset Password</button>
            </div>
            <?php if (isset($error) && $error != "") { ?>
                <div class="error">
                    <?php echo $error; ?>
                </div>
            <?php } ?>
        </form>
    </main>
</body>

</html>