<?php
require_once("vote_fns.php");

$username = $_POST['username'];
$old_passwd = $_POST['old_passwd'];
$new_passwd = $_POST['new_passwd'];
$confirm_passwd = $_POST['confirm_passwd'];
header('Content-Type: application/json');

if($new_passwd != $confirm_passwd)
{
	$login_resp['login_code'] = CONFIRM_NOT_CORRECT; //login error
	echo json_encode($login_resp);
	return;
}	

if ((strlen($new_passwd) > 16) || (strlen($new_passwd) < 6)) 
{
	$login_resp['login_code'] = PASSWD_LENGTH_ERROR; //login error
	echo json_encode($login_resp);
	return;
}

$result = change_password($username, $old_passwd, $new_passwd);
if($result == CHANGE_PASSWD_SUCCESS)
{
	//change passwd success!
	$login_resp['login_code'] = CHANGE_PASSWD_SUCCESS; 
	echo json_encode($login_resp);
	return;
}
else if($result == LOGIN_ERROR)
{
	//old passwd not correct,input again
	$login_resp['login_code'] = LOGIN_ERROR; 
	echo json_encode($login_resp);
	return;
}
else if($result == DB_ERROR)
{
	$login_resp['login_code'] = DB_ERROR; 
	echo json_encode($login_resp);
	return;
}

?>