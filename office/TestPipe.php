<?php require_once '../app/init.php'; 

if (Auth::guest()) redirect_to(App::url());

?>


<?php echo View::make('headernew')->render() ?>

<link href="assets/css/fixstyle.css" rel="stylesheet">



<style>
.list-special .list-group-item:first-child {
  border-top-right-radius: 0px !important;
  border-top-left-radius: 0px !important;
}

.list-special .list-group-item:last-child {
  border-bottom-right-radius: 0px !important;
  border-bottom-left-radius: 0px !important;
}
	.cursorcursor:active {cursor: move;
	}

	.cursorcursor li.ui-sortable-helper{
    cursor: move;
}

	.ui-draggable-dragging{

	/**-ms-transform: rotate(7deg);-webkit-transform: rotate(7deg);=transform: rotate(7deg);**/
		z-index: 999999999;
		background:#F0F0F0;
		border: 1px dashed #525252 !important;
		}
	.hover li { 
   -moz-box-shadow:    inset 0 0 10px #000000;
   -webkit-box-shadow: inset 0 0 10px #000000;
   box-shadow:         inset 0 0 10px #000000;
}

</style>
<?php if (Auth::check()):?>
<?php  $CompanySettingsDash = DB::table('settings')->where('id', '=', '1')->first(); ?>


<div class="col-md-12 col-sm-12">
    
<div class="row pb-3">

<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-home fa-fw"></i> <?php echo lang('control_panel') ?>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">
<a href="javascript:void(0);" class="btn btn-info btn-block" data-ip-modal="#AddNewLead" dir="rtl"><i class="fas fa-plus-circle fa-fw"></i> <?php echo lang('a_new_lead') ?></a>
</div>


</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item active"><?php echo lang('path_main') ?></li>
  <li style="float: left; left: 30px; position:absolute;" dir="ltr">
  <a href="#" data-toggle="tooltip" title="<?php echo lang('expired_tasks') ?>"><i class="fas fa-circle" style="color: #ff8080;"></i></a>
  <a href="#" style="padding-left: 5px;"><i class="fas fa-circle" style="color: #fff0b3;"></i></a>
  <a href="#" style="padding-left: 5px;" data-toggle="tooltip" title="<?php echo lang('tasks_are_planned_not_expired_yet') ?>"><i class="fas fa-circle" style="color: #9ce2a7;"></i></a>
  <a href="#" style="padding-left: 5px;" data-toggle="tooltip" title="<?php echo lang('no_tasks_planned_tasks_were_in_past') ?>"><i class="fas fa-circle" style="color: #abb1bf;"></i></a>
	<a href="#" style="position: absolute; left: 27px; bottom: 2px;z-index: 2;" data-toggle="tooltip" title="<?php echo lang('tasks_are_not_defined') ?>">
     	<span style="color: #efc15d; margin-left: 4px; font-size: 10px;">
     	<i class="fas fa-exclamation"></i>
     	</span>
	 </a>
  </li>
  </ol>  
</nav>    


  <style>
#container{width: 80%;margin: auto auto;}
.news_list {
list-style: none;
}
.loadmore {
color: #FFF;
border-radius: 5px;
width: 50%;
height: 50px;
font-size: 20px;
background: #42B8DD;
outline: 0;
}
 .loadbutton{
    text-align: center;
}	</style>


    
  <input type="text">  

 
<ul class="news_list">

<li class="loadbutton"><button class="loadmore" data-page="2">Load More</button></li>
</ul>	
<script type="text/javascript">
$(document).on('click','.loadmore',function () {
  $(this).text('Loading...');
    var ele = $(this).parent('li');
        $.ajax({
      url: 'TestPipe2.php',
      type: 'POST',
      data: {
              page:$(this).data('page'),
            },
      success: function(response){
           if(response){
             ele.hide();
                $(".news_list").append(response);
              }
            }
   });
});
    
    
var is_dirty = '';  
    
$("input[type='text']").change( function() {
 is_dirty = true;    
});    


$('a').mousedown(function(e) {
    if(is_dirty) {
        // if the user navigates away from this page via an anchor link, 
        //    popup a new boxy confirmation.
       alert('<?php echo lang('note_testpipe') ?>');
    }
});

window.onbeforeunload = function() {
if((is_dirty)){
            // call this if the box wasn't shown.
    return '<?php echo lang('note_testpipe') ?>';
    }
};
    
</script>






<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>