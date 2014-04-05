<?php
require_once("vote_fns.php");

function add_friend_request($from,$to)
{
	//1.search the device token of $to
	//2.push the request to the peer
	echo "function add_friend_request";
	$token = search_token_from_db($to);
	echo $token;
}

function add_friend_response($from,$to)
{
	//1.search the device token of $to
	//2.push the response to the peer

}

function del_friend_request($from,$to)
{
	//delete the item from database
	//return the $result to the peer
}

function delete_friend_response($from,$to)
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