<div class="card spacebottom" style="margin-bottom: 20px;">
	<div class="card-header text-right">
		<strong class="text-center text-secondary">
		<span><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i></span> כרטיסי לקוח אחרונים</strong>
	</div>


	<div class="card-body text-right DivScroll" style='min-height:154px; max-height:154px; overflow-y:scroll; overflow-x:hidden;'>
		<div class="card-body text-right" style="padding-top: 0px; margin-top: 0px;">

			<?php
    $ClientLasts = DB::table('client')->where('CompanyNum','=',$CompanyNum)->where('ChangeDate','>=',date('Y-m-d'))->limit(15)->get();        
foreach ($ClientLasts as $ClientLast) {   
    ?>
			<span class="text-right">
				<small>
					<a href="ClientProfile.php?u=<?php echo $ClientLast->id; ?>">
						<?php echo $ClientLast->CompanyName; ?>
					</a>
					<strong class="float-left">
						<?php echo with(new DateTime($ClientLast->ChangeDate))->format('H:i'); ?>
					</strong>
				</small>
			</span>
			<hr>

			<?php } ?>
		</div>

	</div>


	<span class="text-center text-secondary">
		<small class="font-weight-bold">
			<a href="Client.php?Act=0" class="text-secondary">ניהול לקוחות >></a>
		</small>
	</span>
	</span>
</div>