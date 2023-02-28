<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-manage-items d-none flex-column overflow-hidden
 position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-decoration-none font-weight-bolder p-0 mie-30 mb-20" role="button" data-target="storeSettings-manage-settings" >
    <h5 class="d-flex align-items-start text-black font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_single') ?>
    </h5>
  </a>


  <h3 class="text-gray-700 text-start font-weight-bolder mb-10 bsapp-fs-14">
  <?php echo lang('manage_itmes_category') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <ul class="items-list list-unstyled mt-10 p-0">
        <li class="item-loading mb-10 animated fadeInUp">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only"><?php echo lang('loading') ?>...</span>
            </div>
          </div>
        </li>
        <li class="item-loading mb-10 animated fadeInUp delay-1">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only"><?php echo lang('loading') ?>...</span>
            </div>
          </div>
        </li>
        <li class="item-loading mb-10 animated fadeInUp delay-2">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only"><?php echo lang('loading') ?>...</span>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>

  <div class="form-toggle position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <div class="form-static d-flex">
      <a class="bsapp-new-item btn btn-lg bg-light text-gray-700 rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button">+ <?= lang('add_new_membership_type') ?></a>
    </div>
  </div>

</div>