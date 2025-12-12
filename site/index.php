<?php

// ALWAYS start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load config
require __DIR__ . "/../config.php";
require __DIR__ . "/../ConnectPDO.php";
require __DIR__ . "/../bootstrap.php";

// Router defaults
$c = $_GET["c"] ?? "home";
$a = $_GET["a"] ?? "index";

// Build controller name and file
$controllerName = ucfirst($c) . "Controller";
$controllerFile = __DIR__ . "/Controller/" . $controllerName . ".php";

// Check controller file exists
if (!file_exists($controllerFile)) {
    die("Không tìm thấy controller: $controllerName");
}

// Load controller
require_once $controllerFile;

// Instantiate controller
$controller = new $controllerName();

// Run action
if (method_exists($controller, $a)) {
    $controller->$a();
} else {
    die("Action không tồn tại: $a trong controller $controllerName");
}
