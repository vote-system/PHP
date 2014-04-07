<?php
require_once("friend_fns.php");

$usrname=$_POST['usrname'];
$action=$_POST['friend_action'];
$friend_name=$_POST['friend_name'];
$message=$_POST['add_friend_message'];

switch($action)
{
	case ADD_FRIEND_REQUEST:
	$result=add_friend_request($usrname,$friend_name,$message);
	break;

	case DELETE_FRIEND_REQUEST:
	$result=del_friend_request($usrname,$friend_name);
	break;

	case ADD_FRIEND_RESPONSE:
	$result=add_friend_response($usrname,$friend_name,$action);
	break;

	case DELETE_FRIEND_RESPONSE:
	$result=delete_friend_response($usrname,$friend_name);
	break;
}

?>