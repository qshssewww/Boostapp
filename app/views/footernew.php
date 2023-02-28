<?php
$CompanyNum = $CompanyNum ?? Auth::user()->CompanyNum;
$useIntercom = true;    // false - use WhatsApp
$CompanySettingsDash = $CompanySettingsDash ?? DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

require_once __DIR__ . '/../helpers/MultiUserHelper.php';
require_once __DIR__ . '/../../office/partials-views/char-popup/char-popup-modal.php';

$studiosList = MultiUserHelper::getList(Auth::user());
?>
<!-- content goes here :: end -->
</div>
</div>
</div>
</section>
</div>
</div>
<!-- desktop menu :: end -->
<!-- mobile menu :: begin 2 -->
<div id="bottomMenuContainer" class="d-block d-md-none position-fixed bg-dark w-100" style="bottom:0; padding-bottom: env(safe-area-inset-bottom);z-index:1;">
    <div class="d-flex py-8" >
        <div class="flex-fill d-flex justify-content-center">
            <a class="js-slide-menu-show text-white mx-auto text-center <?php if ($_SERVER['PHP_SELF'] == '/office/index.php' || basename($_SERVER['PHP_SELF']) == 'office') echo 'active'; ?>" href="/office/">
                <i class="fal fa-tachometer-alt"></i>
                <div><?php echo lang('path_main') ?></div>
            </a>
        </div>
        <div class="flex-fill d-flex justify-content-center">
            <a class="js-slide-menu-show text-white mx-auto text-center <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Cal.php') !== false || strpos(basename($_SERVER['REQUEST_URI']), 'TaskReport.php') !== false) echo "active"; ?>" href="/office/Cal.php">
                <i class="fal fa-tasks"></i>
                <div><?php echo lang('tasks') ?></div>
            </a>
        </div>
        <div class="flex-fill d-flex justify-content-center">
            <a class="js-slide-menu-show text-white mx-auto text-center <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'DeskPlanNew.php') !== false) echo "active"; ?>" href="/office/DeskPlanNew.php">
                <i class="fal fa-calendar-day"></i>
                <div><?php echo lang('sidebar_calendar') ?></div>
            </a>
        </div>
        <div class="flex-fill d-flex justify-content-center">
            <a class="js-slide-menu-show text-white mx-auto text-center <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Client.php') !== false || strpos(basename($_SERVER['REQUEST_URI']), 'ClientProfile.php') !== false) echo "active"; ?>" href="/office/Client.php?Act=0">
                <i class="fal fa-user-alt"></i>
                <div><?php echo lang('clients') ?></div>
            </a>
        </div>
        <div class="flex-fill d-flex justify-content-center">
            <a class="js-sm-menu-show text-white mx-auto text-center"   href="javascript:;">
                <i class="far fa-bars"> </i>
                <div><?php echo lang('menu_chat') ?></div>
            </a>
        </div>
    </div>
</div>
<div class="js-sm-menu bsapp-sm-menu overflow-auto">
    <div class="js-sm-menu-scroll  d-flex justify-content-between flex-column h-min-100">
        <div class="list-group list-group-flush px-0 flex-shrink-0">
            <div  class="list-group-item list-group-item-action d-flex position-relative justify-content-between bg-dark text-white bsapp-menu-item ">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fas fa-bold"></i> </div>
                    <div> Boostapp</div>
                </div>
                <div>
                    <a  href="#" class="text-white js-close-sm-menu">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
            <div   class="list-group-item list-group-item-action d-flex position-relative justify-content-between  bg-light text-dark bsapp-menu-item ">
                <div class="d-flex">
                    <div class="w-25p text-start mie-10"> <img src="<?php echo (!empty($appSettings->logoImg)) ? '/office/files/logo/' . $appSettings->logoImg : '/office/files/logo/smallDefault.png' ?>" class="w-25p rounded-circle"> </div>
                    <div><?php echo $CompanySettingsDash->AppName; ?> </div>
                </div>
                <div>
                    <?php if (Auth::user()->role_id == 1 || count($studiosList) > 1) { ?>
                        <a  class="text-dark stretched-link"  data-toggle="collapse" href="#js-collapse-sm-menu">
                            <i class="fas fa-angle-down"></i>
                        </a>
                    <?php } else { ?>
                        <a class="text-dark stretched-link"  href="javascript:;"></a>
                    <?php } ?>
                </div>
            </div>
            <div class="position-relative text-start bsapp-sub-menu">
                <div class="collapse position-absolute w-100 text-dark vh-100" id="js-collapse-sm-menu" style="z-index:9;">
                    <div class="card bg-light h-min-100">
                        <div>
                            <div class="list-group list-group-flush px-0">
                                <div class="list-group-item">
                                    <div class="d-flex">
                                        <div class="input-group mb-3 input-group-sm  bsapp-search-box ">
                                            <?php if (Auth::user()->role_id == 1) { ?>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="far fa-search"></i></span>
                                            </div>
                                            <input type="text" class="form-control typeahead js-sm-studio-search" placeholder="<?php echo lang('search_studio_footer') ?>"  >
                                            <?php } ?>
                                        </div>
                                        <a href="javascript:;" class="js-close-collapse pis-15" data-attr="js-sm-studio-searches"  data-search="js-sm-studio-search"><i class="fas fa-times"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group list-group-flush px-0 js-sm-studio-searches overflow-auto">
                                <?php
                                if (!empty($studiosList) && Auth::user()->role_id != 1) {
                                    foreach ($studiosList as $studioItem) { ?>
                                        <a href="javascript:;" data-id="<?php echo $studioItem['id'] ?>" class="<?= $studioItem['type'] === 'brand' ? 'js-switch-brand' : 'js-switch-multiuser' ?> list-group-item d-flex align-items-center py-10 px-10">
                                            <img class="w-30p h-30p rounded-circle mie-8" src="<?php echo (!empty($studioItem['logo'])) ? '/office/files/logo/' . $studioItem['logo'] : '/office/files/logo/smallDefault.png' ?>"/>
                                            <div class="d-flex flex-column <?php echo $studioItem['isCurrentStudio'] ? 'text-primary' : '' ?>">
                                                <span><?php echo $studioItem['name']; ?></span>
                                                <span><?php echo $studioItem['CompanyNum']; ?></span>
                                            </div>
                                        </a>
                                <?php }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item <?php echo (in_array(basename($_SERVER['SCRIPT_NAME']), array('Leads.php', 'manage-leads.php', 'LeadsJoinReport.php'))) ? "active" : ""; ?>" href="/office/manage-leads.php">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fal fa-users-class"></i> </div>
                    <div> <?php echo lang('manage_interested') ?></div>
                </div>
                <div>
                    <i class="fal fa-angle-right"></i>
                </div>
            </a>
            <!--a class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item <?php //if (strpos(basename($_SERVER['REQUEST_URI']), 'onlineLibrary.php') !== false) echo "active"; ?>" href="/office/onlineLibrary.php">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fal fa-video"></i> </div>
                    <div> <?php //echo lang('vod_library') ?></div>
                </div>
                <div>
                    <i class="fal fa-angle-right"></i>
                </div>
            </a>-->
            <a href="/office/Store.php" class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('Store.php'))) echo "active"; ?>">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fal fa-store"></i> </div>
                    <div> <?php echo lang('items_management') ?></div>
                </div>
                <div>
                    <i class="fal fa-angle-right"></i>
                </div>
            </a>
            <!--a href="/office/landings/" class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item <?php //if ($_SERVER['PHP_SELF'] == '/office/landings/index.php' || basename($_SERVER['PHP_SELF']) == 'landings') echo "active"; ?>">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fab fa-elementor"></i> </div>
                    <div> <?php //echo lang('landing_pages') ?></div>
                </div>
                <div>
                    <i class="fal fa-angle-right"></i>
                </div>
            </a-->
            <a  href="/office/ReportsDash.php"  class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), $reportsPaths) || basename(dirname($_SERVER['REQUEST_URI'])) == 'Reports') echo "active"; ?>">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fal fa-chart-line"></i> </div>
                    <div> <?php echo lang('reports') ?></div>
                </div>
                <div>
                    <i class="fal fa-angle-right"></i>
                </div>
            </a>
            <!--todo-bp-909 (cart) remove-beta-->
            <?php if((int)$CompanySettingsDash->beta !== 1 ) { ?>
                <a href="/office/Docs.php?Types=<?php echo $types; ?>" class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('Docs.php', 'DocsList.php'))) echo "active"; ?>">
                    <div class="d-flex">
                        <div class="w-20p text-start mie-10"> <i class="fal fa-file-invoice"></i> </div>
                        <div> <?php echo lang('docs') ?></div>
                    </div>
                    <div>
                        <i class="fal fa-angle-right"></i>
                    </div>
                </a>
            <?php }?>
            <a href="/office/SettingsDashboard.php" class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item <?php echo (in_array(basename($_SERVER['SCRIPT_NAME']), $settingsPaths)) ? "active" : ''; ?>">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fal fa-cog"></i> </div>
                    <div> <?php echo lang('path_settings') ?></div>
                </div>
                <div>
                    <i class="fal fa-angle-right"></i>
                </div>
            </a>
            <a href="/office/Chat.php" class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item <?php if (in_array(basename($_SERVER['SCRIPT_NAME']), array('Chat.php'))) echo "active"; ?>">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fal fa-comments"></i> </div>
                    <div> <?= lang('menu_customer_chat') ?></div>
                </div>
            </a>
            <a href="/office/insurance.php" class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item <?php echo (in_array(basename($_SERVER['SCRIPT_NAME']), array('insurance.php'))) ? "active" : '' ?>">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fal fa-trophy-alt"></i> </div>
                    <div> <?= lang('suggestion_for_you') ?>  <label class="badge badge-primary badge-pill"><?= lang('new') ?></label></div>
                </div>
            </a>

            <?php
            //todo-bp-909 (cart) remove-beta
            $CompanySettingsDash = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();
            if (in_array($CompanySettingsDash->beta, [1])) { ?>
            <a href="/office/cart.php" class="list-group-item list-group-item-action d-flex position-relative justify-content-between text-gray-400 bsapp-menu-item">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"> <i class="fal fa-cart-plus"></i> </div>
                    <div> <?= lang('cart_title') ?>  <label class="badge badge-primary badge-pill"><?= lang('new') ?></label></div>
                </div>
            </a>
            <?php }?>

        </div>
        <div class="flex-shrink-0">
            <div   class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item ">
                <div class="d-flex">
                    <div class="w-25p text-start mie-10"> <img class="rounded-circle w-25p h-25p" src="<?php echo (!empty(Auth::user()->UploadImage)) ? '/camera/uploads/large/' . Auth::user()->UploadImage : 'https://ui-avatars.com/api/?name=' . $userDetails->LastName . '+' . $userDetails->FirstName . '&background=' . hexcode(Auth::user()->display_name) . '&color=ffffff&font-size=0.5'; ?>"/> </div>
                    <div class="support"> <?php echo Auth::user()->display_name ?> </div>
                </div>
                <div>
                    <span   class="text-gray-400 ">
                        <i class="fal fa-angle-down"></i>
                    </span>
                    <a href="javascript:;" data-toggle="collapse"  data-target="#js-user-drop" class="stretched-link"></a>
                </div>
            </div>
            <div class="collapse" id="js-user-drop">
                <div class="text-start list-group-item list-group-item-action  position-relative text-gray-400 bsapp-menu-item " >
                    <div class="d-flex support <?php echo (in_array(basename($_SERVER['SCRIPT_NAME']), array('MyProfile.php'))) ? "active" : ''; ?>">
                        <div class="w-20p text-start mie-10"> <i class="fal fa-user-edit"></i> </div>
                        <div>
                            <?php echo lang('my_profile') ?>
                            <a href="/office/MyProfile.php"  class="stretched-link"></a>
                        </div>
                    </div>
                </div>
                <div class="text-start d-flex list-group-item list-group-item-action  position-relative  text-gray-400  bsapp-menu-item"  >
                    <div class="w-20p text-start mie-10"> <i class="fal fa-clock"></i> </div>
                    <div><?php echo lang('time_clock') ?>   </div>
                    <a  href="javascript:;" class="stretched-link" data-ip-modal="#UsersClock"></a>
                </div>
                <div class="text-start list-group-item list-group-item-action d-flex position-relative  text-gray-400  bsapp-menu-item " >
                    <div class="w-20p text-start mie-10"> <i class="fas fa-globe-americas"></i> </div>
                    <div><?php echo lang('language') ?>
                        <a data-toggle="collapse" href="javascript:;" data-target="#js-locale-collapse" class="stretched-link"   ></a>
                    </div>

                </div>
                <div class="collapse text-start " id="js-locale-collapse">
                    <?php foreach ($languages as $lang) { ?>
                        <a class="text-gray-400 pie-10 pis-30 py-6 d-block js-translation" data-code="<?php echo $lang->lang_code ?>" data-rtl="<?php echo $lang->direction ?>" <?php echo ((!isset($_COOKIE['boostapp_lang']) && $lang->lang_code == "he") || (isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] == $lang->lang_code)) ? 'style="color: #00c736 !important"' : ''; ?>
                           href="javascript:;" id="trans-eng"><?php echo $lang->lang_code !== "eng" ? $lang->name : $lang->name.' <span class="btn-sm bg-light bsapp-beta-tag" style="background: '.($_COOKIE['boostapp_lang'] != "eng" ?: "#00c73621 !important;").'"><span class="text-'.($_COOKIE['boostapp_lang'] == "eng" ? "success" : "secondary").'">βeta</span></span>' ?></a>
                    <?php } ?>
                </div>
            </div>
<!--            <div class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item">-->
<!--                <div class="d-flex">-->
<!--                    <a class="d-flex text-gray-400">-->
<!--                        <div class="w-20p text-start mie-10">-->
<!--                            <lottie-player src="/office/js/lf30_editor_cyKw43.json"  background="transparent"  speed="0.75"  style="width: 20px; height: 20px;"  loop autoplay></lottie-player>-->
<!--                        </div>-->
<!--                        <div> --><?php //echo lang('guide_header') ?><!--</div>-->
<!--                    </a>-->
<!--                </div>-->
<!--            </div>-->
            <a <?= $useIntercom ? '' : 'target="_blank" href="https://wa.me/972548620333"'?>
               class="<?= $useIntercom ? 'open-custom-intercom-launcher ' : ''?>list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item">
                <div class="d-flex">
                    <div class="w-20p text-start mie-10"><i class="fal fa-life-ring"></i></div>
                    <div> <?php echo lang('path_support') ?></div>
                </div>
            </a>
            <div   class="list-group-item list-group-item-action d-flex position-relative justify-content-between  text-gray-400 bsapp-menu-item ">
                <div class="d-flex">
                    <a class="d-flex text-gray-400" href="/logout.php">
                        <div class="w-20p text-start mie-10"> <i class="fal fa-sign-out-alt"></i> </div>
                        <div> <?php echo lang('logout') ?></div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="js-sm-search-screen bsapp-sm-search-screen   d-flex  flex-column">
    <div class="js-search-scroll">
        <div class="list-group list-group-flush px-0 bg-light ">
            <div class="list-group-item">
                <div class="d-flex">
                    <div class="input-group mb-3 input-group-sm  bsapp-search-box ">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="far fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control typeahead js-sm-typeahead-client" placeholder="חפש לקוח"  >
                    </div>
                    <a href="javascript:;" class="js-hide-search text-dark pis-15 align-self-center" data-attr="js-sm-client-searches" data-search="js-sm-typeahead-client"><i class="fas fa-times"></i></a>
                </div>
            </div>
        </div>
        <div class="list-group list-group-flush  text-start  js-sm-client-searches">
        </div>
    </div>
</div>
<!-- mobile menu :: end -->

<?php
if (((isset($_REQUEST['debugmode']) && $_REQUEST['debugmode'] == 1) || \App\Utils\DebugBar::isEnabled()) && Auth::user()->role_id == 1) {
    echo View::make('debugbar')->render();
}
?>



<!-- </div>
        </div>
<div class="footer" >
        <div class="container">

                <p>כל הזכויות שמורות &copy; <?php //echo date('Y', time()) .' :: '. Config::get('app.name');  ?></p>
        </div>
</div> -->

<?php echo View::make('modals.load')->render() ?>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<?php if (Auth::check()): ?>

    <div id="ReminderCheck"></div>
    <!-- Notification Modal -->
<?php if (Auth::userCan('92')): ?>
    <div class="ip-modal px-0 px-sm-auto bsapp-char-popup text-gray-700 js-modal-no-close overflow-auto" id="no-open-modal">
        <div class="modal-dialog modal-lg modal-dialog-centered bsapp-max-w-420p">
            <div class="modal-content h-100 overflow-auto">
                <div class="d-flex flex-column border-light border">
                    <div class="d-flex justify-content-between w-100">
                        <h4 class="d-flex p-15 align-items-center text-black" id="DetailsTitle"><?php echo lang('notifications') ?></h4>
                        <a class="ip-close text-dark bsapp-fs-26 p-15 cursor-pointer" title="Close"  data-dismiss="modal">
                            <i class="fal fa-times"></i>
                        </a>
                    </div>


                </div>

                <div class="modal-body d-flex flex-column justify-content-between p-0" style="height:calc( 100% - 120px );">
                    <?php
                    $UserId = Auth::user()->id;
                    $Today = date('Y-m-d');
                    $TodayTime = date('H:i:s');
                    ?>

                    <div id="NotificationPOP" class="text-start" style="overflow-y: scroll; overflow-x:hidden; padding:10px;">
                        <div id="Newnotifications" class="notif-container">
                        </div>
                    </div>

                </div>
                <div class="ip-modal-footer text-start bg-gray-300 d-flex justify-content-between  align-items-center w-100 h-60p bsapp-z-1">
                    <div class="ip-actions">
                        <a class="btn btn-light readall" data-dismiss="modal"><?php echo lang('mark_read_footernew') ?></a>
                    </div>

                    <a href="LogNotification.php" class="btn btn-success text-white ip-close" data-dismiss="modal"><?php echo lang('notifications_report_footernew') ?></a>


                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
    <!-- end Notification Modal -->











    <!-- מודל בחירת פריט -->
    <div class="ip-modal" id="ChooseItem">
        <div class="ip-modal-dialog">
            <div class="ip-modal-content text-start">
                <div class="ip-modal-header  d-flex justify-content-between">
                    <h4 class="ip-modal-title"><?php echo lang('branch_select_footernew') ?></h4>
                    <a class="ip-close" title="Close"  data-dismiss="modal">&times;</a>
                </div>
                <div class="ip-modal-body">
                    <select style="margin-right: 0;padding-right: 0;" name="ProductSearchTop" id="ProductSearchTop" class="form-control select2 ProductSearchTop"></select>
                </div>
                <div class="ip-modal-footer text-start">
                    <a class="btn btn-light ip-close" data-dismiss="modal"><?php echo lang('close') ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- מודל בחירת פריט -->
    <?php
    $ItemDetailsHeaderFooer = DB::table('settings')->where('CompanyNum', '=', Auth::user()->JumpBrandsId)->where('Status', '=', '0')->first();
    ?>

    <!-- מודל בחירת פריט -->
    <div class="ip-modal" id="ChooseItemJump">
        <div class="ip-modal-dialog" >
            <div class="ip-modal-content text-start">
                <div class="ip-modal-header  d-flex justify-content-between">
                    <h4 class="ip-modal-title"><?php echo lang('branch_select_footernew') ?></h4>
                    <a class="ip-close" title="Close"  data-dismiss="modal">&times;</a>
                </div>
                <div class="ip-modal-body">
                    <select style="margin-right: 0;padding-right: 0;" name="ProductSearchTopJump" id="ProductSearchTopJump" class="form-control">
                        <option value="0"><?php echo lang('choose') ?></option>
                        <option value="<?php echo @Auth::user()->JumpBrandsId; ?>"><?php echo @$ItemDetailsHeaderFooer->AppName; ?></option>
                    </select>
                </div>
                <div class="ip-modal-footer text-start">
                    <a class="btn btn-light ip-close" data-dismiss="modal"><?php echo lang('close') ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- מודל בחירת פריט -->


    <?php if (Auth::user()->role_id == '1') { ?>

        <!-- מודל בחירת חברה -->
        <div class="ip-modal" id="ChooseCompanyNum">
            <div class="ip-modal-dialog" <?php //### _e('main.rtl')  ?>>

                <div class="ip-modal-content text-start">
                    <div class="ip-modal-header  d-flex justify-content-between"  <?php //### _e('main.rtl')  ?>>
                        <h4 class="ip-modal-title"><?php echo lang('company_change_footernew') ?></h4>
                        <a class="ip-close" title="Close"  data-dismiss="modal">&times;</a>
                    </div>
                    <div class="ip-modal-body">

                        <form action="SupportChangeCompanyNum"  class="ajax-form clearfix"   autocomplete="off">
                            <div class="form-group">
                                <label><strong><?php echo lang('select_company_footernew') ?></strong></label>
                                <select class="CompanyNumSelect" name="CompanyNum" id="CompanyNumSelect"></select>
                            </div>
                    </div>


                    <div class="ip-modal-footer text-start">
                        <div class="ip-actions">
                            <button type="submit" class="btn btn-success text white"><?php echo lang('update') ?></button>
                        </div>
                        </form>
                        <a class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php echo lang('close') ?></a>

                    </div>


                </div>

            </div>
        </div>
        <!-- מודל בחירת חברה -->


        <script>
            $(".CompanyNumSelect").on("select2:unselect", function (e) {
                $(".ItemCompanySelect").select2("val", "");
            });
            $('.CompanyNumSelect').select2({
                theme: "bootstrap",
                placeholder: "<?php echo lang('select_company_footernew') ?>",
                language: "he",
                allowClear: true,
                width: '100%',
                ajax: {
                    url: '/office/action/CompanyNumSelect.php',
                    dataType: 'json'
                },
            });
        </script>





    <?php } ?>

    <!-- מודל לקוח חדש -->
    <div class="modal fade px-0 px-sm-auto js-modal-no-close text-gray-700 text-start overflow-hidden" tabindex="-1" id="js-client-popup" role="dialog" data-backdrop="static" >
        <div class="modal-dialog modal-md modal-dialog-centered bsapp-max-w-420p">
            <div class="modal-content h-100 rounded">
                <div class="modal-body bsapp-min-h-775p">

                </div>
            </div>
        </div>
    </div>

    <!--        TODO remove after beta - BS-1823         -->
    <!-- new task container popup -->
    <?php if (in_array($CompanySettingsDash->beta, [1, 2])) : ?>
        <div class="modal fade px-0 px-sm-auto js-modal-no-close text-gray-700 text-start overflow-hidden" tabindex="-1"
             id="js-task-popup" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-md modal-dialog-centered bsapp-max-w-420p">
                <div class="modal-content h-100 rounded">
                    <div class="modal-body bsapp-min-h-775p">

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- block event popup -->
    <div class="modal fade px-0 px-sm-auto js-modal-no-close text-gray-700 text-start overflow-hidden" tabindex="-1" id="js-block-event-popup" role="dialog" data-backdrop="static" >
        <div class="modal-dialog modal-md modal-dialog-centered bsapp-max-w-300p">
            <div class="modal-content h-100 rounded">
                <div class="modal-body bsapp-min-h-475p">

                </div>
            </div>
        </div>
    </div>


    <!-- מודל חסימת חברה -->
    <?php
    if($CompanySettingsDash->lockStatus == 1) {
        include_once dirname(__FILE__, 3)."/office/lockCompany/lock_company.php";
    }
    ?>
    <!-- מודל חסימת חברה -->


    <!-- מודל שעון נוכחות -->
    <div class="ip-modal" id="UsersClock">
        <div class="ip-modal-dialog">

            <div class="ip-modal-content text-start">
                <div class="ip-modal-header  d-flex justify-content-between" >
                    <h4 class="ip-modal-title"><?php echo lang('time_clock') ?></h4>
                    <a class="ip-close" title="Close" data-dismiss="modal">&times;</a>
                </div>
                <div class="ip-modal-body">
                    <div align="center" style=" font-size:24px; font-weight:bold;">
                        <span id="date_times"></span>
                    </div>
                    <script type="text/javascript">window.onload = date_time('date_times');</script>


                    <form action="AddUsersClock"  class="ajax-form clearfix"  autocomplete="off">
                        <input type="hidden" name="UserId" value="BA999">
                        <div align="center" style=" padding:15px;">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons" dir="ltr">
                                <label class="btn btn-info btn-lg">
                                    <input type="radio" name="options" id="option2" value="1" autocomplete="off"> <?php echo lang('exit_action') ?>
                                </label>
                                <label class="btn btn-primary btn-lg active">
                                    <input type="radio" name="options" id="option1" value="0" autocomplete="off"  checked> <?php echo lang('entrance') ?>
                                </label>
                            </div>
                        </div>

                </div>


                <div class="ip-modal-footer d-flex justify-content-between">
                    <a class="btn btn-light ip-close" data-dismiss="modal"><?php echo lang('close') ?></a>
                    <div class="ip-actions">
                        <button type="submit" class="btn btn-success text white"><?php echo lang('save') ?></button>
                    </div>
                    </form>

                </div>


            </div>

        </div>
    </div>
    <!-- מודל שעון נוכחות -->



    <!-- מודל שיעור חדש -->
    <div class="ip-modal text-start" role="dialog" id="AddNewClass" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="ip-modal-dialog BigDialog">
            <div class="ip-modal-content">
                <div class="ip-modal-header  d-flex justify-content-between">
                    <h4 class="ip-modal-title"><?php echo lang('adding_class_footernew') ?></h4>
                    <a class="ip-close ClassClosePopUp" title="Close" style="" data-dismiss="modal" aria-label="Close">&times;</a>

                </div>
                <div class="ip-modal-body">
                    <form action="AddClassNewPopUp" id="AddClassNewPop" class="ajax-form needs-validation" novalidate autocomplete="off">
                        <div id="ResultAddNewClass"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- מודל שיעור חדש -->




    <!-- מודל שיעור אישי חדש -->
    <div class="ip-modal text-start" role="dialog" id="AddsNewClass" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="ip-modal-dialog BigDialog">
            <div class="ip-modal-content">
                <div class="ip-modal-header  d-flex justify-content-between">
                    <h4 class="ip-modal-title"><?php echo lang('add_personal_class') ?></h4>
                    <a class="ip-close ClassClosePopUp" title="Close" style="" data-dismiss="modal" aria-label="Close">&times;</a>

                </div>
                <div class="ip-modal-body">
                    <form action="AddsClassNewPopUp" id="AddsClassNewPop" class="ajax-form needs-validation" novalidate autocomplete="off">
                        <div id="ResultAddsNewClass"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- מודל שיעור אישי חדש -->

    <div class="ip-modal text-start show" role="dialog" id="RemoveClassesPopUp" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="ip-modal-dialog BigDialog" >
            <div class="ip-modal-content">
                <div class="ip-modal-header  d-flex justify-content-between"  >
                    <h4 class="ip-modal-title"><?php echo lang('remove_classes') ?> </h4>
                    <a class="ip-close ClassClosePopUp" title="Close" style="" data-dismiss="modal" aria-label="Close">×</a>

                </div>
                <div class="ip-modal-body">
                    <div class="alert alert-info" role="alert">
    <?php echo lang('canceled_class_note_footernew') ?>
                    </div>
                    <div class="row">

                        <div class="col-md-3 col-sm-12">
                            <label><?php echo lang('customer_card_start_date') ?></label>
                            <input id="startDate" type="date" class="form-control">

                        </div>
                        <div class="col-md-3 col-sm-12">
                            <label><?php echo lang('customer_card_end_date') ?></label>
                            <input id="endDate" type="date" class="form-control">

                        </div>
                    </div>
                    <div class="panel-body pt-15">
                        <!-- <div class="row">
                                <input id="rm-regular" type="checkbox" checked><span class="mr-2">רגיל</span>
                        </div> -->
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" checked id="rm-regular" name="example1">
                            <label class="custom-control-label" for="rm-regular"><?php echo lang('regular') ?></label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" checked id="rm-online" name="example1">
                            <label class="custom-control-label" for="rm-online"><?php echo lang('online_footernew') ?></label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" checked id="rm-zoom" name="example1">
                            <label class="custom-control-label" for="rm-zoom"><?php echo lang('zoom_footernew') ?></label>
                        </div>
                        <label id="actionMsg" style="display: none; margin-top: 10px"></label>
                        <div class="row justify-content-end">
                            <div id="RemoveBtn" class="btn btn-primary btn-block col-md-3" ><?php echo lang('a_remove_single') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- מודל שיעור חדש -->
    <div class="ip-modal text-start" role="dialog" id="EditNewClass" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="ip-modal-dialog BigDialog">
            <div class="ip-modal-content">
                <div class="ip-modal-header  d-flex justify-content-between">
                    <h4 class="ip-modal-title"><?php echo lang('edit_lesson') ?></h4>
                    <a class="ip-close ClassClosePopUp" title="Close" style="" data-dismiss="modal" aria-label="Close">&times;</a>

                </div>
                <div class="ip-modal-body">
                    <form action="AddClassNewPopUp" id="EditClassNewPop" class="ajax-form needs-validation" novalidate autocomplete="off">
                        <div id="ResultEditNewClass"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- מודל שיעור חדש -->


    <!-- מודל שיעור חדש -->
    <div class="ip-modal text-start" role="dialog" id="DuplicateNewClass" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="ip-modal-dialog BigDialog" >
            <div class="ip-modal-content">
                <div class="ip-modal-header  d-flex justify-content-between">
                    <h4 class="ip-modal-title"><?php echo lang('duplicate_lesson') ?></h4>
                    <a class="ip-close ClassClosePopUp" title="Close" style="" data-dismiss="modal" aria-label="Close">&times;</a>

                </div>
                <div class="ip-modal-body">
                    <form action="AddClassNewPopUp" id="DuplicateClassNewPop" class="ajax-form needs-validation" novalidate autocomplete="off">
                        <div id="ResultDuplicateNewClass"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- מודל שיעור חדש -->


    <!-- מודל שיעור חדש -->
    <div class="ip-modal text-start" role="dialog" id="ViewNewClass" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="ip-modal-dialog BigDialog">
            <div class="ip-modal-content">
                <div class="ip-modal-header  d-flex justify-content-between">
                    <h4 class="ip-modal-title"><?php echo lang('see_lesson_details') ?></h4>
                    <a class="ip-close ClassClosePopUp" title="Close" style="" data-dismiss="modal" aria-label="Close">&times;</a>

                </div>
                <div class="ip-modal-body">

                    <div id="ResultViewNewClass"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>

                </div>
            </div>
        </div>
    </div>
    <!-- מודל שיעור חדש -->


    <!-- מודל שיעור חדש -->
    <div class="ip-modal text-start" role="dialog" id="RunWatingListClass" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="ip-modal-dialog">
            <div class="ip-modal-content">
                <div class="ip-modal-header  d-flex justify-content-between">
                    <h4 class="ip-modal-title"><?php echo lang('run_waiting_list') ?></h4>
                    <a class="ip-close ClassClosePopUp" title="Close" style="" data-dismiss="modal" aria-label="Close">&times;</a>

                </div>
                <div class="ip-modal-body" >
                    <form class="clearfix text-start" autocomplete="off">
                        <input type="hidden" name="RunWatingListClassID" id="RunWatingListClassID" value="">

                        <div class="form-group" >
                            <label><?php echo lang('waitlist_footernew') ?></label>
                        </div>
                </div>

                <div class="ip-modal-footer text-start">

                    <button type="button" class="btn btn-success text-white ip-close ip-closePopUp ClientClosePOPNew" data-dismiss="modal"><?php echo lang('start_waitlist_footernew') ?></button>

                    <div class="ip-actions">
                        <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?php echo lang('no_close_both') ?></button>
                    </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <!-- מודל שיעור חדש -->


    <!-- מודל סרטון הדרכה  -->
    <div class="ip-modal text-start" role="dialog" id="GetVideo" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="ip-modal-dialog BigDialog" <?php //### _e('main.rtl')  ?>>
            <div class="ip-modal-content">
                <div class="ip-modal-header  d-flex justify-content-between"  <?php //### _e('main.rtl')  ?>>
                    <h4 class="ip-modal-title"><?php echo lang('guidance_video') ?></h4>
                    <a class="ip-close ClassClosePopUp" title="Close" style="" data-dismiss="modal" aria-label="Close">&times;</a>

                </div>
                <div class="ip-modal-body" style="height: 550px; padding: 0px;">

                    <div id="ResultGetVideo"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>

                </div>
            </div>
        </div>
    </div>
    <!-- מודל סרטון הדרכה -->





    <!-- מודל פעילות חדשה -->
    <div class="ip-modal text-start" role="dialog" id="AddNewCal" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="ip-modal-dialog BigDialog">
            <div class="ip-modal-content">
                <div class="ip-modal-header d-flex justify-content-between" >
                    <h4 class="ip-modal-title"><?php echo lang('task_window_title') ?></h4>
                    <a class="ip-close" title="Close" style="" data-dismiss="modal" aria-label="Close">&times;</a>

                </div>
                <div class="ip-modal-body">
                    <form action="AddCalendarClient" class="ajax-form text-start" autocomplete="off">
                        <div id="ResultAddNewCal"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- מודל פעילות חדשה -->
    <?php
    require_once __DIR__ . '/../../office/partials-views/phone-validation/phone-validation-popup.php';
    ?>

    <!-- מודל כתובת עסק -->
    <?php include_once 'modals/businessLocation.php'; ?>

    <!--2023 מודל עליית מחירי המנוי -->
<!--    --><?php //include_once 'modals/SubscribersPriceIncrease.php'; ?>

    <script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/office/dist/js/app.js?'.filemtime(__DIR__.'/../../office/dist/js/app.js') ?>"></script>

    <script>

        

        let notifications = {
            markAsRead: function (elem) {
                elem = $(elem);
                const Acts = elem.val();
                $(elem).closest('div.AlertCloseMe').fadeOut();
                $.ajax({
                    type: 'POST',
                    url: '/office/action/StatusChange.php',
                    data: { Act: Acts },
                    success: function (msg) {
                    }
                });
            }
        }

        $('.ClientClosePOPNew').click(function () {
            var ClassId = $('#RunWatingListClassID').val();
            $('#ClosePOPUP').removeClass("ClientCloseNew");
            $.ajax({
                type: 'POST',
                data: 'ClassId=' + ClassId,
                dataType: 'json',
                url: 'new/RunWatingList.php',
                success: function (data) {
                }
            });
        });

        $(function () {
            var time = function () {
                return'?' + new Date().getTime()
            };

            $('#no-open-modal').imgPicker({
            });
            $('#ChooseItem').imgPicker({
            });
            $('#ChooseItemJump').imgPicker({
            });
            $('#ChooseCompanyNum').imgPicker({
            });
            $('#UsersClock').imgPicker({
            });




        });


        $('.ClassClosePopUp').click(function () {
            location.hash = "";

            $('#ResultEditNewClass').find('.select2Desk').select2('destroy');
            $('#ResultEditNewClass').find('.select2Desk').off('select2:select');
            $('#ResultEditNewClass').find('.select2LimitLevel').select2('destroy');
            $('#ResultEditNewClass').find('.select2LimitLevel').off('select2:select');

            $('#ResultAddNewClass').html("");
            $('#ResultEditNewClass').html("");
            $('#RemoveClassesPopUp').hide();
        });

        $(document).ready(function () {

            $('#RemoveBtn').click(function () {
                var data = {
                    startDate: $("#startDate").val(),
                    endDate: $("#endDate").val(),
                    regular: ($("#rm-regular").is(':checked')) ? 1 : 0,
                    online: ($("#rm-online").is(':checked')) ? 1 : 0,
                    zoom: ($("#rm-zoom").is(':checked')) ? 1 : 0,
                    fun: "removeClasses"
                }
                if (data.startDate > data.endDate) {
                    let datesErr = '<?php echo lang('start_date_footernew') ?>';
                    $("#actionMsg").text(datesErr).show();
                    setTimeout(function () {
                        $("#actionMsg").fadeOut('slow');
                    }, 2000);

                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: '/office/ajax/ajaxCorona.php',
                    data: data,
                    success: function (res) {
                        let msg = "";
                        if (res == "1") {
                            msg = "<?php echo lang('classes_deleted_footernew') ?>";
                            $("#actionMsg").css("color", "#48ad3f");
                        } else {
                            msg = "<?php echo lang('no_class_found_footernew') ?>";
                        }
                        $("#actionMsg").text(msg).show();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        let msg = "<?php echo lang('action_failed_footernew') ?>";
                        $("#actionMsg").css("color", "#fd3131");
                        $("#actionMsg").text(msg).show();
                    }
                });


            })


    //Chat check for new message
            var ChatCheckNewMessagesVar;
            function ChatCheckNewMessages() {
                $.get("/office/action/ChatNewMessage.php", function (data) {

                    var curr = $('#ChatCountHeader span').text();
                    if (data >= 1 && data > curr) {
                        var formattedNumber = parseInt(data, 10);
                        if ($('#ChatCountHeader span').length) {
                            $('#ChatCountHeader span').remove();
                        }
                        if (formattedNumber > 99) {
                            formattedNumber = "99+";
                        }
                        const chatNotification = $('#js-chatNotification-sideBar');

                        if (chatNotification.find('.bsapp-notification-badge').html() != formattedNumber) {
                            chatNotification.html('<?= lang('menu_customer_chat') ?><span class="px-10 bsapp-notification-badge animated shake">' + formattedNumber + '</span>');
                        }

                        //There are new messages
                        //clearInterval(ChatCheckNewMessagesVar);

                        $.ajax({
                            url: '/office/action/ChatNewMessageContent.php',
                            dataType: 'json',

                            success: function (response) {


                                for (var i = 0; i < response.length; i++) {
                                    var obj = response[i];
                                    $.notify({
                                        icon: obj.photo,
                                        title: obj.name,
                                        message: obj.message,
                                    }, {
                                        type: 'minimalist',
                                        delay: 5000,
                                        icon_type: 'image',
                                        template: '<div data-notify="alert" class="col-xs-11 col-sm-3 text-start alert alert-{0}" role="alert" style="line-height: 15px;cursor: pointer;" onclick="location.href=\'/office/Chat.php?U=' + obj.id + '\'" >' +
                                                '<img data-notify="icon" class="rounded-circle float-right profileimage">' +
                                                '<span data-notify="title">{1}</span>' +
                                                '<div data-notify="message" style="height:27px;overflow: hidden;">{2}</div>' +
                                                '</div></a>'
                                    });
                                    $(".profileimage").attr('onerror', "this.src='/office/assets/img/21122016224223511960489675402.png'");

                                }

                            }
                        });

                    } else {
                        //$('#ChatCountHeader').html('');
                    }
                });
            }
            ChatCheckNewMessagesVar = setInterval(ChatCheckNewMessages, 60000);
            ChatCheckNewMessages();
    //END Chat check for new message

         if (meetingSidebarManage) { // interval for users that waiting to approve notification
            setInterval(() => {
                meetingSidebarManage.getUsersToApprove()
            }, 30000);

        }



            $("#ProductSearchTopJump").change(function () {

                var Id = this.value;
                if (Id != '0') {

                    $.ajax({
                        type: 'POST',
                        url: '/office/action/UpdateBrandJumpSelected.php?BrandId=' + Id,
                        success: function (msg) {
                            BN('0', '<?php echo lang('action_done') ?>');
                            location.reload()
                        },
                        error: function (xhr, status, error) {
                            BN('1', '<?php echo lang('update_branch_footernew') ?>');
                        }
                    });



                }

            });


            const auto_refresh = setInterval(
                function () {
                    $.ajax({
                        url: "/office/action/notification.php",
                        type: 'POST',
                        success: function (response) {

                            const curr = $('#Clicknotification').text();
                            if (response > 0 && response > curr) {
                                if ($('#Clicknotification').length) {
                                    $('#Clicknotification').remove();
                                }
                                if (response > 99) {
                                    response = "99+";
                                }
                                $('#notification').append('<span id="Clicknotification" class="px-10 bsapp-notification-badge animated shake">' + response + '</span>');
                            }
                        },
                        error: function () {

                        }
                    });

                }, 60000);


            $('.readall').click(function (e) {
                e.preventDefault();
                $('#Newnotifications').load('/office/action/AllRead.php?Act=0').fadeIn("fast");
                $("#notification span").remove();
            });

            let verified_mobile = '<?php echo Auth::user()->multiUserId != 0 ?>';
            if(!verified_mobile && !isMobileVerPopupSeen()) {
                $("#js-phone-valid-modal").modal("show");
            }

            function isMobileVerPopupSeen() {
                if (localStorage) {
                    let local_name = 'boostapp_mobile_verify';
                    //  get the localStorage variable.
                    let timerValue = localStorage.getItem(local_name);
                    if (!timerValue) {
                        timerValue = new Date().toString();
                        localStorage.setItem(local_name, timerValue);
                        return false;
                    }
                    //  parse string date and get difference between now - old time in milliseconds.
                    let diff = new Date().getTime() - new Date(timerValue).getTime();
                    //  compare difference and check if it matches 1 hour (1000ms * 60s * 60m)
                    if (diff >= (1000 * 60 * 60 * 4))   // 4 hours
                    {
                        //  reset timer immediately.
                        localStorage.setItem(local_name, new Date().toString());
                        return false;
                    }
                }
                return true;
            }
        });


        $('#NotificationBtn').click(function () {
            var medicalSaveBtn = function (i, el) {
                var el = jQuery(el);
                $('<a class="btn btn-danger text-white btn-block btn-sm medicalSaveBtn" style="margin-bottom: 0.3em">' + '<?php echo lang('add_to_medical_footernew') ?>' + '</a>').insertAfter(el.find('ul'));
                var clientId = el.attr('data-health-alerts');

                $('.medicalSaveBtn').off('click').on('click', function () {
                    var container = jQuery(this).closest('div[data-health-alerts]');
                    var clientId = container.attr('data-health-alerts');
                    var Content = container.find('ul li').toArray().map(function (x) {
                        return jQuery(x).text();
                    }).join('<br>');

                    $.post('/ajax.php', {'ClientId': clientId, 'Content': Content, 'action': 'AddClientMedical'}, function () {
                        var select = jQuery('div[data-health-alerts="' + clientId + '"]').closest('.AlertCloseMe').find('#StatusEventReminder');
                        select.val(select.find('option:last').val()).change();
                    })
                })
            }

            var formsSaveBtn = function (i, el) {
                var el = $(el);
                $('<a class="btn btn-info text-white btn-block btn-sm formsSaveBtn" style="margin-bottom: 0.3em">' + '<?php echo lang('add_to_documentation_footernew') ?>' + '</a>').insertAfter(el.find('ul'));
                var clientId = el.attr('data-forms-alerts');

                $('.formsSaveBtn').off('click').on('click', function () {
                    var container = $(this).closest('div[data-forms-alerts]');
                    var clientId = container.attr('data-forms-alerts');
                    var Content = container.find('ul li').toArray().map(function (x) {
                        return $(x).text();
                    }).join('<br>');

                    $.post('/ajax.php', {'ClientId': clientId, 'Remarks': Content, 'action': 'AddCRM'}, function () {
                        var select = jQuery('div[data-forms-alerts="' + clientId + '"]').closest('.AlertCloseMe').find('#StatusEventReminder');
                        select.val(select.find('option:last').val()).change();
                    })
                })
            }



            $(document).ready(function () {
                $('div[data-health-alerts]').each(medicalSaveBtn);
                $('div[data-forms-alerts]').each(formsSaveBtn);

            })

            $('#Newnotifications').empty();
            $('#Newnotifications').append($('<div>').load('/office/action/notifications.php', function () {

                $('div[data-health-alerts]').each(medicalSaveBtn);
                $('div[data-forms-alerts]').each(formsSaveBtn);


                $(".StatusEventNotification").change(function () {
                    var Acts = this.value;

                    $.ajax({
                        type: 'POST',
                        url: '/office/action/StatusChangeNotification.php',
                        data: 'Act=' + Acts,
                        success: function (msg) {}
                    });

                });

                const cont = $(this).parent('.notif-container');

                function moreClickHandler() {
                    $(cont).find(".show_more").hide();
                    $(cont).find(".loading").show();

                    const loaderContainer = $(this).parent('.loader-container');

                    $('#Newnotifications').append($('<div>').load('/office/action/notifications.php' , {skip:loaderContainer.attr("data-skip")}, function () {
                        loaderContainer.remove();
                        $(cont).find(".show_more").click({cont:cont}, moreClickHandler);
                    }));
                }

                $(cont).find(".show_more").click({cont:cont}, moreClickHandler);

            }));

        });


        $('.Select2OY').on('select2:open', function (e) {
            $('.Select2OY').removeClass('select2opacity');
        });
        $('.Select2OY').on('select2:close', function (e) {
            $('.Select2OY').addClass('select2opacity');
        });
        $('.ChoodeItemOY').mouseover(function () {
            $('.ChoodeItemOY').removeClass('select2opacity');
        });
        $('.ChoodeItemOY').mouseout(function () {
            $('.ChoodeItemOY').addClass('select2opacity');
        });



    </script>


    <script>
        // עיצוב תוצאות סלקט2
        function formatClient(repo) {
            if (repo.loading) {
                return repo.text;
            }

            if (repo.name != null && repo.name != '') {
                var name = "<strong>" + "<?php echo lang('name_footernew') ?>" + "</strong>" + repo.name;
            } else {
                var name = "";
            }

            if (repo.companyid != null && repo.companyid != '') {
                var companyid = "<br><strong>" + "<?php echo lang('id_footernew') ?>" + "</strong>" + repo.companyid;
            } else {
                var companyid = "";
            }

            if (repo.phone != null && repo.phone != '') {
                var phone = "<br><strong>" + "<?php echo lang('mobile_footernew') ?>" + "</strong>" + repo.phone;
            } else {
                var phone = "";
            }

            if (repo.email != null && repo.email != '') {
                var email = "<br>" + repo.email;
            } else {
                var email = "";
            }

            if (repo.barnd != null && repo.barnd != '') {
                var barnd = "<br><strong>" + "<?php echo lang('branch_footernew') ?>" + " </strong>" + repo.barnd;
            } else {
                var barnd = "";
            }

            var markup = "<div style='font-size:12px;'>" + name + "" + companyid + "" + phone + "" + email + "" + barnd + "</div>";
            return markup;
        }
        function formatClientSelection(repo) {
            return repo.text;
        }
        // עיצוב תוצאות סלקט2

        $('.ClientSearchTop').select2({
            templateResult: formatClient,
            templateSelection: formatClientSelection,
            theme: "bootstrap",
            placeholder: "<?php echo lang('search_client') ?>",
            escapeMarkup: function (markup) {
                return markup;
            },
            language: "he",
            allowClear: true,
            width: '100%',
            ajax: {
                url: '/office/action/ClientSelect.php',
                dataType: 'json',
            },
            minimumInputLength: 3,
        });
        $('.ProductSearchTop').select2({
            theme: "bootstrap",
            placeholder: "<?php echo lang('choose_branch') ?>",
            language: "he",
            allowClear: false,
            width: '100%',
            ajax: {
                url: '/office/action/BrandSelect.php',
                dataType: 'json'
            },
        });
        $(document).ready(function () {
            $('.ClientSearchTop').on('select2:select', function (e) {
                window.location.href = '/office/ClientProfile.php?u=' + $(this).val();
            });

            $('.ProductSearchTop').on('select2:select', function (e) {
                var SelectedItem = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: '/office/action/UpdateBrandSelected.php?BrandId=' + SelectedItem,
                    success: function (msg) {
                        BN('0', '<?php echo lang('action_done') ?>');
                        location.reload()
                    },
                    error: function (xhr, status, error) {
                        BN('1', '<?php echo lang('update_branch_footernew') ?>');
                    }
                });

            });
    <?php $ItemDetailsFooter = DB::table('brands')->where('id', Auth::user()->ItemId)->where('CompanyNum', '=', Auth::user()->CompanyNum)->first(); ?>
            $(".ProductSearchTop").append('<option value="<?php echo @$ItemDetailsFooter->id; ?>" selected="selected"><?php echo @$ItemDetailsFooter->BrandName; ?></option>').trigger('change');
        });



    </script>

    <?php if (Auth::user()->id == '1') { ?>
        <!--<script id="sb-php-init" src="<?php //echo App::url('supportboard/php/sb.php') ?>"></script>-->
    <?php } ?>

<?php else: endif; ?>
<?php
if (!empty($footerJs) && is_array($footerJs)) {
    foreach ($footerJs as $js) {
        if (!empty($js['src']))
            printf('<script src="%s"></script>', $js['src']);
    }
}
?>

<?php require_once dirname(__FILE__, 3) . "/office/js/meeting_edit/modals.php" ?>
<script type="text/javascript" src="/office/js/meeting_edit/details-module.js?<?php echo filemtime(__DIR__. '/../../office/js/meeting_edit/details-module.js') ?>"></script>

<script src="/office/js/trainee/trainee.js?<?php echo filemtime(__DIR__. '/../../office/js/trainee/trainee.js') ?>"></script>
<script src="/office/js/createClass/createClass.js?<?php echo filemtime(__DIR__. '/../../office/js/createClass/createClass.js') ?>"></script>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P3BPF8F"
                  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

</body>
</html>
