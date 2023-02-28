<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-calendars--new d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-id='' data-depth="3">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="calendarSettings-calendars-and-classes_calendars">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('back_new_add_credit') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
      <?php echo lang('path_calendars_new') ?>
  </h3>

  <!-- Start of Scrollable Area -->
  <div class="scrollable" style="max-height: 65%">
    <div class="">
      <div class="d-flex flex-column">
          <div class="text-black bsapp-fs-14 text-start"><?php echo lang('space_name')?></div>
        <div class="d-flex align-items-center mb-15 w-100">
          <i class="fal fa-bookmark bsapp-fs-16 mie-7"></i>
          <input type="text" class="form-control bg-light border-0 rounded shadow-none m-0 py-2 px-10" maxlength="70" aria-label="Calendar name" id="newCalendar-name" placeholder='<?php echo lang("cal_new_calendar_placeholder")?>' required>
        </div>
          <div class="branch-space-section">
              <div class="text-black bsapp-fs-14 text-start"><?php echo lang('space_branch')?></div>
              <div class="d-flex align-items-center w-100 mb-15">
                  <i class="fal fa-map-marker-alt bsapp-fs-16 mie-6"></i>
                  <div class="w-100">
                      <select class="select2--branches" name="calendar-branches" required></select>
                  </div>
              </div>
          </div>
          <div class="text-black bsapp-fs-14 text-start"><?php echo lang('section_location')?></div>
          <div class="d-flex align-items-center mb-15 w-100">
              <i class="fal fa-location-circle bsapp-fs-16 mie-5"></i>
              <div class="w-100">
                  <select class="select2--outdoor" name="calendar-outdoor" required></select>
              </div>
          </div>

<!--          --><?php
//          require_once __DIR__.'/../../../office/Classes/Settings.php';
//          $class_companySettings = new Settings($CompanyNum ?? Auth::user()->CompanyNum);
//
//          if ($class_companySettings->beta == 1) {
//          ?>
<!--          -->
<!--              <div class="text-black bsapp-fs-14 text-start">--><?php //echo 'סוג המתחם'//lang('lesson_type') //TODO change translation?><!--</div>-->
<!--              <div class="d-flex align-items-center mb-15 w-100">-->
<!--                  <i class="fal fa-walking bsapp-fs-15 mie-7"></i>-->
<!--                  <div class="w-100">-->
<!--                      <select class="select2--spaceType" name="calendar-spaceType" required></select>-->
<!--                  </div>-->
<!--              </div>-->
<!--              <div id="js-entrance-price-block" class="d-none">-->
<!--                  <div class="text-start tagInfo mb-15">-->
<!--                      <div class="form-group flex-fill ">-->
<!--                          <div class="text-black bsapp-fs-14 text-start" for="js-select2-class">--><?//= lang('lesson_tag') ?><!--</div>-->
<!--                          <div class="d-flex align-items-center input-group" style="flex-wrap:nowrap;">-->
<!--                              <i class="fal fa-tag bsapp-fs-16 mie-6"></i>-->
<!--                              <input name="tag" class="form-control text-start bg-light input-group-text" disabled style="border: 0; border-radius: 0" type="text" required autocomplete="off">-->
<!--                              <div class="input-group-prepend bg-light" style="border: 0; border-radius: 0">-->
<!--                                  <a onclick="fieldEvents.showCategoryChoice()" href="javascript:;"-->
<!--                                     data-id="js-items-tab-1-stuff" style="color:blue; padding:10px"-->
<!--                                     class="bg-light border-light"> --><?//= lang('edit_two') ?><!-- </a>-->
<!--                              </div>-->
<!--                          </div>-->
<!--                          <div class="js-tab-sub-preview bsapp-fs-9 text-muted d-flex mr-20"  >בחרו תגית המתארת בצורה הקרובה ביותר את השיעור-->
<!--                              *בחירה זו לא תוצג ללקוחות שלכם-->
<!--                          </div>-->
<!--                      </div>-->
<!--                  </div>-->
<!---->
<!--                  <div class="d-flex ">-->
<!--                      <span class="d-flex align-items-center bsapp-fs-18 ml-9" >₪</span>-->
<!--                      <input inputmode="numeric" type="number" min="0" max="999" aria-label="entrance-price" name="entrance-price" required-->
<!--                             class="form-control bg-light border col-2 rounded js-entrance-price shadow-none ml-13 price-block align-right">-->
<!--                      <span class="text-start bsapp-fs-12" style="line-height: normal ;">* סכום זה אינו קבוע, ניתן להגדיר עלויות כניסה נוספות ואף מנויים וכרטיסיות באזור ניהול הפריטים.</span>-->
<!--                  </div>-->
<!--              </div>-->
<!--          --><?php //} ?>

      </div>
      <div class="position-absolute bottom-0 left-0 p-15 bg-white w-100">
        <a class="save-calendar btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 mb-15 bsapp-fs-16" role="button"><?php echo lang('save') ?></a>
        <a class="btn btn-lg bg-light text-gray-700 rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button" data-target="calendarSettings-calendars-and-classes_calendars"><?php echo lang('action_cacnel') ?></a>
      </div>
    </div>
  </div>
</div>