<?php

// include function files for this application
require_once('vote_fns.php');
require_once('user_auth_fns.php');
require_once('usrinfo_fns.php');
require_once('push_message_to_ios.php');
//session_start();

//create short variable names
$usrname = $_POST['usrname'];
$passwd = $_POST['passwd'];
$device_token = $_POST['device_token'];

//return the json type data to the client
header('Content-Type: application/json');

if(LOGIN_DEBUG)
{
	$debug['usrname'] = $usrname; 
	$debug['passwd'] = $passwd; 
	$debug['device_token'] = $device_token;  

	echo json_encode($debug);
}

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
		if($res == COOKIE_SAVE_SUCCESS || $res == DB_ITEM_FOUND){
			//only if cookie insert success in db then send cookie to customer
			//setcookie("user_cookie", sha1($usrname),time()+3600,"/vote","115.28.228.41");
			setcookie("user_cookie", sha1($usrname));
		}
		$login_resp['login_code'] = LOGIN_SUCCESS; //login success

		//set user status to active
		$usr_active = USER_ACTIVE;
		//echo "usr_active=" .$usr_active;
		$query = "update usrinfo
					set active = '".$usr_active."'
					where usrname = '".$usrname."'";
		//echo "query = " .$query;
		$ret = vote_db_query($query);	
		
		//check whether there is unread message, if yes, push the message to the usr
		check_and_push_unread_message($usrname);

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

/*
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
		//echo "COOKIE_NOT_SAVED";

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
*/

function check_and_push_unread_message($usrname)
{
	$usrid = usrname_to_usrid($usrname);
	$query = "select * from unread_message where usrid='".$usrid."'";
	$unread_message_existed = vote_item_existed_test($query);
	//echo "unread_message_existed=" .$unread_message_existed. " \n";
	
	if($unread_message_existed == false){	
		//echo "no unread_message";
		return;
	}else{
		//push the message one bye one
		$query = "select * from unread_message where usrid='".$usrid."'";
		$unread_message_array = vote_get_array($query);
		$message = $unread_message_array['message'];
		$unread_messages = unserialize($message);
		//print_r($unread_messages);
		
		$message_number = count($unread_messages);
		//echo "message_number = " .$message_number;
		while($message_number>0)
		//foreach($unread_messages as $message)
		//for($i=0;i<$message_number;$i++)
		{
			$stranger_id = $unread_messages[0]['stranger_id'];
			$from = usrid_to_usrname($stranger_id);
			//$to = usrid_to_usrname($usrid);
			$to = $usrname;
			$action = $unread_messages[0]['action'];
			$append_message = $unread_messages[0]['append_message'];
			
			//echo "usrid=" .$usrid. " \n";
			//echo "action=" .$action. " \n";
			//echo "append_message=" .$append_message. " \n";
			
			$ret = push_message($from,$to,$action,$append_message);
			
			//echo "message_number=" . $message_number . "\n";
			//if($message_number == 2)
			//	$ret = true;
			//else if($message_number == 1)
			//	$ret = false;
			if(!$ret){
				//push message failed, write the unread_message back to the database
				//try to push the message until next time user successful login.
				$message = serialize($unread_messages);
				$query = "update unread_message set message='".$message."'
							where usrid='".$usrid."'";
				//echo "query=" .$query . "\n";
				vote_db_query($query);
				break;
			}else{
				array_shift($unread_messages); 
				$message_number = count($unread_messages);
				//echo "after: message_number=" . $message_number . "\n";				
			}
		}
		if($message_number == 0)
		{
			$query = "delete from unread_message 
						where usrid='".$usrid."'";
			$ret = vote_db_query($query);
		}else{
			return false;
		}
	}
}
?>
