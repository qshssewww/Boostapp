<?php

require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$Act = $_REQUEST['Act'];

if ($Act=='0'){
  
$dt = date('Y-m-d',strtotime("-7 day"));     
$StartDate = date("Y-m-d", strtotime($dt)); 
$EndDate = date("Y-m-d");     
    
}
else if ($Act=='1'){
 
$dt = date('Y-m-d',strtotime("-1 Months"));     
$StartDate = date("Y-m-d", strtotime($dt)); 
$EndDate = date("Y-m-d");       
    
}

else if ($Act=='2'){
 
$dt = date('Y-m-d',strtotime("-3 Months"));     
$StartDate = date("Y-m-d", strtotime($dt)); 
$EndDate = date("Y-m-d");       
    
}

?>



<canvas id="myChart"></canvas>
   <hr>
       
<?php 
$TotalClassMax = '0';
$TotalClassRegister = '0';
$TotalClassWating = '0';
       
$ClassMaxCounts = DB::table('classstudio_date')->where('CompanyNum','=',$CompanyNum)->where('Status','=','1')->whereBetween('StartDate', array($StartDate, $EndDate))->get();  

foreach ($ClassMaxCounts as $ClassMaxCount) {

$TotalClassMax += $ClassMaxCount->MaxClient;    
$TotalClassRegister += $ClassMaxCount->ClientRegister;
$TotalClassWating += $ClassMaxCount->WatingList;    
    
}
   
       
  if ($TotalClassMax!='0' && $TotalClassRegister!='0'){      
 $FixTotalClassMax2 = @$TotalClassMax;
 $FixTotalClassMax = @round((($TotalClassMax-$TotalClassRegister)/$TotalClassMax)*100);
 $FixTotalClassRegister = @round(($TotalClassRegister/$TotalClassMax)*100);
 $FixTotalClassWating = @round(($TotalClassWating/$TotalClassMax)*100);       
 }       
 else {
 $FixTotalClassMax2 = '0';
 $FixTotalClassMax = '0';
 $FixTotalClassRegister = '0';
 $FixTotalClassWating = '0';     
 }     
       
?>
       
       
       
    <div class="row align-items-center">
    <div class="col-sm order-md-1 text-right">
     <span class="text-center text-secondary"><small class="font-weight-bold">ר.המתנה</small></span> 
    </div>
    <div class="col-sm order-md-2">
       <div class="progress"> 
   <div class="progress-bar bg-warning watingbar" role="progressbar" style="width:0%;" aria-valuenow="<?php echo $FixTotalClassWating; ?>" aria-valuemin="0" aria-valuemax="100"></div>
   </div>      
    </div>
        
    <div class="col-sm order-md-3">
     <span class="text-center text-secondary"><small class="font-weight-bold"><?php echo $FixTotalClassWating; ?>%</small></span>     
    </div>    

       </div>  

<script>

var ctx = document.getElementById("myChart");   
var data = {
  labels: ["ניצול תופסה", "אי ניצול"],
  datasets: [{
 backgroundColor: [
                '#00c736',
                '#FF003B'
            ],
hoverBackgroundColor:[
               '#218838',
                '#c82333'    
],
    data: [<?php echo $FixTotalClassRegister; ?>, <?php echo $FixTotalClassMax; ?>],
  }]
};
    
var option = {
  legend: {
    display: true,
    position: 'right'
},
  responsive: true,
};
    
    
var myPieChart = new Chart(ctx,{
    type: 'doughnut',
    data: data,
    options: option
});

$(".watingbar").animate({
    width: "<?php echo $FixTotalClassWating; ?>%"
}, 50);    
    
</script>
