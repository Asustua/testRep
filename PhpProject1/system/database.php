<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of database
 *
 * @author patryk
 */
class database {
    
    private $oDatabase;
    
    public function __construct() {
        $this->oDatabase = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if(mysqli_connect_errno()) {
            echo 'Failed to connect to MySQL server: '. mysqli_connect_error();
        }
    }
}
