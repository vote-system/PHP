<?php
require_once("friend_fns.php");
require_once("vote_fns.php");

$usrname=$_POST['usrname'];
$action=$_POST['friend_action'];
$friend_name=$_POST['friend_name'];

//save the message to show when user get!
$message=$_POST['add_friend_message'];

header('Content-Type: application/json');
//print_r($action);
switch($action)
{
	case ADD_FRIEND_REQUEST:
	$result = handle_add_fri_req($usrname,$friend_name);
	
	if(!$result)
		$friend['add_friend_request'] = 0;
	else
		$friend['add_friend_request'] = 1;
	echo json_encode($friend);
	
	break;

	case DELETE_FRIEND_REQUEST:
	$result = handle_del_fri_req($usrname,$friend_name);
	
	if(!$result)
		$friend['delete_friend_request'] = 0;
	else
		$friend['delete_friend_request'] = 1;
	echo json_encode($friend);
	
	break;

	case AGREE_ADD_FRIEND:

	$result = handle_agree_add_fri($usrname,$friend_name);	
	
	if(!$result)
		$friend['add_friend_response'] = 0;
	else
		$friend['add_friend_response'] = 1;
	echo json_encode($friend);
	
	break;

	//case DELETE_FRIEND_RESPONSE:
	//$result=delete_friend_response($usrname,$friend_name);
	//break;

	default:
		echo "action not supported in friend.php\n";
	break;
}

?>