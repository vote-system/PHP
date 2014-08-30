<?php
require_once('db_fns.php');
require_once('usrinfo_fns.php');

$usrname = $_GET['usrname'];
$vote_id = $_GET['vote_id'];

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

	$vote_preview['vote_id'] = (int)$vote_info['vote_id'];
	$vote_preview['organizer'] = $vote_info['organizer'];
	$vote_preview['title'] = $vote_info['title'];
	$vote_preview['start_time'] = (double)$vote_info['start_time'];
	$vote_preview['end_time'] = (double)$vote_info['end_time'];
	$vote_preview['basic_timestamp'] = (int)$vote_info['basic_timestamp'];
	$vote_preview['vote_timestamp'] = (int)$vote_info['vote_timestamp'];
	$vote_preview['category'] = $vote_info['category'];
	$vote_preview['max_choice'] = (int)$vote_info['max_choice'];
	$vote_preview['participants'] = unserialize($vote_info['participants']);
	$vote_preview['options'] = unserialize($vote_info['options']);
	$vote_preview['vote_detail'] = unserialize($vote_info['vote_detail']);
	$vote_preview['private'] = (int)$vote_info['private'];

}

//echo json_encode($vote_preview);
echo json_encode($vote_preview,JSON_UNESCAPED_SLASHES);

?>