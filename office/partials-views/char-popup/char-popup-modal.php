<?php
require_once __DIR__ . "/../../../app/init.php";
require_once __DIR__ . "/../../Classes/Settings.php";

/** @var $CompanyNum */
?>

<div class="modal fade px-0 px-sm-auto bsapp-char-popup text-gray-700 js-modal-no-close" tabindex="-1" role="dialog" id="js-char-popup" data-backdrop="static">
    <div class="modal-dialog  modal-lg modal-dialog-centered bsapp-max-w-700p">
        <div class="modal-content h-100 overflow-auto">
            <div class="modal-body d-flex flex-column justify-content-between p-0 h-100">
                <!-- <div class="modal-body d-flex flex-column justify-content-between p-0 h-100"> -->
                <div id="js-char-popup-content" style="height:calc( 100% - 60px );">
                </div>
                <!-- bottom section :: begin -->
                <div class="js-bottom-action-bar bg-gray-300 d-flex justify-content-between  align-items-center w-100 h-60p bsapp-z-1">
                    <div>
                        <div class="btn-group dropup mx-15 <?php if (!Auth::userCan('169')){  echo 'disabled';}?> ">
                            <div type="button" class="js-class-status btn btn-outline-gray-700 btn-sm dropdown-toggle" data-toggle="dropdown">
                                <div class="d-none class-active">
                                    <div class="d-flex align-items-center">
                                        <label class="bsapp-status-icon bg-success my-auto mie-8" ></label>
                                        <span><?= lang('active') ?></span>
                                    </div>
                                </div>
                                <div class="d-none class-completed">
                                    <div class="d-flex align-items-center">
                                        <label class="bsapp-status-icon bg-secondary my-auto mie-8"></label>
                                        <span><?= lang('task_completed_main') ?></span>
                                    </div>
                                </div>
                                <div class="d-none class-canceled">
                                    <div class="d-flex align-items-center">
                                        <label class="bsapp-status-icon bg-danger my-auto mie-8"></label>
                                        <span><?= lang('canceled') ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php if (Auth::userCan('169')): ?>
                                <div class="dropdown-menu text-start w-250p">
                                    <div class="js-class-status-dropdown">
                                        <a class="d-none js-mark-class-canceled dropdown-item  text-gray-700  px-8" href="javascript:;" data-toggle="modal" data-target="#js-modal-cancel-action"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> <?= lang('cancel_lesson') ?></span></a>
                                        <a class="d-none js-mark-class-completed dropdown-item px-8 text-gray-700" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-clipboard-check"></i></span> <span><?= lang('mark_class_as_completed') ?></span> <i class="fad fa-info-circle"></i></a>
                                        <a class="d-none js-mark-class-active dropdown-item px-8 text-gray-700" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-clipboard-check"></i></span> <span><?= lang('mark_class_as_active') ?></span> <i class="fad fa-info-circle"></i></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex align-items-center h-100 justify-content-around">
                        <?php if (Auth::userCan('165')): ?>
                            <div class="mx-15  text-gray-700">
                            <a class="bsapp-fs-26  text-gray-700" href="javascript:;" data-toggle="modal" data-target="#js-modal-add-user">
                                <i class="fal fa-user-plus"></i>
                            </a>
                        </div>
                        <?php endif;
                        if (Auth::userCan('166')): ?>
                        <div class="mx-15  text-gray-700">
                            <a class="bsapp-fs-26  text-gray-700" href="javascript:;" id="js-link-edit-class">
                                <i class="fal fa-cog"></i>
                            </a>
                        </div>
                        <?php endif;?>
                        <?php if (Auth::userCan('167') || Auth::userCan('168') || Auth::userCan('166')): ?>
                        <div class="mx-15  text-gray-700 dropup">
                            <a class=" bsapp-fs-26   text-gray-700 dropdown-toggle"  data-toggle="dropdown" href="javascript:;">
                                <i class="fal fa-bars"></i>
                            </a>
                            <div class="dropdown-menu  text-start w-250p">
                                <?php if (Auth::userCan('166')){ ?>
                                <a class="dropdown-item px-8 text-gray-700" onclick="OpenClassPopup(charPopup.class_info.classid, true)" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-copy fa-flip-horizontal"></i></span> <span><?= lang('duplicate_lesson') ?></span></a>
                                <a class="dropdown-item px-8 text-gray-700" data-toggle="modal" data-target="#js-modal-class-content" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-align-right"></i></span> <span><?= lang('class_content_cal') ?></span></a>
                                <?php }
                                if (Auth::user()->role_id == 1) { ?>
                                    <a class="dropdown-item  text-gray-700  px-8" data-toggle="modal" data-target="#js-modal-window-share" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-share"></i></span> <span> <?= lang('share_link') ?></span></a>
                                <?php }
                                if (Auth::userCan('167')): ?>
                                <a class="dropdown-item px-8 text-gray-700" data-toggle="modal" data-target="#js-modal-window-print" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-print"></i></span> <span><?= lang('print_class_report') ?></span></a>
                                <?php endif;
                                if (Auth::userCan('168')): ?>
                                <a class="dropdown-item px-8 text-gray-700 js-send-message-to-all " href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-paper-plane"></i></span> <span><?= lang('send_messages_to_trainers') ?></span></a>
                                <?php endif;?>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                </div>
                <!-- bottom section :: end -->

                <!-- bottom action bar tab 1 :: begin -->
                <div data-context="js-participant-tab-1" class="js-bottom-user-action-bar bg-gray-300 d-none justify-content-center  align-items-center w-100 h-60p bsapp-z-1">
                    <div data-tab="active" class="d-flex align-items-center h-100">
                        <?php if (Auth::userCan('164')): ?>
                            <a data-toggle="modal" data-target="#js-remove-clients-modal" class="cursor-pointer mie-15 bsapp-fs-26 mx-15 text-gray-700">
                                <i class="fal fa-minus-circle"></i>
                            </a>
                        <?php endif;
                        if (Auth::userCan('168')): ?>
                            <a class="mie-15 bsapp-fs-26 mx-15 text-gray-700" href="javascript:;" id="sendMessage">
                                <i class="fal fa-paper-plane "></i>
                            </a>
                        <?php endif;
                        if (Auth::userCan('163')):?>
                        <a class="mie-15 bsapp-fs-26 mx-15 text-danger" data-toggle="modal" data-target="#js-confirmation-modal-2" href="javascript:;">
                            <i class="fal fa-times-circle"></i>
                        </a>
                        <a class="mie-15 bsapp-fs-26 mx-15 text-success" data-toggle="modal" data-target="#js-confirmation-modal">
                            <i class="fas fa-check-circle"></i>
                        </a>
                        <?php endif;?>
                        <a class="js-cancel-selection mie-15 bsapp-fs-18 mx-15 text-gray-700 cursor-pointer">
                            <?= lang('cancel_selection') ?>
                        </a>
                    </div>
                </div>
                <!-- bottom action bar  tab 1 :: end -->

                <!-- bottom action bar  tab 2 :: begin -->
                <div data-context="js-participant-tab-2" class="js-bottom-user-action-bar bg-gray-300 d-none justify-content-center  align-items-center w-100 h-60p bsapp-z-1">
                    <div data-tab="waiting" class="d-flex align-items-center h-100">
                        <?php if (Auth::userCan('164')): ?>
                            <a data-toggle="modal" data-target="#js-remove-clients-modal" class="cursor-pointer mie-15 bsapp-fs-20 mx-15 text-gray-700">
                                <i class="fal fa-minus-circle"></i>
                            </a>
                        <?php endif;
                        if (Auth::userCan('168')): ?>
                            <a class="mie-15 bsapp-fs-20 mx-15 text-gray-700 js-send-checked-messages">
                                <i class="fal fa-paper-plane"></i>
                            </a>
                        <?php endif;
                        if (Auth::userCan('163')):?>
                            <a class="js-assign-many-waiting btn btn-outline-gray-500 btn-rounded btn-sm "><?= lang('customer_card_embed') ?></a>
                        <?php endif;?>
                        <a class="js-cancel-selection mie-15 bsapp-fs-18 mx-15 text-gray-700 cursor-pointer">
                            <?= lang('cancel_selection') ?>
                        </a>
                    </div>
                </div>
                <!-- bottom action bar  tab 2  :: end -->
            </div>
        </div>
    </div>
</div>

<div class="js-char-shimming-loader position-relative d-none" class="">
    <div class="js-loader-div-char" >
        <div class="d-flex justify-content-end w-100 position-absolute" style="z-index:100;left:0;right:0;top:0;">
            <a class="text-dark js-close-char-popup pie-15 pt-15 bsapp-fs-26" href="javascript:;" data-dismiss="modal"><i class="fal fa-times"></i></a>
        </div>
        <div class="p-15 position-absolute" style="left:0;top:0;right:0;bottom:0;z-index:99;" >
            <div class="overflow-hidden " style="height: calc( 100% - 70px );">
                <div class="bsapp-loading-shimmer mb-50">
                    <div>
                        <div class="mb-15 w-50p h-50p overflow-hidden " style="border-radius:50% !important;">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-50">
                            <div></div>
                        </div>
                    </div>
                </div>
                <div class="bsapp-loading-shimmer">
                    <div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-50">
                            <div></div>
                        </div>
                    </div>
                </div>
                <div class="bsapp-loading-shimmer">
                    <div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-50">
                            <div></div>
                        </div>
                    </div>
                </div>
                <div class="bsapp-loading-shimmer">
                    <div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-100">
                            <div></div>
                        </div>
                        <div class="mb-15 w-50">
                            <div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- modal :: begin -->
<div class="modal fade px-0 px-sm-auto text-start  js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-window-message" data-backdrop="static">
    <div class="modal-dialog   modal-dialog-centered ">
        <div class="modal-content border-0 shadow-lg">
            <div class="js-window-message-shimming-loader position-relative d-none" style="">
                <div class="js-loader-div-char">
                    <div class="p-15 position-absolute" style="left:0;top:0;right:0;bottom:0;z-index:99;">
                        <div class="overflow-hidden " style="height: calc( 100% - 70px );">
                            <div class="bsapp-loading-shimmer">
                                <div>
                                    <div class="mb-15 w-50">
                                        <div></div>
                                    </div>
                                    <div class="mb-15 w-30">
                                        <div></div>
                                    </div>
                                    <div class="mb-15 w-100">
                                        <div></div>
                                    </div>
                                    <div class="mb-15 w-100">
                                        <div></div>
                                    </div>
                                    <div class="mb-15 w-50">
                                        <div></div>
                                    </div>
                                    <div class="mb-15 w-100">
                                        <div></div>
                                    </div>
                                    <div class="mb-15 w-50">
                                        <div></div>
                                    </div>
                                    <div class="mb-15 w-100">
                                        <div></div>
                                    </div>
                                    <div class="mb-15 w-100">
                                        <div></div>
                                    </div>
                                    <div class="mb-15 w-100">
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form id="sendMsgForm"  class="modal-body h-100 d-flex flex-column justify-content-between bsapp-overflow-y-auto position-relative px-0 py-0" style="height : calc( 100vh - 60px ) !important;">
                <div class="d-flex justify-content-between w-100 mb-20">
                    <h5 class="bsapp-fs-18 p-15"><i class="fal fa-paper-plane mie-10"></i><?= lang('send_message') ?></h5>
                    <a href="javascript:;"  class="text-dark close-btn p-15" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                </div>
                <div class="px-15 flex-fill bsapp-scroll bsapp-overflow-y-auto" >
                    <div class="d-flex flex-column  justify-content-between h-100 ">
                        <div>
                            <div class="form-group  mb-15">
                                <div class="border-bottom border-gray-300 window-message-user">
                                    <select class="js-user-select2" id="jsUsers">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-20">
                                <div class="bsapp-max-w-250p">
                                    <select id="selectedMsgTo" class="js-select-message2">
                                        <option value="0" selected><?= lang('free_push_message') ?></option>
<!--                                        --><?php //if (Auth::userCan('88')): ?>
                                            <option value="1"><?= lang('sms_message_pay') ?></option>
<!--                                        --><?php //endif ?>
                                        <option value="2"><?= lang('email_free') ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group subject-form mb-20 d-none">
                                <input type="text" class="form-control bg-light border-light" id="emailOnly" placeholder=" <?= lang('desk_email_title') ?>">
                            </div>
                            <div class="form-group mb-20">
                                <textarea class="form-control summernote" id="clientemailmessage" name="Message" placeholder="<?= lang('class_send_message') ?>" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-15 pt-15">
                    <div class="d-flex  justify-content-end form-group">
                        <a class="btn btn-outline-dark border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10 cancelBtn" href="javascript:;"><?= lang('action_cacnel') ?></a>
                        <button type="submit" class="btn btn-primary border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10"><?= lang('send') ?></button>
                    </div>
                    <div class="response"></div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal :: end -->
<!-- modal :: begin -->
<!-- <div class="modal fade px-0 px-sm-auto text-start  js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-window-message-user"  data-backdrop="static">
    <div class="modal-dialog   modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100  bsapp-overflow-y-auto position-relative">
                <div class="d-flex justify-content-between w-100 mb-20">
                    <h5 class="bsapp-fs-18"><i class="fal fa-paper-plane mie-10"></i><?= lang('send_message') ?></h5>
                    <a  class="text-dark" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                </div>
                <form   style="height : calc( 100% - 50px )">
                    <div class="d-flex flex-column  justify-content-between h-100 ">
                        <div>
                            <div class="form-group  mb-15">
                                <div class="border-bottom border-gray-300 window-message-user">
                                    <select class="js-user-select2 ">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-20">
                                <div class="bsapp-max-w-250p">
                                    <select class="js-select-message2">
                                    <option value="0" selected><?= lang('free_push_message') ?></option>
<?php //if (Auth::userCan('88')): ?>
                                                                                                                                                                                                                                                                                                                                                                                                                    <option value="1"><?= lang('sms_message_pay') ?></option>
<?php //endif ?>
                                    <option value="2"><?= lang('email_free') ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group mb-20">
                                <input class="form-control bg-light border-light" placeholder="כותרת (רק במייל) ">
                            </div>
                            <div class="form-group mb-20">
                                <textarea class="form-control summernote" id="clientemailmessage" name="Message" placeholder="תוכן ההודעה" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="d-flex  justify-content-end form-group">
                            <a class="text-gray-500 text-decoration-none bsapp-fs-18  mie-40" href=""><?= lang('action_cacnel') ?></a>
                            <a class="text-primary text-decoration-none bsapp-fs-18 " href=""><?= lang('send') ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->
<!-- modal :: end -->
<!-- modal :: begin -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close " tabindex="-1" role="dialog" id="js-modal-window-print" data-backdrop="static">
    <div class="modal-dialog  modal-dialog-centered m-0 m-sm-auto h-100">
        <div class="modal-content border-0 shadow-lg" style="min-height:calc( 100% - 420px );">
            <div class="modal-body h-100 d-flex flex-column position-relative px-0 py-0" id="js-modal-window-print-content" >

            </div>
        </div>
    </div>
</div>
<!-- modal :: end -->
<!-- modal :: begin -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close " tabindex="-1" role="dialog" id="js-modal-add-user" data-backdrop="static">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" >
            <form class="modal-body bsapp-overflow-y-auto position-relative d-flex justify-content-between flex-column" id="assign-client-form" style="height : calc( 100vh - 120px );">
                <div class="d-flex justify-content-between w-100 mb-20">
                    <h5 class="bsapp-fs-18"><?= lang('assign_trainee_to_class') ?></h5>
                    <a class="text-dark cursor-pointer" data-dismiss="modal"><i class="fal fa-times h4"></i></a>
                </div>
                <div  class="pointForm flex-fill d-flex flex-column justify-content-start overflow-auto bsapp-scroll" >
                    <div class="d-flex flex-column h-100  bsapp-scroll bsapp-overflow-y-auto" >
                        <div class="h-100">
                            <div class="text-gray-500 mb-15 bsapp-fs-14">
                                <?= lang('client_details_class'); ?>
                            </div>
                            <div class="form-group d-flex align-items-center mb-15 custom-select-tags">
                                <label class="fal fa-search mie-10 my-auto">
                                </label>
                                <div class="flex-fill">
                                    <div class="userselectContainers">
                                        <div class="userField">
                                            <input type="hidden" id="userTypeNew" value="0">
                                            <select class="js-user-search w-100" id="js-user-search">
                                                <option></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="js-user-data-details d-flex flex-column">
                                <div class="form-group  d-none flex-column mb-15 ">
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="fal fa-user-circle mie-10 my-auto">
                                        </label>
                                        <div class="d-flex align-items-center flex-fill position-relative justify-content-between">
                                            <input type="hidden" class="d-none" name="is-new">
                                            <input type="hidden" class="d-none" name="client-id">
                                            <input type="hidden" class="d-none" name="client-isLead">
                                            <input required type="text" class="form-control bg-light border-light pie-100" placeholder="User Name" name="client-name"  id="js-user-name"/>
                                            <div class="position-absolute d-flex bsapp-position-end-12">
                                                <div>
                                                    <a class="text-secondary" onclick="modalClassPopup.showSearchField(this)"><i class="fal fa-times"></i></a>
                                                </div>
                                                <div class="js-client-is-new mis-8">
                                                    <div class="badge badge-info badge-pill"><?= lang('new'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group d-none  flex-column mb-15 ">
                                    <div class="d-flex align-items-center mb-2">
                                        <label class="fal fa-phone mie-10 my-auto">
                                        </label>
                                        <div class="d-flex align-items-center flex-fill position-relative justify-content-between">
                                            <input required type="text" class="form-control bg-light border-light pie-40" placeholder="<?= lang('settings_phone') ?>" name="js-user-phone" id="js-user-phone"/>
                                            <div class="position-absolute d-flex bsapp-position-end-12">
                                                <div class="js-client-phone-valid">
                                                    <i class="fal fa-check  text-info"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pis-25">
                                        <a class="text-info bsapp-fs-14 cursor-pointer " onclick="modalClassPopup.showSearchField(this);"><i class="fal fa-long-arrow-right mie-5"></i><?= lang('search_client'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <!--div class="form-group d-flex align-items-center mb-15">
                                <label class="fal fa-phone mie-10 my-auto">
                                </label>
                                <div class="flex-fill">
                                    <select class="js-user-number-select2" id="userNumber">
                                        <option></option>
                                    </select>
                                </div>
                            </div-->
                            <div id="alert-created-as-lead" class="alert alert-warning d-none" role="alert">
                                <strong><?= lang('attention_app_out') ?></strong> <br>
                                <?= lang('new_lead_creating_notice') ?>
                            </div>
                            <div id="add-client-errors" class="alert alert-danger d-none" role="alert">

                            </div>
                            <div class="show-field">
                                <div class="form-group bsapp-fs-14 text-gray-500">
                                    <?= lang('charge_options'); ?>
                                </div>
                                <div class="form-group d-flex align-items-center mb-15">
                                    <div class="flex-fill">
                                        <div class="d-flex flex-fill">
                                            <label class="fal fa-receipt mie-10 my-auto">
                                            </label>
<!--                                            <div class="flex-fill">-->
                                                <select name="charge-option-exist" id="js-charge-options-exist" class="js-charge-options js-select-custom2 d-none">
                                                    <option value="choose-membership"><?= lang('desk_select_from_subscription') ?></option>
                                                    <option value="without-charge"><?= lang('without_charge'); ?></option>
                                                    <option value="single-payment"><?= lang('one_time_payment_desk') ?></option>
                                                    <option value="new-membership"><?= lang('add_new_subscription_desk') ?></option>
                                                </select>
<!--                                            </div>-->
<!--                                            <div class="">-->
                                                <select name="charge-option-new" id="js-charge-options-new" class="js-charge-options js-select-custom2 d-none">
                                                    <option value="without-charge"><?= lang('without_charge'); ?></option>
                                                    <option value="single-payment"><?= lang('one_time_payment_desk') ?></option>
                                                    <option value="new-membership"><?= lang('add_new_subscription_desk') ?></option>
                                                </select>
<!--                                            </div>-->
                                        </div>
                                        <div id="new-membership" class="js-charge-option pis-25 d-none ">
                                            <div class="form-group d-flex align-items-center my-15">
                                                <div class="mie-15 mie-15 flex-fill">
                                                    <select name="new-membership-select" class="js-select-custom2" id="js-items-container">

                                                    </select>
                                                </div>
                                                <div class="input-group w-150p rounded overflow-hidden">
                                                    <input name="new-membership-amount" type="number" class="form-control border border-light bg-light rounded-0">
                                                </div>
                                            </div>
                                        </div>
                                        <div id="single-payment" class="js-charge-option pis-25 d-none">
                                            <div class="form-group d-flex align-items-center my-15">
                                                <div class="input-group w-150p rounded overflow-hidden">
                                                    <input name="single-payment-amount" type="number"
                                                           placeholder="<?= lang('summary') ?>" class="form-control border border-light bg-light rounded-0">
                                                </div>
                                            </div>
                                        </div>
                                        <div id="choose-membership" class="js-charge-option pis-25 ">
                                            <div class="form-group d-flex align-items-center my-15">
                                                <div class="error-container pis-5 overflow-auto bsapp-max-h-100p bsapp-scroll flex-fill" id="client-activities">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="assign-type-label" class="">
                                    <div class="form-group bsapp-fs-14 text-gray-500">
                                        <?= lang('desk_booking_type') ?>
                                    </div>
                                    <div class="form-group d-flex align-items-center mb-15">
                                        <div class="flex-fill">
                                            <div style="display:inline-flex;">
                                                <label class="fal fa-bullseye-arrow mie-10 my-auto"></label>
                                                <div class="flex-fill w-200p">
                                                    <select id="assign-type" name="assign-type" aria-describedby="assign-typeFeedback" class="js-select-custom2 chooseOption">
                                                        <option value="1"><?= lang('one_time_payment') ?></option>
                                                        <option value="2"><?= lang('desk_series_booking') ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="multi-assign-div" class="error-container form-group  align-items-start mb-15 showOption d-none">
                                    <label class="w-100 bsapp-fs-14 text-gray-500">
                                        <?= lang('desk_ends') ?>
                                    </label>
                                    <div class="form-group align-items-start mb-15">
                                        <div class="pis-30 flex-fill custom-group-radio">
                                            <div class="custom-control custom-radio mb-15">
                                                <input value="never" type="radio" id="optionRadio1" name="multi-assign" class="custom-control-input">
                                                <label class="custom-control-label d-flex align-items-center" for="optionRadio1"><?= lang('cal_never'); ?></label>
                                            </div>
                                            <div class="custom-control custom-radio mb-15">
                                                <input value="by-date" type="radio" id="optionRadio2" name="multi-assign" class="custom-control-input">
                                                <label class="custom-control-label d-flex align-items-center" for="optionRadio2">
                                                    <?= lang('in_date_cron'); ?>
                                                    <input name="assign-until-date" type="date" class="form-control bg-light border-light js-datepicker w-50 mr-10" /></label>
                                            </div>
                                            <div class="custom-control custom-radio mb-15">
                                                <input value="by-count" type="radio" id="optionRadio3" name="multi-assign" class="custom-control-input">
                                                <label class="custom-control-label d-flex align-items-center" for="optionRadio3">
                                                    <?= lang('after_cal') ?>
                                                    <input name="assign-by-count" type="number" class="form-control bg-light border-light col-2 mx-10" />
                                                    <?= lang('shows_desk_plan') ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <div class="pis-30 flex-fill custom-group-radio">
                                        <div class="custom-control custom-radio mb-15">
                                            <input type="radio" name="customRadios3" class="custom-control-input">
                                            <label class="custom-control-label d-flex align-items-center" for="customRadio3">
                                                Some Text
                                                <input type="date" class="form-control bg-light border-light js-datepicker w-50 mr-10" />
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio mb-15">
                                            <input type="radio" name="customRadios4" class="custom-control-input">
                                            <label class="custom-control-label d-flex align-items-center" for="customRadio4">
                                                Some Text
                                                <input type="text" placeholder="Text here..." class="form-control bg-light border-light js-datepicker w-50 mr-10" />
                                            </label>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-15">
                    <a class="btn btn-outline-dark border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10" href="#" data-dismiss="modal"><?= lang('action_cacnel') ?></a>
                    <a class="js-submit-client-assignment btn btn-primary border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10" href="javascript:;"><?= lang('save') ?></a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- modal :: end -->
<!-- modal :: begin -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-cancel-action" data-backdrop="static">
    <div class="modal-dialog  modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-0" id="js-modal-cancel-action-content" >

            </div>
        </div>
    </div>
</div>
<!-- modal :: end -->
<!-- modal :: begin -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-edit-class-popup" data-backdrop="static">
    <div class="modal-dialog  modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-15">
                <div class="d-flex justify-content-between w-100 mb-20">
                    <h5 class="bsapp-fs-20 p-15"><?= lang('cancel_class_app_booking') ?> </h5>
                    <a class="text-dark p-15" data-dismiss="modal" href="javascript:;"><i class="fal fa-times h4"></i></a>
                </div>
                <div class=" mb-15 px-15">
                    <?= lang('q_are_sure_app_booking') ?>
                </div>
                <form class="px-15" style=" height: calc( 100% - 50px ); ">
                    <div class="d-flex flex-column justify-content-between h-100 ">
                        <div class="bsapp-max-h-300p bsapp-min-h-300p bsapp-scroll overflow-auto mb-15">
                            <div class="form-group">
                                <label class="mb-15"><?= lang('desk_how_delete') ?> </label>
                                <div class="pis-15">
                                    <div class="custom-control custom-radio mb-15">
                                        <input type="radio" id="js-ed-radio-1" name="edit_action_radio" class="custom-control-input">
                                        <label class="custom-control-label" for="js-ed-radio-1"><?= lang('one_time_payment') ?></label>
                                    </div>
                                    <div class="custom-control custom-radio mb-15">
                                        <input type="radio" id="js-ed-radio-2" name="edit_action_radio" class="custom-control-input" data-item-event="radio" data-item-class="" data-item-show="">
                                        <label class="custom-control-label" for="js-ed-radio-2"><?= lang('delete_class_series') ?> </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group  flex-column   d-none" data-context="js-ed-radio-2">
                                <label class="mb-15">כל השיעורים </label>
                                <div class="mb-15 bsapp-max-w-200p">
                                    <select class="js-select2-edit-class" id="js-ed-series-dropdown">
                                        <option value="js-ed-series-option-1">Option 1</option>
                                        <option value="js-ed-series-option-2">Option 2</option>
                                        <option value="js-ed-series-option-3">Option 3</option>
                                    </select>
                                </div>
                                <div class="form-group  align-items-center mb-15  d-none" data-context='js-ed-series-option-2'>
                                    <span class="mie-8"> Date Field </span> <input class="form-control js-datepicker  bg-light  bsapp-max-w-150p  border-light  mie-8"><span class="mie-8"> - text - </span> <input class="form-control bsapp-max-w-150p mie-8  bg-light border-light js-datepicker ">
                                </div>

                                <div class="form-group   align-items-center mb-15  d-none" data-context='js-ed-series-option-3'>
                                    <span class="mie-8"> Text Field </span> <input type="text" class="form-control bg-light  bsapp-max-w-60p  border-light  mie-8 px-6"><span class="mie-8"> with number </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-end h-100 form-group mb-20">
                            <a class="text-gray-500 bsapp-fs-18 text-decoration-none mie-40 " data-dismiss="modal" href="javascript:;"><?= lang('cancel') ?> </a>
                            <a class="text-primary bsapp-fs-18 text-decoration-none" href="javascript:;"><?= lang('save') ?> </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- modal :: end -->
<!-- modal :: begin -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close " tabindex="-1" role="dialog" id="js-modal-window-share" data-backdrop="static">
    <div class="modal-dialog  modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-0">
                <div class="d-flex justify-content-between w-100 mb-20">
                    <h5 class="bsapp-fs-18 p-15"><i class="fal fa-link mie-10"></i><?= lang('share_register_link'); ?></h5>
                    <a class="text-dark p-15" data-dismiss="modal" href="javascript:;"><i class="fal fa-times h4"></i></a>
                </div>
                <div class="px-15" style="height : calc( 100% - 50px ); ">
                    <div class="d-flex flex-column justify-content-between h-100 sharing-box bsapp-min-h-100p">
                        <div class="showCopyLinkBtn form-group text-center">
                            <button class="btn btn-primary">Get More Link</button>
                        </div>
                        <div class="copylink-box">
                            <div class="form-group border-bottom">
                                <input type="text"  class="form-control border-0 js-link-to-copy" value="https://1ba.co/hsRvKqBA">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-light js-copy-link-button w-30"><i class="fal fa-copy mie-10"></i><?= lang('copy_link'); ?></button>
                                <button class="btn btn-success js-copy-link-succeed d-none w-30"><i class="far fa-check mie-10"></i><?= lang('copied_to_clipboard') ?></button>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end  form-group">
                        <a class="text-primary bsapp-fs-18 text-decoration-none close-modal cursor-pointer"><?= lang('close'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- modal :: end -->
<!-- modal :: begin -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-6" data-backdrop="static">
    <div class="modal-dialog  modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100 overflow-auto position-relative px-0 py-15">
                <div class="d-flex justify-content-between w-100 mb-20">
                    <h5 class="bsapp-fs-18 p-15">בשמירת שינויים </h5>
                    <a class="text-dark p-15" data-dismiss="modal"  href="javascript:;"><i class="fal fa-times h4"></i></a>
                </div>
                <div class=" mb-15 px-15">
                    הבאפשרתך לבצע שמירה מתקדמת אם את/ה מעוניינ/ת ששינויים אלו ישמרו על סדרת השיעורים
                </div>
                <form class="h-100 px-15">
                    <div class="d-flex flex-column  h-100 ">
                        <div class="form-group">
                            <label class="mb-15">אופן השמירה </label>
                            <div class="pis-15">
                                <div class="custom-control custom-radio mb-15">
                                    <input type="radio" id="custom6Radio1" name="customRadios" class="custom-control-input">
                                    <label class="custom-control-label" for="custom6Radio1"> <?= lang('one_time_payment') ?> </label>
                                </div>
                                <div class="custom-control custom-radio mb-15">
                                    <input type="radio" id="custom6Radio2" name="customRadios" class="custom-control-input">
                                    <label class="custom-control-label" for="custom6Radio2"> <?= lang('delete_class_series') ?> </label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end  h-100 align-items-end form-group mb-20">
                            <a class="text-gray-500 mie-40" href=""><?= lang('action_cacnel') ?></a>
                            <a class="text-gray-500" href=""><?= lang('send') ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- modal :: end -->

<div class="modal fade px-0 px-sm-auto js-modal-no-close" tabindex="-1" role="dialog" id="js-confirmation-modal">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content ">
            <div class="modal-body h-100 bsapp-overflow-y-auto">
                <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
                    <div class="d-flex justify-content-between w-100 px-0 py-15">
                        <h6 class=""><?= lang('attendance') ?></h6>
                        <a class="text-dark close-modal"><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="d-flex  flex-column text-center  my-50 px-15">
                        <div class="w-100 mb-10">
                            <label class="badge badge-light badge-pill px-30">
                                <h1 class="text-primary"><i class="fal fa-check"></i></h1>
                            </label>
                        </div>
                        <div class="w-100"><?= lang('are_you_sure_attendent') ?></div>
                    </div>
                    <div class="d-flex justify-content-around w-100 ">
                        <a class="btn btn-primary flex-fill mie-15 js-mark-many-attended" data-status="2"><?= lang('yes') ?></a>
                        <a class="btn btn-light flex-fill close-modal"><?= lang('no') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Choose device modal -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-device-add">
    <div class="modal-dialog  modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative">
                <div id="js-modal-device-add-content" class="h-100 bsapp-overflow-y-auto position-relative">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Choose device modal :: end -->

<div id="greenPassModalClientList"></div>

<!-- Over max client modal -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" data-backdrop="static" role="dialog" id="js-modal-over-max-client">
    <div class="modal-dialog  modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body bsapp-overflow-y-auto position-relative px-0 p-15">
                <div id="js-modal-over-max-client-content" class="bsapp-min-h-400p h-100  bsapp-overflow-y-auto position-relative ">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Over max client :: end -->

<!-- Canceled to active modal -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-canceled-to-active">
    <div class="modal-dialog  modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-15">
                <div id="js-modal-canceled-to-active-content" class="h-100 bsapp-overflow-y-auto position-relative">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Canceled to active modal :: end -->
<?php
$TemplatesCounts = DB::table('classstudio_date_template')->where('CompanyNum', '=', $CompanyNum)->count();

if ($TemplatesCounts == '') {
    $TemplatesCounts = '0';
}
?>
<div class="modal fade px-0 px-sm-auto" tabindex="-1" role="dialog" id="js-action-modal">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="d-flex flex-column  h-100 ">
                    <div class="d-flex justify-content-between w-100">
                        <h5 class="p-15"><?= lang('select_action') ?> </h5>
                        <a href="javascript:;"  class="bsapp-fs-24 text-dark p-15" data-dismiss="modal" ><i class="fal fa-times"></i></a>
                    </div>
                    <div class="px-15">
                        <ul class="list-group list-group-flush px-0 my-20"  >
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="newClass">
                                <div><i class="fal fa-calendar-plus pl-7"></i> <?= lang('a_new_class') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="new-class" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="new-class"></label>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="newMeeting">
                                <div><i class="fal fa-calendar-plus pl-7"></i> <?= lang('new_meeting') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="new-meeting" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="new-meeting"></label>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" onclick="blockEvent()">
                                <div><i class="fal fa-calendar-plus pl-7"></i> <?= lang('close_calendar_title') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="block-event" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="block-event"></label>
                                </div>
                            </li>
                            <?php if ($TemplatesCounts > 0) { ?>
                                <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="newPersonalClass">
                                    <div><i class="fal fa-calendar-star pl-7"></i> <?= lang('personal_class') ?></div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="personal-class" name="customRadio" class="custom-control-input">
                                        <label class="custom-control-label" for="personal-class"></label>
                                    </div>
                                </li>
                            <?php } ?>
                            <?php
                            // TODO remove beta check after beta - BS-1823
                            if (in_array($CompanySettingsDash->beta, [1,2])) { ?>
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="newTask">
                                <div><i class="fal fa-calendar-star pl-7"></i> <?= lang('new_task') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="personal-class" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="personal-class"></label>
                                </div>
                            </li>
                            <?php } ?>
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="removeClasses">
                                <div><i class="fal fa-do-not-enter pl-7"></i> <?= lang('remove_classes') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="remove-class" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="remove-class"></label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade px-0 px-sm-auto js-modal-no-close" tabindex="-1" role="dialog" id="js-confirmation-modal-2">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content">
            <div class="modal-body  h-100 px-0 py-15">
                <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
                    <div class="d-flex justify-content-between w-100">
                        <h6 class="p-15"><?= lang('attendance') ?></h6>
                        <a href="javascript:;"  class="text-dark p-15" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="d-flex  flex-column text-center  my-50 px-15">
                        <div class="w-100 mb-10">
                            <label class="badge badge-light badge-pill px-30">
                                <h1 class="text-danger"><i class="fal fa-times"></i></h1>
                            </label>
                        </div>
                        <div class="w-100"><?= lang('are_you_sure_attendent') ?></div>
                    </div>
                    <div class="d-flex justify-content-around w-100 px-15">
                        <a class="btn btn-primary flex-fill mie-15 js-mark-many-attended" data-status="8"><?= lang('yes') ?></a>
                        <a class="btn btn-light flex-fill close-modal"><?= lang('no') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</div>

<div class="modal px-0 px-sm-auto js-modal-no-close" tabindex="-1" role="dialog" id="js-remove-clients-modal">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content">
            <div class="modal-body  h-100">
                <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
                    <div class="d-flex justify-content-between w-100">
                        <h6><?= lang('remove_selected_trainees') ?></h6>
                        <a class="text-dark close-modal"><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="d-flex  flex-column text-center  my-50">
                        <div class="w-100 mb-10">
                            <label class="badge badge-light badge-pill px-30">
                                <h1 class="text-danger"><i class="fal fa-trash-alt"></i></h1>
                            </label>
                        </div>
                        <div class="w-100 "><?= lang('q_action_notice') ?></div>
                    </div>
                    <div class="d-flex justify-content-around w-100">
                        <a class="confim-btn btn btn-danger flex-fill mie-15 js-remove-many-clients"><?= lang('yes') ?><span class="js-loader-spin mis-5" style="display:none;"><i class="fad fa-spinner-third fast-spin"></i></span></a>
                        <a class="btn btn-light flex-fill close-modal"><?= lang('no') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade px-0 px-sm-auto js-modal-no-close" tabindex="-1" role="dialog" id="js-charpopup-confirmation-modal">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content">
            <div class="modal-body  h-100 px-0 pt-0 pb-15">
                <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
                    <div class="d-flex justify-content-between w-100">
                        <h5 class="p-15"><?= lang('waiting_list_app_booking') ?></h5>
                        <a href="javascript:;"  class="text-dark p-15" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="d-flex  flex-column text-center  my-50 px-15">
                        <div class="w-100 mb-10">
                            <label class="badge badge-light badge-pill px-30">
                                <h1 class="text-danger"><i class="fal fa-info-circle"></i></h1>
                            </label>
                        </div>
                        <div class="w-100 text-center ">
                            <p><?= lang('attention_app_out') ?></p>
                            <p><?= lang('wating_list_available_space_notice') ?></p>
                            <p><?= lang('waitlist_footernew') ?></p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-around w-100 px-15">
                        <a class="btn btn-info flex-fill mie-15" onclick="charPopup.runWaitingList(this)" href="javascript:;" ><span class="js-loader-spin mie-8" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span><?= lang('run_waiting_list') ?></a>
                        <a class="btn btn-light flex-fill" href="javascript:;" onclick="charPopup.close(this, true);" ><?= lang('no') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assigned over limitation modal :: start -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-over-limitation">
    <div class="modal-dialog  modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative p-0">
                <div id="js-modal-over-limitation-data"></div>
                <div id="js-modal-over-limitation-content" class="modal-body h-100 bsapp-overflow-y-auto position-relative">

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Assigned over limitation modal :: end -->

<!-- Regular assignment over max client modal :: start -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-over-max-regular">
    <div class="modal-dialog  modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative p-0">
                <div class="d-none" id="js-modal-over-max-regular-data"></div>
                <div id="js-modal-over-max-regular-content" class="h-100 bsapp-overflow-y-auto position-relative p-15">

                    <div class="d-flex flex-column mx-20 h-50">
                        <a class="text-dark close-modal"><i class="fal fa-times h4"></i></a>

                        <div class="d-flex flex-column text-center my-30">
                            <p class="w-100 "><?= lang('max_exceeds_assign') ?></p>
                            <p class="font-weight-bold w-100"><?= lang('desk_select_action') ?></p>
                        </div>

                        <div class="d-flex justify-content-around w-100">
                            <a onclick="modalOverMaxRegular.forceAssignment($(this), 12)" class="btn btn-danger text-white flex-fill mie-15" data-button-text="<?= lang('book_and_exceed_desk') ?>"><?= lang('book_and_exceed_desk') ?></a>
                            <a onclick="modalOverMaxRegular.forceAssignment($(this), 9)" class="btn btn-light flex-fill" data-button-text="<?= lang('desk_book_as_waiting') ?>"><?= lang('desk_book_as_waiting') ?></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Regular assignment over max client modal :: end -->


<!-- Regular assignment max client or assignment to waiting list :: start -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-over-max-or-waiting-list">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg p-15">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative p-0">
                <div class="d-none" id="js-modal-over-max-or-waiting-list-data"></div>
                <div id="js-modal-over-max-or-waiting-list" class="h-100 bsapp-overflow-y-auto position-relative">

                    <div class="d-flex flex-column h-50">
                        <a class="text-dark close-modal"><i class="fal fa-times h4"></i></a>

                        <div class="d-flex flex-column text-center my-30">
                            <p class="w-100 "><?= lang('max_exceeds_assign')?></p>
                            <p class="font-weight-bold w-100"><?= lang('desk_select_action') ?></p>
                        </div>

                        <div class="d-flex justify-content-around w-100">
                            <a onclick="modalAssignOverMaxOrWaitingList.forceAssignment($(this), 1)" class="btn btn-danger text-white flex-fill mie-15" data-button-text="<?= lang('book_and_exceed_desk') ?>"><?= lang('book_and_exceed_desk') ?></a>
                            <a onclick="modalAssignOverMaxOrWaitingList.forceAssignment($(this), 12)" class="btn btn-light flex-fill" data-button-text="<?= lang('desk_book_as_waiting') ?>"><?= lang('desk_book_as_waiting') ?></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- Regular assignment over max client modal :: end -->


<!-- Late cancellation modal -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-modal-late-cancel">
    <div class="modal-dialog  modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-0">
                <div class="bsapp-min-h-400p bsapp-overflow-y-auto position-relative ">
                    <div class="d-flex justify-content-between w-100">
                        <h5 class="p-15"><?= lang('late_cancellation') ?></h5>
                        <a href="javascript:;"  class="text-dark p-15" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="d-flex  flex-column text-center  my-50 px-15">
                        <div class="w-100 mb-10">
                            <label class="badge badge-light badge-pill px-30">
                                <h1 class="text-danger"><i class="fal fa-trash-alt"></i></h1>
                            </label>
                        </div>
                        <div class="w-100 text-center ">
                            <p><?= lang('late_cancel_popup') ?></p>
                            <p><?= lang('select_cancel_type') ?></p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-around w-100 px-15">
                        <a class="btn btn-danger flex-fill mie-15" href="javascript:;" data-status="4" onclick="charPopup.removeClientLateCancel(this)"><?= lang('late_cancellation') ?></a>
                        <a class="btn btn-light flex-fill" href="javascript:;" data-status="3" onclick="charPopup.removeClientLateCancel(this)"><?= lang('regular_cancel') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Late cancellation :: end -->

<!-- Class content modal -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close " tabindex="-1" role="dialog" id="js-modal-class-content" data-backdrop="static">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" >
            <form class="modal-body bsapp-overflow-y-auto position-relative d-flex justify-content-between flex-column" id="update-class-content" style="height : calc( 100vh - 120px );">
                <div class="d-flex justify-content-between w-100 mb-20">
                    <h5 class="d-flex align-items-center bsapp-fs-18">
                        <i class="fal fa-align-right"></i>
                        <span class="mx-7"><?= lang('class_description'); ?></span>
                    </h5>
                    <a class="text-dark" data-dismiss="modal"><i class="fal fa-times h4"></i></a>
                </div>
                <div  class="flex-fill d-flex flex-column justify-content-start overflow-auto bsapp-scroll" >
                    <div class="d-flex flex-column h-100  bsapp-scroll bsapp-overflow-y-auto" >
                        <div  class="form-group mb-20">
                            <label for="Remarks"><?= lang('class_content_cal'); ?></label>
                            <textarea class="form-control summernote" id="Remarks" name="Remarks" placeholder="<?= lang('type_here_class_content') ?>" rows="5"></textarea>
                        </div>
                        <label for="display-options"><?= lang('cal_display_options') ?></label>
                        <div class="form-group mb-20 d-flex justify-content-center">
                            <select class="js-select2" id="RemarksStatus" name="RemarksStatus" style="width: 98%;">
                                <option value="0"><?= lang('club_staff_and_customers') ?></option>
                                <option value="1"><?= lang('club_staff') ?></option>
                            </select>
                        </div>
                        <div class="form-group mb-20">
                            <label><?= lang('saving_options_calendar') ?></label>
                            <div id="js-save-options" class="mx-20">
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" id="saveOption1" class="custom-control-input" name="saveOption1" value="series">
                                    <label class="custom-control-label" for="saveOption1"><?= lang('all_the_lesson_from_series') ?></label>
                                </div>
                                <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" id="saveOption2" class="custom-control-input" name="saveOption2" value="day">
                                    <label class="custom-control-label" for="saveOption2"><?= lang('all_lessons_from_day') ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-15">
                    <a class="btn btn-outline-dark border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10" href="#" data-dismiss="modal"><?= lang('action_cacnel') ?></a>
                    <a class="btn btn-primary border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10" onclick="charPopup.submitEditClassContent(charPopup.class_info.classid, this)" href="javascript:;"><?= lang('save') ?></a>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Class content :: end -->

<!-- Tag choosing modal -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" data-backdrop="static" tabindex="-1" role="dialog"
     id="js-tag-category-popup">
    <div class="modal-dialog justify-content-center modal-dialog-centered" >
        <div class="modal-content border-0 shadow-lg modal-tag">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-0 d-flex flex-column">

                <div class="d-flex justify-content-between align-items-center  border-bottom border-light">
                    <div class="w-150p px-15 py-15">
                        <span class="bsapp-fs-18 text-black font-weight-500"> בחירת תגית </span>
                    </div>
                    <a href="javascript:;" class="text-dark bsapp-fs-18 p-15" data-dismiss="modal" onclick="fieldEvents.restartTagsCategory()">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
                <div class="tag-main-box">
                    <div class="category-container">
                        <div id="favorite-categories">
                            <div class="bsapp-fs-18 tag-favorite-header"> קטגוריות תואמות לעיסוק שלך </div>
                        </div>
                        <div id="other-categories">
                            <div class="bsapp-fs-14 text-dark tag-other-header"> קטגוריות נוספות </div>
                        </div>
                    </div>

                    <div class="tag-container d-none">
                        <div id="chosenCategory"></div>
                        <div id="availableTags"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer tag-container d-none">
                <div class="modal-footer-text">לא מצאתם תגית מתאימה גם בקטגוריות האחרות? ניתן לשלוח אלינו בקשה להוספה ונשקול זאת :)
                    <div class="modal-footer-link-text" onclick="fieldEvents.showTagRequest()">שלח בקשה לתגית חסרה</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Tag choosing modal :: end -->

<!-- Tag request model -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" data-backdrop="static" tabindex="-1" role="dialog"
     id="js-tag-request-popup">
    <div class="modal-dialog justify-content-center modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-request">
            <div class="modal-header position-relative px-0 py-0">

                <div class="d-flex justify-content-between align-items-center border-bottom border-light width-100">
                    <div class="px-15 py-15">
                        <span class="bsapp-fs-18 text-black font-weight-500"> בקשה להוספת תגית  </span>
                    </div>
                    <a href="javascript:;" class="text-dark bsapp-fs-18 p-15" data-dismiss="modal" onclick="fieldEvents.restartTagsCategory()">
                        <i class="fal fa-times"></i>
                    </a>
                </div>
            </div>
            <div class="modal-body bsapp-overflow-y-auto position-relative d-flex flex-column">
                <div class="tag-search-main-box">
                    <div class="tag-request-text tag-request-text-padding bsapp-fs-16"> ניתן לשלוח לנו בקשה לעדכן תגית חדשה במערכת </div>
                    <div class="userField">
                        <select class="js-user-search w-100" id="js-tag-search">
                            <option></option>
                        </select>
                    </div>
                </div>

                <div class="justify-content-end d-none request-send">
                    <div class="tag-request-text bsapp-fs-14"> בקשתך נקלטה!<br>שים/י לב, יש לבחור מהתגיות הקיימות, במידה והבקשה שלך תאושר נעדכן אותך על כך! </div>
                </div>
            </div>
            <div class="modal-footer">
                <button href="javascript:;" class="btn btn-primary btn btn-block btn-request" id="requestBtn" onclick="fieldEvents.tagRequest(this)"> שלח בקשה </button>
            </div>
    </div>
</div>
</div>

<!-- Tag request model :: end -->




<!-- Group Edit modal -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" data-backdrop="static" tabindex="-1" role="dialog" id="js-group-edit-modal">
    <div class="modal-dialog  modal-dialog-centered bsapp-max-w-400p">
        <div class="modal-content border-0 shadow-lg bsapp-min-h-500p py-10 px-15">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-0 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2F mt-5">
                        <div>
                            <h6><?= lang('edit_as_group') ?></h6>
                        </div>
                        <a class="text-dark bsapp-fs-18 cursor-pointer close-modal">
                            <i class="fal fa-times"></i>
                        </a>
                    </div>
                    <div class="js-warning alert alert-warning bsapp-fs-14 px-10" role="alert">
                        <?= lang('group_edit_attention_alert') ?>
                    </div>
                    <div class="form-group mb-30">
                        <label class="mt-15"><?= lang('choose_edit_type') ?></label>
                        <div class="pis-15 is-invalid-container">
                            <div class="custom-control custom-radio my-15">
                                <input type="radio" name="group-edit" value="0" id="js-group-radio-1" class="custom-control-input" >
                                <label class="js-opt1 custom-control-label" for="js-group-radio-1"><?= lang('edit_single_lesson') ?></label>
                            </div>
                            <div class="custom-control custom-radio my-15">
                                <input type="radio" name="group-edit" value="1" id="js-group-radio-2" class="custom-control-input">
                                <label class="js-opt2 custom-control-label" for="js-group-radio-2"><?= lang('edit_lesson_series') ?></label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mb-2">
                    <a class="btn btn-outline-secondary mie-12 px-30 cursor-pointer close-modal"><?= lang('back_new_add_credit') ?></a>
                    <button onclick="fieldEvents.confirmGroupEdit(this)" class="btn btn-primary px-30"><?= lang('continue_indexnew') ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Group Edit modal :: end -->

<!-- Class time occupied modal -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" data-backdrop="static" tabindex="-1" role="dialog" id="js-occupied-modal">
    <div class="modal-dialog  modal-dialog-centered bsapp-max-w-400p">
        <div class="modal-content border-0 shadow-lg h-500p py-10 px-15">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-0 d-flex flex-column justify-content-between">
                <div class="d-flex flex-column justify-content-between h-100">
                    <div class="d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2F mt-5">
                            <div>
                                <h6><?= lang('pay_attention') ?></h6>
                            </div>
                            <a class="text-dark bsapp-fs-18 cursor-pointer" onclick="occupiedPopup.abort()">
                                <i class="fal fa-times"></i>
                            </a>
                        </div>
                        <p class="text-start m-0 mb-20 bsapp-fs-14 bsapp-lh-15">
                            <?= lang('meeting_time_occupied') ?>
                        </p>
                    </div>
                    <div class="overflow-auto mb-30 h-100">
                        <ul class="js-occupied-container">

                        </ul>
                    </div>
                    <div class="d-flex flex-column">
                        <p class="text-start m-0 mb-20 bsapp-fs-14 bsapp-lh-15">
                            <?= lang('do_you_want_to_procceed') ?>
                        </p>
                        <div class="d-flex justify-content-end">
                            <a onclick="occupiedPopup.abort()" class="btn btn-outline-secondary mie-12 px-30 cursor-pointer close-modal"><?= lang('back_new_add_credit') ?></a>
                            <button onclick="occupiedPopup.proceed()" class="btn btn-primary px-30"><?= lang('continue_indexnew') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Class time occupied modal :: end -->

<style>
    /*.dance {*/
    /*    color: #f35626;*/
    /*    background: #f35626;*/
    /*    background-image: -webkit-linear-gradient(92deg,#f35626,#feab3a);*/
    /*    -webkit-background-clip: text;*/
    /*    -webkit-animation: dance 1s infinite linear;*/
    /*}*/

    /*@keyframes dance {*/
    /*    0% {*/
    /*        transform: translate(1px, 1px) rotate(0deg);*/
    /*        -webkit-filter: hue-rotate(0deg);*/
    /*    }*/
    /*    10% { transform: translate(-1px, -2px) rotate(-1deg); }*/
    /*    20% { transform: translate(-3px, 0px) rotate(1deg); }*/
    /*    30% { transform: translate(3px, 2px) rotate(0deg); }*/
    /*    40% { transform: translate(1px, -1px) rotate(1deg); }*/
    /*    50% { transform: translate(-1px, 2px) rotate(-1deg); }*/
    /*    60% { transform: translate(-3px, 1px) rotate(0deg); }*/
    /*    70% { transform: translate(3px, 1px) rotate(-1deg); }*/
    /*    80% { transform: translate(-1px, -1px) rotate(1deg); }*/
    /*    90% { transform: translate(1px, 2px) rotate(0deg); }*/
    /*    100% {*/
    /*        transform: translate(1px, -2px) rotate(-1deg);*/
    /*        -webkit-filter: hue-rotate(360deg);*/
    /*    }*/
    /*}*/
</style>