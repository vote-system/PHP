<?php
require_once('db_fns.php');

$usrname = $_GET['usrname'];
$vote_id = $_GET['vote_id'];

header('Content-Type: application/json');

//first, return the vote info where user is organizer
//seconde, return the vote info where user is participants

//$query = "select * from usrinfo where usrname = '".$usrname."'";
//$usrinfo = vote_get_array($query);
//$participant_vote_ids = unserialize($usrinfo["participant_vote_id"]);

//foreach($participant_vote_ids as $vote_id)
//{
//	$query = "select * from vote_info where vote_id = '".$vote_id."'";
//	$vote_info = vote_get_array($query);
//}

$query = "select * from vote_info where vote_id = '".$vote_id."'";
$vote_info = vote_get_array($query);

//echo json_encode($vote_info);
echo json_encode($vote_info,JSON_UNESCAPED_SLASHES);

?>