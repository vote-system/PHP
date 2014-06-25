<?php

require_once("db_fns.php");
require_once("vote_fns.php");

header('Content-Type: application/json');

$usrname = $_POST['usrname'];
//$organizer = $_POST['usrname'];
$vote_id = $_POST['vote_id'];
$selections = $_POST['selections'];

$query = "select * from vote_info where vote_id = '".$vote_id."'";
$vote_info = vote_get_array($query);
$participants = $vote_info['participants'];
$participants = unserialize($participants);

//only if $usrname is in participants name list that allow $usrname to vote;
$bool_vote_allowed = true;

foreach($participants as $participant)
{
	if(!strcmp($participant,$usrname)){
		$bool_vote_allowed = true;
		break;
	}else{
		continue;
	}
}

if(!$bool_vote_allowed )
{
	$submit_vote['submit_vote'] = SUBMIT_VOTE_ERROR;
	echo json_encode($submit_vote);
	return;
}

$vote_detail = unserialize($vote_info['vote_detail']);

foreach($selections as $selection){
	$vote_detail[$selection-1] = $usrname;
}
$vote_detail = unserialize($vote_detail);

$query = "update vote_info
		set vote_detail = '".$vote_detail."'
		where vote_id = '".$vote_id."'";	
$ret = vote_db_query($query);	

if($ret){
	$submit_vote['submit_vote'] = SUBMIT_VOTE_SUCC;
}else{
	$submit_vote['submit_vote'] = SUBMIT_VOTE_ERROR;
}

echo json_encode($submit_vote);

?>