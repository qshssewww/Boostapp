<?php require_once '../../app/initcron.php'; 


$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();

$ClassYear = $_REQUEST['ClassYear'];
$ClassMonth = $_REQUEST['ClassMonth'];    
$ClassDateStart = $ClassYear.'-'.$ClassMonth.'-01';
$ClassDateEnd = $ClassYear.'-'.$ClassMonth.'-'.date('t',strtotime($ClassDateStart));
$ClientId = $_REQUEST['ClientId'];
?>

<div class="row" style="padding-right: 15px;padding-left: 15px;" dir="rtl">

    <div class="col-md-2">     
             <?php

$starting_year  = $SettingsInfo->StartYear;
$ending_year    = date('Y');

for($starting_year; $starting_year <= $ending_year; $starting_year++) {
    if ($starting_year==$ClassYear){
    $years[] = '<option value="'.$starting_year.'" selected>'.$starting_year.'</option>';    
    }
    else {
    $years[] = '<option value="'.$starting_year.'">'.$starting_year.'</option>';
    }
}

?>     
    <select name="HistoryYears" id="HistoryYears" data-placeholder="בחר שנה" class="form-control form-control-sm" style="width:100%;" >   
    <?php echo implode("\n\r", $years);  ?>
    </select>  
    </div>    
        
     <div class="col-md-10">    
    <nav aria-label="Page navigation example">
  <ul class="pagination justify-content-center">
    <ul class="pagination pagination-sm">
    <li class="page-item <?php if ($ClassMonth=='01'){ echo 'active';  $TextColor = ''; } else { $TextColor = 'text-primary'; } ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="01">ינואר</a></li>
    <li class="page-item <?php if ($ClassMonth=='02'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="02">פברואר</a></li>
    <li class="page-item <?php if ($ClassMonth=='03'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="03">מרץ</a></li>
    <li class="page-item <?php if ($ClassMonth=='04'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="04">אפריל</a></li>    
    <li class="page-item <?php if ($ClassMonth=='05'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="05">מאי</a></li>
    <li class="page-item <?php if ($ClassMonth=='06'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="06">יוני</a></li>
    <li class="page-item <?php if ($ClassMonth=='07'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="07">יולי</a></li>
    <li class="page-item <?php if ($ClassMonth=='08'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="08">אוגוסט</a></li>
    <li class="page-item <?php if ($ClassMonth=='09'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="09">ספטמבר</a></li>
    <li class="page-item <?php if ($ClassMonth=='10'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="10">אוקטובר</a></li>
    <li class="page-item <?php if ($ClassMonth=='11'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="11">נובמבר</a></li>
    <li class="page-item <?php if ($ClassMonth=='12'){ echo 'active'; $TextColor = ''; } else { $TextColor = 'text-primary'; }  ?>"><a class="page-link HistoryItem <?php echo $TextColor; ?>" href="javascript:void(0)" data-month="12">דצמבר</a></li>    
  </ul>
</nav>    
 </div>       

 </div>
 
 
  <div class="alertb alert-light">      
  <div class="row" style="padding-right: 15px;padding-left: 15px;" dir="rtl">
      
<div class="col-md-2 col-sm-12 order-md-1">      
<label>מתאמנים</label>     
</div>  
      
   <div class="col-md col-sm-12 order-md-2">      
<label>מיקום</label>      
   </div>
   <div class="col-md-2 col-sm-12 order-md-3">      
<label>כותרת שיעור</label>      
   </div>
   <div class="col-md col-sm-12 order-md-4">      
<label>תאריך</label>    
   </div>

   <div class="col-md col-sm-12 order-md-5">      
<label>יום</label>   
   </div>      
  
    <div class="col-md col-sm-12 order-md-6">      
<label>שעה</label>     
   </div>
      
   <div class="col-md col-sm-12 order-md-7">      
<label>מדריך</label>      
   </div>       
 <div class="col-md col-sm-12 order-md-8">  
 <label>סטטוס</label>    
</div> 
</div>      
</div>         
        
        
<?php 
 

$ClassHistorys =  DB::table('classstudio_date')->where('GuideId', '=', $ClientId)->whereBetween('StartDate', array($ClassDateStart, $ClassDateEnd))->where('CompanyNum', $CompanyNum)->orderBy('StartDate','ASC')->orderBy('Status','ASC')->get();     
foreach ($ClassHistorys as $ClassHistory) { 

$FloorName =  DB::table('sections')->where('id', '=', $ClassHistory->Floor)->where('CompanyNum', $CompanyNum)->first();    
@$UsersDB = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ClassHistory->GuideId)->first();
    
if ($ClassHistory->Status=='0'){
$StatusInfoTitle = 'פעיל';          
$StatusInfoColor = 'class="text-primary"';
}
else if ($ClassHistory->Status=='1'){
$StatusInfoTitle = 'הושלם';          
$StatusInfoColor = 'class="text-info"';
}
else if ($ClassHistory->Status=='2'){
$StatusInfoTitle = 'בוטל';          
$StatusInfoColor = 'class="text-danger"';
}    

    
?>         
<div class="alertb alert-light">
  <div class="row" style="padding-right: 15px;padding-left: 15px;" dir="rtl">
      
<div class="col-md-2 col-sm-12 order-md-1">     
<p><a href='javascript:LogClass("<?php echo $ClassHistory->id; ?>");'>רשימת מתאמנים</a></p>    
</div>  
      
   <div class="col-md col-sm-12 order-md-2">      
<p><?php echo $FloorName->Title; ?></p>       
   </div>
   <div class="col-md-2 col-sm-12 order-md-3">      
<p><?php echo $ClassHistory->ClassName; ?></p>       
   </div>
   <div class="col-md col-sm-12 order-md-4">      
<p><?php echo with(new DateTime($ClassHistory->StartDate))->format('d/m/Y'); ?></p>       
   </div>

   <div class="col-md col-sm-12 order-md-5">      
<p><?php echo $ClassHistory->Day; ?></p>       
   </div>      
  
    <div class="col-md col-sm-12 order-md-6">      
<p><?php echo with(new DateTime($ClassHistory->StartTime))->format('H:i'); ?></p>        
   </div>
      
   <div class="col-md col-sm-12 order-md-7">      
<p><?php echo @$UsersDB->display_name; ?></p>       
   </div>       
 <div class="col-md col-sm-12 order-md-8">  
<p <?php echo $StatusInfoColor ?>><?php echo $StatusInfoTitle ?></p>     
</div> 
      
</div>    
</div> 
        
<hr>        
<?php } ?> 
<input type="hidden" id="HistoryMonth">
    
<script>
$(document).ready(function(){	
$('#HistoryMonth').val('<?php echo $ClassMonth; ?>');
$('.HistoryItem').click(function() {
    
    var ClassYear = $('#HistoryYears').val();
    var ClassMonth = $(this).data('month');
    var ClientId = '<?php echo $ClientId; ?>';
    $('#HistoryMonth').val(ClassMonth);
    var url = 'action/ClassHistoryCoach.php?ClassYear='+ClassYear+'&ClassMonth='+ClassMonth+'&ClientId='+ClientId; 
    $('#DivClassHistory').empty();
    $('#DivClassHistory').load(url,function(){ });

 });    
    
$('#HistoryYears').change(function() {
    
    var ClassYear = $('#HistoryYears').val();
    var ClassMonth = $('#HistoryMonth').val();
    var ClientId = '<?php echo $ClientId; ?>';
    var url = 'action/ClassHistoryCoach.php?ClassYear='+ClassYear+'&ClassMonth='+ClassMonth+'&ClientId='+ClientId; 
    $('#DivClassHistory').empty();
    $('#DivClassHistory').load(url,function(){ });

 });      
    
    
});
</script>