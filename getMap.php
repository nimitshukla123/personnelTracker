<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'admin-class.php';
$admin = new itg_admin();
return $admin->getTrackedData($_POST);
exit(0);