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

$gptick = (isset($_POST['page'])) ? $_POST['page'] : $_GET['page'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Start Looking Different</title>
    <link rel="stylesheet" type="text/css" href="./style.css">
    <link rel="shortcut icon" href="smallleaf.png">
    <script src="pipes.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
    </script>
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
                    <form method="GET" action="dashboard.php">
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
                        if ($_SESSION['paid'] == 1) { ?>
                            <pipe id="info" class="pipe" ajax="./btc.php?page=<?= $_SESSION['page']; ?>" insert="info"></pipe>
                </td>
            <?php } else { ?>
                <td style="background-color:white">
                    <canvas id="stockChart" style="width:100%;max-width:700px"></canvas>
                    <div id='tradingpitpresent'></div><br>
                    <div id='tradingpitfuture'></div><br>
                    <canvas id="stockChartPresent" style="width:100%;max-width:700px"></canvas>
                    <canvas id="stockChartFuture" style="max-width:500px"></canvas>
                    <div id='stockInfo' style="display:block"></div>
                    <pipe id='tradingpit' ajax='./chartbtc.php?page=<?= $gptick ?>' insert='stockInfo'></pipe>

                </td>
            <?php } ?>
        </tr>
    </table>
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