<?php

require_once("db_fns.php");
require_once("vote_fns.php");
require_once("usrinfo_fns.php");
require_once("time.php");

header('Content-Type: application/json');

$usrname = $_POST['usrname'];
//$organizer = $_POST['usrname'];
$vote_id = $_POST['vote_id'];
$old_selections = $_POST['old_selections'];
$new_selections = $_POST['new_selections'];

$query = "select * from vote_info where vote_id = '".$vote_id."'";
$vote_info = vote_get_array($query);
$participants = $vote_info['participants'];
$participants = unserialize($participants);

//only if $usrname is in participants name list that allow $usrname to vote;
$bool_vote_allowed = true;


/*
if(!$bool_vote_allowed )
{
	$submit_vote['submit_vote'] = SUBMIT_VOTE_ERROR;
	echo json_encode($submit_vote);
	return;
}
*/
$vote_detail = unserialize($vote_info['vote_detail']);

//first submit vote,old_selections is null, else old_selections have values
$screen_name = get_screen_name($usrname);
//print_r($old_selections);

//print_r($new_selections);

if($old_selections && $new_selections )
{
	foreach($old_selections as $selection)
	{
		//unset($vote_detail[$selection][$usrname]);
		//unset($vote_detail[$selection]['screen_name'][$screen_name]);
		//$vote_detail[$selection][$usrname] = NULL;
		unset($vote_detail[$selection]);
		//print_r($vote_detail[$selection][$usrname]);
	}

	foreach($new_selections as $selection)
	{
		$vote_detail[$selection][$usrname] = $screen_name;
		//print_r($vote_detail[$selection][$usrname]);
	}
}
else if($new_selections)
{
	foreach($new_selections as $selection)
	{
		$vote_detail[$selection][$usrname] = $screen_name;
	}
}
else
{
	$submit_vote['submit_vote'] = SUBMIT_VOTE_ERROR;
	echo json_encode($submit_vote);
	return;
}

//push notification to the participant
foreach($participants as $participant)
{
	/*
	if(!strcmp($participant['usrname'],$usrname)){
		$bool_vote_allowed = true;
		break;
	}else{
		continue;
	}
	*/
	update_vote_badge($usrname);
	$usr_active = check_usr_status($participant['usrname']);
	//echo "usr_active = " .$usr_active;
	if($usr_active == USER_ACTIVE)
	{	
		$ret = push_notification($organizer,$participant['usrname'],NEW_VOTE_NOTIFICATION);
		continue;
	}
	else if($usr_active == USER_NOT_ACTIVE)
	{	
		//echo "USER_NOT_ACTIVE\n";
		//push the message to a queue

		$participant_id = usrname_to_usrid($participant['usrname']);
		$organizer_id = usrname_to_usrid($organizer);
		save_unpush_message($participant_id,$organizer_id,NEW_VOTE_NOTIFICATION);
	}
}

delete_organizer_vote_badge($organizer);

$vote_detail = serialize($vote_detail);

$query = "update vote_info
		set vote_detail = '".$vote_detail."'
		where vote_id = '".$vote_id."'";	
$ret = vote_db_query($query);	

if($ret){
	$submit_vote['submit_vote'] = SUBMIT_VOTE_SUCC;
}else{
	$submit_vote['submit_vote'] = SUBMIT_VOTE_ERROR;
}

update_vote_timestamp($vote_id);

echo json_encode($submit_vote);

?>