<?php

$deviceToken = '1f648b0de77a89b325464ee20455070a1dcbd25207c41a360addb3f74b651010';
$message = 'iPad!';

    
$body['aps'] = array(
                     'alert' => $message,
                     'badge' => 18
                     );
    
$body['category'] = 'message';
//$body['category'] = 'profile';
//$body['category'] = 'dates';
//$body['category'] = 'daily_dates';

//$body['sender'] = 'jamesHAW';
$body['sender'] = 'jerrytest35';
    

//Server stuff
$passphrase = 'personneltracker';

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'ckDev.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

$fp = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo 'Message successfully delivered' . PHP_EOL;

fclose($fp);
    
?>
