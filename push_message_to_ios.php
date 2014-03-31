<?php
 
// Put your device token here (without spaces):
$deviceToken = '3f77d9491bdf75000d0d1b88cfa1f4f337f38979a6c566b32d2f7a1867fba4f4';
 
// Put your private key's passphrase here:����
$passphrase = 'lucent company';
 
// Put your alert message here:
$message = 'this is a push message';
 
$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
 
// Open a connection to the APNS server
$fp = stream_socket_client(
         'ssl://gateway.sandbox.push.apple.com:2195', $err,
         $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
 
if (!$fp)
         exit("Failed to connect: $err $errstr" . PHP_EOL);
 
echo 'Connected to APNS' . PHP_EOL;
 
// Create the payload body
$body['aps'] = array(
         'alert' => $message,//������Ϣ
         'sound' => 'default'//����Ĭ������
         );
 
// Encode the payload as JSON
$payload = json_encode($body);
 
// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
 
// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));
 
if (!$result)
         echo 'Message not delivered' . PHP_EOL;
else
         echo 'Message successfully delivered' . PHP_EOL;
 
// Close the connection to the server
fclose($fp);
   
?>