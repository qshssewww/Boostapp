<?php require_once '../../app/initcron.php'; 


$Id = $_REQUEST['Id'];
$ClientId = $_REQUEST['ClientId'];

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$ClientInfo = DB::table('client')->where('id' ,'=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();
$PipeInfo = DB::table('pipeline')->where('id' ,'=', $Id)->where('CompanyNum' ,'=', $CompanyNum)->first();
@$AgentForThisLead = DB::table('users')->where('id', '=', @$PipeInfo->AgentId)->first();

$MainPipeId = @$PipeInfo->MainPipeId;

///הצלחה
$GetSuccessInfo = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Act' ,'=', '1')->first();
/// כשלון
$GetFailsInfo = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Act' ,'=', '2')->first();
/// לא רלוונטי
$GetNoneFailsInfo = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Act' ,'=', '3')->first();

$GetSuccess = $GetSuccessInfo->id;
$GetFails = $GetFailsInfo->id;
$GetNoneFails = $GetNoneFailsInfo->id;

?>


            
 <div class="row">
 <div class="col-md-3">	 
 <?php echo $ClientInfo->CompanyName; ?> 
 </div>  
  <div class="col-md-3">	 
  <?php echo $ClientInfo->ContactMobile ?> 
 </div>  
 <div class="col-md-3">	 
 <?php echo $ClientInfo->Email ?> 
 </div>   
  <div class="col-md-3"> 
סניף : <?php echo @$ClientInfo->BrandName; ?> 
 </div>  
</div>


<hr>

<table class="table table-bordered table-hover text-right wrap" dir="rtl"  cellspacing="0" width="100%" id="LeadsTable">
            <tbody>
				<tr>
				<td style="text-align:right;width:20%;" class="bg-light">תאריך הוספה</td>
				<td dir="ltr" style="text-align: right;"><?php echo with(new DateTime(@$PipeInfo->Dates))->format('d/m/Y H:i:s'); ?></td>
				</tr>
				<?php if (@$PipeInfo->Source != '') { ?>
				<tr>
				<td style="text-align:right;width:20%;" class="bg-light">מקור הליד</td>
				<td dir="rtl" style="text-align: right;"><?php echo @$PipeInfo->Source; ?></td>
				</tr>
				<?php } ?>
                <tr>
				<td style="text-align:right;width:20%;" class="bg-light">מתעניין ב-</td>
				<td dir="rtl" style="text-align: right;"><?php echo @$PipeInfo->ClassInfoNames; ?></td>
				</tr>
				<?php	
				if (!empty($PipeInfo->Info)) {
				$Loops =  json_decode($PipeInfo->Info,true);	
    			foreach($Loops['data'] as $key){ 
				foreach($key as $key2=>$val){ 
				?>
				<tr>
				<td style="text-align:right;width:20%;" class="bg-light"><?php echo $key2; ?></td>
        		<td><?php echo $val; ?></td>
				</tr>
				<?php	}	}	}	?>
				<tr>
				<td style="text-align:right;width:20%;" class="bg-light">נציג מטפל</td>
      			<td id="TakeLeadTD">
       			<?php
				if (Auth::userCan('141')) {
				?>
                    <select name="Agents" id="ChooseAgents" class="form-control js-example-basic-single text-right AgentLoop ChangeLeadAgent" dir="rtl" style="width: 100%" data-placeholder="בחר נציג">
                    <option value="0">ללא נציג</option>
                    <?php
					$AgentLoops = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('ActiveStatus', '=', '0')->get();
					foreach ($AgentLoops as $AgentLoop) {
					if ($PipeInfo->AgentId == $AgentLoop->id) {$DoSelected = 'selected';} else {$DoSelected = '';}
						echo '<option value="'.$AgentLoop->id.'" '.$DoSelected.'>'.$AgentLoop->display_name.'</option>';
					}
					?>
                    </select>
				<?php
				}
				else {
				echo @$AgentForThisLead->display_name;
				}
				?>
        		</td>
				</tr>
		</tbody>
	</table>  

        
<div class="ip-modal-footer">
<button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'>סגור</button> 
</div>


</div>

<script>

$('#ChooseAgents').on('change', function() {	 
var Id = this.value;
$.ajax({			   
           type: "POST",
           url: "new/ChooseAgents.php?PipeId=<?php echo @$PipeInfo->id; ?>&Id="+Id,
           success: function(dataN)
           {
		 $.notify(
			 {
			 icon: 'fas fa-thumbs-up',
			 message: 'שיוך לנציג בוצע בהצלחה',
                  
			 },{
			 type: 'success',
             z_index: 999999, 
		 });
           }
         });    

    
}); 

</script>

