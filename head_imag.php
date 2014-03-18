<?php
require_once('vote_fns.php');

$date = new DateTime();
$timestamp = $date->getTimestamp();
 
$username=$_POST['username'];
$upload_dir = "/vote/upload/$username";

if($head_imag_url)
{
	//$item_name='head_imag_url';
	//update_item($item_name,$head_imag_url);	
	//return;

	$res = $conn->query("update user_detail
							set gender = '".$gender."'
							where username = '".$username."'");
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

?>