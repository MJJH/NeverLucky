<?php namespace controller;

class LogoffController extends MainController {
    public function __construct() {
        parent::__construct();
    }
    
    public function render() {
        $this->isLogin();
        $this->logOff();
    }
    
    protected function logOff() {
        if (isset($_SESSION['login']) && !empty($_SESSION['login'])) {
            $session = $_SESSION['login'];
            
            $user = $session['u'];
            $token = $session['t'];
            
            $login = \datamodel\Session::getAll(["u_id" => intval($user), "token" => $token]);
            if ($login) {
                $login[0]->expiredate = date("Y-m-d H:i:s");
                $login[0]->save();
            }
        } 
        
        unset($_SESSION['login']);
        $this->redirect();
    }
}