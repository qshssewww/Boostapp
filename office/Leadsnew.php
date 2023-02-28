<?php require_once '../app/init.php'; ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>

<?php
$CompanyNum = Auth::user()->CompanyNum;
$resultcount = DB::table('pipeline')->where('CompanyNum','=', $CompanyNum)->count();


CreateLogMovement('נכנס לניהול לידים מתקדם', '0');

?>

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment-with-locales.js"></script>
<script src="vendor/angular-moment-picker.min.js"></script>
<link href="vendor/angular-moment-picker.min.css" rel="stylesheet">

<link href="assets/css/fixstyle.css" rel="stylesheet">
<div class="col-md-12 col-sm-12">
<div class="row">

<?php
  

$PageTitleClient = 'ניהול לידים';    
             
                
    
?>    


<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-align-right"></i> <?php echo $PageTitleClient; ?> <span style="color:#48AD42;"><?php echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1"> 
<a href="javascript:void(0);" class="btn btn-primary btn-block" data-ip-modal="#AddNewLead" dir="rtl"><i class="fas fa-plus-circle fa-fw"></i> ליד חדש</a> 
</div>


</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active"><?php echo $PageTitleClient; ?></li>
  </ol>  
</nav>    

<div class="row" ng-cloak ng-app="sa_display" ng-controller="controller" ng-init="display_data()">

<div class="col-md-2 col-sm-12 order-md-2">
    
  
    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-filter"></i> <b>מסננים</b>
 	</div>    
  	<div class="card-body text-right" dir="rtl">      
    
<div class="form-group">
<label for="exampleInputPassword1">שם לקוח</label>
<input type="test" class="form-control" ng-model="searchName.CompanyName">
</div> 
    
<div class="form-group">
<label for="exampleInputPassword1">טלפון</label>
<input type="test" class="form-control" ng-model="searchName.ContactMobile">
</div>  
    
<div class="form-group">
<label for="exampleInputPassword1">דוא"ל</label>
<input type="test" class="form-control" ng-model="searchName.Email">
</div>  
        
<div class="form-group">
<label for="exampleInputPassword1">ת.הוספה</label>
<input class="form-control" id="Datenew" ng-model="searchName.datesnew"
       ng-model-options="{ updateOn: 'blur' }"
       placeholder="בחר תאריך..."
       locale="he"
       format="DD/MM/YYYY"   
       max-view="day"
       start-view="month"
       autoclose="false"
       today="true"
       moment-picker="searchName.datesnew"
       >  
    
    
<input type="text" class="form-control">    

    
</div>   
        
        
<div class="form-group">
<label for="exampleInputPassword1">מקור אוטומטי</label>
<select class="form-control">
      <option value="">הכל</option>
      <option>2</option>
      <option>3</option>
      <option>4</option>
      <option>5</option>
    </select>
</div>  
        
<div class="form-group">
<label for="exampleInputPassword1">מקור ידני</label>
<select class="form-control">
      <option value="">הכל</option>
      <option>2</option>
      <option>3</option>
      <option>4</option>
      <option>5</option>
    </select>
</div>          
        
<div class="form-group">
<label for="exampleInputPassword1">נציג מכירות</label>
<select class="form-control" ng-model="searchName.display_name">
      <option value="">הכל</option>
      <option>2</option>
      <option>3</option>
      <option>4</option>
      <option>5</option>
    </select>
</div>          
        
<div class="form-group">
<label for="exampleInputPassword1">סטטוס</label>
<select class="form-control" ng-model="searchName.Title">
      <option value="">הכל</option>
      <option>ליד חדש</option>
      <option>ליד חם</option>
      <option>התקשרות ראשונית</option>
      <option>שיעור נסיון</option>
      <option>מצב סגירה</option>
      <option>הצלחה</option>
      <option>כשלון</option>
    </select>
</div>          
        
        
</div>
</div>
    
</div>    
    
    
    
<div class="col-md-10 col-sm-12 order-md-1">	

    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-align-right"></i> <b><?php echo $PageTitleClient; ?></b>
 	</div>    
  	<div class="card-body">       
 <p>Search Query: {{ ctrl.timepicker  }} | {{ searchName.datesnew  }}</p> 
		<table class="table table-bordered table-striped text-right" dir="rtl">
            
         <thead>
      <tr>
        <td>#</td>  
        <td>
          <a href="javascript:void(0);" ng-click="sortType = 'CompanyName'; sortReverse = !sortReverse">
            <sapn class="text-primary">שם לקוח</sapn>
            <span ng-if="sortType == 'CompanyName' && !sortReverse" class="text-primary fas fa-caret-down"></span>
            <span ng-if="sortType == 'CompanyName' && sortReverse" class="text-primary fas fa-caret-up"></span>
          </a>
        </td>
        <td>
          <a href="javascript:void(0);" ng-click="sortType = 'ContactMobile'; sortReverse = !sortReverse">
            <span class="text-primary">טלפון</span>
            <span ng-if="sortType == 'ContactMobile' && !sortReverse" class="text-primary fas fa-caret-down"></span>
            <span ng-if="sortType == 'ContactMobile' && sortReverse" class="text-primary fas fa-caret-up"></span>
          </a>
        </td>  
        <td>
          <a href="javascript:void(0);" ng-click="sortType = 'Email'; sortReverse = !sortReverse">
            <span class="text-primary">דוא"ל</span>
            <span ng-if="sortType == 'Email' && !sortReverse" class="text-primary fas fa-caret-down"></span>
            <span ng-if="sortType == 'Email' && sortReverse" class="text-primary fas fa-caret-up"></span>
          </a>
        </td>
        <td>
          <a href="javascript:void(0);" ng-click="sortType = 'Dates'; sortReverse = !sortReverse">
            <span class="text-primary">ת.הצטרפות</span>
            <span ng-if="sortType == 'Dates' && !sortReverse" class="text-primary fas fa-caret-down"></span>
            <span ng-if="sortType == 'Dates' && sortReverse" class="text-primary fas fa-caret-up"></span>
          </a>
        </td>
       <td>

            <span class="text-primary">מקור אוטומטי</span>
            
        </td>   
        <td>

            <span class="text-primary">מקור ידני</span>

        </td>  
        <td>
          <a href="javascript:void(0);" ng-click="sortType = 'display_name'; sortReverse = !sortReverse">
            <span class="text-primary">נציג מכירות</span>
            <span ng-if="sortType == 'display_name' && !sortReverse" class="text-primary fas fa-caret-down"></span>
            <span ng-if="sortType == 'display_name' && sortReverse" class="text-primary fas fa-caret-up"></span>
          </a>
        </td>
        <td>
          <a href="javascript:void(0);" ng-click="sortType = 'Title'; sortReverse = !sortReverse">
            <span class="text-primary">סטטוס</span>
            <span ng-if="sortType == 'Title' && !sortReverse" class="text-primary fas fa-caret-down"></span>
            <span ng-if="sortType == 'Title' && sortReverse" class="text-primary fas fa-caret-up"></span>
          </a>
        </td>  
          
    <td>
          <a href="javascript:void(0);" ng-click="sortType = 'Brands'; sortReverse = !sortReverse">
            <span class="text-primary">סניף</span>
            <span ng-if="sortType == 'Brands' && !sortReverse" class="text-primary fas fa-caret-down"></span>
            <span ng-if="sortType == 'Brands' && sortReverse" class="text-primary fas fa-caret-up"></span>
          </a>
        </td>        
          
          
          
      </tr>
    </thead>   
        
            <tbody>
                
			<tr ng-repeat="x in names | orderBy:sortType:sortReverse | filter:searchName">
                <th>{{$index+1}}</th>
				<th>{{x.CompanyName}}</th>
				<td>{{x.ContactMobile}}</td>
				<td>{{x.Email}}</td>
				<td>{{x.datesnew}}</td>
                <td>{{x.pipelineid}}</td>
                <td ng-if="x.pipelinestatus == '0'">פעיל</td>
                <td ng-if="x.pipelinestatus !== '0'">ארכיון</td>
                <td >{{x.display_name}}</td>
                <td>{{x.Title}}</td>
                <td></td>
			</tr>
            
            </tbody>
		</table>

		</div></div>
    
	</div> 
</div>

</div>


<script>
    var app = angular.module("sa_display", ['moment-picker']);
    
      app.filter('toSec', function($filter) {
  return function(input) {
      var result = new Date(input).getTime();
      return result || '';
  };
           
          
});  
  

    app.controller("controller", ["$scope", "$http", "$filter", function($scope, $http, $filter) {

        $scope.sortType     = 'CompanyName'; // set the default sort type
        $scope.sortReverse  = false;  // set the default sort order
        $scope.searchName   = {};     // set the default search/filter term
        $scope.filFecha = {};
        $scope.locale='he';
        $scope.format='dd/MM/yyyy';
        $scope.formattedDate =   $filter('date')($scope.datesnew, "dd/MM/yyyy");

       $scope.datesnew = new Date();
       $scope.date = $filter('date')($scope.datesnew, "dd/MM/yyyy"); 
        
        $scope.display_data = function() {
            $http.get("LeadsNewPost.php")
                .then(function(data) {
                    console.log(data);
                    $scope.names = data.data;
                }, function(err){});
        }
    }]);
    
    
</script> 

<td ng-bind = "ddMMyyyy">{{x.datesnew}}</td>

<?php include('InfoPopUpInc.php'); ?>


<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>



<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>