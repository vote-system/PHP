<?php
require_once('vote_fns.php');

$username=$_POST['username'];
$gender=$_POST['gender'];
$signature=$_POST['signature'];
$screen_name=$_POST['screen_name'];
$screen_name_pinyin=$_POST['screen_name_pinyin'];
$head_imag_url=$_POST['head_imag_url'];

echo "username={$username}\n";
if(!$username) //must fill the username with other parameter
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
	
	echo "db_connect error\n";
	$usrinfo_resp['usrinfo_code'] = DB_CONNECT_ERROR; 
	header('Content-Type: application/json');
	echo json_encode($usrinfo_resp);
	return;
  }

  // check if username is unique
  $result = $conn->query("select * from user_detail
                         where username='".$username."'");
  if (!$result) {
    $msg = "update_usr_info,db query failed!";
	echo "db_query error\n";
	//$auth_log->general($msg);
	$usrinfo_resp['usrinfo_code'] = DB_QUERY_ERROR; 
	header('Content-Type: application/json');
	echo json_encode($usrinfo_resp);
	return;
  }

  if ($result->num_rows>0) {
     //do nothing, the line for the user existed.
	 	echo "line existed\n";
  }
  else{
	// create the line for the username
	echo "line not existed,created\n";
	$res = $conn->query("insert into user_detail values
                           ('".$username."','','','','','')");
	
	if (!$res) {
		echo "db_insert error\n";
		$msg = "Function update_usr_info,db insert line failed";
		//$auth_log->general($msg);
		$usrinfo_resp['usrinfo_code'] = DB_INSERT_ERROR; 
		header('Content-Type: application/json');
		echo json_encode($usrinfo_resp);
		return;
	 }	
  }


$res = $conn->query("update user_detail
							set "$item_name" = '".$item_value."'
							where username = '".$username."'");
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

/*
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
	$item_name='screen_name_pinyin';
	update_item($item_name,$screen_name_pinyin);	
	return;
}

if($head_imag_url)
{
	$item_name='head_imag_url';
	update_item($item_name,$head_imag_url);	
	return;
}

function update_item($item_name,$item_value)
{
	echo "function update_item\n";
	$res = $conn->query("update user_detail
							set "$item_name" = '".$item_value."'
							where username = '".$username."'");
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