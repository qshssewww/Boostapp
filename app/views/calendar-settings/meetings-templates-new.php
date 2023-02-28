<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-meetings-templates-new
 d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="3">
  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" data-target="all-templates">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?=lang('back_single') ?>
    </h5>
  </a>

  <h3 class="path-title d-flex align-items-center text-gray-700 font-weight-bolder mb-10 bsapp-fs-14">
    <i class="fal fa-th-large mie-6 text-gray-500 bsapp-fs-19"></i>
      <?=lang('path_new_appointments') ?>
  </h3>

  <div class="bsapp-tabs-navigation d-flex align-items-center justify-content-start mb-10 bsapp-fs-16">
    <a class="text-decoration-none py-3 mie-20 active" id="list-template-basics-list" data-toggle="list" href="#list-template-basics">
        <?=lang('general_information') ?>
    </a>
    <a class="text-decoration-none py-3 mie-20" id="list-template-advanced-list" data-toggle="list" href="#list-template-advanced">
        <?=lang('advenced_settings') ?>
    </a>
  </div>



  <!-- Start of Scrollable area -->
    <div id="js-content-new-template" class="scrollable d-none">
        <div class="pb-50">
      <div class="tab-content">
          <ul class="list-unstyled p-0 tab-pane fade active show" id="list-template-basics">
              <li class="mb-15">
                  <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                      <?=lang('template_meeting_name') ?>
                  </h6>
                  <input type="text" class="form-control bg-light border rounded shadow-none m-0 py-2 px-10" maxlength="50"
                         aria-label="Template name" id="template-name" name="TemplateName" onfocusout="fieldEvents.showTagInfo(this)"
                         placeholder="<?=lang('cal_template_name_placeholder') ?>" required>
              </li>
              <li class="mb-10 row">
                  <div class="form-group mb-10 col-8">
                      <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                          <?=lang('meeting_category') ?>
                      </h6>
                      <select id='js-select2-template-category' onchange="fieldEvents.showTagInfo(this)" name="CategoryId" required></select>
                  </div>
                  <div class="form-group mb-10 col-4">
                      <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                          <?=lang('color_calendar') ?>
                      </h6>
                      <select class="template-colors" name="ColorId" required></select>
                  </div>
              </li>

              <!-- START OF TAGS SECTION -->
              <div class="text-start mb-15 tagInfo">
                  <div class="form-group flex-fill mb-15">
                      <label class="" for="js-select2-class"> <?= 'תגית פגישה'//lang('meeting_tag') ?></label>
                      <div class="input-group">
                          <input name="tag" class="form-control bg-light border-light" disabled style="border: 0; border-radius: 0" type="text" required autocomplete="off">
                          <div class="input-group-prepend bg-light" style="border: 0; border-radius: 0">
                              <a onclick="fieldEvents.showCategoryChoice()" href="javascript:;"
                                 data-id="js-items-tab-1-stuff" style="color:blue; padding:10px"
                                 class="bg-light border-light"> <?= lang('edit_two') ?> </a>
                          </div>
                      </div>
                      <div class="js-tab-sub-preview bsapp-fs-9 text-muted d-flex" >בחרו תגית המתארת בצורה הקרובה ביותר את השיעור
                          *בחירה זו לא תוצג ללקוחות שלכם
                      </div>
                  </div>
              </div>
              <!-- END OF TAGS SECTION -->

              <li class="mb-15">
                  <h6 class="text-gray-700 text-start font-weight-bolder mb-10 border-bottom">
                      <?=lang('buy_options') ?>
                  </h6>
                  <div class="payment-options-section">
                      <div class="row m-5 mb-10 price-duration-block" data-id=0>
                          <div class="col-11 shadow rounded-lg p-10 ">
                              <div class="row">
                                  <div class="col-6">
                                      <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                          <?=lang('duration') ?>
                                      </h6>
                                  </div>
                                  <div class="col-6">
                                      <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                          <?=lang('price') ?>
                                      </h6>
                                  </div>
                              </div>
                              <div class="row">
                                  <div class="col-6">
                                      <select class="js-select2-dropdown-arrow-template template-duration price-block" name="template-duration-0" required>
                                          <?php for ($i = 5; $i <= 600; $i+=5):
                                              $interval = mktime(0, $i);
                                              if ($i / 60 < 1)
                                                  $intervalText = (int)date('i', $interval) . ' ' . lang('minutes');
                                              else if ($i % 60 == 0)
                                                  $intervalText = date('G', $interval) . ' ' . lang('hours');
                                              else
                                                  $intervalText = date('G', $interval) . ' ' . lang('hours_and') . (int)date('i', $interval) . ' ' . lang('minutes');
                                              ?>
                                              <option <?=$i == 60 ? "selected" : "" ?> data-text="<?=$intervalText ?>" value="<?=$i ?>">
                                                  <?=$intervalText ?>
                                              </option>
                                              <?php
                                              if (120 <= $i && $i < 420)
                                                  $i += 10;
                                              else if ($i >= 420)
                                                  $i += 55;
                                          endfor;
                                          ?>
                                      </select>
                                  </div>
                                  <div class="col-6 ">
                                      <div class="position-relative">
                                          <input inputmode="decimal" type="number" onchange="globalCalendarSettings.setTwoNumberDecimal(this)"  aria-label="Template price" name="template-price-0" required
                                                 onKeyPress="if(this.value.length==7) return false;"  class="form-control bg-light border rounded js-template-price shadow-none m-0 py-2 px-15 price-block"
                                                 placeholder="<?=lang('add_membership_price_js') ?>">
                                          <span class="position-absolute" style="top:6px;right:0;">₪</span>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-1 pl-0 align-items-center d-none remove-button">
                              <a role="button" onclick="meetingTemplate.removePaymentOption(this)">
                                  <i class="p-0 fal fa-trash-alt bsapp-fs-24"></i>
                              </a></div>
                      </div>
                  </div>
                  <a id="js-add-payment-option" class="font-weight-bold js-add-payment-option d-flex text-start
                  my-15 mb-0 bsapp-fs-16" role="button" onClick="meetingTemplate.addPaymentOption(this)"><?=lang('add_buy_option') ?></a>
              </li>
          </ul>
          <div class="tab-pane fade" id="list-template-advanced">
              <div class="js-external-registration position-relative w-100 d-flex justify-content-between align-items-center px-15 pt-7 pb-10 border-bottom border-light">
                  <div class="">
                      <h6 class="text-gray-700 text-start font-weight-bolder"><?=lang('ext_register')?></h6>
                      <div class="d-flex align-items-center">
                          <div class="mie-12">
                              <i class="fal fa-eye icon-block bsapp-fs-28"></i>
                          </div>
                          <div class="bsapp-fs-16 text-right title-block-value">
                              <?=lang('allows_everyone_order') ?>
                          </div>
                      </div>
                  </div>
                  <div class="">
                      <i class="fal fa-angle-right bsapp-fs-32"></i>
                      <a class="stretched-link advanced-setting-item" data-target="calendarSettings-meetings-templates-advanced-settings"
                         data-target-id="external-registration-section" data-text="<?=lang('ext_register')?>" href="javscript:;"></a>
                  </div>
              </div>
              <div class="js-coaches-limit position-relative w-100 d-flex justify-content-between align-items-center px-15 pt-7 pb-10 border-bottom border-light">
                  <div class="">
                      <h6 class="text-gray-700 text-start font-weight-bolder"><?=lang('restriction_by_coaches')?></h6>
                      <div class="d-flex align-items-center">
                          <div class="mie-12">
                              <i class="fas fa-user-circle icon-block bsapp-fs-28" style="color:#A5A5A5"></i>
                          </div>
                          <div class="bsapp-fs-16 text-right title-block-value ">
                              <?=lang('all_coaches') ?>
                          </div>
                      </div>
                  </div>
                  <div class="">
                      <i class="fal fa-angle-right bsapp-fs-32"></i>
                      <a class="stretched-link advanced-setting-item" data-target="calendarSettings-meetings-templates-advanced-settings"
                         data-target-id="coaches-limit-section" data-text="<?=lang('restriction_by_coaches')?>" href="javscript:;"></a>
                  </div>
              </div>
              <div class="js-calendars-limit position-relative w-100 d-flex justify-content-between align-items-center px-15 pt-7 pb-10 border-bottom border-light">
                  <div class="">
                      <h6 class="text-gray-700 text-start font-weight-bolder "><?=lang('restriction_by_calendar') ?></h6>
                      <div class="d-flex align-items-center">
                          <div class="mie-12">
                              <i class="fal fa-calendar icon-block bsapp-fs-28"></i>
                          </div>
                          <div class="bsapp-fs-16 text-right title-block-value ">
                            <?=lang('all_calendar') ?>
                          </div>
                      </div>
                  </div>
                  <div class="">
                      <i class="fal fa-angle-right bsapp-fs-32"></i>
                      <a class="stretched-link advanced-setting-item" data-target="calendarSettings-meetings-templates-advanced-settings"
                         data-target-id="calendars-limit-section" data-text="<?=lang('restriction_by_calendar')?>" href="javscript:;"></a>
                  </div>
              </div>
              <div class="js-sessions-limit position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-10 border-bottom border-light">
                  <div class="">
                      <h6 class="text-gray-700 text-start font-weight-bolder "><?=lang('daily_restriction_of_meetings') ?></h6>
                      <div class="d-flex align-items-center">
                          <div class="mie-12">
                              <i class="fal fa-stop-circle icon-block bsapp-fs-28"></i>
                          </div>
                          <div class="bsapp-fs-16 text-right title-block-value ">
                              <?=lang('unlimited') ?>
                          </div>
                      </div>
                  </div>
                  <div class="">
                      <i class="fal fa-angle-right bsapp-fs-32"></i>
                      <a class="stretched-link advanced-setting-item" data-target="calendarSettings-meetings-templates-advanced-settings"
                         data-target-id="sessions-limit-section" data-text="<?=lang('daily_restriction_of_meetings')?>" href="javscript:;"></a>
                  </div>
              </div>
              <div class="js-online-options position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-10 border-bottom border-light">
                  <div class="">
                      <h6 class="text-gray-700 text-start font-weight-bolder "><?=lang('online_options') ?></h6>
                      <div class="d-flex align-items-center">
                          <div class="mie-12">
                              <i class="fal fa-video-slash icon-block bsapp-fs-28"></i>
                          </div>
                          <div class="bsapp-fs-16 text-right title-block-value">
                              <?=lang('physical_meeting') ?>
                          </div>
                      </div>
                  </div>
                  <div class="">
                      <i class="fal fa-angle-right bsapp-fs-32"></i>
                      <a class="stretched-link advanced-setting-item" data-target="calendarSettings-meetings-templates-advanced-settings"
                         data-target-id="online-options-section" data-text="<?=lang('online_options')?>" href="javscript:;"></a>
                  </div>
              </div>
              <div class="js-preparation position-relative w-100 justify-content-between align-items-center px-15 pt-7 pb-10 border-bottom border-light
                <?php if (Auth::user()->role_id == "1") { ?> d-flex <?php } else { ?> d-none <?php } ?>"> <!--todo change this to show preparation settings-->
                  <div class="">
                      <h6 class="text-gray-700 text-start font-weight-bolder "><?=lang('preparations_time') ?></h6>
                      <div class="d-flex align-items-center">
                          <div class="mie-12">
                              <i class="fal fa-watch icon-block bsapp-fs-28"></i>
                          </div>
                          <div class="bsapp-fs-16 text-right title-block-value ">
                              10 דק' לאחר הפגישה
                          </div>
                      </div>
                  </div>
                  <div class="">
                      <i class="fal fa-angle-right bsapp-fs-32"></i>
                      <a class="stretched-link advanced-setting-item" data-target="calendarSettings-meetings-templates-advanced-settings"
                         data-target-id="preparation-time-section" data-text="<?=lang('preparations_time')?>" href="javscript:;"></a>
                  </div>
              </div>
              <div class="js-add-more-info position-relative w-100 d-flex justify-content-between align-items-center px-15 pt-7 pb-10">
                  <div class="">
                      <h6 class="text-gray-700 text-start font-weight-bolder"><?=lang('add_note')?></h6>
                      <a class="stretched-link advanced-setting-item" data-target="calendarSettings-meetings-templates-advanced-settings"
                         data-target-id="more-info-section" data-text="<?=lang('more_info_class_page')?>" href="javscript:;"></a>
                  </div>
              </div>
        </div>
      </div>
    </div>
    </div>
    <div id="js-loader-new-template" class="form-static d-flex align-items-center justify-content-center rounded text-start m-0 py-15 px-10 bsapp-fs-14">
        <div class="spinner-border spinner-border-sm text-success" role="status">
            <span class="sr-only"><?php echo lang('loading') ?></span>
        </div>
    </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
      <a onclick="meetingTemplate.saveTemplate(this)"
         class="save-app-template btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16">
          <?=lang('save_changes_button') ?>
      </a>
  </div>

</div>


