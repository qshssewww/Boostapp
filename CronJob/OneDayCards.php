<?php
die('<hr /><pre>' . print_r('Here: ' . __LINE__ . ' at ' . __FILE__, true) . '</pre><hr />');

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();



set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');


//////////////////////////////////////////////////////////////// סגירת שיעורים ///////////////////////////////////////////////////////


$Clients = DB::table('classstudio_act')->where('ClassDate', '<', $ThisDate)->where('ActStatus', '=', '0')->whereIn('Status', array(1,2,4,6,8,11,15,21))->get(); 

foreach ($Clients as $Client) {
 
$CheckSettings = DB::table('settings')->where('CompanyNum' ,'!=', '569121')->where('CompanyNum' ,'=', $Client->CompanyNum)->where('Status','=','0')->first();     
if (@$CheckSettings->id!=''){      
     
           /// ניקוב משיבוץ קבוע להגיע/מומש     
           
            $ClientInfo = DB::table('client_activities')->where('id', '=', $Client->ClientActivitiesId)->where('CompanyNum', $Client->CompanyNum)->first();
    
            if (@$ClientInfo->Department=='2' || @$ClientInfo->Department=='3') {
                
            $ActBalanceValue = $ClientInfo->ActBalanceValue-1;   
                
//            if ($ActBalanceValue=='1'){
//            $CardStatus = '0';
//            }
//            else if ($ActBalanceValue<='0'){
//            $CardStatus = '1';
//            }
//            else {
//            $CardStatus = $ClientInfo->CardStatus;
//            }
                
            /// עדכון כרטיסיה        
            DB::table('client_activities')
           ->where('id', $Client->ClientActivitiesId)
           ->where('CompanyNum', $Client->CompanyNum)       
           ->update(array('ActBalanceValue' => $ActBalanceValue));
                
           } 
    
           DB::table('classstudio_act')
           ->where('id', $Client->id)
           ->where('CompanyNum', $Client->CompanyNum)       
           ->update(array('ActStatus' => '1')); 
    

}
    
else {
 
DB::table('classstudio_act')
->where('id', $Client->id)
->where('CompanyNum', $Client->CompanyNum)       
->update(array('ActStatus' => '1'));     
    
    
}    
    

    
}

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');

//////////////////////////////////////////////////////////////// סיום פקודת מערכת ///////////////////////////////////////////////////////

$Cron->end();

?>
