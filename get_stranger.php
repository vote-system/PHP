<?php
require_once('db_fns.php');
require_once('vote_fns.php');
require_once('usrinfo_fns.php');

$usrname = $_GET['usrname'];
header('Content-Type: application/json');

if($usrname)
{
	//1.clear the friend_badge in usrinfo
	$query = "update usrinfo set friend_badge = 0
							where usrname = '".$usrname."'";
	//echo $query;
	vote_db_query($query);

	//2.return the stranger info to usr
    ret_stranger_info($usrname);
}

function ret_stranger_info($usrname)
{
	$usrid = usrname_to_usrid($usrname);
	//$stranger_list = array();
	$query = "select * from stranger where usrid = '".$usrid."'";
	$stranger_list = vote_get_assoc($query); 
	//print_r($stranger_list);
    $i = 0;
	foreach ($stranger_list as $stranger)
	{
		$stranger_id = $stranger['stranger_id'];
		//print_r($stranger_id);
		$query = "select * from usrinfo where usrid = '".$stranger_id."'";
		$usrinfo = vote_get_array($query);

		$stranger_info['usrname'] = $usrinfo['usrname'];
		$stranger_info['signature'] = $usrinfo['signature'];
		$stranger_info['screen_name'] = $usrinfo['screen_name'];
		$stranger_info['screen_name_pinyin'] = $usrinfo['screen_name_pinyin'];
		$stranger_info['gender'] = $usrinfo['gender'];
		$stranger_info['original_head_imag_url'] = $usrinfo['original_head_imag_url'];
		$stranger_info['medium_head_imag_url'] = $usrinfo['medium_head_imag_url'];
		$stranger_info['thumbnails_head_imag_url'] = $usrinfo['thumbnails_head_imag_url'];
		$stranger_info['usr_info_timestamp'] = (int)$usrinfo['usr_info_timestamp'];
		$stranger_info['head_imag_timestamp'] = (int)$usrinfo['head_imag_timestamp'];

		$stranger_detail[$i] = $stranger_info;
		//print_r($stranger_info);
		//$json = json_encode($stranger_array);
		//echo stripslashes($json);
		//echo json_encode($stranger_array); 
		//print_r($stranger_detail[$i]);
		$i++;
	}

	$strangers_array["strangers_array"] = $stranger_detail;
	//print_r($strangers_array["strangers_array"]);
	echo json_encode($strangers_array,JSON_UNESCAPED_SLASHES);
	//$json = json_encode($strangers_array);
	//echo stripslashes($json);
}

?>