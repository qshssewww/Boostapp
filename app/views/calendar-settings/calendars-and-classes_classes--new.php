<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-classes--new d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="3">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="calendarSettings-calendars-and-classes_classes">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('back_new_add_credit') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
      <?php echo lang('cal_calendars_new_class_type') ?>
  </h3>

  <!-- Start of Scrollable Area -->
  <div class="scrollable">
    <div class="pb-100">

      <div class="d-flex flex-column">

        <div class="d-flex align-items-center mb-15 w-100">
          <i class="fal fa-bookmark bsapp-fs-16 mie-7"></i>
          <input type="text" id="newClass--name" class="form-control bg-light border-0 rounded shadow-none m-0 py-2 px-10"
                 aria-label="Calendar name" placeholder="<?php echo lang('cal_new_class_type_name') ?>">
        </div>

        <div class="d-flex align-items-center mb-15 w-100">
          <i class="fal fa-calendar bsapp-fs-16 mie-7"></i>
          <div class="col-2 px-0">
              <select class="select2--colors" name="set-class-color"></select>
            </div>
        </div>

        <div class="d-flex align-items-center mb-20 w-100">
          <i class="fal fa-clock bsapp-fs-16 mie-7"></i>
          <div class="bsapp-fs-16 d-flex" style="min-width: 50%;">
              <!-- todo-> out of this page-->
              <select class="js-select2-dropdown-arrow-classes newClass--duration" id="newClass--duration" aria-label="New Class Duration" name="newClass--duration" required>
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
                      if (120 <= $i && $i < 240)
                          $i += 10;
                      else if (240 <= $i && $i < 420)
                          $i += 25;
                      else if ($i >= 420)
                          $i += 55;
                  endfor;
                  ?>
              </select>
          </div>
        </div>

        <div class="newClass--memberships d-flex flex-column">
          <h6 class="text-gray-700 text-start mb-10"><?php echo lang('cal_class_type_memberships') ?></h6>
          <p class="text-gray-500 text-start m-0 mb-20 bsapp-fs-13 bsapp-lh-15"><?php echo lang('cal_class_type_memberships_sub') ?></p>
          <!-- Memberships rendered here -->
        </div>

      </div>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="save-class btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 mb-15 bsapp-fs-16" role="button"><?php echo lang('save_changes_button') ?></a>
    <a class="btn btn-lg bg-light text-gray-700 rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button" data-target="calendarSettings-calendars-and-classes_classes"><?php echo lang('cancel_app_booking') ?></a>
  </div>
</div>