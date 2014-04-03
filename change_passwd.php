<?php
require_once("vote_fns.php");

$username = $_POST['username'];
$old_passwd = $_POST['old_passwd'];
$new_passwd = $_POST['new_passwd'];
$confirm_passwd = $_POST['confirm_passwd'];

if($new_passwd != $confirm_passwd)
{
	$chg_passwd_resp['chg_passwd_code'] = CONFIRM_NOT_CORRECT; //login error
	header('Content-Type: application/json');
	echo json_encode($chg_passwd_resp);
	return;
}	

if ((strlen($new_passwd) > 16) || (strlen($new_passwd) < 6)) 
{
	$chg_passwd_resp['chg_passwd_code'] = PASSWD_LENGTH_ERROR; //login error
	header('Content-Type: application/json');
	echo json_encode($chg_passwd_resp);
	return;
}

$result = change_password($username, $old_passwd, $new_passwd);
if($result == CHANGE_PASSWD_SUCCESS)
{
	//change passwd success!
	$chg_passwd_resp['chg_passwd_code'] = CHANGE_PASSWD_SUCCESS; 
	header('Content-Type: application/json');
	echo json_encode($chg_passwd_resp);
	return;
}
else if($result == LOGIN_ERROR)
{
	//old passwd not correct,input again
	$chg_passwd_resp['chg_passwd_code'] = LOGIN_ERROR; 
	header('Content-Type: application/json');
	echo json_encode($chg_passwd_resp);
	return;
}
else if($result == DB_ERROR)
{
	$chg_passwd_resp['chg_passwd_code'] = DB_ERROR; 
	header('Content-Type: application/json');
	echo json_encode($chg_passwd_resp);
	return;
}

?>