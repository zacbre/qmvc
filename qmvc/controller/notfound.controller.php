<?php

class NotFoundController extends Controller {
    
    public function __onload() {
        $this->uses("auth");
        
        $this->set('title', "Page Not Found");

        $this->view("index");
    }
    
    public function index() {
        //$this->redirect("/404");
    }
    
    public function notfound() {
        /*if(ltrim($this->request->url, "/") != "404") {
            $this->redirect("/404");
        } */       
    }
}