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
		where organizer='".$organizer."' 
		and start_timestamp='".$start_timestamp."'";
$vote_existed = vote_item_existed_test($query);

if(!$vote_existed)
{
	$participants = serialize($participants);
	$options = serialize($options);

	//echo $query;
	$query = "insert into vote_info values
             (NULL,'".$organizer."', '".$title."','".$start_time."', '".$end_time."',
			 '".$update_timestamp."','".$participants."','".$options."',NULL,NULL)";
	$ret = vote_db_query($query);
	if($ret){
		$setup_vote['setup_vote'] = SET_UP_VOTE_SUCC; 
		//echo json_encode($setup_vote);
	}else{
		$setup_vote['setup_vote'] = SET_UP_VOTE_FAIL; 
		//echo json_encode($setup_vote);
	}

	$query = "select * from vote_info where organizer='".$usrname."' and start_time = '".$start_time."'";
	$vote_info = vote_get_array();
	$vote_id = $vote_info['vote_id'];

	$query = "select * from usrinfo where usrname='".$usrname."'";
	$usrinfo = vote_get_array();
	$participant_vote_id = unserialize($usrinfo['participant_vote_id']);
	$participant_vote_id[] = $vote_id;
	$participant_vote_id = serialize($participant_vote_id);

	$query = "update usrinfo
				set participant_vote_id = '".$participant_vote_id."'
				where usrname = '".$usrname."'";
	$ret = vote_db_query($query);

	//then push the message to every user
	foreach($participants as $participant)
	{
		$usr_active = check_usr_status($participant);
		//echo "usr_active = " .$usr_active;
		if($usr_active == USER_ACTIVE)
		{	
			$ret = push_notification($organizer,$participant,VOTE_NOTIFICATION)
			continue;
		}
		else if($usr_active == USER_NOT_ACTIVE)
		{	
			//echo "USER_NOT_ACTIVE\n";
			//push the message to a queue

			$participant_id = usrname_to_usrid($participant);
			$organizer_id = usrname_to_usrid($organizer);
			save_unpush_message($participant_id,$organizer_id,VOTE_NOTIFICATION);
		}
	}
}

?>