<?php 

use engine\Router;

require_once "engine/include.php";
require_once "vendor/autoload.php";
require_once "engine/Medoo.php";

$router = new Router();

require_once "engine/lessphp.inc.php";
\engine\lessphp\compileFolder("resources/css/less", "resources/css");

require_once "resources/controllers/MainController.php";
require_once "models/DataModel.inc.php";


// Pages
session_start();
# Off
$router->addRoute("login", "/account/login", ["GET", "POST"], new controller\LoginController("login.html"));
$router->addRoute("logoff", "/account/logoff", "GET", new controller\LogoffController);
$router->addRoute("register", "/account/register", ["GET", "POST"], new controller\RegisterController("register.html"));

# On
$router->addRoute("index", "/", "GET", new controller\IndexController("index.html"));

$router->page();