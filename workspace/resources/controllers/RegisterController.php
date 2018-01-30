<?php namespace controller;

class RegisterController extends MainController {
    public function __construct($template) {
        parent::__construct();
        
        $this->template = $template;
        
        $this->addScripts([
                "scripts/import/moment.js",
                "scripts/import/bootstrap-datepicker.min.js",
                "scripts/page_js/register.js"
            ]);
        $this->addStyles([
                "css/import/bootstrap-datepicker3.min.css"
            ]);
    }
    
    public function onPost($post_data) {
        try {
            if (empty($post_data['password']) || strlen($post_data['password']) < 8) {
                throw new \Exception("Probeer anders een veilig wachtwoord, minimaal 8 tekens");
            }
            
            $user = new \datamodel\User([
                        'email' => $post_data['email'],
                        'password' => $post_data['password'],
                        'username' => $post_data['username'],
                        'firstname' => $post_data['firstname'],
                        'lastname' => $post_data['lastname'],
                        'prefix' => $post_data['prefix'],
                        'born' => date("Y-m-d", strtotime($post_data['born'])),
                        'adress' => $post_data['adress'],
                        'residence' => $post_data['residence'],
                        'phone' => $post_data['phone']
                ]);
            $user->save();
            
            $this->redirect("login", ["registered" => $post_data['username']]);
            
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (strpos($msg, "Duplicate") !== false) {
                $msg = "Email adres al in gebruik; Neem contact op met een Beheerder";
            } elseif (strpos($msg, "NULL") !== false) {
                $msg = "Er is iets mis gegaan met registreren.";
            }
            
            $this->errors[] = $msg;
        }
    }
    
    public function render() {
        $this->isNotLogin();
        return parent::render();
    }
}