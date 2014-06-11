<?php
require_once("vote_fns.php");
require_once("usrinfo_fns.php");

//push_message("dingyi","dingyi",ADD_FRIEND_REQUEST,"3f77d9491bdf75000d0d1b88cfa1f4f337f38979a6c566b32d2f7a1867fba4f4","",0);

function push_notification($from,$to,$action)
{
	// Put your device token here (without spaces):   
	//$token = '3f77d9491bdf75000d0d1b88cfa1f4f337f38979a6c566b32d2f7a1867fba4f4';  
	//echo "token = " . $token . "\n";  

	//1.search the device token of $to
	$token = search_token_from_db($to);
	if($token == null)
		return false;

	//private key's passphrase for this APP(vote)   
	$passphrase = '890iopkl;';  
	  
	// Put your alert message here: 
	//$user_group = array($from,$to);
	switch ($action)
	{
		case ADD_FRIEND_REQUEST:
		$push_message = 'ADD_FRIEND_REQUEST';
		break;

		case AGREE_ADD_FRIEND:
		$push_message = 'AGREE_ADD_FRIEND';
		break;

		//case REFUSE_ADD_FRIEND:
		//$push_message = 'REFUSE_ADD_FRIEND';
		//break;

		case VOTE_NOTIFICATION:
		$push_message = 'VOTE_NOTIFICATION';
		break;
		
		default:
			//echo "push request content not support!\n";
			return false;
		break;
	}
	//print_r($push_message);
	//$usrname = array($from);
	$usrinfo = query_usr_info($from);
	$screen_name = $usrinfo['screen_name'];

	$screen_name = array($screen_name);
	$message = array(
		"loc-key" => $push_message,
		"loc-args" => $screen_name
	);
  
	$ctx = stream_context_create();  
	stream_context_set_option($ctx, 'ssl', 'local_cert', 'PushVoteCK.pem');  
	stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);  
  
	// Open a connection to the APNS server   
	$fp = stream_socket_client(  
		'ssl://gateway.sandbox.push.apple.com:2195', $err,  
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);  
  
	if (!$fp){ 
		//exit("Failed to connect: $err $errstr" . PHP_EOL); 
		return false;
	}
	//echo 'Connected to APNS' . PHP_EOL;  

	$total_badge = get_user_badge($to);
	  
	// Create the payload body   
	$body['aps'] = array(  
		'alert' => $message,        //push message   
		'sound' => 'default',      //default sound   
		'badge' => $total_badge   //total badge of the usr
		);  
	$body['append_message'] = array(  
		'action_code' => $action,//push message   
		//'append_message' => $append_message //user append message  
		);    
	// Encode the payload as JSON   
	//print_r($body);
	$payload = json_encode($body);  
	  
	// Build the binary notification   
	$msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;  
	  
	// Send it to the server   
	$result = fwrite($fp, $msg, strlen($msg));  
	/*  
	if (!$result)  
		echo 'Message not delivered' . PHP_EOL;  
	else  
		echo 'Message successfully delivered' . PHP_EOL;  
	*/  
	// Close the connection to the server   
	fclose($fp);  
	return true;
}
?>
