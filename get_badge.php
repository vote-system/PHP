<?php
require_once("usrinfo_fns.php");

$usrname = $_GET['usrname'];
//$usrname = "dingyi";
$badge_arr = query_badge($usrname);
header('Content-Type: application/json');
$badge['friend_badge'] = (int)$badge_arr['friend_badge']; 
$badge['usr_vote_badge'] = (int)$badge_arr['usr_vote_badge']; 
echo json_encode($badge);
?>
