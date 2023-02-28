<?php
require_once __DIR__.'/../app/init.php';
require_once __DIR__ . '/../app/router/Router.php';

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
} else {
    $inputJson = file_get_contents('php://input');
    $requestData = json_decode($inputJson, true);
    if ($requestData && isset($requestData['action'])) {
        $action = $requestData['action'];
    } else {
        throw new InvalidArgumentException('Page not found.', 404);
    }
}

$request = (new Router())->handleRequest();

/** @var BaseController $controller */
$controller = $request['controller'];
$controller->runAction($request['action']);
