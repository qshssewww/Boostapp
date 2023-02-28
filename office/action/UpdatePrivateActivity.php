<?php require_once '../../app/init.php'; ?>

<?php
$CompanyNum = Auth::user()->CompanyNum;
$Act = $_REQUEST['Act'];
$GroupNumber = @$_REQUEST['GroupNumber'];
$ClientId = @$_REQUEST['ClientId'];
$ActivityId = @$_REQUEST['ActivityId'];


if ($Act=='1'){
  
    
DB::table('tempclient_private')
->where('CompanyNum', '=' , $CompanyNum)
->where('ClientId', '=' , $ClientId)
->where('GroupNumber', '=' , $GroupNumber)     
->update(array('ActivityId' => $ActivityId));      
    
    
}
else if ($Act=='2'){
 
DB::table('tempclient_private')
->where('CompanyNum', '=' , $CompanyNum)
->where('ClientId', '=' , $ClientId)
->where('GroupNumber', '=' , $GroupNumber)    
->update(array('NewActivityId' => $ActivityId));    
    
    
}	
else if ($Act=='3'){
    
 
DB::table('tempclient_private')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->where('GroupNumber', '=', $GroupNumber)->delete();      
    
}


?>