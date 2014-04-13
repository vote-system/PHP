<?php
require_once('vote_fns.php');

function db_connect() {
   $result = new mysqli('localhost', 'root', '841023', 'vote');
   if (!$result) {
	 $msg = "db connect error!";
	 $log->general($msg);
	 return DB_CONNECT_ERROR;
   } else {
     return $result;
   }
}

function insert_item($line)
{
  $conn = db_connect();
  if(!$conn)
  {
	//$msg = "Function do_update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }
  $result = $conn->query($line);
  if (!$result) {
    //$msg = "Function register,db insert failed";
	//$auth_log->general($msg);
	return DB_INSERT_ERROR;
  }
  return true;
}

function update_item($line)
{

}

function delte_item($line)
{

}

?>
