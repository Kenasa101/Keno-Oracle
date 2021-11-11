<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


$proxies = get_file(urldecode('https://api.proxyscrape.com/v2/?request=getproxies&protocol=socks4&timeout=5000&country=all'));

$proxyList = explode(PHP_EOL,$proxies);

if(strlen($proxyList[0]) < 6 )
{
   $proxies = file_get_contents("prox.history");
   $proxyList = explode(PHP_EOL,$proxies);
}
else
{
    file_put_contents("prox.history", $proxies);
}

$proxy = trim(file_get_contents("ok.prox"));
if (hasParam('proxy'))
{
$proxy = urlDecode($_GET['proxy']);
}


if (!hasParam('url'))
{
  echo "no url provided";
  exit;
}
else
{
  $url = urlDecode($_GET['url']);
  $result = proxy($proxy, $url);
  echo $result;
  exit;
}




$lastProxPos = 0;
function getRandomProxy($proxyList, $lastPos)
{
$max = count($proxyList);
if($lastPos <= $max)
{
$prox = $proxyList[$lastPos];
 return trim($prox);
}
else
{
exit("Failed over ".$lastPos." proxies");
}
}



function proxy($proxyin, $url){
global $proxyList;
global $lastProxPos;

$agent = "Mozilla/5.0 (Linux; Android 10; VOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.50 Mobile Safari/537.36";

$cookieFile = "cookies.list";
$headers =array( ['Accept: text/html,application/xhtml+xml,application/xml;q=0.9']);

$ch = curl_init();
$ref = "http://127.0.0.1:8080";
if (hasParam('referer'))
{
$ref = urlDecode($_GET['referer']);
}

curl_setopt($ch, CURLOPT_REFERER, $ref);
curl_setopt($ch, CURLOPT_PROXY, $proxyin);
curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE,  $cookieFile);
curl_setopt($ch, CURLOPT_COOKIESESSION, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$time = 4;
if($doLoad)
{
$time = 2;
}
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $time);
curl_setopt($ch, CURLOPT_TIMEOUT, 8);
curl_setopt($ch, CURLOPT_USERAGENT, $agent);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$page = curl_exec($ch);
$error = curl_error($ch);

if(strlen($error) > 0 | strpos($page,"Access Denied") !== false)
{
$lastProxPos = $lastProxPos+1;
$page = proxy(getRandomProxy($proxyList,$lastProxPos), $url);

}
else
{
if($lastProxPos > 0)
{
$ok_proxy = $proxyList[$lastProxPos];
file_put_contents("ok.prox",trim($ok_proxy));
}
}

curl_close($ch);
return $page;
}

function get_file($target)
{
global $testing;
$parse = parse_url($target);
$host = $parse['host'];
$ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $target);
  curl_setopt($ch, CURLOPT_POST, false);
  curl_setopt($ch, CURLOPT_REFERER, $host);

  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$outputData = curl_exec ($ch);
$error = curl_error($ch);
  curl_close ($ch);
  return $outputData.$error;
}

function hasParam($param) 
{
   return array_key_exists($param, $_REQUEST);
}

?>
