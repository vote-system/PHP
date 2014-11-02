<?php

require_once("db_fns.php");
require_once("vote_fns.php");
require_once("push_notification.php");
require_once("time.php");
require_once("usrinfo_fns.php");
require_once("friend_fns.php");

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
//$category = $vote_info['category'];
$max_choice = $vote_info['max_choice'];
$participants = $vote_info['participants'];
$options = $vote_info['options'];
//$private = $vote_info['private'];
$anonymous = $vote_info['anonymous'];
$the_public = $vote_info['the_public'];
$description = $vote_info['description'];
$image_url = $vote_info['image_url'];
$city = $vote_info['city'];

define("VOTE_DEBUG",0);

//save_vote_id("zhaobo",45);
//push_vote_info("dingyi","zhaobo");

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

//$vote_existed = null;
if($vote_existed)
{
	$setup_vote['setup_vote'] = VOTE_EXISTED; 
	echo json_encode($setup_vote);
	return;
}
else
{
	$participants_number = count($participants);
	//echo $participants_number;

	$participants_db = serialize($participants);
	//echo $participants . "\n";
	$options = serialize($options);
	//echo $options;
	//echo "\n";

	$timestamp = get_current_timestamp();

	//echo $query;
	$query = "insert into vote_info values
             (NULL,'".$organizer."', '".$title."','".$start_time."', '".$end_time."',
			 '".$timestamp."','".$timestamp."',NULL,'".$max_choice."',
			 '".$participants_db."','".$options."',NULL,NULL,'".$anonymous."',
			 '".$the_public."','".$description."','".$image_url."','".$participants_number."','".$city."')";

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

	
	$vote_id = $setup_vote['vote_id'];
	//print_r($participants);
	
	foreach($participants as $participant)
	{
		//echo $participant['usrname'];
		$usrname = $participant['usrname'];
		save_vote_id($usrname,$vote_id);
		push_vote_info($usrname,$organizer);
		update_vote_badge($usrname);
	}

	delete_organizer_vote_badge($organizer);

	update_vote_info_timestamp($vote_id);
	
	echo json_encode($setup_vote);
}

function push_vote_info($usrname,$organizer)
{
	//then push the message to every user
	
	$usr_active = check_usr_status($usrname);
	//echo "usr_active = " . $usr_active;
	//echo "usr_active = " .$usr_active;
	if($usr_active == USER_ACTIVE)
	{	
		$ret = push_notification($organizer,$usrname,VOTE_NOTIFICATION);
	}
	else if($usr_active == USER_NOT_ACTIVE)
	{	
		//echo "USER_NOT_ACTIVE\n";
		//push the message to a queue
		//echo "participant_id = " . $participant_id . "\n";
		//echo "organizer_id = " . $organizer_id . "\n";

		$participant_id = usrname_to_usrid($usrname);
		$organizer_id = usrname_to_usrid($organizer);
		save_unpush_message($participant_id,$organizer_id,VOTE_NOTIFICATION);
	}
}



function save_vote_id($usrname,$vote_id)
{	
	//$setup_vote['participant_usrname'] = $usrname; 

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
	$vote_delete_forever[$vote_id] = false;
	$vote_delete_forever = serialize($vote_delete_forever);

	$query = "update usrinfo
				set participant_vote_id = '".$participant_vote_id."',
				vote_notification = '".$vote_notification."',
				vote_delete_forever = '".$vote_delete_forever."'
				where usrname = '".$usrname."'";
	$ret = vote_db_query($query);
	return $ret;
	
}



?>
