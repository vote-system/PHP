<?php
require_once('vote_fns.php');

$usrname=$_POST['usrname'];
$gender=$_POST['gender'];
$signature=$_POST['signature'];
$screen_name=$_POST['screen_name'];
$screen_name_pinyin=$_POST['screen_name_pinyin'];

header('Content-Type: application/json');

if(!$usrname) //must fill the usrname with other parameter
{	
	$usrinfo_resp['usrinfo_code'] = USER_NAME_NOT_FILL; //user name and passwd not correct!
	echo json_encode($usrinfo_resp);
	return;
}

if($gender)
{
	$item_name='gender';
	update_item($item_name,$gender);	
	return;	
}

if($signature)
{
	$item_name='signature';
	update_item($item_name,$signature);	
	return;
}

if($screen_name)
{
	$item_name='screen_name';
	update_item($item_name,$screen_name);	
	return;
}

if($screen_name_pinyin)
{
	//need to add code here, save this item according to the screen name;
}

function update_item($item_name,$item_value)
{
	$date = new DateTime();
	$timestamp = $date->getTimestamp();
	//echo $timestamp;

	$conn = db_connect();
	if(!$conn){
		$msg = "update_item ,db connect error!";
		//$auth_log->general($msg);
		
		//echo "db_connect error\n";
		$usrinfo_resp['usrinfo_code'] = DB_CONNECT_ERROR; 
		echo json_encode($usrinfo_resp);
		return;
	}
	
	//echo "function update_item\n";
	$update="update usrinfo
			set $item_name = '".$item_value."', usr_info_timestamp = '".$timestamp."'
			where usrname = '".$usrname."'";
	$res = $conn->query($update);
	
	if (!$res) {
		echo "db_update error\n";
		$msg = "Function update_usr_info,db insert line failed";
		//$auth_log->general($msg);
		$usrinfo_resp['usrinfo_code'] = DB_UPDATE_ERROR; //user name and passwd not correct!
		echo json_encode($usrinfo_resp);
	 }
	 else{
		$usrinfo_resp['usrinfo_code'] = INFO_UPDATE_SUCCESS; //user name and passwd not correct!
		echo json_encode($usrinfo_resp);
	 }
}

?>