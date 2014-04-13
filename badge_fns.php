<?php
require_once("vote_fns.php");

define("ADD_BADGE",1);
define("SUBTRCT_BADGE",2);

function update_badge($badge_name,$usrname,$action)
{
  $badge = query_badge("friend_badge",$usrname)
  if(!$badge)
  {
	  return false;
  }
  else
  {
	switch($action)
	{
		case ADD_BADGE:
			$badge +=1;
			break;
		case SUBTRCT_BADGE:
			$badge -=1;
			break;
		default:
			return false;
	}
	$conn = db_connect();
    if(!$conn)
    {
		//$msg = "Function update_friend_db,db connect error!";
		//$auth_log->general($msg);
		return DB_CONNECT_ERROR;
    }
	$query = "update usrinfo set '".$badge_name."' = '".$badge."'
							where usrname = '".$usrname."'"
	$result = $conn->query();
	if(!$result)
		return false;
	else
		return true;
  }
  return false;
}

function query_badge($badge_name,$usrname)
{
  $conn = db_connect();
  if(!$conn)
  {
	//$msg = "Function update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }

  //save the usrid
  $query = "select '".$badge_name."' from usrinfo where usrname='".$usrname."'";
  $result = $conn->query();
  if (!$result) {
    //$msg = "Function register,db query failed";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
  }
  $badge = $result->fetch_array($result);
  return $badge;
}

?>