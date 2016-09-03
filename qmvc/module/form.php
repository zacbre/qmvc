<?php
/*
    qmvc - A small but powerful MVC framework written in PHP.
    Copyright (C) 2016 ThrDev
    
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class Formify extends Module {
    private $form;
    private $prerender = true;
    
    public function create($values) {
        /*
        id, class, method = post, prerender = true, action = current url
        */
        $this->form = "";
        $this->prerender = true;
        
        if(array_key_exists('prerender', $values))
            $this->prerender = $values['prerender'];
            
        $id = "";
        if(array_key_exists('id', $values))
            $id = $values['id'];
        $class = "";
        if(array_key_exists('class', $values))
            $class = $values['class'];
        $method = "POST";
        if(array_key_exists('method', $values)) 
            $method = $values['method'];
        $action = "";
        if(array_key_exists('action', $values)) 
            $action = $values['action'];
            
        $form = sprintf('<form id="%s" class="%s" method="%s" action="%s">', $id, $class, $method, $action);
        
        if($this->prerender) {
            echo $form;    
        } else {
            $this->form .= $form;
        }
    }
    
    public function input($values) {
        /*
        id; class; placeholder; type = text/email/password/tel,checkbox,radio,file,number
        coming soon: type = range,date,color,reset,month,time,url,week,
        */
        if(!array_key_exists('type', $values))
            throw new Exception('No type specified for form field.');
        if(!array_key_exists('name', $values))
            throw new Exception('No name specified for form field.');
            
        $id = "";
        $class = "";
        if(array_key_exists('prerender', $values))
            $prerender = $values['prerender'];
        if(array_key_exists('id', $values))
            $id = $values['id'];
        if(array_key_exists('class', $values))
            $class = $values['class'];
        $value = "";
        if(array_key_exists('value', $values)) 
            $value = $values['value'];
        $placeholder = "";
        if(array_key_exists('placeholder', $values)) 
            $placeholder = $values['placeholder'];
        $name = "";
        if(array_key_exists('name', $values)) 
            $name = $values['name'];
            
        $attrs = "";
        if(array_key_exists('attr', $values)) {
            foreach($values['attr'] as $key => $val) {
                $attrs .= sprintf(' "%s"="%s"', $key, $val);
            }
        }
        $forminput = sprintf('<input type="%s" id="%s" class="%s" value="%s" placeholder="%s" name="%s"%s>', $values['type'], $id, $class, $value, $placeholder, $name, $attrs);
        if($this->prerender) {
            echo $forminput;
        } else {
            $this->form .= $forminput;
        }
    }
    
    public function submit($values) {
        /* 
        id, class, text,
        */
        $id = "";
        if(array_key_exists('id', $values))
            $id = $values['id'];
        $class = "";
        if(array_key_exists('class', $values))
            $class = $values['class'];
        $text = "Submit";
        if(array_key_exists('text', $values))
            $text = $values['text'];
            
        $formsubmit = sprintf('<input type="submit" id="%s" class="%s" value="%s">', $id, $class, $text);
        if($this->prerender) {
            echo $formsubmit;
        } else {
            $this->form .= $formsubmit;
        }
    }
    
    public function hidden($values) {
        
    }
    
    public function end() {
        $formend = "</form>";
        if($this->prerender) {
            echo $formend;
        } else {
            $this->form .= $formend;
        }
    }
    
    private function render() {
        $this->end();
        
        echo $this->form;
    }
}