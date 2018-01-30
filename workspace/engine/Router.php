<?php namespace engine;

class Router {
    private $root;
    public $links;
    
    public $post_data;
    public $full_link;
    public $page;
    
    public function __construct() {
        $this->root = new Path();
        $this->links = array();
    }
    
    private function createDatabase() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS USER (
                ID INTEGER NOT NULL AUTO_INCREMENT,
                Email VARCHAR(50) NOT NULL,
                Password VARCHAR(64) NOT NULL,
                Salt VARCHAR(20) NOT NULL,
                CreationDate TIMESTAMP NOT NULL,
                PRIMARY KEY (ID)
            );
        ");
        $this->db->query("
            CREATE TABLE IF NOT EXISTS ACCOUNT (
                U_ID INTEGER NOT NULL,
                Firstname VARCHAR(20) NOT NULL,
                LastnamePrefix VARCHAR(10) NULL,
                Lastname VARCHAR(30) NOT NULL,
                DateOfBirth TIMESTAMP NOT NULL,
                Via INTEGER NULL,
                Adres VARCHAR(50) NULL,
                Woonplaats VARCHAR(50) NULL,
                Provincie VARCHAR(50) NULL,
                Postcode VARCHAR(10) NULL,
                Info VARCHAR(100) NULL,
                PRIMARY KEY (U_ID),
                FOREIGN KEY(U_ID) REFERENCES USER(ID),
                FOREIGN KEY(Via) REFERENCES USER(ID)
            );
        ");
        $this->db->query("
            CREATE TABLE IF NOT EXISTS SESSION (
                U_ID INTEGER NOT NULL,
                TOKEN VARCHAR(16) NOT NULL,
                CreationDate TIMESTAMP NOT NULL,
                ExpireDate TIMESTAMP NOT NULL,
                IP INTEGER NULL,
                Browser VARCHAR(50) NOT NULL,
                PRIMARY KEY (U_ID, TOKEN),
                FOREIGN KEY(U_ID) REFERENCES USER(ID)
            );
        ");
        $this->db->query("
            CREATE TABLE IF NOT EXISTS SETTING (
                U_ID INTEGER NOT NULL,
                Type VARCHAR(20) NOT NULL,
                Value VARCHAR(20) NOT NULL,
                PRIMARY KEY (U_ID, Type),
                FOREIGN KEY(U_ID) REFERENCES USER(ID)
            );
        ");
    }
    
    public function addRoute($title, $path, $methods, $controller) {
        $path = $this->root->getPath($path, true);
        
        if ($path)
            $path->addMethod($methods, $controller);
            
        if (!$controller->title)
            $controller->title = ucfirst($title);
        $controller->router = $this;
        
        $this->links[$title] = $path->fullPath();
    }
    
    public function page() {
        try {
            $uri = $this->getUri();
            if (strpos($uri, "resources")) {
                if (file_exists($uri)) {
                    echo file_get_contents($uri);
                } else {
                    http_response_code(404);
                }
            } else {
                $path = $this->full_link = $this->root->getPath($uri);
                
                $controller = $path-> request($this->getMethod());
                    
                if (isset($_POST) && !empty($_POST)) {
                    $this->post_data = $_POST;
                    $controller->onPost($_POST);
                }
                    
                echo $controller->render();
            }
        } catch (\Exception $e) {
            echo "404 <br>".$e->getMessage();
        }
    }
    
    private function getUri() {
        $path = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($_SERVER['REQUEST_URI'], strlen($path));
        
        if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
        $uri = '/' . trim($uri, '/');
        
        return $uri;
    }
    
    private function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }
}