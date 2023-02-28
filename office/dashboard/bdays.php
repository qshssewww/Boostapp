<div class="card spacebottom" style="margin-bottom: 20px;">
			<div class="card-header text-right">
				<strong class="text-center text-secondary">
					<i class="fas fa-gift"></i> חוגגים יום הולדת החודש</strong>
			</div>

			<div class="card-body text-right DivScroll" style='min-height:149px; max-height:149px; overflow-y:scroll; overflow-x:hidden;'>
				<div class="card-body text-right" style="padding-top: 0px; margin-top: 0px;">
					<?php   
			$CheckM = date('m');
			$a_date = date('Y-m-d');
			$Today = date('m-d');
			if ($CheckM=='12'){
			$SevenDays = date("Y-m-t", strtotime($a_date));
			}
			else {
			$SevenDays = date('m-d', strtotime("+7 day"));	
			} 
          
        $ClientDobs = DB::select("SELECT * FROM `client` where CompanyNum='".$CompanyNum."' AND Status=0 AND DATE_FORMAT(Dob, '%m-%d') BETWEEN '".$Today."' AND '".$SevenDays."'  ORDER by  DATE_FORMAT(Dob,'%M %d') ASC LIMIT 15 ");     
         foreach ($ClientDobs as $ClientDob) {   
         ?>
					<span class="text-right">
						<small>
							<a href="ClientProfile.php?u=<?php echo $ClientDob->id; ?>">
								<?php echo $ClientDob->CompanyName; ?>
							</a>
							<strong class="float-left">
								<?php echo with(new DateTime($ClientDob->Dob))->format('d/m/Y'); ?>
							</strong>
						</small>
					</span>
					<hr>

					<?php } ?>

				</div>
			</div>
			<span class="text-center text-info">
				<small class="font-weight-bold">
					<a href="Reports/BDay.php" class="text-secondary">ניהול ימי הולדת >></a>
				</small>
			</span>
		</div>