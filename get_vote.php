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
	if(!$vote_info)
		break;

	$vote_preview['title'] = $vote_info['title'];
	$vote_preview['vote_id'] = (int)$vote_info['vote_id'];
	$vote_preview['private'] = (int)$vote_info['private'];
	$vote_preview['organizer'] = $vote_info['organizer'];
	$vote_preview['end_time'] = (double)$vote_info['end_time'];
	$vote_preview['start_time'] = (double)$vote_info['start_time'];
	$vote_preview['category'] = $vote_info['category'];
	$vote_preview['basic_timestamp'] = (int)$vote_info['basic_timestamp'];
	$vote_preview['vote_timestamp'] = (int)$vote_info['vote_timestamp'];
	$vote_preview['anonymous'] = (int)$vote_info['anonymous'];
	$vote_preview['the_public'] = (int)$vote_info['the_public'];
	$vote_preview['description'] = $vote_info['description'];
	$vote_preview['image_url'] = $vote_info['image_url'];


	$vote_array[] = $vote_preview;
}

if(!$vote_array)
{
	$votes['votes'] = NULL;

}
else
{
	$votes['votes'] = $vote_array;
}
echo json_encode($votes,JSON_UNESCAPED_SLASHES);

?>