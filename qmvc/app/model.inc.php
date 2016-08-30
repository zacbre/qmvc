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

class Database {
    private $conn;
    
    public function __construct() {
        $this->conn = mysqli_connect(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);
    
        if(!$this->conn) {
            die;
        }
    }
    
    public function instance() {
        return $this->conn;
    }
}

class Model {
    private $db;
    private $dbinst;
    //model loader
    public function __construct() {
        $args = func_get_args();
        if(count($args) > 0) {
            $key = "name";
            $this->$key = func_get_arg(0);
        }
        
        //
        $this->db = $GLOBALS['__mydb'];
        $this->dbinst = $this->db->instance();
    }
    
    public function __getmodelname() {
        return $this->name;
    }

    /**********************
    * User Callable Functions
    **********************/

    public function toobject($msql) {
        $arr = array();
        foreach ($msql as $entry) {
            $object = new stdClass();
            foreach($entry as $key => $value) {
                $object->$key = $value;
            }
            array_push($arr, $object);
        }
        return $arr;
    }
    
    public function beforeFind($array) {
        //maybe permission-based role check here
        return $array;
    }
    
    public function afterFind($array) {
        //to cleanup data
        return $array;
    }
    
    public function beforeSave($array) {
        //pre-save logic such as modifying dates in a specific format etc
        return $array;
    }
    
    public function afterSave($created, $array) {
        //any data that has been saved
        
    }
    
    public function find($type = 'all', $array = array()) {
        
        if(!($array = $this->beforeFind($array))) return false;
        
        $conditions = "";
        $conditionstring = "";
        $conditioncount = 0;
        $conditionparam = array();
        if(array_key_exists('conditions', $array)) {
            $conditions = " WHERE ";
            $i = 0;
            $conditioncount = count($array['conditions']);
            foreach($array['conditions'] as $name => $val) {
                $conditions .= sprintf("`%s` = ?", $name);
                $conditionstring .= "s";
                if($i++ != ($conditioncount - 1)) {
                    $conditions .= " AND ";
                }
            }
            
            $conditionparam[] = &$conditionstring;
            
            $conds = array_values($array['conditions']);
            for($i = 0; $i < count($conds); $i++) {
                $conditionparam[] = &$conds[$i];
            }
        }
        
        $fields = "";
        if(array_key_exists('fields', $array)) {
            $fields = implode(',', $array['fields']);
        }
        
        $options = "";
        if(array_key_exists('options', $array)) {
            foreach($array['options'] as $key => $value) {
                $options .= sprintf("%s %s", strtoupper($key), $value);
            }
        }
        
        switch($type) {
            case 'list': 
                $type = $fields;
                break;
            case 'count':
                $type = 'COUNT(*)';
                break;
            case 'all':
            default: 
                $type = '*';
                break;
        }
        
        $query = sprintf("SELECT %s FROM `%s`%s %s", $type, $this->__getmodelname(), $conditions, $options);

        $stmt = $this->dbinst->prepare($query);
        
        if(!$stmt) {
            return false;
        }
        
        if(count($conditionparam) > 0)
            call_user_func_array(array($stmt, 'bind_param'), $conditionparam);
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $stmt->close();
        
        if($result->num_rows <= 0) return false;
        
        $result = $result->fetch_all(MYSQLI_ASSOC);

        if(!($result = $this->afterFind($result))) return false;
        
        return $result;
    }
    
    public function save($array) {
        
        if(!array_key_exists('values', $array)) return false;
        
        if(!($array = $this->beforeSave($array))) return false;
        
        $conditionstring = "";
        $conditionparam = array();
        
        $updatestr = " (";
        $valstr = "";
        $update = "";
        
        $count = count($array['values']);
        $i = 0;
        
        foreach($array['values'] as $key => $value) {
            $update .= sprintf("`%s` = ?", $key);
            $valstr .= "?";
            $conditionstring .= "s";
            $updatestr .= $key;
            if($i++ != ($count - 1)) {
                $update .= ", ";
                $updatestr .= ",";
                $valstr .= ",";
            } else {
                $updatestr .= ")";
            }
        }
        
        if(array_key_exists('conditions', $array) && count($this->find('all', $array['conditions'])) > 0) {
            $conditions = " WHERE ";
            $conditioncount = 0;
            $i = 0;
            $conditioncount = count($array['conditions']);
            
            foreach($array['conditions'] as $name => $val) {
                $conditions .= sprintf("`%s` = ?", $name);
                $conditionstring .= "s";
                if($i++ != ($conditioncount - 1)) {
                    $conditions .= " AND ";
                }
            }
            
            $conditionparam[] = &$conditionstring;
            
            $vals = array_values($array['values']);
            for($i = 0; $i < count($vals); $i++) {
                $conditionparam[] = &$vals[$i];
            }
            
            $cond = array_values($array['conditions']);
            for($i = 0; $i < count($cond); $i++) {
                $conditionparam[] = &$cond[$i];
            }
            
            //update
            $query = sprintf("UPDATE `%s` SET %s%s", $this->__getmodelname(), $update, $conditions);    
            
            $stmt = $this->dbinst->prepare($query);
            
            var_dump($conditionparam);
            
            if(!$stmt) {
                return false;
            }
            
            call_user_func_array(array($stmt, 'bind_param'), $conditionparam);
                
            $stmt->execute();
            
            $stmt->close();
            
            $this->afterSave($array);
            
        } else {
            //insert
            
            $conditionparam[] = &$conditionstring;
            
            $vals = array_values($array['values']);
            for($i = 0; $i < count($vals); $i++) {
                $conditionparam[] = &$vals[$i];
            }

            $query = sprintf("INSERT INTO `%s`%s VALUES(%s)", $this->__getmodelname(), $updatestr, $valstr);
            
            $stmt = $this->dbinst->prepare($query);
            
            if(!$stmt) {
                return false;
            }
            
            call_user_func_array(array($stmt, 'bind_param'), $conditionparam);
                
            $stmt->execute();
            
            $stmt->close();
            
            $this->afterSave($array, $array['values']);
        }
        
        return true;
    }
    
    public function delete($array = array()) {
        
        if(!array_key_exists('conditions', $array) || count($array['conditions']) <= 0) return false;
        
        $conditions = "";
        $conditionstring = "";
        $conditioncount = 0;
        $conditionparam = array();
        
        $conditions = " WHERE ";
        $i = 0;
        $conditioncount = count($array['conditions']);
        foreach($array['conditions'] as $name => $val) {
            $conditions .= sprintf("`%s` = ?", $name);
            $conditionstring .= "s";
            if($i++ != ($conditioncount - 1)) {
                $conditions .= " AND ";
            }
        }
        
        $conditionparam[] = &$conditionstring;
        
        $conds = array_values($array['conditions']);
        for($i = 0; $i < count($conds); $i++) {
            $conditionparam[] = &$conds[$i];
        }
        
        $sql = sprintf("DELETE FROM `%s` %s");
        
        $stmt = $this->dbinst->prepare($sql);
        
        if(!$stmt) return false;
        
        //bind param here.
        call_user_func_array(array($stmt, 'bind_param'), $conditionparam);
        
        $stmt->execute();
        
        $stmt->close();
        
        return true;
    }
    
    public function query($sql, $array = array()) {
        //get count of arguments
        $stmt = $this->dbinst->prepare($sql);
        
        if(count($array) > 0) {
            $ars = array();
            $str = str_repeat("s", count($array));
            $ars[] = &$str;
            for($i = 0; $i < count($array); $i++) {
                $ars[] = &$array[$i];
            }
            //bind param here.
            call_user_func_array(array($stmt, 'bind_param'), $ars);
        }
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $stmt->close();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function rawquery($sql) {
        $result = $this->dbinst->query($sql);
        if(!$result || $result->num_rows <= 0) return array();
        return $this->__toobject($result);
    }
    
    public function geterror() {
        return $this->dbinst->error;
    }
}

$GLOBALS['__mydb'] = new Database();