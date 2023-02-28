<div class="d-none flex-column    bsapp-page-tab" data-page-id="js-tabs-page-2" data-depth="1">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
        <a class="text-black d-flex text-decoration-none font-weight-bold js-from-tag-go-home" href="javascript:;"
           onclick="ClientsSettings.goTo(this, event);" data-next="js-tabs-home"><i
                    class="fal fa-angle-left mie-7 bsapp-fs-24"></i><?= lang('back_new_add_credit') ?></a>
        <a href="javascript:;" onclick="ClientsSettings.closeSettings(this)" class="text-black  text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <div class="mb-16">
        <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold ">
            <div class="mie-7"><i class="fal fa-user-tag"></i></div>
            <span><?= lang('client_tags_settings') ?></span>
        </div>
    </div>
    <div class="d-flex px-8 mb-10">
        <span class="bsapp-fs-14 bsapp-lh-17" style="width:197px;"><?= lang('store_name') ?></span>
        <span class="bsapp-fs-14 bsapp-lh-17"><?= lang('num_clients') ?></span>
    </div>
    <div class="bsapp-overflow-y-auto bsapp-scroll" style="height:calc( 100% - 60px);">
        <div class="scrollable" style="overflow-y: auto; width: calc(100% + 3em); padding-inline-end: 3em;">
            <ul class="js-tags-clients-list list-unstyled p-0 bsapp-fs-14">

                <li class="item-loading item-placeholder mb-10 animated fadeInUp">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-12 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?= lang('loading') ?></span>
                        </div>
                    </div>
                </li>
                <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-1">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-12 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?= lang('loading') ?></span>
                        </div>
                    </div>
                </li>
                <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-2">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-12 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?= lang('loading') ?></span>
                        </div>
                    </div>
                </li>

                <li class="js-fields item-example d-none" data-id="">
                    <div class="bsapp-editable-field js-editable-item">
                        <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-flex  align-items-center border px-8"
                             style="border-color:#c6c6c6;border-radius: 6px;">
                            <div style="width:60%; overflow: hidden;text-overflow: ellipsis; display: -webkit-box;-webkit-line-clamp: 2;line-clamp: 2;-webkit-box-orient: vertical;" class="js-text-div js-item-name"></div>
                            <div class="bsapp-fs-14 bsapp-lh-17 flex-fill js-item-count"></div>
                            <div class="">

                            </div>
                            <div class="d-flex align-items-center ">
                                <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle"
                                   data-toggle="dropdown">
                                    <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                </a>
                                <div class="dropdown-menu  text-start">
                                    <a class="dropdown-item text-gray-700  px-8"
                                       onclick="ClientsSettings.showEdit(this, event);" href="javascript:;">
                                        <i class="fal fa-edit fa-fw mx-5"></i>
                                        <span> <?php echo lang('edit') ?> </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="js-part-edit w-100 h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8 border border-success"
                             style="border-radius: 6px;">
                            <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"
                                   placeholder="<?= lang('enter_name') ?>"
                                   onchange="ClientsSettings.addClassChanged(this)"/>
                            <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                <a class="mie-20 text-gray-700" onclick="ClientsSettings.cancelEdit(this, event);"><i
                                            class="fal fa-minus-circle"></i></a>
                                <a class="text-success" onclick="ClientsSettings.saveTag(this, event);"><i
                                            class="fal fa-check-circle"></i></a>
                            </div>
                        </div>
                    </div>
                </li>

            </ul>
        </div>


        <div class="">
            <a class="text-gray-700  text-decoration-none font-weight-bold" href="javascript:;" data-copy-container=".js-fields"
               data-copy-item="js-design-editable-item"
               onclick="ClientsSettings.addTag(this, event);"><?= lang('add_client_tag') ?></a>
        </div>
    </div>
</div>