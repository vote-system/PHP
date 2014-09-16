<?php

require_once("db_fns.php");
require_once("vote_fns.php");
//require_once("usrinfo_fns.php");
//require_once("time.php");

header('Content-Type: application/json');

$usrname = $_POST['usrname'];
$vote_id = $_POST['vote_id'];
//$old_selections = $_POST['old_selections'];
//$new_selections = $_POST['new_selections'];

$query = "select * from vote_info where vote_id = '".$vote_id."'";
$vote_info = vote_db_query($query);

$participants = $vote_info['participants'];
$participants = unserialize($participants);
unset($participants['$usrname']);

$query = "update vote_info set participants = '".$participants."'
							where vote_id = '".$vote_id."'";
$ret = vote_db_query($query);


$query = "select * from usrinfo where usrname = '".$usrname."'";
$usrinfo = vote_db_query($query);
$participant_vote_id = unserialize($usrinfo['participant_vote_id']);

foreach ($participant_vote_id as $key => $id) {
	if($vote_id == $id)
    unset($array[$key]);
}

$participant_vote_id = serialize($participant_vote_id);
$query = "update vote_info set participants = '".$participants."'
							where vote_id = '".$vote_id."'";
$ret = vote_db_query($query);
?>