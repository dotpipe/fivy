<?php
require_once 'config.php';
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $resetCode = $_GET['resetCode'];
    $newPassword = $_GET['newPassword'];
    $email = $_GET['resetEmail'];

    // Fetch the user from the database
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Check if the reset code matches
    if ($user['code'] == $resetCode) {
        // Update the user's password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $userId);
        $stmt->execute();
        $stmt->close();

        echo "Password updated successfully!";
    } else {
        echo "Invalid reset code.";
    }
}
?>

<form method="GET">
    <label for="resetCode">Reset Code:</label>
    <input type="hidden" id="resetCode" name="resetCode" value='<?= $_GET['code'] ?>' required>
    <label for="newPassword">New Password:</label>
    <input type="password" id="newPassword" name="newPassword" required>
    <button type="submit">Reset Password</button>
</form>