<?php
const PRE_ORDER_TIME = [
    ['value' =>0 , 'type' => 'possible'],
    ['value'=> 1 , 'type' => 'hour'],
    ['value'=> 2 , 'type' => 'hour'],
    ['value'=> 3 , 'type' => 'hour'],
    ['value'=> 6 , 'type' => 'hour'],
    ['value'=> 12 ,'type' =>  'hour'],
    ['value'=> 1 , 'type' => 'day'],
    ['value'=> 2 , 'type' => 'day'],
    ['value'=> 3 , 'type' => 'day'],
    ['value'=> 6 , 'type' => 'day']
]

?>




<!-- Calendar appointments Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-meetings-general-settings d-none flex-column
 overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="Meetings-navigation">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?=lang('back_single') ?>
    </h5>
  </a>

    <h5 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-15 bsapp-fs-14">
        <i class="fal fa-th-large mie-6 text-gray-500 bsapp-fs-19"></i>
        <?=lang('path_cal_appointments_settings') ?>
    </h5>
    <h5 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-15 bsapp-fs-18">
        <?=lang('general_settings_appointment') ?>
    </h5>

    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="pb-50">
            <ul class="list-unstyled p-0 list-of-loading">
                <li class="item-loading item-placeholder mb-10 animated fadeInUp">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?></span>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="list-unstyled p-0 list-of-settings d-none">
                <li class="mb-15 border-bottom pb-10">
                    <div class="mb-6">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-6">
                            <?=lang('settings_order_from_now_to_now') ?>
                        </h6>
                        <input  type="hidden" value=1 name="PreOrder" id="pre-order-status">
                        <input  type="hidden" value=0 name="PreOrderType" id="pre-order-type">
                        <select class="js-select2-dropdown-arrow-template pre-order" required name="PreOrderTime"
                                onchange="meetingGeneralSettings.preOrderChanged(this)">
                            <?php foreach (PRE_ORDER_TIME as $item) {
                                $value = $item['value'];
                                $class = $item['type'];
                                if ( $class === 'day'){
                                    $textType = lang('days_ahead');
                                }
                                elseif ($class === 'hour') {
                                    $textType =  lang('hours_ahead');
                                } else {
                                    $textType = lang('error_admin');
                                }
                                $text = $class === 'possible' ?  lang('possible') :
                                    lang('at_least') ." " .$value . " " . $textType ; ?>
                                <option class="<?=$class?>" value=<?=$class==='day' ? 'd-'.$value : $value?>
                                    <?=3==$value&&$class==='hour' ? 'selected' : '' ?>><?=$text?></option>
                            <?php } ?>

                        </select>
                        <div class="text-gray-500 text-start px-5 bsapp-fs-13 bsapp-lh-15 mt-6">
                            <span>
                                <?=lang('settings_preorder_sub')?>
                            </span>
                        </div>
                    </div>
                </li>
                <li class="mb-15 border-bottom pb-10">
                    <div class="mb-6">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-6">
                            <?=lang('settings_slot_block_value') ?>
                        </h6>
                        <select class="js-select2-dropdown-arrow-template slot-block" required name="SlotBlockValue">
                            <option value=10 ><?= 10 . " " . lang('minutes')?></option>
                            <option value=15 selected><?= 15 . " " . lang('minutes')?></option>
                            <option value=20 ><?= 20 . " " . lang('minutes')?></option>
                            <option value=30 ><?= 30 . " " . lang('minutes')?></option>
                            <option value=60 ><?= 1 . " " . lang('hour')?></option>
                        </select>
                        <input type="hidden" value=0 name="SlotBlockType">
                        <div class="text-gray-500 text-start px-5 bsapp-fs-13 bsapp-lh-15 mt-6">
                            <span>
                                <?=lang('settings_slot_block_value_sub')?>
                            </span>
                        </div>
                    </div>
                </li>
                <li class="mb-15 border-bottom pb-10">
                    <div class="mb-6">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                            <?=lang('send_reminder_before_appointment') ?>
                        </h6>
                        <select class="js-select2-dropdown-arrow-template send-reminder-status" required
                                onchange="meetingGeneralSettings.sendReminderStatusChanged(this)" name="SendReminder">
                            <option value=0><?=lang('no')?></option>
                            <option value=1 selected><?=lang('yes')?></option>
                        </select>
                        <div class="my-8 more-details">
                            <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                <?=lang('schedule_send_reminder') ?>
                            </h6>
                            <div class="row mb-6">
                                <div class="form-group col-3 mb-6">
                                    <select class="js-select2-dropdown-arrow-template time-reminder" name="TimeReminder" required>
                                        <?php for ($i = 1; $i <= 24; $i++) : ?>
                                            <option value=<?=$i;?> <?= 2 == $i ? 'selected' : '' ?>><?= $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group col-9 mb-6">
                                    <select class="js-select2-dropdown-arrow-template send-reminder-type" required name="TypeReminder">
<!--                                        <option data-text='--><?php //echo lang('days_before_meeting')?><!--' value=1>--><?php //echo lang('days_before_meeting')?><!--</option>-->
                                        <option data-text='<?=lang('hours_before_meeting')?>' value=0 selected><?=lang('hours_before_meeting')?></option>
                                    </select>
                                </div>
                                <div class="text-gray-500 text-start px-15 bsapp-fs-13 bsapp-lh-15">
                                <span>
                                    <?=lang('settings_send_reminder_sub')?>
                                </span>
                                </div>
                            </div>
                        </div>

                    </div>
                </li>
                <li class="mb-15 border-bottom pb-10">
                    <div class="mb-6">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-6">
                            <?=lang('allow_full_payment_in_advance') ?>
                        </h6>
                        <select class="js-select2-dropdown-arrow-template allow-full-pre-payment" required name="AllowFullPrePayment">
                            <option value=0 selected><?=lang('no')?></option>
                            <option value=1><?=lang('yes')?></option>
                        </select>
                        <div class="text-gray-500 text-start px-5 bsapp-fs-13 bsapp-lh-15 mt-6">
                            <span>
                                <?=lang('allow_full_payment_in_advance_sub')?>
                            </span>
                        </div>
                    </div>
                </li>

                <li class="mb-15 border-bottom pb-10 d-none">
                    <div class="mb-6">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-6">
                            <?=lang('close_meeting_without_invoice') ?>
                        </h6>
                        <select class="js-select2-dropdown-arrow-template close-without-invoice" required name="CloseWithoutInvoice">
                            <option value=0 selected><?=lang('no')?></option>
                            <option value=1><?=lang('yes')?></option>
                        </select>
                        <div class="text-gray-500 text-start px-5 bsapp-fs-13 bsapp-lh-15 mt-6">
                            <span>
                                <?=lang('close_meeting_without_invoice_sub')?>
                            </span>
                        </div>
                    </div>
                </li>
                <li class="mb-15 border-bottom pb-10">
                    <div class="mb-6">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-6">
                            <?=lang('settings_schedule_external_order') ?>
                        </h6>
                        <select class="js-select2-dropdown-arrow-template auto-approval" required name="AutoApproval">
                            <option value=0><?=lang('only_after_approval')?></option>
                            <option value=1 selected><?=lang('automatically')?></option>
                        </select>
                        <div class="text-gray-500 text-start px-5 bsapp-fs-13 bsapp-lh-15 mt-6">
                            <span>
                                <?=lang('settings_external_order_sub')?>
                            </span>
                        </div>
                    </div>
                </li>

                <li class="mb-15 border-bottom pb-10">
                    <div class="mb-6">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-6">
                            <?=lang('settings_schedule_allows_coincide_section') ?>
                        </h6>
                        <select class="js-select2-dropdown-arrow-template allows_coincide_section" required name="AllowsCoincideSection">
                            <option value=0 selected><?=lang('no')?></option>
                            <option value=1><?=lang('yes')?></option>
                        </select>
                        <div class="text-gray-500 text-start px-5 bsapp-fs-13 bsapp-lh-15 mt-6">
                            <span>
                                <?=lang('settings_schedule_allows_coincide_section_sub')?>
                            </span>
                        </div>
                    </div>
                </li>

                <li class="mb-15 border-bottom pb-10">
                    <div class="mb-6 external-ordering-range-section">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-6">
                            <?=lang('external_ordering_range') ?>
                        </h6>
                        <select class="js-select2-dropdown-arrow-template external_ordering_range" required
                                onchange="meetingGeneralSettings.externalOrderingRangeChanged(this)"  name="ExternalOrderingRange">
                            <option time-type='1' value=1><?= lang('week') ?></option>
                            <option time-type='1' value=2><?= lang('two_weeks') ?></option>
                            <option time-type='1' value=3> 3 <?= lang('weeks') ?></option>
                            <option time-type='2' value=1><?= lang('month') ?></option>
                            <option time-type='2' value=2><?= lang('two_months') ?></option>
                            <option time-type='2' value=3> 3 <?= lang('months') ?></option>
                            <option time-type='2' value=4> 4 <?= lang('months') ?></option>
                            <option time-type='2' value=5> 5 <?= lang('months') ?></option>
                            <option time-type='2' value=6 selected"><?= lang('half_year') ?></option>
                        </select>
                        <input type='hidden' value="1" id='js-external-ordering-range-type' name='ExternalOrderingRangeType'/>
                        <div class="text-gray-500 text-start px-5 bsapp-fs-13 bsapp-lh-15 mt-6">
                            <span>
                                <?=lang('external_ordering_range_section_sub')?>
                            </span>
                        </div>
                    </div>
                </li>

                <li class="border-bottom pb-10">
                    <div class="mb-6">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-6">
                            <?=lang('settings_schedule_link_coach_at_select') ?>
                        </h6>
                        <select class="js-select2-dropdown-arrow-template associate_coach" required name="AssociateCoach">
                            <option value=0><?=lang('automatically_fifo_option')?></option>
                            <option value=1 selected><?=lang('manually_select_coach_option')?></option>
                        </select>
                        <div class="text-gray-500 text-start px-5 bsapp-fs-13 bsapp-lh-15 mt-6">
                            <span>
                                <?=lang('settings_link_coach_at_select_order_sub')?>
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="position-absolute d-none bottom-0 left-0 p-15 bg-white w-100 js-save-meeting-settings">
                <a class="save-meeting-settings btn btn-lg btn-primary text-white rounded-lg font-weight-bolder
                 shadow-none border-0 w-100 mb-15 bsapp-fs-16" onclick="meetingGeneralSettings.updateGeneralSettings(this)"
                   role="button"><?=lang('save_changes_button') ?></a>
            </div>
        </div>
    </div>
</div>
