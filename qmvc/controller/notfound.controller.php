<?php

class NotFoundController extends Controller {
    
    public function __onload() {
        $this->set('title', "Page Not Found");

        $this->view("index");
    }
    
    public function index() {
        
    }
    
}