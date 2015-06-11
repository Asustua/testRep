<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of model
 *
 * @author patryk
 */
class model {
    private $oDatabase;
    private $oRegistry;
    
    public function __construct($oRegistry) {
        $this->oRegistry = $oRegistry;
        $this->oDatabase = $this->oRegistry->oDb;
    }
}
