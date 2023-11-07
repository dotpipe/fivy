<?php
require_once 'config.php';
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resetCode = $_POST['resetCode'];
    $newPassword = $_POST['newPassword'];
    $email = $_POST['resetEmail'];

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

<form method="POST">
    <label for="resetEmail">Email:</label>
    <input type="text" id="resetEmail" name="resetEmail" required>
    <input type="hidden" name="userId" value="<?= $_GET['userId'] ?>">
    <label for="resetCode">Reset Code:</label>
    <input type="text" id="resetCode" name="resetCode" required>
    <label for="newPassword">New Password:</label>
    <input type="password" id="newPassword" name="newPassword" required>
    <button type="submit">Reset Password</button>
</form>