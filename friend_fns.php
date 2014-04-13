<?php
require_once("vote_fns.php");
require_once("push_message_to_ios.php");
require_once("badge_fns.php");

function handle_add_fri_req($from,$to)
{
	//1.add item to stranger table
	//insert_stranger_table($to,$from);
	$line = "insert into stranger values
                           (NULL, '".$usrid."', '".$stranger_id."',NULL)";
	insert_item($line);

	//2.update the friend_badge in table usrinfo
	update_badge("friend_badge",$to,ADD_BADGE);

	//3.prepare to push message to peer
	//	query the token and count the total badge
	//  then push message to peer
	$token = search_token_from_db($to);

	$friend_badge = query_badge("friend_badge",$to);
	$vote_badge = query_badge("vote_badge",$to);
	$total_badge = $friend_badge + $vote_badge;

	push_message($from,$to,ADD_FRIEND_REQUEST,$token,$message,$total_badge);
}

function handle_add_fri_resp($from,$to,$response)
{
	//1.search the device token of $to
	$token = search_token_from_db($to);

	//2.push the response to the peer
	push_message($from,$to,$response,$token);
	switch($response)
	{
		case AGREE_ADD_FRIEND:
			//delete the line in stranger
			
			//insert_friend_table($from,$to,$response);
			$line = "insert into stranger values
                           (NULL, '".$usrid."', '".$stranger_id."',NULL)";
			insert_item($line);
			break;

		case REFUSE_ADD_FRIEND:

			break;
		case IGNORE_ADD_FRIEND:

			break
	}
	//
	if($response == AGREE_ADD_FRIEND)
	{
		//insert_friend_table($from,$to,$response);
		$line = "insert into stranger values
                           (NULL, '".$usrid."', '".$stranger_id."',NULL)";
		insert_item($line);
	}
	else
	{
		//update db, friend status and badge
		//
		update_stranger_status($from,$to,$response);
		update_badge($to,SUBTRCT_BADGE);
	}
}

function handle_del_fri_req($from,$to)
{
	//delete the item from database
	$ret = delete_friend_db($from,$to)
	if(!$ret)
		return true;
	else
		return false;
}

function handle_get_fri_list($usrname)
{
  $conn = db_connect();
  if(!$conn)
  {
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

function insert_stranger_table($usrid,$stranger_id)
{
  $conn = db_connect();
  if(!$conn)
  {
	//$msg = "Function do_update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }

  //friend relationship is bidirect, so insert two lines at the same time
  $query = "insert into stranger values
                           (NULL, '".$usrid."', '".$stranger_id."',NULL)"; 
  $result = $conn->query($query);
  if (!$result) {
    $msg = "Function register,db insert failed";
	//$auth_log->general($msg);
	return DB_INSERT_ERROR;
  }
  return true;
}

function insert_friend_table($from,$to)
{
  $usrid = search_usr_id($from);
  if($usrid == DB_ITEM_NOT_FOUND || $usrid == NULL)
	return ;

  $friendid = search_usr_id($to);
  if($friendid == DB_ITEM_NOT_FOUND || $usrid == NULL)
	return ;

  $conn = db_connect();
  if(!$conn)
  {
	$msg = "Function do_update_friend_db,db connect error!";
	//$auth_log->general($msg);
	return DB_CONNECT_ERROR;
  }

  //friend relationship is bidirect, so insert two lines at the same time
  $query = "insert into friend values
                           (NULL, '".$usrid."', '".$friend_id."', NULL, NULL),
						   (NULL, '".$friend_id."', '".$usrid."', NULL, NULL)"; 
  $result = $conn->query($query);
  if (!$result) {
    $msg = "Function register,db insert failed";
	//$auth_log->general($msg);
	return DB_INSERT_ERROR;
  }
  return true;
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

function delete_friend_db($usrid,$friendid)
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

function update_stranger_status($from,$to,$status)
{
	
}


?>
