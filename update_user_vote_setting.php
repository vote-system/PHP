<?php

require_once("db_fns.php");
require_once("vote_fns.php");
//require_once("usrinfo_fns.php");
//require_once("time.php");

header('Content-Type: application/json');

$usrname = $_POST['usrname'];
$vote_id = $_POST['vote_id'];
$vote_notification = $_POST['notification'];
$vote_delete_forever = $_POST['delete_forever'];

$query = "select * from usrinfo where usrname = '".$usrname."'";
$usrinfo = vote_get_array($query);
$vote_notification = unserialize($usrinfo['vote_notification']);
$vote_delete_forever = unserialize($usrinfo['vote_delete_forever']);

$vote_notification[$vote_id] = $vote_notification;
$vote_delete_forever[$vote_id] = $vote_delete_forever;

$vote_notification = serialize($vote_notification);
$vote_delete_forever = serialize($vote_delete_forever);

$query = "update usrinfo
		set vote_notification = '".$vote_notification."',
		vote_delete_forever = '".$vote_delete_forever."'
		where usrname = '".$usrname."'";
$ret = vote_db_query($query);


//return $ret;


?>