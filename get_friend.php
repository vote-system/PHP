<?php
require_once("usrinfo_fns.php");
require_once("db_fns.php");
require_once("vote_fns.php");

$usrname=$_GET['usrname'];
header('Content-Type: application/json; charset=utf-8');

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

	  $friend_info['usrname'] = $usrinfo['usrname'];
	  $friend_info['signature'] = $usrinfo['signature'];
	  $friend_info['screen_name'] = $usrinfo['screen_name'];
	  $friend_info['screen_name_pinyin'] = $usrinfo['screen_name_pinyin'];
	  $friend_info['gender'] = $usrinfo['gender'];
	  $friend_info['original_head_imag_url'] = $usrinfo['original_head_imag_url'];
	  $friend_info['medium_head_imag_url'] = $usrinfo['medium_head_imag_url'];
	  $friend_info['thumbnails_head_imag_url'] = $usrinfo['thumbnails_head_imag_url'];
	  $friend_info['usr_info_timestamp'] = (int)$usrinfo['usr_info_timestamp'];
	  $friend_info['head_imag_timestamp'] = (int)$usrinfo['head_imag_timestamp'];

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
	echo json_encode($friends_array,JSON_UNESCAPED_SLASHES);
	//print_r($friends_array);
	//print_r(json_encode($friends_array));
	//echo json_encode($friends_array);
	//$json = json_encode($friends_array);
	//echo stripslashes($json);
}

?>