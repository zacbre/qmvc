<?php

class TemplatingController extends Controller {
    public function __onload() {
        $this->uses('template');
        
        $this->template->create($this, array(
            'cache' => false,
        ));
    }

    public function index() {
        $this->set(array(
            'var1' => 'VAR 1 VALUE',
            'var2' => 'VAR 2 VALUE',
        ));
        $this->template->render();
    }
}