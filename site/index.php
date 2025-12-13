<?php


require "../config.php";
require "../connectPDO.php";
//model
$bootstrap = __DIR__ . '/../bootstrap.php';
if (!file_exists($bootstrap)) {
    die("Bootstrap file not found: $bootstrap");
}
require_once $bootstrap;
// site/index.php - Entry router (safe, dev mode)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load bootstrap (use require_once để tránh define trùng)
$bootstrap = __DIR__ . '/../bootstrap.php';
if (!file_exists($bootstrap)) {
    die("Bootstrap file not found: $bootstrap");
}
require_once $bootstrap;
session_start();


// ensure $c and $a are defined
$c = isset($_GET['c']) && $_GET['c'] !== '' ? $_GET['c'] : 'home';
$a = isset($_GET['a']) && $_GET['a'] !== '' ? $_GET['a'] : 'index';

// Build controller name & path
$controllerName = ucfirst($c) . 'Controller';
$controllerFile = __DIR__ . '/Controller/' . $controllerName . '.php';


// Require controller file
if (!file_exists($controllerFile)) {
    http_response_code(404);
    die("Controller not found: $controllerName");
}
require_once $controllerFile;

// Check class exists
if (!class_exists($controllerName)) {
    http_response_code(500);
    die("Controller class $controllerName not found in file $controllerFile");
}

// Instantiate and call action
$controller = new $controllerName();

if (!method_exists($controller, $a)) {
    http_response_code(404);
    die("Action not found: $a in controller $controllerName");
}

// Instantiate controller
$controller = new $controllerName();

// Verify method exists (you already did this above — keep for safety)
if (!method_exists($controller, $a)) {
    http_response_code(404);
    die("Action not found: $a in controller $controllerName");
}

try {
    $ref = new ReflectionMethod($controller, $a);
} catch (ReflectionException $e) {
    http_response_code(500);
    die("Reflection error: " . $e->getMessage());
}

// Build argument list by matching method parameter names with $_GET
$paramsToPass = [];
foreach ($ref->getParameters() as $param) {
    $pname = $param->getName();
    if (array_key_exists($pname, $_GET)) {
        // simple sanitation: if looks numeric convert to int
        $val = $_GET[$pname];
        if (is_numeric($val) && ctype_digit((string) $val)) {
            $paramsToPass[] = (int) $val;
        } else {
            $paramsToPass[] = $val;
        }
    } elseif ($param->isDefaultValueAvailable()) {
        $paramsToPass[] = $param->getDefaultValue();
    } elseif ($param->allowsNull()) {
        $paramsToPass[] = null;
    } else {
        http_response_code(400);
        die("Missing required parameter: $pname");
    }
}

// Finally call the action with mapped parameters
call_user_func_array([$controller, $a], $paramsToPass);