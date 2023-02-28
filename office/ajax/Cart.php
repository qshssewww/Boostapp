<?php

require_once __DIR__ . '/../../app/controllers/CartController.php';

$controller = CartController::getInstance();
$controller->runAction();