<?php namespace controller;

class LoginController extends MainController {
    public function __construct($template) {
        parent::__construct();
        
        $this->template = $template;
    }
    
    # Override
    public function onPost($postdata) {
        $mail = $postdata['email'];
        $password = $postdata['password'];
        
        $user = \datamodel\User::getAll(['email' => $mail]);
        
        if (!$user[0]->admin && empty($user[0]->accepted)) {
            $this->errors[] = "Account is nog niet geactiveerd. (Kan een dag duren).";
            return;
        }
        
        if (count($user) === 1 && $user[0]->comparePassword($password)) {
            $this->login($user[0]);
        } else {
            $this->errors[] = "Foute gebruikersnaam en/of wachtwoord.";
        }
    }
    
    public function render() {
        if ($_GET['registered']) {
            $this->errors[] = "Jouw account \"{$_GET['registered']}\" is aangemaakt. Zodra deze geaccepteerd is door een admin kun je inloggen.";
        }
        
        $this->isNotLogin();
        return parent::render();
    }
    
    protected function login($user) {
        $session_id = $user->createSession(isset($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
        
        $_SESSION["login"] = ['u' => $user->id, 't' => $session_id];
        
        $this->redirect();
    }
    
}