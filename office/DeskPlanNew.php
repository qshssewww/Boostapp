<?php
ini_set("max_execution_time", 0);

require_once '../app/init.php';
$pageTitle = lang('lessons_calendar');
if (!empty($_GET['param'])) {
    Session::set('deskPlan', true);
}
require_once '../app/views/headernew.php';
include_once('loader/loader.php');

if (!isset($_COOKIE['screen_width'])):
    ?>
    <script>
        $(document).ready(function () {
            $.cookie('screen_width', $(window).width());
            window.location.reload();
        });
    </script>
    <?php
    exit;
endif;



require_once 'Classes/ClassCalendar.php';
require_once 'Classes/ClassSettings.php';


if (Auth::check() ):
    if (Auth::userCan('159')):
        $ClassCalendar = new ClassCalendar();
        $calendarData = $ClassCalendar->getCalendarData();
        $calendarDataDecoded = json_decode($calendarData);
        $countClasses = 0;
        $countAct = 0;
        foreach ($calendarDataDecoded->Classes as $class) {
            if (!$class->isCancelled) {
                $countClasses++;
            }
        }
        ?>
        <script>
            const js_app_url = '<?php echo $_SERVER["HTTP_HOST"] != "localhost:8000" ? App::url() : "http://localhost:8000" ?>'; // login url
            const js_application_url = '<?= get_appboostapp_domain() ?>'; // app url
            var calendar_data = <?php echo $calendarData; ?>;
        </script>


<link href="/office/assets/css/fixstyle.css?<?php echo filemtime('assets/css/fixstyle.css') ?>" rel="stylesheet">
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<?php //if (!isset($_COOKIE['boostapp_lang']) || (isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] == 'he')) { ?>
<!--    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/locale/he.js"></script>-->
<?php //} ?>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5/main.min.css">
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@5.5.1/main.min.js"></script>
        <script type="text/javascript" src="js/calendarView/swiper.js"></script>
        <script type="text/javascript" src="js/calendarView/calendarView.js?<?php echo filemtime('js/calendarView/calendarView.js') ?>"></script>
        <script type="text/javascript" src="js/settingsDialog/tasksSettings.js?<?php echo filemtime(__DIR__ . '/js/settingsDialog/tasksSettings.js') ?>"></script>
        <script type="text/javascript" src="js/settingsDialog/settingsDialog.js?<?php echo filemtime('js/settingsDialog/settingsDialog.js') ?>"></script>
        <!--script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@2.18.0/dist/editor.min.js"></script-->
        <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/rr-1.2.7/sc-2.0.2/sl-1.3.1/datatables.min.css"/>
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/rr-1.2.7/sc-2.0.2/sl-1.3.1/datatables.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/clipboard-js@0.3.6/clipboard.min.js"></script>

        <link href="/office/assets/css/timepicker/timepicker.css" rel="stylesheet">
        <script src="/office/assets/js/timepicker/timepicker.js"></script>
        <link href="/office/calendarPopups/assets/css/popup.css" rel="stylesheet">
        <link href="/office/calendarPopups/assets/css/editPopup.css" rel="stylesheet">
        <link href="/office/calendarPopups/assets/css/createNewCalendar.css" rel="stylesheet">
        <link href="/office/calendarPopups/assets/css/frequencySettingsPopup.css" rel="stylesheet">
        <link href="/office/calendarPopups/assets/css/createNewClassType.css" rel="stylesheet">
        <link href="/office/calendarPopups/assets/css/cancelPopup.css" rel="stylesheet">
        <link href="/office/calendarPopups/assets/css/zoomPopup.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />


        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<!--        <script src="/office/calendarPopups/assets/js/newCalendar.js"></script>-->
<!--        <script src="/office/calendarPopups/assets/js/editPopup.js"></script>-->
<!--        <script src="/office/calendarPopups/assets/js/repeatPopup.js"></script>-->
<!--        <script src="/office/calendarPopups/assets/js/cancelPopup.js"></script>-->
<!--        <script src="/office/calendarPopups/assets/js/createNewClassPopup.js"></script>-->
<!--        <script src="/office/calendarPopups/assets/js/createNewLocationPopup.js"></script>-->
        <script src="/assets/office/js/jquery.Jcrop.min.js"></script>
        <script src="/assets/office/js/jquery.imgpicker.js"></script>
        <script src="/office/calendarPopups/assets/js/uploadImage.js"></script>
        <script src="/office/assets/js/sticky-1.0.4/jquery.sticky.js"></script>


        <!-- content goes here -->

<div id="bsapp-calendar" class="row d-flex ">
    <div class="col-md-3  p-0 px-md-15 d-md-flex" style="max-width:260px;">
        <div class="modal d-md-block  bsapp-modal-calendar-filter px-0 px-sm-auto text-start "  role="dialog" id="js-modal-calendar-filter" style="box-shadow: 0px 0px 5px rgb(0,0,0,0.16);">
            <div class="modal-dialog  modal-dialog-centered m-0 m-sm-auto">
                <div class="modal-content border-0 rounded-0">
                    <div class="modal-body h-100 position-relative p-md-0 px-0 pt-0 bg-light overflow-hidden">
                        <div class="js-modal-view-filter">
                            <div class="d-flex justify-content-between w-100 d-md-none mb-0 bg-white w-100">
                                <h5 class="bsapp-fs-28"><i class="fal fa-sliders-h  px-8 py-15"></i> <?php echo lang('view_options_new_cal') ?> </h5>
                                <a class="text-dark px-8 py-15 bsapp-fs-24" data-dismiss="modal" href="javascript:;"><i class="fal fa-times"></i></a>
                            </div>
                        </div>
                        <div class="js-modal-view-calendar">
                            <div class="d-flex justify-content-between w-100 mb-10 d-md-none py-15 px-8 bg-white w-100">
                                <h5 class="bsapp-fs-18"><i class="fal fa-calendar"></i> <?php echo lang('desk_date_select') ?> </h5>
                                <a class="text-dark" data-dismiss="modal" href="javascript:;"><i class="fal fa-times h4"></i></a>
                            </div>
                        </div>
                        <div class="bsapp-filter-scrollable  bsapp-overflow-y-auto bg-white pie-0" style="height: calc( 100% - 50px );">
                            <div class="d-flex flex-column  h-100 ">
                                <!-- Sidebar part -->
                                <div class="js-side-filters  js-modal-view-filter" style="font-size: 25px;">
                                    <div class="list-group rounded-0 d-md-none">
                                        <div class="list-group-item">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="js-sm-no-split-view" name="js_sm_split_settings" value="1" class="custom-control-input">
                                                <label class="custom-control-label" for="js-sm-no-split-view"><?php echo lang('desk_no_split') ?></label>
                                            </div>
                                        </div>
                                        <div class="list-group-item">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="js-sm-coach-split-view" name="js_sm_split_settings" value="0" class="custom-control-input">
                                                <label class="custom-control-label" for="js-sm-coach-split-view"><?php echo lang('desk_split_by_coach') ?></label>
                                            </div>
                                        </div>
                                        <div class="list-group-item">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="js-sm-location-split-view" name="js_sm_split_settings" value="2" class="custom-control-input">
                                                <label class="custom-control-label" for="js-sm-location-split-view"><?php echo lang('desk_split_location') ?></label>
                                            </div>
                                        </div>
                                        <div class="list-group-item">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="js-sm-3day-view" name="js_sm_calendar_settings" value="4" class="custom-control-input">
                                                <label class="custom-control-label" for="js-sm-3day-view"><?php echo lang('desk_three_days') ?></label>
                                            </div>
                                        </div>
                                        <div class="list-group-item">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="js-sm-weekly-view" name="js_sm_calendar_settings" value="2" class="custom-control-input">
                                                <label class="custom-control-label" for="js-sm-weekly-view"><?php echo lang('cal_weekly_view') ?></label>
                                            </div>
                                        </div>
                                        <div class="list-group-item">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="js-sm-monthly-view" name="js_sm_calendar_settings" value="1" class="custom-control-input">
                                                <label class="custom-control-label" for="js-sm-monthly-view"><?php echo lang('desk_a_month') ?></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="bsapp-calendar-sidebar   rounded ">
                                    <div class="position-relative bg-white rounded-lg  py-10 px-16 px-md-0 pb-75 pb-md-50 with-h-100">
                                        <?php for ($i = 0; $i < 3; $i++) : ?>
                                            <div class="mb-50 js-loading-sidebar-calendar-shimmer">
                                                <div class="bsapp-loading-shimmer p-15">
                                                    <div>
                                                        <div class="mb-15 w-100  h-10p">
                                                            <div class=" h-10p"></div>
                                                        </div>
                                                        <div class="mb-15 w-100  h-10p">
                                                            <div class=" h-10p"></div>
                                                        </div>
                                                        <div class="mb-15 w-100  h-10p">
                                                            <div class=" h-10p"></div>
                                                        </div>
                                                        <div class="mb-15 w-50  h-10p">
                                                            <div class=" h-10p"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                        <div class="js-side-filters px-8">
                                            <?php require_once '../app/views/calendar-view/calendar-sm.php'; ?>
                                            <?php require_once '../app/views/calendar-view/filters.php'; ?>
                                        </div>
                                    </div>
                                    <?php require_once '../app/views/calendar-view/stats.php'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 px-md-0 flex-fill bsapp-calendar-column">
        <!-- Main Part -->
        <div class="d-flex align-items-center d-md-none justify-content-between mb-10">
            <a id="js-modal-show-calendar" class="text-dark text-decoration-none h5"><span><?php echo lang('select_date_rest') ?></span> <i class="fal fa-caret-down"></i></a>
            <div class="">
                <a class="btn  btn-sm btn-outline-dark rounded-lg bsapp-text-sm-18 js-copy-link-meeting-booking" href="javascript:;"><i class="fal fa-link"></i></a>
                <a class="btn  btn-sm btn-outline-dark rounded-lg bsapp-text-sm-18" id="js-modal-show-filters" href="javascript:;"><i class="fal fa-sliders-h"></i></a>
                <?php if (Auth::userCan('160')): ?>
                <a class="btn btn-sm btn-outline-dark rounded-lg  bsapp-text-sm-18 js-sm-calendar-settings" id="" href="javascript:;"><i class="fal fa-cog"></i></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="bsapp-calendar-main js-div-calendar-main  bsapp-scroll-hidden px-8  rounded  py-10 position-relative">
            <div class="position-relative bg-white rounded-lg">
                <div class="row js-loading-calendar-shimmer">
                    <?php for ($i = 0; $i < 20; $i++) : ?>
                        <div class="col-md-3 col-sm-6 mb-20">
                            <div class="border border-light rounded ">
                                <div class="bsapp-loading-shimmer p-15">
                                    <div>
                                        <div class="mb-15 w-100  h-10p">
                                            <div class=" h-10p"></div>
                                        </div>
                                        <div class="mb-15 w-100  h-10p">
                                            <div class=" h-10p"></div>
                                        </div>
                                        <div class="mb-15 w-100  h-10p">
                                            <div class=" h-10p"></div>
                                        </div>
                                        <div class="mb-15 w-50  h-10p">
                                            <div class=" h-10p"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
                <?php require_once '../app/views/calendarSettings.php'; ?>
                <div class="d-none d-md-flex justify-content-between align-items-center mb-20 js-calendar-custom-header">
                    <div class="d-flex">
                        <div class="w-150p  mie-15">
                            <select class="select2-calendar-select" name="calendar-view-select" required>
                                <option value="3" data-view="timeGridDay"><?php echo lang('desk_daily') ?></option>
                                <option value="4" data-view="timeGridThreeDay"><?php echo lang('desk_3_days') ?></option>
                                <option value="2" data-view="timeGridWeek"><?php echo lang('desk_weekly_view') ?></option>
                                <option value="1" data-view="dayGridMonth" selected><?php echo lang('desk_monthly_view') ?></option>
                            </select>
                        </div>
                        <div>
                            <a data-toggle="tooltip" data-placement="top" title="<?php echo lang('previous') ?>" class="btn btn-outline-gray-300 mie-8 js-calendar-main-prev text-dark">
                                <i class="fas fa-angle-left"></i>
                            </a>
                            <a data-toggle="tooltip" data-placement="top" title="<?php echo lang('next_client_profile') ?>" class="btn btn-outline-gray-300 js-calendar-main-next text-dark">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div id="js-calendar-date-range" class="bsapp-fs-20">
                        <span></span>
                    </div>
                    <div class="d-flex">
                        <a class="btn btn-outline-gray-300 mie-15 js-btn-today text-dark"><?php echo lang('today') ?></a>
                        <a class="btn btn-outline-gray-300 mie-15 js-copy-link-meeting-booking"><i class="fal fa-link"></i></a>
                        <?php if (Auth::userCan('160')): ?>
                        <a class="btn btn-outline-gray-300 js-sm-calendar-settings"><i class="fal fa-cog"></i></a>
                        <?php endif;?>
                    </div>
                </div>

                <div id="calendarMainAboutScroll" class="modal fade bottom" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-frame modal-bottom">
                        <div class="modal-content text-center">
                            <div class="modal-body">
                                <div class="hand-animation">
                                    <div class="box"><i class="fal fa-hand-pointer"></i></div>
                                </div>
                                <p><?php echo lang('slide_notice_cust') ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-gray" data-dismiss="modal"><?php echo lang('confirm') ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php require_once '../app/views/calendar-view/calendar.php'; ?>
            </div>
            <div class="d-flex justify-content-center w-100">
                <a class="js-back-to-today btn btn-white bg-white font-weight-bold position-fixed py-10 px-10 d-md-none " style="bottom: calc(90px + env(safe-area-inset-bottom));z-index:2;box-shadow:0px 3px 10px rgb(0,0,0,0.16);"><?php echo lang('desk_back_to_today') ?></a>
            </div>
        </div>
    </div>
    <div class="modal  bsapp-modal-calendar-filter px-0 px-sm-auto text-start " tabindex="-1" role="dialog" id="js-modal-calendar-selector" style="box-shadow: 0px 0px 5px rgb(0,0,0,0.16);">
        <div class="modal-dialog  modal-dialog-centered m-0 m-sm-auto">
            <div class="modal-content border-0 h-100 rounded-0">
                <div class="modal-body h-100  position-relative p-md-0 px-0 py-0 bg-light overflow-hidden">
                    <div class="d-flex justify-content-between w-100  bg-white w-100">
                        <h5 class="bsapp-fs-18    py-15 px-8"><i class="fal fa-calendar"></i> <?php echo lang('desk_date_select') ?> </h5>
                        <a class="text-dark    py-15 px-8 bsapp-fs-24" data-dismiss="modal" href="javascript:;"><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="js-calendar-days-names    py-15 px-8 bg-white border-bottom border-top border-light ">
                        <div class="bsapp-sm-calendar-modal-view   fc fc-media-screen fc-theme-standard">
                            <div class="fc-view-harness fc-view-harness-passive">
                                <div class="fc-daygrid fc-dayGridMonth-view fc-view">
                                    <table class="fc-scrollgrid ">
                                        <thead>
                                            <tr class="fc-scrollgrid-section fc-scrollgrid-section-header ">
                                                <td>
                                                    <div class="fc-scroller-harness">
                                                        <div class="fc-scroller" style="overflow: visible;">
                                                            <table class="fc-col-header " style="width: 0px;">
                                                                <colgroup></colgroup>
                                                                <tbody>
                                                                    <tr class="js-calendar-heading-for-ltr">
                                                                        <th class="fc-col-header-cell fc-day fc-day-sun">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_sunday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-mon">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_monday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-tue">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_tuesday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-wed">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_wednesday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-thu">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_thursday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-fri">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_friday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-sat">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_saturday') ?></a></div>
                                                                        </th>
                                                                    </tr>
                                                                    <tr class="js-calendar-heading-for-rtl">
                                                                        <th class="fc-col-header-cell fc-day fc-day-sat">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_saturday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-fri">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_friday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-thu">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_thursday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-wed">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_wednesday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-tue">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_tuesday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-mon">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_monday') ?></a></div>
                                                                        </th>
                                                                        <th class="fc-col-header-cell fc-day fc-day-sun">
                                                                            <div class="fc-scrollgrid-sync-inner"><a class="fc-col-header-cell-cushion "><?php echo lang('desk_sunday') ?></a></div>
                                                                        </th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bsapp-filter-scrollable js-filter-calendar-scrollable bsapp-overflow-y-auto bg-white pie-0" style="height: calc( 100% - 137px );">
                        <div class="d-flex flex-column  h-100 ">
                            <!-- Sidebar part -->
                            <div class="bsapp-calendar-sidebar   rounded ">
                                <div class="d-flex flex-column   position-relative js-swiper-calendar px-8">

                                </div>
                            </div>
                            <div class="d-flex justify-content-center w-100">
                                <a class="js-back-to-today btn btn-white bg-white font-weight-bold position-fixed py-10 px-10 d-md-none " style="bottom: calc(90px + env(safe-area-inset-bottom));z-index:2;box-shadow:0px 3px 10px rgb(0,0,0,0.16);"><?php echo lang('desk_back_to_today') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="js-char-error-html position-relative d-none " class="">
        <div class="js-error-div-char" >
            <div class="d-flex justify-content-end w-100 position-absolute" style="z-index:100;left:0;right:0;top:0;">
                <a class="text-dark js-close-char-popup pie-15 pt-15 py-15" href="javascript:;" data-dismiss="modal"><i class="fal fa-times h4"></i></a>
            </div>
            <div class="p-15 position-absolute d-flex align-items-center justofy-content-center" style="left:0;top:0;right:0;bottom:0;z-index:99;" >
                <div class="d-flex flex-column align-items-center w-100 text-center">
                    <i class="fal fa-info-circle fa-4x mb-20 text-gray-200"></i>
                    <h5 class="text-secondary mb-10 js-error-title"><?php echo lang('error_oops_something_went_wrong') ?></h5>
                    <span class="js-error-description mb-20"></span>
                    <a class="btn btn-light js-error-btn-reload"><i class="fal fa-redo"></i> <?php echo lang('reload_main') ?></a>
                </div>
            </div>
        </div>
    </div>
    <div class="js-modal-print-shimming-loader position-relative d-none">
        <div class="js-loader-div-print h-100"  >
            <div class="d-flex justify-content-end w-100 position-absolute" style="z-index:100;left:0;right:0;top:0;">
                <a class="text-dark  pie-15 pt-15 py-15" href="javascript:;" data-dismiss="modal"><i class="fal fa-times h4"></i></a>
            </div>
            <div class="p-15 position-absolute" style="left:0;top:45px;right:0;bottom:0;z-index:99;" >
                <div class="overflow-hidden " style="height: calc( 100% - 70px );">
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


            <?php
//        include_once('calendarPopups/mainPopup.php');
//        include_once('calendarPopups/editPopup.php');
//        include_once('calendarPopups/createNewClassTypePopup.php');
        include_once('calendarPopups/createNewCalendarPopup.php');
        include_once('calendarPopups/frequencySettingsPopup.php');
        include_once('calendarPopups/cancelPopup.php');
        include_once('calendarPopups/zoomPopup.php');

        ?>

    <?php if (Auth::userCan('166')): ?>
        <a data-toggle="modal" class="floating-plus-btn d-flex bg-primary" href="#js-action-modal" >
            <i class="fal fa-plus fa-lg margin-a"></i>
        </a>
    <?php endif;?>
</div>

<div class="d-none js-calendar-copy">
    <div class="bsapp-sm-calendar-modal-view js-calendar-copy-id">
    </div>
</div>


<?php require_once '../app/views/footernew.php'; ?>
<!--<script src="js/trainee/trainee.js?--><?php //echo filemtime('js/trainee/trainee.js') ?><!--"></script>-->
<!--<script src="js/createClass/createClass.js?--><?php //echo filemtime('js/createClass/createClass.js') ?><!--"></script>-->
<script type="text/javascript" src="js/settingsDialog/settingsDialog.js?<?php echo filemtime('js/settingsDialog/settingsDialog.js') ?>"></script>
<script type="text/javascript" src="js/settingsDialog/calendarSettings.js?<?php echo filemtime('js/settingsDialog/calendarSettings.js') ?>"></script>

<style>
    @media screen and (min-width:767px) {
        .bsapp-calendar-column {
            width: calc(100% - 275px) !important;
        }
    }
</style>

<style id="js-dynamic-styles">

</style>

<style>
    .disabled {
        pointer-events: none;
        opacity: 60%;
    }

    .calendars-list .disabled {
        pointer-events: none;
        opacity: 1;
    }

    .fa-do-not-enter {
        color: red;
    }
    body {
        overflow-x: hidden;
    }
    .card {
        padding: 2px 3px;
        margin: 0px 3px;
    }
    .w-30 {
        width: 35%;
    }
    .table tbody{
        min-height:300px !important;
    }
    .edit-icon {
        background: #f7f7f7;
        padding: 5px;
        color: #000;
        border-radius: 5px;
    }
    .fc-timegrid-event-harness{
        border-bottom : 1px solid transparent;
    }
    /*#calendar-main  .fc-scroller {
       scroll-behavior:smooth;
    }*/
    .bsapp-tab-pane-waiting .dataTables_wrapper{
        display : flex ;
        width : 100%;
    }

    .bsapp-content  .container-fluid:first-of-type{
        margin-top:13px  !important;
        margin-bottom : 0px !important;
    }

    body{
        overflow: hidden !important ;
    }
    .pb-65{
        padding-bottom : calc(85px + env(safe-area-inset-bottom)) !important;
    }
</style>
<script type="text/javascript">
    var $companyNo;
    $(document).ready(function () {
        $companyNo = <?php echo $CompanyNum ?>
    });
</script>


<script>
    $(document).ready(function () {
        $("#newTask").on("click", function () {
            $("#js-action-modal").modal("hide");
            if ($('#js-task-popup')) {
                handleNewTask();
            } else {
                $("#js-action-modal").modal("hide");
                $("#AddNewTask").modal("show");
            }
        });

        $("#newClass").on("click", function () {
            $("#js-action-modal").modal("hide");
            OpenClassPopup(null, 0, function () {
                fieldEvents.classActions.show()
            })
        });

        $("#newMeeting").on("click", function () {
            $("#js-action-modal").modal("hide");
            OpenClassPopup(null, 0, function () {
                fieldEvents.meetingActions.show()
            })
        });

        $("#newPersonalClass").on("click", function () {
            $("#js-action-modal").modal("hide");
            NewsClass();
        });

        $("#removeClasses").on('click', function () {
            $("#js-action-modal").modal("hide");
            removeClasses();
        });

        $("body").on("click", "#js-link-edit-class", function () {
            OpenClassPopup($("#js-class-data").data('classid'))
        })

        var hash = window.location.hash;
        if (hash == '#js-action-modal' && window.location.pathname == '/office/DeskPlanNew.php') {
            $(hash).modal("show");
        }

            });
        </script>
    <?php else: ?>
        <?php redirect_to('../index.php'); ?>
    <?php endif ?>
<?php endif ?>
<?php if (Auth::guest()): ?>
    <?php redirect_to('../index.php'); ?>
<?php endif ?>