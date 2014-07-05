<?php
require_once('db_fns.php');

$usrname = $_GET['usrname'];

header('Content-Type: application/json');

//first, return the vote info where user is organizer
//seconde, return the vote info where user is participants

$query = "select * from usrinfo where usrname = '".$usrname."'";
$usrinfo = vote_get_array($query);
$participant_vote_ids = unserialize($usrinfo["participant_vote_id"]);

foreach($participant_vote_ids as $vote_id)
{
	$query = "select * from vote_info where vote_id = '".$vote_id."'";
	$vote_info = vote_get_array($query);

	$vote_preview['title'] = $vote_info['title'];
	$vote_preview['private'] = $vote_info['private'];
	$vote_preview['organizer_screen_name'] = get_screen_name($vote_info['organizer']);
	$vote_preview['end_time'] = $vote_info['end_time'];
	$vote_preview['update_timestamp'] = $vote_info['update_timestamp'];
	$vote_preview['vote_timestamp'] = $vote_info['vote_timestamp'];

	$votes[] = $vote_preview;
}

echo json_encode($votes);

?>