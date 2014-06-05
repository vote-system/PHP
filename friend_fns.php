<?php
require_once("db_fns.php");
require_once("push_message_to_ios.php");
require_once("vote_fns.php");
require_once("usrinfo_fns.php");

//handle_add_fri_req("dingyi","test","");


function handle_add_fri_req($from,$to,$append_message)
{
	//update badge number
	//$badge_arr = query_badge($to);
	//$friend_badge = $badge_arr['friend_badge'];
	//$friend_badge +=1;
	//$query = "update usrinfo
	//			set friend_badge = '".$friend_badge."'
	//			where usrname = '".$usrname."'";
	//$ret = vote_db_query($query);	

	//1.add item to stranger table
	//add two item for each require
	$stranger_id = usrname_to_usrid($from);
	$usrid = usrname_to_usrid($to);

	$ret = stranger_item_existed($usrid,$stranger_id);
	//echo $ret;
	if($ret == DB_ITEM_FOUND)
	{ 
		//item already existed, do nothing, goon next step
		//return false;
	}
	else if($ret == DB_ITEM_NOT_FOUND)
	{
		$status = ADD_FRIEND_SEND;
		insert_stranger_table($usrid,$stranger_id,$status);

		$status = IGNORE_FRIEND_RESPONSE;
		insert_stranger_table($stranger_id,$usrid,$status);

		
	}

	//2.update the friend_badge in table usrinfo
	update_friend_badge($to);

	//3.prepare to push message to peer
	
	$usr_active = check_usr_status($to);
	//echo "usr_active = " .$usr_active;
	if($usr_active == USER_ACTIVE)
	{	
		$ret = push_message($from,$to,ADD_FRIEND_REQUEST,$append_message);
		return $ret;
	}
	else if($usr_active == USER_NOT_ACTIVE)
	{	
		//echo "USER_NOT_ACTIVE\n";
		//push the message to a queue
		$friend_action = ADD_FRIEND_REQUEST;
		//从数据库中取出该usr的未读信息，添加到尾部，在写入到数据库
		push_back_friend_message($usrid,$stranger_id,$friend_action,$append_message);

	}
}

function handle_agree_add_fri($from,$to)
{
	update_friend_badge($to);

	$stranger_id = usrname_to_usrid($from);
	$usrid = usrname_to_usrid($to);
	//echo "usrid=" . $usrid;
	//echo " stranger_id=" . $stranger_id;
	$status = AGREE_ADD_FRIEND;		
	//echo "status=" . $status;
	update_stranger_status($usrid,$stranger_id,$status);

	//add two rows to friend table
	$friend_id = $stranger_id;
	insert_friend_table($usrid,$friend_id);	

	$usr_active = check_usr_status($to);
	if($usr_active == USER_ACTIVE)
	{
		$message = "";
		push_message($from,$to,AGREE_ADD_FRIEND,$message);
	}
	else if($usr_active == USER_NOT_ACTIVE)
	{
		//push the message to a queue
		$friend_action = AGREE_ADD_FRIEND;
		$append_message = "";
		//从数据库中取出该usr的未读信息，添加到尾部，在写入到数据库
		push_back_friend_message($usrid,$stranger_id,$friend_action,$append_message);

	}
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

function insert_stranger_table($usrid,$stranger_id,$status)
{
	//echo "function insert_stranger_table";
  
  $query = "insert into stranger values
                           (NULL, '".$usrid."', '".$stranger_id."','".$status."')";
  $ret = vote_db_query($query);
  return $ret;
}

function update_stranger_status($usrid,$stranger_id,$status)
{
	$query = "update stranger
				set status = '".$status."'
				where usrid = '".$usrid."' and stranger_id = '".$stranger_id."'";
	$ret = vote_db_query($query);
	return $ret;
}

function insert_friend_table($usrid,$friend_id)
{
  //echo "function insert_stranger_table";
  $ret = friend_item_existed($usrid,$friend_id);
  //echo $ret;
  if($ret == DB_ITEM_FOUND)
	  return;
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

function stranger_item_existed($usrid,$stranger_id)
{
	//echo "function stranger_item_existed";
	  $query = "select * from stranger where usrid='".$usrid."'
				and stranger_id='".$stranger_id."'" ;
	  $name_existed = vote_item_existed_test($query);
	  //echo $name_existed;
	  if($name_existed)
		  return DB_ITEM_FOUND;
	  else
		  return DB_ITEM_NOT_FOUND;
}

function friend_item_existed($usrid,$friend_id)
{
	//echo "function stranger_item_existed";
	  $query = "select * from friend where usrid='".$usrid."'
				and friend_id='".$friend_id."'" ;
	  $name_existed = vote_item_existed_test($query);
	  //echo $name_existed;
	  if($name_existed)
		  return DB_ITEM_FOUND;
	  else
		  return DB_ITEM_NOT_FOUND;
}

function check_usr_status($usrname)
{
	$query = "select * from usrinfo where usrname='".$usrname."'" ;
	$usrinfo = vote_get_array($query);
	return $usrinfo['active'];
}

function push_back_friend_message($usrid,$stranger_id,$friend_action,$append_message)
{
	$stranger_message = array(
		"stranger_id" => $stranger_id,
		"action" => $friend_action,	
		"append_message" => $append_message,
	);
	
	$query = "select * from unread_message where usrid='".$usrid."'";
	$item_existed = vote_item_existed_test($query);

	
	if($item_existed == true){
		//item existed, first query the item, then update it
		$query = "select * from unread_message where usrid='".$usrid."'";
		$unread_message_item = vote_get_array($query);
		$message_string = $unread_message_item['message'];
		$unread_message = unserialize($message_string);
		//echo "before message:\n ";
		//print_r($unread_message);
		$unread_message[] = $stranger_message;
		//echo " after message:\n";
		//print_r($unread_message);
		$message_string = serialize($unread_message);
		//write the array back to the database
		$query = "update unread_message
				set message = '".$message_string."'
				where usrid = '".$usrid."'";
		$ret = vote_db_query($query);
		return $ret;
	}else if($item_existed == false){
		$unread_message[] = $stranger_message;
		$message_string = serialize($unread_message);
		$query = "insert into unread_message values
				(NULL,'".$usrid."','".$message_string."')";
		$ret = vote_db_query($query);
		return $ret;
	}

}

?>
