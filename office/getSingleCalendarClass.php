<?php
require_once '../app/init.php';
require_once './Classes/ClassesType.php';
require_once './Classes/ZoomClasses.php';
require_once './Classes/ClassCalendar.php';
require_once './Classes/Brand.php';
require_once './Classes/Company.php';
require_once './Classes/RepetitionSettings.php';

if (Auth::guest()) exit;
if (Auth::check()) {
    if (Auth::userCan('31')) {
        try {
            $company = Company::getInstance();
            $postdata = file_get_contents('php://input');
            $data = json_decode($postdata);
            $class = new ClassCalendar();
            $class->setClassCalendarObjectById($data->id);
            echo json_encode($class->createArrayFromObj($class));
        } catch (Exception $e) {
            echo $e;
        }
    }
}
