<?php

class IndexController extends Controller {
    
    /* Runs right when controller is loaded */
    public function __onload() {
        $this->uses('auth');
        $this->uses('ajaxer');
        $this->uses('form');
    }
    
    /* Runs right before page is rendered */
    public function __onready() {
        
    }
    
    public function index() {
        $this->set('title', 'QMVC');
        
        //check for our ajax form test POST.
        if($this->request->is_post()) {
            if($this->request->data['secretcode'] != 'test') {
                exit(json_encode(array("error" => true, "error_msg" => "Incorrect code.")));
            } else {
                exit(json_encode(array("error" => false, "msg" => 'You guessed the code correctly!')));    
            }
        }
        
        //save a new admin user.
        if(!$this->auth->register(array(
            'email' => 'admin',
            'password' => 'test',
            'firstname' => 'Admin',
            'lastname' => 'Test',
        ))) {
            $this->set('admin', 'The test user was not created because it already exists.');
        } else {
            $this->set('admin', 'The test user did not exist and was created.');
        }
        
        //lookup the admin user.
        $admin = $this->users->find('all', array('conditions' => array(
            'email' => 'admin'
        )));
        
        $this->set('admin_user', $admin);
        
        $this->append("head", $this->ajaxer->setup(array(
            "url" => $this->getURL(), 
            "form" => "ajaxform",
            "redirect" => false,
            "required" => array("secretcode"),
            "failed" => "field.attr('style', 'border: 1px solid red;')",
            "success" => "alert alert-success",
            "error" => "alert alert-danger",
        )));
    }
}