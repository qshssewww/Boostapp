<?php require_once '../app/init.php'; 

if (Auth::guest()) redirect_to(App::url());

?>


<?php echo View::make('headernew')->render() ?>

<?php if (Auth::check()):?>
<?php

$day = date("l");

$daynum = date("j");

$month = date("F");

$year = date("Y");
	
if($day == "Monday"){

$day = "שני";

}elseif($day == "Tuesday"){

$day = "שלישי";

}elseif($day == "Wednesday"){

$day = "רביעי";

}elseif($day == "Thursday"){

$day = "חמישי";

}elseif($day == "Friday"){

$day = "שישי";

}elseif($day == "Saturday"){

$day = "שבת";

}elseif($day == "Sunday"){

$day = "ראשון";

}



if($month == "January"){

$month = "ינואר";

}elseif($month == "February"){

$month = "פברואר";

}elseif($month == "March"){

$month = "מרץ";

}elseif($month == "April"){

$month = "אפריל";

}elseif($month == "May"){

$month = "מאי";

}elseif($month == "June"){

$month = "יוני";

}elseif($month == "July"){

$month = "יולי";

}elseif($month == "August"){

$month = "אוגוסט";

}elseif($month == "September"){

$month = "ספטמבר";

}elseif($month == "October"){

$month = "אוקטובר";

}elseif($month == "November"){

$month = "נובמבר";

}elseif($month == "December"){

$month = "דצמבר";

}


?>


<link href="<?php echo asset_url('office/css/vendor/dataTables.bootstrap.css') ?>" rel="stylesheet">
<script src="<?php echo asset_url('office/js/vendor/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo asset_url('office/js/vendor/dataTables.bootstrap.js') ?>"></script>



<div class="row">

<div class="col-md-6">
<div style="float:right;"></div>
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;"><div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; font-family:Tahoma; float:left;"><?php echo $day; ?> <span style="color:#e35623;"><?php echo $daynum;?></span> <?php echo $month;?>, <?php echo $year;?></div>
</h3> </div>


<div class="col-md-6">
<div style="float:left;"></div>
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; font-family:Tahoma; float:right;">חיפוש לידים</div></h3>
</div>


</div>

<?php

$Search = @$_GET['Search'];

$OpenTables = DB::table('leads')->where('Email', '=', $Search)->Orwhere('Phone', '=', $Search)->orderBy('Dates', 'ASC')->groupBy('Phone')->get();



//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
if (@$Search != '') {$SearchRequest = " :: <a href='SearchLead.php?Search=".@$Search."' target='_blank'>".@$Search."</a>";}
$LogContent = "<i class='fa fa-search' aria-hidden='true'></i> ".$LogUserName." נכנס לחיפוש ליד".@$SearchRequest;
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	

?>


<div class="alert alert-info" style="text-align:center; font-weight:bold; padding-top:10px" dir="rtl">

<form autocomplete="off">
    
<div class="form-group">
    <input type="text" name="Search" id="Search" class="form-control input-lg" required="required" value="<?php echo $Search;?>" placeholder="חפש לפי טלפון / דואר אלקטרוני"  />
  </div>
    
    
    <button type="submit" class="btn btn-info btn-block btn-lg">חפש</button>
    
</form>    
    
    
</div>






<div class="row" style="padding:10px;">
    <div id="updatestatustext" class="alert alert-warning" dir="rtl" style="margin-top:20px; display:none;">
 אנא המתן בזמן עיבוד הנתונים...
</div>

  
  <table class="table table-striped table-bordered table-hover table-dt display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th style="text-align: right;">מספר לקוח</th>
                <th style="text-align: right;">שם לקוח</th>

				<th style="text-align: right;">טלפון</th>
				<th style="text-align: right;">דוא"ל</th>
                <th style="text-align: right;">ת.הצטרפות</th>

                <th style="text-align: right;">נציג מכירות</th>
                
               <th style="text-align: right;">סטטוס</th>
			</tr>
		</thead>
      
		<tbody>
     
     <?php
$i=1;	
foreach($OpenTables as $Client){ 

$ClientInfo = DB::table('client')->where('id', '=', $Client->ClientId)->first();    
@$ManagerInfo = DB::table('users')->where('id', '=', $Client->Manager)->first();    
@$SellerInfo = DB::table('users')->where('id', '=', $Client->Seller)->first();    
    
if (@$ClientInfo->Status=='0'){
$MemberShipText = '<SPAN class=\"text-success\"><strong>פעיל</strong></SPAN>';    
}   
else {
$MemberShipText = '<SPAN class=\"text-danger\"><strong>מוקפא</strong></SPAN>';     
}  
 
$StatusInfo = DB::table('leadstatus')->where('id', '=', $Client->Status)->first();    
    
?>        
            
            <tr>
            <td>
			<?php echo $ClientInfo->id; ?> <a href="javascript:ViewCallsLog('<?php echo $ClientInfo->id; ?>');"><i class='fa fa-archive' aria-hidden='true'></i></a> <a href="javascript:ViewTaskLog('<?php echo $ClientInfo->id; ?>');"><i class='fa fa-calendar' aria-hidden='true'></i></a> <a href="javascript:ViewInfoLog('<?php echo $ClientInfo->id; ?>');"><i class='fa fa-info-circle' aria-hidden='true'></i></a> <a href="javascript:ViewLeadLog('<?php echo $ClientInfo->id; ?>');"><i class='fa fa-hand-pointer-o' aria-hidden='true'></i></a>
		    </td>
            <td><a href="ClientProfile.php?u=<?php echo $ClientInfo->id; ?>"><strong><?php echo htmlentities($ClientInfo->CompanyName); ?></strong></a></td>
            <td><?php echo @$ClientInfo->ContactMobile; ?></td>
            <td><?php echo @$ClientInfo->Email; ?></td>
            <td><?php echo with(new DateTime($ClientInfo->Dates))->format('d/m/Y H:i'); ?></td>
            <td><?php if (Auth::user()->role_id == "1") { ?><a href="javascript:UpdateSaller('<?php echo $Client->id; ?>');"><i class="fa fa-cog" aria-hidden="true"></i> </a><?php } ?><?php echo @$SellerInfo->display_name; ?></td>
            <td><select name="TypeStatus" id="TypeStatus" class="form-control" onchange="func(this.value)" > <?php $Statuss = DB::table('leadstatus')->get();foreach ($Statuss as $Status){?><option value="<?php echo $Status->id; ?>:<?php echo $Client->id; ?>" <?php if ($Status->id==$Client->Status){ ?>selected <?php } else {} ?>><?php echo $Status->Status; ?></option><?php } ?></select></td>    
            </tr>
            
  <?php } ?>          
            
        </tbody>
	
	
        </table>                                                                       
                                                        
                                                 
</div>



<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="SallerEditPopup" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">הגדרת נציג מכירות</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="EditSaller"  class="ajax-form clearfix">
<input type="hidden" name="SallerId">
<div id="resultSaller">


  
</div>

				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-success"><?php _e('main.save_changes') ?></button>
                </div>
                
				<button type="button" class="btn btn-default ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->


<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="ViewCallsLogPOPUP" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">תיעוד שיחות</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">

					
					
<form action="AddCRMPP"  class="ajax-form clearfix">
 <input type="hidden" name="ClientId">
<div id="resultViewCallsLog">


  
</div>

				</div>
				<div class="ip-modal-footer">
                
				<button type="button" class="btn btn-default ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>
					
					
					
				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->





<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="ViewTaskLogPOPUP" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">משימות מתוזמנות ללקוח</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="AddReminderPP"  class="ajax-form clearfix">
 <input type="hidden" name="ClientId">
<div id="resultViewTaskLog">


  
</div>

				</div>
				<div class="ip-modal-footer">
                
				<button type="button" class="btn btn-default ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>

				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="ViewInfoLogPOPUP" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">מידע כללי</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<div id="resultViewInfoLog">


  
</div>

				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->





<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="ViewLeadLogPOPUP" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">לוג לקוח</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<div id="resultViewLeadLog">


  
</div>

				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<script>

    
function func(selectedValue)
 {
    //make the ajax call
    $.ajax({
        url: 'action/SaveStatus.php',
        type: 'POST',
        data: {option : selectedValue},
        success: function() {
            console.log("Data sent!");
			$("#updatestatustext").show();
			setTimeout(function() {$('#updatestatustext').fadeOut('fast');}, 1000); 
        }
    });
}    
    
</script>


<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>