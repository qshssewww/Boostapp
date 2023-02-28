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

	$CurrentChatWindow = DB::table('client')->where('CompanyNum' ,'=', $CompanyNum)->where('id', '=', @$CurrentUserId)->first();
		
	if (@$CurrentUserId=='0') {
	$CurrentChatUserId = '0';
	$CurrentChatUserName = 'הודעה כללית';
	$CurrentChatUserEmail = @$CurrentChatWindow->Email;
	$MyChatUserEmail = Auth::user()->email;
	}
	else {
	$CurrentChatUserId = @$CurrentChatWindow->id;
	$CurrentChatUserName = @$CurrentChatWindow->CompanyName;
	$CurrentChatUserEmail = @$CurrentChatWindow->Email;
	$MyChatUserEmail = Auth::user()->email;
	}
?>

		
		
		
		
  
			
			
			
			
			<div id="ChatBoxCheck"></div>
			
			
			
			<?php
				if (@$CurrentChatUserId == '0'){
				$ChatContentBoxs = DB::table('chat')->where('CompanyNum' ,'=', $CompanyNum)->where('UserId', '=', '0')->where('AllUsers', '=', '1')->orderBy('Dates', 'DESC')->get();
				}
				else {
				$ChatContentBoxs = DB::table('chat')->where('CompanyNum' ,'=', $CompanyNum)->where('ToUserId', '=', @$CurrentChatUserId)->orderBy('Dates', 'DESC')->get();
				}
		
		        foreach($ChatContentBoxs as $ChatContentBox) {
				$TimeToDB = date('Y-m-d G:i:s');
				

		
			?>
				<li class="<?php echo $ChatContentBox->SendFrom == '0' ? 'replies text-end' : 'sent text-start'; ?>">

					<p>
					<?php echo $ChatContentBox->Content; ?>
					<br />
					<small class="text-<?php if ($ChatContentBox->SendFrom == '0') {echo 'muted';} else {echo 'white';} ?>">
					<?php
					if ($ChatContentBox->AllUsers == '1') {
						echo '<i class="fas fa-bullhorn fa-flip-horizontal"></i> &nbsp;';
					}
					?>
					<span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo with(new DateTime($ChatContentBox->Dates))->format('d/m/Y H:i:s'); ?>" style="cursor: help;">
					<i class="fas fa-clock fa-fw"></i>
					<?php echo time_elapsed_string($ChatContentBox->Dates); ?>
					</span>
				<?php
				if ($ChatContentBox->Notification == '1' && $ChatContentBox->Status == '0') {
						echo '<i class="fa fa-check" aria-hidden="true"></i>';
						echo '<i class="fa fa-check" aria-hidden="true" style="margin-right: -5px;"></i>';
					}
					else {
						if ($ChatContentBox->Status == '0') {
						echo '<i class="fa fa-check" aria-hidden="true"></i>';
						}
					else {
						echo '<span data-toggle="tooltip" data-placement="top" title="" data-original-title="'.with(new DateTime($ChatContentBox->StatusTime))->format('d/m/Y H:i:s').'" style="cursor: help;color:#48AD42;">';
                        if ($ChatContentBox->SendFrom == '0'){
						echo '<i class="fa fa-check" aria-hidden="true" class="text-primary"></i>';
						echo '<i class="fa fa-check" aria-hidden="true" style="margin-right: -5px;" class="text-primary"></i>';
                        }
                        else {  
                        echo '<i class="fa fa-check" aria-hidden="true" style="color:white;"></i>';
						echo '<i class="fa fa-check" aria-hidden="true" style="margin-right: -5px; color:white;"></i>';    
                        }
                        
						echo '</span>';
					}
					}
				?>
					</small>
					</p>
				</li>
			<?php
				}
			?>
			
			

<script>
$(document).ready(function(){
$(".profileimage").attr('onerror',"this.src='../assets/img/21122016224223511960489675402.png'");
});
    
$(function() {
	$('[data-toggle="tooltip"]').tooltip()
});
    
</script>