<?php
require_once __DIR__.'/../../../app/init.php';
require_once __DIR__ . '/../../../app/controllers/PhoneValidationController.php';

$controller = PhoneValidationController::getInstance();
$controller->runAction();


