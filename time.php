<?php
require_once("db_fns.php");
date_default_timezone_set('PRC');

function get_current_timestamp()
{
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

function update_usrinfo_timestamp($usrname,$type)
{
	$query = "select * from usrinfo where usrname='".$usrname."'";
    $usrinfo = vote_get_array($query);

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

function update_vote_info_timestamp($vote_id)
{
	$timestamp = get_current_timestamp();
	$query = "update vote_info
		set basic_timestamp = '".$timestamp."'
		where vote_id='".$vote_id."'";
	$ret = vote_db_query($query);

	return $ret;
}

function update_vote_timestamp($vote_id)
{
	$timestamp = get_current_timestamp();
	$query = "update vote_info
		set vote_timestamp = '".$timestamp."'
		where vote_id='".$vote_id."'";
	$ret = vote_db_query($query);

	return $ret;
}

?>