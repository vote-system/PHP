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
$vote_info = vote_get_array($query);

/*delete vote_info table*/
$participants = $vote_info['participants'];
$participants = unserialize($participants);

foreach ($participants as $key => $value) {
	if($participants[$key]['usrname'] == $usrname)
	{
		unset($participants[$key]);
	}
}

$participants = serialize($participants);

$query = "update vote_info set participants = '".$participants."'
							where vote_id = '".$vote_id."'";
$ret1 = vote_db_query($query);

/*delete usrinfo table*/
$query = "select * from usrinfo where usrname = '".$usrname."'";
$usrinfo = vote_get_array($query);

$participant_vote_id = unserialize($usrinfo['participant_vote_id']);
foreach ($participant_vote_id as $key => $value) {
	if($vote_id == $value)
		unset($participant_vote_id[$key]);
}
$participant_vote_id = serialize($participant_vote_id);

/*
$vote_notification = unserialize($usrinfo['vote_notification']);
$vote_notification[$vote_id]=NULL;
$vote_notification = serialize($vote_notification);

$vote_delete_forever = unserialize($usrinfo['vote_delete_forever']);
$vote_delete_forever[$vote_id]=NULL;
$vote_delete_forever = serialize($vote_delete_forever);


$query = "update usrinfo set participant_vote_id = '".$participant_vote_id."',
					vote_notification = '".$vote_notification."',
					vote_delete_forever = '".$vote_delete_forever."'
					where usrname = '".$usrname."'";
*/
$query = "update usrinfo set participant_vote_id = '".$participant_vote_id."'
					where usrname = '".$usrname."'";
$ret2 = vote_db_query($query);

if($ret1 && $ret2)
	$del_vote['del_vote'] = 1;
else
	$del_vote['del_vote'] = 0;

$del_vote['vote_id'] = $vote_id;

echo json_encode($del_vote);

?>