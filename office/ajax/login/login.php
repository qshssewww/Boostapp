<?php
require_once __DIR__.'/../../../app/init.php';
require_once __DIR__ . '/../../../app/controllers/LoginController.php';

$controller = LoginController::getInstance();
$controller->runAction();


