<?php
require_once __DIR__.'/../../app/init.php';
require_once __DIR__ . '/../../app/controllers/EmailController.php';

$controller = EmailController::getInstance();
$controller->runAction();

