<?php
ini_set("max_execution_time", 0);

require_once '../app/init.php';
require_once 'services/EmailService.php';

$email = $_GET['email'];
EmailService::sendGetRegistrationSuccess($email);