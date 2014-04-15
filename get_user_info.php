<?php
require_once("vote_fns.php");

$usrname = $_GET['usrname'];
$fetch_name = $_GET['fetch_name'];

header('Content-Type: application/json');

$conn = db_connect();
if(!$conn)
{
	//$msg = "Function register,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
}
$result = $conn->query("select * from usrinfo where usrname='".$fetch_name."'");
if (!$result) 
{
	$msg = "Function register,db query failed";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
}
else
{
	$row = $result->fetch_array();
	$stranger['usrname'] = $row['usrname'];
	$stranger['signature'] = $row['signature'];
	$stranger['screen_name'] = $row['screen_name'];
	$stranger['gender'] = $row['gender'];
	$stranger['original_head_imag_url'] = $row['original_head_imag_url'];
	$stranger['medium_head_imag_url'] = $row['medium_head_imag_url'];
	$strangers_array['strangers_array'] = $stranger;

	echo json_encode($strangers_array); 
} 

?>
