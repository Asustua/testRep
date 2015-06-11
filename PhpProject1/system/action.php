<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of action
 *
 * @author patryk
 */
class action {
    
    protected $sFolder = '';
    protected $sController = '';
    protected $sAction = '';
    protected $sClass = '';
    protected $aParams = array();
    protected $oClass = '';
    protected $iRoutes = 0;
    
    private $oRegistry = array();
    
    public function __construct($oRegistry, $sRoute) {
        $this->oRegistry = $oRegistry;
        $aRoute = explode('/', $sRoute);
        $this->iRoutes = count($aRoute);
        $iRoute_start = 3;
        if(!empty($aRoute[0])) {
            $this->sFolder = $aRoute[0];
            if(!$this->checkFolder()) {
                echo 'Error, no folder';
            }
        }
        
        if(!empty($aRoute[1])) {
            $this->sController = $aRoute[1];
            if($this->checkFile()) {
                require_once CONTROLLER.$this->sFolder.'/'.$this->sController.SYS_EXTENSION;
                $this->sClass = 'Controller'.ucfirst($this->sFolder).ucfirst($this->sController);
                $this->oClass = new $this->sClass($this->oRegistry);
            } else {
                echo 'Error, no file';
            }
        }
        
        if(!empty($aRoute[2])) {
            if(method_exists($this->oClass, $aRoute[2]) && is_callable(array($this->oClass, $aRoute[2]))) {
                $this->sAction = $aRoute[2];
            } else {
                $this->sAction = 'index';
                $iRoute_start--;
            }
        } else {
            $this->sAction = 'index';
        }
        
        if($this->iRoutes > $iRoute_start) {
            for($i = $iRoute_start; $i < $this->iRoutes; $i++) {
                $this->aParams[] = $aRoute[$i];
            }
        }
    }
    
    public function execute() {
        $this->oClass->{$this->sAction}();
    }
    
    private function checkFolder() {
        if(is_dir(CONTROLLER.$this->sFolder)) {
            return true;
        }
        return false;
    }
    
    private function checkFile() {
        if(is_file(CONTROLLER.$this->sFolder.'/'.$this->sController.SYS_EXTENSION)) {
            return true;
        }
        return false;
    }
}
