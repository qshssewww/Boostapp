<?php require_once '../app/initcron.php'; 



if (Auth::guest()){
  header("HTTP/1.0 401 Not Allowed");
  exit;
} 
if (Auth::userCan('82')):

$CompanyNum = Auth::user()->CompanyNum;
$OpenTables = DB::table('classstudio_date')->where('CompanyNum','=', $CompanyNum)->where('Status','!=', '0')->orderBy('StartDate', 'ASC')->get();
$OpenTableCount = count($OpenTables);

$data = array();

$number = $OpenTableCount;

foreach($OpenTables as $Class){
  $ClassType = DB::table('class_type')->where('CompanyNum','=', $CompanyNum)->where('id','=', $Class->ClassNameType)->first();
    
  $RegisterClient = $Class->ClientRegister; 
  $WatingClient = $Class->WatingList; 
    

  @$PRegisterClient = round(($RegisterClient) / ($Class->MaxClient) * 100);
 
  $ClassTitle = str_replace('"','``',$Class->ClassName);
  $ClassTitle = str_replace("'",'`',$ClassTitle); 
    
  $Type = str_replace('"','``',$ClassType->Type);
  $Type = str_replace("'",'`',$Type);     
 

$StausTitle =  ($Class->Status=='1')  ? '<SPAN class=\"text-success\"><strong>הושלם</strong></SPAN>' : '<SPAN class=\"text-danger\"><strong>בוטל</strong></SPAN>';     
  
   
$data[] = array(
  htmlentities(addslashes(@$Type)),
  "<a class=\"text-success\" href=\"javascript:NewViewClass('". @$Class->id ."');\" ><strong class=\"text-success\">". htmlentities(addslashes(@$ClassTitle)) ."</strong></a>",
  with(new DateTime(@$Class->StartDate))->format('d/m/Y'),
  @$Class->Day,
  with(new DateTime(@$Class->StartTime))->format('H:i'),
   @$Class->GuideName,
  "<a class=\"text-success\" href=\"javascript:UpdateClass('". @$Class->id ."','2');\" ><strong class=\"text-success\">". @$RegisterClient ."</strong></a>",
  @$Class->MaxClient-@$RegisterClient,
  @$WatingClient,
  @$PRegisterClient ."%",
  "<a class=\"text-success\" href=\"javascript:UpdateClassStatus('". @$Class->id."','". @$Class->Status ."');\" >". $StausTitle ."</a>"
);

 $RegisterClient = '0';
 $WatingClient = '0';    
 $PRegisterClient = '0';   
  
} 
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array("data"=>$data), JSON_UNESCAPED_UNICODE);
else:
  header("HTTP/1.0 401 Not Allowed");
  die();
endif;
?>