<?php require_once '../app/init.php';?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>
<?php if (Auth::user()->role_id == '1'): ?>

<style>
.card-header {
    cursor: pointer;
}	
</style>
<link href="<?php echo asset_url('css/vendor/imgpicker.css') ?>" rel="stylesheet">
<link href="assets/css/fixstyle.css" rel="stylesheet">

<!-- include summernote css/js -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>


<link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">



<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>

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
	
	
	 $.fn.dataTable.moment( 'd/m/Y H:i' );
	
	
	var categoriesDataTable;
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
   var categoriesDataTable =   $('#categories').dataTable( {
            language: BeePOS.options.datatables,
			responsive: true,
		    processing: true,
            "paging":         true,
	        pageLength: 100,
	      dom: "Bfrtip",
	    buttons: [
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'רשימת חברות במערכת', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'רשימת חברות במערכת' , className: 'btn btn-dark'},
        ],
        } );
    
    var categoriesDataTable;
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
   var categoriesDataTable =   $('#faq').dataTable( {
            language: BeePOS.options.datatables,
			responsive: true,
		    processing: true,
            "paging":         true,
	        pageLength: 100,
	      dom: "Bfrtip",
	    buttons: [
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'רשימת שאלות תשובות', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'רשימת שאלות תשובות' , className: 'btn btn-dark'},
        ],
        } );
    
    
    
});


</script>


<div class="col-md-12 col-sm-12">


<div class="row pb-3">

<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-lock fa-fw"></i> ניהול מערכת
</div>
</h3>
</div>



</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active" aria-current="page">ניהול מערכת</li>
  </ol>  
</nav>    

<div class="row" dir="rtl">


	
	
	
	<div class="col-md-2 col-sm-12 order-md-2" dir="rtl">

	<div class="card" style="margin-bottom: 20px;">
  <a data-toggle="collapse" href="#MenuSettingSystem" aria-expanded="true" aria-controls="MenuSettingSystem" style="color: black;">
  <div class="card-header text-right">
    <strong><i class="fas fa-lock fa-fw"></i> ניהול מערכת</strong>
  </div>
  </a>
  
  <div class="collapse show" id="MenuSettingSystem">
  <div class="card-body">
<div class="nav nav-tabs flex-column nav-pills text-right" id="v-pills-tab" role="tablist" aria-orientation="vertical">
  <a class="nav-link text-dark active"  data-toggle="pill" href="#Dash" role="tab" aria-controls="v-pills-overview" aria-selected="true">לוח בקרה</a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#Companys" role="tab" aria-controls="v-pills-overview" aria-selected="true">חברות</a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#NewCompany" role="tab" aria-controls="v-pills-overview" aria-selected="true">הקמת חברה</a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#FAQ" role="tab" aria-controls="v-pills-overview" aria-selected="true">FAQ</a>    

</div>      
  </div>
	</div></div>
	</div>
	
	
	
	
	
	
	
	
	
	
	<div class="col-md-10 col-sm-12 order-md-2">

	
	
	
	

<div class="tab-content">
  <div class="tab-pane fade show active text-right" role="tabpanel" id="Dash">
  <div class="card spacebottom">
			<div class="card-header text-right"><strong>לוח בקרה</strong></div>    
 			<div class="card-body">  
 			    כמה חברות פעילות
 			    <br>
 			    כמה משתמשים פעילים
 			    <br>
 			    
 			    
				</div></div></div>
				
				
				
				
				
  <div class="tab-pane fade text-right" role="tabpanel" id="Companys">
            <div class="card spacebottom">
			<div class="card-header text-right"><strong>חברות</strong></div>    
 			<div class="card-body">  
 			    
<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right">שם החברה</th>
                <th class="text-right">שם מסחרי</th>
                <th class="text-right">שם לקוח</th>
                <th class="text-right">מספר חברה</th>
                <th class="text-right">טלפון נייד</th>
                <th class="text-right">לקוחות פעילים</th>
                <th class="text-right">מסמכים</th>
                <th class="text-right">שיבוצים</th>
                <th class="text-right">ה.קבע באשראי</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$Companys = DB::table('settings')->where('CompanyNum', '!=', '100')->where('Status', '=', '0')->get();
			foreach ($Companys as $Company) {
			$UsersByCompany = DB::table('client')->where('CompanyNum', '=', $Company->CompanyNum)->count();
			$UsersByCompanyA = DB::table('client')->where('Status', '=', '0')->where('CompanyNum', '=', $Company->CompanyNum)->count();
                
            $DocsIconCount = DB::table('docs')->where('CompanyNum', '=', $Company->CompanyNum)->where('UserDate', '>=', date('Y-m-01'))->count();
            $ClassIconCount = DB::table('classstudio_act')->where('CompanyNum', '=', $Company->CompanyNum)->where('ClassDate', '>=', date('Y-m-d'))->count();    
               
            $GetClientId = DB::table('247softnew.client')->where('FixCompanyNum', '=', $Company->CompanyNum)->first();     
                
            $KevaIconCount = DB::table('247softnew.paytoken')->where('ClientId', '=', @$GetClientId->id)->where('Status', '=', '0')->where('ItemId', '=', '2')->count();    
                
                
            if ($DocsIconCount>='1'){
            $DocsIcon = '<i class="fas fa-check"></i> כן';    
            }   
            else {
            $DocsIcon = '<i class="fas fa-times"></i> לא';     
            }
                
            if ($ClassIconCount>='1'){
            $ClassIcon = '<i class="fas fa-check"></i> כן';     
            }   
            else {
            $ClassIcon = '<i class="fas fa-times"></i> לא';     
            } 
                
            if ($KevaIconCount>='1'){
            $KevaIcon = '<i class="fas fa-check"></i> כן';     
            }   
            else {
            $KevaIcon = '<i class="fas fa-times text-danger"></i> לא';     
            }     
                
		?>
			<tr>
				<td class="text-right"><?php echo $Company->CompanyName; ?></td>
                <td class="text-right"><?php echo $Company->AppName; ?></td>
                <td class="text-right"><?php echo $Company->ClientName; ?></td>
				<td class="text-right"><?php echo $Company->CompanyId; ?></td>
				<td class="text-right"><?php echo $Company->ContactMobile; ?></td>
				<td class="text-right"><?php echo $UsersByCompanyA; ?></td>
                <td class="text-right"><?php echo $DocsIcon; ?></td>
                <td class="text-right"><?php echo $ClassIcon; ?></td>
                <td class="text-right"><?php echo $KevaIcon; ?></td>
			</tr>

       <?php } ?>
            
  
        </tbody>
	
     <tfoot>

         
         <?php
         $TotalByCompany = DB::table('client')->where('CompanyNum', '!=', '100')->count();
		 $TotalByCompanyA = DB::table('client')->where('Status', '=', '0')->where('CompanyNum', '!=', '100')->count();
         ?>
         
            <tr>
                <th colspan="5" style="text-align:right">סה"כ</th>
                <th><?php echo $TotalByCompanyA; ?></th>
            </tr>
        </tfoot>
    
    
	
        </table> 			    
		</div></div></div>
	  
	  
    
    
     <div class="tab-pane fade text-right" role="tabpanel" id="FAQ">
            <div class="card spacebottom">
			<div class="card-header text-right"><strong>FAQ - ניהול שאלות תשובות</strong></div>    
 			<div class="card-body">  
 			 
<div class="col-md-12 col-sm-12 order-md-2 pb-1 text-center">
<a href="#" data-ip-modal="#FAQPopup" class="btn btn-dark" name="Items"  dir="rtl"><i class="fas fa-plus-circle fa-fw"></i> הוסף שאלה חדשה</a>
</div>    
                
                
<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="faq" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right">#</th>
                <th class="text-right">שאלה</th>
                <th class="text-right">תשובה</th>
                <th class="text-right">סטטוס</th>
                <th class="text-right">פעולות</th>
			</tr>
		</thead>
		<tbody>
		<?php
            $i = '1';
			$Faqs = DB::table('faq')->orderBy('Status', 'ASC')->get();
			foreach ($Faqs as $Faq) {
                
            if ($Faq->Status=='0'){
            $FaqStatus = 'מוצג';    
            }    
            else {
            $FaqStatus = 'מוסתר';     
            }    
		?>
			<tr>
				<td class="text-right"><?php echo $i; ?></td>
				<td class="text-right"><?php echo $Faq->Question; ?></td>
				<td class="text-right"><?php echo $Faq->Answer; ?></td>
                <td class="text-right"><?php echo $FaqStatus; ?></td>
				<td class="text-right"><a class="btn btn-success btn-sm" style="color: #FFFFFF !important;" href='javascript:UpdateFAQ("<?php echo $Faq->id; ?>");'>ערוך שאלה</a></td>
			</tr>

       <?php ++$i; } ?>
            
  
        </tbody>

        </table> 			    
		</div></div></div> 
    
    
  <div class="ip-modal" id="FAQPopup">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">הוספת שאלה חדשה</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
                
                <form action="AddFAQ"  class="ajax-form clearfix">
                <div class="form-group" dir="rtl">
                <label>שאלה</label>
                <input type="text" name="Question" class="form-control">
                </div>     

                <div class="form-group" dir="rtl">
                <label>תשובה</label>
                <textarea class="form-control summernote" name="Answer" rows="5"></textarea>
                </div> 
 
    
				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-success"><?php _e('main.save_changes') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close"><?php _e('main.close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>  
    
    
  	<div class="ip-modal" id="EditFAQPopup" tabindex="-1">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header" dir="rtl">
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title">עריכת שאלה</h4>
                
				</div>
				<div class="ip-modal-body" dir="rtl">
<form action="EditFAQ"  class="ajax-form clearfix">
<input type="hidden" name="ItemId">
<div id="resultFAQ">


  
</div>

				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-success"><?php _e('main.save_changes') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>  
    
    
    
	  
	  
  <div class="tab-pane fade text-right" role="tabpanel" id="NewCompany">
            <div class="card spacebottom">
			<div class="card-header text-right"><strong>הקמת חברה</strong></div>    
 			<div class="card-body">  
		
<?php $DefaultSettings = DB::table('settings')->where('CompanyNum', '100')->first();	?>
<?php $ReqInput = '<span class="text-danger" style="font-size: 10px; font-weight:bold;">*שדה חובה*</span>'; ?>
			    	    	    	    
<form action="CreateNewCompanyInSystem"  class="ajax-form clearfix" dir="rtl" autocomplete="off">
<div class="alertb alert-info" role="alert"><strong>פרטי החברה</strong></div>
		<div class="row">
			<div class="col-md-4 col-sm-12">
 				<div class="form-group">
                <label>שם החברה <?php echo $ReqInput; ?></label>
                <input type="text" class="form-control" name="CompanyName" id="CompanyName">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>סוג החברה <?php echo $ReqInput; ?></label>
                <select name="BusinessType" id="BusinessType" class="form-control BusinessTypeSelect">
                <?php	$BusinessTypes = DB::table('BusinessType')->where('id', '!=', '1')->get();	?>
                <?php	foreach ($BusinessTypes as $BusinessType) {	?>
                <option value="<?php echo $BusinessType->id; ?>"><?php echo $BusinessType->Type; ?></option>
                <?php	}	?>
                </select>
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>מספר החברה <?php echo $ReqInput; ?></label>
                <input type="text" class="form-control" name="CompanyId" id="CompanyId">
                </div>
			</div>
		</div>
               
		<div class="row">
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>חברה חייבת במע״מ <?php echo $ReqInput; ?></label>
                <select name="CompanyVat" class="form-control">
                <option value="0">כן</option>
                <option value="1">לא</option>
              </select>
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>% ניכוי מס במקור</label>
                <input type="number" class="form-control" name="NikuyMsBamakor" id="NikuyMsBamakor">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>תוקף ניכוי מס במקור</label>
                <input type="date" class="form-control" name="NikuyMsBamakorDate" id="NikuyMsBamakorDate">
                </div>
			</div>
		</div>
                
                
                                
				<hr>	
<div class="alertb alert-info" role="alert"><strong>מיקום החברה</strong></div>				
		<div class="row">
			<div class="col-md-6 col-sm-12">
 				<div class="form-group">
                <label>עיר <?php echo $ReqInput; ?></label>
                <select class="CitiesSelect" name="Cities" id="CitiesSelect"></select>
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>רחוב <?php echo $ReqInput; ?></label>
                <input type="text" class="form-control" name="Street" id="Street">
                </div>
			</div>
		</div>
                
				
		<div class="row">
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>מספר רחוב <?php echo $ReqInput; ?></label>
                <input type="tel" class="form-control" name="Number" id="Number">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>מיקוד</label>
                <input type="tel" class="form-control" name="Zip" id="Zip">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>תא דואר</label>
                <input type="tel" class="form-control" name="POBox" id="POBox">
                </div>
			</div>
		</div>
					
				<hr>	
<div class="alertb alert-info" role="alert"><strong>דרכי התקשרות עם החברה</strong></div>
	
	<div class="row">
		
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>טלפון נייד <?php echo $ReqInput; ?></label>
                <input type="tel" class="form-control" name="ContactMobile" id="ContactMobile">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>טלפון קווי</label>
                <input type="tel" class="form-control" name="ContactPhone" id="ContactPhone">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>פקס</label>
                <input type="tel" class="form-control" name="ContactFax" id="ContactFax">
                </div>
			</div>
		</div>
                
		<div class="row">
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>דואר אלקטרוני <?php echo $ReqInput; ?></label>
                <input type="email" class="form-control" name="Email" id="Email">
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>אתר אינטרנט</label>
                <input type="text" class="form-control" name="WebSite" id="WebSite">
                </div>
			</div>
		</div>
                
                
                
                
			
               
                
					
				<hr>	
<div class="alertb alert-info" role="alert"><strong>הגדרות חברה</strong></div>
	
	<div class="row">
		
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>אפשר ניהול מסמכים והנה״ח <?php echo $ReqInput; ?></label>
                <select name="EnableDocs" class="form-control">
                <option value="0">לא</option>
                <option value="1">כן</option>
              </select>
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>אפשר סליקת אשראי וטוקנים <?php echo $ReqInput; ?></label>
                <select name="EnablePayments" class="form-control">
                <option value="0">לא</option>
                <option value="1">כן</option>
              </select>
                </div>
			</div>
		</div>
             
             
             
                            

              
              <hr>
               
              
	<div class="row">
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>מחיר שליחת דואר אלקטרוני (₪) <?php echo $ReqInput; ?></label>
                <input type="text" class="form-control" name="EmailPrice" id="EmailPrice" value="<?php echo @$DefaultSettings->EmailPrice; ?>">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>מחיר הודעת SMS (₪) <?php echo $ReqInput; ?></label>
                <input type="text" class="form-control" name="SMSPrice" id="SMSPrice" value="<?php echo $DefaultSettings->SMSPrice; ?>">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>אורך הודעת SMS (תווים) <?php echo $ReqInput; ?></label>
                <input type="text" class="form-control" name="SMSLimit" id="SMSLimit" value="<?php echo $DefaultSettings->SMSLimit; ?>">
                </div>
			</div>
		</div>
				
				
				

				
					
				<hr>	
<div class="alertb alert-info" role="alert"><strong>הקמת משתמש מנהל ראשי</strong></div>
	
	<div class="row">
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>שם פרטי <?php echo $ReqInput; ?></label>
                <input type="tel" class="form-control" name="UFirstName" id="UFirstName">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>שם משפחה <?php echo $ReqInput; ?></label>
                <input type="tel" class="form-control" name="ULastName" id="ULastName">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>שם משתמש <?php echo $ReqInput; ?></label>
                <input type="text" class="form-control" name="UUserName" id="UUserName">
                </div>
			</div>
	</div>
			
	<div class="row">
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>טלפון נייד <?php echo $ReqInput; ?></label>
                <input type="tel" class="form-control" name="UContactMobile" id="UContactMobile">
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>דואר אלקטרוני <?php echo $ReqInput; ?></label>
                <input type="email" class="form-control" name="UContactEmail" id="UContactEmail">
                </div>
			</div>
		</div>
                
				
				<hr>	
               
					
<div class="alertb alert-info" role="alert" id="DesignSettingsB" style="cursor: pointer;"><strong>עיצוב מסמכים</strong></div>
	
	<div class="row" id="DesignSettingsD" style="display: none;">
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>לוגו למסמכים</label>
                <input type="text" class="form-control" name="DocsCompanyLogo" id="DocsCompanyLogo">
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>צבע רקע, קווים וכותרות</label>
                <div id="SetDocBackPreview" style="width:50px;height:10px;display:inline-block;"></div>
                <select class="form-control" name="DocsBackgroundColor" id="DocsBackgroundColor" onchange="dsfsd()">
                	<option value="#e10025">אדום</option>
                	<option value="#bd1a2f">אדום כהה</option>
                	<option value="#f19218">כתום</option>
                	<option value="#f8b43d">צהוב</option>
                	<option value="#7aa229">ירוק</option>
                	<option value="#648426">ירוק כהה</option>
                	<option value="#17a2b8">טורקיז</option>
                	<option value="#2b71b9">כחול</option>
                	<option value="#2B619D">כחול כהה</option>
                	<option value="#e83e8c">ורוד</option>
                	<option value="#b79bf7">סגול</option>
                	<option value="#6610f2">סגול כהה</option>
                	<option value="#343a40">אפור</option>
                	<option value="#000000">שחור</option>
                </select>
                </div>
			</div>
		</div>
                
		
				<hr>	
<div class="alertb alert-info" role="alert" id="CalSettingsB" style="cursor: pointer;"><strong>הגדרות יומן</strong></div>
	
	<div class="row" id="CalSettingsD" style="display: none;">
		
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>תחילת יום עבודה <?php echo $ReqInput; ?></label>
                <select name="FirstHour" class="form-control">
                <option value="4">04:00</option>
                <option value="5">05:00</option>
                <option value="6">06:00</option>
                <option value="7">07:00</option>
                <option value="8">08:00</option>
                <option value="9">09:00</option>
                <option value="10">10:00</option>
                <option value="11">11:00</option>
                <option value="12">12:00</option>
                <option value="13">13:00</option>
				</select>
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>סיום יום עבודה <?php echo $ReqInput; ?></label>
                <select name="LastHour" class="form-control">
                <option value="14">14:00</option>
                <option value="15">15:00</option>
                <option value="16">16:00</option>
                <option value="17">17:00</option>
                <option value="18">18:00</option>
                <option value="19">19:00</option>
                <option value="20">20:00</option>
                <option value="21">21:00</option>
                <option value="22">22:00</option>
                <option value="23">23:00</option>
                <option value="24">24:00</option>
              </select>
                </div>
			</div>
		</div>
                
					
				<hr>	
<div class="alertb alert-info" role="alert" id="PaySettingsB" style="cursor: pointer;"><strong>הגדרות סליקה</strong></div>
	<div id="PaySettingsD" style="display: none;">
	<div class="row">
			<div class="col-md-3 col-sm-12">
                <div class="form-group">
                <label>מספר מסוף פנימי (יעד שריג)</label>
                <input type="text" class="form-control" name="YaadNumber" id="YaadNumber">
                </div>
			</div>
			<div class="col-md-3 col-sm-12">
                <div class="form-group">
                <label>סיסמא לזיכויים (יעד שריג)</label>
                <input type="text" class="form-control" name="YaadzPass" id="YaadzPass" placeholder="השאר ריק לברירת מחדל">
                </div>
			</div>
			<div class="col-md-3 col-sm-12">
                <div class="form-group">
                <label>מספר מסוף בש.ב.א</label>
                <input type="text" class="form-control" name="Shva" id="Shva">
                </div>
			</div>
			<div class="col-md-3 col-sm-12">
                <div class="form-group">
                <label>סולק עיקרי</label>
                <select name="CreditType" class="form-control">
                <option value="">בחר סולק עיקרי</option>
                <option value="0">לאומי קארד</option>
                <option value="1">ישראכארט</option>
                <option value="2">ויזה כאל</option>
                <option value="3">אמריקן אקספרס</option>
                <option value="4">דיינרס</option>
              </select>
                </div>
			</div>
		</div>
              
	<div class="row">
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>מס׳ ספק: ישראכראט</label>
                <input type="text" class="form-control" name="Isracrd" id="Isracrd">
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>מס׳ ספק: אמריקן אקספרס</label>
                <input type="text" class="form-control" name="Amkas" id="Amkas">
                </div>
			</div>
			</div>
	<div class="row">
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>מס׳ ספק: ויזה כאל</label>
                <input type="text" class="form-control" name="VisaCal" id="VisaCal">
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>מס׳ ספק: דיינרס</label>
                <input type="text" class="form-control" name="Diners" id="Diners">
                </div>
			</div>
			</div>
	<div class="row">
			<div class="col-md-12 col-sm-12">
                <div class="form-group">
                <label>מס׳ ספק: לאומי קארד</label>
                <input type="text" class="form-control" name="LeumiCard" id="LeumiCard">
                </div>
			</div>
		</div>
	</div>
				<hr>	
               
					
<div class="alertb alert-info" role="alert" id="MassavSettingsB" style="cursor: pointer;"><strong>הגדרות מס״ב</strong></div>
	<div id="MassavSettingsD" style="display: none;">
	<div class="row">
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>מספר מוסד</label>
                <input type="text" class="form-control" name="MassavMosad" id="MassavMosad">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>מספר מוסד עבור קובץ זיכויים</label>
                <input type="text" class="form-control" name="MassavZikoy" id="MassavZikoy">
                </div>
			</div>
			<div class="col-md-4 col-sm-12">
                <div class="form-group">
                <label>מספר מוסד שולח</label>
                <input type="text" class="form-control" name="MassavSender" id="MassavSender">
                </div>
			</div>
		</div>
	</div>
				<hr>	
               
					
<div class="alertb alert-info" role="alert" id="VCSettingsB" style="cursor: pointer;"><strong>התממשקות מרכזיה VoiceCenter</strong></div>
	
	<div class="row" id="VCSettingsD" style="display: none;">
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>טוקן התחברות</label>
                <input type="text" class="form-control" name="VoiceCenterToken" id="VoiceCenterToken">
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>מספר ברירת מחדל לשיחות יוצאות</label>
                <input type="text" class="form-control" name="VoiceCenterNumber" id="VoiceCenterNumber">
                </div>
			</div>
		</div>
               
				<hr>	
               
					
<div class="alertb alert-info" role="alert" id="CpaSettingsB" style="cursor: pointer;"><strong>דוחות אוטומטיים</strong></div>
	
	<div class="row" id="CpaSettingsD" style="display: none;">
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>דואר אלקטרוני של מנהל החשבונות</label>
                <input type="email" class="form-control" name="CpaEmail" id="CpaEmail">
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>דואר אלקטרוני לשליחת העתק</label>
                <input type="email" class="form-control" name="CpaEmailCopy" id="CpaEmailCopy">
                </div>
			</div>
		</div>
               
				<hr>	
               
					
<div class="alertb alert-info" role="alert" id="019SettingsB" style="cursor: pointer;"><strong>התממשקות שליחת <span dir="ltr">(019) SMS</span></strong></div>
	
	<div class="row" id="019SettingsD" style="display: none;">
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>שם משתמש</label>
                <input type="text" class="form-control" name="Username019" id="Username019" placeholder="השאר ריק לברירת מחדל">
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>סיסמא</label>
                <input type="text" class="form-control" name="Password019" id="Password019" placeholder="השאר ריק לברירת מחדל">
                </div>
			</div>
		</div>
               
				<hr>	
               
					
<div class="alertb alert-info" role="alert" id="SGSettingsB" style="cursor: pointer;"><strong>התממשקות שליחת מיילים (SendGrid)</strong></div>
	
	<div class="row" id="SGSettingsD" style="display: none;">
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>שם משתמש</label>
                <input type="text" class="form-control" name="UsernameSendGrid" id="UsernameSendGrid" placeholder="השאר ריק לברירת מחדל">
                </div>
			</div>
			<div class="col-md-6 col-sm-12">
                <div class="form-group">
                <label>סיסמא</label>
                <input type="text" class="form-control" name="PasswordSendGrid" id="PasswordSendGrid" placeholder="השאר ריק לברירת מחדל">
                </div>
			</div>
		</div>                
                
                
<hr>	
<div class="form-group">
<button type="submit" class="btn btn-primary btn-lg">הקמת חברה</button>	
</div>
		</div>
				</form>			    
 			    
		</div></div></div>
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
		  
	     
   
		
   
	         
      
		         
  	
</div>                               
 
</div>  
</div>
     
	</div> 
	
	 	   
<script>
	
$(document).ready(function(){
    $("#CalSettingsB").click(function(){
        $("#CalSettingsD").toggle();
    });
    $("#PaySettingsB").click(function(){
        $("#PaySettingsD").toggle();
    });
    $("#MassavSettingsB").click(function(){
        $("#MassavSettingsD").toggle();
    });
    $("#VCSettingsB").click(function(){
        $("#VCSettingsD").toggle();
    });
    $("#CpaSettingsB").click(function(){
        $("#CpaSettingsD").toggle();
    });
    $("#019SettingsB").click(function(){
        $("#019SettingsD").toggle();
    });
    $("#SGSettingsB").click(function(){
        $("#SGSettingsD").toggle();
    });
    $("#DesignSettingsB").click(function(){
        $("#DesignSettingsD").toggle();
    });
    
    
 $('.summernote').summernote({
        tabsize: 2,
        height: 100,
	   toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough']],      
    ['para', ['ul', 'ol']],
    ['color', ['color']], 
    ['insert', ['hr']]       
  ]
      });    
    
    
    
    
});
	
	
$(function() {
			var time = function(){return'?'+new Date().getTime()};
			
			// Header setup
			$('#FAQPopup').imgPicker({
			});
			// Header setup
			$('#EditFAQPopup').imgPicker({
			});
	
});	
	
$(document).ready(function()
{
    $('#ContactMobile').keyup(function()
    {
         $('#UContactMobile').val($(this).val());
         $('#UUserName').val($(this).val());
    });
    $('#Email').keyup(function()
    {
         $('#UContactEmail').val($(this).val());
         $('#CpaEmailCopy').val($(this).val());
    });
});
	
	
	
	$(document).ready(function(){
    var x = document.getElementById("DocsBackgroundColor").value;
    document.getElementById("SetDocBackPreview").style.backgroundColor = x;
	});
	
	function dsfsd() {
    var x = document.getElementById("DocsBackgroundColor").value;
    document.getElementById("SetDocBackPreview").style.backgroundColor = x;
}

$('[data-toggle="tabajax"]').click(function(e) {
    var $this = $(this),
        loadurl = $this.attr('href'),
        targ = $this.attr('data-target');

    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

    $this.tab('show');
    return false;
});		

	//שינוי עמוד בהתאם לטאב
$('#newnavid a').click(function(e) {
  e.preventDefault();
  $(this).pill('show');
$('.tab-content > .tab-pane.active').jScrollPane();   
$('html,body').scrollTop(0);
});


$("a").on("shown.bs.tab", function(e) {
    
  var id = $(e.target).attr("href").substr(1);
  window.location.hash = id;
  $('html,body').scrollTop(0);

});    
    
    
    
// on load of the page: switch to the currently selected tab
var hash = window.location.hash;
$('.nav-tabs a[href="' + hash + '"]').tab('show');
//סיום שינוי עמוד בהתאם לטאב

</script>
	     
	      
	       
	        
<script>
	
$(document).ready(function(){
  var windowWidth = $(window).width();
  if(windowWidth <= 1024) //for iPad & smaller devices
     $('#MenuSettingSystem').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>

	          
	           
           
	             
	               
<script>
$(function() {
	$('[data-toggle="tooltip"]').tooltip()
});	
	
	
$(document).ready(function() {
 /// טעינת ערים ורחובות

  	$('.CitiesSelect').select2({
		theme:"bootstrap", 
		placeholder: "בחר עיר",
		language: "he",
		allowClear: true,
		width: '100%',
  		ajax: {
			url: 'action/CitiesSelect.php',
    		dataType: 'json'
  		},
		minimumInputLength: 3,
	});

});
</script>              
                   

<?php else: ?>
<?php //redirect_to('index.php');  ?>
<?php ErrorPage ('עמוד לבעלי הרשאות בלבד', 'סליחה, אין לך הרשאות לגשת לעמוד זה.'); ?>
<?php endif ?>


<?php endif ?>



<?php require_once '../app/views/footernew.php'; ?>