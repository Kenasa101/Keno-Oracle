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
$logs= "logs/";

$highest = trim(file_get_contents($logs."highest.log"));

$hitsLogData = file($logs."lasthits.log");
$hitsArr = array();
$sizeHits = 0;
foreach($hitsLogData as $logEntry)
{
   $dataBits = explode(',',$logEntry);
   $amount = $dataBits[2];
   $hitsArr[$sizeHits]=$amount;
   $sizeHits++;
}
$a = array_filter($hitsArr);
$average = array_sum($a)/count($a);
$average = number_format(($average*2));
$highest_evens = trim(file_get_contents($logs."evens.log"));
$average_evens = 17;

$lastData = explode(',',file_get_contents($logs."last.log"));
$last_number = trim($lastData[0]);
$last_amount = trim($lastData[1]);
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
exit('<script type="text/JavaScript">setTimeout(function(){window.location.href = window.location.href;},500);</script>');
}
else
{

$current_numbers_string = "";
$lastHits = file($logs."lasthits.log");
                $lastHit = explode(',',$lastHits[0]);
                $hitGame = $lastHit[0];
if($current_game_number == $hitGame)
              {
                $hitVal = $lastHit[1];
                if ($current_game_number == $hitGame)
                  {
                      $is_new_number = true;
                      $last_number = $hitVal>0?$hitVal:0;
                  }
              }

foreach($current_numbers as $no)
{
if($no <= 9)
{
 $no = "0".$no;
}

if($no == $last_number)
{
$current_numbers_string.='<span class="numberCircleWin">'.$no.'</span> ';
}
else
{
$current_numbers_string.='<span class="numberCircle">'.$no.'</span> ';
}
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
$max = 159000;
$loadtime = $max - (($minutes>=1)?($minutes*60000+$seconds*1000):($seconds*1000));

if($testing)
{
$loadtime = 60000;
}

//echo ($loadtime);

// LOAD BACKWARDS
$amountToGet = 100;
if($current_game_number <= $amountToGet)
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
    exit('<script type="text/JavaScript">setTimeout(function(){
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
$past_to_hit_evens = 0;
$past_to_hit = 0;
$found_evens = false;
$last_evens = 0;
$past_tos = array();
$is_new_number = false;

foreach($newItems as $draw)
{
 if($draw["game-number"] != null)
 {
  $draw_no = $draw['game-number'];
  $numbers = $draw["draw"];
$varts = $draw["variants"];
$headsOr = $varts['heads-or-tails'];
$gameResult = $headsOr['result'];
$heads = $headsOr['heads'];
$tails = $headsOr['tails'];

   if(($gameResult == "evens" || $heads == $tails )&! $found_evens)
     {
       $found_evens = true;
       $last_evens = $past_to_hit_evens+1;
     }

      foreach($numbers as $no)
      {
          
         if($past_tos[$no] == null)
          {

              $past_tos[$no] = $past_to_hit+1;
          }

          $hits[$no] +=1;

          if(!$found_last && isLast($no,$hits))
          {
             
             $found_last = true;
             $last_nums = $numbers;
             $last = $no;
             
                $lastHits = file($logs."lasthits.log");
                $lastLog = file($logs."last.log");
                $lastNo = $lastLog[0];
                $lastAm = $lastLog[1];
                $lastHit = explode(',',$lastHits[0]);
                $hitGame = $lastHit[0];

             if($last != $lastNo && $current_game_number != $hitGame)
              {
                 file_put_contents($logs."last.log",$last.','.$past_to_hit);
                 file_put_contents($logs."lasthits.log",$current_game_number.','.$lastNo.','.$lastAm."\n".file_get_contents($logs."lasthits.log"));
                 $is_new_number = true;
              }

             if($last == $lastNo && $past_to_hit > $lastAm)
               {
                   file_put_contents($logs."last.log",$last.','.$past_to_hit);
               }
            
             $last_draw_no = $draw_no;
          }
        
       }
 if($found_last && $found_evens)
   {
      break;
   }
if(!$found_last)
{
$past_to_hit +=1;
}

if(!$found_evens)
{
$past_to_hit_evens +=1;
}

}
}

$ratio =(($past_to_hit)/4);
$ratio_perc = ((($past_to_hit-1)/$highest)*100);
$ratio_perc_str = number_format($ratio_perc);

$avper = ((($past_to_hit-1)/$average)*100);
if($avper > 100){$avper = 100;}
$ratio_perc_average = $avper/10*8;

$ratio_perc_str_average = number_format($ratio_perc_average);

$colorBack ='background: '.percToColor($ratio_perc_str_average).'; -webkit-background-clip: text; -webkit-text-fill-color: transparent;';

$colorBackNoText = 'background:'.percToColor($ratio_perc_str_average).';';

if($past_to_hit > $highest)
{
  file_put_contents($logs."highest.log",$past_to_hit);
  $highest = $past_to_hit;
}

if($last_evens > $highest_evens)
{
  file_put_contents($logs."evens.log",$last_evens);
  $highest_evens = $last_evens;
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
$past_tos_now = $past_tos[$num_now+1];
$opacity = 100-(($past_tos_now/$highest)*100);

if(strpos($opacity."",".") !== false )
{
$opacity = substr($opacity,0,strpos($opacity."","."));
}
$multi = '&#8623;';
if(($past_tos_now-1) > 0)
{
  $multi = 'x'.($past_tos_now-1);
}

$html .= '<td class="body-item mbr-fonts-style display-7" style="font-size:13px;opacity:1.0; filter: opacity('.($opacity).'%); text-align:center;"><span style="font-size:9px;">'.($multi).'</span><br>'.($num_now+1).'</td>';

$ten ++;
if($ten == 10){ $html .= '</tr>'; $ten= 0;}
$num_now ++;
}
}
}


function percToColor($perc)
{
$double = $perc;
$green = $perc <= 100 ? ((255 / 100) *$double) : 255;
$green = number_format($green);
return 'rgb(255,'.$green.',0)';
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
$ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "http://" . $_SERVER['HTTP_HOST']."/proxy/index.php?url=".urlencode($target));
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$outputData = curl_exec ($ch);
$error = curl_error($ch);
  curl_close ($ch);
 if($testing){return file_get_contents($target);}
  $res = $outputData.$error;
  if(strpos($res,"Access Denied") !== false){ $res = "Failure Loading Information";}

  return $outputData;
}


function isLast($no, $hits)
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
      else
      {
        if($hits[$no] >= 1)
         {
              $past_tos[$no] = $allHits+1;
         }
      }
     $allHits++;
   }
return $count_empty == 0;
}



$cost = 0;
$saved_up = expo($past_to_hit-1);
$saved_up_cost = $cost;
$left_up = expo($highest-($past_to_hit+1));
$left_up_cost = $cost;

function expo($in, $ofAm = 1)
{
if($in >= 1)
{
global $cost;
$cost = 0;
$max = $in+1;
$now = 1;
$out = $ofAm;
while ($now != $max)
{
  $out = $out*2;
  $cost += $out;
  $now++;
}

return $out;
}
else{
$cost = 0;
return 0;
}
}

?>