<?php
require_once('vote_fns.php');

define("UPDATE_USRINFO_DEBUG",1);

$usrname=$_POST['usrname'];
$gender=$_POST['gender'];
$signature=$_POST['signature'];
$screen_name=$_POST['screen_name'];
//$screen_name_pinyin=$_POST['screen_name_pinyin'];

header('Content-Type: application/json');

if(UPDATE_USRINFO_DEBUG)
{
    $usrname="test";
    $gender="f";
    $signature="abc";
    $screen_name="me";
}

if(!$usrname) //must fill the usrname with other parameter
{	
	$usrinfo_resp['usrinfo_code'] = USER_NAME_NOT_FILL; //user name and passwd not correct!
	echo json_encode($usrinfo_resp);
	return;
}

//$date = new DateTime();
//$timestamp = $date->getTimestamp();
//echo $timestamp;
$timestamp = 1;

if($gender)
{
	$update = "update usrinfo
			set gender = '".$gender."', usr_info_timestamp = '".$timestamp."'
			where usrname = '".$usrname."'";
	$ret = vote_db_query($update);
	//echo $ret;
	if($ret == VOTE_DB_ERROR)
		$usrinfo_resp['update_gender'] = DB_UPDATE_ERROR;
	else
		$usrinfo_resp['update_gender'] = INFO_UPDATE_SUCCESS; 
}

if($signature)
{
	$update = "update usrinfo
			set signature = '".$signature."', usr_info_timestamp = '".$timestamp."'
			where usrname = '".$usrname."'";
	$ret = vote_db_query($update);
	//echo $ret;
	if($ret == VOTE_DB_ERROR)
		$usrinfo_resp['update_signature'] = DB_UPDATE_ERROR;
	else
		$usrinfo_resp['update_signature'] = INFO_UPDATE_SUCCESS; 
}

if($screen_name)
{
	$update = "update usrinfo
			set screen_name = '".$screen_name."', usr_info_timestamp = '".$timestamp."'
			where usrname = '".$usrname."'";
	$ret = vote_db_query($update);
	//echo $ret;
	if($ret == VOTE_DB_ERROR)
		$usrinfo_resp['screen_name'] = DB_UPDATE_ERROR;
	else
		$usrinfo_resp['screen_name'] = INFO_UPDATE_SUCCESS; 
}

if($screen_name_pinyin)
{
	//need to add code here, save this item according to the screen name;
}
echo json_encode($usrinfo_resp);

?>
