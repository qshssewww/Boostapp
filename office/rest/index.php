<?php

    require_once realpath(__DIR__.'/../../app/').DIRECTORY_SEPARATOR.'initcron.php';

    
    include('REST.php');

    $rest = new REST;    

    $_GET = array_merge($_GET, $_POST);
    $type = empty($_GET['type'])?false:$_GET['type'];
    $method = empty($_GET['method'])?false:$_GET['method'];

    switch($type){
        case 'report':
            switch($method){
                case "quarter": include('quarter.php'); break;
                case "registeredVisitors": include('registeredVisitors.php');  break;
                case "paymentRefuse": include('paymentRefuse.php'); break;
                case "pipeline": include('pipeline.php'); break; // לידים
                case "tasks": include('tasks.php');break;
                case "avgIncome": include('avgIncome.php') ;break;
                case "occupancy": include('occupancy.php'); break; 
                case "leftStudio": include('leftStudio.php'); break;
                case "joinStudio": include('joinStudio.php'); break;
                case "newEntrence": include('newEntrence.php'); break;
                case "register": include('register.php'); break;
                case "bday": include('bday.php'); break;
                case "attendance": include('attendance.php'); break;
                case "nonattendance": include('NonAttendance.php'); break;
                case "nonregister": include('NonRegister.php'); break;
                case "classregister": include('classRegister.php'); break;
                case "sales": include('sales.php'); break;
                case "clients": include('clients.php'); break;
                case "receipts": include('receipts.php'); break;
                case "branches": include('branches.php'); break;
                case "classes": include('classes.php'); break;
                case "coaches": include('coaches.php'); break;
                case "guideNames": include('coaches.php'); break;
                case "sections": include('sections.php'); break;
                case "pipelineSources": include('pipelineSources.php'); break;
                case "agents": include('agents.php'); break;
                case "push": include('push.php'); break;
                case "overbooked": include('overBooked.php'); break;
                case "classNoAperance": include('classNoAperance.php'); break; // אי הגעות
                case "firstTime": include('firstTime.php'); break; // כניסות חדשות לסטדיו
                case "users": include('users.php'); break; // כניסות חדשות לסטדיו
                case "clock": include('clock.php'); break; // שעון נוכחות לפי חודש שנה ומספר עובד
                case "paytoken": include('paytoken.php'); break; // הוראות קבע
                case "frozen": include('frozenMembers.php'); break; // הוראות קבע
                

                
                
                default:
                $rest->answer->err = true;
                $rest->answer->message = 'סוג דוח לא ידוע';
                $rest->answer->code = 401;
            }
        break;
        case "medicalindicators":
            switch($method){
                case "dic": include('medicalindicators/dic.php'); break;
                case "clients": include('medicalindicators/clients.php'); break;
                case "bmr": include('medicalindicators/bmr.php'); break;
                case "bmi": include('medicalindicators/bmi.php'); break;
                case "weight": include('medicalindicators/weight.php'); break;
                case "scopes": include('medicalindicators/scopes.php'); break;
                case "bodyFat": include('medicalindicators/bodyFat.php'); break;
                case "muscleMass": include('medicalindicators/muscleMass.php'); break; // medical_muscle_mass
                default:
                $rest->answer->err = true;
                $rest->answer->message = 'סוג מדדים לא ידוע';
                $rest->answer->code = 401;
            }
        break;
        case "exrecise":
            switch($method){
                case "schema": include('exrecise/schema.php'); break;
                case "clients": include('exrecise/clients.php'); break;
                case "workout": include('exrecise/workout.php'); break;
            }
        break;
        default:
           $rest->answer->err = true;
           $rest->answer->message = 'סוג תוכן לא ידוע';
           $rest->answer->code = 401;
    }