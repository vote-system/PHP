<?php

require_once('db_fns.php');
require_once('vote_fns.php');

header('Content-Type: application/json');

$usrname=$_POST['usrname'];

//set user status to active
$usr_active = USER_NOT_ACTIVE;
$query = "update usrinfo
			set active = '".$usr_active."'
			where usrname = '".$usrname."'";	
$ret = vote_db_query($query);	

if($ret == false)
	$logout_resp['logout_code'] = LOGOUT_ERROR; //login error,server error
else
	$logout_resp['logout_code'] = LOGOUT_SUCCESS;

echo json_encode($logout_resp);

?>