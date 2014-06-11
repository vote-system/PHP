<?php

$usrname = $_POST['usrname'];

header('Content-Type: application/json');

//first, return the vote info where user is organizer
//seconde, return the vote info where user is participants

$query = "select * from vote_info where organizer = '".$usrname."'";
$vote_infos = vote_get_assoc($query);

foreach ($vote_infos as $vote_info)
{
	$vote_resp[] = $vote_info;
}






?>