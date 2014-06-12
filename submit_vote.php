<?php

require_once("db_fns.php");
require_once("vote_fns.php");

header('Content-Type: application/json');


$usrname = $_POST['usrname'];
$vote_id = $_POST['vote_id'];
$option = $_POST['option'];

$query = "select * from vote_info where vote_id = '".$vote_id."'";
$vote_info = vote_get_array($query);
$vote_detail = unserialize($vote_info['vote_detail']);

foreach($option as $option_number){
	$vote_detail[$option-1] = $usrname;
}
$submit_vote['submit_vote'] = 1;
echo json_encode($submit_vote);

?>