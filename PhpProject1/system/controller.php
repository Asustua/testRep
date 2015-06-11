<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of controller
 *
 * @author patryk
 */
class controller {
    
    protected $aInfo = array();
    public $aData = array();
    private $oRegistry = array();
    
    public function __construct($oRegistry) {
        $this->oRegistry = $oRegistry;
    }
    
    public function __set($sName, $sValue) {
        $this->aInfo[$sName] = $sValue;
    }
    
    public function __get($sName) {
        if(!empty($this->aInfo[$sName]))    return $this->aInfo[$sName];
        return '';
    }
    
    public function load($sName,$sMethod = 'model', $sName2 = '') {
        if(empty($sName))   return '';
        
        switch($sMethod) {
            case 'model':
                $sMethod = MODEL;
            break;
            case 'lang':
                $sMethod = 'lang';
            break;
        }
        
        if(is_file($sMethod.$sName.SYS_EXTENSION)) {
            require_once $sMethod.$sName.SYS_EXTENSION;
            $aName = explode('/', $sName);
            $sClass = 'Model'.ucfirst($aName[0]).ucfirst($aName[1]);
            if(empty($sName2))  $sName2 = implode('_', $aName);
            $sName2 = 'model_'.$sName2;
            $this->{$sName2} = new $sClass($this->oRegistry);
        }
    }
}
