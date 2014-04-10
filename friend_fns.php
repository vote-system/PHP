<?php
require_once("vote_fns.php");
require_once("push_message_to_ios.php");

function add_friend_request($from,$to)
{
	//1.search the device token of $to
	$token = search_token_from_db($to);

	//2.push the request to the peer
	push_message($from,$to,ADD_FRIEND_REQUEST,$token,);
}

function add_friend_response($from,$to,$response)
{
	//1.search the device token of $to
	$token = search_token_from_db($to);

	//2.push the response to the peer
	push_message($from,$to,$response,$token);

	//3.decide whether to write to database
	if($response == AGREE_ADD_FRIEND)
	{
		//write to database
		update_friend_db($from,$to);
	}
	else if($response == REFUSE_ADD_FRIEND)
	{
		//do nothing
	}

}

function del_friend_request($from,$to)
{
	//delete the item from database
	$ret = delete_friend_db($from,$to)
	if(!$ret)
		return true;
	else
		return false;
}
/*
function delete_friend_response($from,$to,$response)
{
	//do not know whether this function is useful or not!
}
*/
function search_token_from_db($usrname)
{
	$conn = db_connect();
	if(!$conn){
		//$msg = "Function register,db connect error!";
		//$auth_log->general($msg);
		return DB_CONNECT_ERROR;
	}
	// check if username is unique
	$result = $conn->query("select * from usrinfo where usrname='".$usrname."'");
	if (!$result) {
		//$msg = "Function register,db query failed";
		//$auth_log->general($msg);
		return DB_QUERY_ERROR;
	}

	//$user_info = $result->fetch_assoc();
	$row = $result->fetch_array();
	return $row[4];
}

function update_friend_db($from,$to)
{
  $usrid = search_usr_id($from);
  if($usrid == DB_ITEM_NOT_FOUND || $usrid == NULL)
	return ;

  $friendid = search_usr_id($to);
  if($friendid == DB_ITEM_NOT_FOUND || $usrid == NULL)
	return ;

  $ret = do_update_friend_db($usrid,$friendid);
  if(!$ret)
	  return;
}

function search_usr_id($usrname)
{
  $conn = db_connect();
  if(!$conn)
  {
	$msg = "Function update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }

  //save the usrid
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $result = $conn->query();
  if (!$result) {
    $msg = "Function register,db query failed";
	//$auth_log->general($msg);
	return DB_QUERY_ERROR;
  }

  $row = mysqli_fetch_assoc($result);
  if($row['usrid']);
	return $row['usrid'];
  else
	return DB_ITEM_NOT_FOUND;
}

function do_update_friend_db($usrid,$friendid)
{
  $conn = db_connect();
  if(!$conn)
  {
	$msg = "Function do_update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }

  //friend relationship is bidirect, so insert two lines at the same time
  $query = "insert into friend values
                           (NULL, '".$usrid."', '".$friendid."', NULL, NULL),
						   (NULL, '".$friendid."', '".$usrid."', NULL, NULL)"; 
  $result = $conn->query($query);
  if (!$result) {
    $msg = "Function register,db insert failed";
	//$auth_log->general($msg);
	return DB_INSERT_ERROR;
  }
  return true;
}

delete_friend_db($usrid,$friendid)
{
  $conn = db_connect();
  if(!$conn)
  {
	$msg = "Function do_update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }

  $delete = "delete from friend where
			usrid='".$usrid."' and friendid='".$friendid."'";
  $result = $conn->query($delete);
  if (!$result) {
    //$msg = "Function register,db insert failed";
	//$auth_log->general($msg);
	return DB_INSERT_ERROR;
  }
  return true;
}

function get_friend_list($usrname)
{
  $conn = db_connect();
  if(!$conn)
  {
	$msg = "Function do_update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }
  $usrid = search_usr_id($usrname);

  $result = $conn->query("select * from friend where usrid='".$usrid."'");
  if (!$result) {
     //$msg = "Function register,db query failed";
     //$auth_log->general($msg);
	 return DB_QUERY_ERROR;
  }

  while ($row = $result->fetch_assoc()) 
  {
     $friend_id = $row['friendid'];
	 get_usrdetail($friendid);
  }

  /* free result set */
  $result->free();
}

function get_usrdetail($friendid)
{
  $conn = db_connect();
  if(!$conn)
  {
	$msg = "Function do_update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }
  $result = $conn->query("select * from usrinfo where usrid='".$friendid."'");
  if (!$result) 
  {
     //$msg = "Function register,db query failed";
     //$auth_log->general($msg);
	 return DB_QUERY_ERROR;
  }

  while ($row = $result->fetch_assoc()) 
  {
	 //only return part of items in table usrinfo
     $friend_info = array_slice($row,4);
	 //not sure whether need to put the following line out of the while loop
	 echo json_encode($friend_info);
  }

  /* free result set */
  $result->free();

}


?>
