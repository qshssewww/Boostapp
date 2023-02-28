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
            return lang('one_minute_chat');
        }
        else{
            return lang('before_single'). ' '.$minutes.' '.lang('minutes');
        }
    }
    //Hours
    else if($hours <=24){
        if($hours==1){
            return lang('one_hour_chat');
        }else{
            return lang('before_single').' '.$hours.' '.lang('minutes');
        }
    }
    //Days
    else if($days <= 7){
        if($days==1){
            return lang('yesterday');
        }else{
            return lang('before_single').' '.$days.' '.lang('minutes');
        }
    }
    //Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            return lang('one_week_chat');
        }else{
            return lang('before_single').' '.$weeks.' '.lang('minutes');
        }
    }
    //Months
    else if($months <=12){
        if($months==1){
            return lang('one_month_chat');
        }else{
            return lang('before_single').' '.$months.' '.lang('minutes');
        }
    }
    //Years
    else{
        if($years==1){
            return lang('year_ago_chat');
        }else{
            return lang('before_single').' '.$years.' '.lang('minutes');
        }
    }
}
?>
<?php 
	$CurrentUserId = @$_REQUEST['U'];

	$CurrentChatWindow = DB::table('client')->where('CompanyNum' ,'=', $CompanyNum)->where('id', '=', @$CurrentUserId)->first();
	if (!empty($CurrentChatWindow) || @$CurrentUserId=='0') {
		
	if (@$CurrentUserId=='0') {
	$CurrentChatUserId = '0';
	$CurrentChatUserName = lang('general_notice_chatbox');
	$CurrentChatUserEmail = '0';
	$MyChatUserEmail = Auth::user()->email;
	}
	else {
	$CurrentChatUserId = @$CurrentChatWindow->id;
	$CurrentChatUserName = @$CurrentChatWindow->CompanyName;
	$CurrentChatUserEmail = @$CurrentChatWindow->Email;
	$MyChatUserEmail = Auth::user()->email;
	}
?>

    <div class="card spacebottom">
    <div class="card-header text-start">
    <i class="fas fa-comments"></i> <b><?php echo lang('chat_header') ?> :: <a href="ClientProfile.php?u=<?php echo @$CurrentChatUserId; ?>" class="text-primary"><?php echo @$CurrentChatUserName; ?></a></b>
 	</div>    
  	<div class="card-body" style="padding: 0px;"> 
  	<div id="frame">
  	<div class="content" style="margin-top: 0;padding-top: 0;">  
  			<div class="message-input" style="position: absolute;width: 100%;margin-top: 32px; top:0;">
			<form action="ChatSend" id="ChatSend" class="ajax-form clearfix">
			<input type="hidden" name="UserId" value="<?php echo @$CurrentChatUserId; ?>">

    <div class="form-group" style="border-radius: 0px; cursor: pointer;margin-top: 0;padding-top: 0;">
   <div class="input-group" style="position: absolute;;">
  <div class="input-group-icon" style="padding:5px;text-align: center; vertical-align: middle;"<center><span id="SendChatButton"><i class="fab fa-telegram-plane fa-fw text-primary" style=""></i></span></center></div>
  <div class="input-group-area"><input id="SendTrueChat" autocomplete="off" type="text" name="Content" class="form-control text-start" placeholder="<?php echo lang('type_message_chatbox') ?>" required autofocus style="padding: 0;margin:0;padding:5px;width: 100%;border-radius: 0;"></div>
</div>
				</div>
			
				
		  	  
			</form>
		</div>
		
		
		
	<div class="messages" style="padding-top:37px;">
			<ul style="padding-left:5px;padding-right:5px;padding-top:10px;height:420px;overflow-y: scroll;">
		<div id="ChatBoxContent"></div>
  			</ul>

		</div>		

		<script>
            
        	$('#SendChatButton').on('click', function(){
		$('#SendTrueChat').submit();
	});    
            
		$(document).ready(function(){
		var ChatBoxRefreshVar;
		function ChatBoxRefresh() {
		 $.ajax({
            url: 'action/ChatBoxContent.php?U=<?php echo @$CurrentUserId; ?>',
            type: 'POST',
            data: '',
            success: function(data) {
              $('#ChatBoxContent').html(data);
				
            }
        });
		}
		//ChatBoxRefreshVar = setInterval(ChatBoxRefresh, 10000);
		ChatBoxRefresh();
		$(".profileimage").attr('onerror',"this.src='../assets/img/21122016224223511960489675402.png'");
		});
            
            
          TrueFixUserId = <?php echo @$CurrentUserId; ?>;
        var ChatCheckNewMessagesVar;
        ChatCheckNewMessagesVar = setInterval( function () {
            ChatCheckNewMessages( TrueFixUserId );
        }, 10000 );          
            
    function ChatCheckNewMessages( FixUserId ) {

        var url = 'action/ChatBoxCheck.php?U=' + FixUserId;
        $( '#ChatBoxCheck' ).load( url, function ( e ) {
            $( '#SendTrueChat' ).focus();
        } );

    }    
            
            
		</script>	

		</div></div>
	</div>
    </div>
    <?php } else { ?>
    <div class="card spacebottom">
    <div class="card-header text-start">
    <i class="fas fa-comments"></i> <b><?php echo lang('chat_header') ?></b>
 	</div>    
  	<div class="card-body" style="padding: 0px;height:500px;"> 
  	<div class="text-start" style="padding: 10px;">
  	<?php echo lang('slect_customer_chatbox') ?>
	</div>
	</div>
	</div>
    <?php } ?>



<script>
$(function() {
	$('[data-toggle="tooltip"]').tooltip()
});
</script>


