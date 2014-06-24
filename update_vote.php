<?php

require_once("db_fns.php");
require_once("time.php");
require_once("vote_fns.php");

header('Content-Type: application/json');

$vote_id = $_POST['vote_id'];
$usrname = $_POST['usrname'];
$title = $_POST['title'];
$end_time = $_POST['end_time'];
$category = $_POST['category'];
$participants = $_POST['participants'];
$options = $_POST['options'];
$private = $_POST['private'];

if($title){
	$query = "update vote_info
				set title = '".$title."'
				where organizer = '".$usrname."' and vote_id = '".$vote_id."'";
	$ret = vote_db_query($query);	
	
	if($ret){
		$update_vote['update_title'] = UPDATE_SUCCESS; 
	}else{
		$update_vote['update_title'] = UPDATE_FAIL; 
	}

}

if($end_time){
	$query = "update vote_info
				set end_time = '".$end_time."'
				where organizer = '".$usrname."' and vote_id = '".$vote_id."'";	
	$ret = vote_db_query($query);	
	
	if($ret){
		$update_vote['update_end_time'] = UPDATE_SUCCESS; 
	}else{
		$update_vote['update_end_time'] = UPDATE_FAIL; 
	}
}

if($category){
	$query = "update vote_info
				set category = '".$category."'
				where organizer = '".$usrname."' and vote_id = '".$vote_id."'";	
	$ret = vote_db_query($query);	
	
	if($ret){
		$update_vote['update_category'] = UPDATE_SUCCESS; 
	}else{
		$update_vote['update_category'] = UPDATE_FAIL; 
	}
}

if($participants){
	$query = "update vote_info
				set participants = '".$participants."'
				where organizer = '".$usrname."' and vote_id = '".$vote_id."'";	
	$ret = vote_db_query($query);	
	
	if($ret){
		$update_vote['update_participants'] = UPDATE_SUCCESS; 
	}else{
		$update_vote['update_participants'] = UPDATE_FAIL; 
	}
}

if($options){
	$query = "update vote_info
				set options = '".$options."'
				where organizer = '".$usrname."' and vote_id = '".$vote_id."'";	
	$ret = vote_db_query($query);	
	
	if($ret){
		$update_vote['update_options'] = UPDATE_SUCCESS; 
	}else{
		$update_vote['update_options'] = UPDATE_FAIL; 
	}
}

if($private){
	$query = "update vote_info
				set private = '".$private."'
				where organizer = '".$usrname."' and vote_id = '".$vote_id."'";	
	$ret = vote_db_query($query);	
	
	if($ret){
		$update_vote['update_options'] = UPDATE_SUCCESS; 
	}else{
		$update_vote['update_options'] = UPDATE_FAIL; 
	}
}

update_vote_info_timestamp($organizer,$vote_id);

echo json_encode($update_vote);

?>