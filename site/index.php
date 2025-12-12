<?php
require "../config.php";
require "../connectPDO.php";
//model
require "../require.php";
session_start();
//?c=auth&a=login
$c              = $_GET["c"] ?? "home";
$a              = $_GET["a"] ?? "index";
$controllerName = ucfirst($c) . "Controller";
//AuthController
require "controller/" . $controllerName . ".php";
//controller/AuthController.php
$controller = new $controllerName();
//new AuthController
if (method_exists($controller, $a)) {
    $controller->$a();
} else {
    die("Action không tồn tại: $a");
}