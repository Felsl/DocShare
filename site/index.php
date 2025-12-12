<?php
require "../config.php";
require "../connectPDO.php";
//model
$bootstrap = __DIR__ . '/../bootstrap.php';
if (!file_exists($bootstrap)) {
    die("Bootstrap file not found: $bootstrap");
}
require_once $bootstrap;
// Dev: bật hiển thị lỗi tạm thời
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bắt đầu session trước khi có output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// base directory của file này (site/)
$baseDir = __DIR__;

// include config / connect PDO bằng đường dẫn tuyệt đối
$configFile = $baseDir . '/../Config.php';
$connectFile = $baseDir . '/../ConnectPDO.php';
$requireFile = $baseDir . '/../require.php';

if (!file_exists($configFile) || !file_exists($connectFile) || !file_exists($requireFile)) {
    // lỗi rõ ràng nếu file thiếu
    http_response_code(500);
    die('Missing required core files. Check paths: config.php, connectPDO.php, require.php');
}

require $configFile;
require $connectFile;
require $requireFile;

// default controller/action
$c = $_GET['c'] ?? 'home';
$a = $_GET['a'] ?? 'index';
$controllerName = ucfirst($c) . 'Controller';

// require controller file (từ thư mục site/controller)
$controllerFile = $baseDir . '/controller/' . $controllerName . '.php';
if (!file_exists($controllerFile)) {
    http_response_code(404);
    die("Controller not found: $controllerName");
}
require $controllerFile;

// tạo instance controller và gọi action nếu tồn tại
$controller = new $controllerName();

if (method_exists($controller, $a)) {
    $controller->$a();
} else {
    http_response_code(404);
    die("Action không tồn tại: $a");
}
