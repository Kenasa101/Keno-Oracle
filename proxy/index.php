<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// get target
$url = "https://kenoexample.com";
if(hasParam('url'))
{
$url = urldecode($_REQUEST['url']);
}
$host = parse_url($url, PHP_URL_HOST);
// use private rotating proxy
// $proxy = '209.127.191.180:9279';
// $proxyauth = 'hfpthwde:cf7c09e26vpv';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$url);   

//curl_setopt($ch, CURLOPT_PROXY, $proxy);
//curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

$agent = 'Mozilla/5.0 (Linux; Android 10; VOG-L29) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/95.0.4638.50 Mobile Safari/537.36';

curl_setopt($ch, CURLOPT_USERAGENT, $agent);


$customHeaders = array(
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
    'Accept-Language: en-AU,en-GB;q=0.9,en-US;q=0.8,en;q=0.7',
    'Host: '.$host,
    'Referer: '.$host,
    'Connection: close',
    'Upgrade-Insecure-Requests: 0',
    'Sec-Ch-Ua-Model: none',
    'Sec-Ch-Ua-Platform: none',
    'Sec-Ch-Ua-Platform-Version: none',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-User: ?1',
    'Sec-Ch-Ua-Mobile: ?1',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: none',
    'Viewport-Width: 1024',
    'Sec-Ch-Ua: "Google Chrome";v="95", "Chromium";v="95", ";Not A Brand";v="99"',
    'Sec-Ch-Ua-Full-Version: "95.0.4638.50"',
    'Ect: 4g',
    'Sec-Fetch-Site: same-origin',
    'Sec-Ch-Prefers-Color-Scheme: light',
    'Device-Memory: 8',
    'User-Agent: '.$agent);
curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
$curl_scraped_page = curl_exec($ch);

exit($curl_scraped_page);
// check params
function hasParam($param) 
{
   return array_key_exists($param, $_REQUEST);
}

function closeConnection($session)
{
curl_close($session);
}
?>
