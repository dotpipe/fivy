<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';
require_once './vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once './vendor/phpmailer/phpmailer/src/Exception.php';
require_once './vendor/phpmailer/phpmailer/src/SMTP.php';

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
    $username = $user['username'];

    // Generate a 5-digit reset code
    $resetCode = rand(10000, 99999);

    $stmt = $mysqli->prepare("UPDATE users SET code = ? WHERE id = ?");
    $stmt->bind_param("ii", $resetCode, $userId);
    $stmt->execute();
    $stmt->close();
    
    $mail = new PHPMailer();
    
    //$mail->SMTPDebug = 3;                               // Enable verbose debug output
    
    $mail->isSMTP();                                      // Set mailer to use SMTP
    // $mail->Host = 'localhost';  // Specify main and backup SMTP servers
    // $mail->SMTPAuth = true;                               // Enable SMTP authentication
    // $mail->Username = "ww";                 // SMTP username
    // $mail->Password = 'RTYfGhVbN!3$###';                           // SMTP password
    $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->setFrom('noreply@fivy.org', 'Fivy Support Desk');
    $mail->addAddress($email, $user['name']);
    $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
    $mail->isHTML(true);                                  // Set email format to HTML
    
    $mail->Subject = 'Fivy.org Password Reset Code';
    $mail->MsgHTML("Your password reset link is at https://fivy.org/fivy/reset_with_code.php?code=".$resetCode);
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
    if($mail->send()) {
        echo "Reset code sent to " . $userEmail;
    }
    else {
        echo 'Message could not be sent.';
        echo 'Mailer Error!: ' . $mail->ErrorInfo;
        return false;
    } 
    return true;
}
$error = "";
// Check if form is submitted
if (isset($_POST["email"]))
{
    // Get form data
    $email = ($_POST["email"]);
    // Create and send reset code
    if (sendResetCode($email) == false) {
        echo "Didn't work..";
        exit;
    }// else
     {
        // Email not found in database
        $error = $email . 'Email not found';
    }
    echo "Thank you...";
    // header('Location: ./login.php');
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