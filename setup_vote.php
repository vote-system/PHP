<?php

require_once("db_fns.php");
require_once("vote_fns.php");
require_once("push_notification.php");
require_once("time.php");

header('Content-Type: application/json');

$usrname = $_POST['usrname'];
$vote_info = $_POST['vote_info'];

//print_r($vote_info);

//$vote_info = json_decode($vote_info);

//echo $usrname;
//print_r($vote_info);

$organizer = $vote_info['organizer'];
$title = $vote_info['title'];
$start_time = $vote_info['start_time'];
$end_time = $vote_info['end_time'];
$category = $vote_info['category'];
$participants = $vote_info['participants'];
$options = $vote_info['options'];
$private = $vote_info['private'];

define("VOTE_DEBUG",1);
if(VOTE_DEBUG){
	$organizer = "dingyi";
	$start_time = "121312131213";
}

if($organizer != $usrname)
{
	$setup_vote['setup_vote'] = VOTE_EXISTED; 
	echo json_encode($setup_vote);
}

$query = "select * from vote_info
		where organizer='".$organizer."' 
		and start_time='".$start_time."'";
$vote_existed = vote_item_existed_test($query);

if(!$vote_existed)
{
	$participants_db = serialize($participants);
	$options = serialize($options);

	//echo $query;
	$query = "insert into vote_info values
             (NULL,'".$organizer."', '".$title."','".$start_time."', '".$end_time."',
			 '".$update_timestamp."','".$vote_timestamp."','".$category."','".$participants_db."','".$options."','".$private."')";
	$ret = vote_db_query($query);
	if($ret){
		$setup_vote['setup_vote'] = SET_UP_VOTE_SUCC; 

		update_vote_info_timestamp($organizer,$vote_id);
		update_vote_timestamp($organizer,$vote_id);
		
		//echo json_encode($setup_vote);
	}else{
		$setup_vote['setup_vote'] = SET_UP_VOTE_FAIL; 
		echo json_encode($setup_vote);
		return;
	}

	$query = "select * from vote_info where organizer='".$usrname."' and start_time = '".$start_time."'";
	$vote_info = vote_get_array($query);
	print_r($vote_info);
	$setup_vote['vote_id'] = $vote_info['vote_id']; 
	$setup_vote['update_timestamp'] = $vote_info['update_timestamp']; 
	$setup_vote['vote_timestamp'] = $vote_info['vote_timestamp']; 

    save_and_push_vote_info($usrname,$participant_vote_id,$organizer);

	echo json_encode($setup_vote);
}

function save_and_push_vote_info($usrname,$participant_vote_id,$organizer)
{
	//record the vote_id every user participate
	$query = "select * from usrinfo where usrname='".$usrname."'";
	$usrinfo = vote_get_array($query);
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
			$ret = push_notification($organizer,$participant,VOTE_NOTIFICATION);
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