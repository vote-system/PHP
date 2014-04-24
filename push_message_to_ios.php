<?php
require_once("vote_fns.php");

//push_message("zhaobo","dingyi",1,"3f77d9491bdf75000d0d1b88cfa1f4f337f38979a6c566b32d2f7a1867fba4f4","",0);

function push_message($from,$to,$action,$token,$append_message,$badge)
{
// Put your device token here (without spaces):   
//$token = '3f77d9491bdf75000d0d1b88cfa1f4f337f38979a6c566b32d2f7a1867fba4f4';  
//echo "token = " . $token . "\n";  
  
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

	case REFUSE_ADD_FRIEND:
	$push_message = 'REFUSE_ADD_FRIEND';
	break;
	
	default:
		echo "push request content not support!\n";
	break;
}
$usrname = array($from);

$usr = array($from);
$message = array(
	"loc-key" => $push_message,
	"loc-args" => $usr
);
  
$ctx = stream_context_create();  
stream_context_set_option($ctx, 'ssl', 'local_cert', 'PushVoteCK.pem');  
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);  
  
// Open a connection to the APNS server   
$fp = stream_socket_client(  
    'ssl://gateway.sandbox.push.apple.com:2195', $err,  
    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);  
  
if (!$fp)  
    exit("Failed to connect: $err $errstr" . PHP_EOL);  
  
//echo 'Connected to APNS' . PHP_EOL;  

$total_badge = get_user_badge($to);
  
// Create the payload body   
$body['aps'] = array(  
    'alert' => $message,        //push message   
    'sound' => 'default',      //default sound   
	'badge' => $total_badge   //total badge of the usr
    );  
$body['append_message'] = array(  
    'friend_code' => $action,//push message   
    'append_message' => $append_message //user append message  
    );    
// Encode the payload as JSON   
$payload = json_encode($body);  
  
// Build the binary notification   
$msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;  
  
// Send it to the server   
$result = fwrite($fp, $msg, strlen($msg));  
  
if (!$result)  
    echo 'Message not delivered' . PHP_EOL;  
else  
    echo 'Message successfully delivered' . PHP_EOL;  
  
// Close the connection to the server   
fclose($fp);  

}
?>