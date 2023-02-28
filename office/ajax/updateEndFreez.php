<?php 
require_once '../../app/init.php';
require_once '../Classes/FreezActivities.php';

if (Auth::guest()) {
    exit;
}
$freez_id = isset($_POST['freezId']) ? $_POST['freezId'] : '';
$freezActivity = new FreezActivities($freez_id);
$count = 0;
$action = '';
if(isset($_POST['method'])) {
    switch ($_POST['method']) {
        case "update":
            $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

            if(empty($end_date)) {
                echo 'לא התקבל תאריך סיום';
                exit;
            }

            if($end_date > date('Y-m-d')) {
                $count = $freezActivity->updateEndDate($end_date);
                $msg = 'תאריך סיום ההקפאה נערך ל - '. date('d/m/Y' ,strtotime($end_date)) . ' בהצלחה עבור '. $count . ' מנויים.';
                $action = $_POST['method'];
            } else {
                $count = $freezActivity->freezOut();
                $msg = $count . ' מנויים הופשרו בהצלחה.';
                $action = "freezOut";
            }
            if($count == 0) {
                $msg = 'לא נמצאו מנויים.';
            }
        break;

        case 'freezOut':
            $count = $freezActivity->freezOut();
            $msg = $count . ' מנויים הופשרו בהצלחה.';
            $action = $_POST['method'];
        break;

    }
} else {
    $msg = 'לא התקבלו נתונים';
}

echo json_encode(array('message' => $msg, 'count' => $count, 'action' => $action), true);

?>