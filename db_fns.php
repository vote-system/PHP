<?php
require_once('vote_fns.php');

function vote_db_connect() 
{
   $result = new mysqli('localhost', 'root', '841023', 'vote');
   if (!$result) {
	 $msg = "db connect error!";
	 $log->general($msg);
	 return VOTE_DB_ERROR;
   } else {
     return $result;
   }
}

function vote_db_closed($result)
{
	$result->free();
}

function vote_db_query($query)
{
  $conn = vote_db_connect();
  $result = $conn->query($query);
  if (!$result) {
	return VOTE_DB_ERROR;
  }
  vote_db_closed();
  return $conn;
}

function vote_get_array($query)
{
  $result = vote_db_query($query);
  $vote_array = $result->fetch_array($result);
  vote_db_closed();
  return $vote_array;
}

function vote_get_assoc($query)
{
  $result = vote_db_query($query);
  $posts = array();
  while ($row = mysql_fetch_assoc($result)) {
	$posts[] = $row;
  }
  vote_db_closed($result);
  return $posts;
}

function vote_item_existed_test($query)
{
  // connect to db
  $conn = db_connect();
  if(!$conn){
	//$msg = "Function register,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }
  // check if usrname is unique
  $result = $conn->query($query);
  if (!$result) {
    //$msg = "Function register,db query failed";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
  }

  if ($result->num_rows>0) {
	//$msg = "Function register,usrname={$usrname} already in used";
	//$auth_log->general($msg);
	vote_db_closed($result);
	return true;  
  }else{
	vote_db_closed($result);
	return false;
  }
	
}


?>
