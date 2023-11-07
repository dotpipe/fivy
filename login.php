<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION))
    session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate form data
    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password';
    } else {
        // Get user from database
        $user = getUserByUsername($username);
        // Check if user exists and password is correct
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['paid'] = $user['paid'];
            $_SESSION['username'] = $user['username'];
            if (strtotime($user['paid_date']) < time()) {
                $_SESSION['paid'] = 0;
            }
            if ($_SESSION['paid'] != 1) {
                header('Location: today.php');
                exit;
            }
            // Redirect to dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <link rel="icon" href="./smallleaf.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Login</h1>
    </header>
    <table>
        <tr>
            <td class="information">
                <?php if (isset($error)) { ?>
                    <p class="error">
                        <?php echo $error; ?>
                    </p>
                <?php } ?>
                <form action="login.php" method="post">
                    <div>
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button style="color:black" type="submit">Login</button>
                </form>
                <span>Don't have an account? <a href="register.php">Register</a></span><br>
                <span>Forgot your password? <a href="password_reset.php">Reset Password</a></span>
            </td>
            <td rowspan='2'>
                <article class="information">
                    If you've been in the shares market you've been asking and paying for information for sometime now.
                    You've also been wondering how to get further information. Like how to gain insights. Let Fivy
                    handle that!
                    You're using an obsolete system if you're not choosing AI! We use an waveform algorithm. It's been
                    specially
                    designed over 2 years of work to capture almost everything in the distance. Up to 100% (eg BTC-USD).
                    So many stocks are here. We use the <a href="finance.yahoo.com">Yahoo! Finance</a> for our base
                    numbers.
                    The information is accurate. Always. And the further numbers are equally as good.<br><br> We call
                    on that information
                    every 5 days. And we keep it there so you see the element of surprise you're going to have forever.
                    <br><br>This information is so accurate. Almost perfect. We hope you'll take the 1-week challenge.
                    Just $35 per month for viewing unlimited stocks.
                    And that includes all stocks and crypto on Yahoo! Finance. We're waiting for you to grin larger
                    than you ever have.<br><br>
                    After the first wave of monthly user signs up, we'll be adding minute-to-minute accuracy. It's been
                    tested, and it works.
                    So if you're ready, get in and try it out. Just $35 per month for your first stock.<br>
                </article>
            </td>
        </tr>
    </table>
    <b class="footer">&copy;
        <?php echo date('Y'); ?> Fivy. All rights reserved.
    </b>
</body>

</html>