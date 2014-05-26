<?php
require_once("vote_fns.php");
require_once("push_message_to_ios.php");
//require_once("badge_fns.php");
handle_add_fri_req("dingyi","test","");

function handle_add_fri_req($from,$to,$message)
{
	//1.add item to stranger table
	//add two item for each require
	$usrid = usrname_to_usrid($from);
	$stranger_id = usrname_to_usrid($to);
	$status = ADD_FRIEND_SEND;
	insert_stranger_table($usrid,$stranger_id,$status);

	//add the peer item to stranger table
	$usrid = usrname_to_usrid($to);
	$stranger_id = usrname_to_usrid($from);
	$status = IGNORE_FRIEND_RESPONSE;
	insert_stranger_table($usrid,$stranger_id,$status);

	//2.update the friend_badge in table usrinfo
	update_friend_badge($to,ADD_BADGE);

	//3.prepare to push message to peer
	//	query the token and count the total badge
	//  then push message to peer
	$token = search_token_from_db($to);

	$friend_badge = query_badge("friend_badge",$to);
	$vote_badge = query_badge("vote_badge",$to);
	$total_badge = $friend_badge + $vote_badge;

	push_message($from,$to,ADD_FRIEND_REQUEST,$token,$message,$total_badge);
}

function handle_agree_add_fri($from,$to)
{
	//1.search the device token of $to
	$token = search_token_from_db($to);

	//2.push the response to the peer
	push_message($from,$to,$response,$token);

	$usrid = usrname_to_usrid($from);
	$stranger_id = usrname_to_usrid($to);

	$status = AGREE_ADD_FRIEND;		  
	update_stranger_status($usrid,$stranger_id,$status);

	//add two rows to friend table
	$friend_id = $stranger_id;
	insert_friend_table($usrid,$friend_id);	
}

function handle_del_fri_req($from,$to)
{
	//delete the item from database
	$ret = delete_friend_db($from,$to);
	if(!$ret)
		return true;
	else
		return false;
}

function search_token_from_db($usrname)
{
	$query = "select * from usrinfo where usrname='".$usrname."'";
	$usrinfo = vote_get_array($query);
	$token = $usrinfo["device_token"];
	return $token;
}

function insert_stranger_table($usrid,$stranger_id,$status)
{
  $query = "insert into stranger values
                           (NULL, '".$usrid."', '".$stranger_id."','".$status."')";
  $ret = vote_db_query($query);
  return $ret;
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
  $query = "select * from usrinfo where usrid='".$friendid."'";
  $rows = vote_get_assoc($query);
  foreach($rows as $row) 
  {
	 //only return part of items in table usrinfo
     $friend_info = array_slice($row,5,15);
	 $friend_array["friends_array"] = $friend_info;
	 //not sure whether need to put the following line out of the while loop
	 echo json_encode($friend_info);
  }
}

function usrname_to_usrid($usrname)
{
  $query = "select usrid from usrinfo where usrname='".$usrname."'";
  $row = vote_get_array($query);
  return $row['usrid']; 
}


?>
