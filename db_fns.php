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

?>
