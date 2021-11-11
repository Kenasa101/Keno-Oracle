<?php
date_default_timezone_set("Australia/Sydney");

// KENO ENDPOINTS 
// ----------------
// HISTORY,
$history_api="/v2/info/history";
// --------------- 
// LIVE DRAW, 
$live_api="/v2/games/kds";
// --------------- 
// TRENDING NUMBERS,
$trending_api="/v2/info/trends";
// ---------------
// HOT COLD
$hotcold_api="/v2/info/hotCold";
// ---------------
// JACKPOTS,
$jackpot_api="/v2/info/jackpots";

// CODE START
// ACT = NT + TAS + SA 
// NSW 
// VIC

$states = ["ACT", "NSW", "QLD", "VIC", "WA", "NT", "SA", "TAS"];
global $highest;
$highest = trim(file_get_contents("highest.log"));
$selected_state = $states[0];
if(hasParam('state'))
{
  $selected_state = $_REQUEST['state'];
}
$continue = false;
foreach($states as $state)
{
  if(strtolower($state) == strtolower($selected_state))
   {
      $continue = true;
   }
}
if(!$continue)
{
 exit("NO VALID STATE PROVIDED");
}

$pos = strtolower($selected_state);

if($pos == "nt" || $pos == "tas" || $pos == "sa")
{
 $selected_state = "ACT";
}
elseif($pos == "wa" || $pos == "qld")
{
  exit("THIS STATE IS NOT SUPPORTED");
}
else
{
  $selected_state = strtoupper($selected_state);
}

$api = "https://api-info-".strtolower($selected_state).".keno.com.au";
$state = "?jurisdiction=$selected_state";

$testing = false;



// GET CURRENT DRAW NUMBER

$url = $api.$live_api.$state;
if($testing){$url="responses/live.json";}
//echo $url."<br>";
$response = file_get($url);
$json = json_decode($response,true);
$current = $json['current'];
$current_game_number = $current['game-number'];
$current_numbers = $current['draw'];

if(strlen("$current_numbers") <= 1)
{
exit('LOADING INFORMATION FAILED : '.$response.'<br>AUTOMATICALLY RETRYING...<br><script type="text/JavaScript">setTimeout(function(){window.location.href = window.location.href;},10000);</script>');
}
else
{
$current_numbers_string = "";
foreach($current_numbers as $no)
{
$current_numbers_string.=$no.' ';
}
$current_end = $current['closed'];
$current_game_date = explode("T",$current_end)[0];
$current_game_time = explode("T",$current_end)[1];
$current_game_time = explode(".",$current_game_time)[0];
$current_game_year = explode("-",$current_game_date)[0];
$current_game_month = explode("-",$current_game_date)[1];
$current_game_day = explode("-",$current_game_date)[2];
$current_time = new DateTime($current_game_date.' '.$current_game_time);

$tod = getdate();
$year = $tod['year'];
$month = $tod['mon'];
$day = $tod['mday'];

$year = $year > $current_game_year ? $year: $current_game_year;
$month = $month > $current_game_month ? $month: $current_game_month;
$day = $day > $current_game_day ? $day: $current_game_day;


$date_now = $year .'-'.$month.'-'.$day;

//echo $date_now;

$timediff = $current_time->diff(new DateTime());
$minutes = $timediff->format('%i');
$seconds = $timediff->format('%s');
$max = 165000;
$loadtime = $max - (($minutes>=1)?($minutes*60000+$seconds*1000):($seconds*1000));

if($testing)
{
$loadtime = 60000;
}

//echo ($loadtime);

// LOAD BACKWARDS
$amountToGet = 50;
if($current_game_number <= 50)
{
 $amountToGet = $current_game_number-1;
}

$history_params ="&date=".$date_now."&starting_game_number=".($current_game_number-$amountToGet)."&number_of_games=".$amountToGet."&page_size=".$amountToGet."&page_number=1";

$url = $api.$history_api.$state.$history_params;
if($testing){$url="responses/history.json";}
//echo $url;

$selected = array();
$hits = array();
$all = 79;
$now = 0;
while($now != ($all+1))
{
   $hits[$now] = 0;
   $now ++;
}



$response = file_get($url);
$json = json_decode($response,true);
$items = $json['items'];

if(count($items) < 1){
$date_now = $year .'-'.$month.'-'.($day-1);

$history_params ="&date=".$date_now."&starting_game_number=".($current_game_number-$amountToGet)."&number_of_games=".$amountToGet."&page_size=".$amountToGet."&page_number=1";

$url = $api.$history_api.$state.$history_params;

$response = file_get($url);
$json = json_decode($response,true);
$items = $json['items'];
if(count($items) < 1)
{
    exit('LOADING NUMBERS FAILED on '.$url.'<br>AUTOMATICALLY RETRYING...<br><script type="text/JavaScript">setTimeout(function(){
    window.location.href = window.location.href;},5000);</script>');
}
}
$max = count($items);

$now = 0;
$newItems[0] = $current;
while($now != $max)
{
  $todo = $max-$now;
  $newItems[$now+1] = $items[$todo];
  $now++;
}


$found_last = false;
$last = 0;
$last_draw_no = 0;
$last_nums = array();
$past_to_hit = 0;



foreach($newItems as $draw)
{
 if($draw["game-number"] != null)
 {
  $draw_no = $draw['game-number'];
  $numbers = $draw["draw"];

      foreach($numbers as $no)
      {
          $hits[$no] += 1;
          if(isLast($no,$hits) &! $found_last)
          {
             $found_last = true;
             $last_nums = $numbers;
             $last = $no;
             $last_draw_no = $draw_no;
          }
        
       }
 if($found_last)
   {
      break;
   }
$past_to_hit +=1;
}
}

$ratio =(($past_to_hit-1)/4);

  $red = $ratio <= 4.50;
  $blue = $ratio >= 5.50 && $ratio < 6.50;
  $green = $ratio  >= 6.50 && $ratio < 7.50;
  $gold = $ratio >= 7.50;
   
  $color = "black";
if($gold)
   {
      $color = "gold";
   }
  if($green)
   {
      $color = "rgb(121, 255, 77)";
   }
if($blue)
   {
      $color = "rgb(0, 153, 255)";
   }
if($red)
   {
      $color = "rgb(255, 51, 51)";
   }


if($past_to_hit > $highest)
{
  file_put_contents("highest.log",$past_to_hit);
  $highest = $past_to_hit;
}




$html = '';
$num_now = 0;
$ten = 0;

foreach($hits as $numHits)
{
if($num_now <= 79)
{
if($ten == 0){ $html .= '<tr>';}
$hitsnow =$hits[$num_now+1];
$opacity = 1.0;
if($hitsnow <= 9 && $hitsnow >= 1)
{
  $opacity ='0.'.($hitsnow);
}

$html .= '<td class="body-item mbr-fonts-style display-7" style="font-size:13px; opacity:'.$opacity.';text-align:center;"><span style="font-size:8px;">x '.$hitsnow.'</span><br>'.($num_now+1).'</td>';

$ten ++;
if($ten == 10){ $html .= '</tr>'; $ten= 0;}
$num_now ++;
}
}
}



function hasParam($param) 
{
   if (array_key_exists($param, $_REQUEST))
    {
       return array_key_exists($param, $_REQUEST);
    }
}


function file_get($target)
{
global $testing;
$parse = parse_url($target);
$host = $parse['host'];
$ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,"http://keno-oracle.ml/get.php?url=".urlencode($target));
  curl_setopt($ch, CURLOPT_POST, false);
  curl_setopt($ch, CURLOPT_REFERER, $host);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
  curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$outputData = curl_exec ($ch);
$error = curl_error($ch);
  curl_close ($ch);
  return $outputData.$error;
}



function isLast($no,$hits)
{
$count_empty = 0;
$allHits = 0;
  foreach($hits as $hit)
   {
     if(!$hit >= 1 &! $allHits == 0)
       {
         $count_empty ++;
         $last = $allHits+1;
      }
     $allHits++;
   }
return $count_empty == 0;
}



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
<meta property="og:image" content="https://keno-oracle.ml/og_image.png" />
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
  
  
  
</head>
<body>
  <section class="mbr-section content5 cid-sO613tfE82" id="content5-0">

    

    <div class="mbr-overlay" style="opacity: 0.8; background-color: rgb(35, 35, 35);">
    </div>

    <div class="container">
        <div class="media-container-row">
            <div class="title col-12 col-md-8">
                <h2 class="align-center mbr-bold mbr-white pb-3 mbr-fonts-style display-1">THE<br>KENO ORACLE</h2>
                <h3 class="mbr-section-subtitle align-center mbr-light mbr-white pb-3 mbr-fonts-style display-5"><?php echo $selected_state; ?> Keno Analysis System</h3>
                <br><span style="font-size:11px; color:white;">Also available in <a href="/?state=act">ACT, NT, TAS, SA</a> - <a href="/?state=nsw">NSW</a> & <a href="/?state=vic">VIC</a></span>'
                
            </div>
        </div>
    </div>
</section>

<section class="mbr-section article content9 cid-sO614y61cG" id="content9-1">
    
     

    <div class="container">
        <div class="inner-container" style="width: 100%;">
            <div class="section-text align-center mbr-fonts-style display-5" style="font-size: 80px;"><?php

$percent = ((($past_to_hit-1)/$highest)*100);
$percent_friendly = number_format($percent, 1 ) . '%';

  echo '<span style="color:'.$color.';">'.$last.'</span><span style="font-size:14px;"> 🎲 '.($past_to_hit-1).'</span><span style="font-size:16px;"><br>GAME '.$current_game_number.'</span><br><span style="font-size:13px;">'.$current_numbers_string.'</span><br><span style="font-size:15px; color:'.$color.';">x'.(($past_to_hit-1)/4).'</span>
       <br><span style="font-size:10px;">Estimated Chance of WIN (Game Depth / Highest Record) = '.$percent_friendly.'</span>';?>
</div>

        </div>
        </div>
</section>
<section class="section-table cid-sO61aXDGBJ" id="table1-2" style="background:white;margin-top:-20px;" align="center">

  
  
  <div class="container container-table" style="background:white;">
      
      <div class="table-wrapper">



        <div class="container" style="margin:0px; padding:0px;">
          <table class="table" cellspacing="2" style="background:<?php echo $color; ?>;width:80%;table-layout:fixed;margin:0px; padding:0px;">
          <tbody><?php echo $html; ?></tbody>
         </table>
<br>
        </div>
        <div class="container table-info-container">
          
            <div class="col-12">
                <p class="mbr-text mb-0 mbr-fonts-style display-7" style="font-size:12px;">RED = HIGHEST RISK<br>BLACK = HIGH RISK ( COMMON )<br>BLUE = MEDIUM RISK<br>GREEN = LOW RISK ( RARE )<br>GOLD = BEST CHANCE ( LEGENDARY )</p><br><image src="og_image.png" width="90%"/>
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
<div style="font-size:10px;">This website should not be taken as gambling or financial advice, we give no guarantee&nbsp;in the accuracy of the information contained on this website.<br>Your Welcome 👍</div>
        </div>
    </div>
</section>

  <script src="assets/web/assets/jquery/jquery.min.js"></script>
  <script>
   var loadtime = <?php echo $loadtime; ?>;
   setTimeout(function(){
    window.location.href = window.location.href;
   }, loadtime);
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
