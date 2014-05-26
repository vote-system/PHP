<?php
require_once("vote_fns.php");

function get_current_timestamp()
{
	date_default_timezone_set('PRC');
	$date = new DateTime();
	$timestamp = $date->getTimestamp();
	if($timestamp)
		return $timestamp;
}

function get_time_stamp($usrname)
{
	$query = "select * from usrinfo where usrname='".$usrname."'";
    $timestamp = vote_get_array($query);
    return $timestamp;
}

function update_time_stamp($usrname,$type)
{
	$query = "select * from usrinfo where usrname='".$usrname."'";
    $usrinfo = vote_get_array($query);
	/*
	if($type == USR_INFO_TIME_STAMP)
	{
		$usr_info_timestamp = $usrinfo["usr_info_timestamp"];
		$usr_info_timestamp++;
		$query = "update usrinfo
			set usr_info_timestamp = '".$usr_info_timestamp."'
			where usrname = '".$usrname."'";
		$ret = vote_db_query($query);
		return $ret;
	}
	else if($type == HEAD_IMAG_TIME_STAMP)
	{
		$head_imag_timestamp = $usrinfo["head_imag_timestamp"];
		$head_imag_timestamp++;
		$query = "update usrinfo
			set head_imag_timestamp = '".$head_imag_timestamp."'
			where usrname = '".$usrname."'";
		$ret = vote_db_query($query);
		return $ret;
	}
	*/
	$timestamp = get_current_timestamp();
	if($type == USR_INFO_TIME_STAMP)
	{
		$query = "update usrinfo
			set usr_info_timestamp = '".$timestamp."'
			where usrname = '".$usrname."'";
		$ret = vote_db_query($query);
	}
	else if($type == HEAD_IMAG_TIME_STAMP)
	{
		$query = "update usrinfo
			set head_imag_timestamp = '".$timestamp."'
			where usrname = '".$usrname."'";
		$ret = vote_db_query($query);
	}
	return $ret;
}

?>