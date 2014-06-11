<?php
require_once('vote_fns.php');
require_once('pinyin.php');
require_once('time.php');
require_once('db_fns.php');

define("UPDATE_USRINFO_DEBUG",0);

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

if($gender)
{
	
	$update = "update usrinfo
			set gender = '".$gender."'
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
			set signature = '".$signature."'
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
	//echo "screen_name = " . $screen_name . "\n";	

	//判断是否为中文，若是，则转为英文
	if(true){
		$str = iconv("UTF-8", "GB2312//IGNORE", $screen_name);
			if(!$str)
				return;
		$pinyin = get_pinyin_array($str);
		$screen_name_pinyin = $pinyin[0];
		//print_r($pinyin);
		//echo $pinyin[0];
	}else{
		$screen_name_pinyin = $screen_name;
	}
	

	//$usrinfo_resp['screen_name_pinyin'] = $screen_name_pinyin;

	$update = "update usrinfo
			set screen_name = '".$screen_name."',screen_name_pinyin = '".$screen_name_pinyin."'
			where usrname = '".$usrname."'";
	$ret = vote_db_query($update);
	//echo $ret;
	if($ret == VOTE_DB_ERROR)
		$usrinfo_resp['screen_name'] = DB_UPDATE_ERROR;
	else
		$usrinfo_resp['screen_name'] = INFO_UPDATE_SUCCESS; 	
}

update_usrinfo_timestamp($usrname,USR_INFO_TIME_STAMP);

echo json_encode($usrinfo_resp);

?>
