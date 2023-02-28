<?php require_once '../app/init.php'; ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('45') && @$_REQUEST['Act']=='0' || Auth::userCan('46') && @$_REQUEST['Act']=='1' || Auth::userCan('47') && @$_REQUEST['Act']=='2'): ?>
<?php
$CompanyNum = Auth::user()->CompanyNum;
$StatusAct = @$_REQUEST['Act'];
if ($StatusAct==''){
$StatusAct = '0';    
}
$Clients = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('Status', '=', $StatusAct)->orderBy('CompanyName', 'ASC')->get();
$resultcount = count($Clients);

CreateLogMovement('נכנס לניהול לקוחות', '0');

?>

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js"></script>
<link href="assets/css/fixstyle.css" rel="stylesheet">
<div class="col-md-12 col-sm-12">
<div class="row">

<?php
  
if (@$StatusAct=='0'){
$PageTitleClient = 'ניהול לקוחות';    
} 
else if (@$StatusAct=='1'){
$PageTitleClient = 'ארכיון לקוחות';    
}  
else if (@$StatusAct=='2'){
$PageTitleClient = 'ניהול מתעניינים';    
}  
else {
$PageTitleClient = 'ניהול לקוחות';     
}                
                
    
?>    


<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-users"></i> <?php echo $PageTitleClient; ?> <span style="color:#48AD42;"><?php echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">
<?php if (Auth::userCan('50') && @$_REQUEST['Act']=='0' || Auth::userCan('50') && @$_REQUEST['Act']=='1'): ?>       
<a href="javascript:void(0);" onclick="NewClient()" class="btn btn-success btn-block" dir="rtl"><i class="fas fa-users"></i> הוספת לקוח חדש</a>
<?php endif ?>
    
<?php if (Auth::userCan('51') && @$_REQUEST['Act']=='2'): ?>    
<a href="javascript:void(0);" class="btn btn-primary btn-block" data-ip-modal="#AddNewLead" dir="rtl"><i class="fas fa-plus-circle fa-fw"></i> ליד חדש</a>
<?php endif ?>
    
  
    
    
</div>














</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active"><?php echo $PageTitleClient; ?></li>
  </ol>  
</nav>    

<div class="row">
<div class="col-md-12 col-sm-12">	

    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-th"></i> <b><?php echo $PageTitleClient; ?></b>
 	</div>    
  	<div class="card-body">       

	<div ng-app="sa_display" ng-controller="controller" ng-init="display_data()">
		<table class="table table-bordered">
			<tr>
				<th>S.No</th>
				<th>Name</th>
				<th>Email</th>
				<th>Age</th>
                <th>Age</th>
			</tr>
			<tr ng-repeat="x in names">
				<td>{{x.id}}</td>
				<td>{{x.CompanyName}}</td>
				<td>{{x.CompanyId}}</td>
				<td>{{x.ContactMobile}}</td>
                <td>{{x.Dates | date : dd/mm/yyyy }}</td>
			</tr>
		</table>
	</div>
		</div></div>
    
	</div> 
</div>

</div>


	<div class="ip-modal text-right" id="AddNewLead">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close" title="Close" style="float:left;" data-dismiss="modal">&times;</a>
				<h4 class="ip-modal-title">הוסף ליד חדש</h4>

				</div>
				<div class="ip-modal-body">

				<form action="AddNewLead"  class="ajax-form clearfix" autocomplete="off">
                
                <div class="row">    
                <div class="col-md-6 col-sm-12 order-md-1">    
				<div class="form-group" dir="rtl">
                <label>שם פרטי</label>
                <input type="text" name="FirstName" class="form-control">
                </div>
				</div>	
                <div class="col-md-6 col-sm-12 order-md-2">     
				<div class="form-group" dir="rtl">
                <label>שם משפחה</label>
                <input type="text" name="LastName" class="form-control">
                </div>	
                </div>    
                    
                </div> 
                    
                    
				<div class="form-group" dir="rtl">
                <label>טלפון סלולרי</label>
                <input type="text" name="ContactMobile" id="ContactMobile" class="form-control">
                </div>	
					
				<div class="form-group" dir="rtl">
                <label>דואר אלקטרוני</label>
                <input type="email" name="Email" class="form-control">
                </div>	
				 
                <div class="form-group" dir="rtl">
                <label>מתעניין בשיעור</label>
                <select class="form-control js-example-basic-single select2multipleDesk text-right" name="ClassType[]" id="ClassType" dir="rtl"  multiple="multiple" >
                <option value="BA999">כל השיעורים</option>    
				<?php
				$ClassTypes = DB::table('class_type')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('Type', 'ASC')->get();    
				foreach ($ClassTypes as $ClassType) { ?> 	
				<option value="<?php echo $ClassType->id; ?>"><?php echo $ClassType->Type ?></option>
				<?php } ?>	
				</select>
                </div>	    
                    
                    
                    
				<div class="form-group" dir="rtl">
                <label>סטטוס</label>
                <select class="form-control" name="Status">
				<?php
				$PipeTitles = DB::table('leadstatus')->where('Status','=', '0')->orderBy('Sort', 'ASC')->get();    
				foreach ($PipeTitles as $PipeTitle) { ?> 	
				<option value="<?php echo $PipeTitle->id; ?>"><?php echo $PipeTitle->Title ?></option>
				<?php } ?>	
				</select>
                </div>	
					
				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                </div>
				</form>    
                <a  class="btn btn-dark ip-close text-white" data-dismiss="modal"><?php _e('main.close') ?></a>     
				
                
				</div>
			</div>
		</div>
	</div>

<script>
    var app = angular.module("sa_display", []);
    app.controller("controller", function($scope, $http) {
        $scope.display_data = function() {
            $http.get("ClientPostNew.php")
                .success(function(data) {
                    $scope.names = data;
                });
        }
    });
</script> 

<script>
 
$( ".select2multipleDesk" ).select2( {theme:"bootstrap", placeholder: "בחר סוג שיעור", 'language':"he", dir: "rtl" } );      
    
$('#ClassType').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){
      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "בחר סוג שיעור", 'language':"he", dir: "rtl" } );
    }
  }
    
});	      
    
$(function() {
			var time = function(){return'?'+new Date().getTime()};
						
			$('#AddNewLead').imgPicker({
			});

	
	
});	

</script>


<?php include('InfoPopUpInc.php'); ?>


<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>