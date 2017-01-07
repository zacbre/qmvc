<?php

class IndexController extends Controller {
    
    /* Runs right when controller is loaded */
    public function __onload() {
        $this->uses(array('auth', 'ajaxer', 'form'));
        $this->uses('script', array('url' => $this->request->config['url']));
    }
    
    /* Runs right before page is rendered */
    public function __onready() {
        
    }
    
    public function index() {
        $this->set('title', 'QMVC');
        
        ///
        $this->users->save(array('values' => array(
            'firstname' => 'test',
        ), 'conditions' => array(
            'email' => 'admin',
        )));
        ///
        
        //save a new admin user.
        if(!$this->auth->register(array(
            'email' => 'adminas',
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
        
        /*
        IN query
        $admin = $this->users->find('all', array('conditions' => array(
            'id IN' => array(
                '2',
            ),
        )));
        */
        
        $this->set('admin_user', $admin);
        
        $user = array(
            'email' => 'admin',
            'password' => 'test',
        );
        //attempt to verify the user.
        if($this->auth->attempt($user)) {
            //we've logged in.
            $this->set('loggedin', true);
            $this->set('login_attempt', $user);
        } else {
            $this->set('loggedin', false);
        }
        
        $this->append("head", $this->ajaxer->setup(array(
            "url" => $this->getURL('/submit'), 
            "form" => "ajaxform",
            "redirect" => false,
            "required" => array("secretcode"),
            "failed" => "field.attr('style', 'border: 1px solid red;')",
            "success" => "alert alert-success",
            "error" => "alert alert-danger",
        )));
    }
    
    public function submit() {
        //check for our ajax form test POST.
        if($this->request->is_post()) {
            if($this->request->data['secretcode'] != 'test') {
                exit(json_encode(array("error" => true, "error_msg" => "Incorrect code.")));
            } else {
                exit(json_encode(array("error" => false, "msg" => 'You guessed the code correctly!')));    
            }
        }
    }
}