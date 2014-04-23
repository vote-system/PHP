<?php
require_once("vote_fns.php");
$usrname = $_GET['usrname'];

//$friend_badge = query_badge("friend_badge",$usrname);
//$vote_badge = query_badge("vote_badge",$usrname);
$badge_arr = query_badge($usrname);

header('Content-Type: application/json');
$badge['friend_badge'] = $badge_arr['friend_badge']; 
$badge['usr_vote_badge'] = $badge_arr['usr_vote_badge']; 
echo json_encode($badge);

?>