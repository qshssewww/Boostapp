<?php
require_once '../app/init.php';
include_once('loader/loader.php');
require_once 'Classes/Company.php';
require_once 'Classes/Utils.php';
require_once 'Classes/ClassCalendar.php';
require_once __DIR__."/Classes/Client.php";
require_once "Classes/ClassesType.php";
require_once "Classes/ClassStudioDate.php";
require_once "Classes/Users.php";
require_once "Classes/EncryptDecrypt.php";
require_once "Classes/Brand.php";
require_once "Classes/MeetingTemplates.php";
require_once 'Classes/Settings.php';

$Company = Company::getInstance();
$CompanyNum = $Company->__get('CompanyNum');
$classCalendarObj = new ClassCalendar();
$currentTime = date('H'). ':' . str_pad(floor(date('i')/5)*5, 2, '0', STR_PAD_LEFT);

$class_data = $classCalendarObj->initClassData();

$membershipTypes = $class_data['membershipTypes'];
$clientLevels = $class_data['clientLevel'];
$brands = $class_data['brands'];
$classTypes = $class_data['classTypes'];
$coachers = $class_data['coaches'];
$colors = $class_data['colors'];
$deviceTypes = $class_data['deviceTypes'];
$user = $class_data['user'];

$classId = $_GET['classId'] ?? null;
$duplicate = $_GET['duplicate'] ?? 0;


//Templates Data
$MeetingTemplates = new MeetingTemplates();
$templateArray=[];
$templates = $MeetingTemplates->getActiveTemplatesByCompany($CompanyNum);
foreach ($templates as $template) {
    $template->setClassesType();
    $template->setCoaches();
    $template->setCalendars();
    $templateArray[] = $template->newMeetingTemplateDisplay();
}

?>
<!-- new meeting modal :: begin -->
<div class="modal-body d-flex flex-column justify-content-between p-0 h-100">
    <form id="new-class" class="h-100" method="post" onsubmit="fieldEvents.classActions.submitForm(this, event)" novalidate>
        <div class="js-subpage-home js-subpage-class-new h-100">
            <div class="d-flex justify-content-between align-items-center  border-bottom border-light">
                <?php if ($classId): ?>
                    <?php if ($duplicate): ?>
                        <div class="w-200p px-15 py-15">
                            <span class="mis-10"><?= lang('duplicate_lesson') ?></span>
                        </div>
                            <?php else: ?>
                        <input required class="d-none" name="CalendarId" value="<?= $classId ?>">
                        <input required class="d-none" name="GroupEdit">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <span class="mis-10 bsapp-fs-18 font-weight-bold"><?= lang('edit_lesson') ?></span>
                        </div>
                    <?php endif ?>
                <?php else: ?>
                <div class="w-200p px-15 py-15">
                    <select class="js-select2-dropdown-arrow js-select-create-new" >
                        <option value="new-class"><?= lang('class_creation') ?></option>
                        <option value="new-meeting"><?= lang('meeting_creation') ?></option>
                    </select>
                </div>
                <?php endif; ?>
                <a href="javascript:;" class="text-dark bsapp-fs-20 p-15 font-weight-bold" data-dismiss="modal">
                    <i class="fal fa-times"></i>
                </a>
            </div>

            <div class="bsapp-scroll">
                <div class="h-100 d-flex flex-column">
                    <ul class="nav nav-tabs border-0 pis-0 mb-5 bsapp-tab-active-underline" id="js-class-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="js-class-tab-link-general" data-toggle="tab" href="#js-class-tab-general" role="tab"  aria-selected="true"><?= lang('general') ?></a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="js-class-tab-link-advanced" data-toggle="tab" href="#js-class-tab-advanced" role="tab"  aria-selected="false"><?= lang('cal_templates_advanced') ?></a>
                        </li>
                    </ul>
                    <div class="tab-content overflow-auto" id="myTabContent">
                        <div class="tab-pane fade show active" id="js-class-tab-general" role="tabpanel" >
                            <div class="d-flex px-15">
                                <div class="form-group flex-fill mb-15 mie-15">
                                    <label class="" for="js-select2-class"><?= lang('class_name') ?></label>
                                    <div class="is-invalid-container">
                                        <input name="ClassName" maxlength="60" onfocusout="fieldEvents.showTagInfo(this)"
                                               class="form-control bg-light border-light" type="text" required autocomplete="off">
                                    </div>
                                </div>
                                <div class="form-group w-100p  mb-15" >
                                    <label class="" for="js-class-attendance"><?= lang('attendees') ?></label>
                                    <div class="is-invalid-container">
                                        <input name="MaxClient" max="999" class="form-control bg-light border-light"
                                               id="js-class-attendance" type="number" min="1" required/>
                                    </div>
                                    <div class="invalid-feedback">
                                        <?= lang('error_range_1_999') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex px-15">
                                <div class="form-group w-75 flex-fill mb-15 mie-15">
                                    <label class="" for="js-select2-class-type"><?= lang('lesson_type') ?></label>
                                    <div class="is-invalid-container">
                                        <input class="d-none" name="NewClassType" value="1">
                                        <input class="d-none" name="NewClassTypeMemberships">
                                        <select name="ClassNameType" onchange="fieldEvents.classActions.choseClassType(this)" id="js-select2-class-type" required>
                                            <?php foreach ($classTypes as $class): ?>
                                                <option data-duration="<?= $class->duration ?>" data-color="<?= $class->Color ?>"
                                                        data-text="<?= $class->Type ?>" value="<?= $class->id; ?>">
                                                    <?= $class->Type; ?>
                                                </option>
                                            <?php endforeach; ?>
                                            <option></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group w-100p  mb-15" >
                                    <label class="" for="js-select2-class-color"><?= lang('color_shop_render') ?></label>
                                    <div class="is-invalid-container">
                                        <select name="colorId" onchange="fieldEvents.classActions.choseColor(this)"
                                                id="js-select2-class-color" required>
                                            <?php foreach ($colors as $key => $color): ?>
                                                <option value="<?= $color->id ?>" data-color-code="<?= $color->hex ?>"><?= $color->id ?></option>
                                            <?php endforeach; ?>
                                            <option value="custom" disabled="disabled"></option>
                                        </select>
                                    </div>
                                    <input class="d-none" id="classColor" name="color" value="<?= $colors[0]->hex ?>" required>
                                </div>
                            </div>
                            <!-- START OF TAGS SECTION -->
<!--                            todo change when SECTION are ready-->
                            <?php if (false) { ?>
                            <div class="text-start px-15 tagInfo d-none">
                                <div class="form-group flex-fill mb-15">
                                    <label class="" for="js-select2-class"><?= lang('lesson_tag') ?></label>
                                    <div class="input-group">
                                        <input name="tag" class="form-control bg-light border-light" disabled style="border: 0; border-radius: 0" type="text" required autocomplete="off">
                                        <div class="input-group-prepend bg-light" style="border: 0; border-radius: 0">
                                            <a onclick="fieldEvents.showCategoryChoice()" href="javascript:;"
                                               data-id="js-items-tab-1-stuff" style="color:blue; padding:10px"
                                               class="bg-light border-light"> <?= lang('edit_two') ?> </a>
                                        </div>
                                    </div>
                                    <div class="js-tab-sub-preview bsapp-fs-12 text-muted" >בחרו תגית המתארת בצורה הקרובה ביותר את השיעור<br>
                                        *בחירה זו לא תוצג ללקוחות שלכם
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <!-- END OF TAGS SECTION -->
                            <div class="<?= (count($coachers) > 1) ? 'd-flex' : 'd-none' ?> js-div-tab-preview position-relative w-100
                        justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-top border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('desk_new_coaches') ?></div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="fas fa-user-circle bsapp-min-w-30p bsapp-fs-22"></i>
                                        <div class="js-tab-preview js-guide-name bsapp-fs-18 ">
                                            <?= lang('choose_coaches') ?>
                                            <input type="hidden" class="d-none js-guide-name">
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-1-stuff"   ></a>
                                </div>
                            </div>
                            <div class="<?= ($class_data['calendarCount'] > 1) ? 'd-flex' : 'd-none' ?> js-div-tab-preview position-relative w-100
                            justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('calendar_new_class') ?></div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="mie-5 fal fa-calendar bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="js-tab-preview bsapp-fs-18 ">
                                            <?= lang('choose_calendar') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-2-calendar"  ></a>
                                </div>
                            </div>
                            <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('date_and_shows') ?></div>
                                    <div class="d-flex align-items-center mb-3 mt-7" style="line-height: 1;">
                                        <i class="mie-5 fal fa-calendar-day bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="d-flex flex-column">
                                            <div class="js-tab-preview bsapp-fs-18 mb-1">
                                                <?= lang('select_date') ?>
                                            </div>
                                            <div class="js-tab-sub-preview bsapp-fs-12 text-muted"><?= lang('choose_freq') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-3-datenshows"  ></a>
                                </div>
                            </div>
                            <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('timing') ?></div>
                                    <div class="d-flex align-items-center mb-3 mt-7" style="line-height: 1;">
                                        <i class="mie-5 fal fa-clock bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="d-flex flex-column">
                                            <div class="js-tab-preview bsapp-fs-18 mb-1">
                                                <?= lang('class_time_dur') ?>
                                            </div>
                                            <div class="js-tab-sub-preview bsapp-fs-12 text-muted"><?= lang('notify_pref') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-4-timing"  ></a>
                                </div>
                            </div>
                            <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('registration_options') ?></div>
                                    <div class="d-flex align-items-center mb-3 mt-7" style="line-height: 1;">
                                        <i class="mie-5 fal fa-shekel-sign bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="d-flex flex-column">
                                            <div class="js-tab-preview bsapp-fs-18 mb-1">
                                                <?= lang('member_single_register') ?>
                                            </div>
                                            <div class="js-tab-sub-preview bsapp-fs-12 text-muted"><?= lang('cancel_option') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-6-registrationoption"  ></a>
                                </div>
                            </div>
                        </div>
                    <div class="tab-pane fade" id="js-class-tab-advanced" role="tabpanel" >
                            <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('cal_display_options') ?></div>
                                    <div class="d-flex align-items-center mb-3 mt-7" style="line-height: 1;">
                                        <i class="mie-5 fal fa-eye bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="d-flex flex-column">
                                            <div class="js-tab-preview bsapp-fs-18 mb-1">
                                                <?= lang('ext_register') ?>
                                            </div>
                                            <div class="js-tab-sub-preview bsapp-fs-12 text-muted"><?= lang('display_registered') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-5-displayoption"  ></a>
                                </div>
                            </div>

                            <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('class_waiting_list') ?></div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="mie-5 fal fa-pause-circle bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="bsapp-fs-18 js-tab-preview">
                                            <?= lang('waiting_list_reg_pref') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-21-waitinglist"  ></a>
                                </div>
                            </div>
                            <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('broadcast_options') ?></div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="mie-5 fal fa-video bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="bsapp-fs-18 js-tab-preview">
                                            <?= lang('broadcast_options') ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-22-broadcastoptions"  ></a>
                                </div>
                            </div>
                            <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('min_participants') ?></div>
                                    <div class="d-flex align-items-center mb-3 mt-7" style="line-height: 1;">
                                        <i class="mie-5 fal fa-calendar-day bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="d-flex flex-column">
                                            <div class="js-tab-preview bsapp-fs-18 mb-1">
                                                <?= lang('without') ?>
                                            </div>
                                            <div class="js-tab-sub-preview bsapp-fs-12 text-muted d-none"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-23-participantsclass"  ></a>
                                </div>
                            </div>
                            <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('class_register_block') ?></div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="mie-5 fal fa-hand-paper bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="bsapp-fs-18 js-tab-preview">

                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-24"></i>
                                    <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-24-registrationrestrictions"  ></a>
                                </div>
                            </div>
                            <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                                <div class="">
                                    <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('open_close_reg_time') ?></div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="mie-5 fal fa-hourglass-start bsapp-min-w-30p bsapp-fs-22" ></i>
                                        <div class="bsapp-fs-18 js-tab-preview">

                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <i class="fal fa-angle-right bsapp-fs-24"></i>
                                <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-items-tab-25-opencloseregistration"  ></a>
                            </div>
                        </div>
                        <div class="form-group my-15 px-15">
                            <label><?= lang('choose_class_devices') ?></label>
                            <div class="">
                                <select name="ClassDevice" class="js-select2" required>
                                    <option value="0"><?= lang('without_devices') ?></option>
                                    <?php foreach ($deviceTypes as $deviceType): ?>
                                        <option value="<?= $deviceType->id ?>"><?= $deviceType->Name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-15 px-15">
                            <a onclick="MeetingPopup.toggleTargetAnimation(this)" data-target="#js-div-class-content" class="text-dark" href="javascript:;">
                                <i class="fal fa-plus"></i> <?= lang('contet_single') ?>
                            </a>
                        </div>
                        <div id="js-div-class-content" class="collapse form-group mb-15 px-15">
                            <textarea class="custom-required form-control" id="RemarksNew" name="Remarks" placeholder="<?= lang('type_here_class_content') ?>" rows="5"></textarea>
                        </div>
                        <div class="form-group mb-15 px-15">
                            <div class="avatar-container d-flex flex-column">
                                <a class="text-dark" data-ip-modal="#itemModal"><i class="fal fa-plus"></i> <?= lang('add_image_membership') ?></a>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="" id="avatar" class="bsapp-max-h-125p align-self-start mt-5 rounded">
                                    <a id="js-delete-avatar" class="btn d-none" onclick="fieldEvents.removePicture(this)">
                                        <i class="fas fa-do-not-enter"></i>
                                    </a>
                                </div>
                            </div>
                            <input class="d-none custom-required" id="pageImgPath" name="image" value=""/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end border-top border-light px-15 py-15">
            <a href="javascript:;" data-dismiss="modal" class="btn btn-outline-secondary mie-12 px-30"><?= lang('action_cacnel') ?></a>
            <button type="submit" class="btn btn-primary px-30"><?= lang('save') ?></button>
        </div>
    </div>

        <!-- stuff -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-1-stuff">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitGuide(this)">
                <a href="javascript:;" class="text-dark mie-10">
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('choose_lesson_instructor') ?>
                </div>
                </div>
            </div>
                <div class="pos-abs-pad bsapp-scroll">
            <div class="h-100 ">
                    <div class="h-100 d-flex flex-column">
                        <ul class="nav nav-tabs border-0 pis-0 mb-5 bsapp-tab-active-underline" id="js-coach-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link  active" id="js-coach-tab-link-head" data-toggle="tab" href="#js-coach-tab-head" role="tab" aria-selected="true"><?= lang('instructor') ?></a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="js-coach-tab-link-assistant" data-toggle="tab" href="#js-coach-tab-assistant" role="tab"  aria-selected="false"><?= lang('instructor_help') ?></a>
                            </li>
                        </ul>
                        <div class="tab-content overflow-auto" id="js-tabs-coach">
                            <div class="tab-pane  show  active" id="js-coach-tab-head" role="tabpanel" >
                                <?php foreach ($coachers as $key => $coacher): ?>
                                    <div class="px-15 py-10 border-light border-bottom mb-10 d-flex justify-content-between position-relative align-items-center" onclick="fieldEvents.classActions.choseGuide(this)">
                                        <div class="d-flex align-items-center">
                                            <img src="
                                            <?php //display avatar.
                                                if (!is_null($coacher->UploadImage))
                                                    echo '/camera/uploads/large/' . $coacher->UploadImage;
                                                else
                                                    echo 'https://ui-avatars.com/api/?length=1&name=' . $coacher->FirstName . '&background=f3f3f4&color=000&font-size=0.5';
                                            ?>"
                                                 class="mie-8 w-40p h-40p rounded-circle">
                                            <div class="bsapp-fs-18 mis-3"><?= $coacher->display_name ?></div>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input
                                                <?php
                                                    if ($key == 0 || $coacher->id == $user->__get('id'))
                                                        echo 'checked';
                                                    ?>
                                                    data-preview="<?= $coacher->display_name ?>" type="radio"
                                                    value="<?= $coacher->id; ?>" id="js-coach-head-<?= $key; ?>"
                                                    onchange="console.log('test')"
                                                    name="GuideId" class="custom-control-input" required>
                                            <label class="custom-control-label" for="js-coach-head-<?= $key; ?>">

                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="tab-pane" id="js-coach-tab-assistant" role="tabpanel">
                                <div class="px-15 py-10 border-light border-bottom mb-10 d-flex justify-content-between position-relative align-items-center js-coach-container" onclick="MeetingPopup.checkChild(this)">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?length=1&name=<?= lang('without') ?>&background=f3f3f4&color=000&font-size=0.5" class="mie-8 w-40p h-40p rounded-circle">
                                    <div class="bsapp-fs-18 mis-3"><?= lang('without') ?></div>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input data-preview="<?= lang('without') ?>" type="radio"
                                           value="-1"  name="ExtraGuideId" class="custom-control-input">
                                    <label class="custom-control-label" >

                                    </label>
                                </div>
                            </div>
                            <?php foreach ($coachers as $key => $coacher): ?>
                                    <div class="px-15 py-10 border-light border-bottom mb-10 d-flex justify-content-between position-relative align-items-center js-coach-container"
                                         onclick="MeetingPopup.checkChild(this)">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php
                                            //display avatar.
                                            if (!is_null($coacher->UploadImage)) {
                                                echo '/camera/uploads/large/' . $coacher->UploadImage;
                                            } else {
                                                echo 'https://ui-avatars.com/api/?length=1&name=' . $coacher->FirstName . '&background=f3f3f4&color=000&font-size=0.5';
                                            }
                                            ?>" class="mie-8 w-40p h-40p rounded-circle">
                                            <div class="bsapp-fs-18 mis-3"><?= $coacher->display_name ?></div>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input data-preview="<?= $coacher->display_name ?>" type="radio"
                                                   value="<?= $coacher->id; ?>"  name="ExtraGuideId" class="custom-control-input">
                                            <label class="custom-control-label" >

                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitGuide(this)"><?= lang('confirm') ?></a>
            </div>
        </div>

        <!-- calendar -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-2-calendar">
        <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
            <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitCalendar(this)">
            <a href="javascript:;" class="text-dark mie-10" >
                <i class="fal fa-angle-left"></i>
            </a>
            <div>
                <?= lang('choose_calendar') ?>
            </div>
            </div>
        </div>
        <div class="bsapp-scroll overflow-auto">
            <div class="h-100 ">
                <div>
                    <input class="d-none" id="brands" name="Brands" val="0" required>
                    <?php foreach ($brands as $brandKey => $brand):?>
                        <div class="mb-10">
                        <div class="px-15 py-10 d-flex justify-content-between ">
                            <div class="d-flex ">
                                <div class="bsapp-fs-18 font-weight-bold"><?= $brand->BrandName; ?></div>
                            </div>
                        </div>
                        <?php if (!isset($brand->hasActiveSection)): ?>
                            <div class="px-15 py-10 border-light border-bottom d-flex justify-content-between position-relative">
                                <div class="bsapp-fs-18 font-weight-bold"><?= lang('no_cal_branch') ?></div>
                            </div>
                        <?php
                        else:
                            foreach($brand->Sections as $key => $section): ?>
                                <div onclick="MeetingPopup.checkChild(this)"
                                     class="<?= ($section->Status == 1) ? 'd-none' : 'd-flex' ?> px-15 py-10 border-light border-bottom justify-content-between position-relative">
                                    <div class="d-flex">
                                        <div class="bsapp-fs-18"><?= $section->Title ?></div>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input
                                                type="radio" value="<?= $section->id; ?>" id="js-radio-head-<?= $brandKey; ?><?= $key; ?>"
                                                data-preview="<?= $section->Title ?>" name="Floor" class="custom-control-input"
                                                data-brand="<?= $brand->id ?>" required>
                                        <label class="custom-control-label" for="js-radio-head-<?= $key; ?>">

                                            </label>
                                        </div>
                                    </div>
                        <?php
                                endforeach;
                            endif;?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitCalendar(this)"><?= lang('confirm') ?></a>
            </div>
        </div>
        <input name="StartDate" id="StartDate" value="<?= date('Y-m-d') ?>" class="d-none" required>
        <!-- date and shows -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-3-datenshows">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitDate(this)">
                <a href="javascript:;" class="text-dark mie-10" >
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('date_and_shows') ?>
                </div>
                </div>
            </div>
            <div class="pt-10 bsapp-scroll overflow-auto">
            <div class="h-100 ">
                <div>
                    <div class="form-group px-15 py-5 mb-0 js-group-edit-effect">
                        <div class="datepicker" onchange="fieldEvents.choseStartDate(this)" required>

                        </div>
                    </div>
                    <div class="js-single-edit-effect">
                        <div class="form-group px-15 py-10 mb-0">
                            <label class="" for="js-select2-class-frequency"><?= lang('frequency') ?></label>
                            <div class="w-100">
                                <select onchange="MeetingPopup.toggleRelatedSelect(this)" class="js-select2 form-control" name="ClassRepeat"
                                        id="js-select2-class-frequency" data-hide-val="0" data-post-change="0" data-target="#js-div-stopped" required>
                                    <option value="0"><?= lang('one_time_cal') ?> </option>
                                    <option value="1"><?= lang('every_week') ?></option>
                                    <option value="2"><?= lang('every')  ?> 2 <?= lang('weeks') ?></option>
                                    <option value="3"><?= lang('every')  ?> 3 <?= lang('weeks') ?></option>
                                    <option value="4"><?= lang('every') ?> 4 <?= lang('weeks') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-stopped" class="d-none form-group px-15 py-10 mb-0">
                            <label class="" for="js-select2-class-stopped"><?= lang('ends_at_new_class') ?></label>
                            <div class="w-100">
                                <select onchange="MeetingPopup.toggleRelatedSelect(this)" class="js-select2 form-control" id="js-select2-class-stopped"
                                        name="freqType" data-hide-val="0,2,3,4,5,6,7,8,9,10" data-post-change="0" data-target="#js-div-until-date">
                                    <option value="0"><?= lang('cal_never') ?> </option>
                                    <option value="date"><?= lang('select_date') ?></option>
                                    <?php
                                    if (!$classId || $duplicate):
                                    for ($i = 2; $i <= 10; $i++): ?>
                                        <option class="js-times-option" value="<?= $i; ?>">
                                            <?= lang('after').' '.$i.' '.lang('times') ?>
                                        </option>
                                    <?php
                                    endfor;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-until-date" class="d-none form-group px-15 py-10 mb-0">
                            <label class="" for="regularEndDate"><?= lang('desk_date_select') ?></label>
                            <div class="w-100">
                                <input id="regularEndDate" name="regularEndDate" class="form-control" type="date"
                                       min="<?= date("Y-m-d", strtotime('tomorrow')) ?>" max="<?= date('Y-m-d', strtotime('+2 years')) ?>">
                                <div class="invalid-feedback">
                                    <?= lang('enter_valid_future_lesson') ?> <?= date('d/m/Y', strtotime('+2 years')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-none justify-content-end border-top border-light px-15 py-15">
            <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitDate(this)"><?= lang('confirm') ?></a>
        </div>
    </div>

        <!-- timing -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-4-timing">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitTime(this)">
                <a href="javascript:;" class="text-dark mie-10" >
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('class_details_timing') ?>
                </div>
                </div>
            </div>
            <div class="pt-10 bsapp-scroll overflow-auto">
            <div class="h-100 ">
                    <div>
                        <div class="d-flex px-15 mb-15  border-bottom border-light">
                            <div class="form-group w-150p mie-15">
                                <label><?= lang('begin_time') ?></label>
                                <input name="StartTime" value="<?= $currentTime; ?>"
                                       type="time" step="300" class="form-control bg-light border-light" onchange="fieldEvents.checkStartTime(this)" required/>
                                <div class="invalid-feedback">
                                    <?= lang('multiple_of_5') ?>
                                </div>
                            </div>
                            <div class="form-group flex-fill ">
                                <label><?= lang('duration_folder_page') ?></label>
                                <div class="d-flex is-invalid-container">
                                    <select name="duration" class="js-select2" required>
                                        <?php for ($i = 5; $i <= 600; $i+=5):
                                            $interval = mktime(0, $i);
                                            if ($i / 60 < 1)
                                                $intervalText = intval(date('i', $interval)) . ' ' . lang('minutes');
                                            else if ($i % 60 == 0)
                                                $intervalText = date('G', $interval) . ' ' . lang('hours');
                                            else
                                                $intervalText = date('G', $interval) . ' ' . lang('hours_and') .  intval(date('i', $interval)) . ' ' . lang('minutes');
                                        ?>
                                        <option <?= $i == 60 ? "selected" : "" ?> data-text="<?= $intervalText ?>" value="<?= $i ?>">
                                            <?= $intervalText ?>
                                        </option>
                                        <?php
                                            if (120 <= $i && $i < 420)
                                                $i += 10;
                                            else if ($i >= 420)
                                                $i += 55;
                                            endfor;
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group px-15 mb-15">
                            <label><?= lang('alert_before_class') ?></label>
                            <div>
                                <select name="SendReminder" onchange="MeetingPopup.toggleRelatedSelect(this)" data-post-change="0" data-hide-val="1" data-target="#notify-time" class="js-select2" required>
                                    <option value="0"><?= lang('yes') ?></option>
                                    <option value="1"><?= lang('no') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="notify-time" class="form-group px-15 mb-15 pb-15 border-bottom border-light ">
                            <label><?= lang('timing') ?></label>
                            <div class="d-flex">
                                <div class="w-150p mie-15">
                                <input value="<?php if (!$classId) echo date('H:i',strtotime('-3 hours', strtotime($currentTime))) ?>" name="TimeReminder"
                                       onchange="fieldEvents.checkReminderTime(this)" id="TimeReminder"
                                       class="form-control bg-light border-light " required type="time"/>
                                <div class="invalid-feedback">
                                    <?= lang('reminder_time_before_class_time') ?>
                                </div>
                            </div>
                                <div class="flex-fill">
                                    <select name="TypeReminder" required class="js-select2" onchange="fieldEvents.checkReminderTime($('#TimeReminder'))">
                                        <option value="1" data-text="<?= lang('in_lesson_day') ?>">
                                            <?= lang('in_lesson_day') ?>
                                        </option>
                                        <option value="2" data-text="<?= lang('day_before_lesson_day') ?>">
                                            <?= lang('day_before_lesson_day') ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitTime(this)"><?= lang('confirm') ?></a>
            </div>
        </div>

        <!-- display option -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-5-displayoption">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitExtRegister(this)">
                <a href="javascript:;" class="text-dark mie-10" >
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('class_display') ?>
                </div>
                </div>
            </div>
            <div class="pt-10 bsapp-scroll overflow-auto">
            <div class="h-100 ">
                    <div>
                        <div class="form-group px-15 mb-15">
                            <label><?= lang('ext_register') ?></label>
                            <div>
                                <select name="purchaseLocation" onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-sub-class" data-hide-val="0" data-post-change="1" class="js-select2" required>
                                    <option value="1"><?= lang('yes_from_app') ?></option>
                                    <option value="0"><?= lang('no') ?></option>
                                    <option value="3" ><?= lang('yes_everywhere') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-sub-class" class="form-group px-15 mb-15">
                            <label><?= lang('show_register_count') ?></label>
                            <div>
                                <select name="ShowClientNum" onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-show-names" data-hide-val="1" data-post-change="1" class="js-select2" required>
                                    <option value="1"><?= lang('no') ?></option>
                                    <option value="0"><?= lang('yes') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-show-names" class="form-group px-15 mb-15 d-none">
                            <label><?= lang('show_part_names') ?></label>
                            <div>
                                <select name="ShowClientName" class="js-select2">
                                    <option value="1"><?= lang('no') ?></option>
                                    <option value="0"><?= lang('yes') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitExtRegister(this)"><?= lang('confirm') ?></a>
            </div>
        </div>

        <!-- registration option -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-6-registrationoption">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-lightbsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitRegOption(this)">
                <a href="javascript:;" class="text-dark  mie-10" >
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('registration_options') ?>
                </div>
                </div>
            </div>
            <div class="pt-10 bsapp-scroll overflow-auto">
            <div class="h-100 ">
                    <div>
                        <div class="form-group px-15 mb-15">
                            <label><?= lang('registration_options_lesson') ?></label>
                            <div>
                                <select name="purchaseOptions" id="register-option" data-hide-val="membership,free-register" data-post-change="0" class="js-select2" required>
                                    <option value="membership-cost" disabled="disabled"><?= lang('membership_or_one_time') ?></option>
                                    <option value="membership"><?= lang('membership_only') ?></option>
                                    <option value="free-register" disabled="disabled"><?= lang('online_no_limit') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-free-register">
                            <div id="js-div-cost" class="form-group px-15 mb-15 pb-15 border-bottom border-light d-none">
                                <label><?= lang('register_cost_no_membership') ?></label>
                                <input name="purchaseAmount" value="35" type="number" min="0" step=".01" class="form-control bg-light border-light " required/>
                            <div class="text-center text-gray-400 mt-5">
                                <div class="border-radius-3r p-8 bsapp-fs-14 ">
                                    * <?= lang('soon_single_payment_notice') ?> &#9996;
                                </div>
                            </div>
                        </div>

                            <div class="form-group px-15 mb-15">
                                <label><?= lang('free_cancel_fieild') ?></label>
                                <div>
                                    <select name="CancelLaw"
                                            onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-time-before"
                                            data-hide-val="4,5" data-post-change="hour" class="js-select2 js-cancel-law-field" required>
                                        <option value="3"><?= lang('choose_time_before_class') ?></option>
                                        <option value="4"><?= lang('without_cancel_option') ?></option>
                                        <option value="5"><?= lang('free_cancel') ?></option>
                                    </select>
                                </div>
                            </div>

                            <div id="js-div-time-before" class="form-group px-15 mb-15 pb-15 border-bottom border-light ">
                                <label><?= lang('timing') ?></label>
                                <div class="d-flex">
                                    <input name="CancelPeriodAmount" class="form-control bg-light border-light  w-100p mie-15 js-cancel-law-field" value="3"
                                        type="number" required/>
                                    <div class="flex-fill">
                                        <select name="CancelPeriodType" class="js-select2 js-cancel-law-field" required>
                                            <option value="hour"><?= lang('hours_before_class') ?></option>
                                            <option value="day"><?= lang('days_before_class') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="js-div-class-stop-cancel" class="form-group px-15 mb-15">
                            <div>
                                <label><?php echo preg_replace('/[?]/', "", lang('class_details_cancel_button')); ?></label>
                                <select name="StopCancel" class="js-select2" onchange="MeetingPopup.toggleStopCancel(this)" required>
                                    <option value="0"><?= lang('yes'); ?></option>
                                    <option value="1" selected><?= lang('no'); ?></option>
                                </select>
                            </div>  
                            <div id="js-div-stop-cancel-time-before" class="form-group py-15 border-bottom border-light d-none">
                                <label><?= lang('timing') ?></label>
                                <div class="d-flex">
                                    <input id="StopCancelTime" name="StopCancelTime" class="form-control bg-light border-light w-100p mie-15"
                                     value="10" type="number" required/>
                                    <div class="flex-fill">
                                        <select name="StopCancelType" class="js-select2" required>
                                            <option value="1" selected><?= lang('min_before_class'); ?></option>
                                            <option value="2"><?= lang('hours_before_class'); ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitRegOption(this)"><?= lang('confirm') ?></a>
            </div>
        </div>

        <!-- waiting list -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-21-waitinglist">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitWaitingList(this)">
                <a href="javascript:;" class="text-dark mie-10">
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('class_waiting_list') ?>
                </div>
                </div>
            </div>
            <div class="pt-10 bsapp-scroll overflow-auto">
            <div class="h-100 ">
                    <div>
                        <div class="form-group px-15 pb-15 border-bottom border-light">
                            <label><?= lang('allow_waiting_list') ?></label>
                            <div class="">
                                <select name="ClassWating" onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-no-waiting" data-hide-val="1" data-post-change="1" class="js-select2" required>
                                    <option value="0"><?= lang('yes') ?></option>
                                    <option value="1"><?= lang('no') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-no-waiting">
                            <div class="pb-15 border-bottom border-light">
                                <div class="form-group px-15 mb-15">
                                    <label><?= lang('limit_clients_waiting') ?></label>
                                    <div class="">
                                        <select name="MaxWatingList" onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-limit-waiting" data-hide-val="1" data-post-change="1" class="js-select2" required>
                                            <option value="1"><?= lang('no') ?></option>
                                            <option value="0"><?= lang('yes') ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div id="js-div-limit-waiting" class="form-group px-15 mb-15 d-none">
                                    <div class="d-flex">
                                        <div class="d-flex flex-column mie-15">
                                        <input name="NumMaxWatingList" type="number" min="0" max="999" value="3" class="form-control bg-light border-light w-150p" />
                                        <div class="invalid-feedback">
                                            <?= lang('error_range_1_999') ?>
                                        </div>
                                    </div>
                                        <input class="form-control bg-white" value="<?= lang('waiting') ?>" readonly />
                                    </div>

                                </div>
                            </div>
                            <div class="form-group px-15 mb-15">
                                <label><?= lang('show_waiting_pos') ?></label>
                                <div class="">
                                    <select name="WatingListOrederShow" class="js-select2" required>
                                        <option value="0"><?= lang('yes') ?></option>
                                        <option value="1"><?= lang('no') ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitWaitingList(this)"><?= lang('confirm') ?></a>
            </div>
        </div>

        <!-- broadcast option -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-22-broadcastoptions">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitBroadcastOptions(this)">
                    <a href="javascript:;" class="text-dark mie-10" >
                        <i class="fal fa-angle-left"></i>
                    </a>
                    <div>
                        <?= lang('broadcast_options') ?>
                    </div>
            </div>
        </div>
            <div class="pt-10 bsapp-scroll overflow-auto">
            <div class="h-100 ">
                    <div>
                        <div class="form-group px-15 mb-15">
                            <label><?= lang('broadcast_options') ?></label>
                            <div class="">
                                <select name="LiveClass" data-hide-val="" id="broadcast-option" class="js-select2">
                                    <option value="without"><?= lang('without') ?></option>
                                    <option value="zoom"><?= lang('zoom_class') ?></option>
                                    <option value="online"><?= lang('online_class') ?></option>
                                </select>
                            </div>
                        </div>

                        <div id="js-div-zoom" class="js-broadcast d-none">
                            <div class="form-group px-15 mb-15">
                                <label><?= lang('meeting_number') ?></label>
                                <input name="meetingNumber" class="form-control bg-light border-light" value=""    />
                            </div>
                            <div class="form-group px-15 mb-15">
                                <label><?= lang('password') ?></label>
                                <input name="ZoomPassword" class="form-control bg-light border-light" value=""    />
                            </div>
                        </div>

                        <div id="js-div-online-class" class="js-broadcast d-none">
                            <div class="form-group px-15 mb-15">
                                <label><?= lang('meeting_link') ?></label>
                                <input name="liveClassLink" class="form-control bg-light border-light" value=""    />
                            </div>
                            <div class="form-group px-15 mb-15 pb-15 border-bottom border-light ">
                                <label><?= lang('sched_link_submit') ?></label>
                                <div class="d-flex">
                                    <input name="onlineSendTime" class="form-control bg-light border-light  w-100p mie-15 " value="3" />
                                    <div class="flex-fill">
                                        <select name="onlineSendTimeType" class="js-select2">
                                            <option value="1"><?= lang('min_before_class') ?></option>
                                            <option value="2" selected><?= lang('hours_before_class') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group px-15 mb-15">
                                <label><?= lang('send_link_through') ?></label>
                                <div class="">
                                    <select name="onlineSendType" class="js-select2">
                                        <option value="2"><?= lang('mail_short_link') ?></option>
                                        <option value="1">SMS</option>
<!--                                        <option value="3">--><?//= lang('mail_sms') ?><!--</option>-->
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitBroadcastOptions(this)"><?= lang('confirm') ?></a>
            </div>
        </div>

        <!-- participants class -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-23-participantsclass">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitClassMin(this)">
                <a href="javascript:;" class="text-dark mie-10" >
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('class_min_participants') ?>
                </div>
                </div>
            </div>
            <div class="pt-10 bsapp-scroll overflow-auto">
            <div class="h-100 ">
                    <div>
                        <div class="form-group px-15 mb-15">
                            <label><?= lang('set_minimum_class') ?></label>
                            <div class="">
                                <select name="MinClass" onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-minimum"
                                        data-hide-val="0" data-post-change="1" class="js-select2" required>
                                    <option value="0"><?= lang('without') ?></option>
                                    <option value="1"><?= lang('yes') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-minimum" class="d-none">
                            <div class="form-group px-15 mb-15">
                                <label><?= lang('class_min_participants') ?></label>
                                <div class="">
                                    <select name="MinClassNum" class="js-select2">
                                        <?php for ($i = 1; $i <= 50; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group px-15 mb-15 pb-15 border-bottom border-light ">
                                <label><?= lang('check_min_participants') ?></label>
                                <div class="d-flex">
                                    <input name="ClassTimeCheck" class="form-control bg-light border-light  w-100p mie-15 " value="60" />
                                    <div class="flex-fill">
                                        <select name="ClassTimeTypeCheck" class="js-select2">
                                            <option value="1"><?= lang('min_before_class') ?></option>
                                            <option value="2"><?= lang('hours_before_class') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitClassMin(this)"><?= lang('confirm') ?></a>
            </div>
        </div>

        <!-- registration block -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-24-registrationrestrictions">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitRegisterRist(this)">
                <a href="javascript:;" class="text-dark mie-10" >
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('class_restrictions') ?>
                </div>
                </div>
            </div>
            <div class="pt-10 bsapp-scroll overflow-auto">
            <div class="h-100 ">
                    <div>
                        <div class="form-group px-15 mb-15">
                            <label><?= lang('restriction_by_age_js') ?></label>
                            <div class="">
                                <select name="GenderLimit" class="js-select2" required>
                                    <option value="0"><?= lang('everyone')?></option>
                                    <option value="1"><?= lang('male') ?></option>
                                    <option value="2"><?= lang('female') ?></option>
                                </select>
                            </div>
                        </div>
                        <?php if ($class_data['productSettings']->manageMemberships == 1): ?>
                        <div class="d-flex flex-column px-15 mb-15">
                            <label><?= lang('restrict_by_subscription_type_js') ?></label>
                            <div class="">
                                <select name="ClassMemberType[]" multiple="multiple" class="js-select2-multi" required>
                                    <option class="js-default-opt" selected value="BA999"><?= lang('all_membership_types') ?></option>
                                    <?php foreach ($membershipTypes as $membershipType): ?>
                                    <option value="<?= $membershipType->id ?>"><?= $membershipType->Type ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="form-group px-15 mb-15">
                            <label><?= lang('restrict_by_rank_js') ?></label>
                            <div class="">
                                <select name="LimitLevel[]" multiple="multiple" class="js-select2-multi" required>
                                    <option class="js-default-opt" onselect="console.log(this)" selected value="0"><?= lang('all_ranks')?> </option>
                                    <?php foreach ($clientLevels as $level): ?>
                                        <option value="<?= $level->id ?>">
                                            <?= $level->Level ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group px-15 mb-15 d-none">
                            <label><?= lang('restrict_age_range_js') ?></label>
                            <div class="">
                                <!-- onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-age"
                            TODO: To enable age ristriction add the line above to the select under that comment in addition to uncommeting the lines on `createClass.js`, method 'submitRegisterRist'-->
                            <select
                                        data-hide-val="0" data-post-change="0" class="js-select2" name="ageRistriction" required>
                                    <option value="0"><?= lang('no') ?></option>
                                    <option value="1"><?= lang('yes') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-age" class="d-none form-group px-15 mb-15 pb-15 border-bottom border-light ">
                            <label><?= lang('age') ?></label>
                            <div class="d-flex flex-wrap bsapp-min-h-125p" style="gap: 10px;">
                                <div class="flex-fill mie-15">
                                    <select onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-age-between"
                                            data-hide-val="0,1,2" data-post-change="0" class="js-select2" name="ageLimitType">
                                        <option value="1"><?= lang('older_than') ?></option>
                                        <option value="2"><?= lang('younger_than') ?></option>
                                        <option value="3"><?= lang('age_range') ?></option>
                                    </select>
                                </div>
                                <input name="ageLimitNum1" class="form-control bg-light border-light w-150p mie-15" type="number" min="0" />
                                <div id="js-div-age-between" class="d-none">
                                    <div class="d-flex">
                                        <span class="mt-8"><?= lang('coupon_till') ?></span>
                                        <div>
                                            <input name="ageLimitNum2" class="form-control bg-light border-light w-150p mis-15" type="number" min="0" />
                                            <div class="invalid-feedback">
                                                <?= lang('min_max_age') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitRegisterRist(this)"><?= lang('confirm') ?></a>
            </div>
        </div>

        <!-- open close registrations -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-items-tab-25-opencloseregistration">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.classActions.submitOpenClose(this)">
                <a href="javascript:;" class="text-dark mie-10" >
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('class_register_block') ?>
                </div>
                </div>
            </div>
            <div class="pt-10 bsapp-scroll overflow-auto">
            <div class="h-100 ">
                    <div>
                        <div class="form-group px-15 mb-15">
                            <label><?= lang('limit_class_register') ?> </label>
                            <div class="">
                                <select name="OpenOrder" onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-allow-time"
                                        data-hide-val="1" data-post-change="1" class="js-select2" required>
                                    <option value="1"><?= lang('no') ?></option>
                                    <option value="0"><?= lang('yes_set_time') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-allow-time" class="d-none form-group px-15 mb-15 pb-15 border-bottom border-light ">
                            <label><?= lang('open_class_register') ?></label>
                            <div class="d-flex">
                                <input name="OpenOrderTime" type="number" min="0" class="form-control bg-light border-light  w-100p mie-15 " value="3"/>
                                <div class="flex-fill">
                                    <select name="OpenOrderType" class="js-select2">
                                        <option value="1"><?= lang('min_before_class') ?></option>
                                        <option value="2" selected><?= lang('hours_before_class') ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group px-15 mb-15">
                            <label><?= lang('block_register') ?></label>
                            <div class="">
                                <select name="CloseOrder" onchange="MeetingPopup.toggleRelatedSelect(this)" data-target="#js-div-block-time"
                                        data-hide-val="1" data-post-change="1" class="js-select2" required>
                                    <option value="1"><?= lang('no') ?></option>
                                    <option value="0"><?= lang('yes_set_time') ?></option>
                                </select>
                            </div>
                        </div>
                        <div id="js-div-block-time" class="d-none form-group px-15 mb-15 ">
                            <label><?= lang('sched_lesson_reg_block') ?></label>
                            <div class="d-flex">
                                <input name="CloseOrderTime" type="number" min="0" class="form-control bg-light border-light  w-100p mie-15 " value="3" />
                                <div class="flex-fill">
                                    <select name="CloseOrderType" class="js-select2">
                                        <option value="1"><?= lang('min_before_class') ?></option>
                                        <option value="2" selected><?= lang('hours_before_class') ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="d-none justify-content-end border-top border-light px-15 py-15">
                <a href="javascript:;" class="btn btn-primary btn-block px-30" onclick="fieldEvents.classActions.submitOpenClose(this)"><?= lang('confirm') ?></a>
            </div>
        </div>
    </form>

    <form id="new-meeting" class="d-none h-100" onsubmit="fieldEvents.meetingActions.submit(this, event)" novalidate>
        <div class="js-subpage-home h-100 js-subpage-meeting-new">
            <div class="d-flex justify-content-between align-items-center  border-bottom border-light">
                <div class="bsapp-fs-18 w-150p px-15 py-15">
                    <select class="js-select2-dropdown-arrow js-select-create-new" >
                        <option value="new-class"><?= lang('class_creation') ?></option>
                        <option value="new-meeting"><?= lang('meeting_creation') ?></option>
                    </select>
                </div>
                <a href="javascript:;" class="text-dark bsapp-fs-18 p-15" data-dismiss="modal">
                <i class="fal fa-times"></i>
            </a>
            </div>
            <div class="bsapp-scroll">
                <div class="h-100 d-flex flex-column">
                    <!-- Meeting :: Choose client preview -->
                    <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-top border-light">
                            <div class="cursor-pointer w-100" onclick="MeetingPopup.showSlide(this, MeetingPopup.userSearch.triggerFocus())" data-id="js-meeting-tab-client">
                                <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('client_details_class') ?></div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-user-circle bsapp-min-w-30p bsapp-fs-22"></i>
                                    <div class="js-tab-preview js-guide-name bsapp-fs-18 ">
                                        <?= lang('occasional_customer') ?>
                                        <input type="hidden" class="d-none js-guide-name">
                                    </div>
                                </div>
                            </div>
                            <div id="js-client-tab-link">
                                <i class="fal fa-angle-right bsapp-fs-24"></i>
                                <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this, MeetingPopup.userSearch.triggerFocus())" href="javascript:;" data-id="js-meeting-tab-client"></a>
                            </div>
                            <div class="js-show-slide cursor-pointer d-none" id="js-client-tab-reset"  onclick="fieldEvents.meetingActions.resetClient()" href="javascript:;" data-id="js-meeting-tab-client">
                                <span class="bsapp-fs-18" style="color: red;"><?= lang('a_remove_single') ?></span>
                            </div>
                    </div>
                    <!-- Meeting :: Choose therapist preview -->
                    <div class="<?= (count($coachers) > 1) ? 'd-flex' : 'd-none' ?> js-div-tab-preview position-relative
                    w-100 justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-top border-light">
                            <div class="">
                                <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('therapist_single') ?></div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="fas fa-user-circle bsapp-min-w-30p bsapp-fs-22"></i>
                                    <div class="js-tab-preview js-guide-name bsapp-fs-18 ">
                                        <?= lang('choose_therapist') ?>
                                        <input type="hidden" class="d-none js-guide-name">
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <i class="fal fa-angle-right bsapp-fs-24"></i>
                                <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-meeting-tab-therapist"></a>
                            </div>
                    </div>
                    <!-- Meeting :: Choose date preview -->
                    <div class="js-div-tab-preview position-relative w-100  d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                            <div class="">
                                <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('due') ?></div>
                                <div class="d-flex align-items-center mb-3 mt-7" style="line-height: 1;">
                                    <i class="mie-5 fal fa-calendar-day bsapp-min-w-30p bsapp-fs-22" ></i>
                                    <div class="d-flex flex-column">
                                        <div class="js-tab-preview bsapp-fs-18 ">
                                            <?= lang('select_date') ?>
                                        </div>
                                        <div class="js-tab-sub-preview bsapp-fs-12 text-muted"><?= lang('choose_freq') ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <i class="fal fa-angle-right bsapp-fs-24"></i>
                                <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-meeting-tab-date"  ></a>
                            </div>
                    </div>
                    <!-- Meeting :: Choose calendar preview -->
                    <div class="<?= ($class_data['calendarCount'] > 1) ? 'd-flex' : 'd-none' ?> js-div-tab-preview
                    position-relative w-100 justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light">
                            <div class="">
                                <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('calendar_new_class') ?></div>
                                <div class="d-flex align-items-center mb-3">
                                    <i class="mie-5 fal fa-calendar bsapp-min-w-30p bsapp-fs-22" ></i>
                                    <div class="js-tab-preview bsapp-fs-18 ">
                                        <?= lang('choose_calendar') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <i class="fal fa-angle-right bsapp-fs-24"></i>
                                <a class="stretched-link js-show-slide"  onclick="MeetingPopup.showSlide(this)" href="javascript:;" data-id="js-meeting-tab-calendar"  ></a>
                            </div>
                    </div>
                    <div class="px-15 pt-7 bsapp-fs-14 font-weight-medium"><?= lang('meetings') ?></div>
                    <div class="overflow-auto">
                        <div class="position-relative w-100  d-flex justify-content-between align-items-center px-15 pb-5 border-light mt-5">
                        <div class="w-100">
                            <div class="">
                                <div id="js-all-treatment-container">
                                    <div class="js-treat-include-num d-flex mt-7 mb-15">
                                        <div class="js-treat-num bsapp-fs-16 font-weight-bold d-flex align-items-start mt-12 w-30p">1</div>
                                        <div class="w-100">
                                            <div class="js-treatment-container">
                                                <div class="js-treatment-preview d-none align-items-center">
                                                    <div class="input-group">
                                                        <input class="form-control bg-light border-light" readonly aria-label="Username" aria-describedby="basic-addon1">
                                                        <div class="input-group-prepend cursor-pointer" onclick="fieldEvents.meetingActions.showTreatment(this)">
                                                            <span class="input-group-text input-group-text bg-light border-light" id="basic-addon1">
                                                                <i class="fas fa-caret-down"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <a class="cursor-pointer mis-8" onclick="fieldEvents.meetingActions.removeTreatment(this)">
                                                        <i class="fal fa-trash-alt"></i>
                                                    </a>
                                                </div>
                                                <div class="js-treatment-item collapse show">
                                                    <div class="bsapp-fs-16 d-flex align-items-start justify-content-between">
                                                        <div class="bsapp-fs-16 d-flex flex-column justify-content-be w-100">
                                                            <div class="mb-5 d-flex align-items-center justify-content-between">
                                                                <div class="form-group mb-0 w-100">
                                                                    <div class="is-invalid-container">
                                                                        <select class="js-select2-templates js-template-select" name="classTypeId">
                                                                            <option></option>
                                                                            <?php foreach ($templateArray as $key => $template): ?>
                                                                                <?php if (!empty($template['volumes'])) : ?>
                                                                                    <?php foreach ($template['volumes'] as $volume): ?>
                                                                                        <option value="<?= $volume['id'] ?>"
                                                                                                data-cost="<?= $volume['price'] ?>"
                                                                                                data-minutes="<?= $volume['duration'] ?>"
                                                                                                data-label="<?= $template['TemplateName'] ?>"
                                                                                                data-template-id="<?= $template['id'] ?>"
                                                                                                data-calendars="<?= isset($template['calendarIds']) ? implode(',', $template['calendarIds']) : '' ?>"
                                                                                                data-coaches="<?= isset($template['coachIds']) ? implode(',', $template['coachIds']) : '' ?>"
                                                                                                data-prep-type="<?= $template['PreparationTimeStatus'] ?>"
                                                                                                data-prep-time-val="<?= $template['PreparationTimeValue'] ?>"
                                                                                                data-prep-time-unit="<?= $template['PreparationTimeType'] ?>"
                                                                                                data-gender="<?= $template['RegistrationLimitedTo'] ?>">
                                                                                            <?= $template['TemplateName'] ?> - <?= $volume['toString'] ?>
                                                                                        </option>
                                                                                    <?php endforeach; ?>
                                                                                <?php endif; ?>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                        <div class="invalid-feedback">
                                                                            Error placeholder (check translation)
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="d-none justify-content-between js-template-chosen">
                                                                <div class="d-flex flex-column w-25">
                                                                    <span class="bsapp-fs-14 mb-2"><?= lang('hour') ?></span>
                                                                    <div class="form-group mb-0">
                                                                    <select class="js-select2-schedule js-schedule-select bg-light"
                                                                            name="StartTime"
                                                                            onchange="fieldEvents.meetingActions.checkAllScheduleSelect()">
                                                                        <?php
                                                                        $currentHour = date('H');
                                                                        $currentMinutes = date('i') + (30 - date('i') % 30);
                                                                        if ($currentMinutes == 60){
                                                                            $currentMinutes = 0;
                                                                            $currentHour++;
                                                                        }
                                                                        for ($i = 0; $i <= 23; $i++) {
                                                                            for ($j = 0; $j <= 55; $j += 5) {
                                                                                $selected = ($i == $currentHour) && ($currentMinutes == $j) ? 'selected' : '';
                                                                                $time = date('H:i', strtotime("$i:$j"));
                                                                                echo "<option $selected value='$time'>$time</option>";
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <div class="invalid-feedback">
                                                                        Error placeholder (check translation)
                                                                    </div>
                                                                </div>
                                                                </div>
                                                                <div class="d-flex flex-column w-50 mx-10">
                                                                    <span class="bsapp-fs-14 mb-2"><?= lang('duration') ?></span>
                                                                    <select class="js-select2 js-template-duration" name="duration" onchange="fieldEvents.meetingActions.templateUpdate()">
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
                                                                            if (120 <= $i && $i < 420)
                                                                                $i += 10;
                                                                            else if ($i >= 420)
                                                                                $i += 55;
                                                                        endfor;
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                                <div class="d-flex flex-column w-25">
                                                                    <span class="bsapp-fs-14 mb-2"><?= lang('price') ?></span>
                                                                    <div dir="ltr" class="input-group is-invalid-container">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text px-0 pis-5 border-light bg-light input-group-text">₪</span>
                                                                    </div>
                                                                    <input class="form-control bg-light border-light shadow-none js-template-cost px-3 text-center"
                                                                           type="number"
                                                                           onchange="fieldEvents.meetingActions.templateUpdate()"
                                                                           name="cost"
                                                                    >
                                                                </div>
                                                                </div>
                                                            </div>
                                                            <div class="js-charge-type-div d-none flex-column">
                                                                <div class="my-7 font-weight-medium bsapp-fs-14"><?= lang('registeration_type') ?></div>
                                                                <div class="custom-control custom-radio custom-control-inline">
                                                                    <input name="ChargeType" type="radio" class="custom-control-input js-from-membership-input" id="js-radio-member-1" value="">
                                                                    <label class="custom-control-label" for="js-radio-member-1">
                                                                        <?= lang('from_customer_membership') ?>
                                                                    </label>
                                                                </div>
                                                                <div class="custom-control custom-radio custom-control-inline">
                                                                    <input name="ChargeType" type="radio" class="custom-control-input" id="js-radio-member-2" value="0" checked>
                                                                    <label class="custom-control-label" for="js-radio-member-2">
                                                                        <?= lang('attach_as_debt') ?>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <a class="cursor-pointer mis-8 mt-12 invisible" onclick="fieldEvents.meetingActions.removeTreatment(this)">
                                                            <i class="fal fa-trash-alt"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="js-treat-include-num d-none align-content-center mb-15 border-top">
                                        <div class="js-treat-num bsapp-fs-16 font-weight-bold d-flex align-items-start mt-12 w-30p">2</div>
                                        <div class="w-100 my-auto">
                                            <div class="js-add-treat-btn bsapp-fs-16 d-flex flex-column justify-content-start">
                                                <a class="bsapp-fs-14 font-weight-bold cursor-pointer mt-12" onclick="fieldEvents.meetingActions.addTreatment(this)"><?= lang('treatment') ?> +</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="js-note-container" class="mt-20 mb-10">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-dark bsapp-fs-14 d-flex align-items-center">
                                            <i class="fal fa-plus mie-5 bsapp-fs-12" id="js-note-toggle-symbol"></i>
                                            <a onclick="MeetingPopup.toggleTarget(this)"
                                               data-target="form#new-meeting #js-note-textarea"
                                               href="javascript:;" class="text-dark js-trigger-note-container">
                                                <?= lang('note_for_client') ?>
                                            </a>
                                        </div>
                                        <div class="invisible" id="js-note-clear-container">
                                            <a onclick="fieldEvents.clearRemarks(this)" class="cursor-pointer">
                                                <i class="fal fa-times"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div id="js-note-textarea" class="d-none">
                                        <div>
                                            <textarea onchange="MeetingPopup.checkRemarks(this)" onkeyup="MeetingPopup.checkRemarks(this)" name="Remarks"
                                                      maxlength="256"
                                                      class="custom-required form-control h-60p" style="line-height: 1.2;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <div id="js-div-tab-conclusion"
                         class="position-relative w-100 d-none justify-content-start align-items-center px-15 pt-7 pb-5 mt-auto border-top border-light">
                        <div class="">
                            <div class="mb-7 font-weight-medium bsapp-fs-14"><?= lang('total') ?></div>
                            <div class="d-flex align-items-center mb-3 mt-7 bsapp-fs-21">
                                <div class="mie-15" id="js-meeting-length">

                                </div>
                                <div class="font-weight-medium">
                                    <span id="js-meeting-cost"></span>
                                    ₪
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end border-top border-light px-15 py-15">




                <a id="js-proceed-to-payment" onclick="fieldEvents.meetingActions.submit($(this).closest('form'), null, null, true)" href="javascript:;" class="btn btn-outline-secondary mie-12 px-30"><?= lang('save_nad_move_to_payment') ?></a>
                <button type="submit" class="btn btn-primary px-30"><?= lang('save') ?></button>
            </div>
        </div>

        <!-- Meeting :: Choose client tab -->
        <div class="js-subpage-tabs h-100 d-none" data-href="js-meeting-tab-client">
<!--            <div class="h-100 d-flex flex-column">-->
                <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light">
                    <div class="d-flex cursor-pointer" onclick="fieldEvents.meetingActions.submitClient(this)">
                        <a href="javascript:;" class="position-relative text-dark bsapp-fs-18 mie-10">
                            <i class="fal fa-angle-left"></i>
                        </a>
                        <span><?= lang('choose_client') ?></span>
                    </div>
                </div>
                <div class="bsapp-scroll overflow-auto h-100 px-15 py-10">
                    <div class="h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="form-group d-flex align-items-center mb-15 custom-select-tags">
                                <label class="fal fa-search mie-10 my-auto">
                                </label>
                                <div class="flex-fill">
                                    <div class="userselectContainers">
                                        <div class="userField">
                                            <input type="hidden" id="userTypeNew" value="0">
                                            <!-- <div class="icon-container bsapp-z-1">
                                                <span class="newLabel">חדש</span>

                                            </div> -->
                                            <select class="js-user-search w-100" id="js-user-search-meeting"
                                            name="user">
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
                                            <input class="d-none" name="IsNew">
                                            <input class="d-none" name="ClientId">
                                            <input type="text" class="form-control bg-light border-light pie-100"
                                                   placeholder="<?= lang('client_name') ?>" name="ClientName"  id="js-user-name"/>
                                            <div class="position-absolute d-flex bsapp-position-end-12">
                                                <div>
                                                    <a class="text-secondary" onclick="MeetingPopup.userSearch.showSearchField(this)"><i class="fal fa-times"></i></a>
                                                </div>
                                                <div class="js-client-is-new mis-8">
                                                    <div class="badge badge-info badge-pill"><?= lang('new'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group d-none  flex-column mb-15 ">
                                    <div class="d-flex mb-2">
                                        <label class="fal fa-phone mie-10 mt-18">
                                        </label>
                                        <div class="d-flex flex-column align-items-center flex-fill position-relative justify-content-between">
                                            <input type="text" class="form-control bg-light border-light pie-40" placeholder="<?= lang('settings_phone') ?>"
                                                   name="UserPhone" id="js-user-phone" />
                                            <div class="invalid-feedback">
                                                <?= lang('phone_format_incorrect_ajax') ?>
                                            </div>
                                            <div class="position-absolute d-flex bsapp-position-end-12">
                                                <div class="js-client-phone-valid">
                                                    <i class="fal fa-check  text-info"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pis-25">
                                        <a class="text-info bsapp-fs-14 cursor-pointer " onclick="MeetingPopup.userSearch.showSearchField(this)"><i class="fal fa-long-arrow-right mie-5"></i><?= lang('search_client'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="js-new-client-button" class="px-10 pt-10 w-100 position-relative d-none">
                            <a class="btn btn-primary text-white px-30 w-100" onclick="fieldEvents.meetingActions.submitClient(this)">
                                <?= lang('save_new_client'); ?>
                            </a>
                        </div>
                    </div>
                </div>

        </div>

        <!-- Meeting :: Choose therapist tab -->
        <div class="js-subpage-tabs h-100 d-none" data-href="js-meeting-tab-therapist">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.meetingActions.submitDate(this)">
                    <a class="text-dark bsapp-fs-18 mie-10"  href="javascript:;">
                        <i class="fal fa-angle-left"></i>
                    </a>
                    <div>
                        <?= lang('choose_therapist') ?>
                    </div>
                </div>
            </div>
            <div class="bsapp-scroll overflow-auto h-100">
                <?php foreach ($coachers as $key => $coacher): ?>
                    <div class="js-therapist-option px-15 py-10 border-light border-bottom mb-10 d-flex justify-content-between position-relative align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="
                            <?php //display avatar.
                            if (!is_null($coacher->UploadImage))
                                echo '/camera/uploads/large/' . $coacher->UploadImage;
                            else
                                echo 'https://ui-avatars.com/api/?length=1&name=' . $coacher->FirstName . '&background=f3f3f4&color=000&font-size=0.5';
                            ?>"
                                 class="mie-8 w-40p h-40p rounded-circle">
                            <div class="bsapp-fs-18 mis-3"><?= $coacher->display_name ?></div>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input
                                <?php
                                if ($key == 0 || $coacher->id == $user->__get('id'))
                                    echo 'checked';
                                ?>
                                    data-preview="<?= $coacher->display_name ?>" type="radio"
                                    value="<?= $coacher->id; ?>" id="js-coach-head-<?= $key; ?>"
                                    class="d-none"
                                    name="GuideId" class="custom-control-input" required>
                            <a type="button" onclick="fieldEvents.meetingActions.choseTherapist(this)" class="btn text-primary">
                                <?= lang('choose') ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Meeting :: Scheduale tab -->
        <div class="js-subpage-tabs h-100 d-none" data-href="js-meeting-tab-date">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.meetingActions.submitDate(this)">
                <a href="javascript:;" class="text-dark bsapp-fs-18 mie-10">
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('date_and_shows') ?>
                </div>
                </div>
            </div>
            <div class="pt-10">
                <div class="bsapp-scroll overflow-auto h-100 ">
                    <div>
                        <div class="form-group px-15 py-5 mb-0">
                            <div class="datepicker" onchange="fieldEvents.choseStartDate(this)" required>

                            </div>
                            <input class="d-none custom-required" name="StartDate" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="js-single-edit-effect">
                            <div class="form-group px-15 py-10 mb-0">
                                <label class="" for="js-select2-meeting-frequency"><?= lang('frequency') ?></label>
                                <div class="w-100">
                                    <select onchange="MeetingPopup.toggleRelatedSelect(this)" class="js-select2 form-control" name="ClassRepeat"
                                            id="js-select2-meeting-frequency" data-hide-val="0" data-post-change="0" data-target="#js-meeting-div-stopped" required>
                                        <option value="0"><?= lang('one_time_cal') ?> </option>
                                        <option value="1 day"><?= lang('every'). " " .lang('day') ?></option>
                                        <option value="2 day"><?= lang('every'). " " .lang('two_days') ?></option>
                                        <?php for ($i = 3; $i < 7; $i++): ?>
                                            <option value="<?= $i ?> day"><?= lang('every'). " $i " .lang('days') ?></option>
                                        <?php endfor; ?>
                                        <option value="1 week"><?= lang('every_week') ?></option>
                                        <option value="2 week"><?= lang('every'). " " .lang('two_weeks') ?></option>
                                        <?php for ($i = 3; $i < 7; $i++): ?>
                                            <option value="<?= $i ?> week"><?= lang('every'). " $i " .lang('weeks') ?></option>
                                        <?php endfor; ?>
                                        <option value="1 month"><?= lang('every'). " " . lang('month')?></option>
                                        <option value="2 month"><?= lang('every'). " " . lang('two_months')?></option>
                                        <?php for ($i = 3; $i < 7; $i++): ?>
                                            <option value="<?= $i ?> month"><?= lang('every'). " $i " .lang('months') ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div id="js-meeting-div-stopped" class="d-none form-group px-15 py-10 mb-0">
                                <label class="" for="js-select2-class-stopped"><?= lang('ends_at_new_class') ?></label>
                                <div class="w-100">
                                    <select onchange="MeetingPopup.toggleRelatedSelect(this)" class="js-select2 form-control" id="js-select2-class-stopped"
                                            name="freqType" data-hide-val="0,2,3,4,5,6,7,8,9,10" data-post-change="0" data-target="#js-meeting-div-until-date">
                                        <option value="0"><?= lang('cal_never') ?> </option>
                                        <option value="date"><?= lang('select_date') ?></option>
                                        <?php
                                        if (!$classId):
                                            for ($i = 2; $i <= 10; $i++): ?>
                                                <option class="js-times-option" value="<?= $i; ?>">
                                                    <?= lang('after').' '.$i.' '.lang('shows_desk_plan') ?>
                                                </option>
                                            <?php
                                            endfor;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div id="js-meeting-div-until-date" class="d-none form-group px-15 py-10 mb-0">
                                <label class="" for="regularEndDate"><?= lang('desk_date_select') ?></label>
                                <div class="w-100">
                                    <input id="regularEndDate" name="regularEndDate" class="form-control" type="date" max="<?= date('Y-m-d', strtotime('+2 years')) ?>">
                                    <div class="invalid-feedback">
                                        <?= lang('enter_valid_future_lesson') ?> <?= date('d/m/Y', strtotime('+2 years')) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meeting :: Calendar tab -->
        <div class=" js-subpage-tabs h-100 d-none" data-href="js-meeting-tab-calendar">
            <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light">
                <div class="d-flex cursor-pointer" onclick="fieldEvents.meetingActions.submitCalendar(this)">
                <a href="javascript:;" class="text-dark bsapp-fs-18 mie-10" >
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('choose_calendar') ?>
                </div>
                </div>
            </div>
            <div>
                <div class="bsapp-scroll overflow-auto h-100 ">
                    <div>
                        <input class="d-none" id="brands" name="Brands" val="0" required>
                        <?php foreach ($brands as $brandKey => $brand):?>
                            <div class="mb-10">
                                <div class="px-15 py-10 d-flex justify-content-between ">
                                    <div class="d-flex ">
                                        <div class="bsapp-fs-18 font-weight-bold"><?= $brand->BrandName; ?></div>
                                    </div>
                                </div>
                                <?php if (!isset($brand->hasActiveSection)): ?>
                                    <div class="px-15 py-10 border-light border-bottom d-flex justify-content-between position-relative">
                                        <div class="bsapp-fs-18"><?= lang('no_cal_branch') ?></div>
                                    </div>
                                <?php
                                else:
                                    foreach($brand->Sections as $key => $section): ?>
                                        <div class="<?= ($section->Status == 1) ? 'd-none' : 'd-flex' ?> js-calendar-option px-15 py-10 border-light border-bottom align-items-center justify-content-between position-relative">
                                            <div class="d-flex">
                                                <div class="bsapp-fs-18"><?= $section->Title ?></div>
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input
                                                        type="radio" value="<?= $section->id; ?>" id="js-radio-meeting-<?= $brandKey; ?><?= $key; ?>"
                                                        data-preview="<?= $section->Title ?>" name="Floor" class="custom-control-input"
                                                        data-brand="<?= $brand->id ?>" required>
                                                <a type="button" onclick="fieldEvents.meetingActions.choseCalendar(this)" class="btn text-primary">
                                                    <?= lang('choose') ?>
                                                </a>
                                            </div>
                                        </div>
                                    <?php
                                    endforeach;
                                endif;?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- new meeting modal :: end -->

<!-- Add\Edit phone modal -->
<div class="ip-modal" id="itemModal">
    <div class="ip-modal-dialog">
        <div class="ip-modal-content text-right">
            <div class="ip-modal-header" <?php _e('main.rtl') ?>>
                <a class="ip-close" title="Close" style="float:<?php _e('main.left') ?>;">&times;</a>
                <h4 class="ip-modal-title"><?= lang('lesson_details_picture') ?></h4>
            </div>
            <div class="ip-modal-body" dir="rtl">

                <div class="alertb alert-info"><?= lang('image_rec_size') ?>
                    <br>
                    <?= lang('picture_size_details') ?>
                </div>

                <div class="btn btn-primary ip-upload"><?php _e('main.upload') ?> <input type="file" name="file" class="ip-file"></div>
                <!-- <button class="btn btn-primary ip-webcam">Webcam</button> -->
                <button type="button" class="btn btn-info ip-edit"><?= lang('edit_image') ?></button>
                <button type="button" class="btn btn-danger ip-delete"><?= lang('remove_image') ?></button>

                <div class="alert ip-alert"></div>
                <div class="ip-info"><?php _e('main.crop_info') ?></div>
                <div class="ip-preview"></div>
                <div class="ip-rotate">
                    <button type="button" class="btn btn-default ip-rotate-ccw" title="Rotate counter-clockwise"><i class="icon-ccw"></i></button>
                    <button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><i class="icon-cw"></i></button>
                </div>
                <div class="ip-progress">
                    <div class="text"><?php _e('main.uploading') ?></div>
                    <div class="progress progress-striped active"><div class="progress-bar"></div></div>
                </div>
            </div>
            <div class="ip-modal-footer">
                <div class="ip-actions">
                    <button type="button" class="btn btn-success ip-save"><?php _e('main.save_image') ?></button>
                    <button type="button" class="btn btn-primary ip-capture"><?php _e('main.capture') ?></button>
                    <button type="button" class="btn btn-default ip-cancel"><?php _e('main.cancel') ?></button>
                </div>
                <button type="button" class="btn btn-default ip-close"><?php _e('main.close') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- New class type modal -->
<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" data-backdrop="static" tabindex="1" role="dialog" id="js-modal-new-class-type">
    <div class="modal-dialog modal-dialog-centered modal-md bsapp-max-w-400p">
        <div class="modal-content border-0 shadow-lg bsapp-min-h-775p">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative p-0 rounded">
                <div class="bsapp-settings-dialog flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-20">
                    <div class="d-flex justify-content-start align-items-center mb-2F mt-5">
                        <div>
                            <h6><?= (lang('associate_class_type_membership')) ?></h6>
                        </div>
<!--                        <a onclick="fieldEvents.cancelNewClassType(this)" href="javascript:;" class="text-dark bsapp-fs-18">-->
<!--                            <i class="fal fa-times"></i>-->
<!--                        </a>-->
                    </div>
                    <p class="text-gray-500 text-start m-0 mb-20 bsapp-fs-13 bsapp-lh-15">
                        <?= lang('mark_to_add_class_type') ?>
                    </p>
                    <div class="scrollable">
                        <div class="pb-175 membership-container">

                        </div>
                    </div>
                    <div class="position-absolute bottom-0 left-0 bg-white px-15 pt-10 w-100">
                        <a onclick="fieldEvents.cancelNewClassType(this)" class="btn btn-lg bg-light text-gray-700 rounded-lg shadow-none border-0 w-100 mb-3 bsapp-fs-16">
                            <?= lang('cancel_app_booking') ?>
                        </a>
                        <a onclick="fieldEvents.confirmNewClassType(this)" class="btn btn-lg btn-primary text-white rounded-lg border-0 w-100 mb-10 bsapp-fs-16">
                            <?= lang('save') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" data-backdrop="static" tabindex="1" role="dialog" id="js-modal-new-class-type">
    <div class="modal-dialog modal-dialog-centered modal-md bsapp-max-w-400p">
        <div class="modal-content border-0 shadow-lg bsapp-min-h-775p">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative p-0 rounded">
                <div class="bsapp-settings-dialog flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-20">
                    <div class="d-flex justify-content-start align-items-center mb-2F mt-5">
                        <div>
                            <h6><?= (lang('associate_class_type_membership')) ?></h6>
                        </div>
                        <!--                        <a onclick="fieldEvents.cancelNewClassType(this)" href="javascript:;" class="text-dark bsapp-fs-18">-->
                        <!--                            <i class="fal fa-times"></i>-->
                        <!--                        </a>-->
                    </div>
                    <p class="text-gray-500 text-start m-0 mb-20 bsapp-fs-13 bsapp-lh-15">
                        <?= lang('mark_to_add_class_type') ?>
                    </p>
                    <div class="scrollable">
                        <div class="pb-175 membership-container">

                        </div>
                    </div>
                    <div class="position-absolute bottom-0 left-0 bg-white px-15 pt-10 w-100">
                        <a onclick="fieldEvents.cancelNewClassType(this)" class="btn btn-lg bg-light text-gray-700 rounded-lg shadow-none border-0 w-100 mb-3 bsapp-fs-16">
                            <?= lang('cancel_app_booking') ?>
                        </a>
                        <a onclick="fieldEvents.confirmNewClassType(this)" class="btn btn-lg btn-primary text-white rounded-lg border-0 w-100 mb-10 bsapp-fs-16">
                            <?= lang('save') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hotjar Tracking Code for https://login.boostapp.co.il -->
<!--<script>-->
<!--    (function(h,o,t,j,a,r){-->
<!--        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};-->
<!--        h._hjSettings={hjid:2748652,hjsv:6};-->
<!--        a=o.getElementsByTagName('head')[0];-->
<!--        r=o.createElement('script');r.async=1;-->
<!--        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;-->
<!--        a.appendChild(r);-->
<!--    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');-->
<!--</script>-->

<style>
    .select2-results__option[role="group"] {
        padding: 0px !important;
    }

    #js-meeting-popup .ui-datepicker{
        width: 100%;
    }

    #js-meeting-popup .label {
        cursor: pointer;
    }

    #js-meeting-popup .img-container img {
        max-width: 100%;
    }

    #js-meeting-popup .ui-state-default, .ui-widget-content .ui-state-default{
        text-align: center;
        border: none;
        color: gray;
        border-radius: 50%;
        background-color: transparent;
        display: inline-block;
        width: 35px;
        height: 35px;
        margin: 5px;
        padding-top: 5px;
    }

    #js-meeting-popup .ui-datepicker td span, .ui-datepicker td a{
        display: inline;
    }

    #js-meeting-popup .ui-datepicker td{
        text-align: center;
    }

    #js-meeting-popup .ui-state-active {
        background-color: grey;
        color: white !important;
        font-weight: 600;
    }

    #js-meeting-popup .ui-state-highlight {
        border: 1px solid gray !important;
    }

    #js-meeting-popup .ui-widget-header {
        border: 0;
        background: none;
    }

    #js-meeting-popup .ui-datepicker-current-day .ui-state-active { background: #00c736;; }

    #js-meeting-popup div.is-invalid {
        border: 1px solid red !important;
        border-radius: 5px;
    }

    #js-meeting-popup [aria-disabled="true"] {
        display: none;
    }

    #js-meeting-popup .disabled {
        opacity: 70%;
        pointer-events: none;
    }

    #js-meeting-popup form {
        overflow-x:hidden;
    }

    #js-meeting-popup form input[type="time"] {
        min-width: 100%;
        -webkit-appearance: none;
        -moz-appearance: none;
        display:block;
    }

    #js-meeting-popup .select2-container--default .select2-results__option[aria-disabled=true] {
        display: none;
    }

    @media screen and (min-width: 576px) {
        .modal-dialog.bsapp-max-w-400p {
            max-width: 400px !important;
        }
    }

    @media screen and (max-width: 576px) {
        .modal-dialog.bsapp-max-w-400p {
            max-width: 100% !important;
        }
    }

    #js-meeting-popup .js-subpage-home .bsapp-scroll {
        height: calc(100% - 128px) !important;
    }

    #js-meeting-popup .js-subpage-tabs .bsapp-scroll {
        height: calc(100% - 60px) !important;
    }

    @media screen and (max-height: 832px) {
        .modal-dialog.bsapp-max-w-400p {
            margin: 0px auto !important;
            height: 100% !important;
            min-height: auto
        }

        #js-modal-new-class-type .modal-content {
            min-height: 100% !important;
        }
    }

</style>