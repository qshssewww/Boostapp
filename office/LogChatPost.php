<?php 
require_once '../app/init.php'; 

if (Auth::guest()) exit;

if (Auth::userCan('116')){

  header('Content-Type: text/html; charset=utf-8');
  $CompanyNum = Auth::user()->CompanyNum;
  if (isset($_GET['Dates'])){
    $cMonth = date('m', strtotime($_GET['Dates']));
    $cYear = date('y', strtotime($_GET['Dates']));
  }
  else {
    $cMonth = isset($_GET['month']) ? $_GET['month'] : date('m');
    $cYear = isset($_GET['year']) ? $_GET['year'] : date('y'); 
  }
  
  $StartDate = date('Y-m-01', strtotime("$cYear-$cMonth-01"));
  $EndDate = date('Y-m-t', strtotime($StartDate));

  $OpenTables = DB::table('chat')->where('CompanyNum','=',$CompanyNum)->where('UserId', '!=', '0')->where('ToUserId', '!=', '0')->whereBetween('Dates', array($StartDate, $EndDate))->orderBy('id', 'DESC')->get();
  $OpenTableCount = count($OpenTables);

  $data = array();

  foreach($OpenTables as $key=>$Client){ 
    $UserId = DB::table('users')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $Client->UserId)->first();
    $ToUserId = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('id', '=', $Client->ToUserId)->first();
    
    $sender = $UserId ? $UserId->display_name : lang('not_found');
    $reciever = $ToUserId ? $ToUserId->CompanyName : lang('not_found');        
    
    $data[] = array(
      $key+1,
      $sender,      
      $reciever,   
      date('d/m/Y', strtotime($Client->Dates)),
      date('H:i:s', strtotime($Client->Dates)),
      $Client->Content,   
      $Client->Notification,
      ($Client->StatusTime != '') ? '<span style="font-size: 10px">'.date('d/m/Y H:i:s', strtotime($Client->StatusTime)).'</span>'      
      :"<span style='font-size: 10px;'></span>"
    );
  }
  echo json_encode(array('data'=>$data), JSON_UNESCAPED_UNICODE);
}