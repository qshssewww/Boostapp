<?php require_once '../app/init.php'; ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('152')): ?>
<?php
$CompanyNum = Auth::user()->CompanyNum;

CreateLogMovement('נכנס לניהול טפסים דינאמיים', '0');	


defined('DEBUG') or define('DEBUG', 0);


// A helper function to output JSOn data fro AJAX request
function output($data){
    http_response_code($data->error?500:200);
    die(json_encode($data));     
}

// self submiting form from same url
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // prepare our response object in JSON



    $data = new stdclass();
    
    // PHP dosn't support JSON post in native
    // we need to import them ourself
    try {
        $inputJSON = file_get_contents('php://input');
        $_POST = json_decode( $inputJSON, FALSE );    
        if(empty($_POST)) throw new Exception("השדות לא הגיעו לשרת, פנה לתמיכה");
    }catch(\Exception $e){
        $data->error = true;
        $data->message = $e->getMessage();
        output($data); // kill execution
    }


    // Insert a new form to our database
    try{


//        $forecRenew = (bool) !empty($_POST->options->forceRenew) && $_POST->options->forceRenew === true? true : false;
        
        if ($_POST->editoption->formId=='') {
        $FormId = '';  
        }
        else {
        $FormId = $_POST->editoption->formId;    
        }
        

//        if($forecRenew):
//            DB::table('client')->where('CompanyNum', '=', $CompanyNum)->update(array('Medical'=>'0'));
//            DB::table('boostapplogin.studio')->where('CompanyNum', '=', $CompanyNum)->update(array('Medical'=>'0'));
//        endif;
        
        if ($FormId=='' ||  $FormId==0) {
        
        $GroupNumber = rand(1,9999999);
        
        $id = DB::table('dynamicforms')->insertGetId(array(
            "CompanyNum"=> $CompanyNum,
            "name"      => $_POST->title->text,
            "data"      => json_encode($_POST),
            "forceRenew" => '0',
            "GroupNumber" => $GroupNumber
        ));

        $TextLog = 'יצר טופס חדש בשם <u>'.$_POST->title->text.'</u> גרסה #'.$GroupNumber;    
        CreateLogMovement(
        $TextLog,
        '0');     
			
        }
        
        else {
          
        $item = DB::table('dynamicforms')
        ->where('id', '=', $FormId)
        ->where('CompanyNum', '=', $CompanyNum)
        ->first();    
         
        $GroupNumber = $item->GroupNumber;
        $id = $item->id;
        $FormName = $item->name;    
            
            DB::table('dynamicforms')
		    ->where('CompanyNum', '=' , $CompanyNum)
            ->where('id', '=' , $FormId)
            ->update(array(
            'name' => $_POST->title->text,
            "data"      => json_encode($_POST),
            "forceRenew" => '0'    
            ));     
         
        $TextLog = 'ערך טופס דינאמי בשם <u>'.$_POST->title->text.'</u> גרסה #'.$GroupNumber;    
        CreateLogMovement(
        $TextLog,
        '0'); 
	
        }
            
            
    }catch(\Exception $e){
        $data->error = true;
        if(!DEBUG) $data->message = 'אנו מתנצלים אך התגלתה שגיאה בעת שמירת הטופס, אנא פנה לשירות לקוחות להמשך טיפול בבעיה, תודה';
        if(DEBUG) $data->message = $e->getMessage();
        if(DEBUG) $data->trace = $e->getTrace();
        if(DEBUG) $data->code = $e->getCode();
        output($data);
    }



    $data->error = false;
    $data->id = $id;
    $data->message = sprintf("טופס %d נוצר בהצלחה", $GroupNumber);

    // finish excution of script
    output($data);

}

// generate form from pre existing template
$Dynamicforms = file_get_contents('AddForms.html');


if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['formId']) && (int) $_GET['formId'] > 0){
    
    
    $FormId = $_GET['formId'];
    
    // connect to our DB
    try{
    $item = DB::table('dynamicforms')
        ->where('id', '=', (int) $_GET['formId'])
        ->where('CompanyNum', '=', $CompanyNum)
        ->first();
        
    $TextLog = 'נכנס למצב עריכת טופס דינאמי בשם <u>'.$item->name.'</u> גרסה #'.$item->GroupNumber;    
    CreateLogMovement(
    $TextLog,
    '0');     
        
        
    }catch(\Exception $e){
       
    }

    // no result return, generate a blank form
    if(!empty($item) && !empty($item->data))
        $Dynamicforms = (str_replace('<!--custom script injection-->', sprintf('<script>var dynForm = %s</script>', $item->data), file_get_contents('AddForms.html')));
}



?>
<?php 
$pageTitle = 'ניהול טפסים דינאמיים';
require_once '../app/views/headernew.php';
?>

<link href="assets/css/fixstyle.css" rel="stylesheet">
<!-- <div class="col-md-12 col-sm-12">
<div class="row">



<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fab fa-wpforms"></i> ניהול טפסים דינאמיים
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">

</div>


</div>

<nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item active" aria-current="page">ניהול טפסים דינאמיים</li>
  </ol>  
</nav>     -->


    
    
<div class="row">
    

<?php include("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12">	
    <div class="card spacebottom">
    <div class="card-header text-start" >
    <i class="fab fa-wpforms"></i> <b>ניהול טפסים דינאמיים</b>
 	</div>    
  	<div class="card-body text-start">       
                    
 <div class="alertb alert-info" >שים לב! כל שאלה/פסקה מתווספים בתחתית הטופס. השאלות מוגדרות כשאלות חובה כברירת מחדל.</div>                      
<?php echo $Dynamicforms; ?>                       
<input type="hidden" id="fixformId" value="<?php echo @$FormId ?>"> 
        </div>
    </div>

	</div> 
</div>

</div>




<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>