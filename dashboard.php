<?php
require_once 'config.php';
require_once 'db.php';

if (!isset($_SESSION))
    session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user details from database
$user = getUserById($_SESSION['user_id']);
if ($user['paid'] == 1 && strtotime($user['paid_date']) >= time())
    $_SESSION['paid'] = 1;
else
    $_SESSION['paid'] = 0;

// Get today's page
// $todayPage = getTodayPage();

if (isset($_POST['page']) && $_POST['page'] != ""){
    $_SESSION['page'] = $_POST['page'];
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Fivy</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <link rel="icon" href="./smallleaf.png" type="image/x-icon">
    <script src="pipes.js"></script>
    <script
        src="https://www.paypal.com/sdk/js?client-id=AU3GPvXWfZx8F-s29cYDuURasyjwyBdiANh1eF-SMGnfqATPP2LbQ80URor91syyH_0tl2yPsOFP-_eK"></script>
</head>

<body>
    <table>
        <tr>
            <td style="align:top;width:200;margin-top:50">
                <?= include('menu.php'); ?>
            </td>
            <td>
                <article style="align:top;position:absolute;z-index:2;margin-left:200">
                    <form method="POST" action="dashboard.php">
                        <input type="text" name="page" placeholder="Enter Ticker here">
                        <button onclick>Look Up</button>
                        <?php
                        if ($_SESSION['paid'] != 1) { ?>
                            <div id="paypal-button-container-P-7HK63531FS910263RMVD55WI"></div>
                            <script
                                src="https://www.paypal.com/sdk/js?client-id=AdvDfbOhJIOM4hn3n9AfE1loBfADjY0GM8cFTJwWiat9bqoDY9zU64gmv0P7nWabg6TETsZ7paH-k2Ud&vault=true&intent=subscription"
                                data-sdk-integration-source="button-factory"></script>
                            <script>
                                paypal.Buttons({
                                    style: {
                                        shape: 'rect',
                                        color: 'gold',
                                        layout: 'vertical',
                                        label: 'subscribe'
                                    },
                                    createSubscription: function (data, actions) {
                                        return actions.subscription.create({
                                            /* Creates the subscription */
                                            plan_id: 'P-7HK63531FS910263RMVD55WI'
                                        });
                                    },
                                    onApprove: function (data, actions) {
                                        alert(data.subscriptionID); // You can add optional success message for the subscriber here
                                    }
                                }).render('#paypal-button-container-P-7HK63531FS910263RMVD55WI'); // Renders the PayPal button
                            </script>
                            <pipe id="info" class="pipe" ajax="./btc.php?g=oiei" insert="info"></pipe>
                            <?php
                        } else if (isset($_POST['page']) || isset($_GET['page'])) {
                            $page = isset($_POST['page']) ? $_POST['page'] : $_GET['page']; ?>
                                <pipe id="info" class="pipe" ajax="./btc.php?page=<?= $page; ?>" insert="info"></pipe>
                        <?php } ?>
                    </form>
                </article>
            </td>
            <?php if (isset($_SESSION['paid']) == 0) { ?>
            <td>
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
            <?php } ?>
        </tr>
    </table>
</body>

</html>