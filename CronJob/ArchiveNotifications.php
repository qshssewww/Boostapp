<?php


$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/AppNotification.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/AppNotificationArc.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

//require_once '../app/init.php';
//require_once '../office/Classes/AppNotification.php';
//require_once '../office/Classes/AppNotificationArc.php';
//require_once '../office/Classes/Utils.php';

set_time_limit(0);
ini_set("memory_limit", "-1");
$now = date('Y-m-d H:i:s');
echo("Cron_start " . date('H:i:s') . "\n");

$date = date('Y-m-01',strtotime('-6 months'));
$appObj = new AppNotification();
$utils = new Utils();
$notifications = $appObj->getNotificationsBeforeDate($date,300);
if(!empty($notifications)) {
    $notifications = $utils->createArrayFromObjArr($notifications, true);
}
$ids = array();
$iteration = 1;
$rows = count($notifications);
while (!empty($notifications) && $rows < 300000){
    echo("Iteration Number: " . $iteration . " Start Time: " . date('H:i:s') . "\n");
    AppNotificationArc::insertBulk($notifications);
    foreach ($notifications as $key => $notification) {
        array_push($ids,$notification["id"]);
    }
    echo("Iteration Number: " . $iteration . " End Time: " . date('H:i:s') . "\n");
    $iteration++;
    echo("Rows Number: " . $rows . " Time: " . date('H:i:s') . "\n");
    $notifications = $appObj->getNotificationsBeforeDate($date,300);
    $notifications = $utils->createArrayFromObjArr($notifications,true);
    $rows = $rows + count($notifications);
}
$beforeDel = date('Y-m-d H:i:s');
echo("Start Deleting " . date('H:i:s') . "\n");
AppNotification::deleteBulk($ids);
$afterDel = date('Y-m-d H:i:s');
$diffDel = strtotime($afterDel) - strtotime($beforeDel);
echo("Deletion Ends After " .$diffDel . " Seconds". "\n");
echo("Cron_end " . date('H:i:s') . "\n");
$later = date('Y-m-d H:i:s');
$diff = strtotime($later) - strtotime($now);

echo("Cron Ends After " .$diff . " Seconds". "\n");



$Cron->end();
