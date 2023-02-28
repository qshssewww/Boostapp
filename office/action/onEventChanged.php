<?php
require_once '../../app/initcron.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$ItemId = Auth::user()->ItemId; 

$Id = $_REQUEST['TaskId'];
   
$CalInfo = DB::table('calendar')->where('id','=',$Id)->first(); 
$FloorInfo = DB::table('sections')->where('id','=',$CalInfo->Floor)->first();     
 
$Floor = @$CalInfo->Floor; 
$FloorName = @$FloorInfo->Title;
$StartDate = with(new DateTime($CalInfo->start_date))->format('Y-m-d');
$StartTime = with(new DateTime($CalInfo->start_date))->format('H:i:s');
$EndTime = with(new DateTime($CalInfo->end_date))->format('H:i:s');   
    
DB::table('calendar')
->where('id', $Id)
->update(array('Floor' => $Floor, 'FloorName' => $FloorName, 'StartDate' => $StartDate, 'StartTime' => $StartTime, 'EndTime' => $EndTime));    

?>