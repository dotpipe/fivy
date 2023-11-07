<?php
require_once 'config.php';
require_once 'db.php';
// 

if (!isset($_SESSION))
    session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ./dashboard.php');
    exit;
}

// Get user details from database
$user = getUserById($_SESSION['user_id']);

// Get today's page
$todayPage = getTodayPage();

// Get like and dislike counts for today's page
$likeCount = getLikeCount($_SESSION['page']);
$dislikeCount = getDislikeCount($_SESSION['page']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Fivy</title>
    <link rel="stylesheet" type="text/css" href="style.css">
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>

<script>
	const handler = Plaid.create({
	  token: '<?= session_id(); ?>',
	  onSuccess: (public_token, metadata) => {},
	  onLoad: () => {},
	  onExit: (err, metadata) => {},
	  onEvent: (eventName, metadata) => {},
	  //required for OAuth; if not using OAuth, set to null or omit:
	  //receivedRedirectUri: window.location.href,
	});
</script>

</head>
<body>
    <header>
        <h1>Welcome, <?php echo $user['username']; ?>!</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="today.php">Today's Page</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2><?php echo $todayPage['headline']; ?></h2>
        <p><?php echo $todayPage['content']; ?></p>
        <div class="like-dislike">
            <form action="like.php" method="post">
                <input type="hidden" name="page" value="<?php echo $_SESSION['page']; ?>">
                <button type="submit">Like (<?php echo $likeCount; ?>)</button>
            </form>
            <form action="dislike.php" method="post">
                <input type="hidden" name="page" value="<?php echo $_SESSION['page']; ?>">
                <button type="submit">Dislike (<?php echo $dislikeCount; ?>)</button>
            </form>
        </div>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Fivy. All rights reserved.</p>
    </footer>
</body>
</html>
