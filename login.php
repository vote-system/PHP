<?php

// include function files for this application
require_once('vote_fns.php');
//session_start();

//create short variable names
$usrname = $_POST['usrname'];
$passwd = $_POST['passwd'];
$device_token = $_POST['device_token'];

//return the json type data to the client
header('Content-Type: application/json');

//$reg_resp['usrname'] = $usrname; 
//$reg_resp['passwd'] = $passwd; 
//echo $reg_resp["name_used"];
//header('Content-Type: application/json');
//echo json_encode($reg_resp);

//header('Content-Type: application/json');
//usrname&passwd login
if ($usrname && $passwd) 
{
	$result = login($usrname, $passwd,$device_token);

	if($result == DB_ITEM_FOUND)
	{
		$msg = "user {$usrname}: login successful!";
		//$log->general($msg);

		//check whether the usrname item have been created in table user_detail
		//if not, create it
		

		// login successful,produce a cookie for the user
		// write the cookie into database

		$res = cookie_insert($usrname);
		//echo "res={$res}\n";
		if($res == COOKIE_SAVE_SUCCESS || $res == DB_SIMILAR_ITEM_FOUND){
			//only if cookie insert success in db then send cookie to customer
			setcookie("user_cookie", sha1($usrname),time()+3600,"/vote","115.28.228.41");
			//setcookie("user_cookie", sha1($usrname),"115.28.228.41");
		}
		$login_resp['login_code'] = LOGIN_SUCCESS; //login success
	}
	else if( $result == DB_ITEM_NOT_FOUND)
	{
		$msg = "user {$usrname}: login failed!";
		//$log->general($msg);

		$login_resp['login_code'] = DB_ITEM_NOT_FOUND; //user name and passwd not correct!
	}
	else
	{
		$login_resp['login_code'] = LOGIN_ERROR; //login error,server error
	}
	echo json_encode($login_resp);

}

//cookie login
if (isset($_COOKIE['user_cookie']))
{
	$cookie=$_COOKIE['user_cookie'];
	
	$result = cookie_login($cookie);
	if ($result == DB_ITEM_FOUND)
	{
		$msg = "cookie {$cookie}: cookie login successful!";
		//$log->general($msg);

		$login_resp['login_code'] = COOKIE_LOGIN_SUCCESS; //register error
	}
	else if($result == COOKIE_NOT_SAVED)
	{
		$msg = "cookie {$cookie}: cookie not saved in db, please use usrname and password to login!";
		//$log->general($msg);

		$login_resp['login_code'] = COOKIE_NOT_SAVED; //register error
	}
	else
	{
		$msg = "cookie {$cookie}: cookie login error!";
		//$log->general($msg);

		$login_resp['login_code'] = COOKIE_LOGIN_ERROR; //register error
	}
	echo json_encode($login_resp);

}
?>
