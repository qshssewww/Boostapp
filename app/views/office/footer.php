	</div> <!-- main -->
</div> <!-- container-fluid -->
<div class="footer">
	<div class="container">
		<p>כל הזכויות שמורות &copy; <?php echo date('Y', time()) .' :: '. Config::get('app.name'); ?></p>
	</div>
</div>

<?php echo View::make('modals.load')->render() ?>


<?php if (Auth::check()): ?>
<?php $CompanySettingsBottom = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first(); ?>




 <div id="ReminderCheck"></div> 
  
  
   <!-- Notification Modal -->
	<div class="ip-modal text-right" id="no-open-modal">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close" title="Close" style="float: <?php _e('main.floatLeft') ?>;" data-dismiss="modal">&times;</a>
				<h4 class="ip-modal-title" id="DetailsTitle">התראות</h4>
                
				</div>

<div class="ip-modal-body">
<?php
$CompanyNum = Auth::user()->CompanyNum; 
             
				
$UserId = Auth::user()->id;
$Today = date('Y-m-d');
$TodayTime = date('H:i:s');
$notifications = DB::table('appnotification')
->where('Date', '=', $Today)->where('Time', '<=', $TodayTime)->where('Status', '=', '0')->where('Type', '=', '3')->where('CompanyNum', '=', $CompanyNum)
->Orwhere('Date', '<', $Today)->where('Status', '=', '0')->where('Type', '=', '3')->where('CompanyNum', '=', $CompanyNum)
->orderBy('Date', 'DESC')->orderBy('Time', 'DESC')->get(); 
$resultcount = count($notifications);

?>

<div id="NotificationPOP" style="height: 350px; overflow-y: scroll; overflow-x:hidden; padding:10px;">

<?php if (!empty($notifications)){  foreach($notifications as $notification){ 

$ClientInfo = DB::table('client')->where('id', '=', $notification->ClientId)->where('CompanyNum', '=', $CompanyNum)->first(); 
    
	
?>
    
   <div class="AlertCloseMe">    
   <div class="alertb alert-light text-dark text-right" >   
   <div class="row align-items-center">
   <div class="col-md-12">       
   <small><?php echo @$notification->Subject; ?> <?php if ($notification->ClientId=='0'){} else { ?> // <a href="/office/ClientProfile.php?u=<?php echo @$notification->ClientId; ?>"><?php echo @$ClientInfo->CompanyName; ?></a> <?php } ?> </small>
       
    <br>
 <small><?php echo $notification->Text; ?></a></small>    
       
   </div>   
    </div>
       
       
   <div class="row align-items-center">

   <div class="col-md-6">       
   <small><?php echo with(new DateTime(@$notification->Date))->format('d/m/Y'); ?> | <?php echo with(new DateTime(@$notification->Time))->format('H:i'); ?></small>
   </div> 
       
   <div class="col-md-6">       
   <select name="StatusEvent" id="StatusEventReminder" data-placeholder="בחר סטטוס" class="form-control form-control-sm StatusEventReminder" style="width:100%;">
   <option value="<?php echo $notification->id ?>:0" <?php if ($notification->Status=='0'){ echo 'selected'; } else {} ?>>פעיל</option>
   <option value="<?php echo $notification->id ?>:1" <?php if ($notification->Status=='1'){ echo 'selected'; } else {} ?>>סמן כנקרא</option>     
   </select>
   </div>  
       
 
       
    </div>   
       
       
       
    </div> 
    <hr>     
    </div>


<?php } }  else { echo 'אין התראות חדשות'; }  ?>

</div>

				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <a class="btn btn-dark text-white readall" data-dismiss="modal">סמן הכל כנקרא</a>
                </div>
                     
                <a  class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php _e('main.close') ?></a> 
                <a href="LogNotification.php" class="btn btn-primary text-white ip-close" data-dismiss="modal">דוח התראות</a>     
				 
                
				</div>
			</div>
		</div>
	</div>
	<!-- end Notification Modal -->
	
	
	
	
	
	
	
	
	
	
	
	<!-- מודל בחירת פריט -->
	<div class="ip-modal" id="ChooseItem">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close" title="Close" style="float: <?php _e('main.floatLeft') ?>;" data-dismiss="modal">&times;</a>
				<h4 class="ip-modal-title">בחירת סניף</h4>
				</div>
				<div class="ip-modal-body">
				<select style="margin-right: 0;padding-right: 0;" name="ProductSearchTop" id="ProductSearchTop" class="form-control select2 ProductSearchTop"></select>
				</div>
				<div class="ip-modal-footer">
                <a class="btn btn-light ip-close" data-dismiss="modal"><?php _e('main.close') ?></a>     
				</div>
			</div>
		</div>
	</div>
	<!-- מודל בחירת פריט -->
	
<?php if (Auth::user()->role_id == '1') { ?>
	
	<!-- מודל בחירת חברה -->
	<div class="ip-modal" id="ChooseCompanyNum">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close" title="Close" style="float: <?php _e('main.floatLeft') ?>;" data-dismiss="modal">&times;</a>
				<h4 class="ip-modal-title">החלפת חברה</h4>
				</div>
				<div class="ip-modal-body">
 				
                <form action="SupportChangeCompanyNum"  class="ajax-form clearfix" dir="rtl" autocomplete="off">
                <div class="form-group">
                <label><strong>בחירת חברה</strong></label>
                <select class="CompanyNumSelect" name="CompanyNum" id="CompanyNumSelect"></select>
                </div>
				</div>
                
                
                <div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" class="btn btn-primary text white">עדכן</button>
                </div>
                </form>    
               <a class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php _e('main.close') ?></a> 
                
				</div>
                

				</div>
				
			</div>
		</div>
	<!-- מודל בחירת חברה -->
	
	
<script>
	$(".CompanyNumSelect").on("select2:unselect", function(e) {
		$(".ItemCompanySelect").select2("val", "");
 	});
	$('.CompanyNumSelect').select2({
		theme:"bootstrap", 
		placeholder: "בחר חברה",
		language: "he",
		allowClear: true,
		width: '100%',
  		ajax: {
			url: '/office/action/CompanyNumSelect.php',
    		dataType: 'json'
  		},
	});
	</script>
	
	
	
	
	
<?php } ?>		
	
<!-- מודל לקוח חדש -->
<div class="ip-modal text-right" role="dialog" id="AddNewClient" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>
				<h4 class="ip-modal-title">לקוח חדש</h4>

				</div>
				<div class="ip-modal-body">
				<form action="AddClient" class="ajax-form text-right" autocomplete="off">
				<div id="ResultAddNewClient"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
				</form>
				</div>
			</div>
		</div>
	</div>
<!-- מודל לקוח חדש -->


	<!-- מודל שעון נוכחות -->
	<div class="ip-modal" id="UsersClock">
		<div class="ip-modal-dialog" <?php _e('main.rtl') ?>>
			
			<div class="ip-modal-content text-right">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close" title="Close" style="float: <?php _e('main.floatLeft') ?>;" data-dismiss="modal">&times;</a>
				<h4 class="ip-modal-title">שעון נוכחות</h4>
				</div>
				<div class="ip-modal-body">
 				<div align="center" style=" font-size:24px; font-weight:bold;">
<span id="date_times"></span>
</div>

                    
                    
                <form action="AddUsersClock"  class="ajax-form clearfix" dir="rtl" autocomplete="off">
                  <input type="hidden" name="UserId" value="BA999">  
                 <div align="center" style=" padding:15px;">  
                  <div class="btn-group" data-toggle="buttons" dir="ltr">
                  <label class="btn btn-warning  btn-lg">
                    <input type="radio" name="options" id="option2" value="1" autocomplete="off"> יציאה
                  </label>
                  <label class="btn btn-success btn-lg active">
                    <input type="radio" name="options" id="option1" value="0" autocomplete="off"  checked> כניסה
                  </label>
                </div>
                </div>    

				</div>
                
                
                <div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" class="btn btn-primary text white">שמור</button>
                </div>
                </form>    
               <a class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php _e('main.close') ?></a> 
                
				</div>
                

				</div>
				
			</div>
		</div>
	<!-- מודל שעון נוכחות -->
	



<!-- מודל לקוח חדש -->
<div class="ip-modal text-right" role="dialog" id="AddNewClass" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close ClassClosePopUp" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>
				<h4 class="ip-modal-title">הוספת שיעור</h4>

				</div>
				<div class="ip-modal-body">
				<form action="AddClassNewPopUp" id="AddClassNewPop" class="ajax-form needs-validation" novalidate autocomplete="off">
				<div id="ResultAddNewClass"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
				</form>
				</div>
			</div>
		</div>
	</div>
<!-- מודל לקוח חדש -->


	
<!-- מודל פעילות חדשה -->
<div class="ip-modal text-right" role="dialog" id="AddNewCal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header"  <?php _e('main.rtl') ?>>
                <a class="ip-close" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>
				<h4 class="ip-modal-title">הגדרת משימה</h4>

				</div>
				<div class="ip-modal-body">
				<form action="AddCalendarClient" class="ajax-form text-right" autocomplete="off">
				<div id="ResultAddNewCal"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
				</form>
				</div>
			</div>
		</div>
	</div>
<!-- מודל פעילות חדשה -->

<?php
        echo "\n\t";

        global $headerJs;
        $headerJs = array_merge(
            [
                'CDN/fontawesome/js/fontawesome-all.min.js',
                'assets/js/date_time.js',
                'assets/office/js/vendor/jquery-1.11.1.min.js',
                ['src'=>'//code.jquery.com/ui/1.11.4/jquery-ui.min.js'],
                ['src'=>'//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js'],
                'CDN/bootstrap/popper.min.js',
                'CDN/bootstrap/bootstrap.min.js',
                'assets/office/js/BeePOS.js',
                'assets/office/js/jquery.Jcrop.min.js',
                'assets/office/js/jquery.imgpicker.js',
                'office/assets/js/jquery.ui.touch-punch.min.js',
                'assets/office/js/main.js',
                'assets/office/js/jquery.simpleWeather.min.js',
                'assets/js/jqueryExecuting.js',
                'office/assets/js/bootstrap-notify.js',
                'office/tinybox2/tinybox.js',
                'CDN/select2/select2.min.js',
                'CDN/select2/he.js',
            ], (is_array($headerJs))?$headerJs:[]
        );
        foreach($headerJs as $url):
            if(!is_array($url)):
                printf("<script type=\"text/javascript\" src=\"%s?v=%d\"></script>\n\t", app()->url($url), filemtime(app_path('../'.$url)));
            else:
                printf("<script type=\"text/javascript\" src=\"%s\"></script>\n\t", $url['src']);
            endif;
        endforeach;

?>
<script>BeePOS.options = {ajaxUrl: '<?php echo App::url("ajax.php") ?>',lang: <?php echo json_encode(trans('main.js'), JSON_UNESCAPED_UNICODE) ?>,debug: <?php echo Config::get('app.debug')?1:0 ?>};</script>


<script> 

$(function() {
			var time = function(){return'?'+new Date().getTime()};
						
			$('#no-open-modal').imgPicker({
			});
			$('#ChooseItem').imgPicker({
			});
    		$('#ChooseCompanyNum').imgPicker({
			});
        	$('#UsersClock').imgPicker({
			});
    
    

	
});


 $('.ClassClosePopUp').click(function(){
    location.hash = "";
    $('#ResultAddNewClass').html("");
    $('#ResultEditNewClass').html("");
 });    
    
$(document).ready(function(){

	
	
	
//Chat check for new message
var ChatCheckNewMessagesVar;
function ChatCheckNewMessages() {
    $.get("./office/action/ChatNewMessage.php", function(data) {
        if(data >= 1) {
			var formattedNumber = parseInt(data, 10);
			$('#ChatCountHeader').html('<span style="position: absolute;" class="badge badge-pill badge-danger" dir="rtl">'+ formattedNumber +'</span>');
            //There are new messages
            //clearInterval(ChatCheckNewMessagesVar);			
			
$.ajax({
	url:'./office/action/ChatNewMessageContent.php',
    dataType : 'json',
					
    success  : function (response) {


  for(var i = 0; i < response.length; i++) {
    var obj = response[i];
		$.notify({
            
	icon: obj.photo,
	title: obj.name,
	message: obj.message,
},{
	type: 'minimalist',
	delay: 5000,
	icon_type: 'image',
	
	template: '<div data-notify="alert" class="col-xs-11 col-sm-3 text-right alert alert-{0}" role="alert" style="line-height: 15px;direction:rtl;cursor: pointer;" onclick="location.href=\'/office/Chat.php?U=' + obj.id + '\'" >' +
		'<img data-notify="icon" class="rounded-circle float-right profileimage">' +
		'<span data-notify="title">{1}</span>' +
		'<div data-notify="message" style="height:27px;overflow: hidden;">{2}</div>' +
	'</div></a>'
});
	  	$(".profileimage").attr('onerror',"this.src='/office/assets/img/21122016224223511960489675402.png'");

}


		
		
		

    }
});

        }
		else {
			$('#ChatCountHeader').html('');
		}
    });
}
// ChatCheckNewMessagesVar = setInterval(ChatCheckNewMessages, 10000);
// ChatCheckNewMessages();
//END Chat check for new message


	
	
	
	
	
	
	
 

$(".StatusEventReminder").change(function () {
var Acts = this.value;
$(this).closest('div.AlertCloseMe').fadeOut();	
$.ajax({
type: 'POST',    
url:'/office/action/StatusChange.php',
data:'Act='+ Acts,
success: function(msg){}
});
		 
});	

    
    
//  var auto_refresh = setInterval(
//    function()
//    {
//    $('#notification').load('/office/action/notification.php').fadeIn("fast");
//    }, 10000);   
    
    
//  var auto_refresh = setInterval(
//    function()
//    {
//    $('#NotificationPOP').load('/office/action/notifications.php' + ' #Newnotifications',function(){          
    
	  
// $(".StatusEventReminder").change(function () {
// var Acts = this.value;
// $(this).closest('div.AlertCloseMe').fadeOut();	
// $.ajax({
// type: 'POST',    
// url:'/office/action/StatusChange.php',
// data:'Act='+ Acts,
// success: function(msg){}
// });
		 
// });	 
	   
	   
// $(".StatusEventNotification").change(function () {
// var Acts = this.value;
	
// $.ajax({
// type: 'POST',    
// url:'/office/action/StatusChangeNotification.php',
// data:'Act='+ Acts,
// success: function(msg){}
// });
		 
// });	 	   
	   
	   
// }).fadeIn("fast");
//    }, 25000);

$('.readall').click(function(e){
         e.preventDefault();
     $('#NotificationPOP').load('/office/action/AllRead.php?Act=0' + ' #Newnotifications').fadeIn("fast");
      });	    
    
});
    
	
	
	
	
$('.Select2OY').on('select2:open', function (e) {
      $('.Select2OY').removeClass('select2opacity');
});
$('.Select2OY').on('select2:close', function (e) {
      $('.Select2OY').addClass('select2opacity');
});
$('.ChoodeItemOY').mouseover(function() {
      $('.ChoodeItemOY').removeClass('select2opacity');
});
$('.ChoodeItemOY').mouseout(function() {
      $('.ChoodeItemOY').addClass('select2opacity');
});


    
</script>
<i class="fas fa-arrow-alt-circle-up fa-2x" onclick="topFunction()" id="BTT" title="חזור למעלה" style="position: fixed;right: 30;bottom:30;z-index: 99;display: none; cursor: pointer;"></i>


<script>
// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("BTT").style.display = "block";
    } else {
        document.getElementById("BTT").style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}
</script>

<script>
$(document).ready(function() {
  $.simpleWeather({
    location: '<?php echo @$CompanySettingsBottom->WeatherCityName; ?>',
    woeid: '',
    unit: 'c',
    success: function(weather) {
	  if(weather.temp > 26) {
        $('#weather').animate({color: '#F7AC57'}, 0);
      } else {
        $('#weather').animate({color: '#0091c2'}, 0);
      }
      html = '<span><i class="icon-'+weather.code+'"></i> '+weather.temp+'&deg;</span>';
      $("#weather").html(html);
    },
    error: function(error) {
      $("#weather").html('<span style="font-size:10px;"><span dir="ltr"><i class="icon-29"></i> <span dir="ltr">00&deg;</span></span></span>');
    }
  });
});

</script>
	  


	  <script>
		  // עיצוב תוצאות סלקט2
		  function formatClient (repo) {
  			if (repo.loading) {return repo.text;}
			  
			if (repo.name != null && repo.name != '') {var name = "<strong>שם: </strong>" + repo.name;}
			else {var name = "";}
							 
			if (repo.companyid != null && repo.companyid != '') {var companyid = "<br><strong>ת.ז: </strong>" + repo.companyid;}
			else {var companyid = "";}
							 
			if (repo.phone != null && repo.phone != '') {var phone = "<br><strong>סלולרי: </strong>" + repo.phone;}
			else {var phone = "";}
							 
			if (repo.email != null && repo.email != '') {var email = "<br>" + repo.email;}
			else {var email = "";}
              
            if (repo.barnd != null && repo.barnd != '') {var barnd = "<br><strong>סניף: </strong>" + repo.barnd;}
			else {var barnd = "";}  
			
  			var markup = "<div style='font-size:12px;'>" + name + "" + companyid + "" + phone + "" + email + "" + barnd + "</div>";
			return markup;
		  }
		  function formatClientSelection (repo) {
			  return repo.text;
		  }
		  // עיצוב תוצאות סלקט2
		  
	  $('.ClientSearchTop').select2({
  		templateResult: formatClient,
  		templateSelection: formatClientSelection,
		theme:"bootstrap", 
		placeholder: "חפש לקוח",
	    escapeMarkup: function (markup) { return markup; },
		language: "he",
		allowClear: true,
		width: '100%',
  		ajax: {
			url: './office/action/ClientSelect.php',
    		dataType: 'json',
  		},
		minimumInputLength: 3,
	  });
	  $('.ProductSearchTop').select2({
		theme:"bootstrap", 
		placeholder: "בחר סניף",
		language: "he",
		allowClear: false,
		width: '100%',
  		ajax: {
			url: '/office/action/BrandSelect.php',
    		dataType: 'json'
  		},
	  });
	  $(document).ready(function () {
		 $('.ClientSearchTop').on('select2:select', function (e) {
	     window.location.href = '/office/ClientProfile.php?u=' +  $(this).val();
		 });
          
		 $('.ProductSearchTop').on('select2:select', function (e) {
			 var SelectedItem = $(this).val();
			 $.ajax({
				 type: 'POST',    
				 url:'/office/action/UpdateBrandSelected.php?BrandId='+SelectedItem,
				 success: function(msg){
					 BN('0', 'הפעולה בוצעה בהצלחה!');
	     			 location.reload()
				 },
				 error: function(xhr, status, error) {
				 	BN('1', 'אופס, לא ניתן לעדכן את הסניף.');
				 }
			 });

		 });
	  <?php $ItemDetailsFooter = DB::table('brands')->where('id', Auth::user()->ItemId)->where('CompanyNum', '=', Auth::user()->CompanyNum)->first(); ?>
	  $(".ProductSearchTop").append('<option value="<?php echo @$ItemDetailsFooter->id; ?>" selected="selected"><?php echo @$ItemDetailsFooter->BrandName; ?></option>').trigger('change');
	  });
		  
		  
		  
	  </script>


<?php else: endif; ?>



<script>
	(function($){
		$.ajaxSetup({
			beforeSend: function(xhr, settings){
				if(settings && settings.url && settings.url.match(/api\.boostapp\.co\.il/)){
					for(var key in $.ajaxSettings.headers){
						xhr.setRequestHeader(key, null)
					}
					xhr.setRequestHeader('x-cookie', 'localhost-dev')
					xhr.setRequestHeader('Content-Type', 'application/json')
				}
			}
		});

		// $.get('<?php echo get_loginboostapp_domain() ?>/api/client/activity?report=true').done(function(data){console.log(data)})
		$.get(<?php echo App::url("api/client/activity?report=true") ?>).done(function(data){console.log(data)})
	})(jQuery);
</script>



</body>
</html>