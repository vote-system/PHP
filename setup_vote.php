<?php

require_once("db_fns.php");
require_once("vote_fns.php");
require_once("push_notification.php");

$vote_info = $_POST['vote_info'];
$usrname = $_POST['usrname'];

header('Content-Type: application/json');

$vote_info = json_decode($vote_info);

$organizer = $vote_info['organizer'];
$title = $vote_info['title'];
$start_timestamp = $vote_info['start_timestamp'];
$end_time = $vote_info['end_time'];
$update_timestamp = $vote_info['update_timestamp'];
$participants = $vote_info['participants'];
$options = $vote_info['options'];

if($organizer != $usrname)
{
	$setup_vote['setup_vote'] = VOTE_EXISTED; 
	echo json_encode($setup_vote);
}

$query = "select * from vote_info
		where organizer='".$organizer."' and start_timestamp='".$start_timestamp."'";
$vote_existed = vote_item_existed_test($query);
if($vote_existed == false)
{
	$participants = serialize($participants);
	$options = serialize($options);

	//echo $query;
	$query = "insert into vote_info values
             (NULL,'".$organizer."', '".$title."','".$start_time."', '".$end_time."','".$update_timestamp."','".$participants."','".$options."',NULL,NULL)";

	$ret = vote_db_query($query);
	if($ret){
		$setup_vote['setup_vote'] = SET_UP_VOTE_SUCC; 
		//echo json_encode($setup_vote);
	}else{
		$setup_vote['setup_vote'] = SET_UP_VOTE_FAIL; 
		//echo json_encode($setup_vote);
	}

	//then push the message to every user
	foreach($participants as $participant)
	{
		$usr_active = check_usr_status($to);
		//echo "usr_active = " .$usr_active;
		if($usr_active == USER_ACTIVE)
		{	
			$ret = push_message($organizer,$participant,ADD_FRIEND_REQUEST)
			continue;
		}
		else if($usr_active == USER_NOT_ACTIVE)
		{	
			//echo "USER_NOT_ACTIVE\n";
			//push the message to a queue
			$friend_action = ADD_FRIEND_REQUEST;
			//从数据库中取出该usr的未读信息，添加到尾部，在写入到数据库
			push_back_friend_message($usrid,$stranger_id,$friend_action,$append_message);

		}
		$ret = push_vote_message($organizer,$participant,$action)
	}
}

?>