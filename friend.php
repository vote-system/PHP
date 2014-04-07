<?php
require_once("friend_fns.php");

$usrname=$_POST['usrname'];
$action=$_POST['friend_action'];
$friend_name=$_POST['friend_name'];
$message=$_POST['add_friend_message'];

header('Content-Type: application/json');

switch($action)
{
	case ADD_FRIEND_REQUEST:
	$result=add_friend_request($usrname,$friend_name,$message);
	if(!$result)
		$friend['add_friend_request'] = ;
	else
		$friend['add_friend_request'] = ;
	echo json_encode($friend);
	break;

	case DELETE_FRIEND_REQUEST:
	$result=del_friend_request($usrname,$friend_name);
	if(!$result)
		$friend['delete_friend_request'] = ;
	else
		$friend['delete_friend_request'] = ;
	echo json_encode($friend);
	break;

	case ADD_FRIEND_RESPONSE:
	$result=add_friend_response($usrname,$friend_name,$action);
	if(!$result)
		$friend['add_friend_response'] = ;
	else
		$friend['add_friend_response'] = ;
	echo json_encode($friend);
	break;

	case GET_FRIEND_LIST:
	$result=get_friend_list($usrname);
	if(!$result)
	{
		$friend['get_friend_list'] = ;
		echo json_encode($friend);
	}
	break;

	//case DELETE_FRIEND_RESPONSE:
	//$result=delete_friend_response($usrname,$friend_name);
	//break;

	default:
		echo "action not supported in friend.php\n";
	break;
}

?>