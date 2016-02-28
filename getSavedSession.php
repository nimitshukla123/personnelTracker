<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'admin-class.php';
$admin = new itg_admin();

if($_POST['delete'] == 1){
   $admin->deleteSavedSession($_POST['sessionId']); 
}else{
$admin->getSavedSession($_POST['sessionId']);
}
