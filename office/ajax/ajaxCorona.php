<?php
require_once '../../app/init.php';
require_once '../Classes/Company.php';
require_once '../Classes/ClassCalendar.php';
require_once '../Classes/Client.php';

if (Auth::check()) {

    if(isset($_POST["corona"])) {
        $corona = $_POST["corona"];
        if($corona == 1 || $corona == 0) {
            $company = Company::getInstance();
            $company->__set("greenPass",$corona);
            $company->updateGreenPass();
        }
    }
    if(isset($_POST["fun"]) && $_POST["fun"] == "removeClasses" ) {
        $classesCalender = new ClassCalendar();
        $types = array(
            "regular" => $_POST["regular"],
            "zoom" => $_POST["zoom"],
            "online" => $_POST["online"],
        );
        echo $classesCalender->getAllClassInRange($_POST["startDate"], $_POST["endDate"],$types);

    } else if(isset($_POST["fun"]) && $_POST["fun"] == "resetGreenPassDate") {
        $res = (new Client())->resetGreenPassDateToAll();
        echo json_encode($res,JSON_UNESCAPED_UNICODE);
    }
}