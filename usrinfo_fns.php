<?php
require_once('db_fns.php');
require_once('vote_fns.php');

function update_friend_badge($usrname)
{
    $usrinfo = query_badge($usrname);
    $friend_badge = $usrinfo['friend_badge'];
    //echo $friend_badge;
	if($friend_badge < 0)
		return false;
	$friend_badge +=1;

	//echo $usrname;
	$query = "update usrinfo set friend_badge = '".$friend_badge."'
							where usrname = '".$usrname."'";
	$result = vote_db_query($query);
	return $result;
}

function query_badge($usrname)
{
  //save the usrid
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $usrinfo = vote_get_array($query);
  return $usrinfo;
}

function get_user_badge($usrname)
{	
	$badge = query_badge($usrname);
	$total_badge = $badge['friend_badge'] +  $badge['usr_vote_badge'];
	return $total_badge;
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
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $row = vote_get_array($query);
  return $row['usrid']; 
}

function usrid_to_usrname($usrid)
{
  $query = "select * from usrinfo where usrid='".$usrid."'";
  $row = vote_get_array($query);
  return $row['usrname']; 
}

function query_usr_info($usrname)
{
  $query = "select * from usrinfo where usrname='".$usrname."'";
  $usrinfo = vote_get_array($query);
  return $usrinfo;
}

function search_token_from_db($usrname)
{
	$query = "select * from usrinfo where usrname='".$usrname."'";
	$usrinfo = vote_get_array($query);
	$token = $usrinfo["device_token"];
	return $token;
}

function save_unpush_message($selfid,$peerid,$action)
{
	$unpush_message = array(
		"peerid" => $peerid,
		"action" => $action,	
		//"append_message" => $append_message,
	);
	
	//item existed, first query the item, then update it
	$query = "select * from unread_message where usrid='".$usrid."'";
	$unread_message_item = vote_get_array($query);
	$message_string = $unread_message_item['message'];
	$unread_message = unserialize($message_string);
	//echo "before message:\n ";
	//print_r($unread_message);
	$unread_message[] = $unpush_message;
	//echo " after message:\n";
	//print_r($unread_message);
	$message_string = serialize($unread_message);
	//write the array back to the database
	$query = "update unread_message
			set message = '".$message_string."'
			where usrid = '".$usrid."'";
	$ret = vote_db_query($query);
	return $ret;

}

function get_screen_name($usrname)
{	
	$query = "select * from usrinfo where usrname='".$usrname."'";
	$usrinfo = vote_get_array($query);
	$screen_name = $usrinfo['screen_name'];
	return $screen_name;
}

?>