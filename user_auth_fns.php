<?php

require_once('vote_fns.php');

//$auth_log = new vote_log();

function register($usrname, $email, $password) {
// register new person with db
// return true or error message

  // connect to db
  $conn = db_connect();
  if(!$conn){
	$msg = "Function register,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }
  // check if usrname is unique
  $result = $conn->query("select * from usrinfo where usrname='".$usrname."'");
  if (!$result) {
    $msg = "Function register,db query failed";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
  }

  if ($result->num_rows>0) {
	$msg = "Function register,usrname={$usrname} already in used";
	//$auth_log->general($msg);
	return DB_ITEM_FOUND;  
  }
  // if ok, put in db
  $query = "insert into usrinfo values
                   (NULL, sha1('".$password."'), '".$email."',NULL, NULL,'".$usrname."',NULL,NULL,NULL,NULL,NULL,NULL,NULL,-1,-1)";
  $result = $conn->query($query);
  if (!$result) {
    $msg = "Function register,db insert usrname={$usrname} error";
	//$auth_log->general($msg);
	return DB_INSERT_ERROR;
  }
  else
	return true;
}

function username_unique($usrname) {
  // check if the usrname to register have already been used
  //echo "Function username_unique";
 
  $conn = db_connect();
  if(!$conn){
	$msg = "Function username_unique,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }
  // check if usrname is unique
  $result = $conn->query("select * from usrinfo where usrname='".$usrname."'");
  if (!$result) {
    $msg = "Function username_unique,db query failed";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
  }
  
  if ($result->num_rows>0) {
	$msg = "Function username_unique,usrname={$usrname} already in used";
	//$auth_log->general($msg);
	//echo "found usrname in db";
	return DB_ITEM_FOUND;  
  }
  else{
	//echo "usrname not been used";
	return DB_ITEM_NOT_FOUND;
  }
}

function login($usrname, $password,$device_token) {
// check usrname and password with db
// if yes, return true
// else throw exception

  // connect to db
  $conn = db_connect();
  if(!$conn){
	$msg = "Function login,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }

  // check if usrname is unique
  $result = $conn->query("select * from usrinfo
                         where usrname='".$usrname."'
                         and passwd = sha1('".$password."')");
  if (!$result) {
    $msg = "Function login,db query failed!";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
  }

  if ($result->num_rows>0) {
	 //insert the device token
	 $result = $conn->query("update usrinfo
							set device_token = '".$device_token."'
							where usrname = '".$usrname."'");
	 if (!$result) {
		$msg = "Function cookie_insert,db insert cookie={$cookie} failed";
		//$auth_log->general($msg);
		return DB_INSERT_ERROR;
	 }

     return DB_ITEM_FOUND;
  } else {
     $msg = "Function login,  usrname={$usrname} and passwd={$passwd} not found in database";
	 //$auth_log->general($msg);
	 return DB_ITEM_NOT_FOUND;
  }
}

function cookie_login($cookie){
// check cookie with db
  // connect to db
  $conn = db_connect();
  if(!$conn){
	$msg = "Function login,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }
	
  $result = $conn->query("select * from usr where cookie='".$cookie."'");
  if (!$result) {
    $msg = "Function cookie_login,db query failed";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
  }

  if ($result->num_rows>0) {
	// cookie already write into database, return true to indicate cookie login success!
	$msg = "Function cookie_login,cookie={$cookie} login success!";
	//$auth_log->general($msg);
	return DB_ITEM_FOUND;  
  }
  else{
	// cookie not found into database, return cookie login failed
	$msg = "Function cookie_login,cookie={$cookie} login filed, please use usrname and passwd login!";
	//$auth_log->general($msg);
	return COOKIE_NOT_SAVED;  
  }
}

function cookie_insert($usrname){
// first check if the cookie have already write to the database
// if not, insert it, else return alaready insert
  //echo "FUNCTION cookie_insert!\n";
  $cookie = sha1($usrname);
  $conn = db_connect();
  if(!$conn){
	$msg = "Function cookie_insert,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }

  // check if usrname is unique
  $result = $conn->query("select * from usrinfo
                         where cookie='".$cookie."' 
						 and usrname='".$usrname."'");
  if (!$result) {
    $msg = "Function cookie_insert,db query failed!";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
  }

  if ($result->num_rows>0) {
     return DB_SIMILAR_ITEM_FOUND;
  }
  else{
	// cookie not found in database,inset it
	$result = $conn->query("update usrinfo
							set cookie = sha1('".$usrname."')
							where usrname = '".$usrname."'");
	if (!$result) {
		$msg = "Function cookie_insert,db insert cookie={$cookie} failed";
		//$auth_log->general($msg);
		return DB_INSERT_ERROR;
	 }
	 else{
		return COOKIE_SAVE_SUCCESS;
	 }		
  }
}

/*
function check_valid_user() {
// see if somebody is logged in and notify them if not
  if (isset($_SESSION['valid_user']))  {
      echo "Logged in as ".$_SESSION['valid_user'].".<br />";
  } else {
     // they are not logged in
     //echo 'You are not logged in.<br />';
     exit;
  }
}
*/
function change_password($usrname, $old_password, $new_password) {
	// change password for usrname/old_password to new_password
	// return true or false

	// if the old password is right
	// change their password to new_password and return true
	// else throw an exception
	$result = login($usrname, $old_password);
	if($result == DB_ITEM_NOT_FOUND)
	{
		return LOGIN_ERROR;
	}
	else if($result == DB_ITEM_FOUND)
	{
		$conn = db_connect();
		$res = $conn->query("update usrinfo
							  set passwd = sha1('".$new_password."')
							  where usrname = '".$usrname."'");
		if (!$res) 
		{
			return DB_ERROR;
		} 
		else 
		{
			return CHANGE_PASSWD_SUCCESS;  // changed successfully
		}
	}
	else
	{
		return DB_ERROR;
	}
}

function get_random_word($min_length, $max_length) {
// grab a random word from dictionary between the two lengths
// and return it

   // generate a random word
  $word = '';
  // remember to change this path to suit your system
  $dictionary = '/usr/dict/words';  // the ispell dictionary
  $fp = @fopen($dictionary, 'r');
  if(!$fp) {
    return false;
  }
  $size = filesize($dictionary);

  // go to a random location in dictionary
  $rand_location = rand(0, $size);
  fseek($fp, $rand_location);

  // get the next whole word of the right length in the file
  while ((strlen($word) < $min_length) || (strlen($word)>$max_length) || (strstr($word, "'"))) {
     if (feof($fp)) {
        fseek($fp, 0);        // if at end, go to start
     }
     $word = fgets($fp, 80);  // skip first word as it could be partial
     $word = fgets($fp, 80);  // the potential password
  }
  $word = trim($word); // trim the trailing \n from fgets
  return $word;
}

function reset_password($usrname) {
// set password for usrname to a random value
// return the new password or false on failure
  // get a random dictionary word b/w 6 and 13 chars in length
  $new_password = get_random_word(6, 13);

  if($new_password == false) {
    throw new Exception('Could not generate new password.');
  }

  // add a number  between 0 and 999 to it
  // to make it a slightly better password
  $rand_number = rand(0, 999);
  $new_password .= $rand_number;

  // set user's password to this in database or return false
  $conn = db_connect();
  $result = $conn->query("update usrinfo
                          set passwd = sha1('".$new_password."')
                          where usrname = '".$usrname."'");
  if (!$result) {
    throw new Exception('Could not change password.');  // not changed
  } else {
    return $new_password;  // changed successfully
  }
}

function notify_password($usrname, $password) {
// notify the user that their password has been changed

    $conn = db_connect();
    $result = $conn->query("select email from usrinfo
                            where usrname='".$usrname."'");
    if (!$result) {
      throw new Exception('Could not find email address.');
    } else if ($result->num_rows == 0) {
      throw new Exception('Could not find email address.');
      // usrname not in db
    } else {
      $row = $result->fetch_object();
      $email = $row->email;
      $from = "From: zhaobo1023@gmail.com \r\n";
      $mesg = "Your cowork password has been changed to ".$password."\r\n"
              ."Please change it next time you log in.\r\n";

      if (mail($email, 'cowork login information', $mesg, $from)) {
        return true;
      } else {
        throw new Exception('Could not send email.');
      }
    }
}

?>
