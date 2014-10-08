<?php

require_once("db_fns.php");
require_once("vote_fns.php");
//require_once("usrinfo_fns.php");
//require_once("time.php");

header('Content-Type: application/json');

$usrname = $_POST['usrname'];
$vote_id = $_POST['vote_id'];

$query = "select * from vote_info where vote_id = '".$vote_id."'";
$vote_info = vote_get_array($query);
$participants_number = $vote_info['participants_number'];
++$participants_number;

$query = "update vote_info set participants_number = '".$participants_number."'
							where vote_id = '".$vote_id."'";
$ret = vote_db_query($query);

if($ret)
	$add_support['add_support'] = 1;
else
	$add_support['add_support'] = 0;

echo json_encode($add_support);

?>