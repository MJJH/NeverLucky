<?php namespace engine\controller;

abstract class TwigController implements IController {
    public $title;
    
    public $stylesheets;
    public $javascripts;
    
    protected $resource_folder;
    
    protected $template;
    protected $renderAttributes;
    
    protected $twig;
    public $router;
    
    public function __construct($template_folder, $resource_folder, $twig_attributes=[]) {
        $templates = new \Twig_Loader_Filesystem($resource_folder."/".$template_folder);
        $this->twig = new \Twig_Environment($templates, $twig_attributes);
        $this->resource_folder = $resource_folder;
        
        $this->stylesheets = array();
        $this->javascripts = array();
        $this->renderAttributes = array();
    }
    
    protected function addStyles($paths) {
        if (is_array($paths)) {
            foreach ($paths as $path) {
                $this->stylesheets[] = $this->url_for($path);
            }   
        } else {
            $this->stylesheets[] = $this->url_for($paths);
        }
    }
    
    protected function addScripts($paths) {
        if (is_array($paths)) {
            foreach ($paths as $path) {
                $this->javascripts[] = $this->url_for($path);
            }   
        } else {
            $this->javascripts[] = $this->url_for($paths);
        }
    }
    
    public function url_for($path) {
        $host = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1) || 
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ? 'https' : 'http').
        "://".$_SERVER['SERVER_NAME'];
        
        if ($this->router && array_key_exists($path, $this->router->links)) {
            return $host.$this->router->links[$path];
        }
        
        return $host."/".$this->resource_folder."/".$path;
    }
    
    public function render() {
        return $this->twig->render($this->template, array_merge([
                "controller" => $this
            ], $this->renderAttributes));
    }
}