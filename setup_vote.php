<?php

require_once("db_fns.php");
require_once("vote_fns.php");
require_once("push_notification.php");
require_once("time.php");

header('Content-Type: application/json');

//$usrname = $_POST['usrname'];
//$vote_info = $_POST['vote_info'];
//$vote_info = json_decode($_POST['vote_info'],true);
//echo $vote_info;

$raw_post_data = file_get_contents('php://input', 'r');
//echo $raw_post_data;

$raw_post_data_json = json_decode($raw_post_data,true);
//print_r($raw_post_data_json);

$usrname = $raw_post_data_json['usrname'];
$vote_info = $raw_post_data_json['vote_info'];

$organizer = $vote_info['organizer'];
$title = $vote_info['title'];
$start_time = $vote_info['start_time'];
$end_time = $vote_info['end_time'];
$category = $vote_info['category'];
$max_choice = $vote_info['max_choice'];
$participants = $vote_info['participants'];
$options = $vote_info['options'];
$private = $vote_info['private'];
$anonymous = $vote_info['anonymous'];
$the_public = $vote_info['the_public'];
$description = $vote_info['description'];
$image_url = $vote_info['image_url'];

//echo $options;
//echo "\n";
//print_r($options);

define("VOTE_DEBUG",0);

if(strcmp($organizer,$usrname))
{
	$setup_vote['setup_vote'] = SET_UP_VOTE_FAIL; 
	echo json_encode($setup_vote);
	return;
}

$query = "select * from vote_info
		where organizer='".$organizer."' 
		and start_time='".$start_time."'";
$vote_existed = vote_item_existed_test($query);

$vote_existed = null;
if($vote_existed)
{
	$setup_vote['setup_vote'] = VOTE_EXISTED; 
	echo json_encode($setup_vote);
	return;
}
else
{
	$participants_db = serialize($participants);
	$options = serialize($options);
	//echo $options;
	//echo "\n";

	$timestamp = get_current_timestamp();

	//echo $query;
	$query = "insert into vote_info values
             (NULL,'".$organizer."', '".$title."','".$start_time."', '".$end_time."',
			 '".$timestamp."','".$timestamp."','".$category."','".$max_choice."',
			 '".$participants_db."','".$options."',NULL,'".$private."','".$anonymous."',
			 ,'".$the_public."','".$description."','".$image_url."')";
	$ret = vote_db_query($query);
	if($ret)
	{
		$setup_vote['setup_vote'] = SET_UP_VOTE_SUCC; 
	}
	else
	{
		$setup_vote['setup_vote'] = SET_UP_VOTE_FAIL; 
		echo json_encode($setup_vote);
		return;
	}

	$query = "select * from vote_info where organizer='".$organizer."' and start_time = '".$start_time."'";
	$vote_info = vote_get_array($query);
	$setup_vote['vote_id'] = (int)$vote_info['vote_id']; 
	$setup_vote['basic_timestamp'] = (int)$vote_info['basic_timestamp']; 
	$setup_vote['vote_timestamp'] = (int)$vote_info['vote_timestamp']; 

	save_vote_id($usrname,$setup_vote['vote_id']);
	
	save_and_push_vote_info($usrname,$setup_vote['vote_id'],$organizer);

	update_vote_info_timestamp($vote_id);

	echo json_encode($setup_vote);
}
function save_vote_id($usrname,$vote_id)
{	
	$query = "select * from usrinfo where usrname='".$usrname."'";
	$usrinfo = vote_get_array($query);

	
	$participant_vote_id = unserialize($usrinfo['participant_vote_id']);
	$participant_vote_id[] = $vote_id;
	$participant_vote_id = serialize($participant_vote_id);

	//set the default value for the following two value
	$vote_notification = unserialize($usrinfo['vote_notification']);
	$vote_notification[$vote_id] = true;
	$vote_notification = serialize($vote_notification);

	$vote_delete_forever = unserialize($usrinfo['vote_delete_forever']);
	$vote_delete_forever[$vote_id] = true;
	$vote_delete_forever = serialize($vote_delete_forever);

	$query = "update usrinfo
				set participant_vote_id = '".$participant_vote_id."',
				vote_notification = '".$vote_notification."',
				vote_delete_forever = '".$vote_delete_forever."'
				where usrname = '".$usrname."'";
	$ret = vote_db_query($query);
	return $ret;
	
}

function save_and_push_vote_info($usrname,$vote_id,$organizer)
{
	//then push the message to every user
	foreach($participants as $participant)
	{
		save_vote_id($participant,$vote_id);
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
