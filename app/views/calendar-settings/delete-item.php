<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-remove-class d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="3">

  <a class="text-decoration-none font-weight-bolder p-0 mie-30 mb-20" role="button" data-target="calendarSettings-calendars-and-classes_classes">
    <h5 class="d-flex align-items-start text-black font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('back_single') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-30 bsapp-fs-14">
      <?php echo lang('remove_class_type_settings') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <form>
        <p class="text-center mt-20 mb-15 bsapp-fs-16"><?php echo lang('are_you_sure_delete_settings') ?></p>
        <p class="text-center bsapp-fs-16">
          <span class="item-label"></span>
            <?php echo lang('has_future_cal') ?>
          <span class="item-count"></span>
            <?php echo lang('futuer_event_cal') ?>
        </p>

        <div class="form-toggle text-start py-15">

          <div class="form-inline mb-20">
            <input class="toggle-manage form-check-input position-relative m-0" type="radio" name="deleteMoveItems" id="delete-class-type" value="delete">
            <label class="form-check-label text-gray-700 font-weight-bolder mis-10 bsapp-fs-16" for="delete-class-type">
                <?php echo lang('delete') ?><span class="item-count mx-6"></span><?php echo lang('related_items_store') ?>
            </label>
          </div>

          <div class="form-inline mb-15">
            <input class="toggle-manage form-check-input position-relative m-0" type="radio" name="deleteMoveItems" id="move-class-type" value="move" checked>
            <label class="form-check-label text-gray-700 font-weight-bolder mis-10 bsapp-fs-16" for="move-class-type"><?php echo lang('move_to_another') ?> <span class="item-type mis-6"><?php echo lang('class_settings_cal') ?></span></label>
          </div>

          <div class="toggle-content d-flex justify-content-between bg-light rounded col-8 mis-20 p-0">
            <select class="select2--class-type" name="moveToClass" id="move-to-class"> </select>
            <div class="bsapp-new-tag d-none bg-light py-8 pis-0 pie-10">
              <span role="button" class="badge badge-pill badge-info font-weight-normal py-5 px-10 bsapp-fs-12"><?php echo lang('new') ?></span>
            </div>
          </div>

        </div>

      </form>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <div class="bsapp-delete-or-move-class btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 mb-15 bsapp-fs-16"><?php echo lang('save_changes_button') ?></div>
    <a class="btn btn-lg bg-light text-gray-700 rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button" data-target="calendarSettings-calendars-and-classes_classes"><?php echo lang('cancel') ?></a>
  </div>

</div>