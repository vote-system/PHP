<?php
require_once("db_fns.php");
require_once("push_message_to_ios.php");
require_once("vote_fns.php");

//require_once("badge_fns.php");
//handle_add_fri_req("dingyi","test","");

function handle_add_fri_req($from,$to,$message)
{
	//1.add item to stranger table
	//add two item for each require
	$usrid = usrname_to_usrid($from);
	$stranger_id = usrname_to_usrid($to);
	$ret = stranger_item_existed($usrid,$stranger_id);
	//echo $ret;
	if($ret == DB_ITEM_FOUND)
	{ 
		//item already existed, just return
		return false;
	}
	else{
		$status = ADD_FRIEND_SEND;
		insert_stranger_table($usrid,$stranger_id,$status);

		$status = IGNORE_FRIEND_RESPONSE;
		insert_stranger_table($stranger_id,$usrid,$status);

		//2.update the friend_badge in table usrinfo
		update_friend_badge($to,ADD_BADGE);

		//3.prepare to push message to peer
		//	query the token and count the total badge
		//  then push message to peer
		$token = search_token_from_db($to);

		$friend_badge = query_badge("friend_badge",$to);
		$vote_badge = query_badge("vote_badge",$to);
		$total_badge = $friend_badge + $vote_badge;
		
		$usr_active = check_usr_status($usrname);
		if($usr_active == USR_ACTIVE)
		{	
			$ret = push_message($from,$to,ADD_FRIEND_REQUEST,$token,$message,$total_badge);
			if(!$ret)
				return false;
			else
				return true;
		}
		else if($usr_active == USR_NOT_ACTIVE)
		{
			//push the message to a queue
			$friend_action = ADD_FRIEND_REQUEST;
			//�����ݿ���ȡ����usr��δ����Ϣ����ӵ�β������д�뵽���ݿ�
			push_back_friend_message($usrid,$friend_action);

		}
	}
	
}

function handle_agree_add_fri($from,$to)
{
	$usrid = usrname_to_usrid($from);
	$stranger_id = usrname_to_usrid($to);
	//echo "usrid=" . $usrid;
	//echo " stranger_id=" . $stranger_id;
	$status = AGREE_ADD_FRIEND;		
	//echo "status=" . $status;
	update_stranger_status($usrid,$stranger_id,$status);

	//add two rows to friend table
	$friend_id = $stranger_id;
	insert_friend_table($usrid,$friend_id);	

	$usr_active = check_usr_status($usrname);
	if($usr_active == USR_ACTIVE)
	{
		//1.search the device token of $to
		$token = search_token_from_db($to);

		$friend_badge = query_badge("friend_badge",$to);
		$vote_badge = query_badge("vote_badge",$to);
		$total_badge = $friend_badge + $vote_badge;

		//2.push the response to the peer
		$message = "";
		push_message($from,$to,AGREE_ADD_FRIEND,$token,$message,$total_badge);
	}
	else if($usr_active == USR_NOT_ACTIVE)
	{
		//push the message to a queue
		$friend_action = AGREE_ADD_FRIEND;
		//�����ݿ���ȡ����usr��δ����Ϣ����ӵ�β������д�뵽���ݿ�
		push_back_friend_message($usrid,$friend_action);

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

function search_token_from_db($usrname)
{
	$query = "select * from usrinfo where usrname='".$usrname."'";
	$usrinfo = vote_get_array($query);
	$token = $usrinfo["device_token"];
	return $token;
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
	$query = "select active from usrinfo where usrname='".$usrname."'" ;
	$usrinfo = vote_get_array($query);
	return $usrinfo['active'];
}

function push_back_friend_message($usrid,$friend_action)
{
	$stranger_message = array(
		"usrid" => $usrid;
		"action" => $friend_action;
	);

	$query = "select * from usrinfo where usrname='".$usrname."'";
	$unread_message = vote_get_array($query);

	$unread_message[] = $stranger_message;
	
	//write the array back to the database
	$query = "update unread_message
			set message = '".$$unread_message."'
			where usrid = '".$usrid."'";
	$ret = vote_db_query($query);
	return $ret;
}

?>
