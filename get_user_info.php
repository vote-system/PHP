<?php
require_once("vote_fns.php");
//$username = $_POST['username'];
$friend_id = $_POST['friend_id'];

$conn = db_connect();
if(!$conn){
//$msg = "Function register,db connect error!";
//$auth_log->general($msg);
return DB_CONNECT_ERROR;
}
// check if username is unique
$result = $conn->query("select * from user_detail where username='".$friend_id."'");
if (!$result) {
$msg = "Function register,db query failed";
//$auth_log->general($msg);
return DB_QUERY_ERROR;
}

$user_info = $result->fetch_assoc();
if(!$user_info)
{
	header('Content-Type: application/json');
	echo json_encode($user_info);
}
?>