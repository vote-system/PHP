<?php
require_once("vote_fns.php");
$usrname = $_POST['usrname'];
$fetch_name = $_POST['fetch_name'];

user_detail_key=array(0=>'usrname',1=>'gender',2=>'signature',3=>'screen_name',4=>'screen_name_pinyin',5=>'original_head_iamge_url',6=>'medium_head_iamge_url',7=>'thumbnails_head_iamge_url',8=>'info_timestamp',9=>'image_timestamp');


header('Content-Type: application/json');

$conn = db_connect();
if(!$conn){
//$msg = "Function register,db connect error!";
//$auth_log->general($msg);
return DB_CONNECT_ERROR;
}
// check if username is unique
$result = $conn->query("select * from user_detail where usrname='".$fetch_name."'");
if (!$result) {
$msg = "Function register,db query failed";
//$auth_log->general($msg);
return DB_QUERY_ERROR;
}

//$user_info = $result->fetch_assoc();
if(!$user_info)
{
	$usrinfo_array = array();
	for ($count=0; $row = $result->fetch_assoc(); $count++) 
	{
		$key=user_detail_key[$count];
		$usrinfo_array[$key] = $row;
	}
	echo json_encode($usrinfo_array);
}
else
{
	$usrinfo_array['usrinfo_code'] = DB_ITEM_NOT_FOUND;
	echo json_encode($usrinfo_array);
}
?>
