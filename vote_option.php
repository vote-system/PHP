<?php

require_once("db_fns.php");
require_once("vote_fns.php");
//require_once("usrinfo_fns.php");
//require_once("time.php");

header('Content-Type: application/json');

$usrname = $_POST['usrname'];
$vote_id = $_POST['vote_id'];
$notification = $_POST['notification'];
$delete_forever = $_POST['vote_id'];


?>