<?php

// include function files for this application
require_once('vote_fns.php');
//session_start();

//create short variable names
$username = $_POST['username'];
$passwd = $_POST['passwd'];

//username&passwd login
if ($username && $passwd) {
	$result = login($username, $passwd);
    if( $result == DB_ITEM_NOT_FOUND){
		$msg = "user {$username}: login failed!";
		//$log->general($msg);

		$login_resp['login_code'] = LOGIN_ERROR; //login error
		header('Content-Type: application/json');
		echo json_encode($login_resp);
	}
	else if($result == DB_ITEM_FOUND){
		$msg = "user {$username}: login successful!";
		//$log->general($msg);

		// login successful,produce a cookie for the user
		// write the cookie into database
		$res = cookie_insert($cookie,$username);
		if($res == COOKIE_SAVE_SUCCESS){
			//only if cookie insert success in db then send cookie to customer
			setcookie("user_cookie", sha1($username));
		}
		$login_resp['login_code'] = LOGIN_SUCCESS; //login success
		header('Content-Type: application/json');
		echo json_encode($login_resp);
	}
}

//cookie login
if (isset($_COOKIE['user_cookie']))
{
	$cookie=$_COOKIE['user_cookie'];
	if(!cookie_login($cookie))
	{
		$msg = "cookie {$cookie}: cookie login error!";
		//$log->general($msg);

		$login_resp['login_code'] = COOKIE_LOGIN_ERROR; //register error
		header('Content-Type: application/json');
		echo json_encode($login_resp);
	}
	else
	{
		$msg = "cookie {$cookie}: cookie login successful!";
		//$log->general($msg);

		$login_resp['login_code'] = COOKIE_LOGIN_SUCCESS; //register error
		header('Content-Type: application/json');
		echo json_encode($login_resp);
	}
}
?>
