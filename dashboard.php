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

$gptick = (isset($_POST['page'])) ? $_POST['page'] : $_GET['page'];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Fivy</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <link rel="icon" href="./smallleaf.png" type="image/x-icon">
    <script src="pipes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
    </script>
    <script
        src="https://www.paypal.com/sdk/js?client-id=AU3GPvXWfZx8F-s29cYDuURasyjwyBdiANh1eF-SMGnfqATPP2LbQ80URor91syyH_0tl2yPsOFP-_eK"></script>
</head>

<body>
    <div class="container">
    <article class="menu">
        <?= include('menu.php'); ?>
    </article>
    <article class="prices" style="align:top;">
        <form method="POST" action="dashboard.php">
            <input type="text" name="page" placeholder="Enter Ticker here">
            <button onclick>Look Up</button>
            <?php
            if ($_SESSION['paid'] != 1) { ?>
                <div id="paypal-button-container-P-56U667816V9552243MVGDTNY"></div>
                <script
                    src="https://www.paypal.com/sdk/js?client-id=AU3GPvXWfZx8F-s29cYDuURasyjwyBdiANh1eF-SMGnfqATPP2LbQ80URor91syyH_0tl2yPsOFP-_eK&vault=true&intent=subscription"
                    data-sdk-integration-source="button-factory"></script>
                <script>
                    paypal.Buttons({
                        style: {
                            shape: 'pill',
                            color: 'white',
                            layout: 'horizontal',
                            label: 'paypal'
                        },
                        createSubscription: function (data, actions) {
                            return actions.subscription.create({
                                /* Creates the subscription */
                                plan_id: 'P-56U667816V9552243MVGDTNY'
                            });
                        },
                        onApprove: function (data, actions) {
                            alert(data.subscriptionID); // You can add optional success message for the subscriber here
                        }
                    }).render('#paypal-button-container-P-56U667816V9552243MVGDTNY'); // Renders the PayPal button
                </script>
                <?php
            }
            if (substr($gptick,-1) == '?') {
                $gptick = substr($gptick,0,-1);
            }
            // echo strtoupper(substr($_GET['page'],0,strlen($_GET['page']))) . " " .strtoupper($_SESSION['page3']);
            if ($_SESSION['paid'] == 1 && strtoupper($_SESSION['page3']) == $gptick) { ?>
                <pipe id="info" class="pipe" ajax="./btc.php?page=<?= $gptick; ?>" insert="info"></pipe>
            <?php } else {
                echo "<h2>Please join if you love the site so much!<br>It's really good at figuring this hedge out!</h2>";
            } ?>
        </form>
    </article>
    <?php if (isset($_SESSION['paid']) == 0) { ?>
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
            Just $1.99/week for viewing unlimited stocks.
            And that includes all stocks and crypto on Yahoo! Finance. We're waiting for you to grin larger
            than you ever have.<br><br>
            After the first wave of monthly user signs up, we'll be adding minute-to-minute accuracy. It's been
            tested, and it works.
            So if you're ready, get in and try it out. Just $1.99/week for your first stock.<br>
        </article>
    <?php } else { ?>
        <article class="chart">
            <canvas id="stockChart" style="max-width:500px"></canvas>
            <div id='tradingpitpresent'></div><br>
            <div id='tradingpitfuture'></div><br>
            <canvas id="stockChartPresent" style="max-width:500px"></canvas>
            <canvas id="stockChartFuture" style="max-width:500px"></canvas>
            <div id='stockInfo' style="display:block"></div>
            <pipe id='tradingpit' ajax='./chartbtc.php?page=<?= $gptick ?>' insert='stockInfo'></pipe>
        </article>
    <?php } ?>
    </div>
</body>

<script>

    function getChartPresent() {
        if (document.getElementById("stockChartPresent"))
        document.getElementById("stockChartPresent").textContent = "";
        if (document.getElementById("stockChartFuture"))
        document.getElementById("stockChartFuture").textContent = "";
        var dates = new Date(Date.now());
        var chartDates = new Array();
        var days = ["Mon", "Tues", "Wed", "Thurs", "Fri", "Sat", "Sun"];

        for (i = dates.getDay(); (i) > dates.getDay() - 21; i--) {
            if (i % 7 != 5 && i % 7 != 6) {
                chartDates.push(days[Math.abs(7 - i) % 5]);
            }
            else
                i--;
        }
        var delay = 4000;
        var arg = "<?= $gptick; ?>";// document.getElementById("tickinput").value;
        setTimeout(() => {
            var stockTag = document.getElementById("tradingpitpresent");

            stockTag.setAttribute("ajax", 'chart.php?page=' + arg);
            stockTag.setAttribute("insert", 'stockInfo');
            pipes(stockTag);
            const xValues = chartDates;
            let yValues = document.getElementById("stockInfo").textContent;
            console.log(yValues);
            try {

                yValues = JSON.parse(yValues);
                console.log(yValues);
                new Chart("stockChartPresent", {
                    type: "line",
                    data: {
                        labels: xValues,
                        datasets: [
                            {
                                label: "Real Price",
                                backgroundColor: "rgba(0,0,255,0.0)",
                                borderColor: "rgba(0,0,255,1)",
                                data: yValues["r"]["p"].slice(-21)
                            },
                            {
                                label: "Imaginary",
                                backgroundColor: "rgba(255,0,0,0.0)",
                                borderColor: "rgba(255,0,0,1)",
                                data: yValues["i"]["p"].slice(-21)
                            }
                        ]
                    },
                    options: {}
                });

                dates = new Date(Date.now());
                chartDates = new Array();
                for (i = dates.getDay(); (i) < dates.getDay() + 21; i++) {
                    if (i % 7 != 5 && i % 7 != 6) {
                        chartDates.push(days[Math.abs(i % 7)]);
                    }
                    else
                        i++;
                }
                new Chart("stockChartFuture", {
                    type: "line",
                    data: {
                        labels: chartDates,
                        datasets: [
                            {
                                label: "Estimated Future Gain/Loss",
                                backgroundColor: "rgba(0,0,255,0.0)",
                                borderColor: "rgba(0,0,255,1)",
                                data: yValues["r"]["f"].slice(-25)
                            },
                            {
                                label: "Algo. Movement of Gain/Loss",
                                backgroundColor: "rgba(255,0,0,0.0)",
                                borderColor: "rgba(255,0,0,1)",
                                data: yValues["i"]["f"].slice(-25)
                            }
                        ]
                    },
                    options: {}
                });
                document.getElementById("stockInfo").textContent = "";
            } catch (error) { console.log(error); getChartPresent(arg); };
        }, delay);
    }
    getChartPresent();
</script>

</html>