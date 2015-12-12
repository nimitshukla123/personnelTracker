<?php

/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */
error_reporting(0);


    $recieverMail = $_POST['reciever_email'];
    $recieverName = $_POST['reciever_name'];
    $subject = 'Hearing Test Results';
    $msg = $_POST['message'];
    if (!isset($recieverMail)) {
        $result['status'] = FALSE;
        $result['msg'] = 'Recievers Email not found';
        echo json_encode($result);
                exit(0);
    }
    if (filter_var($_POST['reciever_email'], FILTER_VALIDATE_EMAIL) === false) {
        $result['status'] = FALSE;
        $result['msg'] = 'Recievers Email is not correct';
        echo json_encode($result);exit(0);
    }
    date_default_timezone_set('Etc/UTC');

    require './PHPMailerAutoload.php';
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Debugoutput = 'html';
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->setFrom($senderEmail, $senderName);
    $mail->addReplyTo($senderEmail, $senderName);
    $mail->addAddress($recieverMail, $recieverName);
    $mail->Subject = $subject;
    $mail->msgHTML($msg);
    $mail->AltBody = 'This is a plain-text message body';
    if (!$mail->send()) {
        $result['status'] = FALSE;
        $result['msg'] = $mail->ErrorInfo;
        echo json_encode($result);exit(0);
    } else {
        $result['status'] = TRUE;
        $result['msg'] = 'Email has been successfully sent';
        echo json_encode($result);exit(0);
    }