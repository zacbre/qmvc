<?php
class Router {
    private $routes_list;
    
    public function __construct() {
        $this->routes_list = array();
        register_shutdown_function(array($this, "ErrorHandler"));
    }
    
    public function ErrorHandler() {
        $error = error_get_last();
        if($error !== null) {
            ?>
            <html>
            <body>
            <pre><?php print_r($error); ?></pre>
            </body>
            </html>
            <?php
        }
    }
    
    public function Connect($source, $dest) {
        $this->routes_list[$source] = $dest;
    }
    
    public function GetRoute($source) {
        if(strlen($source) > 1) {
            $source = rtrim($source, "/");
        }
        //first, grab all regex entries from routes list
        foreach($this->routes_list as $key => $value) {
            if(array_key_exists("regex", $value)) {
                //this is a regex, compare first?
                $matches = array();
                preg_match($key, $source, $matches);
                
                if(count($matches) > 0 && $matches[0] == $source && !array_key_exists($source, $this->routes_list)) {
                    //this is our route.
                    return $this->routes_list[$key];
                }
            }
        }
        if(!array_key_exists($source, $this->routes_list)) return null;      
        return $this->routes_list[$source];
    }
    
    public function GetRouteByProperty($property_name, $property_value) {
        foreach($this->routes_list as $route) {
            //search for property in array ayyy lmao
            if(array_key_exists($property_name, $route)) {
                //does it equal?
                if($route[$property_name] != $property_value) continue;
                //return it ayy lmao           
                return $route;
            }
        }
        return null;
    }
       
    public function DoRoute($source) {
        $route_piece = "";
        $route = null;
        $pieces = array();
        $source .= ($source[strlen($source) - 1] == "/" ? "" : "/");
        
        if($source !== "/") {
            $tmp = strlen($source) - 1;
            while(strrpos($source, "/", $tmp) !== FALSE)
            {
                $route_piece = rtrim(substr($source, 0, $tmp), "/");
                if (strlen($route_piece) == 0) break;
                $route = $this->GetRoute($route_piece);   
                if (!is_null($route)) break;
                $tmp = strrpos($route_piece, "/");
            }   
        }
        else {
            //our route is probably /? let's just keep it as /
            $route = $this->GetRoute("/");
        }
        
        if (is_null($route)) {
            //ay we don't have a valid route. return our 404 route instead
            //look for our 404 route
            $error_route = $this->GetRouteByProperty("error_page", "404");
            if(!is_null($error_route)) {
                $route = $error_route;
            }
            else {
                exit("404 page controller not found");
            }
        }

        $args_piece = ltrim(substr($source, strlen($route_piece)), "/");
        
        $action = (array_key_exists("action", $route) ? $route["action"] : "index");
        $args = array();

        $search = "/^(.+?)\/(.*)$/i";
        $matches = array();
        if (preg_match($search, $args_piece, $matches))
        {
            $action = $matches[1];
            $args = explode("/", rtrim($matches[2], "/"));
        }
        $args = array_filter($args, create_function('$a', 'return $a !== "";'));
        $preclasses = get_declared_classes();
        if(!LoadFile(array("controller", $route["controller"].".controller.php"))) {
            throw new Exception(sprintf("Controller not found: %s/%s", "controller", $route["controller"]));
        }     
        $postclasses = get_declared_classes();
        
        $class = array_diff($postclasses, $preclasses);
        $class = array_values($class);
        
        $instance = new $class[0]($args);
        
        $call_action = str_replace("-", "_", $action);
        
        if (!is_callable(array($instance, $call_action)))
        {
            array_unshift($args, $action);
            $args = array_filter($args, create_function('$a', 'return $a !== "";'));
            
            $call_action = (array_key_exists("action", $route) ? $route["action"] : "index");
            
            $instance->setArgs($args);
        }

        if(is_callable(array($instance, "__onload"))) {
            $instance->{"__onload"}();
        }
        
        $instance->{$call_action}();
        
        //based on action - we need to call the template
        $instance->{"__render"}($class[0], $action, $route["controller"]);
    }
}