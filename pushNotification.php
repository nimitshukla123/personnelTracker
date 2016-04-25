

<?php

include_once realpath(dirname(__FILE__) . '/admin-class.php');
$admin = new itg_admin();
$pushTosend = array();
$offlineUsers = $admin->getOfflineUsers();
$allUsers = $admin->getAllUsers();
$androidToken = array();

foreach ($allUsers as $alluser) {
    foreach ($offlineUsers as $offline) {
        if (in_array(strtoupper($offline['username']), $alluser)) {
            $pushTosend[] = $alluser;
        }
    }
}
foreach ($pushTosend as $value) {
    if ($value['is_on_track'] == 1) {

        if ($value['devicetype'] == 'ios') {
            iosPush($value['token']);
        }
        if ($value['devicetype'] == 'android') {
            $androidToken[] = $value['token'];
        }
    }
}
sendPushNotification($androidToken);

function iosPush($token) {
    try {
        $deviceToken = $token;
        $message = 'You are not connected to personnel Tracker! Connect with tracker to continue the service.';
        $body['aps'] = array(
            'alert' => $message,
            'badge' => 18
        );

        $body['category'] = 'message';
        $passphrase = 'personneltracker';
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'ckDistNew.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        $fp = stream_socket_client(
                'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

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
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function sendPushNotification($registration_id) {
    try {


        $message = 'You are not connected to personnel Tracker! Connect with tracker to continue the service.';
        $url = 'https://android.googleapis.com/gcm/send';
        $fields = array(
            'registration_ids' => $registration_id,
            'data' => array('message' => $message),
        );

        define('GOOGLE_API_KEY', 'AIzaSyBm0y_zo-4jFvDBlrrxaIRy7bcsZ1xQNnk');

        $headers = array(
            'Authorization:key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        if ($result === false)
            die('Curl failed ' . curl_error());

        curl_close($ch);
        return $result;
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$admin->setTrackingUpdate($allUsers);



