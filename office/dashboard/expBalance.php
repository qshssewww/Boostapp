<div class="card-header text-right">
	<strong class="text-secondary">
		<i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i> יתרה/תוקף שעומד להסתיים</strong>
</div>

<div class="card-body text-right DivScroll" style='min-height:361px; max-height:361px; overflow-y:scroll; overflow-x:hidden;'>

	<?php
    
    $GetClientActivitys = DB::table('client_activities')
    ->where('NotificationDays','<=', date('Y-m-d'))->where('Department','=', '1')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')
    ->Orwhere('TrueBalanceValue','<=', '1')->where('BalanceValue','>', '1')->whereNull('TrueDate')->where('Department','=', '2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')
    ->Orwhere('TrueBalanceValue','<=', '1')->where('BalanceValue','>', '1')->where('NotificationDays','<=', date('Y-m-d'))->where('Department','=', '2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')
    ->Orwhere('TrueBalanceValue','<=', '1')->where('BalanceValue','>', '1')->whereNull('TrueDate')->where('Department','=', '3')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')
    ->Orwhere('TrueBalanceValue','<=', '1')->where('BalanceValue','>', '1')->where('NotificationDays','<=', date('Y-m-d'))->where('Department','=', '3')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')  
    ->limit(10)->get();
    foreach ($GetClientActivitys as $GetClientActivity) { 
     
    $ClientInfo = DB::table('client')->where('id', '=', $GetClientActivity->ClientId)->where('CompanyNum', '=' , $CompanyNum)->first();    
        
   ?>

	<div class="alertb alert-light text-dark text-right">
		<div class="row align-items-center">
			<div class="col-md-3">

				<img class="rounded-circle img-fluid profileimage<?php echo @$ClientInfo->id; ?>"
				 alt="" style="vertical-align: middle;" src="../assets/img/21122016224223511960489675402.png">

			</div>
			<div class="col-md-9">
				<small>
					<a href="ClientProfile.php?u=<?php echo @$ClientInfo->id; ?>">
						<?php echo @$ClientInfo->CompanyName; ?>
					</a>, מנוי:
					<?php echo @$GetClientActivity->ItemText; ?>
				</small>
				<br>
				<?php if (@$GetClientActivity->Department=='1') { ?>
				<small class="text-danger">תוקף המנוי:
					<?php echo with(new DateTime(@$GetClientActivity->TrueDate))->format('d/m/Y'); ?>
				</small>
				<?php } else if (@$GetClientActivity->Department=='2' || @$GetClientActivity->Department=='3') { ?>
				<small class="text-danger">יתרת שיעורים:
					<?php echo @$GetClientActivity->TrueBalanceValue; ?>
				</small>
				<?php if (@$GetClientActivity->TrueDate!='') { ?>
				<small class="text-danger">תוקף המנוי:
					<?php echo with(new DateTime(@$GetClientActivity->TrueDate))->format('d/m/Y'); ?>
				</small>
				<?php } ?>
				<?php } ?>

			</div>
		</div>
	</div>
	<hr>


	<?php } ?>

</div>



<span class="text-center text-secondary">
	<small class="font-weight-bold">
		<a href="InvildMemberShip.php" class="text-secondary">ניהול מנויים לא בתוקף >></a>
	</small>
</span>