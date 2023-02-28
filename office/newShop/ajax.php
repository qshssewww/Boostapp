<?php
require_once '../../app/init.php';
require_once "../Classes/Company.php";
require_once "../Classes/ShopPost.php";
require_once "../Classes/ClassCalendar.php";
require_once "../Classes/Utils.php";
require_once "../Classes/ItemColor.php";
require_once "../Classes/Size.php";
require_once "../Classes/ItemSupplier.php";
require_once "../Classes/ItemRoles.php";

if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $data = $_POST;
    $company = Company::getInstance();
    if($data){
        $util = new Utils();
        $company = Company::getInstance();
        $classObj = new ClassCalendar();
        if($data["fun"] == "classesTickets"){
            $time = strtotime($data["date"]);
            $dateFormat = date("Y-m-d",$time);
            $classes = $classObj->getClassesByDate($dateFormat,$company->__get("CompanyNum"));
            if($classes == null){
                echo "classes not found";
                die();
            }
            else{
                $rolesObj = new ItemRoles();
                $classes = $rolesObj->checkItemRolesClasses($data["itemId"],$classes);
                if(empty($classes)){
                    echo "classes not found";
                    die();
                }
                $classesArr = array();
                foreach ($classes as $class){
                    $arr = array(
                        "id" => $class->__get("id"),
                        "name" => $class->__get("ClassName"),
                        "EndTime" => $class->__get("EndTime"),
                        "StartTime" => $class->__get("StartTime")
                    );
                    array_push($classesArr,$arr);
                }
                echo json_encode($classesArr, JSON_UNESCAPED_UNICODE);
                die();
            }
        }
        if($data["fun"] == "classesMembership"){
            $duration = $util->convertMembershipDurationToTime($data["duration"],$data["type"],$data["payment"]);
            $classes = $classObj->getGroupClassesByDay($data["day"],$company->__get("CompanyNum"),$duration);
            if($classes == null){
                echo "classes not found";
                die();
            }
            else{
                $rolesObj = new ItemRoles();
                $classes = $rolesObj->checkItemRolesClasses($data["itemId"],$classes);
                if(empty($classes)){
                    echo "classes not found";
                    die();
                }
                $classesArr = array();
                foreach ($classes as $class){
                    $arr = array(
                        "id" => $class->__get("id"),
                        "name" => $class->__get("ClassName"),
                        "group" => $class->__get("GroupNumber"),
                        "EndTime" => $class->__get("EndTime"),
                        "StartTime" => $class->__get("StartTime")
                    );
                    array_push($classesArr,$arr);
                }
                echo json_encode($classesArr, JSON_UNESCAPED_UNICODE);
                die();
            }
        }
        if($data["fun"] == "getColors"){
            $colorsObj = new ItemColor();
            $res = $colorsObj->getDefaultColors();
            $res = json_encode($res,true);
            echo $res;
            die();
        }
        if($data["fun"] == "getSizes"){
            $sizesObj = new Size();
            $res = $sizesObj->getDefaultSizes();
            $res = json_encode($res,true);
            echo $res;
            die();
        }
        if($data["fun"] == "getCompanySuppliers"){
            $suppObj = new ItemSupplier();
            $res = $suppObj->getCompanySuppliers($company->__get("CompanyNum"));
            $res = json_encode($res,true);
            echo $res;
            die();
        }
        if($data["fun"] == "getSingleItem"){
            $shop_post = new ShopPost();
            if($data["id"]){
                $res = $shop_post->getSingleItem($data["id"]);
                $res = json_encode($res, JSON_UNESCAPED_UNICODE);
                echo $res;
                die();
            }
            else
            {
                echo "error";
                die();
            }
        }
        if($data["fun"] == "getItemRoles"){
            $rolesObj = new ItemRoles();
            $roles = $rolesObj->getItemRoles($data["itemId"]);
            if(!empty($roles)) {
                $rolesArr = $rolesObj->createArrayFromObjArr($roles);
                echo json_encode($rolesArr,JSON_UNESCAPED_UNICODE);
                die();
            }
            echo "Not Found";
            die();
        }


        if($data["fun"] == "getSingleClubMemberships"){
            $shop_post = new ShopPost();
            if($data["id"]){
                $res = $shop_post->getSingleItem($data["id"]);
                $res = json_encode($res, JSON_UNESCAPED_UNICODE);
                echo $res;
                die();
            }
            else
            {
                echo "error";
                die();
            }
        }



    }
    else
    {
        echo "error";
        die();
    }

}
