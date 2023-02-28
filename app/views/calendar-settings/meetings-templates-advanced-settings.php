<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-meetings-templates-advanced-settings
 d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="4">

  <a class="text-black d-flex text-decoration-none text-start p-0 mie-30 mb-20"
     onclick="meetingTemplate.backToAdvancedSettings(this, 'calendarSettings-meetings-templates-new')" >
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <h5 class="back-button-text d-flex align-items-start font-weight-bolder">
        <?=lang('back_single') ?>
    </h5>
  </a>

  <!-- Start of Scrollable area -->
  <div class="scrollable">
    <div class="pb-50">

        <!-- External registration -->
        <ul class="d-none list-unstyled p-0 tab-pane fade js-subpage-tabs" id="external-registration-section">
            <li class="mb-15">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('external_registration_option') ?>
                </h6>
                <select class="js-select2-dropdown-arrow-template external-registration-status" required
                        onchange="meetingTemplate.externalRegistrationStatusChanged(this)" name="ExternalRegistration">
                    <option data-text='<?=lang('cant_register_ext')?>' value=0><?=lang('no')?></option>
                    <option data-text='<?=lang('yes')?>' value=1 selected><?=lang('yes')?></option>
                </select>
            </li>
            <li class="mb-15 js-registration-limited-to">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('who_can_register') ?>
                </h6>
                <select class="js-select2-dropdown-arrow-template registration-limited-to" name="RegistrationLimitedTo" required
                        onchange="meetingTemplate.registrationLimitedToChanged(this)">
                    <option data-text='<?=lang('allows_everyone_order')?>' value=0 selected><?=lang('everyone') ?> </option>
                    <option data-text='<?=lang('only_to_women')?>' value=2><?=lang('only_women')?> </option>
                    <option data-text='<?=lang('only_to_men')?>' value=1><?=lang('only_men') ?> </option>
                </select>
            </li>
        </ul>

        <!-- Sessions limit -->
        <ul class="d-none list-unstyled p-0 tab-pane fade js-subpage-tabs" id="sessions-limit-section">
            <li class="mb-15">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('limit_session_number_daily') ?>
                </h6>
                <select class="js-select2-dropdown-arrow-template sessions-limit-status" required
                        onchange="meetingTemplate.sessionsLimitStatusChanged(this)" name="SessionsLimitType">
                    <option data-text='<?=lang('unlimited')?>' value=0><?=lang('no')?></option>
                    <option data-text='<?=lang('yes')?>' value=1 selected><?=lang('yes')?></option>
                </select>
            </li>
            <li class="mb-15 js-sessions-limit-number">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('number_of_meetings') ?>
                </h6>
                <select class="js-select2-dropdown-arrow-template sessions-limit-number" name="SessionsLimit" required
                        onchange="meetingTemplate.sessionsLimitNumberChanged(this)">
                    <?php for ($i = 1; $i <= 20; $i++) : ?>
                        <option data-text='<?=$i . " " . lang('max_js') ?>' value='<?=$i;?>' <?= 4 == $i ? 'selected' : '' ?>><?= $i; ?></option>
                    <?php endfor; ?>
                </select>
            </li>
        </ul>

        <!-- Coaches limit -->
        <ul class="d-none list-unstyled p-0 mt-5 tab-pane fade js-subpage-tabs leastOneChoice" id="coaches-limit-section">
            <li class="mb-15">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('who_is_it_for') ?>
                </h6>
                <div class="text-danger d-none is-invalid">
                    <span><?=lang('must_select_at_least_one_option')?></span>
                </div>
                <div>
                    <div class="py-10 d-flex justify-content-between position-relative">
                        <div class="d-flex ">
                            <div class="bsapp-fs-18"><?=lang('all_coaches') ?></div>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" id="js-all-coaches-selected" name="AllCoaches" required checked
                                   class="custom-control-input js-select-all" onchange="meetingTemplate.allCoachesSelectedChanged(this)">
                            <label class="custom-control-label" for="js-all-coaches-selected">
                            </label>
                        </div>
                    </div>
                </div>
                <hr class='mt-5' style="height:10px;border:none;background-color:#F5F5F5;">
                <div class="coaches-list">
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <div class=" py-10 border-light border-bottom mb-10 d-flex justify-content-between position-relative">
                            <div class="d-flex ">
                                <img src="assets/images/cover.jpg"  class="w-40p h-40p mie-10 rounded-circle"/>
                                <div class="bsapp-fs-18 coach-name">coach <?=$i; ?></div>
                            </div>
                            <div class="custom-control custom-checkbox custom-control-inline">
                                <input type="checkbox" id="js-coach-id-<?=$i; ?>" name="CoachId[]" required checked
                                       onchange="meetingTemplate.coachSelectedChanged(this)"
                                       value=<?=$i; ?> class="custom-control-input">
                                <label class="custom-control-label" for="js-coach-id-<?=$i; ?>">
                                </label>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </li>
        </ul>

        <!-- Calendar limit -->
        <ul class="d-none list-unstyled p-0 mt-5 tab-pane fade js-subpage-tabs leastOneChoice" id="calendars-limit-section">
            <li class="mb-15">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('who_is_it_for') ?>
                </h6>
                <div class="text-danger d-none is-invalid">
                    <span><?=lang('must_select_at_least_one_option')?></span>
                </div>
                <div>
                    <div class="py-10 d-flex justify-content-between position-relative">
                        <div class="d-flex ">
                            <div class="bsapp-fs-18"><?=lang('all_calendar') ?></div>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" id="js-all-calendars-selected" name="AllCalendars" value="1" required checked
                                   class="custom-control-input js-select-all" onchange="meetingTemplate.allCalendarsSelectedChanged(this)">
                            <label class="custom-control-label" for="js-all-calendars-selected">
                            </label>
                        </div>
                    </div>
                </div>
                <hr class='mt-5' style="height:10px;border:none;background-color:#F5F5F5;">
                <div class="calendar-list">
                    <!--  brands-->
                    <?php $num =0; ?>
                    <?php for ($i = 1; $i < 4; $i++): ?>
                        <div>
                            <div class="d-flex justify-content-center border-bottom">
                                <span class="py-5">סניף מספר  <?=$i; ?></span>
                            </div>
                            <?php for ($j = 0; $j < 4; $j++ ,$num++): ?>
                                <div class=" py-10 border-light border-bottom mb-10 d-flex justify-content-between position-relative">
                                    <div class="d-flex ">
                                        <div class="bsapp-fs-18 calendar-name">calendar <?=$num; ?></div>
                                    </div>
                                    <div class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" id="js-calendar-id-<?=$num; ?>" checked
                                               onchange="meetingTemplate.calendarsSelectedChanged(this)"
                                               name="CalendarId[]" required value=<?=$num; ?> class="custom-control-input">
                                        <label class="custom-control-label" for="js-calendar-id-<?=$num; ?>">
                                        </label>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </li>
        </ul>

        <!-- Online options -->
        <ul class="d-none list-unstyled p-0 tab-pane fade js-subpage-tabs" id="online-options-section">
            <li class="mb-15">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('type_meeting') ?>
                </h6>
                <select class="js-select2-dropdown-arrow-template online-options-type" required
                        onchange="meetingTemplate.onlineTypeChanged(this)" name="MeetingType">
                    <option data-text='<?=lang('physical_meeting')?>' value=0 selected><?=lang('physical_meeting')?></option>
                    <option data-text='<?=lang('online_footernew')?>' value=1><?=lang('online_footernew')?></option>
                    <option data-text='<?=lang('zoom_footernew')?>' value=2 ><?=lang('zoom_footernew')?></option>
                </select>
            </li>

            <!--online section-->
            <li class="mb-15 not-physical-meeting js-online-send-info d-none">
                <div class="mb-15">
                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                        <?php echo lang('online_class_link') ?>
                    </h6>
                    <input type="url" placeholder="https://example.com" name="LiveClassLink" dir="ltr"
                           class="form-control bg-light border rounded shadow-none m-0 px-10 live-class-link"/>

                </div>
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                <?php echo lang('means_sending_link') ?>
                </h6>
                <select class="js-select2-dropdown-arrow-template online-send-type" required
                        onchange="meetingTemplate.onlineReminderValueChanged(this)" name="OnlineSendType">
                    <option data-text='<?=lang('mail_short_link')?>' value=2 selected><?=lang('mail_short_link')?></option>
                    <option data-text='<?=lang('reports_sms')?>' value=1><?=lang('reports_sms')?></option>
<!--                    <option data-text='--><?//=lang('mail_sms')?><!--' value=3 >--><?//=lang('mail_sms')?><!--</option>-->
                </select>
                <div class="my-15">
                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                        <?=lang('schedule_send_link') ?>
                    </h6>
                    <div class="mb-15 row">
                        <div class="form-group col-3">
                            <input required type="text" pattern="\d*" maxlength="4" name="OnlineReminderValue" value="" required
                                   onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"
                                   onchange="meetingTemplate.onlineReminderValueChanged(this)"
                                   class="online-reminder-value form-control bg-light border rounded shadow-none m-0 p-0 text-center">
                        </div>
                        <div class="form-group col-9">
                            <select class="js-select2-dropdown-arrow-template online-reminder-type" required
                                    onchange="meetingTemplate.onlineReminderValueChanged(this)" name="OnlineReminderType">
                                <option data-text='<?=lang('minutes_before_meeting')?>' value=0><?=lang('minutes_before_meeting')?></option>
                                <option data-text='<?=lang('hours_before_meeting')?>' value=1 selected><?=lang('hours_before_meeting')?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </li>
            <!--Zoom section-->
            <li class="mb-15 not-physical-meeting js-zoom-details d-none">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('meeting_number') ?>
                </h6>
                <input type="text" name="ZoomMeetingNumber" onchange="meetingTemplate.zoomDetailsChanged(this)"
                       class=" form-control bg-light border rounded shadow-none m-0 py-2 px-10 zoom-meeting-number"
                       maxlength="100"
                       id="zoom-meeting-number" placeholder="<?=lang('meeting_number') ?>" required">
                <div class="my-15">
                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                        <?=lang('password') ?>
                    </h6>
                    <input type="text" onchange="meetingTemplate.zoomDetailsChanged()" name="ZoomMeetingPassword"
                           class="zoom-meeting-password form-control bg-light border rounded shadow-none m-0 py-2 px-10"
                           id="zoom-password" maxlength="45" placeholder="<?=lang('password') ?>" required">
                </div>
            </li>

        </ul>

        <!-- Preparation time -->
        <ul class="d-none list-unstyled p-0 tab-pane fade js-subpage-tabs" id="preparation-time-section">
            <li class="mb-15">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                     <?=lang('add_preparation_time') ?>
                </h6>
                <select class="js-select2-dropdown-arrow-template preparation-time-status" required
                        onchange="meetingTemplate.preparationStatusChanged(this)" name="PreparationTimeStatus">
                    <option data-text='<?=lang('without')?>' value=0 selected><?=lang('without')?></option>
                    <option data-text='<?=lang('before_each_event')?>' value=1 ><?=lang('before_each_event')?></option>
                    <option data-text='<?=lang('after_each_event')?>' value=2><?=lang('after_each_event')?></option>
                </select>
            </li>
            <!--Preparation info-->
            <li class="mb-15 js-preparation-info d-none">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('add_time') ?>
                </h6>
                <div class="row">
                    <div class="form-group col-3">
                        <input name="PreparationTimeValue" onchange="meetingTemplate.preparationInfoChanged(this)"
                               value="10" required type="text" pattern="\d*" maxlength="4"
                               onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"
                               class="preparation-time-value form-control bg-light border rounded shadow-none m-0 p-0 text-center">
                    </div>
                    <div class="form-group col-9">
                        <select class="js-select2-dropdown-arrow-template preparation-time-type" required
                                onchange="meetingTemplate.preparationInfoChanged(this)" name="PreparationTimeType">
                            <option data-text='<?=lang('minutes')?>' value=0 selected><?=lang('minutes')?></option>
                            <option data-text='<?=lang('hours')?>' value=1><?=lang('hours')?></option>
                        </select>
                    </div>
                </div>
            </li>

        </ul>

        <!-- More info -->
        <ul class="d-none list-unstyled p-0 tab-pane fade js-subpage-tabs" id="more-info-section">
            <li class="mb-15">
                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                    <?=lang('more_info_placeholder')?>
                </h6>
                <div class="form-group">
                    <textarea class="form-control" id="more-info-text" name="MoreInfoText" rows="12"
                    onkeyup="globalCalendarSettings.changeDirection(this)">
                    </textarea>
                </div>
            </li>
        </ul>






    </div>
  </div>


</div>
