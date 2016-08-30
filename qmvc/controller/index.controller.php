<?php

class IndexController extends Controller {
    
    /* Runs right when controller is loaded */
    public function __onload() {
    }
    
    /* Runs right before page is rendered */
    public function __onready() {
        
    }
    
    public function index() {
        $this->set('title', 'QMVC');
    }
}