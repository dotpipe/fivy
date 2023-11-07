<?php
require_once './config.php';
require_once './db.php';
require_once './functions.php';
require_once './vote.php';
// Start or resume the session
if (!isset($_SESSION))
    session_start();

// Get the current user's ID
$user_id = $_SESSION['user_id'];

// Retrieve the likes of the current user
$sql = "SELECT * FROM likes WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$likes = $stmt->fetchAll(PDO::FETCH_ASSOC);
$likeHTML = "";
// Display the likes
foreach ($likes as $like) {
    $likeHTML .= "<ul><a href='./dashboard.php?page=" . $like['page'] . "'>" . strtoupper($like['page']) . "</a></ul>";
}
$likeCount = getLikeCount($_SESSION['page']);
$dislikeCount = getDislikeCount($_SESSION['page']);
// Close the database connection
$pdo = null;
?>
<h1 style="color:lightgray">Welcome,
    <?php echo $_SESSION['username']; ?>!
</h1>
<nav>
    <ul>
        <?php if ($_SESSION['paid'] == "1") { ?>
            <li><a href="dashboard.php">Dashboard</a></li>
        <?php } else { ?>
            <li><a href="register.php">Register</a></li>
        <?php } ?>
        <li><a href="today.php">Today's Page</a></li>
        <li><a href="favorites.php">Favorites</a><br>
            <?= $likeHTML; ?>
        </li>
        <?php if ($_SESSION['username'] == "1") { ?>
            <li><a href="logout.php">Logout</a></li>
        <?php } else { ?>
            <li><a href="login.php">Login</a></li>
        <?php } ?>
    </ul>

    <?php
    if ($_SERVER['PHP_SELF'] == '/fivy/dashboard.php' && $_SESSION['paid'] == 1) { ?>
        <div class="like-dislike">
            <?php $page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page']; ?>
            <input id="l-page" class="form-like" type="hidden" name="page" value="<?= $page; ?>">
            <input id="l-type" class="form-like" type="hidden" name="like" value="1">
            <dyn id="l-submit" class="redirect" form-class="form-like" ajax="vote.php">
                <img style="width:75px;height:75px" src="thumbsup.png">
            </dyn>(
            <?= $likeCount; ?>)
            <input id="d-page" class="form-dislike" type="hidden" name="page" value="<?= $page; ?>">
            <input id="d-type" class="form-dislike" type="hidden" name="like" value="0">
            <dyn id="d-submit" class="redirect" form-class="form-dislike" ajax="vote.php">
                <img style="width:75px;height:75px" src="thumbsdown.png">
            </dyn>(
            <?= $dislikeCount; ?>)
        </div>
    <?php } ?>
</nav>