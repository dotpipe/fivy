<?php
require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

if (!isset($_SESSION))
    session_start();

if (!isset($_GET['page']))
    header("Location: randompage.php");

// Get user details from database
$user = getUserById($_SESSION['user_id']);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Start Looking Different</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <link rel="shortcut icon" href="smallleaf.png">
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
            <td rowspan='2'>
                <article style="align:top;position:absolute;z-index:2;margin-left:200">
                    <form method="post" action="dashboard.php">
                        <input type="text" name="page" placeholder="Enter Ticker here">
                        <button onclick>Look Up</button>
                        <?php
                        if ($_SESSION['paid'] != 1) { ?>
                            <div id="paypal-button-container-P-5GW667969T261650LMVD5GAA"></div>
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
                                            plan_id: 'P-5GW667969T261650LMVD5GAA',
                                            quantity: 1 // The quantity of the product for a subscription
                                        });
                                    },
                                    onApprove: function (data, actions) {
                                        alert(data.subscriptionID); // You can add optional success message for the subscriber here
                                    }
                                }).render('#paypal-button-container-P-5GW667969T261650LMVD5GAA'); // Renders the PayPal button
                            </script>
                            <!-- <pipe id="info" class="pipe" ajax="./btc.php?g=oiei" insert="info"></pipe> -->
                            <?php
                        } if (substr($_GET['page'],0,3) == substr($_SESSION['page'],0,3)) { ?>
                                <pipe id="info" class="pipe" ajax="./btc.php?page=<?= $_GET['page']; ?>" insert="info"></pipe>
                        <?php } else echo "<h2>Please join if you love the site so much!<br>It's really good at figuring this hedge out!</h2>"; ?>
                    </form>
                </article>
            </td>
        </tr>
    </table>
    <footer>
        <!-- Footer content goes here -->
    </footer>
</body>

</html>