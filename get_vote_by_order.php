<?php
require_once('db_fns.php');
require_once('usrinfo_fns.php');

$usrname = $_GET['usrname'];
$begin_number = $_GET['begin_number'];
$count = $_GET['count'];

$end = $begin_number + $count;
header('Content-Type: application/json');

//first, return the vote info where user is organizer
//seconde, return the vote info where user is participants
$query = "select vote_id from vote_info order by participants_number desc limit ".$begin_number.",".$end."";
$vote_ids = vote_get_assoc($query);
//var_dump($vote_ids);
$i = 0;

foreach($vote_ids as $key => $value)
{
	if($i >= $count)
		break;

	$vote_id = $vote_ids[$key]['vote_id'];
	$query = "select * from vote_info where vote_id = '".$vote_id."'";
	$vote_info = vote_get_array($query);
	if(!$vote_info)
		break;

	$vote_preview['title'] = $vote_info['title'];
	$vote_preview['vote_id'] = (int)$vote_info['vote_id'];
	$vote_preview['private'] = (int)$vote_info['private'];
	$vote_preview['organizer'] = $vote_info['organizer'];
	$vote_preview['organizer_screen_name'] = get_screen_name($vote_info['organizer']);
	$vote_preview['end_time'] = (double)$vote_info['end_time'];
	$vote_preview['start_time'] = (double)$vote_info['start_time'];
	$vote_preview['category'] = $vote_info['category'];
	$vote_preview['basic_timestamp'] = (int)$vote_info['basic_timestamp'];
	$vote_preview['vote_timestamp'] = (int)$vote_info['vote_timestamp'];
	$vote_preview['anonymous'] = (int)$vote_info['anonymous'];
	$vote_preview['the_public'] = (int)$vote_info['the_public'];
	$vote_preview['description'] = $vote_info['description'];
	$vote_preview['image_url'] = $vote_info['image_url'];
	$vote_preview['participants_number'] = $vote_info['participants_number'];

	$vote_array[] = $vote_preview;

	$i++;
}

if(!$vote_array)
{
	$votes['votes_by_order'] = NULL;
}
else
{
	$votes['votes_by_order'] = $vote_array;
}
echo json_encode($votes,JSON_UNESCAPED_SLASHES);

?>