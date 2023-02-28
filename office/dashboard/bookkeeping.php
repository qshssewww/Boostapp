<div class="card-header text-right">
	<strong class="text-secondary">
    <span><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i></span> הנה"ח
	</strong>
</div>
<div class="card-body text-right">
	<div class="row align-items-center">
		<div class="col-md-7 order-md-2 text-right">
			<canvas id="myChartTop"></canvas>
		</div>
		<div class="col-md-5 order-md-1">

			<?php
                        
                        //// סיכום מזומן
                        $CashAmount = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->where('UserDate', '=', date('Y-m-d'))->where('TypePayment', '=', '1')->sum('Amount'); 
                        
                        //// סיכום כרטיסי אשראי
                        $CreditAmount = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->where('UserDate', '=', date('Y-m-d'))->where('TypePayment', '=', '3')->sum('Amount'); 
                            
                        //// סיכום המחאות
                        $CheckAmount = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->where('UserDate', '=', date('Y-m-d'))->where('TypePayment', '=', '2')->sum('Amount');     
                            
                        //// סיכום העברות בנקאיות
                        $BankAmount = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->where('UserDate', '=', date('Y-m-d'))->where('TypePayment', '=', '4')->sum('Amount');   
                            
                        $TotalAmount = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->where('UserDate', '=', date('Y-m-d'))->sum('Amount');       
                        
                        if ($TotalAmount==''): $TotalAmount = '0.00'; endif;  
                        if ($CashAmount==''): $CashAmount = '0.00'; endif;  
                        if ($CreditAmount==''): $CreditAmount = '0.00'; endif;  
                        if ($CheckAmount==''): $CheckAmount = '0.00'; endif;   
                        
                    ?>

			<div class="card-body text-right" style="margin-bottom: 0px; padding-bottom: 0px;">
				<span class="text-center text-secondary">
					<small class="font-weight-bold">סה"כ תקבולים היום</small>
				</span>
				<br>
				<span class="text-center font-weight-bold text-success" style="font-size: 30px;">₪
					<?php echo $TotalAmount; ?>
				</span>
				<?php if ($CashAmount!='') { ?>
				<p class="text-right">סה"כ מזומן:
					<strong class="float-left">
						<?php echo $CashAmount; ?>
						</mark>
					</strong>
				</p>
				<?php } ?>
				<?php if ($CreditAmount!='') { ?>
				<p class="text-right">סה"כ כ.אשראי:
					<strong class="float-left">
						<?php echo $CreditAmount; ?>
						</mark>
					</strong>
				</p>
				<?php } ?>
				<?php if ($CheckAmount!='') { ?>
				<p class="text-right">סה"כ המחאות:
					<strong class="float-left">
						<?php echo $CheckAmount; ?>
						</mark>
					</strong>
				</p>
				<?php } ?>
				<?php if ($BankAmount!='') { ?>
				<p class="text-right">סה"כ ה.בנקאיות:
					<strong class="float-left">
						<?php echo $BankAmount; ?>
						</mark>
					</strong>
				</p>
				<?php } ?>
				<p class="text-right" style="display: none;">סה"כ מס"ב:
					<strong class="float-left">0.00</mark>
					</strong>
				</p>

				<p class="text-right text-danger" style="display: none;">סה"כ זיכויים:
					<strong class="float-left">0.00</mark>
					</strong>
				</p>
			</div>
		</div>
	</div>
</div>
<span class="text-center text-secondary">
	<small class="font-weight-bold">
		<a href="CartesetAll.php" class="text-secondary">דוחות כספיים >></a>
	</small>
</span>