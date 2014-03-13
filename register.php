<?php
// include function files for this application
require_once('vote_fns.php');

$reg_log = new vote_log(); 

//create short variable names
$email=$_POST['email'];
$username=$_POST['username'];
$passwd=$_POST['passwd'];
$usrunique=$_POST['usrname_unique'];

//reg_resp = array("name_used"=> 0, "reg_code"=>0,);
//first check if the message is to check whether the username is unique
if($usrunique == 1)
{
	$uniq_res = username_unique($username);

	if($uniq_res == DB_ITEM_FOUND)
	{				
		$reg_resp['name_used'] = NAME_BEEN_USED; 
		//echo $reg_resp["name_used"];
		header('Content-Type: application/json');
		echo json_encode($reg_resp);
		return;
		
	}
	else if($uniq_res == DB_ITEM_NOT_FOUND)
	{
		$reg_resp['name_used'] = NAME_NOT_USED; 
		//echo $reg_resp['name_used'];
		header('Content-Type: application/json');
		echo json_encode($reg_resp);
		return;
	}
	else
	{
		$reg_resp['name_used'] = NAME_CHECK_ERROR;
		header('Content-Type: application/json');
		echo json_encode($reg_resp);
		return;
	}
}	
else
{
	// email address not valid
	if (!valid_email($email)) {
	  $msg = "user {$username} do not fill a valid email address";
	  //$reg_log->user($msg,$username);

	  $reg_resp['reg_code'] = EMAIL_INVALID_ERROR; //register success
	  header('Content-Type: application/json');
	  echo json_encode($reg_resp);
	  return;
	}

	// check password length is ok
	// ok if username truncates, but passwords will get
	// munged if they are too long.
	if ((strlen($passwd) < 6) || (strlen($passwd) > 16)) {
	  $msg = "user {$username}: password must be between 6 and 16 characters";
	  //$reg_log->user($msg,$username);

	  $reg_resp['reg_code'] = PASSWD_LENGTH_ERROR; //register success
	  header('Content-Type: application/json');
	  echo json_encode($reg_resp);
	  return;
	}

	// attempt to register
	// this function can also throw an exception
	$result = register($username, $email, $passwd);
	if(!$result){
	  $msg = "user {$username}: regist error";
	  //$reg_log->general($msg);
	  $reg_resp['reg_code'] = REGISTER_ERROR; //regist error,check server's log
	  header('Content-Type: application/json');
	  echo json_encode($reg_resp);
	}
	//register success,
	//$_SESSION['valid_user'] = $username;
	//response to the customer
	//$reg_resp['sessionid'] =  sha1($username);		
	$msg = "user {$username}: regist success!";
	//$reg_log->general($msg);

	$reg_resp['reg_code'] = REGISTER_SUCCESS; //register success
	header('Content-Type: application/json');
	echo json_encode($reg_resp);		
}

?>
