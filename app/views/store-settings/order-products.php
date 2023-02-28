<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-order-products d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="1">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="main-settings-panel">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_to_store') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
    <i class="fal fa-sort mie-6 text-gray-500 bsapp-fs-19"></i>
    <?php echo lang('app_display_order') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <div class="list-group list-group-horizontal mt-6" role="tablist">
        <a class="list-group-item list-group-item-action bg-light text-gray-700 font-weight-bolder border-0 rounded text-center mie-10 py-8 bsapp-fs-16 bsapp-lh-21 active" id="list-memberships-list" data-toggle="list" href="#list-memberships" role="tab" aria-controls="memberships" onclick="getDisplayOrders('memberships')"><?php echo lang('memberships_single') ?></a>
        <a class="list-group-item list-group-item-action bg-light text-gray-700 font-weight-bolder rounded border-0 text-center mis-10 py-8 bsapp-fs-16 bsapp-lh-21" id="list-products-list" data-toggle="list" href=
        "#list-products" role="tab" aria-controls="products" onclick="getDisplayOrders('categories')"><?php echo lang('products') ?></a>
      </div>

      <div class="tab-content">
        <div class="tab-pane fade show active" id="list-memberships" role="tabpanel" aria-labelledby="list-memberships">
          <ul class="order-list order-memberships-list list-unstyled mt-15 p-0 bsapp-fs-14">

            <li class="item-placeholder d-flex font-weight-bolder py-5 mb-10">
              <span class="pis-0 pie-10">#</span>
              <div class="col-auto flex-grow-1">
                <div class="row">
                  <span class="col-6 text-start px-0 pis-30"><?php echo lang('store_name') ?></span>
                  <span class="col-2 text-center px-5"><?php echo lang('store_period') ?></span>
                  <span class="col-2 text-center px-5"><?php echo lang('store_arrivals') ?></span>
                  <span class="col-2 text-end pis-5 pie-10"><?php echo lang('price') ?></span>
                </div>
              </div>
            </li>

            <li class="item-loading item-placeholder mb-10 animated fadeInUp">
              <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                <div class="spinner-border spinner-border-sm text-success" role="status">
                  <span class="sr-only"><?php echo lang('loading') ?>...</span>
                </div>
              </div>
            </li>

            <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-1">
              <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0  py-15 px-10 bsapp-fs-14">
                <div class="spinner-border spinner-border-sm text-success" role="status">
                  <span class="sr-only"><?php echo lang('loading') ?>...</span>
                </div>
              </div>
            </li>

            <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-2">
              <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                <div class="spinner-border spinner-border-sm text-success" role="status">
                  <span class="sr-only"><?php echo lang('loading') ?>...</span>
                </div>
              </div>
            </li>

          </ul>
        </div>

        <div class="tab-pane fade show" id="list-products" role="tabpanel" aria-labelledby="list-products">
          <ul class="order-list order-categories-list list-unstyled mt-15 p-0 bsapp-fs-14">

            <li class="item-placeholder d-flex font-weight-bolder py-5 mb-10">
              <span class="pis-0 pie-10">#</span>
              <div class="col-auto flex-grow-1">
                <div class="row">
                  <span class="col-12 text-start px-0 pis-30"><?php echo lang('category_name') ?></span>
                </div>
              </div>
            </li>

            <li class="item-loading item-placeholder mb-10 animated fadeInUp">
              <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                <div class="spinner-border spinner-border-sm text-success" role="status">
                  <span class="sr-only"><?php echo lang('loading') ?>...</span>
                </div>
              </div>
            </li>

            <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-1">
              <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                <div class="spinner-border spinner-border-sm text-success" role="status">
                  <span class="sr-only"><?php echo lang('loading') ?>...</span>
                </div>
              </div>
            </li>

            <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-2">
              <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                <div class="spinner-border spinner-border-sm text-success" role="status">
                  <span class="sr-only"><?php echo lang('loading') ?>...</span>
                </div>
              </div>
            </li>

          </ul>
        </div>

        <div class="tab-pane fade show" id="list-items" role="tabpanel" aria-labelledby="list-items">
          <a class="d-flex align-items-center font-weight-bolder text-gray-700 text-start text-decoration-none py-5 px-0 mt-15 bsapp-fs-14" id="list-products-list" data-toggle="list" href="#list-products" role="tab" aria-controls="products">
            <i class="fal fa-angle-left mie-5 bsapp-fs-21"></i>
            <?php echo lang('back_single') ?>
          </a>

          <ul class="order-list order-items-list list-unstyled mt-15 p-0 bsapp-fs-14">

            <li class="item-placeholder d-flex font-weight-bolder py-5 mb-10">
              <span class="pis-0 pie-10">#</span>
              <div class="col-auto flex-grow-1">
                <div class="row">
                  <span class="col-9 text-start px-0 pis-30"><?php echo lang('product_name') ?></span>
                  <span class="col-3 text-end"><?php echo lang('price') ?></span>
                </div>
              </div>
            </li>

            <li class="item-loading item-placeholder mb-10 animated fadeInUp">
              <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-20 px-10 bsapp-fs-14"></div>
            </li>

            <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-1">
              <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-20 px-10 bsapp-fs-14"></div>
            </li>

            <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-2">
              <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-20 px-10 bsapp-fs-14"></div>
            </li>

          </ul>

        </div>
      </div>

    </div>
  </div>

</div>
