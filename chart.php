<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
</script>
<script src="pipes.js"></script>

<canvas id="stockChart" style="width:100%;max-width:700px"></canvas>

<div id='tradingpitpresent'></div>
	<div id='tradingpitfuture'></div><br>
	<canvas id="stockChartPresent" style="width:100%;max-width:700px"></canvas>
	<canvas id="stockChartFuture"
		style="position:absolute;margin-left:800px;margin-top:-345px;width:100%;max-width:700px"></canvas>
	<div id='stockInfo' style="display:none"></div>
<pipe id='tradingpit' ajax='./chartbtc.php?page=<?= $_SESSION['page'] ?>' insert='stockInfo'></pipe>
<div style='display:none' id='stockInfo'></div>

<script>

function getChartPresent() {
		document.getElementById("stockChartPresent").textContent = "";
		document.getElementById("stockChartFuture").textContent = "";
		var dates = new Date(Date.now());
		var chartDates = new Array();
		var days = [ "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat", "Sun"];

		for (i = dates.getDay() ; (i) > dates.getDay() - 21; i--)
		{
			if (i % 7 != 5 && i % 7 != 6)
			{
				chartDates.push(days[Math.abs(7 - i) % 5]);
			}
			else
				i--;
		}
		var delay = 4000;
		var arg = <?= $_GET['page'] ?>;// document.getElementById("tickinput").value;
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
				for (i = dates.getDay() ; (i) < dates.getDay() + 21 ; i++)
				{
					if (i % 7 != 5 && i % 7 != 6)
					{
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
