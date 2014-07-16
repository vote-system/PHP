<?php
// include function files for this application
require_once('vote_fns.php');
require_once('user_auth_fns.php');
require_once('data_valid_fns.php');
require_once('usrinfo_fns.php');
//macro to control debug log
define("REG_DEBUG", 0);

//$reg_log = new vote_log(); 

//create short variable names
$email=$_POST['email'];
$usrname=$_POST['usrname'];
$passwd=$_POST['passwd'];
$usr_unique=$_POST['usr_unique'];

header('Content-Type: application/json');

if(REG_DEBUG)
{
	$reg_debug['email'] = $email; 
	$reg_debug['usrname'] = $usrname; 
	$reg_debug['passwd'] = $passwd; 
	$reg_debug['usr_unique'] = $usr_unique; 

	echo json_encode($reg_debug);
}

//first check if the message is to check whether the usrname is unique
if($usr_unique == 1)
{
	$uniq_res = username_unique($usrname);

	if($uniq_res == DB_ITEM_FOUND)
	{				
		$reg_resp['name_used'] = NAME_BEEN_USED; 
		//echo $reg_resp["name_used"];
		echo json_encode($reg_resp);
		return;
		
	}
	else if($uniq_res == DB_ITEM_NOT_FOUND)
	{
		$reg_resp['name_used'] = NAME_NOT_USED; 
		//echo $reg_resp['name_used'];
		echo json_encode($reg_resp);
		return;
	}
	else
	{
		$reg_resp['name_used'] = NAME_CHECK_ERROR;
		echo json_encode($reg_resp);
		return;
	}
}	
else
{
	// email address not valid
	if (!valid_email($email)) {
	  $msg = "user {$usrname} do not fill a valid email address";
	  //$reg_log->user($msg,$usrname);

	  $reg_resp['reg_code'] = EMAIL_INVALID_ERROR; //register success
	  echo json_encode($reg_resp);
	  return;
	}

	// check password length is ok
	// ok if usrname truncates, but passwords will get
	// munged if they are too long.
	if ((strlen($passwd) < 6) || (strlen($passwd) > 16)) {
	  $msg = "user {$usrname}: password must be between 6 and 16 characters";
	  //$reg_log->user($msg,$usrname);

	  $reg_resp['reg_code'] = PASSWD_LENGTH_ERROR; //register success
	  echo json_encode($reg_resp);
	  return;
	}

	// attempt to register
	// this function can also throw an exception
	$result = register($usrname, $email, $passwd);
	if(!$result){
	  $msg = "user {$usrname}: regist error";
	  //$reg_log->general($msg);
	  $reg_resp['reg_code'] = REGISTER_ERROR; //regist error,check server's log
	  echo json_encode($reg_resp);
	}
	//register success,
	//$_SESSION['valid_user'] = $usrname;
	//response to the customer
	//$reg_resp['sessionid'] =  sha1($usrname);		
	$msg = "user {$usrname}: regist success!";
	//$reg_log->general($msg);

	//add default head_imag_url
	add_default_usrinfo($usrname);

	$usrid =  usrname_to_usrid($usrname);
	$query = "select * from unread_message where usrid='".$usrid."'";
	$existed = vote_item_existed_test($query);
	if(!$existed)
	{
		$query = "insert into unread_message values
					(NULL,'".$usrid."',NULL)";
		$ret = vote_db_query($query);
	}
	$reg_resp['reg_code'] = REGISTER_SUCCESS; //register success
	echo json_encode($reg_resp);		
}

?>
