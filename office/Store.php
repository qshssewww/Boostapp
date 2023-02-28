<?php
ini_set("max_execution_time", 0);

require_once '../app/init.php';
require_once 'Classes/Company.php';
require_once 'Classes/ShopRender.php';
require_once 'Classes/ShopPost.php';
require_once 'Classes/ItemColor.php';
require_once 'Classes/Size.php';
require_once 'Classes/Item.php';
require_once 'Classes/ItemCategory.php';
require_once "Classes/CompanyProductSettings.php";
require_once "Classes/MembershipType.php";
require_once "Classes/ClassesType.php";

if (Auth::check()) {
    $pageTitle = lang('items_management');
    require_once '../app/views/headernew.php';
    if (Auth::userCan('31')) {
        include_once('loader/loader.php');

        $CompanySettingsDash = $CompanySettingsDash ?? DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();
        ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
        <script src="/office/assets/js/onlineLibrary/confirm.js"></script>
        <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">

        <link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
        <link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/rowreorder/1.2.6/css/rowReorder.dataTables.min.css" rel="stylesheet">

        <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

        <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/rowreorder/1.2.6/js/dataTables.rowReorder.min.js"></script>


        <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
        <script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
        <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

        <script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>

        <link href="/office/assets/css/timepicker/timepicker.css" rel="stylesheet">
        <script src="/office/assets/js/timepicker/timepicker.js"></script>

<!--        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>-->
        <script src="https://cdn.tiny.cloud/1/xaim09qncidvaryqjfpfu4i32rwf3objj6he6zajudj143hf/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>

        <link href="/office/calendarPopups/assets/css/popup-updated.css" rel="stylesheet">
        <link href="assets/css/fixstyle.css?<?= filemtime(__DIR__.'/assets/css/fixstyle.css') ?>" rel="stylesheet">


        <script src="newShop/assets/js/main.js?<?= filemtime('newShop/assets/js/main.js') ?>"></script>
        <link href="newShop/assets/css/main.css" rel="stylesheet">

        <script src="newShop/assets/js/clubmemberships.js?<?= filemtime(__DIR__ . '/newShop/assets/js/clubmemberships.js') ?>"></script>
        <link href="newShop/assets/css/clubmemberships.css" rel="stylesheet">


        <script src="newShop/assets/js/items.js?<?= filemtime(__DIR__.'/newShop/assets/js/items.js') ?>"></script>
        <link href="newShop/assets/css/items.css" rel="stylesheet">

        <script src="newShop/assets/js/links.js?<?= filemtime(__DIR__.'/newShop/assets/js/links.js') ?>"></script>
        <link href="newShop/assets/css/links.css" rel="stylesheet">

        <script src="newShop/assets/js/settings.js?<?= filemtime(__DIR__.'/newShop/assets/js/settings.js') ?>"></script>
        <link href="newShop/assets/css/settings.css" rel="stylesheet">

        <script src="newShop/assets/js/selectAndMainPopup.js?<?= filemtime(__DIR__.'/newShop/assets/js/selectAndMainPopup.js') ?>"></script>
        <link href="newShop/assets/css/selectAndMainPopup.css" rel="stylesheet">

        <script src="newShop/assets/js/registerLimitPopup.js?<?= filemtime(__DIR__.'/newShop/assets/js/registerLimitPopup.js') ?>"></script>
        <link href="newShop/assets/css/registerLimitPopup.css" rel="stylesheet">

        <script src="newShop/assets/js/purchaseLimitPopup.js?<?= filemtime(__DIR__.'/newShop/assets/js/purchaseLimitPopup.js') ?>"></script>
        <link href="newShop/assets/css/purchaseLimitPopup.css" rel="stylesheet">

        <script src="newShop/assets/js/createClubMembership/createClubMemberships.js?<?php echo filemtime('newShop/assets/js/createClubMembership/createClubMemberships.js') ?>"></script>
        <link href="newShop/assets/css/createClubMemberships.css" rel="stylesheet">

        <script src="newShop/assets/js/createClubMembership/externalPurchase.js?<?php echo filemtime('newShop/assets/js/createClubMembership/externalPurchase.js') ?>"></script>
        <script src="newShop/assets/js/createClubMembership/registrationRestrictions.js?<?php echo filemtime('newShop/assets/js/createClubMembership/registrationRestrictions.js') ?>"></script>

        <script src="newShop/mainPopupSections/js/membership.js?<?php echo filemtime('newShop/mainPopupSections/js/membership.js') ?>"></script>
        <link href="newShop/mainPopupSections/css/membership.css" rel="stylesheet">

        <script src="newShop/mainPopupSections/js/product.js?<?php echo filemtime('newShop/mainPopupSections/js/product.js') ?>"></script>
        <link href="newShop/mainPopupSections/css/product.css" rel="stylesheet">

        <script src="newShop/mainPopupSections/js/smartLink.js?<?= filemtime(__DIR__.'/newShop/mainPopupSections/js/smartLink.js') ?>"></script>
        <link href="newShop/mainPopupSections/css/smartLink.css" rel="stylesheet">
        <link href="/office/assets/css/imgUpload.css" rel="stylesheet">
        <script src="/assets/office/js/jquery.Jcrop.min.js"></script>
        <script src="/assets/office/js/jquery.imgpicker.js"></script>

        <script type="text/javascript">
            function OnSearch(input) {
                if (input.value == "") {
                    $(input).trigger('change');
                }
            }
        </script>

        <?php
        $company = Company::getInstance();
        $shopRender = new ShopRender();
        $items = new Item();
        $shopPost = new ShopPost();
        $productItems = DB::table('boostapp.items')->where("Status", "=", 0)->where("CompanyNum", "=", $company->__get('CompanyNum'))->where('Department', "=", 4)->where("isPaymentForSingleClass",0)->get();
        $membershipItems = $items->getMembershipsWithoutSingle($company->__get('CompanyNum'));

        $ClassTypeForSelect = (new ClassesType())->getAllClassTypeForSelect($company->CompanyNum);

        $categories = new ItemCategory();
        $categories->getSetMainCategory($company->CompanyNum);
        MembershipType::getDefaultMembership();

        $levels = DB::table('boostapp.clientlevel')->where("CompanyNum", "=", $company->__get('CompanyNum'))->get();

        $companyProductSettings = (new CompanyProductSettings)->getSingleByCompanyNum($company->__get('CompanyNum'));

        $dynamicForms = DB::table('boostapp.dynamicforms')->where("CompanyNum", "=", $company->__get('CompanyNum'))->where("Status", "=", "0")->get();
        $healthForms = DB::table('boostapp.healthforms')->where("CompanyNum", "=", $company->__get('CompanyNum'))->orderBy('id', 'DESC')->first();
        $registrationFees = DB::table('boostapp.registration_fees')->where("CompanyNum", "=", $company->__get('CompanyNum'))->where("Status", "=", "0")->where("disabled", 0)->get();

        $colors = DB::table('boostapp.item_colors')->where("CompanyNum", "=", $company->__get('CompanyNum'))->get();
        $sizes = DB::table('boostapp.item_sizes')->where("CompanyNum", "=", $company->__get('CompanyNum'))->get();

        include_once "newShop/selectPopup.php";
        include_once "newShop/mainShopPopup.php";
        include_once "newShop/view/create-club-memberships-popup.php";
        include_once "newShop/registerLimitPopup.php";
        include_once "newShop/purchaseLimitPopup.php";
        include_once "newShop/UploadImg.php";
        include_once "newShop/supportPopup.php";
        ?>
        <!-- modal header start -->
        <div class="shopWrapper px-15" id="shopMain">
            <div class="shopContainer position-relative pb-16">
                <div class="d-flex justify-content-between align-items-baseline ">
                    <!-- modal content start -->
                    <div>
                        <div class=" typeButtons d-flex w-100 flex-wrap">
                            <a id="memberships" data-value="1" class="btn btn-light btn-rounded btn-sm current-btn mie-10 mb-10"><?php echo lang('subscriptions') ?></a>
                            <a id="items" data-value="2" class="btn  btn-light btn-rounded btn-sm mie-10 mb-10"><?php echo lang('products') ?></a>
                                <a id="payment" data-value="3" class="btn  btn-light btn-rounded btn-sm mb-10"><?php echo lang('payment_pages') ?></a>
                        </div>
                    </div>
                        <div class="w-50 js-mobile-width-dialog">
                            <?php
                            require_once '../app/views/storeSettings.php';
                            ?>
                        </div>
                </div>
            </div>
            <div class="alertush">
                <?php echo lang('copied_to_clipboard') ?>
            </div>
            <?php
            require_once('newShop/memberships.php');
            require_once('newShop/items.php');
            require_once('newShop/links.php');


            require_once '../app/views/footernew.php';
            ?>
<!--            <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
            <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
            <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
            <script type="text/javascript" src="js/settingsDialog/settingsDialog.js?<?php echo filemtime('js/settingsDialog/settingsDialog.js') ?>"></script>

            <?php
        }
    } else {
        header("Location: /");
    }
