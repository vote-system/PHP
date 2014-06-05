<?php
require_once("usrinfo_fns.php");
require_once("db_fns.php");
require_once("vote_fns.php");

$usrname=$_GET['usrname'];
header('Content-Type: application/json');

if($usrname)
{
	$usrid = usrname_to_usrid($usrname);

	if(FRIEND_DEBUG){
	echo "usrid = " . $usrid;
	}

	$query = "select * from friend where usrid='".$usrid."'";
	$friend_list = vote_get_assoc($query);

	$i=0;
	foreach ($friend_list as $friend)
	{
		$friend_id = $friend['friend_id'];
		//print_r($stranger_id);
	  $query = "select * from usrinfo where usrid = '".$friend_id."'";
	  $usrinfo = vote_get_array($query);
	  //print_r($usrinfo);

	  $friend_info['usrname'] = stripslashes($usrinfo['usrname']);
	  $friend_info['signature'] = stripslashes($usrinfo['signature']);
	  $friend_info['screen_name'] = stripslashes($usrinfo['screen_name']);
	  $friend_info['gender'] = stripslashes($usrinfo['gender']);
	  $friend_info['original_head_imag_url'] = stripslashes($usrinfo['original_head_imag_url']);
	  $friend_info['medium_head_imag_url'] = stripslashes($usrinfo['medium_head_imag_url']);

	  //$stranger_array["strangers_array"] = $stranger;
	  //print_r($stranger_info);
	  //echo json_encode($friend_info); 

	  $friend_detail[$i] = $friend_info;
	  //print_r($stranger_info);
	  //echo json_encode($friend_array); 

	  //$json = json_encode($friend_array);
	  //echo stripslashes($json);
	  $i++;
	}
	$friends_array["friends_array"] = $friend_detail;
	$json = json_encode($friends_array);
	echo stripslashes($json);
}

?>