<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of blog
 *
 * @author patryk
 */
class ControllerHomeBlog extends controller {
    
    public function index() {
        echo 'a';
    }
    
    public function test() {
        $this->load('home/blog', 'model');
        
        $this->model_home_blog->test();
    }
}
