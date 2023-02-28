<!-- Tasks Status Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-tasks-settings-statuses d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart"
     data-depth="2">
    <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button"
       data-target="calendarSettings-tasks-settings">
        <h5 class="d-flex align-items-start font-weight-bolder">
            <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
            <?= lang('back_single') ?>
        </h5>
    </a>
    <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
        <i class="fal fa-clipboard-check mie-6 text-gray-500 bsapp-fs-19"></i>
        <?= lang('settings_status_title') ?>
    </h3>

    <!-- Start of Scrollable Area -->
    <div class="bsapp-overflow-y-auto bsapp-scroll" style="height:calc( 100% - 60px);">
        <div class="scrollable text-start" style="overflow-y: auto; width: calc(100% + 3em); padding-inline-end: 3em; ">
            <ul class="js-task-statuses-list list-unstyled p-0 bsapp-fs-14">

                <li class="item-placeholder item-default disabled mb-12">
                    <div class="form-static d-flex bg-light rounded text-start m-0 py-11 px-10 bsapp-fs-14">
                        <span class="font-weight-bold"><?= lang('open_task') ?></span>
                    </div>
                </li>
                <li class="item-placeholder item-default disabled mb-12">
                    <div class="form-static d-flex bg-light rounded text-start m-0 py-11 px-10 bsapp-fs-14">
                        <span class="font-weight-bold"><?= lang('completed_task') ?></span>
                    </div>
                </li>
                <li class="item-placeholder item-default disabled mb-12">
                    <div class="form-static d-flex bg-light rounded text-start m-0 py-11 px-10 bsapp-fs-14">
                        <span class="font-weight-bold"><?= lang('canceled_task') ?></span>
                    </div>
                </li>

                <li class="item-loading item-placeholder mb-12 animated fadeInUp delay-2">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-12 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?= lang('loading') ?></span>
                        </div>
                    </div>
                </li>

                <li class="js-fields item-example d-none" data-id="">
                    <div class="bsapp-editable-field js-editable-item">
                        <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8"
                             style="border-color:#c6c6c6;border-radius: 6px;">
                            <div style="width:230px; overflow: hidden;text-overflow: ellipsis; display: -webkit-box;-webkit-line-clamp: 2;line-clamp: 2;-webkit-box-orient: vertical;"
                                 class="js-text-div js-item-name font-weight-bold"></div>
                            <div class="">
                                <div class="custom-control custom-switch status-lead-source js-item-status">
                                    <input type="checkbox" class="custom-control-input" id="js-switch-id"
                                           onchange="TasksSettings.changeTaskStatus(this, event)">
                                    <label class="custom-control-label" for="js-switch-id" role="button"></label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center ">
                                <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle"
                                   data-toggle="dropdown">
                                    <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                </a>
                                <div class="dropdown-menu  text-start">
                                    <a class="dropdown-item text-gray-700  px-8"
                                       onclick="TasksSettings.showEdit(this, event);" href="javascript:;">
                                        <i class="fal fa-edit fa-fw mx-5"></i>
                                        <span> <?= lang('edit') ?> </span>
                                    </a>
                                    <a class="dropdown-item  px-8 text-gray-700 js-item-text-status"
                                       onclick="TasksSettings.changeTaskStatus(this, event);" href="javascript:;">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="js-part-edit w-100 h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8 border border-success"
                             style="border-radius: 6px;">
                            <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div"
                                   onchange="TasksSettings.addClassChanged(this)" maxlength=20
                                   placeholder="<?= lang('enter_name') ?>"/>
                            <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                <a class="mie-20 text-gray-700" onclick="TasksSettings.cancelEdit(this, event);">
                                    <i class="fal fa-minus-circle"></i>
                                </a>
                                <a class="text-success" onclick="TasksSettings.saveTaskStatus(this, event);">
                                    <i class="fal fa-check-circle"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

            </ul>
            <div>
                <a href="javascript:;" class="text-gray text-decoration-none font-weight-bold"
                   onclick="TasksSettings.addTaskStatus(this, event);"
                   data-copy-item="js-design-editable-item"
                   data-copy-container=".js-fields"><?= lang('settings_add_status') ?></a>
            </div>
        </div>
    </div>
</div>
