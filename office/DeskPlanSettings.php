<?php 
require_once '../app/init.php'; 
$pageTitle = lang('settings_calendar');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()) : ?>
  <?php if (Auth::userCan('115')) : ?>
    <?php
    $ClassSettingsInfo = DB::table('classsettings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();

    CreateLogMovement(lang('settings_calendar_log'), '0');

    ?>


    <link href="assets/css/fixstyle.css" rel="stylesheet">


      <div class="row">
        <?php include("SettingsInc/RightCards.php"); ?>

        <div class="col-md-10 col-sm-12">


          <div class="card spacebottom">
            <div class="card-header text-start d-flex justify-content-between" >
              <div>
              <i class="fas fa-calendar-alt"></i> <b><?php echo lang('desk_plan_cal') ?></b>
            </div>
            </div>
            <div class="card-body text-start">

              <form action="DeskSettings" class="ajax-form clearfix"  autocomplete="off">
                <?php if(Auth::user()->role_id == 1) { ?>
                <div class="alertb alert-info"><?php echo lang('settings_calendar_notice') ?></div>

                <div class="form-group">
                  <label><?php echo lang('class_max_participants') ?></label>
                  <input type="text" name="MaxClient" class="form-control" value="<?php echo @$ClassSettingsInfo->MaxClient; ?>" onkeypress='validate(event)' required>
                </div>

                <div class="row">
                  <div class="col-md-4">

                    <div class="form-group">
                      <label><?php echo lang('class_min_participants') ?></label>
                      <input type="text" name="MinClient" class="form-control" value="<?php echo @$ClassSettingsInfo->MinClient; ?>" onkeypress='validate(event)' required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label><?php echo lang('max_mix_time') ?></label>
                      <input type="text" name="CheckMinClient" class="form-control" value="<?php echo @$ClassSettingsInfo->CheckMinClient; ?>" onkeypress='validate(event)' required>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label><?php echo lang('option') ?></label>
                      <select class="form-control text-start" name="CheckMinClientType" id="CheckMinClientType" >
                        <option value="1" <?php if ($ClassSettingsInfo->CheckMinClientType == '1') {
                                            echo 'selected';
                                          } else {
                                          } ?>><?php echo lang('minutes') ?></option>
                        <option value="2" <?php if ($ClassSettingsInfo->CheckMinClientType == '2') {
                                            echo 'selected';
                                          } else {
                                          } ?>><?php echo lang('hours') ?></option>
                      </select>
                    </div>
                  </div>
                </div>


                <div class="form-group">
                  <label><?php echo lang('class_end_minutes') ?></label>
                  <input type="text" name="EndClassTime" class="form-control" value="<?php echo @$ClassSettingsInfo->EndClassTime; ?>" onkeypress='validate(event)' required>
                </div>

                <div class="row">
                  <div class="col-md-3">

                    <div class="form-group">
                      <label><?php echo lang('class_notification_time') ?></label>
                      <input type="text" name="ReminderTime" class="form-control" value="<?php echo @$ClassSettingsInfo->ReminderTime; ?>" onkeypress='validate(event)' required>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label><?php echo lang('option') ?></label>
                      <select class="form-control text-start" name="ReminderTimeType" id="ReminderTimeType" >
                        <option value="1" <?php if ($ClassSettingsInfo->ReminderTimeType == '1') {
                                            echo 'selected';
                                          } else {
                                          } ?>><?php echo lang('minutes') ?></option>
                        <option value="2" <?php if ($ClassSettingsInfo->ReminderTimeType == '2') {
                                            echo 'selected';
                                          } else {
                                          } ?>><?php echo lang('hours') ?></option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?php echo lang('class_notification_time_before') ?></label>
                      <input type="time" name="ReminderTimeDayBefore" class="form-control" value="<?php echo @$ClassSettingsInfo->ReminderTimeDayBefore; ?>" required>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-3">

                    <div class="form-group">
                      <label><?php echo lang('class_cancel_set') ?></label>
                      <input type="text" name="CancelTime" class="form-control" value="<?php echo @$ClassSettingsInfo->CancelTime; ?>" onkeypress='validate(event)' required>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label><?php echo lang('option') ?></label>
                      <select class="form-control text-start" name="CancelTimeType" id="CancelTimeType" >
                        <option value="1" <?php if ($ClassSettingsInfo->CancelTimeType == '1') {
                                            echo 'selected';
                                          } else {
                                          } ?>><?php echo lang('minutes') ?></option>
                        <option value="2" <?php if ($ClassSettingsInfo->CancelTimeType == '2') {
                                            echo 'selected';
                                          } else {
                                          } ?>><?php echo lang('hours') ?></option>
                      </select>
                    </div>
                  </div>


                  <div class="col-md-6">
                    <div class="form-group">
                      <label><?php echo lang('class_cancel_set_before') ?></label>
                      <input type="time" name="CancelTimeDayBefore" class="form-control" value="<?php echo @$ClassSettingsInfo->CancelTimeDayBefore; ?>" required>
                    </div>
                  </div>

                </div>

                <hr>
          <?php } ?>
          <div class="form-group">
                  <label><?php echo lang('settings_waitlist_popup') ?></label>
                  <select class="form-control text-start" name="WatingListPOPUP" id="WatingListPOPUP" >
                    <option value="0" <?php if ($ClassSettingsInfo->WatingListPOPUP == '0') {
                                        echo 'selected';
                                      } else {
                                      } ?>><?php echo lang('no') ?></option>
                    <option value="1" <?php if ($ClassSettingsInfo->WatingListPOPUP == '1') {
                                        echo 'selected';
                                      } else {
                                      } ?>><?php echo lang('yes') ?></option>
                  </select>
                </div>


                <div class="form-group">
                  <label><?php echo lang('settings_show_permanent_sign') ?></label>
                  <select class="form-control text-start" name="RegularNum" id="RegularNum" >
                    <option value="0" <?php if ($ClassSettingsInfo->RegularNum == '0') {
                                        echo 'selected';
                                      } else {
                                      } ?>><?php echo lang('yes') ?></option>
                    <option value="1" <?php if ($ClassSettingsInfo->RegularNum == '1') {
                                        echo 'selected';
                                      } else {
                                      } ?>><?php echo lang('no') ?></option>
                  </select>
                </div>


                <div class="form-group">
                  <label><?php echo lang('settings_cancel_min_class') ?></label>
                  <select class="form-control text-start" name="CancelMinimum" id="CancelMinimum" >
                    <option value="0" <?php if ($ClassSettingsInfo->CancelMinimum == '0') {
                                        echo 'selected';
                                      } else {
                                      } ?>><?php echo lang('cancel_send_notification') ?></option>
                    <option value="1" <?php if ($ClassSettingsInfo->CancelMinimum == '1') {
                                        echo 'selected';
                                      } else {
                                      } ?>><?php echo lang('dont_cancle_send_notification') ?></option>
                  </select>
                </div>


                <div class="form-group">
                  <label><?php echo lang('settings_double_guide') ?></label>
                  <select class="form-control text-start" name="GuideCheck" id="GuideCheck" >
                    <option value="0" <?php if ($ClassSettingsInfo->GuideCheck == '0') {
                                        echo 'selected';
                                      } else {
                                      } ?>><?php echo lang('no') ?></option>
                    <option value="1" <?php if ($ClassSettingsInfo->GuideCheck == '1') {
                                        echo 'selected';
                                      } else {
                                      } ?>><?php echo lang('yes') ?></option>
                  </select>
                </div>


                <div class="alertb alert-warning"><?php echo lang('calendar_settings_notice') ?></div>


                <hr>
                <div class="form-group">
                  <button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>
                </div>

            </div>
            </form>




          </div>
        </div>

      </div>
    </div>

    </div>


  <?php else : ?>
    <?php redirect_to('../index.php'); ?>
  <?php endif ?>


<?php endif ?>

<?php if (Auth::guest()) : ?>

  <?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>