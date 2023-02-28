<?php

require_once '../../app/initcron.php';

$Skip = $_REQUEST['skip'] ?? 0;

$CompanyNum = Auth::user()->CompanyNum;

$UserId = Auth::user()->id;
$Today = date('Y-m-d');
$TodayTime = date('H:i:s');
$notifications = DB::table('appnotification')
->where('Date', '=', $Today)->where('Time', '<=', $TodayTime)->where('Status', '=', '0')->where('Type', '=', '3')->where('CompanyNum', '=', $CompanyNum)
->Orwhere('Date', '<', $Today)->where('Status', '=', '0')->where('Type', '=', '3')->where('CompanyNum', '=', $CompanyNum)	
->orderBy('Date', 'DESC')->orderBy('Time', 'DESC')->skip($Skip)->take(50)->get();
$resultcount = count($notifications);




?>


<?php if (!empty($notifications)){  foreach($notifications as $notification){

$ClientInfo = DB::table('client')->where('id', '=', $notification->ClientId)->where('CompanyNum', '=', $CompanyNum)->first(); 
 	
	
?>
    
   <div class="AlertCloseMe">    
   <div class="alertb alert-light text-dark " >   
   <div class="row align-items-center">
   <div class="col-md-12">       
   <span><?php echo $notification->Subject; ?> <?php if ($notification->ClientId=='0'){} else { ?> // <a href="/office/ClientProfile.php?u=<?php echo @$notification->ClientId; ?>"><?php echo @$ClientInfo->CompanyName; ?></a> <?php } ?></span>
       
    <br>
 <span><?php echo $notification->Text; ?></a></span>
       
   </div>   
    </div>
       
       
   <div class="row align-items-center">

   <div class="col-6">
   <select name="StatusEvent" onchange="notifications.markAsRead(this)" id="StatusEventReminder" data-placeholder="בחר סטטוס" class="form-control form-control-sm StatusEventReminder w-auto"
           style="border-color: #f3f3f4; background-color: #f3f3f4">
   <option value="<?php echo $notification->id ?>:0" <?php echo $notification->Status == '0' ? 'selected' : '' ?> ><?= lang('active') ?></option>
   <option value="<?php echo $notification->id ?>:1" <?php echo $notification->Status == '1' ? 'selected' : '' ?> ><?= lang('mark_as_read_notification') ?></option>
   </select>
   </div>

    <div class="col-6 text-end">
       <small><?php echo with(new DateTime(@$notification->Date))->format('d/m/Y'); ?> | <?php echo with(new DateTime(@$notification->Time))->format('H:i'); ?></small>
    </div>

    </div>



    </div> 
    <hr>     
    </div>

<?php } ?>
    <?php if ($resultcount == 50) { ?>
            <div id="notifloader<?php echo $resultcount ?>" class="loader-container" data-skip="<?php echo $Skip + $resultcount ?>">
                <span id="notifmore<?php echo $CompanyNum ?>" data-ajax="<?php echo $CompanyNum ?>" class="show_more" title="<?= lang('load_more') ?>..." > <?= lang('load_more') ?> <i class="fas fa-caret-down"></i></span>
                <span id="notifloading<?php echo $CompanyNum ?>" class="loading" style="display: none;"><span class="loading_txt" > <?= lang('loading')?> <i class="fas fa-spinner fa-pulse"></i></span></span>
            </div>
    <?php } ?>
<?php }  else { echo '<div class="text-center"><span class="font-weight-bold">'.lang('no_new_notifications_modal').'</span></div>'; } ?>
