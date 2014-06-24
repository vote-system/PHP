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
	$vote_info[] = vote_get_array($query);
	$votes[] = $vote_info;
}

echo json_encode($votes);

?>