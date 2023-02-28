<div class="d-flex flex-column bg-white    bsapp-page-tab" data-page-id="js-tabs-home" data-depth="0">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24">
        <span class="font-weight-bold"><?= lang('client_settings') ?></span>
        <a href="javascript:;" onclick="ClientsSettings.closeSettings(this)" class="text-black text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
<!--    --><?php //if (Auth::user()->role_id == 1) { ?>
<!--        <div class="py-8 pt-10 border-bottom border-light">-->
<!--            <div class="d-flex">-->
<!--                <i class="fal fa-address-card mie-10 bsapp-fs-20"></i>-->
<!--                <h6 class="bsapp-fs-16 bsapp-lh-22 mb-6 font-weight-bolder">--><?//= lang('data_from_client') ?><!--</h6>-->
<!--            </div>-->
<!--            <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4">--><?//= lang('data_from_client_description') ?><!--</div>-->
<!--            <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"-->
<!--               href="javascript:;" onclick="ClientsSettings.goTo(this, event);"-->
<!--               data-next="js-tabs-page-1">--><?//= lang('manage') ?><!--<i class="fal fa-angle-right mx-5 bsapp-fs-24"></i></a>-->
<!--        </div>-->
<!--    --><?php //} ?>
    <div class="py-8 pt-10 border-bottom border-light">
        <div class="d-flex">
            <i class="fal fa-user-tag mie-10 bsapp-fs-20"></i>
            <h6 class="bsapp-fs-16 bsapp-lh-22 mb-6 font-weight-bolder"><?= lang('client_tags_settings') ?></h6>
        </div>
        <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4"><?= lang('client_tags_description') ?></div>
        <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
           href="javascript:;" onclick="ClientsSettings.goToTags(this, event);"
           data-next="js-tabs-page-2"><?= lang('manage') ?><i class="fal fa-angle-right mx-5 bsapp-fs-24"></i></a>
    </div>
    <div class="py-8 pt-10">
        <div class="d-flex">
            <i class="fal fa-user-minus mie-10 bsapp-fs-20"></i>
            <h6 class="bsapp-fs-16 bsapp-lh-22 mb-6 font-weight-bolder"><?= lang('reason_leave_not_join') ?></h6>
        </div>
        <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4"><?= lang('reason_leave_not_join_description') ?></div>
        <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
           href="javascript:;" onclick="ClientsSettings.goToReasonsLeave(this, event);"
           data-next="js-tabs-page-3"><?= lang('manage') ?><i class="fal fa-angle-right mx-5 bsapp-fs-24"></i></a>
    </div>
</div>
