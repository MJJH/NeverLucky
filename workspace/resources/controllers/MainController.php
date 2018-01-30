<?php namespace controller;

spl_autoload_register(function($controller) {
    $parts = explode('\\', $controller);
    if ($parts[0] === __NAMESPACE__) {
        require "resources/controllers/".end($parts).".php";
    }
});

abstract class MainController extends \engine\controller\TwigController {
    public $user;
    public $errors;
    
    public function __construct() {
        parent::__construct('templates', 'resources', ['debug' => true, 'auto_reload' => true]);
        
        $this->addScripts([
            "scripts/import/jquery-3.3.1.min.js",
            "scripts/import/bootstrap.min.js",
            "scripts/main.js"
        ]);
        $this->addStyles([
            "css/import/bootstrap.min.css",
            "css/import/fontawesome-all.min.css",
            "css/main.css",
        ]);
        
        $this->template = $template;
    }
    
    public function render() {
        return parent::render();
    }
    
    public function onPost($postdata) {
        return 0;
    }
    
    public function isLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect("login");
        }
    }
    
    public function isNotLogin() {
        if ($this->isLoggedIn()) {
            $this->redirect();
        }
    }

    public function isLoggedIn() {
        if (isset($_SESSION['login']) && !empty($_SESSION['login'])) {
            $session = $_SESSION['login'];
            
            $user = $session['u'];
            $token = $session['t'];
            
            if (\datamodel\Session::getAll(["u_id" => intval($user), "token" => $token])) {
                return $this->user = \datamodel\User::getByID(intval($user));
            }
        } 
        
        return false;
    }
    
    public function redirect($page="index", $params=null) {
        if (!array_key_exists($page, $this->router->links)) {
            $page = "index";
        }
        
        $pars = [];
            if ($params) {
            foreach ($params as $par => $val) {
                $pars[] = $par."=".$val;
            }
        }
        
        header('Location: ' . $this->url_for($page) . ($params ? "?" . join($pars, "&") : ""));
    }
}