<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//print_r($_POST['values']);exit;
error_reporting(0);
include_once './db.php';
$xml = simplexml_load_string($_POST['values'], "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json, TRUE);
if (isset($array['body']) && $array['body'] != '') {
    global $dbhost;
    global $dbuser;
    global $dbpassword;
    $to = $array['@attributes']['to'];
    $toArray = explode('@', $to);
    $database = strtoupper($toArray[0]);
    $adminDb = new ezSQL_mysql($dbuser, $dbpassword, $database, $dbhost);
    $data = json_decode($array['body'], TRUE);
    if (!is_null($data) && $data['email'] !== '' && $data['latitude'] !== '' && $data['longitude'] !== '' && $data['locationtime'] !== '') {
        try {
            $qry = "INSERT into `user_tracking_info` (`email`,`latitude`, `longitude`, `locationtime`, `timestamp`) 
		VALUES ('" . $data['email'] . "','" . $data['latitude'] . "','" . $data['longitude'] . "','" . $data['locationtime'] . "','" . date('Y-m-d H:i:s') . "')";
            $adminDb->query($qry);
            $result['status'] = TRUE;
            $result['message'] = 'Location sucessfully inserted';
            echo json_encode($result);
            exit();
        } catch (Exception $ex) {
            $result['status'] = FALSE;
            $result['message'] = $ex->getMessage();
            echo json_encode($result);
            exit;
        }
    } else {
        $result['status'] = FALSE;
        $result['message'] = 'data is not valid';
        echo json_encode($result);
        exit;
    }
}



print_r($array);
