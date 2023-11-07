<?php
if (!isset($_SESSION))
    session_start();
class CNGN
{

    public function integral(array $sequence)
    {
        $length = array_sum($sequence);
        $avg_height = array_sum($sequence) / count($sequence);
        return ($length * $avg_height);
    }

    /**
     * 
     * Integrand ([[secant, y = base/min, height = base/max], [sec, y, high]])
     * 
     */
    public function find_integral(array $sequence)
    {
        $h = [];
        $sum = [];
        foreach ($sequence as $k => $v) {
            $midpoint = (int) $v[0] / 2;
            $incise = abs((int) $v[2] - (int) $v[1]);
            $perimeter = ($midpoint * 2) + ($incise * 2);
            $length = $perimeter / 2;
            $length += $incise / 2;
            $sum[] = $length;
            $h[] = (int) $v[2];
        }
        $integral = $this->integral($sum);
        return $integral;
    }


    /**
     * 
     * Integrand ([secant, y = base/min, height = base/max])
     * 
     */
    public function integrand(array $sequence)
    {
        $midpoint = $sequence[0] / 2;
        $incise = abs(intval($sequence[2]) - intval($sequence[1]));
        $perimeter = ($midpoint * 2) + ($incise * 2);
        $length = $perimeter / 2;
        $length += $incise / 2;
        $length--;
        return $length;
    }

    /**
     * 
     * Differential ([secant, y = base/min, height = base/max])
     * 
     */
    public function differential(array $sequence)
    {
        $midpoint = $sequence[0] / 2;
        $incise = abs(intval($sequence[2]) - intval($sequence[1]));
        $perimeter = ($midpoint * 2) + ($incise * 2);
        $length = $perimeter / 2;
        $length += $incise / 2;


        $midpoint = $sequence[0] / $length;
        $incise = abs(intval($sequence[2]) - intval($sequence[1]));
        $perimeter = ($midpoint * 2) + ($incise * 2);
        $length = $perimeter / 2;
        $length += $incise / 2;

        return $length;
    }

    /**
     * 
     * Derive ([secant, y = base/min, height = base/max])
     * 
     */
    public function derive(array $sequence)
    {
        $midpoint = $sequence[0] / $sequence[3];
        $incise = abs(intval($sequence[2]) - intval($sequence[1]));
        $perimeter = ($midpoint * 2) + ($incise * 2);
        $length = $perimeter / 2;
        $length += $incise / 2;
        return $sequence[3] / $length;
    }

    function bitcoin(string $btc_csv, int $day_cnt, $data_column = 1, $date_column = 0)
    {
        $uphtml = "<img src='arrowup.png' style='width:20px;height:20px'>";
        $downhtml = "<img src='arrowdown.png' style='width:20px;height:20px'>";
        $sf = fopen("$btc_csv", "r");
        $seq = [];
        $data = [];
        fgets($sf);
        fgets($sf);
        $day_before = 0;
        $date_1 = 0;
        $y = 1;
        $base = 0;
        while (($data = fgetcsv($sf, 300, ',', " ")) !== FALSE) {
            if ($y < 2) {
                $y += $day_cnt;
                $day_before = $data[$data_column];
                $day_before2 = $data[$data_column + 4];
                continue;
            }
            $date_1 = $day_before;
            $date_2 = $day_before2;
            $seq2[] = [$y, $date_2, $day_before2, $data[$date_column]];
            $seq[] = [$y, $date_1, $day_before, $data[$date_column]];
            $day_before = $data[$data_column];
            $day_before2 = $data[$data_column + 4];
            $y += $day_cnt;
        }
        fclose($sf);

        //            $string = "<table valign='top' style='background-color:white;z-index:1;position:absolute;width:100%;top-margin:0px;'>";
        $theaders = "<tr><td style='width:150px;margin-top:5px;'>Long Form Date (Asc.) </td><td> Real Open </td><td> Chase Open </td><td> Real Adj. Close </td><td> Chase Close </td></tr>";
        $y = 1;
        $vals = [];
        $x = 0;
        $exp = 1;
        $out = 1;
        $inc_real = 0;
        $inc_imaginary = 0;
        $inc_last = 0;
        $saved = 0;
        $correct1 = $correct2 = 0;
        $key = [];
        $tempseq = $seq;
        $tempseq2 = $seq2;

        $j = 0;
        for ($i = count($seq) - 1; $i >= 0; $i--) {
            $j = 0;
            $string[$i] = "<tr>";
            
            while ($j < 2) {
                $key = $seq[$i];
                $vals = $key;
                $inc_real = $vals[1];
                array_pop($vals);
                $vals[] = $this->integrand($key);
                $c = $this->differential($key);
                $real = "";
                $bool1 = $downhtml;
                if ($j == 0 && ($inc_real) > $inc_last) {
                    $real = "<td>$uphtml" . $seq[$i][1] . "</td>";
                    $bool1 = "$uphtml";
                } else if ($j == 0)
                    $real = "<td>$downhtml" . $seq[$i][1] . "</td>";
                if ($j == 1 && ($inc_real) > $inc_last) {
                    $real = "<td>$uphtml" . $seq[$i][1] . "</td>";
                    $bool1 = "$uphtml";
                } else if ($j == 1)
                    $real = "<td>$downhtml" . $seq[$i][1] . "</td>";

                if ($j == 0)
                    $string[$i] .= "<td style='width:250;'>" . $seq[$i][3] . " </td>$real";
                else if ($j == 1)
                    $string[$i] .= "$real";
                $lo = $this->derive($vals) / $vals[3] / $c;
                $lo *= $this->derive($vals);
                while ($lo <= 0.0999)
                    $lo *= 10;
                $short_low = abs($lo);
                $short_low = (($lo * intval($vals[2]) / 10) - intval($vals[3]));
                $short_low = ($base + round($short_low / $out, 2) * 2); // - (1 * $exp));
                $exp = 1;
                if ($short_low > pow(10, $exp) && $exp < 3) {
                    $out = pow(10, $exp++);
                }
                $bool2 = "$downhtml";
                if (($short_low > $inc_imaginary)) {
                    $bool2 = "$uphtml";
                }
                if ($bool2 == $bool1)
                    $colored = "green";
                else
                    $colored = "blue";
                if ($j == 0 && $bool2 == $bool1)
                    $correct1++;
                else if ($j == 1 && $bool2 == $bool1)
                    $correct2++;
                if ($i != count($seq) - 1) {
                    $string[$i] .= "<td style='color:black;background-color:" . $colored . "'>" . $bool2 . round(abs(($inc_imaginary - $short_low) / 32.56),4) . "</td>";
                } else
                    $string[$i] .= "<td></td>";
                $inc_imaginary = $short_low;
                $inc_last = $inc_real;
                $j++;
                $seq[$i] = $seq2[$i];
            }
            $string[$i] .= "</tr>";
        }

        $date = strtotime($seq[array_key_last($seq)][3]) + (60 * 60 * 24);
        $base = $short_low;
        $str = $string;
        // $vals[0] = $z = $x;
        $string = [];
        $saved = [($inc_imaginary - $short_low), ($inc_real)];
        // $key = $vals;
        // $string .= "<tr><td colspan='8'>".($correct/sizeof($seq)) . "</td></tr>";
        $all_str = "";
        $seq = $tempseq2;
        $seq2 = $tempseq;
        for ($x = 0; $x < 60; ) // += $day_cnt)
        {
            $j = 0;
            $string = [];
            $string[] = "<tr>";
            $dateNow = date('Y-m-d', $date + (60 * 60 * 12 * ($x+1)));
            while ($j < 2) {
                if ($x == 0)
                    $vals = $key;
                else
                    $key = $vals;
                $inc_real = $seq[152 - $x - 1][1];
                array_pop($vals);
                $vals[] = $this->integrand($key);
                $c = 2; //$this->differential($key);
                $vals[2] = $this->integral($seq[array_key_last($seq) - 1]);
                array_pop($seq);
                $bool1 = "$downhtml";
                $vals[3] += 8;
                if ($j == 1 && (intval($inc_real) / 100) < $inc_last / 100) {
                    $real = "<td>$uphtml" . abs(intval($inc_last) / 100 - intval($saved[0]) / 100) . "</td>";
                    $bool1 = "$uphtml";
                } else if ($j == 1) {
                    $real = "<td>$downhtml" . abs(intval($inc_last) / 100 + intval($saved[0]) / 100) . "</td>";
                }
                if ($j == 0 && (intval($inc_real) / 100) < $inc_last / 100) {
                    $real = "<td>$uphtml" . abs(intval($inc_last) / 100 - intval($saved[0]) / 100) . "</td>";
                    $bool1 = "$uphtml";
                } else if ($j == 0) {
                    $real = "<td>$downhtml" . abs(intval($inc_last) / 100 + intval($saved[0]) / 100) . "</td>";
                }

                if ($j == 0)
                    $string[] = "<td style='width:150;'>" . $dateNow . "</td>$real";
                else if ($j == 1)
                    $string[] = "$real";

                $lo = $this->derive($vals) / intval($vals[3]) / $c;
                $lo *= $this->derive($vals);
                while ($lo <= 0.0999)
                    $lo *= 10;
                // $out = ($out <= 0) ? 100 : $out;
                $short_low = abs(($lo));
                $short_low = (($lo * $vals[2] / 10) - $vals[3]);
                $short_low = ($base + round($short_low / $out, 2) * 2); // - (1 * $exp));
                $exp = 1;
                if ($short_low > pow(10, $exp) && $exp < 3) {
                    $out = pow(10, $exp++);
                }
                // $short_low = $short_low / 10 * (abs(++$count)%7) + 1;
                // $out = round(($out / 10),2);

                $bool2 = "$downhtml";
                if (($short_low > $inc_imaginary)) {
                    $bool2 = "$uphtml";
                }
                if ($bool2 == $bool1)
                    $colored = "green";
                else
                    $colored = "blue";
                if ($i != count($seq) - 1) {
                    $string[] = "<td style='color:black;background-color:" . $colored . "'>" . $bool2 . round(abs(($inc_imaginary - $short_low) / 32.56),4) . "</td>";
                } else
                    $string[] = "<td style='color:black;background-color:" . $colored . "'>" . $bool2 . round(abs(($inc_imaginary - $short_low) / 32.56),4) . "</td>";
                $inc_imaginary = $short_low;
                $inc_last = $inc_real;

                $saved = [($inc_imaginary - $short_low), ($inc_real)];
                $inc_imaginary = $short_low;
                $inc_last = ($inc_real);
                $j++;
                $vals = [($x + 1), $short_low, $vals[1], $vals[3]];
                $seq = $seq2;
                array_pop($seq2);
                $x++;
            }
            if ((date('l', $date + (60 * 60 * 12 * $x))[0] != 'S' && !str_contains(strtolower($btc_csv), '-usd')) || str_contains(strtolower($btc_csv), '-usd'))
                $all_str .= implode($string) . "</tr>";
        }
        $stng = "";
        foreach ($str as $strings => $st) {
            $stng .= $st;
        }
        $string = ($all_str) . "<tr><td></td><td></td><td><br><br><br>" . round($correct1 / (count($tempseq) - 1) * 100, 2) . "% Accuracy</td><td></td><td><br><br><br>" . round($correct2 / (count($tempseq2) - 1) * 100, 2) . "% Accuracy</td></tr>" . $theaders . $stng;
        return $string;
    }
}
$ticker = "";
if (isset($_GET['page']))
    $ticker = strtoupper($_GET['page']);

$next = new CNGN();

// $nasdaq = json_decode(file_get_contents('./info/nasdaq_full_tickers.json'));
// $name = "";
// $industry = "";
// $market_cap = "";
// foreach ($nasdaq as $a) {
//     if (strtolower($a->symbol) == strtolower($ticker)) {
//         $name = $a->name;
//         $market_cap = $a->marketCap;
//         $industry = $a->industry;
//         break;
//     }
// }

if (isset($_GET['g'])) {
    $ticker = "AMZN?";
}

if (!file_exists('./tickers/' . $ticker . '.csv') || filemtime('./tickers/' . $ticker . '.csv') < time() - 60 * 60 * 24 * 5) {
    $time_past = time() - (60 * 60 * 24 * 365 * 2);
    //$time_past = date("Y-m-d", $time_past);
    $time_now = date("Y-m-d", time());
    unlink('./tickers/' . $ticker . '.csv');
    exec("curl --request GET --url 'https://query1.finance.yahoo.com/v7/finance/download/$ticker?period1=" . $time_past . "&period2=" . time() . "&interval=1d&events=history&includeAdjustedClose=true' -o ./tickers/$ticker.csv");
    chown('./tickers/' . $ticker . '.csv', 'www-data');
    chgrp('./tickers/' . $ticker . '.csv', 'www-data');
    chmod('./tickers/' . $ticker . '.csv', 777);
}
$rets_sofar = $next->bitcoin("./tickers/" . $ticker . '.csv', 15);
?>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js">
</script> -->
<script src="pipes.js"></script>
<?php
// echo "<div w3-include-html='chart.php?symbol=" . $_COOKIE['symbol'] . "'></div>";

// $return = file_get_contents(md5($_SERVER['REMOTE_ADDR']) . ".json");
// $jsenc = json_decode($return);
// $ticker = $_GET['symbol'];
// //echo "<pipe ajax='chart.php?symbol=" . $ticker . "' insert='idChart' id='idChart'></pipe>";
// echo "<h2>Symbol: $ticker</h2><h3>$name</h3><b style='font-size:14px'><i>Ind: $industry</i></b><dyn onclick='pipes(this);' id='fave' method='GET' ajax='./fave.php?r=1&symbol=" . $_COOKIE['symbol'] . "' insert='fave'>";
// if (isset($jsenc->$ticker) && $jsenc->$ticker == 1)
//     echo "<img style='height:35px;' src='fave.png'></dyn>";
// else
//     echo "<img style='height:35px;' src='norm.png'></dyn>";
// echo '</dyn>';
echo "<h3 style='color:lightgray'>" . substr($ticker,0,strlen($ticker) - 1) . "</h3>";
echo "<table class='borders' style='background-color:lightgray;opacity:80%;-webkit-text-stroke: 1px black;width:550px;z-index:-1'>";
echo $rets_sofar;
echo "</table>";