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
	$vote_preview['vote_id'] = (int)$vote_info['vote_id'];
	$vote_preview['private'] = $vote_info['private'];
	$vote_preview['organizer'] = $vote_info['organizer'];
	$vote_preview['end_time'] = $vote_info['end_time'];
	$vote_preview['basic_timestamp'] = $vote_info['basic_timestamp'];
	$vote_preview['vote_timestamp'] = $vote_info['vote_timestamp'];

	$vote_array[] = $vote_preview;
}

$votes['votes'] = $vote_array;
echo json_encode($votes,JSON_UNESCAPED_SLASHES);

?>