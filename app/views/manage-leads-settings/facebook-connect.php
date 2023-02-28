<div class="d-none flex-column justify-content-between h-100  bsapp-settings-panel  bsapp-page-tab"
     data-page-id="js-tabs-facebook" data-depth="2">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
        <a class="text-black d-flex text-decoration-none font-weight-bold" href="javascript:;"
           onclick="LeadsSettings.goTo(this, event);" data-next="js-tabs-home"><i
                    class="fal fa-angle-left mie-7 bsapp-fs-24"></i><?php echo lang('back_new_add_credit') ?></a>
        <a href="javascript:;" onclick="LeadsSettings.closeSettings(this)" class="text-black  text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <div class="mb-16">
        <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold">
            <div class="mie-7"><i class="fab fa-facebook-square text-gray-500"></i></div>
            <span><?php echo lang('settings_facebook') ?></span>
        </div>
    </div>
    <div class="js-facebook-error flex-column justify-content-center text-center d-none" dir="rtl"
         style="height:calc(100% - 110px );"></div>
    <div class="js-facebook-loading flex-column justify-content-center text-center" dir="rtl"
         style="height:calc(100% - 110px ); margin-top: 50%;">
        <div class="spinner-border spinner-border text-success" role="status">
            <span class="sr-only"><?php echo lang('loading') ?></span>
        </div>
    </div>
    <div class="js-facebook-disconnected d-flex flex-column justify-content-center" style="height:calc(100% - 60px );">
        <div class="text-center d-flex flex-column m-auto w-50" id="facebookLoginbtnWrapper">
            <div class="d-flex text-center py-30 bsapp-fs-16 font-weight-bold"><?= lang('not_connect_facebook') ?></div>
            <a class="btn btn-social btn-facebook">
                <i class="fab fa-facebook-f"></i> <?php echo lang('connect_leads_to_facebook') ?>
            </a>
            <div class="row d-flex justify-content-center py-30">
                <a class="mie-30 text-info text-underline" href="https://site.boostapp.co.il/terms/" target="_blank"><?= lang('login_terms_facebook') ?></a>
                <a class="text-gray-400 text-underline" href="https://site.boostapp.co.il/privacy/" target="_blank"><?= lang('login_policy_facebook') ?></a>
            </div>
        </div>
    </div>
    <div class="js-facebook-connected d-none" style="height: calc(100% - 110px )">
        <h6 class="js-item-title-page mb-10 d-none"><?php echo lang('facebook_page_name') ?></h6>
        <div class="bsapp-overflow-y-auto bsapp-scroll" style="height: 90%;">

            <ul class="facebook-pages-list list-unstyled p-0 bsapp-overflow-y-auto bsapp-scroll bsapp-fs-14">

                <li class="js-fields item-example d-none" data-id="">
                    <div class="bsapp-editable-field js-editable-item">
                        <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8"
                             style="border-color:#c6c6c6;border-radius: 6px;">
                            <div class="js-text-div js-item-name" style="width:265px;"></div>
                            <div class="">
                                <div class="custom-control custom-switch js-item-status">
                                    <input type="checkbox" class="custom-control-input" id="customSwitch2">
                                    <label class="custom-control-label" for="customSwitch2"></label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle"
                                   data-toggle="dropdown">
                                    <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                </a>
                                <div class="dropdown-menu  text-start">
                                    <a class="dropdown-item text-gray-700  px-8 js-item-go-one-page"
                                       href="javascript:;"
                                       data-next="js-tabs-facebook-one-page" href="javascript:;">
                                        <i class="fal fa-edit fa-fw mx-5"></i>
                                        <span> <?php echo lang('edit') ?> </span>
                                    </a>
                                    <a class="dropdown-item  px-8 text-gray-700 js-item-text-status"
                                       href="javascript:;">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <div class="item-loading item-placeholder mb-10 animated fadeInUp">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?></span>
                        </div>
                    </div>
                </div>

            </ul>
        </div>
    </div>
    <div id="facebookLogout">
        <a class="btn btn-light btn-block btn-facebook"
           style="background: #f5f5f5;border-color: #f5f5f5;border-radius: 4px;color: #FF0015;">
            <?php echo lang('disconnect_facebook') ?>
        </a>
    </div>
</div>