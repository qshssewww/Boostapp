<?php

require_once '../../../app/initcron.php';


$Table = @$_REQUEST['Table'];
$CompanyNum = @$_REQUEST['CompanyNum'];


if ($CompanyNum!=''){
$GetClientActivitys = DB::table($Table)->where('CompanyNum', $CompanyNum)->get(); 
}
else {
$GetClientActivitys = DB::table($Table)->get();	
}


echo json_encode($GetClientActivitys);

?>