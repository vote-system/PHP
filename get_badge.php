<?php
require_once("usrinfo_fns.php");

$usrname = $_GET['usrname'];
//$usrname = "dingyi";
$badge_arr = query_badge($usrname);
header('Content-Type: application/json');
$badge['friend_badge'] = (int)$badge_arr['friend_badge']; 
$badge['usr_vote_badge'] = (int)$badge_arr['usr_vote_badge']; 

$query = "update usrinfo set friend_badge = 0, usr_vote_badge = 0
							where usrname = '".$usrname."'";
vote_db_query($query);
//return $result;

echo json_encode($badge);
?>
