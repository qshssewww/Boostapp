<?php
require_once dirname(__FILE__, 3) . "/office/Classes/Translations.php";
require_once dirname(__FILE__, 3) . "/office/Classes/Settings.php";
require_once dirname(__FILE__, 3) . "/office/services/meetings/MeetingService.php";
require_once __DIR__ . "/../helpers/MultiUserHelper.php";

$useIntercom = true;    // false - use WhatsApp

$color = Config::get('app.color_scheme');
?>
<!doctype html>
<html lang="en" dir="<?= isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_dir'] : 'rtl' ?>" >
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge"  />
<!--        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">-->
        <meta name="viewport" content="width=device-width, initial-scale = 1.0,maximum-scale=1.0, user-scalable=no" />
        <meta name="csrf-token" content="<?php echo csrf_token() ?>">

        <link href="<?php echo asset_url('img/favicon2.png') ?>" rel="icon">

        <title><?php echo (isset($pageTitle) ? $pageTitle . ' | ' : '') . Config::get('app.name') ?></title>

        <link href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/office/css/main.css?' .filemtime(__DIR__.'/../../assets/office/css/main.css'); ?>" rel="stylesheet">

        <link href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/office/css/animate.css?'.filemtime(__DIR__.'/../../assets/office/css/animate.css') ?>" rel="stylesheet">

        <link rel="stylesheet" href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/CDN/CheckBox/pretty-checkbox.min.css' ?>">

        <script type="text/javascript" src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/js/date_time.js' ?>"></script>

        <link href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/assets/office/css/colors/{$color}.css" ?>" rel="stylesheet" id="color_scheme">

        <script src="/assets/js/vendor/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" ></script>
        <script src="../../assets/js/jquery-ui.min.js"></script>
<!--        <script src="/assets/js/jquery-ui-1.13.1.min.js"></script>-->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

        <!-- dashboard page related assets :: begin -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/5.4.2/css/swiper.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/5.4.2/js/swiper.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>

        <!-- dashboard page related assets :: end -->
<!--        <link rel="stylesheet" href="/assets/css/jquery-ui-1.13.1.min.css" />-->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">


        <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/office/js/BeePOS.js' ?>"></script>

        <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/office/js/jquery.Jcrop.min.js' ?>"></script>
        <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/assets/js/jquery.ui.touch-punch.min.js" ?>"></script>

        <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/office/js/main.js?'.filemtime(__DIR__.'/../../assets/office/js/main.js') ?>"></script>

        <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/office/assets/js/bootstrap-notify.js" ?>"></script>

        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>



        <script>

            BeePOS.options = {

                ajaxUrl: '<?php echo (isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] != "localhost:8000") ? 'https://' . $_SERVER["HTTP_HOST"] . '/ajax.php' : "http://localhost:8000/ajax.php" ?>',

                lang: <?php echo json_encode(trans('main.js')) ?>,

                debug: <?php echo Config::get('app.debug') ? 1 : 0 ?>,

                api: '<?php echo App::url("api/") ?>'

            };

        </script>



        <link rel="stylesheet" href="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/office/tinybox2/style.css" ?>" />

        <script type="text/javascript" src="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/office/tinybox2/tinybox.js" ?>"></script>

        <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/js/jqueryExecuting.js' ?>"></script>

        <link href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/CDN/select2/select2.min.css' ?>" rel="stylesheet" />

        <link href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/CDN/select2/select2-bootstrap.css' ?>" rel="stylesheet">

        <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/CDN/select2/select2.min.js' ?>"></script>

        <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/CDN/select2/he.js' ?>"></script>

        <!-- typeahead related scripts :: begin -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>
		<script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/office/assets/js/custom-plugins.js' ?>" defer></script>
        <!-- typeahead related scripts :: end   -->
        <?php
        if (!empty($headerCss) && is_array($headerCss)) {

            foreach ($headerCss as $css) {

                if (!empty($css['href']))
                    printf('<link href="%s" rel="stylesheet" />', $css['href']);

                if (!empty($css['inline']))
                    printf('<style>%s</style>', $css['inline']);
            }
        }

        $user = new Users();
        $isDeskPlan = Session::get('deskPlan') ?? false;
        $userIdFromGet = $_GET['param'] ?? '';

        //redirect if it's not auth.user and it's not user from Get, or if it's user from Get but there is no enough data
        if (!Auth::check() && (!$isDeskPlan || empty($userIdFromGet))) {
            redirect_to('//'.$_SERVER['HTTP_HOST']);
        } else {
            if ($isDeskPlan) { //auto login for new user from Get
                Session::delete('deskPlan');
                require_once dirname(__FILE__, 3) . "/office/Classes/UserAccessGet.php";
                require_once dirname(__FILE__, 3) . "/office/Classes/Users.php";
                require_once dirname(__FILE__, 3) . "/office/services/EmailService.php";

                $userAccess =  UserAccessGet::findByUserId((int)$userIdFromGet);
                if (isset($userAccess)) {
                    UserAccessGet::deleteByUserId((int)$userIdFromGet);

                    $lessThan15MinutesFromUserCreatedDate = (time() - strtotime($userAccess->created_at)) <= 15 * 60;
                    if ($lessThan15MinutesFromUserCreatedDate) {
                        if(!Auth::loginById((int)$userIdFromGet, true)) {
                            redirect_to('//' . $_SERVER['HTTP_HOST']);
                        }
                    } else {
                        redirect_to('//' . $_SERVER['HTTP_HOST']);
                    }
                } else {
                    redirect_to('//' . $_SERVER['HTTP_HOST']);
                }
            }

            $CompanyNum = Auth::user()->CompanyNum;
            $translations = new Translations();
            $languages = $translations->getLanguages();
            $CheckPipe = DB::table('pipeline_category')->where('CompanyNum', '=', $CompanyNum)->orderBy('id', 'ASC')->first();

            $MainPipeId = $CheckPipe->id ?? '';
            $supplier = Auth::user();
            $CompanySettingsDash = Settings::getSettings($CompanyNum);
            $types = ($CompanySettingsDash->BusinessType == 5 || $CompanySettingsDash->BusinessType == 6) ? '300' : '320';

            $userRole = DB::table('roles')->where('id', '=', Auth::user()->role_id)->first();
            $appSettings = DB::table('appsettings')->where('CompanyNum', $CompanyNum)->first();
            $userDetails = DB::table('users')->where('id', Auth::user()->id)->first();

            $studiosList = MultiUserHelper::getList(Auth::user());
            $waitingCount = MeetingService::getMeetingsCountByType($CompanyNum, MeetingService::TYPE_NOT_APPROVED);
            $ChatCountNew = DB::table('chat')->where('Status', '=', '0')->where('SendFrom', '=', 1)->where('CompanyNum', '=', Auth::user()->CompanyNum)->count();

            if(isset($_SESSION["CompanyNum"], $_SESSION["notification"]) && $CompanyNum == $_SESSION["CompanyNum"]){
                $notifications = $_SESSION["notification"];
            }
            else {
                $notifications = DB::table('appnotification')
                    ->where('Status', '=', '0')
                    ->where('Type', '=', '3')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where(function($q) {
                        $q->where('Date', '=', date('Y-m-d'))->where('Time', '<=', date('H:i:s'))
                            ->where('Date', '<', date('Y-m-d'));
                    })->count();

                $_SESSION["notification"] = $notifications;
                $_SESSION["CompanyNum"] = $CompanyNum;
            }
            $landingPath = $_SERVER['PHP_SELF'] == '/office/landings/index.php' || basename($_SERVER['PHP_SELF']) == 'landings';
            $settingsPaths = array('SettingsDashboard.php', 'BusinessSettings.php', 'DeskPlanSettings.php',
                'Roleslist.php', 'AgentList.php', 'Branch.php', 'ClassType.php', 'ActivitySettings.php',
                'DeviceNumbers.php', 'AppsSettings.php', 'CheckInSettings.php', 'MettingsRoom.php', 'ClassTemplate.php',
                'SavedMessagesList.php', 'SavedNotificationList.php', 'StatusList.php', 'SourceList.php', 'ReasonsList.php',
                'ClientLevel.php', 'CalType.php', 'Automation.php', 'DynamicForms.php', 'facebookAttachLeads.php','onlineLibrary.php');

            $reportsPaths = array('ReportsDash.php', 'CartesetAll.php', 'Sales.php', 'SalesReports.php', 'cartesetcheck.php',
                'cartesetcredit.php', 'ExpectedIncome.php', 'MedicalReport.php', 'CoronaReport.php', 'TakanonReport.php',
                'NoneShow.php', 'RegularReport.php', 'NoneRegularReport.php', 'MembershipFrozen.php', 'freezReport.php', 'ClassNotificationReport.php',
                'ClassEntrancesReport.php', 'ManageClass.php', 'ManageClassHistory.php', 'HokList.php', 'Carteseterror.php',
                'ReportSms.php', 'ReportMail.php', 'LogChat.php', 'LogList.php', 'AppLog.php', 'LogNotification.php', 'GreenPassReport.php');
            if (!isset($pageTitle)) {
                $pageTitle = '';
            }

            function rgbcode($id) {
                $res = substr(md5($id), 0, 6);
                return $res;
            }

            function hexcode($str) {
                $code = dechex(crc32($str));
                $code = substr($code, 0, 6);
                return $code;
            }

            $appName = mb_strlen($CompanySettingsDash->AppName) >= 22 ? mb_substr($CompanySettingsDash->AppName, 0, 22) . '...' : $CompanySettingsDash->AppName;
            ?>
            <!-- Site Properties -->

            <link rel="stylesheet" type="text/css" href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/office/dist/css/app.min.css?'. filemtime(__DIR__. '/../../office/dist/css/app.min.css') ?>">

            <link href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/office/css/imgpicker.css' ?>" rel="stylesheet">
            <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/office/js/jquery.imgpicker.js' ?>"></script>
            <link href="/CDN/fontawesome-pro-5.15.3/css/all.css" rel="stylesheet">
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
            <script src="/office/js/translation/translate.js"></script>

		<script src="/CDN/datatables/moment.min.js?<?= filemtime(__DIR__.'/../../CDN/datatables/moment.min.js') ?>"></script>
        <?php if (!isset($_COOKIE['boostapp_lang']) || (isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] == 'he')) { ?>
			<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/locale/he.js"></script>
        <?php } ?>

            <?php
            $js_open_sidebar = true;
            $js_sidebar_width = "250px";
            $js_sidebar_sub_class = "bsapp-shown";
            $js_sidebar_sub_display = "style='display:block;'";
            $js_bsapp_shrink = "bsapp-shrink";
            $js_is_active = "is-active";
            $js_scrollbar_overflow = "";
            if (isset($_COOKIE['js_sidebar_opened']) && $_COOKIE['js_sidebar_opened'] == "no") {
                $js_open_sidebar = false;
                $js_sidebar_width = "60px";
                $js_sidebar_sub_class = "";
                $js_sidebar_sub_display = "";
                $js_bsapp_shrink = "";
                $js_is_active = "";
                $js_scrollbar_overflow = "bsapp-scroll-temp-css";
            }
            ?>

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','GTM-P3BPF8F');</script>
        <!-- End Google Tag Manager -->

        </head>
        <body class="">
			<div id="preloader" class="hide-preloader">
				<svg class="spinner" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="4" stroke-dasharray="140px" stroke-linecap="round" stroke="#00c736" cx="33" cy="33" r="30" /></svg>
			</div>
        	<!-- desktop menu :: begin -->
            <div class="d-flex">

                <div class="bsapp-md-menu js-md-menu" style="width:  <?php echo $js_sidebar_width; ?> ;" >
                    <div class="js-slim-scroll  bg-dark <?php echo $js_scrollbar_overflow; ?>  ">

                        <div class="flex-column justify-content-between    position-relative   d-none  d-md-flex" style="min-height: 100vh;" >
                            <div  class="position-absolute w-60p" style="background:#15202e;top:0;bottom:0;"></div>
                            <div class="px-0">
                                <!-- brand menu item :: begin -->
                                <div class="px-10 bsapp-menu-item" id="home-boost">
                                    <div  class="d-flex    list-group-item   p-0 my-5 mx-auto boost-menu-title cursor-pointer ">
                                        <div class="d-flex  bsapp-min-h-40p align-items-center ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fas fa-bold"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20" <?php echo $js_sidebar_sub_display; ?>>Boostapp </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- brand menu item :: end -->
                                <!-- dropdown menu  :: begin -->
                                <div class="px-10 bsapp-menu-item" id="company-name">
                                    <div  class="d-flex    list-group-item    p-0 my-5 mx-auto boost-menu-title ">
                                        <div class="d-flex  bsapp-min-h-40p align-items-center ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><img src="<?php echo (!empty($appSettings->logoImg)) ? '/office/files/logo/' . $appSettings->logoImg : '/office/files/logo/smallDefault.png' ?>" class="w-30p rounded-circle"></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div id="app-name-div"
												 class="js-toggle-visibility bsapp-toggle-visibility <?php echo $js_sidebar_sub_class; ?> text-start bsapp-fs-14 pis-20 " <?php echo $js_sidebar_sub_display; ?>>
												<span><?php echo $appName; ?></span>
                                            <?php if (Auth::user()->role_id == 1) { ?>
                                                <i class="fas fa-caret-down text-white fa-lg js-studios-list-arrow studios-list-arrow"></i>
                                                <a class="stretched-link" data-toggle="collapse" title="<?php echo $CompanySettingsDash->AppName. ' :: '.$CompanyNum ?>" href="#js-menu-2" onclick="$('.js-studios-list-arrow').toggleClass('fa-rotate-180')"></a>
                                            <?php } elseif (count($studiosList) > 1 && Auth::user()->role_id != 1) { ?>
                                                <i class="fas fa-caret-down text-white fa-lg js-studios-list-arrow studios-list-arrow"></i>
                                                <a class="stretched-link" data-toggle="collapse" title="<?php echo $CompanySettingsDash->AppName; ?>" href="#js-menu-switch-brands" onclick="$('.js-studios-list-arrow').toggleClass('fa-rotate-180')"></a>
                                            <?php } else { ?>
                                                <a class="stretched-link" title="<?php echo $CompanySettingsDash->AppName; ?>" href="/office/"></a>
                                            <?php } ?>
											</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="position-relative bsapp-sub-menu">
                                    <div class="collapse position-absolute text-dark vh-100 overflow-auto " id="js-menu-2" style="z-index:9;">
                                        <div class="card  bg-light h-100 border-0 overflow-auto">
                                            <div>
                                                <div class="list-group text-dark  list-group-flush px-0 text-start">
                                                    <div class="list-group-item bsapp-sidebar-search-box">
                                                        <div class="d-flex">
                                                            <div class="input-group input-group-sm mb-3 bsapp-search-box" id="js-studiosSearch">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text" ><i class="far fa-search"></i></span>
                                                                </div>
                                                                <input type="text" class="form-control typeahead js-typeahead js-studio-input" placeholder="<?php echo lang('search_studio_footer') ?>" >
                                                            </div>
                                                            <a href="javascript:;" class="js-close-collapse pis-15" onclick="$('.js-studios-list-arrow').toggleClass('fa-rotate-180')" data-attr="js-studio-searches" data-search="js-studio-input"><i class="fas fa-times"></i></a>
                                                        </div>
                                                    </div>
                                                </div>   
                                                <div class="list-group text-dark  list-group-flush px-0 text-start js-studio-searches my-20   overflow-auto" style="height:calc( 100vh - 150px);">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!empty($studiosList)) { ?>
                                    <div class="position-relative bsapp-sub-menu">
                                        <div class="collapse position-absolute text-dark vh-100 w-100 overflow-auto " id="js-menu-switch-brands" style="z-index:9;">
                                            <div class="card  bg-light h-100 border-0 overflow-auto">
                                                <div>
                                                    <div class="list-group text-dark  list-group-flush px-0 text-end">
                                                        <div class="list-group-item bsapp-sidebar-search-box">
                                                            <div class="">
                                                                <a href="javascript:;" class="js-close-collapse pis-15"><i class="fas fa-times"></i></a> 
                                                            </div>
                                                        </div>
                                                    </div>   
                                                    <div class="list-group text-dark  list-group-flush px-0 text-start js-studio-searches overflow-auto" style="height:calc( 100vh - 150px);">
                                                        <?php foreach ($studiosList as $studioItem) { ?>
                                                            <a href="javascript:;" data-id="<?php echo $studioItem['id'] ?>" class="<?= $studioItem['type'] === 'brand' ? 'js-switch-brand' : 'js-switch-multiuser' ?> list-group-item d-flex align-items-center py-10 px-10">
                                                                <img class="w-30p h-30p rounded-circle mie-8" src="<?php echo (!empty($studioItem['logo'])) ? '/office/files/logo/' . $studioItem['logo'] : '/office/files/logo/smallDefault.png' ?>"/>
                                                                <div class="d-flex flex-column  <?php echo $studioItem['isCurrentStudio'] ? 'text-primary' : '' ?>">
                                                                    <span><?php echo $studioItem['name']; ?></span>
                                                                </div>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <!-- dropdown menu  :: end -->
                                <div class="px-10 bsapp-menu-item">
                                    <div  class="d-flex  list-group-item  <?php if ($_SERVER['PHP_SELF'] == '/office/index.php' || basename($_SERVER['PHP_SELF']) == 'office') echo 'active'; ?> p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-tachometer-alt"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>> 
                                                <?php echo lang('path_main'); ?>
                                            </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-tachometer-alt"></i></span>   
                                        <a class="stretched-link" title="<?php echo lang('path_main'); ?>" href="/office/"></a>
                                    </div>
                                </div>
                                <div class="px-10 bsapp-menu-item">
                                    <div  class="d-flex    list-group-item  <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'DeskPlanNew.php') !== false) echo "active"; ?>  p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-calendar-day"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start  pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?php echo lang('sidebar_calendar'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-calendar-day"></i></span>  
                                        <a class="stretched-link" title="<?php echo lang('sidebar_calendar'); ?>" href="/office/DeskPlanNew.php"></a>
                                    </div>
                                </div>
                                <div class="px-10 bsapp-menu-item">
                                    <div  class="d-flex    list-group-item <?php if ($_SERVER['PHP_SELF'] == '/office/Client.php' || strpos(basename($_SERVER['REQUEST_URI']), 'ClientProfile.php') !== false) echo "active"; ?> p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-user-alt"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?php echo lang('clients'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-user-alt"></i></span>
                                        <a class="stretched-link" title="<?php echo lang('clients'); ?>" href="/office/Client.php?Act=0"></a>
                                    </div>
                                </div>
                                <div class="px-10 bsapp-menu-item">
                                <div  class="d-flex list-group-item <?php echo (in_array(basename($_SERVER['SCRIPT_NAME']), array('Leads.php', 'manage-leads.php', 'LeadsJoinReport.php'))) ? "active" : ''; ?>  p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-users-class"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?php echo lang('manage_interested'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-users-class"></i></span>
                                        <a class="stretched-link" title="<?php echo lang('manage_interested'); ?>" href="/office/manage-leads.php"></a>
                                    </div>
                                </div>
                                <div class="px-10 bsapp-menu-item">
                                    <div  class="d-flex    list-group-item <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Cal.php') !== false || strpos(basename($_SERVER['REQUEST_URI']), 'TaskReport.php') !== false) echo "active"; ?> p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-tasks"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start  pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?php echo lang('tasks'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-tasks"></i></span>
                                        <a class="stretched-link" title="<?php echo lang('tasks'); ?>" href="/office/Cal.php"></a>
                                    </div>
                                </div>
                                <!--div class="px-10 bsapp-menu-item">
                                    <div  class="d-flex    list-group-item <?php //if (strpos(basename($_SERVER['REQUEST_URI']), 'onlineLibrary.php') !== false) echo "active"; ?> p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-video"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php //echo $js_sidebar_sub_class; ?> text-start  pis-20"  <?php //echo $js_sidebar_sub_display; ?>> <?php //echo lang('vod_library'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-video"></i></span>
                                        <a class="stretched-link" title="<?php //echo lang('vod_library'); ?>" href="/office/onlineLibrary.php"></a>
                                    </div>
                                </div-->
                                <?php //todo-bp-909 (cart) remove-beta
                                $CompanySettingsDash = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();
                                if (in_array($CompanySettingsDash->beta, [1])) { ?>
                                    <div class="px-10 bsapp-menu-item">
                                        <div  class="d-flex list-group-item p-0 my-5 mx-auto" >
                                            <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box">
                                                <div class="bsapp-menu-icon w-40p text-center"><i class="fal fa-cart-plus"></i></div>
                                            </div>
                                            <div class="d-flex align-items-center w-100 ">
                                                <div class="js-toggle-visibility bsapp-toggle-visibility <?php echo $js_sidebar_sub_class;?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?php echo lang('cart_title'); ?>
                                                    <label class="badge badge-white text-primary bsapp-fs-14 bsapp-lh-17 p-0 mis-10 bsapp-badge-insurance-new" ><?= lang('new') ?></label>
                                                </div>
                                            </div>
                                            <span class="bsapp-active-bg-icon"><i class="fal fa-users-class"></i></span>
                                            <a class="stretched-link" title="<?php echo lang('cart_title'); ?>" href="/office/cart.php"></a>
                                        </div>
                                    </div>
                                <?php }?>



                            </div>
                            <div class="px-0">
								<div class="px-10 bsapp-menu-item">
									<div  class="d-flex    list-group-item <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('Chat.php'))) echo "active"; ?> p-0 my-5 mx-auto" >
										<div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
											<div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-comments"></i></div>
										</div>
										<div class="d-flex align-items-center w-100 ">
											<div id="js-chatNotification-sideBar" class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>>
												<?= lang('menu_customer_chat') ?>
                                                <?php if ($ChatCountNew > 0) { ?>
													<span class="<?= $ChatCountNew < 10 ? 'px-5' : 'px-10' ?> bsapp-notification-badge animated animate__bounce"><?php echo $ChatCountNew > 99 ? '99+' : $ChatCountNew; ?></span>
                                                <?php } ?>
											</div>
										</div>
										<span class="bsapp-active-bg-icon"><i class="fal fa-comments"></i></span>
										<a class="stretched-link" title="<?= lang('menu_customer_chat') ?>" href="/office/Chat.php"></a>
									</div>
								</div>
                                <div class="px-10 bsapp-menu-item">
                                    <div  class="d-flex    list-group-item <?php echo (in_array(basename($_SERVER['SCRIPT_NAME']), array('insurance.php'))) ? "active" : '' ?> p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-trophy-alt"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?= lang('suggestion_for_you') ?> <label class="badge badge-white text-primary bsapp-fs-14 bsapp-lh-17 p-0 mis-10 bsapp-badge-insurance-new" ><?= lang('new') ?></label> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-trophy-alt"></i></span>
                                        <a class="stretched-link" title="<?= lang('suggestion_for_you') ?>" href="/office/insurance.php"></a>
                                    </div>
                                </div>
                                <div class="px-10 bsapp-menu-item">
                                    <div  class="d-flex    list-group-item <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('Store.php'))) echo "active"; ?> p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-store"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?php echo lang('items_management'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-store"></i></span>
                                        <a class="stretched-link" title="<?php echo lang('items_management'); ?>" href="/office/Store.php"></a>  
                                    </div>
                                </div>
                                <div class="px-10 bsapp-menu-item" id="menuReports">
                                    <div  class="d-flex    list-group-item <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), $reportsPaths) || basename(dirname($_SERVER['REQUEST_URI'])) == 'Reports') echo "active"; ?> p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-chart-line"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?php echo lang('reports'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-chart-line"></i></span> 
                                        <a class="stretched-link" title="<?php echo lang('reports'); ?>" href="/office/ReportsDash.php"></a>
                                    </div>
                                </div>
                                <!--div class="px-10 bsapp-menu-item">
                                    <div  class="d-flex    list-group-item <?php //if ($_SERVER['PHP_SELF'] == '/office/landings/index.php' || basename($_SERVER['PHP_SELF']) == 'landings') echo "active"; ?> p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fab fa-elementor"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php //echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php //echo $js_sidebar_sub_display; ?>> <?php //echo lang('landing_pages'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fab fa-elementor"></i></span>
                                        <a class="stretched-link" title="<?php //echo lang('landing_pages'); ?>" href="/office/landings/"></a>
                                    </div>
                                </div-->

                                <!--todo-bp-909 (cart) remove-beta-->
                                <?php if((int)$CompanySettingsDash->beta !== 1 ) { ?>
                                    <div class="px-10 bsapp-menu-item" id="menuDocs">
                                        <div  class="d-flex    list-group-item <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('Docs.php', 'DocsList.php'))) echo "active"; ?> p-0 my-5 mx-auto" >
                                            <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                                <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-file-invoice"></i></div>
                                            </div>
                                            <div class="d-flex align-items-center w-100 ">
                                                <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?php echo lang('docs'); ?> </div>
                                            </div>
                                            <span class="bsapp-active-bg-icon"><i class="fal fa-file-invoice"></i></span>
                                            <a class="stretched-link" title="<?php echo lang('docs'); ?>" href="/office/Docs.php?Types=<?php echo $types ?>"></a>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="px-10 bsapp-menu-item">
                                    <div  class="d-flex    list-group-item <?php if ($landingPath || in_array(basename($_SERVER['SCRIPT_NAME']), $settingsPaths)) echo "active"; ?> p-0 my-5 mx-auto" >
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon   w-40p text-center"><i class="fal fa-cog"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility  <?php echo $js_sidebar_sub_class; ?> text-start   pis-20"  <?php echo $js_sidebar_sub_display; ?>> <?php echo lang('path_settings'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-cog"></i></span>
                                        <a class="stretched-link" title="<?php echo lang('path_settings'); ?>" href="/office/SettingsDashboard.php"></a>    
                                    </div>
                                </div>

                                <div class="px-10 bsapp-menu-item">
                                    <div class="<?= $useIntercom ? 'open-custom-intercom-launcher ' : ''?>d-flex cursor-pointer list-group-item p-0 mt-5 mb-10 mx-auto">
                                        <div class="d-flex  bsapp-min-h-40p align-items-center bsapp-menu-box ">
                                            <div class="bsapp-menu-icon w-40p text-center"><i class="fal fa-life-ring"></i></div>
                                        </div>
                                        <div class="d-flex align-items-center w-100 ">
                                            <div class="js-toggle-visibility bsapp-toggle-visibility <?= $js_sidebar_sub_class; ?> text-start pis-20" <?= $js_sidebar_sub_display; ?>> <?= lang('path_support'); ?> </div>
                                        </div>
                                        <span class="bsapp-active-bg-icon"><i class="fal fa-life-ring"></i></span>
                                        <a class="stretched-link" title="<?php echo lang('path_support'); ?>" <?= $useIntercom ? '' : 'target="_blank" href="https://wa.me/972548620333"'?>></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bsapp-main-section js-main-section <?php echo $js_bsapp_shrink; ?> ">
                    <header class="bsapp-horizontal-menu">
                        <nav class="navbar navbar-expand-lg navbar-light px-5 d-flex justify-content-between js-header-primary-menu bsapp-header-primary-menu position-fixed bsapp-z-99  w-100 <?php echo $js_bsapp_shrink; ?>">
                            <div class="d-flex px-10 align-items-center bsapp-main-header-icons">
                                <a class="js-md-menu-show  d-none d-md-flex text-decoration-none pie-15   <?php echo $js_is_active; ?>" href="javascript:;">
                                    <i class="far fa-bars"></i>
                                </a>  
                                <div class="">
                                    <form class="form-inline my-2 my-lg-0">
                                        <div class="bsapp-search-minified  js-search-minified ">
                                            <div class="input-group mb-3 input-group-sm bsapp-search-box" id="js-clientsSearch">
                                                <div class="input-group-prepend"> 
                                                    <a style="margin-top: 2px; padding-right: 1px;" href="javascript:;" class="input-group-text d-none d-md-flex text-decoration-none " ><i class="far color-fa-search fa-search"></i></a>
                                                    <a href="javascript:;" class="input-group-text d-md-none js-show-search" ><i class="far fa-search"></i></a>
                                                </div>
                                                <input style="border-radius: 8px !important;" type="text" class="form-control border-2 typeahead js-typeahead"  placeholder="<?php echo lang('search_client') ?>" >
                                                <div class="input-group-append">
                                                    <a href="javascript:;"  class="input-group-text js-hide-search"><i class="fas fa-times"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php
                            if($CompanySettingsDash->lockStatus == 1) {
                                include dirname(__FILE__, 3) . "/office/lockCompany/header_element.php";
                            }
                            else{
                            ?>
                            <div class="d-md-none">
                                <h4>Boostapp</h4>
                            </div>
                            <?php } ?>
                            <div class="bsapp-main-header-icons">
                                <div class="d-flex justify-content-end">

                                    <?php
                                    // chek if has open checkout order
                                    $OpenCheckoutOrder = CheckoutOrder::getOpenOrdersInCompany($CompanyNum);
                                    ///todo-bp-909 (cart) remove-beta
                                    if ($OpenCheckoutOrder !== null && (int)$CompanySettingsDash->beta === 1):
                                        $linkCart = LinkHelper::getPrefixUrlByHttpHost() .  '/office/cart.php?u=' .$OpenCheckoutOrder->ClientId;
                                        ?>
                                        <div class="px-10 bsapp-notification-content align-self-center">
                                            <div class="px-10 bsapp-notification-content align-self-center">
                                                <a class="btn badge-danger" id="openCheckOutOrder" target="_blank" href="<?=$linkCart?>" title="<?= lang('open_order') ?>" >
                                                    <?= lang('open_order') ?>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>


                                    <div class="dropdown px-10 d-none d-md-flex bsapp-min-w-125p"   style="font-size:14px;line-height: 14px;">
                                        <a class="dropdown-toggle text-decoration-none" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"  >
                                            <div class="d-flex align-items-start">
                                                <div class="pie-10">
                                                <img class="rounded-circle w-30p h-30p" style="border:1px solid #000" src="<?php echo (!empty(Auth::user()->UploadImage)) ? '/camera/uploads/large/' . Auth::user()->UploadImage : 'https://ui-avatars.com/api/?length=1&name=' . $userDetails->FirstName . '&background=f3f3f4&color=000&font-size=0.5' ?>"/>
				
                                                </div>
                                                <div class="mie-20 text-start">
                                                    <small id="user-displayName" class="bsapp-fs-14"> <?php echo Auth::user()->display_name; ?></small> <br> <small><?php echo transDbVal(trim($userRole->Title)); ?></small> 
                                                </div>
                                            </div>
                                        </a>
                                        <form class="dropdown-menu  bsapp-dropdown-menu py-10" style="font-size:14px;line-height: 14px;" >
                                            <div class="dropdown-item  text-start" >
                                                <a class="text-decoration-none" href="/office/MyProfile.php"><span class="bsapp-icon">
                                                        <i class="fal fa-user-edit"></i>
                                                        </span> <?php echo lang('my_profile') ?> </a></div>
                                            <div class="dropdown-item  text-start cursor-pointer" data-ip-modal="#UsersClock"><span class="bsapp-icon"><i class="fal fa-clock"></i> </span> <?php echo lang('time_clock') ?></div>
                                            <div class="dropdown-item  text-start" ><a data-toggle="collapse" class="text-decoration-none"  href="#js-locale-collapse" ><span class="bsapp-icon"><i class="fas fa-globe-americas"></i></span> <?php echo lang('language') ?> </a></div>
                                            <div class="collapse text-start" id="js-locale-collapse">
                                                <div class="py-6">
                                                    <?php foreach ($languages as $lang) { ?>
                                                            <a class="text-gray-400 pie-10 pis-30 py-5 d-block js-translation text-decoration-none" data-code="<?php echo $lang->lang_code?>" data-rtl="<?php echo $lang->direction?>" <?php echo ((!isset($_COOKIE['boostapp_lang']) && $lang->lang_code == "he") || (isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] == $lang->lang_code))  ? 'style="color: #00c736 !important"' : ''; ?>
                                                               href="javascript:;" id="trans-eng"><?php echo $lang->lang_code !== "eng" ? $lang->name : $lang->name.' <span class="btn-sm bg-light bsapp-beta-tag" style="background: '.(isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] != "eng" ?: "#00c73621 !important;").'"><span class="text-'.(isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] == "eng" ? "success" : "secondary").'">βeta</span></span>' ?></a>
                                                    <?php } ?>
                                                </div>
                                            </div>
											<div class="dropdown-item text-start">
                                            	<a class="text-decoration-none" href="/logout.php">
													<span class="bsapp-icon"><i class="fal fa-sign-out"></i> </span>
													<?php echo lang('logout') ?>
												</a>
											</div>
                                        </form>
                                    </div>
                                    <div class="px-10 bsapp-notification-content align-self-center border-md-istart" id="NotificationBtn" data-ip-modal="#no-open-modal">
                                        <a class=" text-gray-400 " href="#" tabindex="-1" aria-disabled="true" title="<?php echo lang('notifications') ?>" id="notification">
                                            <i class="fal fa-bell"></i>
                                            <?php if ($notifications > 0) { ?>
                                                <span id="Clicknotification" class="<?= $notifications < 10 ? 'px-5' : 'px-10' ?> bsapp-notification-badge animated animate__bounce"><?php echo $notifications > 99 ? '99+' : $notifications; ?></span>
                                            <?php } ?>
                                        </a>
                                    </div>
                                    <?php
                                    // meeting management icon should be displayed, only if there’s an open meeting or meeting in waiting status.
									if (MeetingService::hasOpenedOrWaitingMeetings($CompanyNum)):
										?>
										<div class="px-10 bsapp-notification-content align-self-center border-istart">
											<button id="openManageMeetingSidebar" class="text-gray-400 bsapp--manage-meeting--btn"  title="<?= lang('cal_appointments') ?>">
												<i class="fal fa-calendar-exclamation"></i>
                                                <span id='calendarIcon'  class="<?= $waitingCount < 10 ? 'px-5' : 'px-10' ?> <?= $waitingCount > 0 ? 'd-flex' : 'd-none' ?> bsapp-notification-badge animated animate__bounce"><?php echo $waitingCount > 99 ? '99+' : $waitingCount; ?></span>
											</button>
										</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </nav>
                        <div style="min-height: 48px;"></div>
                    </header>
                    <section class="bsapp-content">
                        <div class="container-fluid bg-light px-15">
                            <div class="row">
                                <div class="col-md-12 justify-content-start  bsapp-page"  >
                                    <!-- content goes here :: begin -->

                                    <?php if (!empty($supplier) && !empty($CompanySettingsDash)) { ?>
                                        <!-- intercom api -->
                                        <script>
                                            var companyName = String.raw`<?= $CompanySettingsDash->CompanyName ?>`;
                                            var phone = "<?php echo $supplier->ContactMobile; ?>";

                                            if (phone.substring(0, 1) == '0') {
                                                phone = "+972" + phone.substr(1);
                                            } else if (phone.substring(0, 4) == '+972') {
                                                phone = "<?php echo $supplier->ContactMobile; ?>";
                                            }

                                            window.intercomSettings = {
                                                app_id: "u62pvk7n",
                                                name: `<?php echo $supplier->display_name; ?>`, // Full name
                                                email: '<?php echo $supplier->email; ?>', // Email address
                                                phone: phone,
                                                company: {
                                                    id: "<?= $CompanyNum ?>",
                                                    name: companyName,
                                                    created_at: "<?= $supplier->joined ? strtotime($supplier->joined) : time() ?>",
                                                    // (optional): Insert name of the plan current company is on
                                                    // plan: "pro"
                                                },
                                                company_name: companyName,
                                                created_at: '<?= $supplier->joined ? strtotime($supplier->joined) : strtotime('now') ?>', // Signup date as a Unix timestamp
                                                company_number: <?= $CompanyNum ?>,
                                                custom_launcher_selector: '.open-custom-intercom-launcher',
                                                hide_default_launcher: true
                                            };
                                            window.addEventListener('load', (event) => {
                                                window.Intercom('onUnreadCountChange', count => {
                                                    if (count != 0) {
                                                        $('.custom-intercom-launcher span').addClass('active').text(count);
                                                    } else {
                                                        $('.custom-intercom-launcher span').removeClass('active').text('');
                                                    }
                                                });
                                            });
                                        </script>
                                    <?php } ?>


                                    <script>(function () {
                                            var w = window;
                                            var ic = w.Intercom;
                                            if (typeof ic === "function") {
                                                ic('reattach_activator');
                                                ic('update', w.intercomSettings);
                                            } else {
                                                var d = document;
                                                var i = function () {
                                                    i.c(arguments);
                                                };
                                                i.q = [];
                                                i.c = function (args) {
                                                    i.q.push(args);
                                                };
                                                w.Intercom = i;
                                                var l = function () {
                                                    var s = d.createElement('script');
                                                    s.type = 'text/javascript';
                                                    s.async = true;
                                                    s.src = 'https://widget.intercom.io/widget/u62pvk7n';
                                                    var x = d.getElementsByTagName('script')[0];
                                                    x.parentNode.insertBefore(s, x);
                                                };
                                                if (w.attachEvent) {
                                                    w.attachEvent('onload', l);
                                                } else {
                                                    w.addEventListener('load', l, false);
                                                }
                                            }
                                        })();
                                        Intercom('update', {
                                            "hide_default_launcher": true
                                        });
                                    </script>
                                    <script>
                                        function isWebview(isIosSystem = false) {
                                            const standalone = window.navigator.standalone,
                                                userAgent = window.navigator.userAgent.toLowerCase(),
                                                safari = /safari/.test(userAgent),
                                                ios = /iphone|ipod|ipad/.test(userAgent);

                                            if (isIosSystem) {
                                                return ios;
                                            }

                                            // isApplication for Android/iOS webview
                                            return ios && !standalone && !safari || !ios && userAgent.includes('wv');
                                        }

                                        function isIos() {
                                            const userAgent = window.navigator.userAgent.toLowerCase();

                                            return /iphone|ipod|ipad/.test(userAgent);
                                        }

										if (isWebview()) {
											// show page preloader
											$("#preloader").removeClass('hide-preloader');
										}

                                        $(document).ready(function () {
                                            if (isWebview()) {
												// hide preloader
												// setTimeout(function () {
													$("#preloader").addClass('hide-preloader');
												// }, 250);

                                                // hide landings
                                                $('a[href="/office/landings/"]').hide();

                                                // hide reports
                                                $('a[href="/office/ReportsDash.php"]').removeClass('d-flex').addClass('d-none');
                                                $('#menuReports').hide();

                                                // hide documents
                                                $('a[href="/office/Docs.php?Types=<?= $types ?>"]').removeClass('d-flex').addClass('d-none');
                                                $('#menuDocs').hide();

                                                if (isIos()) {
                                                    $('#bottomMenuContainer').addClass('pb-8');
                                                }

												$("body").on("click", "a[href]", function (e) {
													const el = $(this);
													const href = el.attr("href");
													if (href.indexOf("#") === -1 && href !== "javascript:void(0)" && href !== "javascript:;" && href){
														e.preventDefault();
														// $('#preloader').attr('style', 'opacity: 0.75');
														$('#preloader').addClass('semi-transparent');
														setTimeout(function(){ window.location.href = href; }, 200);
													}
												});
                                            }

                                            $.cookie('screen_width', $(window).width(), { path: '/' });
                                        });
                                    </script>

                                    <?php
                                    }
                                    ?>
