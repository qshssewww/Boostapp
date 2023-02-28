<?php
require_once '../app/init.php';
if (Auth::guest()) redirect_to(App::url());

?>


<?php echo View::make('headernew')->render() ?>

<?php if (Auth::check()):?>

<?php



if (!isset($_REQUEST["DatesFrom"])) $_REQUEST["DatesFrom"] = date("Y-m-d");
if (!isset($_REQUEST["DatesTo"])) $_REQUEST["DatesTo"] = date("Y-m-d");
if (!isset($_REQUEST["Type"])) $_REQUEST["Type"] = '0';

$DatesFrom = $_REQUEST["DatesFrom"];
$DatesTo = $_REQUEST["DatesTo"];
$DatesToNew = date('Y-m-d', strtotime($DatesTo .'+1 day'));
$NewDay = date('l', strtotime($DatesTo));

$Type = $_REQUEST["Type"];

$next_day = date('Y-m-d', strtotime($DatesFrom .'+1 day'));

$prev_day = date('Y-m-d', strtotime($DatesFrom. '-1 day'));





	



if ((@$_GET['MDash'] == '1') && (Auth::user()->role_id == "1")) {
$Reminders = DB::table('clientreminder')->whereBetween('ReminderDate', array($DatesFrom, $DatesToNew))->orderBy('Status', 'ASC')->orderBy('ReminderDate', 'ASC')->orderBy('ReminderTime', 'ASC')->get();
}
else {
$useridtosearch = Auth::user()->id;
$Reminders = DB::table('clientreminder')->where('User', '=', Auth::user()->id)->whereBetween('ReminderDate', array($DatesFrom, $DatesToNew))->orderBy('Status', 'ASC')->orderBy('ReminderDate', 'ASC')->orderBy('ReminderTime', 'ASC')->get();
}
	











/// דוח X קופת חנות
//$Reminders = DB::select($Type1);
$StoreCount = count($Reminders);



?>




<link href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-beta/css/bootstrap.css" rel="stylesheet">
<link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">

<link href="//cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css" rel="stylesheet">

<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">


<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">

<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>

<link href="assets/css/fixstyle.css" rel="stylesheet">

<script type="text/javascript" charset="utf-8">     

$(document).ready(function(){
	
$('#DatesFrom').on('change', function(){
    $('#DatesTo').attr('min', $('#DatesFrom').val());
	$('#DatesTo').val($('#DatesFrom').val());
});

$('#DatesTo').on('change', function(){
    $('#DatesTo').attr('min', $('#DatesFrom').val());
});

});

</script>

<div class="row">

<div class="col-md-6"><div style="float:right; padding-top:4px; color:#666; font-size:22px; font-weight:bold;">סה"כ <span style="color:#0074A4;"><?php echo $StoreCount;?></span> משימות</div>
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;"><div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px;  float:left;"><?php echo $day; ?> <span style="color:#e35623;"><?php echo $daynum;?></span> <?php echo $month;?>, <?php echo $year;?></div>
</h3> </div>


<div class="col-md-6">
<div style="float:left;">
<?php if (Auth::user()->role_id == "1") { ?>
	
<?php if (@$_GET['MDash'] == '1') { ?>
<?php
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-tasks' aria-hidden='true'></i> ".$LogUserName." נכנס לצפייה בכל ה<a href='ReminderReportsClient.php?MDash=1' target='_blank'>משימות המתוזמנות <u>כמנהל</u></a> ";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	
?>
<?php $ManagerAddonQM = '?Manage=Me';	$ManagerAddonAND = '&Manage=Me'; ?>
<a href="ReminderReportsClient.php"  class="btn btn-info">הצג בתור נציג</a>
<?php } else { ?>
<?php
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-tasks' aria-hidden='true'></i> ".$LogUserName." נכנס לצפייה בכל ה<a href='ReminderReportsClient.php' target='_blank'>משימות המתוזמנות</a> ";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	
?>
<a href="ReminderReportsClient.php?MDash=1"  class="btn btn-info">הצג בתור מנהל</a>
<?php } ?>

<?php 
}
else {
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-tasks' aria-hidden='true'></i> ".$LogUserName." נכנס לצפייה בכל ה<a href='ReminderReportsClient.php' target='_blank'>משימות המתוזמנות</a> ";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	
}
?></div>
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;"><?php if ((@$_GET['MDash'] == '1') && (Auth::user()->role_id == "1")) {echo "<span style='color:red;'>ניהול</span> :: ";} ?> משימות מתוזמנות</div></h3>
</div>



</div>

<div class="row" dir="rtl" >
	
	
<div class="col-md-6 col-sm-12">	
<form class="form-inline text-right">

    <label>מתאריך: &nbsp; </label>
    <input type="date" name="DatesFrom" id="DatesFrom" class="form-control text-right" required="required" value="<?php echo $DatesFrom;?>" />&nbsp;&nbsp;


    <label>עד תאריך: &nbsp;</label>
    <input type="date" name="DatesTo" id="DatesTo" class="form-control text-right" min="<?php echo @$DatesFrom;?>"  required="required" value="<?php echo @$DatesTo;?>" /> 

	

  <input type="hidden" name="MDash" value="<?php echo @$_GET['MDash']; ?>" />
	
  <button type="submit" class="btn btn-dark NewBlock text-white">שלח</button>
	
</form>	
</div>		
	
<div class="col-md-6 col-sm-12">
<span class="float-left pr-1"> <a href="<?php echo $_SERVER["PHP_SELF"] . "?DatesFrom=". $next_day."&DatesTo=". $next_day; ?>&MDash=<?php echo @$_GET['MDash']; ?>"  class="btn btn-dark text-white">ליום הבא</a></span>

<span  class="float-left pr-1"> <a href="<?php echo $_SERVER["PHP_SELF"] . "?DatesFrom=". $prev_day."&DatesTo=". $prev_day; ?>&MDash=<?php echo @$_GET['MDash']; ?>"  class="btn btn-dark text-white">ליום הקודם</a></span>
                            
<span  class="float-left pr-1"> <a href="ReminderReportsClient.php?MDash=<?php echo @$_GET['MDash']; ?>"  class="btn btn-info text-white">היום</a></span> 

</div>	
	
	

	
	
</div>



<hr>

<div class="row">
	
<div class="col-md-12 col-sm-12">
	
<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="ActiveClient" dir="rtl"  cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right" scope="col">#</th>
				<th class="text-right" scope="col">פעולות</th>
				<th class="text-right" scope="col">לקוח</th>
				<th class="text-right" scope="col">התראה</th>
                <th class="text-right" scope="col">תאריך</th>
                <th class="text-right" scope="col">שעה</th>
                <th class="text-right" scope="col">התווסף</th>
                <th class="text-right" scope="col">נסגר</th>  
                <th class="text-right lastborder" scope="col">סטטוס</th>                
			</tr>
		</thead>
		<tbody>
<?php 
			
$t = '1';			 
foreach ($Reminders as $Reminder) {	

$UserIfo = DB::table('users')->where('id', '=', $Reminder->User)->first(); 
$UserIfo2 = DB::table('users')->where('id', '=', @$Reminder->CloseUser)->first(); 	

if ($Reminder->Status=='0')	{
$Color = 'class="warning"';
}
else {
$Color = '';	
}	

$Client = DB::table('client')->where('id', '=', $Reminder->ClientId)->first(); 	
	
?>        
        <tr <?php echo $Color; ?>>
        <td><?php echo $t; ?></td>
		<td width="120">
		<a href="javascript:ViewCallsLog('<?php echo $Client->id; ?>');" data-toggle="tooltip" data-placement="top" title="תיעוד שיחות"><i class='fas fa-sticky-note fa-lg' aria-hidden='true'></i></a>
		<a href="javascript:ViewTaskLog('<?php echo $Client->id; ?>');" data-toggle="tooltip" data-placement="top" title="משימות"><i class='fas fa-calendar-check fa-lg' aria-hidden='true'></i></a>
		<a href="javascript:ViewInfoLog('<?php echo $Client->id; ?>');" data-toggle="tooltip" data-placement="top" title="מידע כללי"><i class='fas fa-info-circle fa-lg' aria-hidden='true'></i></a>
		<a href="javascript:ViewLeadLog('<?php echo $Client->id; ?>');" data-toggle="tooltip" data-placement="top" title="לוג"><i class="fas fa-street-view fa-lg"></i></a></td>	
        <td><a href="ClientProfile.php?u=<?php echo $Client->id; ?>" name="widget2" data-toggle="tooltip" data-placement="top" title="נהל לקוח"><strong><?php echo $Client->CompanyName; ?></strong></a>
		</td>
		<td><?php echo $Reminder->Remarks; ?></td>
		<td><?php echo with(new DateTime($Reminder->ReminderDate))->format('d/m/Y'); ?></td>
		<td><?php echo with(new DateTime($Reminder->ReminderTime))->format('H:i'); ?></td>
		<td><?php echo $UserIfo->display_name; ?>, בתאריך: <?php echo with(new DateTime($Reminder->Dates))->format('d/m/Y H:i'); ?></td>
        <td><?php if (@$UserIfo2->display_name==''){} else { echo @$UserIfo2->display_name; ?>, בתאריך: <?php echo with(new DateTime($Reminder->CloseDate))->format('d/m/Y H:i'); } ?></td>
               
        <td class="lastborder"><?php if ($Reminder->Status=='0') { echo 'פתוח'; } else { echo 'סגור'; } ?></td>
        </tr>
        
<?php 

++ $t; } ?>  

     
 </tbody>




	</table>  

	</div>





</div>





<!-- Edit Task -->
	<div class="ip-modal text-right" id="ReminderAddModals">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" style="float:left;" data-dismiss='modal'>&times;</a>
				<h4 class="ip-modal-title">הוספת התראה חדשה</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
  
  
  <div style="height: 490px; overflow-y: scroll; overflow-x:hidden; padding:10px;">
  
                <form action="ReminderNewAdd"  class="ajax-form clearfix">
   
                
                <div class="form-group" dir="rtl">
                <label>תוכן התראה</label>
                <textarea name="Remarks" class="form-control" rows="3" dir="rtl" required></textarea>
                </div>  

              
                <div class="form-group" dir="rtl">
                <label>טלפון</label>
                <input type="text" name="Mobile" class="form-control" value="">
                </div>  
              
               <div class="form-group" dir="rtl">
                <label>תאריך ההתראה</label>
                <input type="date" name="ReminderDate" class="form-control" value="<?php echo date('Y-m-d') ?>" required min="<?php echo date('Y-m-d') ?>">
                </div>  
                  
                   
                <div class="form-group" dir="rtl">
                <label>שעת ההתראה</label>
                <input type="time" name="ReminderTime" class="form-control" value="<?php echo date('H:i') ?>" required>
                </div>       
               
                <div class="form-group" dir="rtl">
                <label>עדיפות</label>
                <select name="Level" class="form-control">
                <option value="0">רגילה</option>
                <option value="1">בינונית</option>
                <option value="2">גבוהה</option>
                </select>
                </div>  
                
                <div class="form-group" dir="rtl">
                <label>הצג התראה ל-</label>
                <select name="ShowLevel" class="form-control" required>
                <option value="">בחר</option>
                <option value="0" selected>כולם</option>
                
                <?php
	              $Users = DB::table('users')->get();
			 
			     foreach ($Users as $User) {
	             ?>
                <option value="<?php echo $User->id; ?>"><?php echo $User->display_name; ?> </option>
                <?php
			 }
	             ?>
               
               
                </select>
                </div>  
                
	  </div>
  
  
				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-success"><?php _e('main.save_changes') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?php _e('main.close') ?></button>
  
				</div>
				</form>
			</div>
		</div>
	</div>
	<!-- end Edit Task -->



<!-- Edit DepartmentsPopup -->
	<div class="ip-modal text-right" id="NotificationsEditPopup" tabindex="-1">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">עריכת התראה</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="EditNotifications"  class="ajax-form clearfix">
<input type="hidden" name="NotificationsId">
<div id="result">


  
</div>

				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-info text-white"><?php _e('main.save_changes') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->
<!-- Edit DepartmentsPopup -->
	<div class="ip-modal text-right" id="ViewCallsLogPOPUP" tabindex="-1">
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
                
				<button type="button" class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>
					
					
					
				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->





<!-- Edit DepartmentsPopup -->
	<div class="ip-modal text-right" id="ViewTaskLogPOPUP" tabindex="-1">
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
                
				<button type="button" class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>

				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<!-- Edit DepartmentsPopup -->
	<div class="ip-modal text-right" id="ViewInfoLogPOPUP" tabindex="-1">
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
	<div class="ip-modal text-right" id="ViewLeadLogPOPUP" tabindex="-1">
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
$(document).ready(function(){
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
    $('#ActiveClient').dataTable( {
            language: BeePOS.options.datatables,
        pageLength: 100,
		 responsive: true,
        } );
});

</script>


<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>



<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>