<div class="card-header text-right">
	<strong class="text-secondary">
		<span><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i></span> אונליין באפליקציה</strong>
</div>

<div class="card-body text-right">

	<?php 
    $ThisMin = date("H:i:s", strtotime('-10 minutes', strtotime(date('H:i:s'))));     
    $AndroidCounts = DB::table('boostapplogin.studio')->where('CompanyNum',  $CompanyNum)->where('OS',  '1')->where('LastDate', '=', date('Y-m-d'))->where('LastTime', '>=', $ThisMin)->count(); 
    $IOSCounts = DB::table('boostapplogin.studio')->where('CompanyNum',  $CompanyNum)->where('OS',  '2')->where('LastDate', '=', date('Y-m-d'))->where('LastTime', '>=', $ThisMin)->count();  
         
    $TotalOnline = $AndroidCounts+$IOSCounts;     
            
    ?>


	<div class="row align-items-center">
		<div class="col-sm order-md-3 text-center">
			<span class="text-center font-weight-bold display-4" style="color: lightgrey;">
				<i class="fab fa-apple"></i>
			</span>
			<br>
			<span class="text-center text-secondary">
				<small class="font-weight-bold">IOS</small>
			</span>
			<br>
			<span class="text-center text-success font-weight-bold">
				<?php echo $IOSCounts; ?>
			</span>
		</div>
		<div class="col-sm order-md-2" style="padding-bottom: 5px; padding-top: 5px;">
			<div class="progress">
				<div class="progress-bar bg-success OSbar" role="progressbar" style="width: 0%;" aria-valuenow="<?php echo $TotalOnline; ?>"
				 aria-valuemin="0" aria-valuemax="100"></div>
			</div>
		</div>
		<div class="col-sm order-md-1 text-center">
			<span class="text-center font-weight-bold display-4" style="color: lightgrey;">
				<i class="fab fa-android"></i>
			</span>
			<br>
			<span class="text-center text-secondary">
				<small class="font-weight-bold">ANDROID</small>
			</span>
			<br>
			<span class="text-center text-success font-weight-bold">
				<?php echo $AndroidCounts; ?>
			</span>
		</div>
	</div>


	<div class="card-body text-right DivScroll" style='min-height:206px; max-height:206px; overflow-y:scroll; overflow-x:hidden;'>
		<?php
    $AppLogs = DB::table('boostapplogin.log')->where('CompanyNum',  $CompanyNum)->where('Dates', '>=', date('Y-m-d'))->orderBy('Dates','DESC')->limit(20)->get();
    foreach ($AppLogs as $AppLog) {   
    
    $ClientLogInfo = DB::table('client')->where('CompanyNum',  $CompanyNum)->where('id',  $AppLog->ClientId)->first();
    $OSIconInfo = DB::table('boostapplogin.studio')->where('CompanyNum',  $CompanyNum)->where('ClientId',  $AppLog->ClientId)->first();    
    
    if (@$OSIconInfo->OS=='1'){
    $OSIcon = 'android';    
    }  
    else if (@$OSIconInfo->OS=='2'){
    $OSIcon = 'apple';     
    }    
    else {
    $OSIcon = 'windows';    
    }    
        
        
    ?>
		<div class=" alertb alert-light text-right">
			<small>
				<i class="fab fa-<?php echo @$OSIcon; ?>"></i>
				<?php echo with(new DateTime(@$AppLog->Dates))->format('H:i'); ?>//
				<a href="ClientProfile.php?u=<?php echo @$ClientLogInfo->id; ?>">
					<?php echo @$ClientLogInfo->CompanyName; ?>
				</a> -
				<?php echo @$AppLog->Text; ?>
			</small>
		</div>
		<hr>
		<?php } ?>

	</div>


</div>

<span class="text-center text-secondary">
	<small class="font-weight-bold">
		<a href="AppLog.php" class="text-secondary">לוג אפליקציה >></a>
	</small>
</span>