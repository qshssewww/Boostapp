<?php
    error_reporting(E_ALL);
    ini_set("max_execution_time", 0);
    ini_set("display_errors", true);
    require_once '../app/init.php';
    require_once '../app/views/headernew.php';

    $site_url = '//' . $_SERVER['HTTP_HOST'];

    // load client form classes
    require_once 'Classes/ClientForm.php';
    require_once 'Classes/Client.php';
    require_once 'Classes/Item.php';
    require_once 'Classes/Banks.php';
    $clientForm = new ClientForm();

    $groupNumber = rand(1,9999999);
    $departmentArray=[1, 2];
    $type = 'client';  //todo  change lead or client
    if ($type === 'lead') {
        $departmentArray[] = 3;
    }

    $userId = Auth::user()->id;
    $companyNum = Auth::user()->CompanyNum;
    $formDetails = $clientForm->getCompanyForm($companyNum, $type, $userId);

    $formId = $formDetails[0]->form_id;
    $fieldsDetails = $clientForm->getFormByCompanyNumAndType($companyNum, $type);

    $item = new Item();
    $membershipData = $item->getMemberships($companyNum, );
    $banks = new Banks();
    $banksData = $banks->getAllBanks();

?>

<script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . "/assets/js/jquery.bootstrap.wizard.js"; ?>"
        xmlns="http://www.w3.org/1999/html"></script>
<script src="https://cdn.jsdelivr.net/gh/RubaXa/Sortable/Sortable.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js" ></script>


<!-- new client main modal :: begin -->
<div class="modal js-new-modals px-0 px-sm-auto text-gray-700 bsapp-wizard-with-summary text-start js-modal-no-close" data-keyboard="false" tabindex="-1" role="dialog" id="js-client-popup" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered m-0 m-sm-auto bsapp-max-w-780p">
        <div class="modal-content overflow-auto rounded position-relative mt-lg-10">
            <div class="bsapp-overlay-loader js-loader d-none">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only"><?php echo  lang('loading_datatables')?></span>
                </div>
            </div>
            <div class="modal-body d-flex flex-column justify-content-between p-0 h-100">
                <div class="h-100">
                    <div class="d-flex position-relative bsapp-wizard-box" >
                        <!-- Right side of the modal -->
                        <div class="col-md-6 p-0 card flex-column h-100 js-summary-container bsapp-summary-container bsapp-scroll overflow-auto"  style="border:0px !important;">
                            <h5 class="d-flex align-items-center text-black p-15 ">
                                <div><?php echo lang('new_client')?></div>
                            </h5>

                            <!-- Right side of the modal : step 1 - main details -->
                            <div class="d-flex flex-column text-start p-15 js-summary-step-1">
                                <div class="d-flex justify-content-between mb-10">
                                    <span><?php echo  lang('client_details_class')?></span>
                                    <a href="javascript:;" class="js-go-step-1 d-none js-go-back-step"><?php echo lang('edit')?></a>
                                </div>

                                <div id="content-step-1-form">
                                    <div class="form-group d-flex align-items-center mb-10">
                                        <label class="fal fa-user-circle mie-10 my-auto">
                                        </label>
                                        <input type="text" id="js-summary-first-name" class="form-control mie-10" required placeholder="<?php echo lang('first_name')?>" aria-required="true">
                                        <input type="text" id="js-summary-last-name" class="form-control" required placeholder="<?php echo lang('last_name')?>">
                                    </div>
                                    <div class="form-group d-flex align-items-center mb-10">
                                        <label class="fal fa-phone mie-10 my-auto"></label>
                                        <div class="d-flex align-items-center position-relative w-100 pie-10">
                                            <select id="js-summary-phone" class="numbers-only">
                                                <option></option>
                                            </select>
                                        </div>
                                        <div class="w-150p">
                                            <select class="js-select2 js-summary-code">
                                                <option>+972</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="pis-25 d-none" id="reset-phone" >
                                        <a class="text-info bsapp-fs-14 cursor-pointer" onclick="ClientForm.showSearchField(this);">
                                            <i class="fal fa-long-arrow-right mie-5"></i>
                                            <?php echo lang('search_client'); ?>
                                        </a>
                                    </div>

                                    <div data-context="js-summary-item-hidden" id="section-minor-step1">
                                        <div class="form-group mt-10 d-flex">
                                            <div class="custom-control custom-checkbox  custom-control-inline mx-0 mie-10">
                                                <input type="checkbox" id="js-checkbox-for-minor" name="js-checkbox" class="custom-control-input">
                                                <label class="custom-control-label" for="js-checkbox-for-minor"> <?php echo  lang('for_new_minor_client')?> </label>
                                            </div>
                                        </div>
                                        <div class="form-group text-gray d-none" id="js-minor-text-warning" data-context="js-for-minor">
                                            * בשיוך מנוי יווצר קטין המשוייך ללקוח בוגר מהארכיון
                                        </div>
                                    </div>

                                    <div class="d-none" id="section-minor-step2">
                                        <div class="d-flex justify-content-between mb-10">
                                            <span><?php echo lang('for_minor')?></span>
                                        </div>

                                        <div class="form-group d-flex align-items-center mb-10"">
                                            <label class="fal fa-child mie-16 w-20p my-auto"></label>
                                            <div class="flex-fill d-flex">
                                                <input type="text" id="minor-first-name-step2" name="first_name" class="form-control mie-10" placeholder="<?php echo  lang('first_name')?>">
                                                <input type="text" id="minor-last-name-step2" name="last_name" class="form-control"  placeholder="<?php echo  lang('last_name')?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right side of the modal : step 2 - summary subscription -->
                            <div class="d-none flex-column text-start p-15 border-top border-light js-summary-step-2">
                                    <div class="d-flex justify-content-between mb-10">
                                        <span><?php echo lang('subscription_details')?></span>
                                        <a href="javascript:;" class="js-go-step-2 js-go-back-step"><?php echo lang('edit')?></a>
                                    </div>
                                <div id="content-step-2-form" class="bsapp-js-disabled-o">
                                    <div class="form-group d-flex align-items-center mb-10">
                                        <label class="fal fa-receipt mie-10 w-20p my-auto">
                                        </label>
                                        <div class="d-flex justify-content-between py-6 w-100 px-12 align-items-center border border-light rounded">
                                            <span class="js-selected-item-name pie-15"></span>
                                            <div class="">₪<span class="js-selected-item-price">₪</span></div>
                                        </div>
                                    </div>
                                    <div class="form-group d-flex w-100 mb-10">
                                        <label class="mie-10 w-20p my-auto"></label>
                                        <div class="list-group list-group-flush flex-fill js-summary-total"></div>
                                    </div>
                                </div>
                                </div>

                        <div class="position-absolute js-box-bottom-bar bsapp-z-99 w-100 border-0 rounded-bottom rounded-lg justify-content-center py-10 d-flex d-md-none"
                             style="background: rgba(255, 255, 255, 0.95);
                             box-shadow: 0px 1px 13px 0px rgba(210, 212, 215, 0.8);
                             bottom: 0;
                             left :0;">
                            <a class="btn btn-info btn-rounded px-50 px-sm-100" id='js-step-0' href="javascript:;">
                                המשך
                            </a>
                        </div>
                            </div>

                        <!-- Left side of the modal -->
                        <div class="card js-wizard-container wizard-container bsapp-expand-50 d-none d-md-block rounded-0 border-light" style="border-inline-start: 1px solid ;" >
                            <!-- Left side of the modal - Buttons at the top of the model -->
                            <div class="px-15 pb-5 pt-10 d-flex align-items-center justify-content-between" id="js-top-model">
                                <div>
                                    <a href="javascript:;" id="previous-step" class="d-none text-black d-sm-none js-go-back-step"> <i class="fal fa-angle-rtl"></i> חזור </a>
                                    <a href="javascript:;" id="hide-iframe"  class="d-none">חזור </a>
                                </div>
                                <div>
                                    <a href="javascript:; "class="text-dark mie-10 js-btn-field-edit-fields"><i class="fal fa-cog h5"></i></a>
                                    <a href="javascript:; "class="text-dark js-modal-dismiss"><i class="fal fa-times h4"></i></a>
                                    <a href="javascript:; "class="text-dark js-btn-hide-fields" style="display:none;" ><i class="fal fa-times h4"></i></a>
                                </div>
                            </div>

                            <div class="overflow-auto bsapp-scroll mb-50" style="height: calc( 100vh - 110px );">
                                <div class="wizard-card js-add-client border-0 bsapp-js-disabled-o px-12 pt-5 pb-12 px-sm-6 bt-sm-3 pb-sm-5 h-100" data-color="red" id="wizard">
                                    <a href="javascript:;" class="js-previous d-none"> <i class="fal fa-angle-rtl"></i> חזור </a>
                                    <div class="d-flex flex-column justify-content-between h-100" action="" method="">
                                        <div class="wizard-navigation d-none">
                                            <div class="progress-with-circle">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="4" style="width: 15%;"></div>
                                            </div>
                                            <ul>
                                                <li>
                                                    <a href="#js-step-1" data-toggle="tab">
                                                        <div class="icon-circle">
                                                            <i class="fas fa-folder-plus"></i>
                                                        </div>
                                                        Step 1
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#js-step-2" data-toggle="tab">
                                                        <div class="icon-circle">
                                                            <i class="fas fa-folder-plus"></i>
                                                        </div>
                                                        Step 2
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#js-step-3" data-toggle="tab">
                                                        <div class="icon-circle">
                                                            <i class="fas fa-folder-plus"></i>
                                                        </div>
                                                        Step 3
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content">
                                            <!-- Left side of the modal - step-1 : more details-->
                                            <div class="tab-pane pt-15 pb-100 bsapp-overflow-x-hidden" id="js-step-1">
                                                <div class="js-section-show-fields flex-column d-flex">
                                                    <h6 class="mb-10 d-none" data-context="js-for-minor"><?php echo lang('minor_client_details')?> </h6>
                                                    <h6 class="mb-10 d-flex" id="more_details_title"><?php echo lang('more_details')?> </h6>
                                                    <form class="d-flex flex-column js-cf-fields" id="js-client-form-add" data-parsley-validate="" >
                                                        <input type="hidden" name="parent_id" id="js-parent-id"/>
                                                        <input type="hidden" name="close" id="js-save-and-close" step="1"/>

                                                        <?php require_once 'partials-views/client-form/cf-fields.php';?>
                                                    </form>
                                                </div>
                                                <div class="js-section-edit-fields d-none flex-column">
                                                    <?php require_once 'partials-views/client-form/cf-fields-crud.php';?>
                                                </div>
                                            </div>

                                            <!-- Left side of the modal - step-2 : subscription -->
                                            <form class="tab-pane bsapp-overflow-x-hidden pt-15 pb-100" id="js-step-2">
                                                <div class="">
                                                    <h6 class="mb-10"><?php echo lang('add_subscription_desk')?></h6>
                                                    <div class="form-group d-flex align-items-center mb-10">
                                                        <label class="fal fa-receipt mie-10 w-20p my-auto"></label>
                                                        <div class="d-flex w-100">
                                                            <select name="" class="js-select2-membership">
                                                                <?php foreach ($membershipData as $memKey => $memValue): ?>
                                                                    <option  value="<?php echo $memValue['id']; ?>" js-data-type="<?php echo $memValue['department']; ?>"  js-data-help="<?php echo $memValue['helpHtml']; ?>" js-valid="<?php echo $memValue['valid']; ?>"  js-valid-type="<?php echo $memValue['valid_type']; ?>"   js-item-price="<?php echo $memValue['itemPrice']; ?>"  >
                                                                        <?php echo $memValue['title'] ?> - <?php echo $memValue['valid']; ?> - <?php echo $memValue['department']; ?> - <?php echo $memValue['valid_type']; ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group d-flex align-items-center mb-10 text-gray-500">
                                                        <label class="fal fa-info-circle mie-10 w-20p my-auto"></label>
                                                        <div class="bsapp-fs-14 js-data-help">
                                                            Payment type: Regular Validity: 3 months || Tab || Admissions: 8
                                                        </div>
                                                    </div>
                                                    <div class="js-context-membership">
                                                        <div class="form-group d-flex align-items-center mb-10">
                                                            <label class="fal fa-hourglass-start mie-10 w-20p my-auto"></label>
                                                            <div class="d-flex flex-fill">
                                                                <select name="" class="js-select2 js-end-date-calculation">
                                                                    <option value="5" ><?php echo lang('cusomer_card_validity_4') ?>
                                                                    </option>
                                                                    <option value="4" ><?php echo lang('cusomer_card_validity_5') ?>
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group d-none align-items-center mb-10 flex-column" id="start-end-membership-date">
                                                            <div class="d-flex w-100 pie-20">
                                                                <label class="fal fa-calendar-exclamation mie-10 w-20p my-auto"></label>
                                                                <div class="pie-5 w-50">
                                                                    <input type="date" class="form-control w-100" id="js-start-date"/>
                                                                </div>
                                                                <div class="w-50 pis-5">
                                                                    <input type="date" class="form-control w-100" id="js-end-date"/>
                                                                </div>
                                                            </div>
                                                            <div class="p-2 d-flex align-items-center bsapp-fs-14" id="membership-date-warning">
                                                                <span></span>
                                                            </div>
                                                        </div>
<!--                                               todo         Reporting a different period, recalculating price (decreased by requirements)-->
<!--                                                        <div class="form-group d-none flex-column mt-30 mb-10">-->
<!--                                                            <div class="custom-control custom-radio mb-15">-->
<!--                                                                <input type="radio" id="customRadio1" name="customRadios" class="custom-control-input">-->
<!--                                                                <label class="custom-control-label" for="customRadio1">There is no need</label>-->
<!--                                                            </div>-->
<!--                                                            <div class="custom-control custom-radio mb-15">-->
<!--                                                                <input type="radio" id="customRadio2" name="customRadios" class="custom-control-input">-->
<!--                                                                <label class="custom-control-label" for="customRadio2"> Yes thought relatively </label>-->
<!--                                                            </div>-->
<!--                                                        </div>-->
<!--                                                        <div class="form-group d-none align-items-center  bsapp-fs-14">-->
<!--                                                            <div class="input-group w-150p rounded overflow-hidden">-->
<!--                                                                <input type="text" class="form-control border-light bg-light rounded-0" value="4" >-->
<!--                                                                <div class="input-group-append w-30p">-->
<!--                                                                    <span class="input-group-text border-light bg-light rounded-0">₪</span>-->
<!--                                                                </div>-->
<!--                                                            </div>-->
<!--                                                            <span class="mis-10">For each day deducted from the original period</span>-->
<!--                                                        </div>-->
<!--                                                        <div class="form-group d-none align-items-center  bsapp-fs-14">-->
<!--                                                            Reduced: 120 NIS (month calculated according to 30 days)-->
<!--                                                        </div>-->

                                                    </div>
                                                    <div class="form-group d-flex align-items-center mb-10">
                                                        <label class="fal fa-shekel-sign mie-10 w-20p my-auto"></label>
                                                        <input type="text" name="" class="form-control bg-light border-light js-item-price" required="" placeholder="">
                                                    </div>
                                                    <!--todo remove d-none -->
                                                    <div id="fix-payment" class="d-none">
                                                        <div>
                                                            <h6 class="mb-10"><?php echo lang('fixed_payments')?></h6>
                                                        </div>
                                                        <div class="js-html-fixed-payments">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>

                                            <!-- Left side of the modal - step-3 : subscription -->

                                            <form class="tab-pane bsapp-overflow-x-hidden pt-5 pb-100" id="js-step-3">
                                                <div class="w-100">
                                                    <div class="form-group d-flex align-items-center mb-10">
                                                        <input type="number" name="" class="form-control bg-light border-light js-final-amount" required=""  min="0" max="20000" placeholder="<?php echo lang('enter_amount')?>" aria-required="true">
                                                    </div>
                                                    <!-- Left side of the modal - step-3 : subscription - Payment Options-->
                                                    <div class="form-group">
                                                        <ul class="nav nav-pills px-0 d-flex justify-content-between my-10" id="myTab" role="tablist">
                                                            <li class="nav-item" role="presentation">
                                                                <a class="nav-link px-10 py-7 active" id="js-tab-btn-credit" data-toggle="pill" href="#js-tab-section-card" type-payment="3" role="tab"><?php echo lang('credit_card')?></a>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <a class="nav-link px-10 py-7" id="js-tab-btn-cash" data-toggle="pill" href="#js-tab-section-cash" type-payment="1" role="tab" ><?php echo lang('cash')?></a>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <a class="nav-link px-10 py-7" id="js-tab-btn-cheque" data-toggle="pill" href="#js-tab-section-cheque" type-payment="2" role="tab" ><?php echo lang('check')?></a>
                                                            </li>
                                                            <li class="nav-item" role="presentation">
                                                                <a class="nav-link px-10 py-7" id="js-tab-btn-ebanking" data-toggle="pill" href="#js-tab-section-ebanking" type-payment="4" role="tab" aria-controls="contact" aria-selected="false"><?php echo lang('bank_transfer_short')?></a>
                                                            </li>
                                                        </ul>
                                                        <div class="tab-content" id="myTabContent">

                                                            <!-- Left side of the modal - step-3 : subscription Payment option- credit card-->
                                                            <div class="tab-pane fade show active" id="js-tab-section-card" role="tabpanel" aria-labelledby="home-tab">
                                                                <div class="tab-content payment-methods-tabs-content">
                                                                    <div class="tab-pane active js-payment-method-cc-wrapper" id="js-tab-cc">
                                                                        <div class="px-15 mb-8 text-dark font-weight-bold">
                                                                            שיטת תשלום
                                                                        </div>
                                                                        <div class="px-15 mb-16 text-gray-400">
                                                                            <select class="form-control bg-light border-light js-payment-type" id="paymentType">
                                                                                <option value="1">רגיל</option>
                                                                                <option value="2">תשלומים</option>
                                                                                <option value="3">תשלומים ללא תפיסת מסגרת </option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="js-payment-number-block" style="display: none;">
                                                                            <div class="px-15 mb-8 text-dark font-weight-bold">
                                                                                מספר תשלומים
                                                                            </div>
                                                                            <div class="px-15 mb-16 text-gray-400">
                                                                                <!-- todo get from data base-->
                                                                                <select class="form-control bg-light border-light js-payment-number-list" id="paymentNumber">
                                                                                    <option value="1">1</option>
                                                                                    <option value="2">2</option>
                                                                                    <option value="3">3</option>
                                                                                    <option value="4">4</option>
                                                                                    <option value="5">5</option>
                                                                                    <option value="6">6</option>
                                                                                    <option value="7">7</option>
                                                                                    <option value="8">8</option>
                                                                                    <option value="9">9</option>
                                                                                    <option value="10">10</option>
                                                                                    <option value="11">11</option>
                                                                                    <option value="12">12</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="payment-methods-wrapper mb-20">
                                                                            <div class="px-15 mb-8 text-dark font-weight-bold">
                                                                                כרטיסי אשראי
                                                                            </div>
                                                                            <div class="payment-methods-cc-list js-payment-methods-cc-list">
                                                                            </div>
                                                                            <div class="px-15 w-100 d-flex py-8 border-bottom border-light js-cc-item js-add-new-card" id="creditCard0">
                                                                                <div class="w-30p">
                                                                                    <div class="custom-control custom-radio">
                                                                                        <input type="radio" id="js-radio-payment-mode-0" name="customRadio" class="custom-control-input js-select-card" value="" checked>
                                                                                        <label class="custom-control-label" for="js-radio-payment-mode-0"></label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="d-flex flex-column flex-fill">
                                                                                    <div>
                                                                                        <span class="mie-10 js-cc-item-text">הוספת כרטיס חדש</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>





<!--                                                                <div><h6 class="mb-10  mt-10">--><?php //echo lang('credit_card_details')?><!--</h6></div>-->
<!--                                                                <div class="form-group mb-10">-->
<!--                                                                    <input class="form-control bg-light border-light js-card-number" placeholder="--><?php //echo lang('credit_card_num')?><!--" />-->
<!--                                                                </div>-->
<!--                                                                <div class="form-group d-flex align-items-center justify-content-between mb-10">-->
<!--                                                                    <div class="d-flex w-150p mie-15">-->
<!--                                                                        <select name="" class="js-select2">-->
<!--                                                                            --><?php //for ($i = date("Y"); $i < date("Y") + 10; $i++): ?>
<!--                                                                                <option value="--><?php //echo $i; ?><!--" >--><?php //echo $i; ?><!--</option>-->
<!--                                                                            --><?php //endfor; ?>
<!--                                                                        </select>-->
<!--                                                                    </div>-->
<!--                                                                    <div class="d-flex w-100p mie-15">-->
<!--                                                                        <select name="" class="js-select2">-->
<!--                                                                            --><?php //for ($m = 1; $m < 12; $m++): ?>
<!--                                                                                <option value="--><?php //echo $m; ?><!--" >--><?php //echo $m; ?><!--</option>-->
<!--                                                                            --><?php //endfor; ?>
<!--                                                                        </select>-->
<!--                                                                    </div>-->
<!--                                                                    <input class="form-control bg-light border-light w-100p js-card-cvv" placeholder="CVV" />-->
<!--                                                                </div>-->
<!--                                                                <div class="form-group mb-10 mt-20">-->
<!--                                                                    <h6>--><?php //echo lang('distribution_payments')?><!--</h6>-->
<!--                                                                </div>-->
<!--                                                                <div class="form-group d-flex flex-column mb-10">-->
<!--                                                                    <div class="d-flex align-items-center">-->
<!--                                                                        <div class="custom-control custom-radio mb-10">-->
<!--                                                                            <input type="radio" id="js-payment-breakdown-1" name="customRadios" class="custom-control-input">-->
<!--                                                                            <label class="custom-control-label" for="js-payment-breakdown-1"></label>-->
<!--                                                                        </div>-->
<!--                                                                        <div class="mx-10 w-75p d-flex">-->
<!--                                                                            <select class="js-select2">-->
<!--                                                                                <option>1</option>-->
<!--                                                                                <option>2</option>-->
<!--                                                                                <option>3</option>-->
<!--                                                                            </select>-->
<!--                                                                        </div>-->
<!--                                                                        <span>--><?php //echo lang('payments')?><!--</span>-->
<!--                                                                    </div>-->
<!--                                                                    <div>-->
<!--                                                                        <div class="custom-control custom-radio mb-10">-->
<!--                                                                            <input type="radio" id="js-payment-breakdown-2" name="customRadios" class="custom-control-input">-->
<!--                                                                            <label class="custom-control-label" for="js-payment-breakdown-2">--><?php //echo lang('according_subscription_period_membership')?><!--</label>-->
<!--                                                                        </div>-->
<!--                                                                    </div>-->
<!--                                                                </div>-->
<!--                                                                <div class="form-group mb-10">-->
<!--                                                                    <h6>--><?php //echo lang('payment_method')?><!--</h6>-->
<!--                                                                </div>-->
<!--                                                                <div class="form-group d-flex flex-column   mb-10">-->
<!--                                                                    <div class="custom-control custom-radio mb-10">-->
<!--                                                                        <input type="radio" id="js-radio-payment-method-1" name="customRadios" class="custom-control-input">-->
<!--                                                                        <label class="custom-control-label" for="js-radio-payment-method-1">--><?php //echo lang('normal_with_frame_perception')?><!--</label>-->
<!--                                                                    </div>-->
<!--                                                                    <div class="custom-control custom-radio mb-10">-->
<!--                                                                        <input type="radio" id="js-radio-payment-method-2" name="customRadios" class="custom-control-input">-->
<!--                                                                        <label class="custom-control-label" for="js-radio-payment-method-2">--><?php //echo lang('cyclical_billing_without_frame')?>
<!--                                                                            <i class="fal fa-info-circle"></i>-->
<!--                                                                        </label>-->
<!--                                                                    </div>-->
<!--                                                                </div>-->
                                                            </div>

                                                            <!-- Left side of the modal - step-3 : subscription Payment option- cash-->
                                                            <div class="tab-pane fade" id="js-tab-section-cash" role="tabpanel" aria-labelledby="profile-tab">

                                                            </div>

                                                            <div class="tab-pane fade" id="js-tab-section-cheque" role="tabpanel" aria-labelledby="contact-tab">
                                                                <div class="form-group d-flex align-items-center justify-content-between mb-10">
                                                                    <div class="d-flex flex-column w-200p mie-15">
                                                                        <label class="bsapp-fs-12"><?php echo lang("bank_account_number")?></label>
                                                                        <input class="form-control bg-light border-light js-bank-account"  />
                                                                    </div>
                                                                    <div class="d-flex flex-column w-150p mie-15">
                                                                        <label class="bsapp-fs-12"><?php echo lang('a_bank')?></label>
                                                                        <div>
                                                                            <select name="" class="js-select2 js-bank-account-name">

                                                                                <?php foreach ($banksData as $bank => $bankValue): ?>
                                                                                    <option  value="<?php echo $bankValue->BankId;?>">
                                                                                    <?php echo $bankValue->ShortName?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-column">
                                                                        <label class="bsapp-fs-12"><?php echo lang('branch')?></label>
                                                                        <input class="form-control bg-light border-light w-100p js-bank-branch-number" placeholder=""/>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group d-flex align-items-center justify-content-between mb-10">
                                                                    <div class="d-flex flex-column  flex-fill mie-15">
                                                                        <label class="bsapp-fs-12"><?php echo lang('cheque_number')?></label>
                                                                        <input class="form-control bg-light border-light js-cheque-number" />
                                                                    </div>
                                                                    <div class="d-flex flex-column flex-fill">
                                                                        <label class="bsapp-fs-12"><?php echo lang('payment_date')?></label>
                                                                        <input type="date" class="form-control bg-light border-light w-150p js-cheque-date" placeholder="" />
                                                                    </div>
                                                                </div>
                                                            </div>


                                                            <div class="tab-pane fade" id="js-tab-section-ebanking" role="tabpanel" aria-labelledby="contact-tab">
                                                                <div class="form-group d-flex align-items-center justify-content-between mb-10">
                                                                    <div class="d-flex flex-column  flex-fill mie-15">
                                                                        <label class="bsapp-fs-12"><?php echo lang('ref_number')?></label>
                                                                        <input type="number" class="form-control bg-light border-light js-deposit-conf-number"/>
                                                                    </div>
                                                                    <div class="d-flex flex-column flex-fill">
                                                                        <label class="bsapp-fs-12"><?php echo lang('deposit_date')?></label>
                                                                        <input type="date" class="form-control bg-light border-light w-150p js-deposit-date" placeholder="" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <!-- Left side of the modal - step-3 : subscription - summary -->
                                                    <div class="form-group mb-10 mt-20">
                                                        <h6><?php echo lang('summary_2')?></h6>
                                                    </div>
                                                    <div id="step-3-summary" class="list-group list-group-flush">
                                                        <div class="list-group list-group-flush" id="list-payment">
                                                            <div class="list-group-item d-flex justify-content-between">
                                                                <h6><strong><?php echo lang('total_revenue')?></strong></h6>
                                                                <h6>₪<strong id="total-revenue-amount"></strong></h6>
                                                            </div>
                                                            <div class="list-group-item d-flex justify-content-between">
                                                                <h6><strong><?php echo lang('remainder_of_payment')?></strong></h6>
                                                                <h6>₪<strong id="remainder-payment-amount"></strong></h6>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </form>
                                            <div style="height: 100vh !important;" id="payment-iframe" class="d-none">
                                                <iframe src="" frameborder="0" width="100%" height="100%" scrolling="no" class="border-0 js-add-cc-iframe bsapp-min-w-301p" id="payment-iframe"></iframe>
                                            </div>
                                        </div>

                                        <!-- Left side of the modal - Buttons at the bottom of the model -->
                                        <div class="px-15 position-absolute js-box-bottom-bar bsapp-z-99 w-100 border-0 rounded-0 py-10" style="background: rgba(255, 255, 255, 0.95);
                                             bottom: 0;
                                             left :0;
                                             ">
                                            <div class="d-flex justify-content-between js-box-next-prev">
<!--                                                <a class="btn btn-light js-previous" id="save-and-close" href="javascript:;">-->
                                                <a class="btn btn-light" id="save-and-close" href="javascript:;">
                                                    <?php echo lang('save_and_close')?></a>
                                                <a class="btn btn-primary js-next" id="add-subscription-desk" href="javascript:;">
                                                    <?php echo lang('add_subscription_desk')?></a>
<!--                                                <a class="btn btn-primary js-finish btn-block" href="javascript:;">-->
<!--                                                    --><?php //echo lang('add_payment')?><!-- </a>-->
                                                <a class="btn btn-primary js-finish btn-block" id="add-full-payment" href="javascript:;">הוסף תשלום</a>
                                                <a class="btn btn-primary js-finish btn-block" id="add-partial-payment" href="javascript:;">הוסף תשלום חלקי</a>

                                                <a href="javascript:;" class="text-gray-700 dropdown-toggle bsapp-fs-44 pis-30 pie-10 pie-md-15 d-none justify-content-center text-decoration-none" id="step-3-dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <i class="fal fa-ellipsis-v "></i>
                                                </a>
                                                <div class="dropdown-menu text-start w-250p" style="" x-out-of-boundaries="">
                                                    <a class="js-go-back-step dropdown-item text-gray-700 px-8"  onclick="window.location.assign('<?php echo $site_url; ?>/office/ClientProfile.php?u=' + JsData.client_id)"><span class="w-20p d-inline-block text-center"><i class="fal fa-minus-circle"></i></span> <span> שמור ללא חיוב כלל </span></a>
                                                    <a class="dropdown-item px-8 text-gray-700 select-item" id="js-save-payment" href="javascript:;"><span class="w-20p d-inline-block text-center"><i class="fal fa-paper-plane"></i></span> <span>שמור תשלום בחוב </span></a>
                                                </div>

                                            </div>
                                            <div class="d-none js-box-fields-add flex-column">
                                                <a class="btn btn-light js-btn-field-add mb-15" href="javascript:;">
                                                    <?php echo lang('add_new_field')?></a>
                                                <a class="btn btn-primary js-btn-field-finish" href="javascript:;" onclick="ClientForm.updateFieldsDataOrder();">
                                                    <?php echo lang('class_end')?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- new client modal :: end -->


<div id="exit-modal" class="modal fade js-modal-no-close" role="dialog" data-backdrop="false">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body text-center">
                <p>האם אתה בטוח שברצונך לצאת?</p>
                <button type="button" class="btn btn-danger js-confirm-closed">כן, סגור (ללא שמירה)</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">לא, המשך בתהליך</button>
            </div>
        </div>
    </div>
</div>

<!-- modal :: begin -->
<div class="modal js-new-modals px-0 px-sm-auto text-start js-modal-no-close " tabindex="-1" role="dialog" id="js-modal-field-add"  data-backdrop="static">
    <div class="modal-dialog   modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content border-0 shadow-lg bsapp-max-w-400p mx-auto">
            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative">
                <div class="bsapp-overlay-loader js-loader d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only"><?php echo lang('loading_datatablesd')?></span>
                    </div>
                </div>
                <div class="d-flex justify-content-between w-100 mb-20">
<!--                    <h5 class="bsapp-fs-18"><i class="fal fa-thumbtack mie-10"></i>--><?php //echo  lang('create_new_field')?><!--</h5>-->
                    <h5 class="bsapp-fs-18"><i class="fal fa-thumbtack mie-10"></i>יצירת שדה חדש</h5>
                    <a href="javascript:;"  class="text-dark" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                </div>
                <form class="js-form-field-add"  style="height : calc( 100% - 50px ); ">
                    <input type="hidden" class="js-field-id" />
                    <div class="d-flex flex-column justify-content-between h-100 ">
                        <div>
                            <div class="form-group d-flex align-items-center mb-10">
                                <label class="fal fa-bookmark mie-10 w-20p my-auto">
                                </label>
<!--                                <input type="text" name="" class="form-control bg-light border-light" required  placeholder="שם השדה" id="js-cf-new-name" aria-required="true">-->
                                <input type="text" name="" class="form-control bg-light border-light" required  placeholder="<?php echo  lang('field_name')?>" id="js-cf-new-name" aria-required="true">
                            </div>
                            <div class="form-group d-flex align-items-center mb-10">
                                <label class="fal fa-info-circle w-20p  mie-10 my-auto">
                                </label>
                                <div class="flex-fill">
                                    <select name="" class="js-select2" id="js-cf-new-type">
                                        <option value='multi'> בחירה יחידה מרשימת אפשרויות</option>
<!--                                        <option value='multi'> --><?php //echo  lang('single_selection_from_list')?><!--</option>-->
                                        <option value='multiCheck'> <?php echo  lang('multiple_select_dynamic')?></option>
                                        <option value='text'> טקסט חופשי</option>
<!--                                        <option value='text'>--><?php //echo  lang('free_string')?><!--</option>-->
                                        <option value='number'> <?php echo  lang('only_numbers')?></option>
                                        <option value='checkbox'> בחירת כן או לא</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-30 mb-20 js_item_options-stuff" style="display:none;">
<!--                                <h6>--><?php //echo  lang('selection_options')?><!--</h6>-->
                                <h6>אפשרויות בחירה</h6>
                            </div>
                            <div class="form-group">
                                <div class="list-group bsapp-draggable-fields js-draggable-fields js_item_options-stuff" id="js_item_options"  style="display:none;" ></div>
                            </div>
                        </div>
                        <div class="d-flex flex-column form-group">
                            <a class="btn btn-light js-btn-option-add mb-15 js_item_options-stuff" href="javascript:;"  style="display:none;">
                                הוספת אפשרויות בחירה +
<!--                                --><?php //echo  lang('add_selection_option')?>
                                <a class="btn btn-primary js-btn-option-finish" href="javascript:;"  onclick="ClientForm.addField();">
                                <?php echo  lang('save')?>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- modal :: end -->

<input type="hidden" value="<?php echo $formId ?>" id="js-client-form-id" />
<input type="hidden" value="<?php echo $type ?>" id="js-new-client-type" />

<!-- these containers act as markup for items to be used in  the js  :: begin  -->
<div class="js-html-for-option-added d-none">
    <div class="list-group-item w-100 js-item p-0 d-flex mb-12 border border-transparent rounded">
        <div class="d-flex align-items-center text-gray-700  py-7 w-30p js-grip-handle">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <div class="w-100 js-item-view bsapp-item-view overflow-hidden rounded d-none">
            <div class="d-flex align-items-center  bg-light py-7 px-10 border border-light js-item-text" style="width : calc( 100% - 30px ); "></div>
            <div class="d-flex align-items-center w-30p bg-light py-7 px-10">
                <a href="javascript:;" class="text-gray-700 dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="far fa-ellipsis-v bsapp-fs-20 "></i>
                </a>
                <div class="dropdown-menu text-start" style="">
                    <a class="dropdown-item js-btn-field-edit text-gray-700  px-8" href="#"><span class="w-20p d-inline-block  text-center"><i class="fal fa-minus-circle"></i></span> <span> Edit</span></a>
                    <a class="dropdown-item js-btn-field-delete px-8 text-gray-700" href="javascript:;"><span class="w-20p d-inline-block  text-center"><i class="fal fa-user"></i></span> <span> Delete</span></a>
                </div>
            </div>
        </div>
        <div class="input-group w-100 border border-primary rounded js-item-edit overflow-hidden d-flex">
            <input type="text" class="flex-grow-1 outline-none border-0 shadow-none py-5 px-10 js-item-input" name="" value="" id="">
            <div class="input-group-append d-flex justify-content-between">
                <div class="js-item-field-save btn text-primary py-3 px-7 bsapp-fs-20">
                    <i class="fal fa-check-circle"></i>
                </div>
                <div class="js-item-field-cancel btn text-gray-700 py-3 px-7 bsapp-fs-20">
                    <i class="fal fa-minus-circle"></i>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="js-html-for-option-yes-no d-none">
    <div class="list-group-item w-100 js-item p-0 d-flex mb-12 border border-transparent rounded">
        <div class="d-flex align-items-center text-gray-700  py-7 w-30p js-grip-handle">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <div class="d-flex w-100 js-item-view bsapp-item-view overflow-hidden rounded" >
            <div class="d-flex align-items-center  bg-light py-7 px-10 border border-light js-item-text w-100" style="cursor: not-allowed;"  >
                <?php echo lang('yes')?>
            </div>
        </div>
        <div class="input-group d-none w-100 border border-primary rounded js-item-edit overflow-hidden">
            <input type="text" class="flex-grow-1 outline-none border-0 shadow-none py-5 px-10 js-item-input" value="" name="" value="Yes" >
        </div>
    </div>
    <div class="list-group-item w-100 js-item p-0 d-flex mb-12 border border-transparent rounded" style="cursor: not-allowed;"  >
        <div class="d-flex align-items-center text-gray-700  py-7 w-30p js-grip-handle">
            <i class="fas fa-grip-vertical"></i>
        </div>
        <div class="d-flex w-100 js-item-view bsapp-item-view overflow-hidden rounded" >
            <div class="d-flex align-items-center  bg-light py-7 px-10 border border-light js-item-text w-100"  >
                <?php echo  lang('no')?>
            </div>
        </div>
        <div class="input-group d-none w-100 border border-primary rounded js-item-edit overflow-hidden">
            <input type="text" class="flex-grow-1 outline-none border-0 shadow-none py-5 px-10 js-item-input" value="" name="" value="No" >
        </div>
    </div>
</div>


<div class="js-loading-shimmer d-none">
    <div class="bsapp-loading-shimmer mb-50">
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
            <div class="mb-15 w-50">
                <div></div>
            </div>
        </div>
    </div>
</div>

<!-- these containers act as markup for items to be used in  the js  :: end  -->








<div>
    <div>
        <?php require_once '../app/views/footernew.php'; ?>
        <script type="text/javascript">
            var JsData = {
                "client_id": "",
                "crm_note_id": "",
                "medical_note_id": "",
                "is_adult_new" : 0,
                "client_activity_id": ""
            };
            var ClientForm = {
                initNotify: function () {
                    var isRTL = false;
                    if ($("html").attr("dir") == 'rtl') {
                        isRTL = true;
                    }
                },
                initSelectCity: function () {
                    $(".cities-select").on("select2:unselect", function(e) {
                        debugger;
                    });

                    $('.cities-select').select2({
                        theme:"bsapp-dropdown",
                        placeholder: "<?php echo lang('select_city') ?>",
                        language: "<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_lang'] : 'he' ?>",
                        allowClear: true,
                        width: '100%',
                        ajax: {
                            url: '/office/action/CitiesSelect.php',
                            dataType: 'json'
                        }
                        ,
                        minimumInputLength: 3,
                    });
                },

                initSelect2Phone: function () {
                    $("#js-summary-phone:not(.select2-hidden-accessible)").select2({
                        tags: true,
                        createTag: function (tag) {
                            return {
                                id: tag.term,
                                text: tag.term,
                                isNew: true
                            };
                        },
                        dropdownParent: $("#js-client-popup"),
                        theme: "bsapp-dropdown bsapp-no-arrow w-100",
                        language: "<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_lang'] : 'he' ?>",
                        placeholder: '<?php echo lang('edit')?>',
                        minimumInputLength: 2,
                        allowClear: true,

                        ajax: {
                            url: '/office/action/getClientJsonFromPhone.php',
                            data: function (params) {
                                var query = {
                                    query: params.term,
                                    type: 'public'
                                }

                                // Query parameters will be ?search=[term]&type=public
                                return query;
                            },
                            processResults: function (data) {
                                var items = $.map($.parseJSON(data).results, user => ({
                                        name: user.name,
                                        firstName: user.firstName,
                                        lastName: user.lastName,
                                        id: user.id,
                                        img: user.img,
                                        phone: user.phone,
                                        parentId: user.parentId,
                                        email: user.email
                                    })
                                )
                                return {
                                    results: items
                                };
                            },
                        },
                        templateResult: formatState,

                        templateSelection: function (item) {
                            if (item.id == '') {
                                $item = $('<div class="d-flex justify-content-between align-items-center" id="phone-main-input"><div>' + "מספר נייד" + '</div></div>');
                            } else if (item.isNew) {
                                $item = $('<div class="d-flex justify-content-between align-items-center" id="phone-main-input"><div>' + item.text + '</div><div><div class="badge badge-info badge-pill"><?php echo  lang('new')?></div> </div> </div>');
                                $("#js-checkbox-for-minor").prop("disabled", false);
                            } else {
                                let phone = item.phone.replace(/^(\+972|\+91|\+1|\+44|0)0?/, '0');
                                $item = $('<div class="d-flex justify-content-between align-items-center" id="phone-main-input"><div>' + phone + '</div> </div>');
                            }
                            return $item;
                        }


                    }).on('select2:selecting', function (e) {
                        js_data_id = '';
                        js_data_has_parent = '';
                        $('#section-minor-step1').removeClass('d-none');
                        if (e.params.args.data.isNew) {
                            $('#js-minor-text-warning').text("* בשיוך מנוי יווצר קטין המשוייך ללקוח בוגר מהארכיון");
                            var selected = e.params.args.data;
                            js_data_has_parent = 1;
                            if (isValidPhone(selected.text)) {
                                $(".js-add-client").addClass("bsapp-js-disabled-o");
                                $("#js-summary-phone").removeClass("error");
                            } else {
                                $("#js-summary-phone").addClass("error");
                                console.log('not valid phone ', selected.text);
                            }
                        } else {
                            var $selected = $(e.params.args.data);
                            //prefill with the related values
                            js_data_id = $selected.attr("id");
                            js_data_has_parent = $selected.attr("parentId");
                            $('#js-minor-text-warning').text("<?php echo '* ' . lang('mobile_number_associated_assign_minor')?>");
                            $("#js-checkbox-for-minor").prop("disabled", true)
                            $("#js-summary-first-name").val($selected.attr('firstName')).attr("readonly", true);
                            $("#js-summary-last-name").val($selected.attr('lastName')).attr("readonly", true);
                            $(".js-summary-email").val($selected.attr('email')).attr("readonly", true);
                            $(".js-add-client").removeClass("bsapp-js-disabled-o").removeClass("js-restore-disabled");
                            $( "#reset-phone" ).even().removeClass( "d-none" );
                        }
                        if (js_data_has_parent == 0) {
                            if (js_data_id > 0) {
                                $("#js-parent-id").val(js_data_id);
                            }
                            $("#js-checkbox-for-minor").prop("checked", true);
                            $("#js-checkbox-for-minor").prop("readonly", true);
                            stage1SwitchToMinorForm();
                        } else {
                            $("#js-parent-id").val(-1);
                            $("section-minor-step1").removeClass('d-none');
                            stage1SwitchToRegularAddForm();
                        }
                    });
                },
                showSearchField: function (elem) {
                    $('#reset-phone').addClass('d-none');
                    $('#js-minor-text-warning').text("* בשיוך מנוי יווצר קטין המשוייך ללקוח בוגר מהארכיון");
                    $("#js-summary-first-name").val('').attr("readonly", false);
                    $("#js-summary-last-name").val('').attr("readonly", false);
                    $(".js-summary-email").val('').attr("readonly", false);
                    $("#js-summary-phone").val("")
                    $(".js-add-client").addClass("bsapp-js-disabled-o");
                    $("#js-checkbox-for-minor").prop("checked", false);
                    $("#js-checkbox-for-minor").prop("disabled", false)
                    $("[data-context='js-for-minor']").hideFlex();
                    $("#phone-main-input" ).replaceWith('<div class="d-flex justify-content-between align-items-center" id="phone-main-input"><div>' + "מספר נייד" + '</div></div>');

                },
                initSelectBasic: function () {
                    $(".js-select2:not(.select2-hidden-accessible)").select2({
                        minimumResultsForSearch: -1,
                        theme: "bsapp-dropdown"
                    });
                },
                initSortableFields: function () {
                    Sortable.create(js_field_items, {
                        animation: 100,
                        group: 'list-1',
                        draggable: '.list-group-item',
                        handle: '.js-grip-handle',
                        sort: true,
                        filter: '.sortable-disabled',
                        chosenClass: 'active',
                        onUpdate: function (evt) {
                            //ClientForm.updateFieldsOrder();
                        },
                    });
                },
                initSortableFieldsEdit: function () {
                    Sortable.create(js_item_options, {
                        animation: 100,
                        group: 'list-1',
                        draggable: '.list-group-item',
                        handle: '.js-grip-handle',
                        sort: true,
                        filter: '.sortable-disabled',
                        chosenClass: 'active',
                        onUpdate: function (evt) {
                            //ClientFrom.updateFieldsOrder();
                        },
                    });
                },
                init: function () {
                    this.initNotify();
                    this.initSelect2Phone();
                    this.initSelectCity();
                    this.initSelectBasic();
                    this.initSortableFields();
                    this.initSortableFieldsEdit();
                },

                getForm: function () {
                    //todo add local save inputs
                    // let saveFormInputs = $('#js-client-form-add').serialize();
                    $("#js-client-popup  .js-cf-fields").html($(".js-loading-shimmer").html());

                    var form_data = {
                        "fun": "GetOrCreateForm",
                        "type":$("#js-new-client-type").val(),
                        // "data": saveFormInputs
                    };
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/ajaxClientFormHTML.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            $("#js-client-popup  .js-cf-fields").html(response);
                            if ($('#js-checkbox-for-minor').prop("checked") == true) {
                                $('[data-context="js-for-major"]').hideFlex();
                                $("#more_details_title").hideFlex();
                                $('[data-context="js-for-minor"]').showFlex();
                            }
                            ClientForm.initSelectCity()
                        }
                    })
                },
                getFormEdit: function () {
                    $("#js-client-popup  .js-section-edit-fields").html($(".js-loading-shimmer").html());
                    $(".js-box-fields-add").hideFlex();
                    var form_data = {
                        "fun": "GetOrCreateForm",
                        "type": $("#js-new-client-type").val(),
                    };
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/ajaxClientFormEditHTML.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            $(".js-box-fields-add").showFlex();
                            $("#js-client-popup  .js-section-edit-fields").html(response);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            loader.hideFlex();
                            $.notify({
                                message: "התגלתה שגיאה בשיוך המנוי"
                            }, {
                                type: 'danger',
                                z_index: 2000,
                            });
                        }

                    });
                },
                addField: function () {
                    if ($(".js-form-field-add").parsley().validate() != true) {
                        return false;
                    }
                    loader = $("#js-modal-field-add .js-loader");
                    loader.showFlex();
                    var self = this;

                    var field_id = $("#js-modal-field-add .js-field-id").val();

                    let options;
                    let type = $("#js-cf-new-type").val();

                    let to_return = true;
                    if (type == 'multi' || type == 'multiCheck') {
                        if ($(".js-form-field-add .js-item").length == 0) {
                            $.notify({
                                message: "<?php echo lang('Please_add_least_one_option')?>"
                            }, {
                                type: 'danger',
                                z_index: 2000,
                            });
                            loader.hideFlex();
                            return false;
                        }
                        options = $("#js_item_options .js-item .js-item-input").map(function () {
                            if ($(this).val().trim() != '') {
                                $(this).parents(".js-item").removeClass("bsapp-js-error");
                                return $(this).val();
                            } else {
                                $(this).parents(".js-item").addClass("bsapp-js-error");
                                loader.hideFlex();
                                to_return = false;
                            }
                        }).get();
                    }

                    if (!to_return) {
                        $.notify({
                            message: 'Please enter text value for the options'
                        }, {
                            // settings
                            type: 'danger',
                            z_index: 2000,
                        });
                        return false;
                    }

                    if (field_id > 0) {
                        var form_data = {
                            "fun": "UpdateField",
                            "form_id": $("#js-client-form-id").val(),
                            "field_id": field_id,
                            "name": $("#js-cf-new-name").val(),
                            "options": options,
                        };
                    } else {
                        var form_data = {
                            "fun": "InsertNewField",
                            "form_id": $("#js-client-form-id").val(),
                            "name": $("#js-cf-new-name").val(),
                            "mandatory": 0,
                            "show": 1,
                            "type": type,
                            "options": options,
                        };
                    }
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/FormSettings.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            if (response.Status == 'Success'){
                                // clean the modal
                                $("#js_item_options").html('');
                                $("#js-cf-new-name").val('');
                                $(".js_item_options-stuff").hide();

                                self.getForm();
                                self.getFormEdit();
                                loader.hideFlex();
                                $("#js-modal-field-add").modal("hide")

                            } else if(response.Status == 'Error') {
                                loader.hideFlex();
                                $("#js-modal-field-add").modal("hide")
                                $.notify({
                                    message: response.Message
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        }
                    });
                },
                deleteField: function (field_id) {
                    let self = this;
                    let loader = $("#js-client-popup .js-loader");
                    loader.showFlex();

                    let form_data = {
                        "fun": "DeleteField",
                        "form_id": $("#js-client-form-id").val(),
                        "field_id": field_id,
                    };
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/FormSettings.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            if (response.Status == 'Success') {
                                $('#js-client-popup #js_field_items [js-field-id="' + field_id + '"]').remove();
                                self.getForm();
                                loader.hideFlex();
                                $.notify({
                                    message: '<?php echo lang('field_deleted_successfully') ?>'
                                }, {
                                    type: 'success',
                                    z_index: 2000,
                                });
                            } else if (response.Status == 'Error') {
                                loader.hideFlex();
                                $.notify({
                                    // options
                                    message: '<?php echo lang('error_deleting_field') ?>'
                                }, {
                                    // settings
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            loader.hideFlex();
                            $.notify({
                                message: "התגלתה שגיאה במחיקת שדה"
                            }, {
                                type: 'danger',
                                z_index: 2000,
                            });
                        }
                    });
                },
                updateFieldsDataOrder: function () {
                    let self = this;
                    if ($(".js-add-client").hasClass("js-restore-disabled")) {
                        $(".js-add-client").removeClass("js-restore-disabled").addClass("bsapp-js-disabled-o");
                    }
                    loader = $("#js-client-popup .js-loader");
                    loader.showFlex();
                    var fields_data = $("#js_field_items .js-item").map(function (x) {
                        var $item = $(this);
                        var single_field = {
                            "field_id": $item.attr("js-field-id"),
                            "name": $item.find(".js-item-input").val(),
                            "mandatory": ($item.find(".js-checkbox-mandatory").prop("checked")) ? 1 : 0,
                            "show": ($item.find(".js-checkbox-visible").prop("checked")) ? 1 : 0,
                            "order": x + 1
                        };
                        return single_field;
                    }).get();
                    var form_data = {
                        "fun": "UpdateFields",
                        "form_id": $("#js-client-form-id").val(),
                        "fields": fields_data,
                    };
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/FormSettings.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            loader.hideFlex();
                            $(".js-btn-hide-fields").click();
                            self.getForm();

                        }
                    });
                },
                getFixedPayments: function () {
                    var self = this;
                    loader = $("#js-client-popup .js-loader");
                    loader.showFlex();
                    var form_data = {
                        "fun": "fixedPayment",
                        "companyNum": <?php echo $companyNum; ?>,
                        "membership": $(".js-select2-membership").val(),
                    };
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/ajaxHTML.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            loader.hideFlex();
                            $(".js-html-fixed-payments").html(response);
                        }
                    });
                },
                getTotalPayment: function () {
                    loader = $("#js-client-popup .js-loader");
                    loader.showFlex();

                    var selected_items = $(".js-item-fixed-payment:checked").map(function () {
                        var $item = $(this).parents(".js-fixed-payment-item");
                        var single_field = {
                            "item_name": $item.find("label").text(),
                            "item_price": $item.find(".js-fixed-item-price").val(),
                            "id": $item.find(".js-fixed-item-price").attr("id")
                        };
                        return single_field;
                    }).get();

                    var form_data = {
                        "fun": "totalPayment",
                        "selected_items": selected_items,
                        "total_price": $(".js-item-price").val()
                    };
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/ajaxHTML.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            ClientForm.assignMembership(response);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $(".js-go-step-2").click();
                            loader.hideFlex();
                        }

                    });
                },
                assignMembership: function (getTotalPaymentResponse) {
                    let form_data = {
                        "fun": "assignMembership",
                        "clientId": JsData.client_id,
                        "itemId": $(".js-select2-membership").val(),
                        "startDate": $("#js-start-date").val(),
                        "end_date_calculation_type": $(".js-end-date-calculation").val(),
                        "endDate": $("#js-end-date").val(),
                        "itemPrice":  $(".js-item-price").val(),
                        "sales_id": 0,
                        "client_activity_id" : JsData.client_activity_id,
                    };
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/Client.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            if (response.Status == 'Success') {
                                if($("#js-save-and-close").val() == "close") {
                                    window.location.assign('<?php echo $site_url; ?>/office/ClientProfile.php?u=' + JsData.client_id);
                                }else {
                                    ClientForm.updateCreditCardsList();
                                    $(".js-summary-total").html(getTotalPaymentResponse);
                                    $(".js-selected-item-name").text($(".js-select2-membership  option:selected").text());
                                    $(".js-selected-item-price").text($(".js-item-price").val());
                                    $(".js-final-amount").val($(".js-final-pay-amount").val());
                                    let final_pay_amount = $(".js-final-pay-amount").val()
                                    $("#remainder-payment-amount").text(final_pay_amount);
                                    $(".js-final-amount").attr("max", final_pay_amount);
                                    JsData["client_activity_id"] = response.Message.ClientActivityId;
                                    ClientForm.loadPaymentFront();
                                    $(".js-summary-step-2").showFlex();
                                }

                            } else if (response.Status == 'Error') {
                                loader.hideFlex();
                                $.notify({
                                    message: response.Message
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            loader.hideFlex();
                            $.notify({
                                message: "התגלתה שגיאה בשיוך המנוי"
                            }, {
                                type: 'danger',
                                z_index: 2000,
                            });
                        }
                    });
                },
                loadPaymentFront: function(data=null) {
                    let showAlert = true
                    if(!data){
                         data = {
                            "tempId" : JsData.client_id,
                            "trueFinalInvoiceNum" : $(".js-selected-item-price").text(),
                            "typeDoc" : '<?php echo $groupNumber; ?>',
                            "companyNum": <?php echo $companyNum; ?>,
                        };
                        showAlert = false;
                    }
                    data['fun'] = 'addPaymentRow';
                    data['clientActivityId'] = JsData.client_activity_id;
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/ajaxHTML.php',
                        type: "POST",
                        data: data,
                        success: function (response) {
                            loader.hideFlex();
                            $("#step-3-summary").replaceWith(response);
                            let new_remainder_amount = parseFloat($("#remainder-payment-amount").text());
                            $(".js-final-amount").attr("max", new_remainder_amount.toFixed(2));
                            $("#myTabContent .active input").not("[name='customRadio']").val("");
                            $(".js-final-amount").val("");
                            if(showAlert){
                                $.notify({
                                    message: "עודכן תשלום"
                                }, {
                                    type: 'success',
                                    z_index: 2000,
                                })
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            loader.hideFlex();
                            if (jqXHR.status== 506) {
                                loader.hideFlex();
                                $.notify({
                                    message: JSON.parse(jqXHR.responseText).Message
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            } else {
                                $.notify({
                                    message: "תקלה בהוספת שורה"
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        }
                    });

                },
                addPayment: function(is_fully_payment=false) {
                    let type_payment = $('#myTab .active').attr("type-payment")
                    loader = $("#js-client-popup .js-loader");
                    loader.showFlex();
                    let form_data = {
                        "paymentValue" : $(".js-final-amount").val(),
                        "tempId" : JsData.client_id,
                        "clientActivityId" : JsData.client_activity_id,
                        "finalInvoiceNum" : $("#remainder-payment-amount").text(),
                        "trueFinalInvoiceNum" : $(".js-selected-item-price").text(),
                        "typeDoc" : '<?php echo $groupNumber; ?>',
                        "act" : type_payment,
                        "fun": "addPayment",
                    };

                    switch (type_payment) {
                        case "1":
                            break;
                        case "2":
                            form_data["checkAccount"] = $(".js-bank-account").val();
                            form_data["checkBank"] = $(".js-bank-account-name").val();
                            form_data["checkSnif"] = $(".js-bank-branch-number").val();
                            form_data["checkNumber"] = $(".js-cheque-number").val();
                            form_data["checkDate"] = $(".js-cheque-date").val();
                            break;
                        case "3":
                            form_data["token"]= $('.js-select-card:checked').val();    // token
                            form_data["paymentType"]= $('#paymentType').val();         // 1- רגיל 2-תשלומים 3-ךללא מסגרת
                            form_data["paymentNumber"]= $('#paymentNumber').val();    //תשלומים
                            form_data["clientId"]= JsData.client_id;       //מספר משתמש
                            form_data["membershipId"]= $(".js-select2-membership").val();       //מספר משתמש
                            break;
                        case "4":
                            form_data["bankDate"] = $(".js-deposit-date").val();
                            form_data["bankNumber"] = $(".js-deposit-conf-number").val();
                            break;
                        default:
                            $.notify({
                                message: "שגיאה בסוג התשלום"
                            }, {
                                type: 'danger',
                                z_index: 2000,
                            })
                            return;
                    }

                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/Client.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            if (response.Status === 'Success') {
                                let paymentData = response.Message
                                if (type_payment == 3) {
                                    $(".js-go-back-step").hideFlex();
                                    if(response.Message.iframe) {
                                        $('.js-add-cc-iframe').attr('src', response.Message.url);
                                        showIframe();
                                        window.paymentStatus = 'waiting';
                                        var checkPaymentStatus = setInterval(function () {
                                            switch (window.paymentStatus) {
                                                case 'success':
                                                    ClientForm.updateCreditCardsList();
                                                    ClientForm.loadPaymentFront(paymentData);
                                                    hideIframe(true);
                                                    break;
                                                case 'success_meshulam':
                                                    $('#list-payment').prepend(
                                                        "<div class='list-group-item d-flex justify-content-between payment-row'>" +
                                                        "<div class='flex'>" +
                                                        "<span>כרטיס אשראי</span>" +
                                                        "</div>" +
                                                        `<h6>₪<span>${$(".js-final-amount").val()}</span></h6>` +
                                                        "</div>"
                                                    )
                                                    let new_revenue_amount = parseFloat($("#total-revenue-amount").text().replace(",", ".")) + parseFloat($(".js-final-amount").val())
                                                    let new_payment_amount = parseFloat($("#remainder-payment-amount").text().replace(",", ".")) - parseFloat($(".js-final-amount").val())
                                                    $("#total-revenue-amount").text(new_revenue_amount.toFixed(2));
                                                    $("#remainder-payment-amount").text(new_payment_amount.toFixed(2));
                                                    hideIframe(true);
                                                    break;
                                                case 'error':
                                                    hideIframe(true);
                                                    $.notify({
                                                        message: 'תשלום לא הצליח, נסה שנית'
                                                    }, {
                                                        type: 'danger',
                                                        z_index: 2000,
                                                    });
                                                    break;
                                                case 'close':
                                                    hideIframe(true);
                                                    break;
                                                case 'stop':
                                                    clearInterval(checkPaymentStatus);
                                                    break;
                                                default:
                                                    break;
                                            }

                                        }, 5000, paymentData);
                                        return;
                                    }
                                }
                                if(is_fully_payment){
                                    ClientForm.savePayment(false, is_fully_payment);
                                }
                                ClientForm.loadPaymentFront(paymentData)
                            } else if (response.Status === 'Error') {
                                loader.hideFlex();
                                $.notify({
                                    message: response.Message
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            loader.hideFlex();
                            if (jqXHR.status== 506) {
                                loader.hideFlex();
                                $.notify({
                                    message: JSON.parse(jqXHR.responseText).Message
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            } else {
                                $.notify({
                                    message: "תקלה בתשלום"
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        }
                    });
                },
                loadPaymentRow: function() {
                    debugger;

                    let form_data = {
                        "tempId" : JsData.client_id,
                        "trueFinalInvoiceNum" : $(".js-selected-item-price").text(),
                        "typeDoc" : '<?php echo $groupNumber; ?>',
                    };


                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/ajaxHTML.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            loader.hideFlex();
                            $("#step-3-summary").replaceWith(response);
                            let new_remainder_amount =  parseFloat($("#remainder-payment-amount").text());
                            $(".js-final-amount").attr("max", new_remainder_amount.toFixed(2));
                            $("#myTabContent .active input").not("[name='customRadio']").val("");
                            $(".js-final-amount").val("");

                            $.notify({
                                message: " שורת התשלום נמחקה בהצלחה"
                            }, {
                                type: 'success',
                                z_index: 2000,
                            })
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            loader.hideFlex();
                            if (jqXHR.status === 506) {
                                loader.hideFlex();
                                $.notify({
                                    message: JSON.parse(jqXHR.responseText).Message
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            } else {
                                $.notify({
                                    message: "תקלה בהוספת שורת תשלום"
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        }
                    });
                },
                removePaymentRow: function(elem) {
                    let loader = $("#js-client-popup .js-loader");
                    loader.showFlex();
                    let form_data = {
                        "fun" : "deletePayment",
                        "tempListsId" : $(elem).closest('.payment-row').find("input").attr("id-payment"),
                        "groupNumber" : '<?php echo $groupNumber; ?>',
                        "clientId" : JsData.client_id,
                        "trueFinalInvoiceNum" : $(".js-selected-item-price").text(),
                    };
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/Client.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            if (response.Status === 'Success') {
                                ClientForm.loadPaymentFront(response.Message);
                            } else if (response.Status === 'Error') {
                                loader.hideFlex();
                                $.notify({
                                    message: response.Message
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            loader.hideFlex();
                            $.notify({
                                message: "מחיקת שורת תשלום נכשלה"
                            }, {
                                type: 'danger',
                                z_index: 2000,
                            });
                        }
                    });
                },
                //removeTempPayments: function () {
                //    let loader = $("#js-client-popup .js-loader");
                //    loader.showFlex();
                //    let form_data = {
                //        "fun" : "removeTempPayments",
                //        "typeDoc" : '<?php //echo $groupNumber; ?>//',
                //        "clientId" : JsData.client_id,
                //    };
                //    $.ajax({
                //        url: '<?php //echo $site_url; ?>///office/ajax/Client.php',
                //        type: "POST",
                //        data: form_data,
                //        success: function (response) {
                //            if (response.Status === 'Success') {
                //                ClientForm.loadPaymentFront(response.Message);
                //            } else if (response.Status === 'Error') {
                //                loader.hideFlex();
                //                $.notify({
                //                    message: response.Message
                //                }, {
                //                    type: 'danger',
                //                    z_index: 2000,
                //                });
                //            }
                //        },
                //        error: function (jqXHR, textStatus, errorThrown) {
                //            loader.hideFlex();
                //            $.notify({
                //                message: "מחיקת תשלומים זמניים נכשלה"
                //            }, {
                //                type: 'danger',
                //                z_index: 2000,
                //            });
                //        }
                //    });
                //
                //},
                savePayment: function(is_credit_card = false, is_fully_payment = false) {
                    loader = $("#js-client-popup .js-loader");
                    loader.showFlex();
                    let form_data = {
                        "fun" : "saveReceipt",
                        "finalInvoiceId" : JsData.client_activity_id,
                        "clientActivityId" : JsData.client_activity_id,
                        "groupNumber" : '<?php echo $groupNumber; ?>',
                        "clientId" : JsData.client_id,
                        "isCreditCard": is_credit_card,
                        "isFullyPayment": is_fully_payment
                    };

                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/Client.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            if (response.Status === 'Success') {
                                loader.hideFlex();
                                if(is_credit_card) {
                                    $.notify({
                                        message: "הופקה חשבונית עבור תשלום באשראי"
                                    }, {
                                        type: 'success',
                                        z_index: 2000,
                                    });
                                }
                                if(is_fully_payment) {
                                    $.notify({
                                        message: "הופקה חשבונית עבור התשלומים שבוצעו"
                                    }, {
                                        type: 'success',
                                        z_index: 2000,
                                    });
                                    window.location.assign('<?php echo $site_url; ?>/office/ClientProfile.php?u=' + JsData.client_id);
                                }
                            } else if (response.Status === 'Error') {
                                loader.hideFlex();
                                $.notify({
                                    message: response.Message
                                }, {
                                    type: 'danger',
                                    z_index: 2000,
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            loader.hideFlex();
                            $.notify({
                                message: "הוספת תשלום נכשלה"
                            }, {
                                type: 'danger',
                                z_index: 2000,
                            });
                        }
                    });
                },
                updateCreditCardsList : function () {
                    let form_data = {
                        "fun" : "getCreditCards",
                        "clientId" : JsData.client_id,
                        "companyNum": <?php echo $companyNum; ?>,
                    }
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/ajaxHTML.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            $(".js-payment-methods-cc-list").html(response);
                            // show add new credit card only if There are less than 5 different cards
                            if ($(".js-payment-methods-cc-list .js-cc-item").length >= 5) {
                                $('.js-add-new-card').hideFlex()
                            } else {
                                $('.js-add-new-card').showFlex()
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            $.notify({
                                message: "שגיאה בטעינת רשימת אשראי שמור"
                            }, {
                                type: 'danger',
                                z_index: 2000,
                            });
                        }
                    })

                },
                setupStep2: function ($elem) {
                    $(".js-data-help").html($elem.attr('js-data-help'));
                    var js_valid = $elem.attr('js-valid');
                    var js_valid_type = $elem.attr('js-valid-type');
                    var js_item_price = $elem.attr('js-item-price');
                    $(".js-select2-membership").val($elem.val());
                    $(".js-item-price").val(js_item_price);

                    var end_date = '';

                    if (js_valid_type == 1) {
                        var cur_timestamp = new Date().getTime();
                        var new_timestamp = cur_timestamp + (86400000 * js_valid);
                        end_date = new Date(new_timestamp);
                        console.log(end_date)
                    }
                    if (js_valid_type == 2) {
                        var cur_timestamp = new Date().getTime();
                        var new_timestamp = cur_timestamp + (86400000 * 7 * js_valid);
                        end_date = new Date(new_timestamp);
                        console.log(end_date)
                    }
                    if (js_valid_type == 3) {
                        var cur_timestamp = new Date().getTime();
                        var new_timestamp = cur_timestamp + (86400000 * 30 * js_valid);
                        end_date = new Date(new_timestamp);
                        console.log(end_date)
                    }
                    document.getElementById("js-start-date").valueAsDate = new Date();
                    document.getElementById("js-end-date").valueAsDate = end_date;
                    ClientForm.getFixedPayments();
                    if (js_valid == '0') {
                        $(".js-context-membership").hide();
                    } else {
                        $(".js-context-membership").show();
                    }
                    if ($elem.attr('js-data-type') == "4") {
                        $(".js-context-membership").hideFlex();
                    }
                }
            };
            $(document).ready(function () {

                $.fn.showFlex = function () {
                    this.removeClass("d-none").addClass("d-flex");
                };
                $.fn.hideFlex = function () {
                    this.removeClass("d-flex").addClass("d-none");
                };
                ClientForm.init();
                $("#js-client-popup").modal("show");

                //show and hide fields edit section :: begin
                $("body").on("click", "#js-client-popup .js-btn-field-edit-fields", function () {
                    if ($(".js-add-client").hasClass("bsapp-js-disabled-o") == true) {
                        $(".js-add-client").removeClass("bsapp-js-disabled-o").addClass("js-restore-disabled");
                    }
                    $(".js-section-show-fields").hideFlex();
                    $(".js-section-edit-fields").showFlex();
                    $(".js-btn-hide-fields").show();
                    $(".js-modal-dismiss").hide();
                    $(".js-box-next-prev").hideFlex();
                    $(".js-box-fields-add").showFlex();
                    $("#previous-step").addClass("d-none");
                    $(this).hide();
                });
                $("body").on("click", "#js-client-popup .js-btn-hide-fields", function () {
                    if ($(".js-add-client").hasClass("js-restore-disabled") == true) {
                        $(".js-add-client").removeClass("js-restore-disabled").addClass("bsapp-js-disabled-o");
                    }
                    $(".js-section-edit-fields").hideFlex();
                    $(".js-section-show-fields").showFlex();
                    $(".js-btn-field-edit-fields").show();
                    $(".js-modal-dismiss").show();
                    $(".js-box-next-prev").showFlex();
                    $(".js-box-fields-add").hideFlex();
                    $("#previous-step").removeClass("d-none");
                    $(this).hide();
                });
                //show and hide fields edit section :: end

                //functions for edit and update content :: begin
                $("body").on("click", "#js-client-popup .js-draggable-fields .js-btn-field-edit, #js-modal-field-add .js-draggable-fields .js-btn-field-edit", function () {
                    var $list_item = $(this).parents(".js-item");
                    var item_text = $list_item.find(".js-item-text span").text().trim();
                    $list_item.find(".js-item-view").hideFlex();
                    $list_item.find(".js-item-edit").showFlex();
                    $list_item.find(".js-item-input").val(item_text);

                });

                $("body").on("click", "#js-client-popup .js-btn-field-add", function () {
                    $("#js-modal-field-add .js-field-id").val('');
                    $("#js-modal-field-add").modal("show");
                    $('#js-cf-new-type').val('text').attr("readonly", false).trigger('change');
                });
                $("body").on("click", "#js-client-popup .js-draggable-fields .js-btn-field-edit-modal", function () {
                    var $list_item = $(this).parents(".js-item");
                    var field_id = $list_item.attr("js-field-id");
                    var item_text = $list_item.find(".js-item-text span").text().trim();

                    $('#js-modal-field-add').modal("show");

                    var field_type = $(this).attr("data-field-type");
                    var data_options = JSON.parse($(this).attr("data-field-options"));
                    $("#js-modal-field-add .js-field-id").val(field_id);
                    $("#js-cf-new-name").val(item_text);
                    $('#js-cf-new-type').val(field_type).attr("readonly", true).trigger('change');
                    if (field_type == 'multiCheck' || field_type == 'multi') {
                        var html = '';
                        for (i = 0; i < data_options.length; i++) {
                            var $options = $('<div>' + $(".js-html-for-option-added").html() + '</div>');

                            $options.find('.js-item-text').text(data_options[i]);
                            $options.find('.js-item-input').attr("value", data_options[i]);
                            html += $options.html();
                        }
                        $("#js_item_options").html(html);
                        $(".js_item_options-stuff").show();
                    } else if (field_type == 'checkbox') {
                        var html = '';
                        for (i = 0; i < data_options.length; i++) {
                            var $options = $('<div>' + $(".js-html-for-option-yes-no").html() + '</div>');

                            $options.find('.js-item-text').text(data_options[i]);
                            $options.find('.js-item-input').attr("value", data_options[i]);
                            html += $options.html();
                        }
                        $("#js_item_options").html(html);
                        $(".js_item_options-stuff.js-btn-option-add").hide();
                    } else {
                        $("#js_item_options").html('');
                        $(".js_item_options-stuff").hide();
                    }
                });
                $("body").on("click", "#js-client-popup .js-draggable-fields .js-item-field-save, #js-modal-field-add .js-draggable-fields .js-item-field-save", function () {
                    var $list_item = $(this).parents(".js-item");
                    var item_text = $list_item.find(".js-item-input").val().trim();
                    $list_item.find(".js-item-text").text(item_text);
                    $list_item.find(".js-item-edit").hideFlex();
                    $list_item.find(".js-item-view").showFlex();
                });
                $("body").on("click", "#js-client-popup .js-draggable-fields .js-item-field-cancel, #js-modal-field-add .js-draggable-fields .js-item-field-cancel", function () {
                    var $list_item = $(this).parents(".js-item");
                    $list_item.find(".js-item-edit").hideFlex();
                    $list_item.find(".js-item-view").showFlex();
                });
                $("body").on("click", "#js-client-popup #js_field_items .js-btn-field-delete", function () {
                    var $list_item = $(this).parents(".js-item");
                    var field_id = $list_item.attr("js-field-id");
                    ClientForm.deleteField(field_id);
                });
                $("body").on("click", "#js_item_options .js-btn-field-delete", function () {
                    var $list_item = $(this).parents(".js-item");
                    $list_item.remove();
                });
                //functions for edit and update content :: begin

                // checkbox for minor :: begin
                $("body").on("click", "#js-checkbox-for-minor", function () {
                    if ($(this).prop("checked") == true) {
                        $('[data-context="js-for-major"]').hideFlex();
                        $("#more_details_title").hideFlex();
                        $('[data-context="js-for-minor"]').showFlex();
                    } else {
                        $('[data-context="js-for-major"]').showFlex();
                        $("#more_details_title").showFlex();
                        $('[data-context="js-for-minor"]').hideFlex();
                    }
                });
                // checkbox for minor :: end


                $("body").on("click", ".js-btn-option-add", function () {
                    var item = $(".js-html-for-option-added").html();
                    $("#js_item_options").append(item);
                });

                $("body").on("click", "#previous-step", function () {
                    $("#wizard .js-previous").click();
                });


                $("body").on("click", "#js-step-0", function () {
                    if ($("#js-summary-first-name").val().trim() != "" && $("#js-summary-last-name").val().trim() != ""
                        && $('#phone-main-input > div').first().text() != 'מספר נייד')
                    {
                        $(".js-add-client").removeClass("bsapp-js-disabled-o");
                        $("#previous-step").removeClass("d-none");
                        $(".js-wizard-container").removeClass("d-none d-md-block");
                        $(".js-summary-container").addClass("bsapp-shrink-50")

                    } else {
                        $(".js-add-client").addClass("bsapp-js-disabled-o");
                        $("#previous-step").addClass("d-none");
                    }
                });


                $("body").on("keyup change", "#js-summary-first-name, #js-summary-last-name, #js-summary-phone, #js-summary-input", function () {
                    if ($(window).width() > 768) {
                        if ($("#js-summary-first-name").val().trim() != "" && $("#js-summary-last-name").val().trim() != ""
                            && $('#phone-main-input > div').first().text() != 'מספר נייד') {
                            $(".js-add-client").removeClass("bsapp-js-disabled-o");
                            $("#previous-step").removeClass("d-none");
                            $(".js-wizard-container").removeClass("d-none d-md-block");
                            $(".js-summary-container").addClass("bsapp-shrink-50")

                        } else {
                            $(".js-add-client").addClass("bsapp-js-disabled-o");
                            $("#previous-step").addClass("d-none");
                        }
                    }
                });

                $("body").on("keyup", ".js-final-amount", function () {
                    let total_amount = parseFloat($(".js-final-pay-amount").val());
                    let payment_amount = parseFloat($(this).val()) + parseFloat($("#total-revenue-amount").text()) ;
                    if (total_amount > payment_amount) {
                        $("#add-full-payment").hide();
                        $("#add-partial-payment").show();
                    } else if(total_amount < payment_amount) {
                        $("#add-full-payment").show();
                        $("#add-partial-payment").hide();

                    } else {
                        $("#add-full-payment").show();
                        $("#add-partial-payment").hide();
                    }
                });

                $('#js-cf-new-type').on('select2:selecting', function (e) {
                    if (e.params.args.data.id == 'multiCheck' || e.params.args.data.id == 'multi') {
                        $("#js_item_options").html('');
                        $(".js_item_options-stuff").show();
                    } else if (e.params.args.data.id == 'checkbox') {
                        $("#js_item_options").html($(".js-html-for-option-yes-no").html());
                        $(".js_item_options-stuff.js-btn-option-add").hide();
                    } else {
                        $("#js_item_options").html('');
                        $(".js_item_options-stuff").hide();
                    }
                });
            });

            $(".js-select2-membership").select2({
                minimumResultsForSearch: 1,
                theme: "bsapp-dropdown"
            }).on("select2:selecting", function (e) {
                var $elem = $(e.params.args.data.element);
                console.log('Selecting: ', $elem.attr('js-data-help'));
                ClientForm.setupStep2($elem);
            });

            $("body").on("change", ".js-end-date-calculation", function () {
                switch ($(".js-end-date-calculation").val()) {
                    case "4":
                        $("#start-end-membership-date").showFlex();
                        break;
                    case "5":
                        $("#start-end-membership-date").hideFlex();
                        break;
                }
            });

            $("body").on("change", "#js-end-date", function () {
                let time_start = Date.parse($("#js-start-date").val());
                let time_end = Date.parse($(this).val());
                let end_time_by_membership = getEndTimeStampMembership(time_start);
                if(time_end === end_time_by_membership) {
                    $("#membership-date-warning").hideFlex();
                } else if(time_end > end_time_by_membership) {
                    $("#membership-date-warning span").text("שים לב, תקופת המנוי שהגדרת ארוכה מתקופתו המקורית כפי שמוגדר במערכת")
                    $("#membership-date-warning").showFlex();
                }else {
                    $("#membership-date-warning span").text("שים לב, תקופת המנוי שהגדרת קצרה מתקופתו המקורית כפי שמוגדר במערכת")
                    $("#membership-date-warning").showFlex();
                }
            });

            $("body").on("click", "#add-full-payment", function () {
                $("#myTabContent> div:not(.active) input").prop('required',false);
                $("#myTabContent> .active input").prop('required',true);
                $("#js-tab-cc input").prop('required',false);
                if ($("#js-step-3").parsley().validate() === true) {
                    ClientForm.addPayment(true);
                }
            });

            $("body").on("click", "#add-partial-payment", function () {
                $("#myTabContent> div:not(.active) input").prop('required',false);
                $("#myTabContent> .active input").prop('required',true);
                $("#js-tab-cc input").prop('required',false);
                if ($("#js-step-3").parsley().validate() === true) {
                    ClientForm.addPayment();
                }
            });

            $("body").on("click", "#js-save-payment", function () {
                ClientForm.savePayment(false, true);

            });

            $("body").on("change", "#js-start-date", function () {
                let ts = Date.parse($(this).val());
                document.getElementById("js-end-date").valueAsDate = new Date(getEndTimeStampMembership(ts));
                $("#membership-date-warning").hideFlex();
            });

            $("body").on("click", "#js-open-textarea-medical", function () {
                $('#add-medical-note').toggle('1000');
                $("i", this).toggleClass("fa-plus fa-minus");
            });

            $("body").on("click", "#js-textarea-medical-delete", function () {
                $("#textarea-medical-note").val("")
                $('#js-open-textarea-medical').click();
            });

            $("body").on("click", "#js-open-textarea-crm", function () {
                $('#add-crm-note').toggle('1000');
                $("i", this).toggleClass("fa-plus fa-minus");
            });


            $("body").on("click", "#js-textarea-cdn-delete", function () {
                $("#textarea-crm-note").val("")
                $('#js-open-textarea-crm').click();
            });


            $("body").on("click", ".js-item-fixed-payment", function () {
            });

            $('body').on('change', '#paymentType', function () {
                if ($(this).val() != 1) {
                    $('.js-payment-number-block').show();
                    $('.js-payment-number-list').change();
                } else {
                    $('.js-payment-number-block').hide();
                }
            });

            var $form_wizard;

            //function for the wizard :: begin
            searchVisible = 0;
            transparent = true;
            var js_step_1 = false;
            $(document).ready(function () {

                // Code for the Validator
                // edit button to go to relevant step

                // Wizard Initialization
                $form_wizard = $('.js-add-client.wizard-card').bootstrapWizard({
                    'tabClass': 'nav nav-pills',
                    'nextSelector': '.js-next',
                    'previousSelector': '.js-previous',
                    'finishSelector': '#add-full-payment',
                    onNext: function (tab, navigation, index) {
                        //var $valid = $('.wizard-card form').valid();

                        /* if (!$valid) {
                         $validator.focusInvalid();
                         return false;
                         }*/

                        if (index == 1 && js_step_1 == false) {
                            $("#js-client-form-add").submit();
                            $("#content-step-1-form").removeClass("bsapp-js-disabled-o");
                            $(".js-next").text("<?php echo lang("add_subscription_desk") ?>");
                            $("#save-and-close").attr("step", "1");
                            return false;
                        }

                        if (index == 1) {
                            $(".js-next").text("מעבר לתשלום");
                            $("#save-and-close").attr("step", "2");
                            $(".js-go-step-1").showFlex();
                            fillInfoPreviousStep(index);
                            var $elem = $(".js-select2-membership option:selected");
                            ClientForm.setupStep2($elem);
                            $("#content-step-1-form").addClass("bsapp-js-disabled-o");
                            $("#section-minor-step1").addClass('d-none');

                        }
                        if (index == 2) {
                            if ($("#js-step-2").parsley().validate() != true) {
                                return false;
                            } else {
                                ClientForm.getTotalPayment();

                            }

                        }
                    },
                    onPrevious: function (tab, navigation, index) {

                        switch (index) {
                            case -1:
                                $(".js-wizard-container").addClass("d-none d-md-block");
                                $(".js-summary-container").removeClass("bsapp-shrink-50");
                                $(".js-add-client").addClass("bsapp-js-disabled-o");
                                $("#previous-step").addClass("d-none");
                                break
                            case 0:
                                $("#js-client-popup .js-btn-field-edit-fields").show();
                                fillInfoPreviousStep(index);
                                js_step_1 = false;
                                $(".js-go-step-1").click();
                                break;
                            case 1 :
                                $(".js-summary-step-2").hideFlex();
                                break;
                            case 2:
                                break
                        }


                    },
                    onInit: function (tab, navigation, index) {

                        //check number of tabs and fill the entire row
                        var $total = navigation.find('li').length;
                        $width = 100 / $total;
                        navigation.find('li').css('width', $width + '%');
                    },
                    onTabClick: function (tab, navigation, index) {

                        //var $valid = $('.wizard-card form').valid();
                        $valid = true;
                        if (!$valid) {
                            return false;
                        } else {
                            return true;
                        }
                    },
                    onTabShow: function (tab, navigation, index) {
                        var $total = navigation.find('li').length;
                        var $current = index + 1;
                        var $wizard = navigation.closest('.wizard-card');
                        // If it's the last tab then hide the last button and show the finish instead
                        if ($current >= $total) {
                            $($wizard).find('.js-next').hide();
                            $("#add-full-payment").show();
                            $("#save-and-close").hide();
                            $("#step-3-dropdown-toggle").showFlex();
                            $("#total-revenue-amount").text(0)

                        } else {
                            $("#save-and-close").show();
                            $($wizard).find('.js-next').show();
                            $($wizard).find('.js-finish').hide();
                            $("#step-3-dropdown-toggle").hideFlex();
                        }

                        //update progress
                        var move_distance = 100 / $total;
                        move_distance = move_distance * (index) + move_distance / 2;
                        $wizard.find($('.progress-bar')).css({width: move_distance + '%'});
                        //e.relatedTarget // previous tab

                        $wizard.find($('.wizard-card .nav-pills li.active a .icon-circle')).addClass('checked');
                    }
                });
                $("body").on("click", ".js-go-step-2", function () {
                    $(".wizard-navigation li:eq(1) a").click();
                    $(".js-summary-step-2").hideFlex();
                });
                $("body").on("click", ".js-go-step-1", function () {
                    $(".wizard-navigation li:eq(0) a").click();
                    js_step_1 = false;
                    $("#content-step-1-form").removeClass("bsapp-js-disabled-o");
                    $("#js-client-popup .js-btn-field-edit-fields").show();
                    $(".js-next").text("<?php echo lang("add_subscription_desk") ?>");
                    $(".js-summary-step-2").hideFlex();
                    $('#section-minor-step1').removeClass("d-none");
                    $("#section-minor-step2").hideFlex();
                    if ($("#js-checkbox-for-minor:checked").length > 0) {
                        $('#section-minor-step1').addClass("d-none");
                        $('#reset-phone').showFlex();
                    }
                    $(this).hideFlex();
                });

                // Prepare the preview for profile picture
                $("#wizard-picture").change(function () {
                    readURL(this);
                });
                $('[data-toggle="wizard-radio"]').click(function () {
                    wizard = $(this).closest('.wizard-card');
                    wizard.find('[data-toggle="wizard-radio"]').removeClass('active');
                    $(this).addClass('active');
                    $(wizard).find('[type="radio"]').removeAttr('checked');
                    $(this).find('[type="radio"]').attr('checked', 'true');
                });

                $('[data-toggle="wizard-checkbox"]').click(function () {
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('active');
                        $(this).find('[type="checkbox"]').removeAttr('checked');
                    } else {
                        $(this).addClass('active');
                        $(this).find('[type="checkbox"]').attr('checked', 'true');
                    }
                });

                $('.set-full-height').css('height', 'auto');

                $('#save-and-close').on("click", function () {
                    $("#js-save-and-close").val("close");
                    if($("#save-and-close").attr("step") == 1) {
                        $("#js-client-form-add").submit();
                    } else {
                        $("#add-subscription-desk").click();
                    }
                });

                $('#hide-iframe').on("click", function () {
                    hideIframe();
                    $(this).hideFlex();
                });

                $("#js-client-form-add").on("submit", function (e) {
                    e.preventDefault();

                    // validtion and minor status
                    let minor =false
                    //cheack minor
                    if ($("#js-checkbox-for-minor:checked").length > 0) {
                        minor = true;
                        $('.js-minor-first-name').prop("required", true);
                        $('.js-minor-last-name').prop("required", true);
                    } else{
                        $('.js-minor-relationship').removeAttr('required');
                    }
                    let phone_not_valid = $("#js-summary-phone").hasClass("error");
                    if ($(this).parsley().validate() !== true ) {
                        return false;
                    } else if(phone_not_valid) {
                        $("#phone-main-input").addClass("border border-danger rounded py-4 px-10");
                        return false;
                    }

                    loader = $("#js-client-popup .js-loader");
                    loader.showFlex();


                    var formData = new FormData(this);
                    var additional_data =[];
                    var form_data = {};

                    // create additional fielde data
                    formData.forEach(function (value, key) {
                        //Prepares the custom fields for the object
                        if(key.startsWith('form_id-')) {
                            let splitKey = key.split(',');
                            var obj = {};
                            splitKey.forEach(function (property) {
                                let temp = property.split('-');
                                obj[temp[0]] = temp[1];
                            });
                            obj["value"] = value;
                            additional_data.push(obj);
                        }else {
                            form_data[key] = value;
                        }
                    });
                    form_data["additional_data"] = additional_data;
                    if(!minor) {
                        form_data['first_name'] = $("#js-summary-first-name").val();
                        form_data['last_name'] = $("#js-summary-last-name").val();
                        form_data['phone'] = $('#phone-main-input > div').first().text();
                        form_data['phone_zone'] = $(".js-summary-code").val();
                        form_data['relationship'] = "";
                        form_data["is_minor"]= false;
                    } else {
                        form_data["is_minor"]= true;
                        //front preparation for stage 2
                        form_data['adult_first_name'] = $("#js-summary-first-name").val();
                        form_data['adult_last_name'] = $("#js-summary-last-name").val();
                        form_data['adult_phone'] = $('#phone-main-input > div').first().text();
                        form_data['adult_phone_zone'] = $(".js-summary-code").val();

                        $("#minor-first-name-step2").val($(".js-minor-first-name").val())
                        $("#minor-last-name-step2").val($(".js-minor-last-name").val())
                    }
                    if (JsData.client_id == "") {
                        form_data['fun'] = "addClient";
                    } else {
                        form_data['fun'] = "updateClient";
                        form_data['id'] = JsData.client_id;
                        form_data['user_id'] = JsData.user_id;
                        form_data['adult_client_id'] = JsData.adult_client_id;
                        form_data['crm_note_id'] = JsData.crm_note_id;
                        form_data['medical_note_id'] = JsData.medical_note_id;
                        form_data['is_adult_new'] = JsData.is_adult_new;
                    }

                    var json = JSON.stringify(form_data);
                    $.ajax({
                        url: '<?php echo $site_url; ?>/office/ajax/Client.php',
                        type: "POST",
                        data: form_data,
                        success: function (response) {
                            loader.hideFlex();
                            if (response.Status == 'Success') {
                                js_step_1 = true;
                                JsData["adult_client_id"] = response.Message.adult_client_id;
                                JsData["user_id"] = response.Message.user_id;
                                JsData["client_id"] = response.Message.client_id;
                                JsData["crm_note_id"] = response.Message.crm_note_id;
                                JsData["medical_note_id"] = response.Message.medical_note_id;
                                debugger//todo;

                                if ($("#js-parent-id").val() == -1) {
                                    JsData["is_adult_new"] = 1;
                                } else {
                                    JsData["is_adult_new"] = 0;
                                }

                                $.notify({
                                    message: response.Notify
                                }, {
                                    type: 'success',
                                    z_index: 2000,
                                });
                                if($("#js-save-and-close").val() == "close") {
                                    window.location.assign('<?php echo $site_url; ?>/office/ClientProfile.php?u=' + JsData.client_id);
                                }
                                $form_wizard.bootstrapWizard('next');
                                return true;
                            } else {
                                $.notify({
                                    message: response.Message
                                }, {
                                    // settings
                                    type: 'danger',
                                    z_index: 2000,
                                });
                                return false;
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            loader.hideFlex();
                            $.notify({
                                message: "שגיאה בהוספת משתמש"
                            }, {
                                // settings
                                type: 'danger',
                                z_index: 2000,
                            });
                        }
                    });
                });

            });

            function fillInfoPreviousStep(index) {
                let minor = $("#js-checkbox-for-minor:checked").length > 0;
                switch(index) {
                    case 0:
                        $("#js-client-popup .js-btn-field-edit-fields").show();
                        // $('#content-step-1-form').removeClass("disabled-section"); /// need add class : pointer-events: none; opacity: 0.4;
                        if(minor) {
                            $('#section-minor-step1').addClass("d-none");
                            $('#reset-phone').showFlex();
                            $('#section-minor-step2').addClass( "d-none")
                        }
                        break;
                    case 1:
                        $("#js-client-popup .js-btn-field-edit-fields").hide();
                        // $('#content-step-1-form').addClass("disabled-section"); /// need add class : pointer-events: none; opacity: 0.4;
                        if(minor) {
                            $('#section-minor-step1').removeClass("d-none");
                            $('#reset-phone').hideFlex();
                            $('#section-minor-step2').removeClass( "d-none");
                        } else {
                            $('#section-minor-step2').addClass( "d-none");
                        }
                        break;
                    case 2:
                        // code block
                        break;
                    default:

                }


            }

            //Function to show image before upload
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function debounce(func, wait, immediate) {
                var timeout;
                return function () {
                    var context = this, args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(function () {
                        timeout = null;
                        if (!immediate)
                            func.apply(context, args);
                    }, wait);
                    if (immediate && !timeout)
                        func.apply(context, args);
                };
            }
            ;
            //function for the wizard :: end


            $(document).on({
                'show.bs.modal': function () {
                    var zIndex = 1040 + (10 * $('.modal:visible').length);
                    $(this).css('z-index', zIndex);
                    setTimeout(function () {
                        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
                    }, 0);
                },
                'hidden.bs.modal': function () {
                    if ($('.modal:visible').length > 0) {
                        // restore the modal-open class to the body element, so that scrolling works
                        // properly after de-stacking a modal.
                        setTimeout(function () {
                            $(document.body).addClass('modal-open');
                        }, 0);
                    }
                }
            }, '.modal');
            /** functions :: begin **/

            function getEndTimeStampMembership(start_time) {

                let $selected_option = $(".js-select2-membership option:selected");
                let js_valid_type = $selected_option.attr('js-valid-type');
                let js_valid = $selected_option.attr('js-valid');
                let end_timestamp;
                switch (js_valid_type) {
                    case "1":
                        end_timestamp = start_time + (86400000 * js_valid);
                        break;
                    case "2":
                        end_timestamp = start_time + (86400000 * 7 * js_valid);
                        break;
                    case "3":
                        end_timestamp = start_time + (86400000 * 30 * js_valid);
                        break;
                }
                return end_timestamp

            }

            function formatState(state) {
                if (!state.firstName) {
                    if (!state.loading) {
                        if (isValidPhone(state.text))
                        var $state = $(
                            '<div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center"><div class="mie-8 w-40p h-40p rounded-circle border  d-flex align-items-center justify-content-center bsapp-plus-icon"><i class="fal fa-plus bsapp-fs-20" ></i></div>' + state.text + '</div><div class="badge badge-info badge-pill">' + lang('create_new_cal') + '</div></div>'
                        );
                        return $state;
                    } else {
                        return state.text;
                    }
                }
                var $state = $(
                    '<div class="d-flex justify-content-between align-items-center"><div class="d-flex align-items-center"> <img src="' + state.img + '" class="w-40p h-40p rounded-circle mie-8" /><div class="d-flex flex-column"><span> ' + state.name + ' </span><span class="bsapp-fs-14">' + state.phone + '</span><div></div></div>'
                );
                return $state;
            }

            function stage1SwitchToMinorForm() {
                $("#more_details_title").hideFlex();
                $('.js-minor-first-name').prop("required", true);
                $('.js-minor-last-name').prop("required", true);
                $('.js-minor-relationship').prop("required", true);
                $("[data-context='js-for-minor']").showFlex();
            }

            function stage1SwitchToRegularAddForm() {
                $("#js-summary-first-name").attr("readonly", false);
                $("#js-summary-last-name").attr("readonly", false);
                $('#reset-phone').hideFlex();
            }

            function showIframe(isMeshulam) {
                if(isMeshulam) {
                    $('#js-top-model').hideFlex();
                }
                $('#payment-iframe').showFlex();
                $('#previous-step').hideFlex();
                $('#hide-iframe').showFlex();
                $('.js-box-bottom-bar').hide();
                $('#js-step-3').hide();
                loader.hideFlex();
            }

            function hideIframe(close=false) {
                $('#js-top-model').showFlex();
                window.paymentStatus = close ? 'stop' : 'waiting';
                $('#previous-step').showFlex();
                $('#payment-iframe').hideFlex();
                $('.js-add-cc-iframe').attr('src','');
                $('.js-box-bottom-bar').show()
                $('#js-step-3').show();
                $('#hide-iframe').hideFlex();
            }

            //Checks if the phone number is valid
            function isValidPhone(phone){
                let isValidMobileRegx = /^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$/
                return phone.match(isValidMobileRegx)
            }


            $(document).ready(function () {
                $('.js-modal-dismiss').click(function () { //Close Button on Form Modal to trigger exit-modal Modal
                    $('#exit-modal').modal('show');
                });

                $('.js-confirm-closed').click(function () { //Waring Modal Confirm Close Button
                    $('#exit-modal').modal('hide'); //Hide exit-modal Modal
                    $('#js-client-popup').modal('hide'); //Hide Form Modal
                    //window.location.assign('<?php //echo $site_url; ?>///office/ClientProfile.php?u=' + JsData.client_id);
                });

            });


            $(document).on('keypress', '.select2-search__field', function(event) {

                const selectElement = $('.select2-container--open').first().closest("div").find( "select" );
                if (selectElement.hasClass('numbers-only')) {
                    if (event.keyCode < 48 || event.keyCode > 57) {
                        event.preventDefault();
                    }
                }
            });

            /** functions :: end **/


        </script>

