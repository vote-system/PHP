<?php
require_once("vote_fns.php");

$usrname = $_GET['usrname'];
$fetch_name = $_GET['fetch_name'];

header('Content-Type: application/json');
if(!$usrname || !$fetch_name)
	return;

$query = "select * from usrinfo where usrname='".$fetch_name."'";
$row = vote_get_array($query);
//print_r($row);

$usrinfo['usrname'] = $row['usrname'];
$usrinfo['signature'] = $row['signature'];
$usrinfo['screen_name'] = $row['screen_name'];
$usrinfo['gender'] = $row['gender'];
$usrinfo['original_head_imag_url'] = $row['original_head_imag_url'];
$usrinfo['medium_head_imag_url'] = $row['medium_head_imag_url'];
$usrinfo['usr_info_timestamp'] = (int)$row['usr_info_timestamp']);
$usrinfo['head_image_timestamp'] = (int)$row['head_image_timestamp'];

echo json_encode($usrinfo);

?>
