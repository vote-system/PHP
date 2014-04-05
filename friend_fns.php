<?php
require_once("vote_fns.php");

function add_friend_request($from,$to)
{
	//1.search the device token of $to
	$token = search_token_from_db($to);

	//2.push the request to the peer
	push_message_to_peer($to,$token,$from,ADD_FRIEND_REQUEST);
}

function add_friend_response($from,$to,$response)
{
	//1.search the device token of $to
	$token = search_token_from_db($to);

	//2.push the response to the peer
	push_message_to_peer($to,$token,$from,$response);

	//3.decide whether to write to database
	if($response == AGREE_ADD_FRIEND)
	{
		//write to database
	}
	else if($response == REFUSE_ADD_FRIEND)
	{
		//do nothing
	}

}

function del_friend_request($from,$to)
{
	//delete the item from database

	//return the $result to the peer
	//echo json();
}

function delete_friend_response($from,$to,$response)
{
	//do not know whether this function is useful or not!
}

function search_token_from_db($usrname)
{
	$conn = db_connect();
	if(!$conn){
		//$msg = "Function register,db connect error!";
		//$auth_log->general($msg);
		return DB_CONNECT_ERROR;
	}
	// check if username is unique
	$result = $conn->query("select * from user where usrname='".$usrname."'");
	if (!$result) {
		//$msg = "Function register,db query failed";
		//$auth_log->general($msg);
		return DB_QUERY_ERROR;
	}

	//$user_info = $result->fetch_assoc();
	$row = $result->fetch_array();
	return $row[4];
}
?>
