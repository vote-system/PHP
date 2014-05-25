<?php
date_default_timezone_set('PRC');
//$date = new DateTime('now');
$date = new DateTime();
//print_r($date);
$timestamp = $date->getTimestamp();
//print_r($timestamp);
echo $timestamp;

//phpinfo();

//$date = date_create();
//echo $date;

?>
