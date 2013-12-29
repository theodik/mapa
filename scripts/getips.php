<?php

error_reporting(0);

$ips = Array();
while (($line = fgets(STDIN)) !== false) {
  $ip = trim($line);
  if ($ips[$ip])
    $ips[$ip]++;
  else
    $ips[$ip] = 1;
}

fwrite(STDOUT, <<<EOD
<?php 
  header("Content-Type: text/xml");
  header('Access-Control-Allow-Origin: *'); 
  echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<addresses>
EOD
);

foreach ($ips as $ip => $count) {
  $info = geoip_record_by_name($ip);
  $country = $info['country_name'];
  $city = $info['city'];
  $lng = $info['longitude'];
  $lat = $info['latitude'];
  $xml = <<<EOD
<address>
  <ip>$ip</ip>
  <city>$city</city>
  <country>$country</country>
  <lat>$lat</lat>
  <lng>$lng</lng>
  <count>$count</count>
</address>
EOD;
  fwrite(STDOUT, $xml);
}

fwrite(STDOUT, "</addresses>\n");
