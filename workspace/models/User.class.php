<?php namespace datamodel;


class User extends DataModel {    
    const tablename = "USER";
    
    # Account
    public $email;
    public $password;
    public $salt;
    public $creationdate;
    public $username;
    
    public $admin;
    public $accepted;
    
    # Personal
    public $firstname;
    public $lastname;
    public $prefix;
    public $born;
    public $adress;
    public $residence;
    public $phone;
    
    
    public $_sessions; 
    
    protected function validate() {
        if (!preg_match("/\d{10}/", $this->phone)) {
            throw new \Exception ("Telefoon nummer niet juiste formaat, gebruik 06xxxxxxxx");
        }
    }
    
    public function __construct($object, $from_db=false) {
        if (!$from_db) {
            $object['salt'] = $this->generateSalt();
            $object['password'] = $this->hashPassword($object['password']);
            $object['_sessions'] = array();
        } else {
            $object['_sessions'] = $this->getSessions();
            $object['admin'] = $object['admin'] > 0;
        }
        
        parent::__construct($object, $from_db);
    }
    
    private function getSessions() {
        return Session::getAll(["u_id" => $this->id]);
    }
    
    public static function createTable() {
        # Via INTEGER FOREIGN KEY(USER) REFERENCES USER(ID) NULL,
        self::db()->query(
            "CREATE TABLE IF NOT EXISTS USER (
                id INTEGER NOT NULL AUTO_INCREMENT,
                email VARCHAR(50) NOT NULL UNIQUE,
                password VARCHAR(64) NOT NULL,
                salt VARCHAR(40) NOT NULL,
                creationdate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                
                username VARCHAR(100) NOT NULL,
                
                firstname VARCHAR(50) NOT NULL,
                prefix VARCHAR(20) NULL,
                lastname VARCHAR(50) NOT NULL,
                born DATE NOT NULL,
                
                adress VARCHAR(100) NULL,
                residence VARCHAR(50) NULL,
                phone VARCHAR(12) NOT NULL,
                
                admin BOOLEAN DEFAULT FALSE,
                accepted INTEGER NULL,
                
                PRIMARY KEY (id),
                FOREIGN KEY (accepted) REFERENCES USER(id)
            );"
        );
    }
    
    public function save() {
        parent::save();
        
        foreach ($this->_sessions as $session) {
            $session->save();
        }
    }
    
    private function generateSalt() {
        return $this->salt = uniqid(mt_rand(), true);
    }
    
    private function hashPassword($password) {
        return hash("sha256", $this->salt . $password);
    }
    
    public function comparePassword($password) {
        return $this->hashPassword($password) === $this->password;
    }
    
    public function createSession($ip, $user_agent) {
        $session = new Session([
                "u_id" => $this->id,
                "ip" => $ip,
                "browser" => $user_agent
            ]);
        $session = $session->save();
        $this->_sessions[] = &$session;
        return $session->token;
    }
}