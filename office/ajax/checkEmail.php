<?php
require_once '../../app/init.php';
require_once '../Classes/Users.php';
require_once '../Classes/Functions.php';

$user = new Users();
$fun = new Functions();
$data = json_decode(file_get_contents('php://input'), true);
if (!isset($data["email"])) exit;
$exists = $user->isEmailExists($data["email"]);
$generated = $fun->generateRandomString();
echo json_encode(["Message" => $exists, "generate" => $generated]);
