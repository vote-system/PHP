<?php
require_once("vote_fns.php");
require_once("db_fns.php");

$usrname = $_GET['usrname'];
$fetch_name = $_GET['fetch_name'];

header('Content-Type: application/json');

//$a = "http://115.28.228.41/vote/test";
//echo $a;
//echo "  ";
//echo json_encode($a);
//echo "  ";

//$str = iconv("UTF-8", "GB2312//IGNORE", $a);
//echo $str. "  ";
//echo json_encode($str);

if(!$usrname || !$fetch_name)
	return;

$query = "select * from usrinfo where usrname='".$fetch_name."'";
$row = vote_get_array($query);
//print_r($row);

$usrinfo['usrname'] = $row['usrname'];
$usrinfo['signature'] = $row['signature'];
$usrinfo['screen_name'] = $row['screen_name'];
$usrinfo['screen_name_pinyin'] = $row['screen_name_pinyin'];
$usrinfo['gender'] = $row['gender'];
$usrinfo['original_head_imag_url'] = $row['original_head_imag_url'];
$usrinfo['medium_head_imag_url'] = $row['medium_head_imag_url'];
$usrinfo['thumbnails_head_imag_url'] = $row['thumbnails_head_imag_url'];
$usrinfo['usr_info_timestamp'] = (int)$row['usr_info_timestamp'];
$usrinfo['head_imag_timestamp'] = (int)$row['head_imag_timestamp'];

echo json_encode($usrinfo,JSON_UNESCAPED_SLASHES);

?>
