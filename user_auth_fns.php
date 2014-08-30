<?php

require_once('vote_fns.php');
require_once('db_fns.php');
require_once('pinyin.php');

//$auth_log = new vote_log();

function register($usrname, $email, $password) {
// register new person with db
// return true or error message

  $query = "select * from usrinfo where usrname='".$usrname."'";
  $name_existed = vote_item_existed_test($query);
  if($name_existed)
	  return DB_ITEM_FOUND;
  else
  {
	$query = "insert into usrinfo values
             (NULL,sha1('".$password."'), '".$email."',NULL, NULL,'".$usrname."', 
			 NULL,NULL,NULL,NULL,NULL,NULL,NULL,-1,-1,0,0,0,NULL,NULL,NULL)";
	$ret = vote_db_query($query);		
	return $ret;
  }
}

function username_unique($usrname) {
  // check if the usrname to register have already been used
  //echo "Function username_unique";
 
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $name_existed = vote_item_existed_test($query);
  if($name_existed)
	  return DB_ITEM_FOUND;
  else
	  return DB_ITEM_NOT_FOUND;
}

function login($usrname, $password,$device_token) 
{
  $query = "select * from usrinfo
            where usrname='".$usrname."'
            and passwd = sha1('".$password."')";
  $login_succ = vote_item_existed_test($query);
  if($login_succ){
	//insert the device token
	$query = "update usrinfo
				set device_token = '".$device_token."'
				where usrname = '".$usrname."'";
	$ret = vote_db_query($query);		
	return $ret;
  }
  else{
	return DB_ITEM_NOT_FOUND;
  }
}

function cookie_login($cookie){
  $query = "select * from usr where cookie='".$cookie."'";
  $cookie_login = vote_item_existed_test($query);
  if($cookie_login){
	return DB_ITEM_FOUND;
  }else{
	return COOKIE_NOT_SAVED;
  }
}

function cookie_insert($usrname){
// first check if the cookie have already write to the database
// if not, insert it, else return alaready insert
  //echo "FUNCTION cookie_insert!\n";
  $cookie = sha1($usrname);
  $query = "select * from usrinfo
            where cookie='".$cookie."' 
			and usrname='".$usrname."'";	
  $cookie_existed = vote_item_existed_test($query);

  if($cookie_existed){
	DB_ITEM_FOUND;
  }else{
	$query = "update usrinfo
			 set cookie = sha1('".$usrname."')
			 where usrname = '".$usrname."'";
	$ret = vote_db_query($query);
	return $ret;
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
	if($result == DB_ITEM_NOT_FOUND){
		return LOGIN_ERROR;
	}else if($result == DB_ITEM_FOUND){
		$query = "update usrinfo
				 set passwd = sha1('".$new_password."')
				 where usrname = '".$usrname."'";
		$ret = vote_db_query($query);
		return $ret;
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
  $query = "update usrinfo
           set passwd = sha1('".$new_password."')
           where usrname = '".$usrname."'";
  $ret = vote_db_query($query);
  return $ret;
}

function notify_password($usrname, $password) {
// notify the user that their password has been changed
	$query = "select email from usrinfo
              where usrname='".$usrname."'";
	$email_existed = vote_item_existed_test($query);
	if(!$email_existed){	
		return ;
	}else{
	  //$row = $result->fetch_object();
	  $row = vote_get_array($query);
      $email = $row->email;
      $from = "From: zhaobo1023@gmail.com \r\n";
      $mesg = "Your cowork password has been changed to ".$password."\r\n"
              ."Please change it next time you log in.\r\n";

      if (mail($email, 'cowork login information', $mesg, $from)) {
        return true;
      } else {
        return false;
		//throw new Exception('Could not send email.');
      }
	}      
}

function add_default_usrinfo($usrname)
{
	$original_head_imag = ORIGINAL_HEAD_IMAG_URL;
	$medium_head_imag = MEDIUM_HEAD_IMAG_URL;
	$thumbnails_head_iamg_url = THUMBNAILS_HEAD_IMAG_URL;
	
	$screen_name = $usrname;

	$str = iconv("UTF-8", "GB2312//IGNORE", $screen_name);
		if(!$str){
			return;
		}
	$pinyin = get_pinyin_array($str);
	$screen_name_pinyin = $pinyin[0];

	$query = "update usrinfo
			set original_head_imag_url = '".$original_head_imag."',
				medium_head_imag_url = '".$medium_head_imag."',
				thumbnails_head_imag_url = '".$thumbnails_head_iamg_url."',
				gender = 'm',
				screen_name = '".$screen_name."',
				screen_name_pinyin = '".$screen_name_pinyin."'
			where usrname = '".$usrname."'";
	//echo $query;
	$ret = vote_db_query($query);
	return $ret;
}

?>
