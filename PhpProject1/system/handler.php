<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of handler
 *
 * @author patryk
 */
class handler {
    protected $aData = array();
    
    public function __get($sName) {
        if(!empty($this->aData[$sName]))    return $this->aData[$sName];
        return '';
    }
    
    public function __set($sName, $mValue) {
        $this->aData[$sName] = $mValue;
    }
}
