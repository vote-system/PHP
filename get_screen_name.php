<?php
require_once("vote_fns.php");
require_once("db_fns.php");

$usrname = $_GET['usrname'];

header('Content-Type: application/json');

$query = "select * from usrinfo where usrname='".$usrname."'";
$row = vote_get_array($query);
$usrinfo['screen_name'] = $row['screen_name'];

echo json_encode($usrinfo);

?>