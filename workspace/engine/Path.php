<?php namespace engine;

class Path {
    private $parent;
    private $children;
    
    private $url;
    private $methods;
    
    public function __construct($url="root", $parent=null) {
        $this->methods = array();
        $this->children = array();
        
        $this->parent = $parent;
        $this->url = $url;
    }  
    
    public function fullPath() {
        if ($this->parent)
            return $this->parent->fullPath()."/".$this->url;
        return "";
    }
    
    public function getPath($path, $create=false) {
        $parts = explode("/", $path);
        $child = $parts[1];
        
        if (!array_key_exists($child, $this->children)) {
            if ($create && $this->is_valid_path($child)) {
                $this->children[$child] = new Path($child, $this);
            } else {
                throw new \Exception("Path \"{$child}\" could not be found for \"{$this->fullPath()}\"");
            }
        }
        
        $subs = array_slice($parts, 2);
        
        if (count($subs) > 0)
            return $this->children[$child]->getPath("/".implode("/", $subs), $create);
        return $this->children[$child];
    }
    
    public function addMethod($method, $callback) {
        if (is_array($method)) {
            foreach ($method as $m) {
                $this->addMethod($m, $callback);
            }
            return;
        }
        
        $method = strtoupper($method);
        
        if (!$this->is_valid_method($method)) 
            throw new \Exception("Method \"{$method}\" not a valid method");
            
        if (array_key_exists($method, $this->methods))
            throw new \Exception("Method \"{$method}\" already defined for \"{$this->fullPath()}\"");
            
        $this->methods[$method] = $callback;
    }
    
    public function request($method) {
        $method = strtoupper($method);
        
        if (!$this->is_valid_method($method)) 
            throw new \Exception("Method \"{$method}\" not a valid method");
            
        if (!array_key_exists($method, $this->methods))
            throw new \Exception("Method \"{$method}\" could not be found for \"{$this->fullPath()}\"");
            
        return $this->methods[$method];
    }
    
    private function is_valid_method($method) {
        return (is_string($method) && in_array(strtoupper($method), [
                "GET", "HEAD", "POST", "PUT",
                "DELETE", "CONNECT", "OPTIONS",
                "TRACE", "PATCH"
            ]));
    }
    
    private function is_valid_path($path) {
        return true;
    }
}