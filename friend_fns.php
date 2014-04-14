<?php
require_once("vote_fns.php");
require_once("push_message_to_ios.php");
require_once("badge_fns.php");

function handle_add_fri_req($from,$to)
{
	//1.add item to stranger table
	//add two item for each require
	$usrid = $from;
	$stranger_id = $to;
	$status = ADD_FRIEND_SEND;
	insert_stranger_table($usrid,$stranger_id,$status);

	//add the peer item to stranger table
	$usrid = $to;
	$stranger_id = $from;
	$status = IGNORE_FRIEND_RESPONSE;
	insert_stranger_table($usrid,$stranger_id,$status);

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

	$usrid = usrname_to_usrid($from);
	$stranger_id = usrname_to_usrid($to);

	switch($response)
	{
		case AGREE_ADD_FRIEND:
			//change the status in stranger table
			$status = AGREE_ADD_FRIEND;		  
			update_stranger_status($usrid,$stranger_id,$status);
	
			//add two rows to friend table
			$friend_id = $stranger_id;
			insert_friend_table($usrid,$friend_id);	
			break;

		case REFUSE_ADD_FRIEND:
			//change the status in stranger table
			$status = REFUSE_FRIEND_REQUEST;
			update_stranger_status($usrid,$stranger_id,$status);
			break;

		case IGNORE_ADD_FRIEND:
			//change the status in stranger table
			$status = IGNORE_FRIEND_REQUEST;
			update_stranger_status($usrid,$stranger_id,$status);

			break;
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
  $usrid = usrname_to_usrid($usrname);

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
  $query = "insert into stranger values
                           (NULL, '".$usrid."', '".$stranger_id."',NULL)";
  vote_db_query($query);
  return true;
}

function update_stranger_status($usrid,$stranger_id,$status)
{
	$query = "update usrinfo
				set status = '".$status."'
				where usrid = '".$usrid."' and strangerid = '".$strangerid."'";
	$ret = vote_db_query($query);
	return $ret;

}

function insert_friend_table($usrid,$friend_id)
{
  //friend relationship is bidirect, so insert two lines at the same time
  $query = "insert into friend values
                           (NULL, '".$usrid."', '".$friend_id."', NULL, NULL),
						   (NULL, '".$friend_id."', '".$usrid."', NULL, NULL)"; 
  $ret = vote_db_query($query);
  return $ret;
}

function delete_friend_db($usrid,$friendid)
{
  $delete = "delete from friend where
			usrid='".$usrid."' and friendid='".$friendid."'";
  $ret = vote_db_query($query);
  return $ret;
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

function usrname_to_usrid($usrname)
{
  $conn = db_connect();
  if(!$conn)
	return DB_CONNECT_ERROR;
  $result = $conn->query("select usrid from usrinfo where usrname='".$usrname."'");
  if($result) 
  {
	$row = $result->fetch_array();
	return $row[0];
  } 
}


?>
