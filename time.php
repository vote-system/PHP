<?php

function get_current_timestamp()
{
	date_default_timezone_set('PRC');
	$date = new DateTime();
	$timestamp = $date->getTimestamp();
	if($timestamp)
		return $timestamp;
}

?>