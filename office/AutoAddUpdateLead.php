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
<link href="//cdn.datatables.net/buttons/1.4.2/css/buttons.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.0/css/responsive.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/buttons/1.4.2/css/buttons.bootstrap.min.css" rel="stylesheet">

<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
<script src="<?php echo asset_url('office/js/vendor/dataTables.bootstrap.js') ?>"></script>

<script src="https://cdn.datatables.net/responsive/2.2.0/js/dataTables.responsive.js"></script>


<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
<script>
$(document).ready(function(){
	
	 $('#categories tfoot th span').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'" style="width:100%;" class="form-control"  />' );

    } );
	

	
	$.fn.dataTable.moment = function ( format, locale ) {
    var types = $.fn.dataTable.ext.type;
 
    // Add type detection
    types.detect.unshift( function ( d ) {
        return moment( d, format, locale, true ).isValid() ?
            'moment-'+format :
            null;
    } );
 
    // Add sorting method - use an integer for the sorting
    types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
        return moment( d, format, locale, true ).unix();
    };
};
	
	 $.fn.dataTable.moment( 'd/m/Y H:i' );
	
	
	var categoriesDataTable;
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
   var categoriesDataTable =   $('#categories').dataTable( {
            language: BeePOS.options.datatables,
			responsive: true,
		    processing: true,
	        autoWidth: true,
	        "scrollY":        "450px",
            "scrollCollapse": true,
	   deferRender: true,
            "paging":         true,
	         fixedHeader: {
        headerOffset: 50
    },

	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 200,
	      dom: "Bfrtip",
		//info: true,
	    buttons: [
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			
           // 'pdfHtml5'
		
			
        ],
	//	order: [[0, 'DESC']]

	   	 	   
        } );
		
		    var table = $('#categories').DataTable();	
	
	
});


</script>



<div class="row">

<div class="col-md-6">
<div style="float:right;"></div>
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;"><div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; font-family:Tahoma; float:left;"><?php echo $day; ?> <span style="color:#e35623;"><?php echo $daynum;?></span> <?php echo $month;?>, <?php echo $year;?></div>
</h3> </div>


<div class="col-md-6">
<div style="float:left;"></div>
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; font-family:Tahoma; float:right;">ייבוא לידים</div></h3>
</div>


</div>

<?php
$Pull = @$_POST['Pull'];
$NumbersToUpdates = array_unique(preg_split('/\r\n|[\r\n]/', $Pull));
$StatusSet = @$_POST['Status'];



?>


<div class="alert alert-info" style="text-align:center; font-weight:bold; padding-top:10px" dir="rtl">

<form autocomplete="off" method="post">
    
<div class="form-group">
	<div style="text-align: right; padding-bottom: 10px;">נא הזן טלפונים מופרדים בשורה חדשה:</div>
    <textarea name="Pull" rows="10" class="form-control input-lg" required><?php echo @$Pull; ?></textarea>
  </div>
<div class="form-group">
    <select class="form-control input-lg" name="Status" required>
<?php
		$Statuss = DB::table('leadstatus')->get();
		foreach ($Statuss as $Status){ ?>
   <option value="<?php echo $Status->id; ?>"><?php echo $Status->Status; ?></option>
   <?php } ?>
    </select>
  </div>
    
    
    <button type="submit" class="btn btn-info btn-block btn-lg">בצע עדכון</button>
    
</form>    
    
    
</div>





<?php if (@$Pull != '') { ?>

<?php
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-refresh' aria-hidden='true'></i> ".$LogUserName." ביצע ייבוא / עדכון לידים";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	
?>

<div class="row" style="padding:10px;">

  
  <table class="table table-striped table-bordered table-hover table-dt display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th style="text-align: right;">#</th>
                <th style="text-align: right;">שם</th>
                <th style="text-align: right;">טלפון</th>
                <th style="text-align: right;">נציג מטפל</th>
                <th style="text-align: right;">סטטוס</th>
                <th style="text-align: right;">הערות</th>
			</tr>
		</thead>
      
		<tbody>
     
            <?php
	$i= '1';

foreach ($NumbersToUpdates as $NumbersToUpdate) {

$OldPhone = $NumbersToUpdate;    
    
if (substr($NumbersToUpdate, 0, 4) == "+972") {
   $NumbersToUpdate = '0'.substr($NumbersToUpdate, 4);
}
elseif (substr($NumbersToUpdate, 0, 3) == "972") {
   $NumbersToUpdate = '0'.substr($NumbersToUpdate, 3);
}

	
	
if ((substr($NumbersToUpdate, 0, 2) == "54") || (substr($NumbersToUpdate, 0, 2) == "52") || (substr($NumbersToUpdate, 0, 2) == "53") || (substr($NumbersToUpdate, 0, 2) == "50") || (substr($NumbersToUpdate, 0, 2) == "58") || (substr($NumbersToUpdate, 0, 2) == "55")) {
   $NumbersToUpdate = '0'.$NumbersToUpdate;
}
else {
   $NumbersToUpdate = $NumbersToUpdate;
}
	
	
$NumbersToUpdate = str_replace("-","",$NumbersToUpdate);
	
if((!preg_match('#[^0-9]#',$NumbersToUpdate)) && ($NumbersToUpdate != '') && (strlen((string)$NumbersToUpdate) >= '9')) {
    
    
	$CheckLeads = DB::table('leads')->where('Phone','=', $NumbersToUpdate)->Orwhere('Phone','=', $OldPhone)->first();
	@$SellerInfo = DB::table('users')->where('id', '=', @$CheckLeads->Seller)->first();  
	@$Statuss = DB::table('leadstatus')->where('id', '=', @$CheckLeads->Status)->first(); 
	@$OldStatusID = @$CheckLeads->Status;
	@$OldStatusName = @$Statuss->Status;
	
if (@$CheckLeads->id==''){ 
    
    // במידה והמספר לא קיים בלידים
	$CheckClients = DB::table('client')->where('ContactMobile','=', $NumbersToUpdate)->Orwhere('ContactMobile','=', $OldPhone)->first();  
    
    // תבדוק האם המספר קיים בלקוחות
    
	if (@$CheckClients->id==''){ 
        

        
		// להוסיף לוג של הוספה למערכת על ידי נציג x מייבוא
		// להוסיף לטבלת הלידים, להוסיף לטבלת הלקוחות
	  
        $time = date('Y-m-d G:i:s');
        $FixEmail = $NumbersToUpdate.'@sms.responder.co.il';	
        
        $CompanyNum = DB::table('client')->insertGetId(
        array('CompanyName' => $NumbersToUpdate, 'CompanyId' => $NumbersToUpdate, 'Email' => $FixEmail, 'Dates' => $time, 'ContactMobile' => $NumbersToUpdate,  'FirstName' => $NumbersToUpdate, 'LastName' => $NumbersToUpdate, 'Dob' => '0000-00-00', 'UserId' => Auth::user()->id ) );

        $AddLeads = DB::table('leads')->insertGetId(
        array('Email' => $FixEmail, 'Name' => $NumbersToUpdate, 'Phone' => $NumbersToUpdate, 'Dates' => $time, 'ClientId' => $CompanyNum, 'ItemId' => '1', 'Status' => $StatusSet) );     
				
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-refresh' aria-hidden='true'></i> ".$LogUserName." הקים לקוח וליד באמצעות ייבוא <a href='ClientProfile.php?u=".$CompanyNum."' target='_blank'>".$NumbersToUpdate." :: ".$CompanyNum."</a>";
DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => $CompanyNum));
//Log	
    
        
        
        // אם לא קיים גם בלידים וגם בלקוחות
     	$ClientInfo = DB::table('client')->where('id','=', $CompanyNum)->first(); 
        $LeadInfo = DB::table('lead')->where('id','=', $AddLeads)->first(); 
        
        @$ClientDB = @$ClientInfo;
		@$LeadDB = @$LeadInfo;
		$TaskNote1 = 'הלקוח והליד נוספו למערכת';
        
        
    }
    
	else {
        
		$TaskNote1 = 'נמצא לקוח ללא ליד, הליד נוסף למערכת';
        
		// להוסיף לוג של הוספה למערכת על ידי נציג x מייבוא
		// להוסיף לטבלת לידים ולקחת מכאן מספר לקוח קיים
        $time = date('Y-m-d G:i:s');
        
        $AddLeads = DB::table('leads')->insertGetId(
        array('Email' => $CheckClients->Email, 'Name' => $CheckClients->CompanyName, 'Phone' => $CheckClients->ContactMobile, 'Dates' => $time, 'ClientId' => $CheckClients->id, 'ItemId' => '1', 'Status' => $StatusSet) );    
       
        
        // אם לא קיים גם בלידים וגם בלקוחות
     	$ClientInfo = DB::table('client')->where('id','=', $CheckClients->id)->first(); 
        $LeadInfo = DB::table('lead')->where('id','=', $AddLeads)->first(); 
        
        @$ClientDB = @$ClientInfo;
		@$LeadDB = @$LeadInfo;
        
        
        
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-refresh' aria-hidden='true'></i> ".$LogUserName." הקים לקוח וליד באמצעות ייבוא <a href='ClientProfile.php?u=".$CheckClients->id."' target='_blank'>".$CheckClients->CompanyName." :: ".$CheckClients->id."</a>";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => $CheckClients->id));
//Log	
        
	}
}

else {
    
 $CheckClientsTwo = DB::table('client')->where('ContactMobile','=', $CheckLeads->Phone)->Orwhere('ContactMobile','=', $OldPhone)->first(); 
     
 if (@$CheckClientsTwo->id==''){
     

		$TaskNote1 = 'הלקוח נוסף למערכת';
     
		// להוסיף לוג של הוספה למערכת על ידי נציג x מייבוא
		// להוסיף לטבלת לידים ולקחת מכאן מספר לקוח קיים
     
     
        $time = date('Y-m-d G:i:s');
        $FixEmail = $NumbersToUpdate.'@sms.responder.co.il';	
        
        $CompanyNum = DB::table('client')->insertGetId(
        array('CompanyName' => $NumbersToUpdate, 'CompanyId' => $NumbersToUpdate, 'Email' => $FixEmail, 'Dates' => $time, 'ContactMobile' => $NumbersToUpdate,  'FirstName' => $NumbersToUpdate, 'LastName' => $NumbersToUpdate, 'Dob' => '0000-00-00', 'UserId' => Auth::user()->id ) );
     
     
     // אם לא קיים גם בלידים וגם בלקוחות
     	$ClientInfo = DB::table('client')->where('id','=', $CompanyNum)->first(); 
        $LeadInfo = DB::table('lead')->where('id','=', $CheckLeads->id)->first(); 
        
        @$ClientDB = @$ClientInfo;
		@$LeadDB = @$LeadInfo;
     
     
      if ((@$OldStatusID == @$StatusSet) || (@$OldStatusID == '18') || (@$OldStatusID == '6') || (@$OldStatusID == '7') || (@$OldStatusID == '9') || (@$OldStatusID == '19') || (@$OldStatusID == '22') || (@$OldStatusID == '23') || (@$OldStatusID == '24')) {
	  $TaskNote1 = 'ללא שינוי';
     
          DB::table('leads')
           ->where('id', $CheckLeads->id)
           ->update(array('ClientId' => $CompanyNum));  
          
        //Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-refresh' aria-hidden='true'></i> ".$LogUserName." שייך לקוח לליד באמצעות ייבוא <a href='ClientProfile.php?u=".$CompanyNum."' target='_blank'>".$NumbersToUpdate." :: ".$CompanyNum."</a>";
DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => $CompanyNum));
//Log	  
          
	  }
     
      else {  
          
      $TaskNote1 = 'סטטוס קודם: <u>'.@$OldStatusName.'</u>'; 
          
       DB::table('leads')
           ->where('id', $CheckLeads->id)
           ->update(array('ClientId' => $CompanyNum, 'Status' => $StatusSet)); 
       
          
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-refresh' aria-hidden='true'></i> ".$LogUserName." שייך לקוח לליד באמצעות ייבוא <a href='ClientProfile.php?u=".$CompanyNum."' target='_blank'>".$NumbersToUpdate." :: ".$CompanyNum."</a>";
DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => $CompanyNum));
//Log	
	@$Statusseee = DB::table('leadstatus')->where('id', '=', @$StatusSet)->first(); 

//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-refresh' aria-hidden='true'></i> ".$LogUserName." החליף סטטוס באמצעות ייבוא לליד <a href='ClientProfile.php?u=".$ClientId."' target='_blank'>".$NumbersToUpdate." :: ".$ClientId."</a>. סטטוס קודם: <u>".$OldStatusName."</u>, סטטוס חדש: <u>".$Statusseee->Status."</u>";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => $ClientId));
//Log	
      
          
	  }
     
     
     
          

     
 }
 else {     
     
     
     // אם לא קיים גם בלידים וגם בלקוחות
     	$ClientInfo = DB::table('client')->where('id','=', $CheckLeads->ClientId)->first(); 
        $LeadInfo = DB::table('lead')->where('id','=', $CheckLeads->id)->first(); 
     
        @$ClientId = $ClientInfo->id; 
        @$ClientDB = @$ClientInfo;
		@$LeadDB = @$LeadInfo;
     
     
     
	 if ((@$OldStatusID == @$StatusSet) || (@$OldStatusID == '18') || (@$OldStatusID == '6') || (@$OldStatusID == '7') || (@$OldStatusID == '9') || (@$OldStatusID == '19') || (@$OldStatusID == '22') || (@$OldStatusID == '23') || (@$OldStatusID == '24')) {
	 $TaskNote1 = 'ללא שינוי';
	 }
     else {  
		 
	 $TaskNote1 = 'סטטוס קודם: <u>'.@$OldStatusName.'</u>';
         
     DB::table('leads')
           ->where('id', $CheckLeads->id)
           ->update(array('Status' => $StatusSet));      
	 @$LeadDB = @$CheckLeads;
	@$Statusseee = DB::table('leadstatus')->where('id', '=', @$StatusSet)->first(); 

//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-refresh' aria-hidden='true'></i> ".$LogUserName." החליף סטטוס באמצעות ייבוא לליד <a href='ClientProfile.php?u=".$ClientId."' target='_blank'>".$NumbersToUpdate." :: ".$ClientId."</a>. סטטוס קודם: <u>".$OldStatusName."</u>, סטטוס חדש: <u>".$Statusseee->Status."</u>";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => $ClientId));
//Log	
         
         
         
	 }
	 // לעדכן מספר לקוח בטבלת לידים
 }

	 
}

	

		?>
           
           
            <?php
		    $CheckLeadsNEW = DB::table('leads')->where('ClientId','=', @$ClientDB->id)->first();
			?>
            <tr>
            <td style="text-align: center;">
			<?php echo $i; ?><br /><a href="javascript:ViewCallsLog('<?php echo @$ClientDB->id; ?>');"><i class='fa fa-archive' aria-hidden='true'></i></a> <a href="javascript:ViewTaskLog('<?php echo @$ClientDB->id; ?>');"><i class='fa fa-calendar' aria-hidden='true'></i></a> <a href="javascript:ViewInfoLog('<?php echo @$ClientDB->id; ?>');"><i class='fa fa-info-circle' aria-hidden='true'></i></a> <a href="javascript:ViewLeadLog('<?php echo @$ClientDB->id; ?>');"><i class='fa fa-hand-pointer-o' aria-hidden='true'></i></a>
		    </td>
            <td><a href="ClientProfile.php?u=<?php echo @$ClientDB->id; ?>"><strong><?php echo htmlentities(@$ClientDB->CompanyName); ?></strong></a></td>
            <td><?php echo @$NumbersToUpdate; ?></td>
            <td><?php if (Auth::user()->role_id == "1") { ?><a href="javascript:UpdateSaller('<?php echo @$LeadDB->id; ?>');"><i class="fa fa-cog" aria-hidden="true"></i> </a><?php } ?><?php echo @$SellerInfo->display_name; ?></td>
            <td><select name="TypeStatus" id="TypeStatus" class="form-control" onchange="func(this.value)" > <?php @$Statuss = DB::table('leadstatus')->get();foreach (@$Statuss as $Status){?><option value="<?php echo $Status->id; ?>:<?php echo @$LeadDB->id; ?>" <?php if ((@$Status->id)==(@$CheckLeadsNEW->Status)){ ?>selected <?php } else {} ?>><?php echo @$Status->Status; ?></option><?php } ?></select><span style="font-size: 1px; color: white;"><?php echo @$CheckLeadsNEW->Status; ?></span></td>
            <td><?php echo @$TaskNote1; ?></td>
            </tr>
		<?php
	
	
  $i++;	  
} else {}

}
?>
            
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
<?php } else {

//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-cloud-upload' aria-hidden='true'></i> ".$LogUserName." נכנס לייבוא לידים";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log
}
?>

<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>