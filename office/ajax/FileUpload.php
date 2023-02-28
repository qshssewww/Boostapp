<?php

require_once __DIR__ . '/../../app/controllers/FileUploadController.php';

$controller = FileUploadController::getInstance();
$controller->runAction();