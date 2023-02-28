<?php

require_once '../../app/init.php';
if (!Auth::check()) redirect_to('../../index.php'); // secure page
echo View::make('headernew')->render(); // header
echo '<link href="../assets/css/fixstyle.css" rel="stylesheet">';
$page = ( !empty($_REQUEST['page']) && file_exists("./".$_REQUEST['page'].'.html') )?$_REQUEST['page'].'.html':'404.html';
$pageName = explode(".", $page)[0];
switch($pageName){
    case "calc_bmi":
        $breadcrumb = 'מחשבון BMI'; 
    break;
    case "calc_bmr":
        $breadcrumb = 'מחשבון BMR (Basal Metabolic Rate)'; 
    break;
    case "bmr":
        $breadcrumb = 'BMR (Basal Metabolic Rate)'; 
    break;
    case "weight":
        $breadcrumb = 'משקל'; 
    break;
    case "indices_weight":
        $breadcrumb = 'מדדי משקל'; 
    break;
    case "indices_muscle_mass": $breadcrumb = 'מדדי מסת שריר';  break;
    case "muscle_mass": $breadcrumb = 'מסת שריר';  break;
    case "indices_scopes":
        $breadcrumb = 'מדדי היקפים'; 
    break;
    case "scopes":
        $breadcrumb = 'דוח היקפים'; 
    break;
    case "calc_bodyFat":
        $breadcrumb = 'אחוז שומן'; 
    break;  
        case "bodyFat":
        $breadcrumb = 'אחוז שומן'; 
    break;      

    
    default:
        $breadcrumb = 'לא קיים'; 
    break;
}

include('./template.php');

require_once '../../app/views/footernew.php'; //footer