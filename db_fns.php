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

function vote_db_query($query)
{
  $conn = db_connect();
  if(!$conn)
  {
	//$msg = "Function do_update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }
  $result = $conn->query($query);
  if (!$result) {
    //$msg = "Function register,db insert failed";
	//$auth_log->general($msg);
	return DB_INSERT_ERROR;
  }
  $result->free();
  return true;
}

?>
