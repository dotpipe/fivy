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
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $user = getUserByUsername($username);

    $err = "";

    if (!$user)
        // Create user in database
        createUser($name, $username, $password, $email, $phone);
    else if ($user['username'] == $username)
        // Redirect to register page
        $err = "User exists with this username";
    else if ($user['email'] == $email)
        // Redirect to register page
        $err = "User exists with this email";
    else if ($user['phone'] == $phone)
        // Redirect to register page
        $err = "User exists with this phone number";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <link rel="icon" href="./smallleaf.png" type="image/x-icon">
</head>

<body>
    <header>
        <h1>Register</h1>
        <h4><?= $err; ?></h4>
        <nav>
            <ul>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <form action="register.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="phone">Phone:</label>
            <input type="tel" id="phone" name="phone" required>
            <button type="submit">Register</button>
        </form>
    </main>
    <footer>
        <p>&copy;
            <?php echo date('Y'); ?> Fivy. All rights reserved.
        </p>
    </footer>
</body>

</html>