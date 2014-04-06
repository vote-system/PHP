<?php
require_once('vote_fns.php');

$usrname=$_POST['usrname'];
$gender=$_POST['gender'];
$signature=$_POST['signature'];
$screen_name=$_POST['screen_name'];
$screen_name_pinyin=$_POST['screen_name_pinyin'];
//$head_imag_url=$_POST['head_imag_url'];

$date = new DateTime();
$timestamp = $date->getTimestamp();
//echo $timestamp;
 
if(!$usrname) //must fill the usrname with other parameter
{	
	$usrinfo_resp['usrinfo_code'] = USER_NAME_NOT_FILL; //user name and passwd not correct!
	header('Content-Type: application/json');
	echo json_encode($usrinfo_resp);
	return;
}

$conn = db_connect();
  if(!$conn){
	$msg = "update_usr_info ,db connect error!";
	//$auth_log->general($msg);
	
	//echo "db_connect error\n";
	$usrinfo_resp['usrinfo_code'] = DB_CONNECT_ERROR; 
	header('Content-Type: application/json');
	echo json_encode($usrinfo_resp);
	return;
  }

  // check if usrname is unique
  $result = $conn->query("select * from user_detail
                         where usrname='".$usrname."'");
  if (!$result) {
    $msg = "update_usr_info,db query failed!";
	//echo "db_query error\n";
	//$auth_log->general($msg);
	$usrinfo_resp['usrinfo_code'] = DB_QUERY_ERROR; 
	header('Content-Type: application/json');
	echo json_encode($usrinfo_resp);
	return;
  }

  if ($result->num_rows>0) {
     //do nothing, the line for the user existed.
	 	//echo "line existed\n";
  }
  else{
	//create the record for the user in database
    //mkdir under the /vote/upload for each user to upload the iamge

	$upload_dir = "/vote/upload/$usrname";
	$oldumask = umask(0);
	$res = mkdir($upload_dir, 0777);
	if(!$res)
	{
		$msg = "mkdir error for $upload_dir\n";
		echo "mkdir error for $upload_dir\n";
		//error_log($msg,3,"/alidate/log");
	}
	umask($oldumask);

	$res = $conn->query("insert into user_detail values
                           ('".$usrname."',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)");
	
	if (!$res) {
		//echo "db_insert error\n";
		$msg = "Function update_usr_info,db insert line failed";
		//$auth_log->general($msg);
		$usrinfo_resp['usrinfo_code'] = DB_INSERT_ERROR; 
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
		return;
	 }	
  }

if($gender)
{
	/*
	$item_name='gender';
	update_item($item_name,$gender);	
	return;
	*/

	$res = $conn->query("update user_detail
							set gender = '".$gender."',info_timestamp = '".$timestamp."'
							where usrname = '".$usrname."'");
	if (!$res) {
		//echo "db_update error\n";
		$msg = "Function update_usr_info,db update failed";
		//$auth_log->general($msg);
		$usrinfo_resp['usrinfo_code'] = DB_UPDATE_ERROR; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
	 else{
		$usrinfo_resp['usrinfo_code'] = INFO_UPDATE_SUCCESS; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
}

if($signature)
{
	//$item_name='signature';
	//update_item($item_name,$signature);	
	//return;

	$res = $conn->query("update user_detail
							set signature = '".$signature."',info_timestamp = '".$timestamp."'
							where usrname = '".$usrname."'");
	if (!$res) {
		echo "db_update error\n";
		$msg = "Function update_usr_info,db update failed";
		//$auth_log->general($msg);
		$usrinfo_resp['usrinfo_code'] = DB_UPDATE_ERROR; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
	 else{
		$usrinfo_resp['usrinfo_code'] = INFO_UPDATE_SUCCESS; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
}

if($screen_name)
{
	//$item_name='screen_name';
	//update_item($item_name,$screen_name);	
	//return;

	$res = $conn->query("update user_detail
							set screen_name = '".$screen_name."',info_timestamp = '".$timestamp."'
							where usrname = '".$usrname."'");
	if (!$res) {
		echo "db_update error\n";
		$msg = "Function update_usr_info,db update failed";
		//$auth_log->general($msg);
		$usrinfo_resp['usrinfo_code'] = DB_UPDATE_ERROR; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
	 else{
		$usrinfo_resp['usrinfo_code'] = INFO_UPDATE_SUCCESS; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
}

if($screen_name_pinyin)
{
	//$item_name='screen_name_pinyin';
	//update_item($item_name,$screen_name_pinyin);	
	//return;

	$res = $conn->query("update user_detail
							set screen_name_pinyin = '".$screen_name_pinyin."',info_timestamp = '".$timestamp."'
							where usrname = '".$usrname."'");
	if (!$res) {
		echo "db_update error\n";
		$msg = "Function update_usr_info,db update failed";
		//$auth_log->general($msg);
		$usrinfo_resp['usrinfo_code'] = DB_UPDATE_ERROR; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
	 else{
		$usrinfo_resp['usrinfo_code'] = INFO_UPDATE_SUCCESS; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
}


/*
function update_item($item_name,$item_value)
{
	echo "function update_item\n";
	//$update="update user_detail
	//		set '$item_name' = '$item_value'
	//		where usrname = '$usrname'";
	//$res = $conn->query($update);
	
	$res = $conn->query("update user_detail
							set '"$item_name"' = '".$item_value."'
							where usrname = '".$usrname."'");
	
	if (!$res) {
		echo "db_update error\n";
		$msg = "Function update_usr_info,db insert line failed";
		//$auth_log->general($msg);
		$usrinfo_resp['usrinfo_code'] = DB_UPDATE_ERROR; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
	 else{
		$usrinfo_resp['usrinfo_code'] = INFO_UPDATE_SUCCESS; //user name and passwd not correct!
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
	 }
}
*/
?>