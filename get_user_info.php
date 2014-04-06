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
$result = $conn->query("select * from user_detail where usrname='".$fetch_name."'");
if (!$result) 
{
	$msg = "Function register,db query failed";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
}

for ($count=0; $row = $result->fetch_assoc(); $count++) 
{
	echo json_encode($row);
}
?>
