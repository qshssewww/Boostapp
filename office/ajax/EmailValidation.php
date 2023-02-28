<?php

require_once '../../app/init.php';
require_once '../Classes/UserActivation.php';

header('Content-Type: application/json');

if (!empty($_POST["fun"])) {
    switch ($_POST["fun"]) {
        case "SendEmail":
            if (!isset($_POST["email"])) {
                echo json_encode(["Message" => "email required", "Status" => "Error"]);
            } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(["Message" => "email is not valid", "Status" => "Error"]);
            } elseif (!isset($_POST["userId"])) {
                echo json_encode(["Message" => "userId required", "Status" => "Error"]);
            } else {
                UserActivation::sendActivation($_POST["userId"], $_POST["email"]);
            }
            break;
        default: 
            echo json_encode(["Message" => "Function not found", "Status" => "Error"]);
            break;
    }
} else {
    echo json_encode(['Message' => "No Function", "Status" => "Error"]);
}