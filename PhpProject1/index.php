<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include_once('config.php');

$oHandler = new handler();

$oDatabase = new database();
$oHandler->db = $oDatabase;


$oAction = new Action($oHandler, 'home/blog/test');
$oAction->execute();