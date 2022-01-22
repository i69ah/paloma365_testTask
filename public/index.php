<?php

use App\Repositories\Db;
use App\Views\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../App/autoload.php';
//var_dump(1);
//die;
$requestedUri = $_SERVER['REQUEST_URI'];
$requestedUriArr = explode('/', $requestedUri);

unset($requestedUriArr[0]);

$class = '\App\\Controllers' . ucfirst($requestedUriArr[1]) . 'Controller';
$action = $requestedUriArr[2];
if (false !== strpos($requestedUriArr[2], '?')) {
    $action = substr($requestedUriArr[2], 0, strpos($requestedUriArr[2], '?'));
}
if (!file_exists(__DIR__ . $class . '.php')) {
    $class = '\App\\Controllers\\IndexController';
}
if (null == $action) {
    $action = 'index';
}
//echo '<pre>';
//var_dump($requestedUriArr, $class, $action, $_POST);die;
$twig = new Environment(new FilesystemLoader(__DIR__ . '/templates'));

$controller = new $class;
$controller->$action($twig, new Db());
