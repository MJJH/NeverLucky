<?php namespace controller;

class IndexController extends MainController {
    public function __construct($template) {
        parent::__construct();
        
        $this->template = $template;
    }
    
    public function render() {
        $this->isLogin();
        return parent::render();
    }
}