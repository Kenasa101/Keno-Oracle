<?php
include("backend.php");
?>
 
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
  <link rel="shortcut icon" href="assets/images/ico.png-128x128.png" type="image/x-icon">
  <meta name="description" content="The Keno Oracle is an automated system to provide analysis and predictions on the Australian Keno System.">
  <title>KENO ORACLE</title>

<meta property="og:title" content="KENO ORACLE" />
<meta property="og:type" content="article" />
<meta property="og:image" content="/og_image.png" />
<meta property="og:url" content="" />
<meta property="og:description" content="Keno Oracle is an automated system that provides analysis and predictions on the Australian Keno System." />

<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="KENO ORACLE" />
<meta name="twitter:description" content="The Keno Oracle is an automated system to provide analysis and predictions on the Australian Keno System." />
<meta name="twitter:image" content="og_image.png" />

  <link rel="stylesheet" href="assets/tether/tether.min.css">
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap-grid.min.css">
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap-reboot.min.css">
  <link rel="stylesheet" href="assets/datatables/data-tables.bootstrap4.min.css">
  <link rel="stylesheet" href="assets/theme/css/style.css">
  <link rel="stylesheet" href="assets/mobirise/css/mbr-additional.css" type="text/css">
  <style>
.numberCircle {
    border-radius: 50%;
    width: 10px;
    height: 10px;
    padding: 3px;
    background: #fff;
    border: 2px solid #666;
    color: #666;
    text-align: center;
    font: 14px Arial, sans-serif;
}
.numberCircleWin {
    width: 10px;
    height: 10px;
    padding: 3px;
    background: radial-gradient(circle at 4px 4px, #09f, #001);
    border-radius:50%;
    border: 2px solid #666;
	  box-shadow: 15px 10px 10px -14px rgba(0,0,0,0.4);
    color: white;
    text-align: center;
    font: 14px Arial, sans-serif;
}
</style>
  
  
</head>
<body>
  <section class="mbr-section content5 cid-sO613tfE82" id="content5-0">

    

    <div class="mbr-overlay" style="opacity: 0.8; background-color: rgb(35, 35, 35);">
    </div>

    <div class="container">
        <div class="media-container-row">
            <div class="title col-12 col-md-8">
                <h2 class="align-center mbr-bold mbr-white pb-1 mbr-fonts-style display-1">THE<br>KENO ORACLE</h2>
                <h3 class="mbr-section-subtitle align-center mbr-light mbr-white pb-3 mbr-fonts-style display-5">Australian Keno Analysis System</h3>
             
<h4 class="mbr-section-subtitle align-center mbr-light mbr-white pb-1 mbr-fonts-style display-5" style="font-size:11px; color:white;">Available in ACT, NT, TAS & SA</h4>
            </div>
        </div>
    </div>
</section>

<section class="mbr-section article content9 cid-sO614y61cG" id="content9-1">
    
     

    <div class="container">
        <div class="inner-container" style="width: 100%;">
            <div class="section-text align-center mbr-fonts-style pb-2 display-5" style="font-size: 80px;line-height: 0.1;">

<?php

$percent = ((($past_to_hit)/$highest)*100);
$percent = number_format($percent, 1 ) . '%';
$percent_average = ((($past_to_hit)/$average)*100);
$percent_average = number_format($percent_average, 1 ) . '%';

$percent_evens = (($last_evens/$highest_evens)*100);
$percent_evens = number_format($percent_evens, 1 ) . '%';
$percent_evens_average = (($last_evens/$average_evens)*100);
$percent_evens_average = number_format($percent_evens_average, 1 ) . '%';

$left_up_str = number_format($left_up);
$saved_up_str = number_format($saved_up);

$left_up_cost_str = number_format($left_up_cost);
$saved_up_cost_str = number_format($saved_up_cost);

$left_up_win = $left_up*3;
$saved_up_win = $saved_up*3;

$left_up_win_str = number_format($left_up*3);
$saved_up_win_str = number_format($saved_up*3);

$left_up_profit_str = number_format((($left_up*3)-$left_up_cost));
$saved_up_profit_str= number_format((($saved_up*3)-$saved_up_cost));


echo '
<span style="'.$colorBack.'">'.$last.'</span><span style="font-size:14px;"> 🎲 '.($past_to_hit).'  🔃 '.($last_evens).'</span><span style="font-size:16px;">
<br>GAME '.$current_game_number.'</span> <span style="font-size:13px;" id="timeBox"></span>

<br><span style="font-size:13px;">'.$current_numbers_string.'</span>
<br><span style="font-size:15px; '.$colorBack.';">x'.$ratio.'</span>
<br><span style="font-size:11px;">🎲 '.$percent.' </span><span style="font-size:10px;"> rarity ('.($past_to_hit).'/'.$highest.')</span>
<span style="font-size:11px;">🔃 '.$percent_evens.' </span><span style="font-size:10px;"> evens rarity ('.$last_evens.'/'.$highest_evens.')</span>

<br><span style="font-size:11px;">🎲 '.$percent_average.' </span><span style="font-size:10px;"> average rarity ('.($past_to_hit).'/'.$average.')</span>
<span style="font-size:11px;">🔃 '.$percent_evens_average.' </span><span style="font-size:10px;"> average evens rarity ('.$last_evens.'/'.$average_evens.')</span>


<br>





</div>
';?>


        </div>
        </div>
</section>

<section class="section-table cid-sO61aXDGBJ" id="table1-2" style="background:white;margin-top:-20px;" align="center">

  
  
  <div class="container container-table" style="background:white;">
      
      <div class="table-wrapper">



        <div class="container" style="margin:0px; padding:0px;">
          <table class="table" cellspacing="2" style="<?php echo $colorBackNoText; ?> width:80%;table-layout:fixed;margin:0px; padding:0px;">
          <tbody><?php echo $html; ?></tbody>
         </table>
<br>
        </div>
        <div class="container table-info-container">
          
            <div class="col-13">
<?php echo '
<span style="font-size:11px; '.$colorBack.'">DOUBLE DOWN STATS</span><br>
<span style="font-size:9px;">from start📈 bet:$'.$saved_up_str.' profit:(win:$'.$saved_up_win_str.': - cost:$'.$saved_up_cost_str.') = $'.$saved_up_profit_str.'<br>to highest📉 bet:$'.$left_up_str.' profit:(win:$'.$left_up_win_str.' - cost:$'.$left_up_cost_str.') = $'.$left_up_profit_str.'</span>'; ?><br><br>
                <p class="mbr-text mb-0 mbr-fonts-style display-7" style="font-size:12px;">RED = HIGHEST RISK<br>GOLD = BEST CHANCE ( LEGENDARY )</p><span style="font-size:9px;">For more information on how this website is used, visit the <a href="https://github.com/Empire-of-E-Projects/Keno-Oracle">GitHub Project</a></span><br><image src="og_image.png" width="90%"/>
            </div>
<br>
        </div>
      </div>
    </div>
</section>




<section once="" class="cid-sO6afmUK5S" id="footer6-b">

    

    

    <div class="container">
        <div class="align-center mbr-white">
            <div class="col-12">
                <p class="mbr-text mb-0 mbr-fonts-style display-7">Copyright © 2021 - Keno Oracle©<br>All Rights Reserved.</p><br>
            </div>
<div style="font-size:10px;">This website should not be taken as gambling or financial advice, we give no guarantee&nbsp;in the accuracy of the information contained on this website.<br>Your Welcome 👍<br>Information collected for analysis is provided by the <a href="https://keno.com.au">Official Keno Website</a>, for more information visit the <a href="https://github.com/Empire-of-E-Projects/Keno-Oracle">GitHub Project</a></div>

        </div>
    </div>
</section>


<script src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
  <script>
   var loadtime = <?php echo $loadtime; ?>;
   var now = 1000;
var timer = 0;
timer= setInterval(function(){
var left = (loadtime-now)/1000;
var timeBox = document.getElementById("timeBox");
 if(left <= 0)
    {
    window.location.href = window.location.href;
    clearInterval(timer);
    }
  var nowTime = millisToMinutesAndSeconds(left*1000);
  timeBox.innerHTML = nowTime;
  now = now+1000;
 }, 1000);

function millisToMinutesAndSeconds(millis) {
  var minutes = Math.floor(millis / 60000);
  var seconds = ((millis % 60000) / 1000).toFixed(0);
  return minutes + "m " + (seconds < 10 ? '0' : '') + seconds+"s";
}
  </script>
  <script src="assets/popper/popper.min.js"></script>
  <script src="assets/tether/tether.min.js"></script>
  <script src="assets/bootstrap/js/bootstrap.min.js"></script>
  <script src="assets/smoothscroll/smooth-scroll.js"></script>
  <script src="assets/datatables/jquery.data-tables.min.js"></script>
  <script src="assets/datatables/data-tables.bootstrap4.min.js"></script>
  <script src="assets/theme/js/script.js"></script>
  
  
</body>
</html>
