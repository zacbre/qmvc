<?php

class IndexModel extends Model {
    
    /* Database table name */
    public $name = 'users';
    
    /* Runs before a find query */
    public function beforeFind($array) {
        return $array;
    }
    
    /* Runs after a find query */
    public function afterFind($array) {
        return $this->toobject($array);
    }
    
    /* Runs before a save query */
    public function beforeSave($array) {
        //make sure to bcrypt password?
        return $array;
    }
    
    /* Runs after a save query */
    public function afterSave($created, $array) {
        
    }
}