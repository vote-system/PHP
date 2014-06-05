<?php

require_once('db_fns.php');
require_once('vote_fns.php');

$usrname=$_POST['usrname'];

//set user status to active
$usr_active = USER_NOT_ACTIVE;
$query = "update usrinfo
			set active = '".$usr_active."'
			where usrname = '".$usrname."'";	
$ret = vote_db_query($query);	


?>