<?php namespace datamodel;


class Session extends DataModel {    
    const tablename = "SESSION";
    
    public $u_id;
    public $_user;
    public $token;
    public $creationdate;
    public $expiredate;
    public $ip;
    public $browser;
    
    protected function validate() {
        return true;
    }
    
    public function __construct($object, $from_db=false) {
        if (!$from_db) {
            $object['token'] = $this->generateToken();
            $object['expiredate'] = $this->calculateExpire();
            $object['ip'] = ip2long($object['ip']);
        }
        
        $object["_user"] = User::getByID($object["u_id"]);
        
        parent::__construct($object, $from_db);
    }
    
    public static function createTable() {
        self::db()->query(
            "CREATE TABLE IF NOT EXISTS SESSION (
                id INTEGER NOT NULL AUTO_INCREMENT,
                u_id INTEGER NOT NULL,
                token VARCHAR(16) NOT NULL,
                creationdate TIMESTAMP NOT NULL,
                expiredate TIMESTAMP NOT NULL,
                ip INTEGER NULL,
                browser VARCHAR(255) NOT NULL,
                PRIMARY KEY (id),
                FOREIGN KEY(U_ID) REFERENCES USER(ID)
            );"
        );
    }
    
    private function generateToken() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 16; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $this->token = $randomString;
    }
    
    private function calculateExpire() {
        return $this->expiredate = date("Y-m-d H:i:s", strtotime("+8 hours"));
    }
    
    public function isAlive($ip, $user_agent) {
        return ((!strpos(strtolower($this->browser), "mobile") && ip2long($ip) === $this->ip) || 
        strpos(strtolower($this->browser, "mobile")) && $user_agent === $this->browser && 
        ($this->expiredate <= (new DateTime())) && $this->calculateExpire());
    }
}