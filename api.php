<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
error_reporting(0);
include_once './db.php';
if ($_POST['op'] == 'getUserDetails' && isset($_POST['secretCode'])) {
    if($_POST['deviceToken'] == '' || $_POST['deviceToken'] == NULL){
        $_POST['deviceToken'] = '';
    }
    if($_POST['deviceType'] == '' || $_POST['deviceType'] == NULL){
        $_POST['deviceType'] = '';
    }
    
    getUserData($_POST['secretCode'],$_POST['deviceToken'],$_POST['deviceType']);
} else {
    $result['status'] = FALSE;
    $result['message'] = 'secret code not found';
    echo json_encode($result);
    exit();
}

function getUserData($secreatCode,$token,$device) {
    if (isset($secreatCode)) {
        global $dbhost;
        global $dbuser;
        global $dbpassword;
        global $db;
        $deviceId = isset($_POST['deviceId']) ? $_POST['deviceId'] : '';
        $code = explode('_', $secreatCode);
        $userdb = new ezSQL_mysql($dbuser, $dbpassword, $code[0], $dbhost);
        $query = "SELECT * FROM `user_info` WHERE `uniqueCode`= '" . $secreatCode . "'";
        $result = $userdb->get_results($query, ARRAY_A);
        if (!empty($result)) {
            if ($result[0]['deviceId'] == '' && $deviceId !== '') {
                $updateDeviceIdQuery = "UPDATE `user_info` SET `deviceId`= '" . $deviceId . "',`token`= '" . $token . "',`devicetype`= '" . $device . "' WHERE `uniqueCode`= '" . $secreatCode . "'";
                $userdb->query($updateDeviceIdQuery);
                $query1 = "SELECT `contactno` FROM `admin_database_info` WHERE `admindatabase`= '$code[0]" . "'";
                $result1 = $db->get_results($query1, ARRAY_A);
                unset($result[0]['password']);
                unset($result[0]['deviceId']);
                $data = $result[0];
                $data['admin_contactNo'] = $result1[0]['contactno'];
                $data['admin_jid'] = $code[0];
                $final['data'] = $data;
                $final['status'] = TRUE;
                echo json_encode($final);
                exit;
            } elseif ($result[0]['deviceId'] !== '' && $result[0]['deviceId'] == $deviceId) {
                $query1 = "SELECT `contactno` FROM `admin_database_info` WHERE `admindatabase`= '$code[0]" . "'";
                $result1 = $db->get_results($query1, ARRAY_A);
                unset($result[0]['password']);
                unset($result[0]['deviceId']);
                $data = $result[0];
                $data['admin_contactNo'] = $result1[0]['contactno'];
                $data['admin_jid'] = $code[0];
                $final['data'] = $data;
                $final['status'] = TRUE;
                echo json_encode($final);
                exit;
            } elseif ($result[0]['deviceId'] !== '' && $result[0]['deviceId'] !== $deviceId) {
                $data['status'] = FALSE;
                $data['message'] = 'This account is already registered with another device';
                echo json_encode($data);
                exit();
            } else {
                $data['status'] = FALSE;
                $data['message'] = 'device id not found';
                echo json_encode($data);
                exit;
            }
        } else {
            $data['status'] = FALSE;
            $data['message'] = 'Secret code is invalid!';
            echo json_encode($data);
            exit;
        }
    }
}
