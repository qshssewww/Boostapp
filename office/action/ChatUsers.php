<?php
require_once '../../app/initcron.php';
$CompanyNum = Auth::user()->CompanyNum;
?>


<?php
function time_elapsed_string($time_ago)
{
    $time_ago = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
        return "נשלח עכשיו";
    }
    //Minutes
    else if($minutes <=60){
        if($minutes==1){
            return "לפני דקה אחת";
        }
        else{
            return "לפני $minutes דקות";
        }
    }
    //Hours
    else if($hours <=24){
        if($hours==1){
            return "לפני שעה אחת";
        }else{
            return "לפני $hours שעות";
        }
    }
    //Days
    else if($days <= 7){
        if($days==1){
            return "אתמול";
        }else{
            return "לפני $days ימים";
        }
    }
    //Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            return "לפני שבוע אחד";
        }else{
            return "לפני $weeks שבועות";
        }
    }
    //Months
    else if($months <=12){
        if($months==1){
            return "לפני חודש אחד";
        }else{
            return "לפני $months חודשים";
        }
    }
    //Years
    else{
        if($years==1){
            return "לפני שנה";
        }else{
            return "לפני $years שנים";
        }
    }
}
?>
<?php 
	$CurrentUserId = @$_REQUEST['U'];

?>
<ul id="myUL" class="ChatBlockGoTop" style="overflow-y: scroll;height:370px;">
      
      
      
      
      
  <?php if (Auth::userCan('114')): ?>    
  <li id="MenuChatIdAll">
   
  <a href="javascript:void(0);" onClick="ChooseUserToChat(0)" style="text-decoration: none;">
  <div class="card text-dark bg-light mb-1 ">
       
     
    <div class="card-header text-right namelist <?php if (@$CurrentUserId == '0') {echo "bg-info";} ?>" dir="rtl">
     <div class="row">
     <table <?php if (@$CurrentUserId == '0') {echo "class='text-white'";} ?>><tr><td>
     <i class="fas fa-bomb fa-3x"></i>
	 </td><td style="padding-right: 12px;padding-top:10px;">
     <div style="display: block; font-weight: bold;margin-bottom: -7px;">הודעה כללית</div>
     <div style="display: inline-block;border: 0px;height: 16px;overflow: hidden; font-size: 13px;">
     שליחת הודעה לכל הלקוחות באפליקציה
     </div>
     
     
     
     
     
     
     
</td></tr></table>		</div>
 	</div>
      
      
 	</div> 
	  </a>   
	  </li>      
   <?php endif ?>   
  
    
<?php if (Auth::userCan('113')): ?>      
 <?php

		
          $ChatUsers = DB::select('select *, MAX(`id`) AS newid , MAX(`Dates`) AS datetime from `chat` where `CompanyNum` = "'.$CompanyNum.'" GROUP BY `ChatRandom` ORDER BY `newid` DESC  LIMIT 100');
          
		  foreach($ChatUsers as $ChatUser){ 
			  
          if ($ChatUser->ToUserId != '0') {

			  $ChatLast = DB::table('chat')->where('CompanyNum' ,'=', $CompanyNum)->where('ToUserId', '=', @$ChatUser->ToUserId)->orderBy('id', 'DESC')->first();	  
			  $UserId = @$ChatUser->ToUserId;
			  $UsersMenu = DB::table('client')->where('CompanyNum' ,'=', $CompanyNum)->where('id', '=', @$UserId)->first();
			  $UserName = @$UsersMenu->CompanyName;
			  $UserEmail = @$UsersMenu->Email;
			 
?>
   <li id="MenuChatId<?php echo @$UserId; ?>">
   
  <a href="javascript:void(0);" onClick="ChooseUserToChat(<?php echo @$UserId; ?>)" style="text-decoration: none;">
  <div class="card text-dark bg-light mb-1 ">

     
    <?php
	$ChatCountNew = DB::table('chat')->where('CompanyNum' ,'=', $CompanyNum)->where('ChatRandom', '=', @$ChatLast->ChatRandom)->where('Status', '=', '0')->where('ToUserId', '=', @$ChatUser->ToUserId)->where('SendFrom', '=', '1')->count();
	if ($ChatCountNew != '0') {
	?>   
    <div style="position: absolute;left:10px;top:10px;">
	<span class="badge badge-pill badge-dark" dir="rtl"><?php echo @$ChatCountNew; ?> הודעות חדשות</span>
    </div>
	<?php } ?>
   
    <div class="card-header text-right namelist <?php if ($ChatCountNew != '0') {echo "bg-info";} ?>" dir="rtl">
     <div class="row">
     <table <?php if ($ChatCountNew != '0') {echo "class='text-white'";} ?>><tr><td>
     <center><img src="../assets/img/21122016224223511960489675402.png" class="rounded-circle pull-right profileimage" style="vertical-align: middle; width:50px; height: 50px;margin-left: 10px;"></center>
	 </td><td>
     <div style="display: block; font-weight: bold;margin-bottom: -7px;"><?php echo @$UserName; ?></div>
     <div style="display: inline-block;border: 0px;height: 16px;overflow: hidden; font-size: 13px;">
     	<?php
		echo @$ChatLast->Content;
		?>
     </div>
					<small style="display: block; margin-top: -4px;margin-right:-1px;" class="text-<?php if ($ChatCountNew != '0') {echo 'white';} else {echo 'muted';} ?>">
					<span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo with(new DateTime(@$ChatLast->Dates))->format('d/m/Y H:i:s'); ?>" style="cursor: help;">
					<i class="fas fa-clock fa-fw"></i>
					<?php echo time_elapsed_string(@$ChatLast->Dates); ?>
					</span>
				<?php
					if (@$ChatLast->ToUserId != Auth::user()->id) {
					if (@$ChatLast->Notification == '1' && @$ChatLast->Status == '0') {
						echo '<i class="fa fa-check" aria-hidden="true"></i>';
						echo '<i class="fa fa-check" aria-hidden="true" style="margin-right: -5px;"></i>';
					}
					else {
					if (@$ChatLast->Status == '0') {
						echo '<i class="fa fa-check" aria-hidden="true"></i>';
					}
					else {
						echo '<span data-toggle="tooltip" data-placement="top" title="" data-original-title="'.with(new DateTime(@$ChatLast->StatusTime))->format('d/m/Y H:i:s').'" style="cursor: help;color:#48AD42;">';
						echo '<i class="fa fa-check" aria-hidden="true"></i>';
						echo '<i class="fa fa-check" aria-hidden="true" style="margin-right: -5px;"></i>';
						echo '</span>';
					}
					}
					}
				?>
					</small>
     
     
     
     
     
</td></tr></table>		</div>
 	</div>    
  </div>
  </a></li>
  <?php }	} ?>
    
<?php endif ?>    
    
	  </ul>
	  
	 
<script>
	
	$(document).ready(function(){
	<?php if (@$_GET['u'] > 0) { ?>
	var offset = $('#MenuChatId<?php echo @$_GET['uU']; ?>').offset();
    $(".ChatBlockGoTop").scrollTop(offset.top-330);
	<?php } ?>
	});

$(".profileimage").attr('onerror',"this.src='../assets/img/21122016224223511960489675402.png'");
    
    $(function() {
	$('[data-toggle="tooltip"]').tooltip()
});
</script>
