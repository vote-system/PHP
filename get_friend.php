<?php
require_once("friend_fns.php");

$usrname=$_GET['usrname'];

if($usrname)
{
	handle_get_fri_list($usrname);
}


?>