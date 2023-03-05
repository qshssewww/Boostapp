<?php
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/Classes/AppSettings.php';
require_once __DIR__ . '/Classes/Settings.php';
require_once __DIR__ . '/Classes/Company.php';
require_once __DIR__ . '/Classes/WhatsAppNotifications.php';
require_once __DIR__ . '/Classes/247SoftNew/ClientGoogleAddress.php';

if (Auth::guest()) {
    redirect_to('index.php');
}

if (Auth::check()):
    if (Auth::userCan('2')):

        $APIKey = ClientGoogleAddress::GOOGLE_API_KEY;

        $pageTitle = lang('system_settings_business');
        require_once '../app/views/headernew.php';

        $AffID = Auth::user()->id;
        $AffName = Auth::user()->display_name;
        $CompanyNum = Auth::user()->CompanyNum;
        $Supplier = Company::getInstance();
        $class_appSettings = AppSettings::getByCompanyNum($CompanyNum);
        $CompanySettingsDash = Settings::getSettings($CompanyNum);

        $Address = ClientGoogleAddress::getBusinessAddress($CompanyNum);

        include_once 'DocsInc/DocsParameters.php';

        $user = Auth::user();
        $allowdCC = in_array($user->email, [
            'dor@cloud247.co.il',
            'adi_bason1@walla.co.il',
            'moshe@boostapp.co.il',
            'ohadashtar@gmail.com',
            'alex@boostapp.co.il',
            'romi@boostapp.co.il',
            'anna@boostapp.co.il',
            'alexg@boostapp.co.il',
        ]);
        ?>


        <link href="<?php echo asset_url('css/vendor/imgpicker.css') ?>" rel="stylesheet">
        <link href="assets/css/bs-modal.css" rel="stylesheet">
        <link href="assets/css/fixstyle.css" rel="stylesheet">

        <!-- include summernote css/js -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>

        <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo $APIKey ?>&libraries=places&language=he"></script>
        <style>
            .card-header {
                cursor: pointer;
            }
        </style>
        <div class="row">

            <?php include("SettingsInc/RightCards.php"); ?>

            <div class="col-md-10 col-sm-12">


                <div class="tab-content">
                    <div class="tab-pane fade show active text-start" role="tabpanel" id="generalsettings">
                        <div class="card spacebottom">
                            <div class="card-header text-start d-flex justify-content-between">
                                <strong><?php echo lang('bookkeeping_details') ?></strong></div>
                            <div class="card-body">
                                <form action="GeneralSettingsPage" class="ajax-form clearfix" autocomplete="off">
                                    <input type="hidden" name="CompanyNum" value="1">
                                    <?php if (Auth::user()->role_id == 1) { ?>
                                        <div class="form-group">
                                            <label><?php echo lang('business_name') ?></label>
                                            <input type="text" class="form-control" name="CompanyName" id="CompanyName"
                                                   value="<?php echo htmlentities(@$Supplier->CompanyName); ?>">
                                        </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label><?php echo lang('commercial_name') ?></label>
                                        <input type="text" class="form-control" name="AppName" id="AppName"
                                               value="<?php echo htmlentities(@$Supplier->AppName); ?>">
                                    </div>
                                    <?php if (Auth::user()->role_id == 1) { ?>
                                        <div class="form-group">
                                            <label>ממתין ללא הגבלה</label>
                                            <input type="text" class="form-control"
                                                   value="<?= $class_appSettings->FreeWatingList == 1 ? lang('active') : lang('not_active') ?>"
                                                   disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>השלמת מנויים</label>
                                            <input type="text" class="form-control"
                                                   value="<?= $class_appSettings->MembershipType == 1 ? 'מכל הסוגים' : 'מאותו סוג' ?>"
                                                   disabled>
                                        </div>
                                        <div class="form-group">
                                            <label><?php echo lang('business_type') ?></label>
                                            <select name="BusinessType" class="form-control" disabled>
                                                <option value="" <?php if (@$Supplier->BusinessType == '') {
                                                    echo "selected";
                                                } ?>><?php echo lang('select_business_type') ?></option>
                                                <option value="2" <?php if (@$Supplier->BusinessType == '2') {
                                                    echo "selected";
                                                } ?>><?php echo lang('auth_prac') ?></option>
                                                <option value="3" <?php if (@$Supplier->BusinessType == '3') {
                                                    echo "selected";
                                                } ?>><?php echo lang('private_comp') ?></option>
                                                <option value="4" <?php if (@$Supplier->BusinessType == '4') {
                                                    echo "selected";
                                                } ?>><?php echo lang('public_comp') ?></option>
                                                <option value="5" <?php if (@$Supplier->BusinessType == '5') {
                                                    echo "selected";
                                                } ?>><?php echo lang('exam_prac') ?></option>
                                                <option value="6" <?php if (@$Supplier->BusinessType == '6') {
                                                    echo "selected";
                                                } ?>><?php echo lang('non_profit') ?></option>
                                                <option value="7" <?php if (@$Supplier->BusinessType == '7') {
                                                    echo "selected";
                                                } ?>><?php echo lang('gov_office') ?></option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label><?php echo lang('license_number') ?></label>
                                            <input type="text" class="form-control" name="CompanyId" id="CompanyId"
                                                   value="<?php echo @$Supplier->CompanyId ?>" readonly
                                                   onkeypress='validate(event)'>
                                        </div>

                                        <!--
                <div class="form-group">
                  <label>צבע עיקרי</label></br>
                  <input type="color" name="primaryColor" id="primaryColor" value="<?php //echo !empty($Supplier->primaryColor) ? $Supplier->primaryColor : '';  ?>">
                </div> -->
                                        <hr>

                                        <div class="form-group">
                                            <label>% <?php echo lang('withholding_tax') ?></label>
                                            <input type="number" class="form-control" name="NikuyMsBamakor"
                                                   id="NikuyMsBamakor" value="<?php echo @$Supplier->NikuyMsBamakor ?>"
                                                   onkeypress='validate(event)' readonly>
                                        </div>

                                        <div class="form-group">
                                            <label><?php echo lang('withholding_tax_date') ?></label>
                                            <input type="date" class="form-control" name="NikuyMsBamakorDate"
                                                   id="NikuyMsBamakorDate"
                                                   value="<?php echo @$Supplier->NikuyMsBamakorDate ?>" readonly>
                                        </div>

                                        <hr>
                                    <?php } ?>
                                    <?php
                                    if (@$Supplier->BrandsMain != '0' && @$Supplier->MainAccounting == '1') {
                                        $TrueCompanyNum = $Supplier->BrandsMain;
                                    } else {
                                        $TrueCompanyNum = Auth::user()->CompanyNum;
                                    }
                                    $DocIdCount = DB::table('docs')->where('CompanyNum', '=', $TrueCompanyNum)->count();
                                    ?>
                                    <?php if (Auth::user()->role_id == 1) { ?>
                                        <div class="form-group">
                                            <label><?php echo lang('auto_invoice_issue') ?></label>
                                            <select name="CpaTypes"
                                                    class="form-control" <?php echo (!$allowdCC) ? 'disabled' : '' ?>>
                                                <option value="0" <?php if (@$Supplier->CpaType == 0) {
                                                    echo "selected";
                                                } ?>><?php echo lang('by_payment_date') ?></option>
                                                <option value="1" <?php if (@$Supplier->CpaType == 1) {
                                                    echo "selected";
                                                } ?>><?php echo lang('receipt_issue') ?></option>
                                            </select>
                                        </div>
                                    <?php } ?>
                                    <hr>

                                    <div class="form-group">
                                        <label><?php echo lang('website_calendar_link') ?></label>
                                        <input type="text" class="form-control" name="StudioUrl" id="StudioUrl"
                                               value="<?php echo get_appboostapp_domain(); ?>/Rest.php?StudioUrl=<?php echo @$Supplier->StudioUrl ?>"
                                               disabled>
                                    </div>


                                    <div class="form-group">
                                        <label><?php echo lang('studio_url_api') ?></label>
                                        <input type="text" class="form-control" name="StudioUrl" id="StudioUrl"
                                               value="<?php echo @$Supplier->StudioUrl ?>" disabled>
                                    </div>

                                    <hr>

                                    <?php
                                    $GooglePlayLink = 'https://play.google.com/store/apps/details?id=com.connect_computer.boostnew&gl=IL';
                                    $AppStoreLink = 'https://apps.apple.com/us/app/boost-%D7%91%D7%95%D7%A1%D7%98/id1479519489';

                                    if (@$Supplier->GooglePlayLink != '') {
                                        $GooglePlayLink = $Supplier->GooglePlayLink;
                                    }

                                    if (@$Supplier->AppStoreLink != '') {
                                        $AppStoreLink = $Supplier->AppStoreLink;
                                    }

                                    ?>

                                    <div class="form-group">
                                        <label><?php echo lang('dynamic_app_link') ?></label>
                                        <input type="text" class="form-control" name="GooglePlayLink"
                                               value="<?php echo get_newboostapp_domain() ?>/AppLink.php?StudioUrl=<?php echo @@$Supplier->StudioUrl ?>"
                                               disabled>
                                    </div>


                                    <div class="form-group">
                                        <label><?php echo lang('app_google') ?></label>
                                        <input type="text" class="form-control" name="GooglePlayLink"
                                               id="GooglePlayLink" value="<?php echo @$GooglePlayLink ?>" disabled>
                                    </div>


                                    <div class="form-group">
                                        <label><?php echo lang('app_apple') ?></label>
                                        <input type="text" class="form-control" name="AppStoreLink" id="AppStoreLink"
                                               value="<?php echo @$AppStoreLink ?>" disabled>
                                    </div>


                                    <hr>


                                    <div class="form-group">
                                        <button type="submit"
                                                class="btn btn-success btn-lg"><?php echo lang('update') ?>
                                        </button>

                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane fade text-start" role="tabpanel" id="contactinfo">
                        <div class="card spacebottom">
                            <div class="card-header text-start"><strong><?php echo lang('contact_details') ?></strong>
                            </div>
                            <div class="card-body">
                                <form action="ContactInfoPage" class="ajax-form clearfix" autocomplete="off">
                                    <input type="hidden" name="CompanyNum" value="1">

                                    <div class="form-group">
                                        <label><?php echo lang('address') ?></label>
                                        <input type="text" class="form-control" name="PlaceString" id="PlaceString"
                                               value="<?php echo($Address->address ?? '') ?>"
                                               placeholder="<?php echo lang('address') ?>">
                                        <input type="text" class="form-control d-none" name="PlaceId" id="PlaceId"
                                               value="<?php echo($Address->place_id ?? '') ?>"
                                               data-string="<?php echo($Address->address ?? '') ?>">
                                        <input type="text" class="form-control d-none" name="PlaceLatLng"
                                               id="PlaceLatLng"
                                               value="<?php echo($Address->lat_lng ?? '') ?>">
                                        <input type="text" class="form-control d-none" name="PlaceCity" id="PlaceCity"
                                               value="<?php echo($Address->city_id ?? '') ?>">
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('zip_code') ?></label>
                                        <input type="tel" class="form-control" name="Zip" id="Zip"
                                               value="<?php echo @$Supplier->Zip ?>" onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('mailbox') ?></label>
                                        <input type="tel" class="form-control" name="POBox" id="POBox"
                                               value="<?php echo @$Supplier->POBox ?>" onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('settings_phone') ?></label>
                                        <input type="tel" class="form-control" name="ContactMobile" id="ContactMobile"
                                               value="<?php echo @$Supplier->ContactMobile ?>"
                                               onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('settings_telephone') ?></label>
                                        <input type="tel" class="form-control" name="ContactPhone" id="ContactPhone"
                                               value="<?php echo @$Supplier->ContactPhone ?>"
                                               onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('show_phone_to_clients') ?></label>
                                        <input type="tel" class="form-control" name="PhoneClient" id="PhoneClient"
                                               value="<?php echo @$Supplier->PhoneClient ?>"
                                               onkeypress='validate(event)'>
                                    </div>


                                    <div class="form-group">
                                        <label><?php echo lang('fax_single') ?></label>
                                        <input type="tel" class="form-control" name="ContactFax" id="ContactFax"
                                               value="<?php echo @$Supplier->ContactFax ?>"
                                               onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('email') ?></label>
                                        <input type="email" class="form-control" name="Email" id="Email"
                                               value="<?php echo @$Supplier->Email ?>">
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('site') ?></label>
                                        <input type="text" class="form-control" name="WebSite" id="WebSite"
                                               value="<?php echo @$Supplier->WebSite ?>">
                                    </div>

                                    <div class="form-group" style="display: none;">
                                        <label><?php echo lang('business_facebook_code') ?></label>
                                        <input type="text" class="form-control" name="FaceBookId2" id="FaceBookId"
                                               value="<?php echo @$Supplier->FaceBookId ?>"
                                               onkeypress='validate(event)'>
                                    </div>
                                    <input type="hidden" name="FaceBookId" value="<?php echo @$Supplier->FaceBookId ?>">
                                    <?php if (Auth::user()->role_id == 1) { ?>
                                        <div class="form-group">
                                            <label><?php echo lang('clearing_page_link') ?></label>
                                            <input type="text" class="form-control" name="PaySiteUrl" id="PaySiteUrl"
                                                   value="<?php echo @$Supplier->PaySiteUrl ?>" disabled>
                                        </div>
                                    <?php } ?>
                                    <hr>
                                    <div class="form-group">
                                        <button type="submit"
                                                class="btn btn-success btn-lg"><?php echo lang('update') ?></button>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>


                    <div class="tab-pane fade text-start" role="tabpanel" id="docsnum">
                        <div class="card spacebottom">
                            <div class="card-header text-start">
                                <strong><?php echo lang('settings_main_docs') ?></strong></div>
                            <div class="card-body">
                                <div class="alertb alert-danger" role="alert">
                                    <u><?php echo lang('settings_doc_notice') ?></u>
                                </div>
                                <br>
                                <form action="DocsNumPage" class="ajax-form clearfix" autocomplete="off">
                                    <input type="hidden" name="CompanyNum" value="1">


                                    <?php

                                    //// בדיקת סניפים
                                    if (@$Supplier->BrandsMain != '0' && @$Supplier->MainAccounting == '1') {
                                        $TrueCompanyNum = $Supplier->BrandsMain;
                                    } else {
                                        $TrueCompanyNum = Auth::user()->CompanyNum;
                                    }

                                    $DocsTables = DB::table('docstable')->where('CompanyNum', '=', $TrueCompanyNum)->where('Status', '=', '0')->get();
                                    foreach ($DocsTables as $DocsTable) {

                                        $DocsCountGets = DB::table('docs')->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', $TrueCompanyNum)->orderBy('TypeNumber', 'DESC')->orderBy('id', 'DESC')->first();
                                        if (@$DocsCountGets->TypeNumber == '') {
                                            $DocIdCount = '0';
                                            $Auto_increment = $DocsTable->TypeNumber;
                                        } else {
                                            $DocIdCount = '1';
                                            $Auto_increment = $DocsCountGets->TypeNumber + 1;
                                        }
                                        ?>
                                        <div class="form-group">
                                            <label><?php echo $DocsTable->TypeTitleSingle; ?></label>
                                            <input type="text" class="form-control"
                                                   name="DocNumber<?php echo $DocsTable->id; ?>"
                                                   id="DocNumber<?php echo $DocsTable->id; ?>"
                                                   value="<?php echo @$Auto_increment; ?>" <?php if (@$DocIdCount != '0') {
                                                echo "readonly";
                                            } ?> onkeypress='validate(event)'>
                                        </div>
                                    <?php } ?>

                                    <hr>
                                    <div class="form-group">
                                        <button type="submit"
                                                class="btn btn-success btn-lg"><?php echo lang('update') ?></button>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>


                    <style>

                        .icon-edit {
                            height: 16px;
                            width: 16px;
                            display: inline-block;
                            background: url('../img/icon-edit.png');
                            opacity: 0.5;
                        }

                        div:hover > .icon-edit {
                            opacity: 1;
                        }

                        .avatar-container {

                        }

                        #avatar {
                            width: 100px;
                            margin-left: 15px;
                        }

                        .edit-avatar {
                            float: right;
                            margin: -1px 0 0 -38px;
                            position: relative;
                            padding: 4px 6px !important;
                        }

                        a[data-target*=activeClientsCountModal]:hover {
                            color: #00c736;
                        }

                        #activeClientsCountModal table td {
                            padding: 0.5rem;
                        }

                        #activeClientsCountModal thead {
                            color: #AFAFAF;
                        }
                    </style>

                    <div class="tab-pane fade text-start" role="tabpanel" id="docsdesign">
                        <div class="card spacebottom">
                            <div class="card-header text-start">
                                <strong><?php echo lang('settings_docs_style') ?></strong></div>
                            <div class="card-body">


                                <form action="DesignDocumentLog" class="ajax-form clearfix" autocomplete="off">
                                    <input type="hidden" name="CompanyNum" value="1">

                                    <div class="form-group">
                                        <label><?php echo lang('logo_for_docs') ?></label>
                                    </div>

                                    <div class="avatar-container">
                                        <button type="button" class="btn btn-light edit-avatar"
                                                data-ip-modal="#headerModal" title="<?php echo lang('edit_logo') ?>"><i
                                                    class="icon-edit"></i></button>
                                        <?php if (@$Supplier->DocsCompanyLogo != '') { ?>
                                            <img src="files/<?php echo @$Supplier->DocsCompanyLogo; ?>" id="avatar">
                                        <?php } else { ?>
                                            <img src="/office/files/logo/smallDefault.png" id="avatar">
                                        <?php } ?>
                                    </div>

                                    <hr>


                                    <div class="form-group">
                                        <label><?php echo lang('docs_desing_color') ?></label>
                                        <div id="SetDocBackPreview"
                                             style="background-color: <?php echo @$Supplier->DocsBackgroundColor; ?>;width:50px;height:10px;display:inline-block;"></div>
                                        <select class="form-control" name="DocsBackgroundColor" id="DocsBackgroundColor"
                                                onchange="dsfsd()">
                                            <option value="#e10025" <?php if ($Supplier->DocsBackgroundColor == '#e10025') {
                                                echo "selected";
                                            } ?>><?php echo lang('red_color') ?></option>
                                            <option value="#bd1a2f" <?php if ($Supplier->DocsBackgroundColor == '#bd1a2f') {
                                                echo "selected";
                                            } ?>><?php echo lang('dark_red') ?></option>
                                            <option value="#f19218" <?php if ($Supplier->DocsBackgroundColor == '#f19218') {
                                                echo "selected";
                                            } ?>><?php echo lang('orange_color') ?></option>
                                            <option value="#f8b43d" <?php if ($Supplier->DocsBackgroundColor == '#f8b43d') {
                                                echo "selected";
                                            } ?>><?php echo lang('yellow_color') ?></option>
                                            <option value="#48AD42" <?php if ($Supplier->DocsBackgroundColor == '#48AD42') {
                                                echo "selected";
                                            } ?>><?php echo lang('green_color') ?></option>
                                            <option value="#648426" <?php if ($Supplier->DocsBackgroundColor == '#648426') {
                                                echo "selected";
                                            } ?>><?php echo lang('dark_green_color') ?></option>
                                            <option value="#17a2b8" <?php if ($Supplier->DocsBackgroundColor == '#17a2b8') {
                                                echo "selected";
                                            } ?>><?php echo lang('turquoise_color') ?></option>
                                            <option value="#98D0C3" <?php if ($Supplier->DocsBackgroundColor == '#98D0C3') {
                                                echo "selected";
                                            } ?>><?php echo lang('turquoise_green') ?></option>
                                            <option value="#2b71b9" <?php if ($Supplier->DocsBackgroundColor == '#2b71b9') {
                                                echo "selected";
                                            } ?>><?php echo lang('blue_color') ?></option>
                                            <option value="#2B619D" <?php if ($Supplier->DocsBackgroundColor == '#2B619D') {
                                                echo "selected";
                                            } ?>><?php echo lang('dark_blue_color') ?></option>
                                            <option value="#e83e8c" <?php if ($Supplier->DocsBackgroundColor == '#e83e8c') {
                                                echo "selected";
                                            } ?>><?php echo lang('pink_color') ?></option>
                                            <option value="#b79bf7" <?php if ($Supplier->DocsBackgroundColor == '#b79bf7') {
                                                echo "selected";
                                            } ?>><?php echo lang('purple_color') ?></option>
                                            <option value="#6610f2" <?php if ($Supplier->DocsBackgroundColor == '#6610f2') {
                                                echo "selected";
                                            } ?>><?php echo lang('dark_purple_color') ?></option>
                                            <option value="#343a40" <?php if ($Supplier->DocsBackgroundColor == '#343a40') {
                                                echo "selected";
                                            } ?>><?php echo lang('grey_color') ?></option>
                                            <option value="#000000" <?php if ($Supplier->DocsBackgroundColor == '#000000') {
                                                echo "selected";
                                            } ?>><?php echo lang('black_color') ?></option>
                                        </select>
                                    </div>
                                    <!--                    <hr>-->

                                    <?php

                                    $DocsDetailDBs = DB::table('docsdetails')->where('EditTable', '=', '1')->where('CompanyNum', '=', Auth::user()->CompanyNum)->orderBy('OrderBy', 'ASC')->get();
                                    if (Auth::user()->role_id == 1) {
                                        foreach ($DocsDetailDBs as $DocsDetailDB) { ?>
                                            <div class="form-group">
                                                <label><?php echo lang('view_in_printed_business_settings') ?><?php echo @$DocsDetailDB->Title; ?></label>
                                                <select class="form-control"
                                                        name="DocChooseTd<?php echo @$DocsDetailDB->id; ?>"
                                                        id="DocChooseTd<?php echo @$DocsDetailDB->id; ?>">
                                                    <option value="0" <?php if (@$DocsDetailDB->Status == '0') {
                                                        echo "selected";
                                                    } ?>><?php echo lang('yes') ?></option>
                                                    <option value="1" <?php if (@$DocsDetailDB->Status == '1') {
                                                        echo "selected";
                                                    } ?>><?php echo lang('no') ?></option>
                                                </select>
                                            </div>

                                            <?php
                                        }
                                    }
                                    ?>


                                    <!--<hr>	-->
                                    <div class="form-group">
                                        <button type="submit"
                                                class="btn btn-success btn-lg"><?php echo lang('update') ?>
                                        </button>
                                        <div class="card spacebottom"role="tabpanel">
                                            <div class="card-header text-start">
                                                <strong>
                                                    <?php echo lang('permanent_notes') ?>
                                                </strong>
                                            </div>
                                            <div class="card-body">
                                                <form action="DocsRemakrsPage" class="ajax-form clearfix" autocomplete="off">
                                                                             <input type="hidden" name="CompanyNum" value="1">

                                                                             <?php

                                                                             $DocsTables = DB::table('docstable')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('Status', '=', '0')->get();
                                                                             foreach ($DocsTables as $DocsTable) {

                                                                                 ?>
                                                                                 <div class="form-group">
                                                                                     <label><?php echo $DocsTable->TypeTitleSingle; ?></label>
                                                                                     <textarea class="form-control summernote"
                                                                                               name="DocNotes<?php echo $DocsTable->id; ?>"
                                                                                               rows="5"><?php echo @$DocsTable->DocsRemarks; ?></textarea>
                                                                                 </div>


                                                                             <?php } ?>
                                                                             <hr>
                                                                     </div>
                                                </form>
                                            </div>
                                            <button type="submit" class="btn btn-success btn-lg">
                                                <?php echo lang('update') ?>
                                            </button>
                                         </div>

                            </div>
                            </form>
                            <script>
                                function dsfsd() {
                                    var x = document.getElementById("DocsBackgroundColor").value;
                                    document.getElementById("SetDocBackPreview").style.backgroundColor = x;
                                }
                            </script>

                        </div>
                    </div>




                    <div class="tab-pane fade text-start" role="tabpanel" id="accountmanager">
                        <div class="card spacebottom">
                            <div class="card-header text-start"><strong><?php echo lang('auto_reports') ?></strong>
                            </div>
                            <div class="card-body">
                                <form action="AccountManagerPage" class="ajax-form clearfix" autocomplete="off">
                                    <input type="hidden" name="CompanyNum" value="1">
                                    <div class="alertb alert-dark" role="alert">
                                        <?php echo lang('auto_reports_notice') ?>
                                    </div>


                                    <div class="form-group">
                                        <label><?php echo lang('auto_reports_email') ?></label>
                                        <input type="email" class="form-control" name="CpaEmail" id="CpaEmail"
                                               value="<?php echo @$Supplier->CpaEmail ?>">
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('auto_reports_copy') ?></label>
                                        <input type="email" class="form-control" name="CpaEmailCopy" id="CpaEmailCopy"
                                               value="<?php echo @$Supplier->CpaEmailCopy ?>">
                                    </div>

                                    <hr>
                                    <div class="form-group">
                                        <button type="submit"
                                                class="btn btn-success btn-lg"><?php echo lang('update') ?></button>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>

                    <div class="tab-pane fade text-start" role="tabpanel" id="creditcard">
                        <div class="card spacebottom">
                            <div class="card-header text-start">
                                <strong><?php echo lang('clearing_cc_terminal') ?></strong></div>
                            <div class="card-body">
                                <script>
                                    $(document).ready(function () {
                                        var TypeShva = $('select[name="TypeShva"]');
                                        var SubmitMasof = $('#SubmitMasof');
                                        TypeShva.on("change", function () {
                                            $('.js-payment-system-settings').hide();
                                            $('.js-payment-system-settings[data-type="' + $(this).val() + '"]').show();
                                        });

                                        SubmitMasof.on('click', function () {
                                            var data = {
                                                TypeShva: TypeShva.val(),
                                                action: 'updateMasof'
                                            };

                                            $('.js-payment-system-settings[data-type=' + TypeShva.val() + ']').find('input, select').map(function (i, el) {
                                                if (el.value) {
                                                    data[el.name] = el.value;
                                                }
                                                return {
                                                    name: el.name,
                                                    value: el.value
                                                }
                                            });

                                            $.ajax({
                                                method: 'POST',
                                                data: data,
                                                url: BeePOS.options.ajaxUrl
                                            }).done(function (response) {
                                                if (response.message) {
                                                    $.notify(
                                                        {icon: 'fas fa-check-circle', message: response.message},
                                                        {type: 'success'}
                                                    );
                                                } else {
                                                    $.notify(
                                                        {
                                                            icon: 'fas fa-times-circle',
                                                            message: '<?php echo lang('error_oops_something_went_wrong') ?>'
                                                        },
                                                        {type: 'danger'}
                                                    );
                                                }
                                            });
                                        })
                                    });
                                </script>

                                <?php
                                require_once __DIR__ . '/services/payment/PaymentTypeEnum.php';
                                ?>


                                <div class="form-group">
                                    <label><?php echo lang('terminal_type') ?></label>
                                    <select name="TypeShva"
                                            class="form-control" <?php echo (!$allowdCC) ? 'disabled' : ''; ?>>
                                        <option value="<?= PaymentTypeEnum::TYPE_YAAD ?>" <?php if ($Supplier->TypeShva == PaymentTypeEnum::TYPE_YAAD) {
                                            echo "selected";
                                        } ?>><?php echo lang('direct_terminal') ?></option>
                                        <option value="<?= PaymentTypeEnum::TYPE_MESHULAM ?>" <?php if ($Supplier->TypeShva == PaymentTypeEnum::TYPE_MESHULAM) {
                                            echo "selected";
                                        } ?>><?php echo lang('meshulam_terminal') ?></option>
                                        <option value="<?= PaymentTypeEnum::TYPE_TRANZILA ?>" <?php if ($Supplier->TypeShva == PaymentTypeEnum::TYPE_TRANZILA) {
                                            echo "selected";
                                        } ?>><?php echo lang('tranzila_terminal') ?></option>
                                    </select>
                                </div>

                                <div class="js-payment-system-settings"
                                     data-type="<?= PaymentTypeEnum::TYPE_TRANZILA ?>"
                                     style="display: <?= ($Supplier->TypeShva == PaymentTypeEnum::TYPE_TRANZILA) ? 'block' : 'none;' ?>">
                                    <div class="form-group">
                                        <label><?php echo lang('tranzila_terminal_name') ?></label>
                                        <input type="text" class="form-control" name="TranzilaTerminal"
                                               id="TranzilaTerminal"
                                               value="<?php echo $Supplier->TranzilaTerminal ?>" <?php echo (!$allowdCC) ? 'readonly' : ''; ?>>
                                    </div>
                                </div>

                                <div id="TypeShva1" class="js-payment-system-settings"
                                     data-type="<?= PaymentTypeEnum::TYPE_MESHULAM ?>"
                                     style="display: <?= ($Supplier->TypeShva == PaymentTypeEnum::TYPE_MESHULAM) ? 'block' : 'none;' ?>">
                                    <div class="form-group">
                                        <label><?php echo lang('business_owner_code') ?></label>
                                        <input type="text" class="form-control" name="MeshulamUserId"
                                               id="MeshulamUserId"
                                               value="<?php echo @$Supplier->MeshulamUserId ?>" <?php echo (!$allowdCC) ? 'readonly' : ''; ?>
                                               onkeypress='validate(event)'>
                                    </div>
                                </div>


                                <div id="TypeShva0" class="js-payment-system-settings"
                                     data-type="<?= PaymentTypeEnum::TYPE_YAAD ?>"
                                     style="display: <?= ($Supplier->TypeShva == PaymentTypeEnum::TYPE_YAAD) ? 'block' : 'none;' ?>">

                                    <div class="form-group">
                                        <label><?php echo lang('terminal_number_business_settings') ?></label>
                                        <input type="text" class="form-control" name="YaadNumber" id="YaadNumber"
                                               value="<?php echo @$Supplier->YaadNumber ?>" <?php echo (!$allowdCC) ? 'readonly' : ''; ?>
                                               onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('terminal_number_shva') ?></label>
                                        <input type="text" class="form-control" name="Shva" id="Shva"
                                               value="<?php echo @$Supplier->Shva ?>" <?php echo (!$allowdCC) ? 'readonly' : ''; ?>
                                               onkeypress='validate(event)'>
                                    </div>


                                    <div class="form-group">
                                        <label><?php echo lang('main_proccessing_payment') ?></label>
                                        <select name="CreditType"
                                                class="form-control" <?php echo (!$allowdCC) ? 'disabled' : ''; ?>>
                                            <option value="" <?php if (@$Supplier->Isracrd == '' && @$Supplier->VisaCal == '' && @$Supplier->LeumiCard == '') {
                                                echo "selected";
                                            } ?>><?php echo lang('select_main_clearing') ?></option>
                                            <option value="0" <?php if (@$Supplier->CreditType == '0' && @$Supplier->LeumiCard != '') {
                                                echo "selected";
                                            } ?>><?php echo lang('leumi_card') ?></option>
                                            <option value="1" <?php if (@$Supplier->CreditType == '1' && @$Supplier->Isracrd != '') {
                                                echo "selected";
                                            } ?>><?php echo lang('isracard') ?></option>
                                            <option value="2" <?php if (@$Supplier->CreditType == '2' && @$Supplier->VisaCal != '') {
                                                echo "selected";
                                            } ?>><?php echo lang('visa_cal') ?></option>
                                            <option value="3" <?php if (@$Supplier->CreditType == '3') {
                                                echo "selected";
                                            } ?>><?php echo lang('american_express') ?></option>
                                            <option value="4" <?php if (@$Supplier->CreditType == '4') {
                                                echo "selected";
                                            } ?>><?php echo lang('diners') ?></option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('isracard_provider_number') ?></label>
                                        <input type="text" class="form-control" name="Isracrd" id="Isracrd"
                                               value="<?php echo @$Supplier->Isracrd ?>" <?php echo (!$allowdCC) ? 'readonly' : ''; ?>
                                               onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('amex_provider_number') ?></label>
                                        <input type="text" class="form-control" name="Amkas" id="Amkas"
                                               value="<?php echo @$Supplier->Amkas ?>" <?php echo (!$allowdCC) ? 'readonly' : ''; ?>
                                               onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('visa_provider_number') ?></label>
                                        <input type="text" class="form-control" name="VisaCal" id="VisaCal"
                                               value="<?php echo @$Supplier->VisaCal ?>" <?php echo (!$allowdCC) ? 'readonly' : ''; ?>
                                               onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('diners_provider_number') ?></label>
                                        <input type="text" class="form-control" name="Diners" id="Diners"
                                               value="<?php echo @$Supplier->Diners ?>" <?php echo (!$allowdCC) ? 'readonly' : ''; ?>
                                               onkeypress='validate(event)'>
                                    </div>

                                    <div class="form-group">
                                        <label><?php echo lang('leumi_provider_number') ?></label>
                                        <input type="text" class="form-control" name="LeumiCard" id="LeumiCard"
                                               value="<?php echo @$Supplier->LeumiCard ?>" <?php echo (!$allowdCC) ? 'readonly' : ''; ?>
                                               onkeypress='validate(event)'>
                                    </div>

                                </div>

                                <button id="SubmitMasof"
                                        class="btn btn-success btn-lg"><?php echo lang('update') ?></button>
                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade text-start" role="tabpanel" id="generalaccounts">
                        <div class="card spacebottom">
                            <div class="card-header text-start">
                                <strong><?php echo lang('reports_boostapp_charge') ?></strong></div>
                            <div class="card-body">

                                <?php
                                /*
                                אני רוצה לדעת בין התאריכים של מחזור חיוב נוכחי של כל חברה את המספר המקסימלי של הלקוחות הפעילים
                                ולפי זה אני רוצה לדעת את הסכום חיוב של אותה חברה לפי המסלול שלה שקיים בטבלה cleint_pricelist



                              SELECT
                                  tbl.clientId,
                                  tbl.clientName,
                                  tbl.clientEmail,
                                  tbl.clientJoinDate,
                                  tbl.customers,
                                  tbl.clientNextPayment,
                                  tbl.lastAmount,
                                  ( SELECT Text FROM `cleint_pricelist` where cleint_pricelist.clientId = tbl.clientId AND cleint_pricelist.NumClient <=  tbl.customers ORDER BY cleint_pricelist.NumClient DESC LIMIT 1 ) as plans,
                                  ( SELECT Amount FROM `cleint_pricelist` where cleint_pricelist.clientId = tbl.clientId AND cleint_pricelist.NumClient <=  tbl.customers ORDER BY cleint_pricelist.NumClient DESC LIMIT 1  ) as amount,
                                  (SELECT COUNT(*) FROM boostapp.client WHERE tbl.companyNum = client.CompanyNum AND client.Status = 0) as currentCustomersCount
                              FROM (
                                  SELECT
                                      client.id as clientId,
                                      client.CompanyName as clientName,
                                      client.ContactMobile as clientPhone,
                                      client.Email as clientEmail,
                                      client.Dates as clientJoinDate,
                                          paytoken.NextPayment as clientNextPayment,
                                      client.FixCompanyNum as companyNum,
                                         (SELECT max(CountClient) from boostapp.client_count WHERE (Date BETWEEN paytoken.LastPayment AND paytoken.NextPayment) AND client_count.CompanyNum = client.FixCompanyNum ) as customers,
                                      (SELECT Amount FROM 247softnew.docs_payment WHERE (UserDate BETWEEN paytoken.LastPayment AND paytoken.NextPayment) AND client.id = docs_payment.ClientId LIMIT 1) as lastAmount
                                  FROM
                                      247SoftNew.client
                                  LEFT JOIN 247SoftNew.paytoken ON client.id = paytoken.ClientId
                                  WHERE
                                   client.Status = 0 AND paytoken.Status = 0 AND paytoken.ItemId = 2 AND paytoken.CountPayment >= 1
                              ) as tbl
                                */
                                $GetClientId = DB::table('247softnew.client')->where('FixCompanyNum', '=', $CompanyNum)->first();
                                /* SELECT * FROM  '247softnew.client` as `client` */
                                if (!empty($GetClientId)) {
                                    $GetClientCreditKeva = DB::table('247softnew.paytoken')->where('ClientId', '=', $GetClientId->id)->where('Status', '=', '0')->where('ItemId', '=', '2')->where('CountPayment', '>=', '1')->first();

                                    /* SELECT NextPayment, LastPayment FROM `paytoken` WHERE Status = 0 AND ItemId = 2 CountPayment >= 1 */

                                    if (!empty($GetClientCreditKeva)) {

                                        $DayPayments = $GetClientCreditKeva->NextPayment;
                                        $DayPaymentTrue = with(new DateTime(@$DayPayments))->format('d/m/Y');

                                        $DateCheck = date("Y-m-d", strtotime('-1 day', strtotime($DayPayments)));
                                        if ($GetClientCreditKeva->LastPayment != '') {
                                            $DateFromCheck = $GetClientCreditKeva->LastPayment;
                                        } else {
                                            $DateFromCheck = date("Y-m-d", strtotime('-1 month', strtotime($DayPayments)));
                                        }

                                        $ClientCounts = DB::table('boostapp.client_count')->where('CompanyNum', '=', $CompanyNum)->whereBetween('Date', array($DateFromCheck, $DateCheck))->max('CountClient');

                                        if ($ClientCounts == '') {
                                            $ClientCounts = '0';
                                        }

                                        $ActiveClientsCountArray = DB::table('boostapp.client_count')->where('CompanyNum', '=', $CompanyNum)->whereBetween('Date', array($DateFromCheck, $DateCheck))->get();
                                        $ClientDealsList = DB::table('247softnew.cleint_pricelist')->select('NumClient', 'Text')->where('ClientId', '=', $GetClientId->id)->where('Status', '0')->whereBetween('NumClient', array('0', '1000000'))->orderBy('NumClient', 'DESC')->get();
                                        $ClientDealsList = array_map(function ($Deal) {
                                            return (object)['min' => $Deal->NumClient, 'text' => $Deal->Text];
                                        }, $ClientDealsList);
                                        ///// בדיקת חבילת תמחור

                                        $ClientDeals = DB::table('247softnew.cleint_pricelist')->where('ClientId', '=', $GetClientId->id)->where('Status', '0')->whereBetween('NumClient', array('0', $ClientCounts))->orderBy('NumClient', 'DESC')->first();

                                        $StepPayments = $ClientDeals->Text;
                                        $StepPaymentPrice = $ClientDeals->Amount;

                                        /////// בדיקת SMS

                                        $ClientSMS = DB::table('boostapp.appnotification')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '1')->where('System', '=', '0')->whereBetween('Date', array($DateFromCheck, $DateCheck))->sum('SMSSumPrice');

                                        if ($ClientSMS == '') {
                                            $ClientSMS = '0';
                                        }

                                        ////// בדיקת תוספות

                                        $ClientExtra = DB::table('247softnew.cleint_pricelist_add')->where('ClientId', '=', $GetClientId->id)->where('Status', '0')->sum('Amount');

                                        if ($ClientExtra == '') {
                                            $ClientExtra = '0';
                                        }


                                        $TotalPayments = $ClientDeals->Amount + $ClientExtra + $ClientSMS;


                                    } else {

                                        if ($GetClientId->DateBank == '') {
                                            $DayPaymentTrue = '';
                                            $ClientCounts = '0';
                                            $TotalPayments = '0';
                                            $ClientExtra = '0';
                                            $ClientSMS = '0';
                                            $StepPayments = lang('no_info_recurring_payment');
                                            $StepPaymentPrice = '0';
                                        } else {

                                            $DayPayments = date('Y-m') . '-' . date("d", strtotime($GetClientId->DateBank));
                                            if ($DayPayments < date('Y-m-d')) {
                                                $DayPayments = date('Y-m', strtotime('+1 month', strtotime(date('Y-m')))) . '-' . date("d", strtotime($GetClientId->DateBank));
                                            }

                                            $DayPaymentTrue = with(new DateTime(@$DayPayments))->format('d/m/Y');

                                            $DateCheck = date("Y-m-d", strtotime('-1 day', strtotime($DayPayments)));
                                            $DateFromCheck = date("Y-m-d", strtotime('-1 month', strtotime($DayPayments)));


                                            $ClientCounts = DB::table('boostapp.client_count')->where('CompanyNum', '=', $CompanyNum)->whereBetween('Date', array($DateFromCheck, $DateCheck))->max('CountClient');

                                            if ($ClientCounts == '') {
                                                $ClientCounts = '0';
                                            }

                                            ///// בדיקת חבילת תמחור

                                            $ClientDeals = DB::table('247softnew.cleint_pricelist')->where('ClientId', '=', $GetClientId->id)->where('Status', '0')->whereBetween('NumClient', array('0', $ClientCounts))->orderBy('NumClient', 'DESC')->first();

                                            $StepPayments = $ClientDeals->Text;
                                            $StepPaymentPrice = $ClientDeals->Amount;

                                            /////// בדיקת SMS

                                            $ClientSMS = DB::table('boostapp.appnotification')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '1')->where('System', '=', '0')->whereBetween('Date', array($DateFromCheck, $DateCheck))->sum('SMSSumPrice');

                                            if ($ClientSMS == '') {
                                                $ClientSMS = '0';
                                            }

                                            ////// בדיקת תוספות

                                            $ClientExtra = DB::table('247softnew.cleint_pricelist_add')->where('ClientId', '=', $GetClientId->id)->where('Status', '0')->sum('Amount');

                                            if ($ClientExtra == '') {
                                                $ClientExtra = '0';
                                            }


                                            $TotalPayments = $ClientDeals->Amount + $ClientExtra + $ClientSMS;

                                        }


                                    }

                                    ?>


                                    <div class="alertb alert-info my-10 px-11 py-5 mx-15 border-radius-8p"><?php echo lang('boostapp_charges_notice') ?></div>


                                    <div class="row" style="padding-right: 15px;padding-left: 15px;">

                                        <div class="col-md-3 col-sm-12 order-md-1 pb-15">


                                            <div class="card spacebottom">
                                                <a data-toggle="collapse" aria-expanded="true" style="color: black;">
                                                    <div class="card-header text-start">
                                                        <strong><i class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i>
                                                            <span><?php echo lang('client_count') ?></strong>
                                                    </div>
                                                </a>

                                                <div class="collapse show position-relative">
                                                    <div class="card-body text-center" style="min-height: 100px;">

                                                        <span class="text-center font-weight-bold"
                                                              style="padding-top: 10px; font-size:20px;"><?php if (isset($ClientCounts)) echo $ClientCounts; ?></span>
                                                        <br>
                                                        <span class="text-center font-weight-bold"
                                                              style="padding-top: 10px; font-size:14px;"><?php echo lang('charge_client_notice') ?></span>
                                                        <?php if (isset($ClientCounts) && (int)$ClientCounts > 0): ?>
                                                            <a class="position-absolute left-0 bottom-0 mx-10"
                                                               role="button" tabindex="0" data-toggle="modal"
                                                               data-target="#activeClientsCountModal">
                                                                <?= lang('monthly_breakdown') ?>
                                                                <i class="fal fa-long-arrow-left"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="col-md-3 col-sm-12 order-md-2 pb-15">


                                            <div class="card spacebottom">
                                                <a data-toggle="collapse" aria-expanded="true" style="color: black;">
                                                    <div class="card-header text-start">
                                                        <strong><i class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i>
                                                            <span><?php echo lang('charge_plan') ?></strong>
                                                    </div>
                                                </a>

                                                <div class="collapse show">
                                                    <div class="card-body text-center" style="min-height: 100px;">
                                                        <span class="text-center font-weight-bold"
                                                              style="padding-top: 10px; font-size:20px;">₪<?php echo $StepPaymentPrice; ?></span>
                                                        <br>
                                                        <span class="text-center font-weight-bold"
                                                              style="padding-top: 10px; font-size:14px;"><?php echo $StepPayments; ?></span>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-3 col-sm-12 order-md-3 pb-15">


                                            <div class="card spacebottom">
                                                <a data-toggle="collapse" aria-expanded="true" style="color: black;">
                                                    <div class="card-header text-start">
                                                        <strong><i class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i>
                                                            <span><?php echo lang('branches') ?></strong>
                                                    </div>
                                                </a>

                                                <div class="collapse show">
                                                    <div class="card-body text-center" style="min-height: 100px;">
                                                        <span class="text-center font-weight-bold"
                                                              style="padding-top: 10px; font-size:20px;">₪<?php echo $ClientExtra; ?></span>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-3 col-sm-12 order-md-3 pb-15">


                                            <div class="card spacebottom">
                                                <a data-toggle="collapse" aria-expanded="true" style="color: black;">
                                                    <div class="card-header text-start">
                                                        <strong><i class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i>
                                                            <span><?php echo lang('next_charge') ?></strong>
                                                    </div>
                                                </a>

                                                <div class="collapse show">
                                                    <div class="card-body text-center" style="min-height: 100px;">
                                                        <span class="text-center font-weight-bold"
                                                              style="padding-top: 10px; font-size:20px;"><?php echo $DayPaymentTrue; ?></span>
                                                        <br>
                                                        <span class="text-center font-weight-bold"
                                                              style="padding-top: 10px; font-size:14px;">₪<?php echo $TotalPayments; ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-3 col-sm-12 order-md-4 pb-15 ">

                                            <div class="card spacebottom">
                                                <a data-toggle="collapse" aria-expanded="true" style="color: black;">
                                                    <div class="card-header text-start">
                                                        <strong><i class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i><i
                                                                    class="fas fa-ellipsis-v"></i> <span>SMS</strong>
                                                    </div>
                                                </a>

                                                <div class="collapse show">
                                                    <div class="card-body text-center" style="min-height: 100px;">
                                                        <span class="text-center font-weight-bold"
                                                              style="padding-top: 10px; font-size:20px;">₪<?php echo $ClientSMS; ?></span>

                                                    </div>
                                                </div>
                                            </div>


                                        </div>


                                    </div>


                                <?php } ?>

                            </div>
                        </div>
                    </div>


                    <div class="tab-pane fade text-start" role="tabpanel" id="maasav">
                        <div class="card spacebottom">
                            <div class="card-header text-start">
                                <strong><?php echo lang('interfacing_center') ?></strong></div>
                            <div class="card-body">

                                <div class="form-group">
                                    <label><?php echo lang('institution_number') ?></label>
                                    <input type="text" class="form-control" name="MassavMosad" id="MassavMosad"
                                           value="<?php echo @$Supplier->MassavMosad ?>" readonly
                                           onkeypress='validate(event)'>
                                </div>

                                <div class="form-group">
                                    <label><?php echo lang('institution_number_for_file') ?></label>
                                    <input type="text" class="form-control" name="MassavZikoy" id="MassavZikoy"
                                           value="<?php echo @$Supplier->MassavZikoy ?>" readonly
                                           onkeypress='validate(event)'>
                                </div>

                                <div class="form-group">
                                    <label><?php echo lang('institution_number_sender') ?></label>
                                    <input type="text" class="form-control" name="MassavSender" id="MassavSender"
                                           value="<?php echo @$Supplier->MassavSender ?>" readonly
                                           onkeypress='validate(event)'>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade text-start" role="tabpanel" id="voicecenter">
                        <div class="card spacebottom">
                            <div class="card-header text-start">
                                <strong><?php echo lang('voice_center_integration') ?></strong></div>
                            <div class="card-body">


                                <div class="alertb alert-dark" role="alert">
                                    <?php echo lang('voice_center_notice') ?>
                                </div>

                                <form action="VoiceCenterPage" class="ajax-form clearfix" autocomplete="off">
                                    <input type="hidden" name="CompanyNum" value="1">
                                    <div class="form-group">
                                        <label><?php echo lang('connection_token') ?></label>
                                        <input type="text" class="form-control" name="VoiceCenterToken"
                                               id="VoiceCenterToken" value="<?php echo @$Supplier->VoiceCenterToken ?>">
                                    </div>
                                    <div class="form-group">
                                        <label><?php echo lang('voice_outcoming_calls') ?></label>
                                        <input type="text" class="form-control" name="VoiceCenterNumber"
                                               id="VoiceCenterNumber"
                                               value="<?php echo @$Supplier->VoiceCenterNumber ?>"
                                               onkeypress='validate(event)'>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <button type="submit"
                                                class="btn btn-success btn-lg"><?php echo lang('update') ?></button>
                                    </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        </div>


        <div class="ip-modal" id="headerModal">
            <div class="ip-modal-dialog">
                <div class="ip-modal-content text-start">
                    <div class="ip-modal-header  d-flex justify-content-between">
                        <h4 class="ip-modal-title"><?php echo lang('logo_for_docs') ?></h4>
                        <a class="ip-close" title="Close" style="">&times;</a>
                    </div>
                    <div class="ip-modal-body">

                        <div class="alertb alert-info"><?php echo lang('docs_logo_guide') ?></div>

                        <div class="btn btn-primary ip-upload"><?php echo lang('upload_image') ?> <input type="file"
                                                                                                         name="file"
                                                                                                         class="ip-file">
                        </div>
                        <!-- <button class="btn btn-primary ip-webcam">Webcam</button> -->
                        <button type="button" class="btn btn-info ip-edit"><?php echo lang('edit_logo') ?></button>
                        <button type="button"
                                class="btn btn-danger ip-delete"><?php echo lang('delete_logo') ?></button>

                        <div class="alert ip-alert"></div>
                        <div class="ip-info"><?php echo lang('crop_image_business_settings') ?></div>
                        <div class="ip-preview"></div>
                        <div class="ip-rotate">
                            <button type="button" class="btn btn-default ip-rotate-ccw"
                                    title="Rotate counter-clockwise"><i class="icon-ccw"></i></button>
                            <button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><i
                                        class="icon-cw"></i></button>
                        </div>
                        <div class="ip-progress">
                            <div class="text"><?php echo lang('uploading_business_settings') ?></div>
                            <div class="progress progress-striped active">
                                <div class="progress-bar"></div>
                            </div>
                        </div>
                    </div>
                    <div class="ip-modal-footer  d-flex justify-content-between">
                        <div class="ip-actions">
                            <button class="btn btn-success ip-save"><?php echo lang('save_image_business_settings') ?></button>
                            <button class="btn btn-primary ip-capture"><?php echo lang('capture_business_settings') ?></button>
                            <button class="btn btn-default ip-cancel"><?php echo lang('action_cacnel') ?></button>
                        </div>
                        <button class="btn btn-default ip-close"><?php echo lang('close') ?></button>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal-dialog modal-dialog-scrollable bs-modal-reposition fade" id="activeClientsCountModal"
             tabindex="-1"
             aria-labelledby="activeClientsCountModal" aria-hidden="true" style="display: none">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="activeClientsCountModalLabel"><?php echo lang('active_clients') ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="fal fa-times"></i></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="w-100">
                            <thead>
                            <tr>
                                <td class="text-start">#</td>
                                <td class="text-start"><?php echo lang('date') ?></td>
                                <td class="text-center"><?php echo lang('num_clients') ?></td>
                                <td class="text-start d-none d-md-table-cell"><?php echo lang('billing_plan') ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (isset($ClientCounts) && (int)$ClientCounts > 0 && !empty($ActiveClientsCountArray) && count($ActiveClientsCountArray) > 0) {
                                for ($i = 0, $iMax = count($ActiveClientsCountArray); $i < $iMax; $i++) {
                                    ?>
                                    <tr>
                                        <td class="text-start font-weight-bold"><?php echo($i + 1) ?></td>
                                        <td class="text-start font-weight-bold"><?php echo date('d.m.Y', strtotime($ActiveClientsCountArray[$i]->Date)); ?></td>
                                        <td class="text-center"><?php echo $ActiveClientsCountArray[$i]->CountClient; ?></td>
                                        <td class="text-start d-none d-md-table-cell">
                                            <?php $DealDescription = "";
                                            for ($dealIndex = 0, $dealIndexMax = count($ClientDealsList); $dealIndex < $dealIndexMax; $dealIndex++) {
                                                if ((int)$ActiveClientsCountArray[$i]->CountClient <= (int)$ClientDealsList[$dealIndex]->min) {
                                                    $DealDescription = isset($ClientDealsList[$dealIndex + 1]) ? 
                                                        $ClientDealsList[$dealIndex + 1]->text : 
                                                        $ClientDealsList[count($ClientDealsList)-1]->text;
                                                }
                                            }
                                            echo $DealDescription;
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>


                    </div>
                    <div class="modal-footer d-none"></div>
                </div>
            </div>
        </div>


        <script>

            $(function () {
                var time = function () {
                    return '?' + new Date().getTime()
                };

                $('#headerModal').imgPicker({
                    url: 'Server/upload_header.php',
                    aspectRatio: 33 / 15,
                    setSelect: [800, 300, 0, 0],
                    deleteComplete: function () {
                        $('#avatar').attr('src', '/office/files/logo/smallDefault.png');
                        this.modal('hide');
                    },
                    loadComplete: function (image) {
                        // Set #avatar image src
                        <?php if (@$Supplier->DocsCompanyLogo != ''){ ?>
                        $('#avatar').attr('src', 'files/<?php echo @$Supplier->DocsCompanyLogo; ?>');
                        <?php } else { ?>
                        $('#avatar').attr('src', '/office/files/logo/smallDefault.png');
                        <?php } ?>
                        // Set the image for re-crop
                        this.setImage(image);
                    },
                    cropSuccess: function (image) {
                        $('#avatar').attr('src', image.versions.header.url + time());
                        this.modal('hide');
                    }
                });

            });

            let autocomplete;

            $(document).ready(function () {
                $('.summernote').summernote({
                    placeholder: '<?php echo lang('type_notes_to_doc') ?>',
                    tabsize: 2,
                    height: 100,
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']]
                    ]
                });

                const input = document.getElementById('PlaceString');

                const options = {
                    componentRestrictions: {country: "il"},
                    fields: ["geometry", "place_id", "formatted_address", "address_components"],
                    types: ["geocode", "establishment"],
                };
                autocomplete = new google.maps.places.Autocomplete(input, options);

                // autocomplete choose event
                autocomplete.addListener("place_changed", function () {
                    const place = autocomplete.getPlace();
                    if (place == undefined) {
                        document.getElementById('PlaceString').value = '';
                        return;
                    }

                    document.getElementById('PlaceLatLng').value = place.geometry.location.toUrlValue();
                    document.getElementById('PlaceId').value = place.place_id;
                    document.getElementById('PlaceId').setAttribute('data-string', place.formatted_address);
                    input.value = place.formatted_address;

                    const address_components = place.address_components;
                    for (let i = 0; i < address_components.length; i++) {
                        const component = address_components[i];
                        if (component.types.includes('locality')) {
                            document.getElementById('PlaceCity').value = component.long_name || '';
                        }
                    }
                });

                // check for correct input
                input.addEventListener("change", function (e) {
                    if ($('.pac-container:visible').length > 0) {
                        // trigger to correctly handle click outside
                        google.maps.event.trigger(autocomplete, 'place_changed');
                    }

                    if (this.value == '') {
                        // clear google object
                        autocomplete.set('place', undefined);
                        // clear values
                        document.getElementById('PlaceLatLng').value = '';
                        document.getElementById('PlaceId').value = '';
                        document.getElementById('PlaceId').setAttribute('data-string', '');
                        document.getElementById('PlaceCity').value = '';
                    } else {
                        this.value = document.getElementById('PlaceId').getAttribute('data-string');
                    }
                });
            });


            $('[data-toggle="tabajax"]').click(function (e) {
                var $this = $(this),
                    loadurl = $this.attr('href'),
                    targ = $this.attr('data-target');

                $.get(loadurl, function (data) {
                    $(targ).html(data);
                });

                $this.tab('show');
                return false;
            });

            //שינוי עמוד בהתאם לטאב
            $('#newnavid a').click(function (e) {
                e.preventDefault();
                $(this).pill('show');
                $('.tab-content > .tab-pane.active').jScrollPane();
                $('html,body').scrollTop(0);
            });


            $("a").on("shown.bs.tab", function (e) {

                var id = $(e.target).attr("href").substr(1);
                window.location.hash = id;
                $('html,body').scrollTop(0);

            });


            // on load of the page: switch to the currently selected tab
            var hash = window.location.hash;
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
            //סיום שינוי עמוד בהתאם לטאב

        </script>


        <script>

            $(document).ready(function () {
                var windowWidth = $(window).width();
                if (windowWidth <= 1024) //for iPad & smaller devices
                    $('#MenuSettingSystem').removeClass('show');
                $('html,body').scrollTop(0);

                // Simple example, see optional options for more configuration.


            });
        </script>


        <script>
            $(".js-example-basic-single").select2({
                placeholder: "1Select a State",
                maximumSelectionSize: 6,
                allowClear: true
            });

            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });


            $("#select1").change(function () {

                $("#select2").val('').trigger('change');
                if ($("#select1").data('options') == undefined) {
                    /*Taking an array of all options-2 and kind of embedding it on the select1*/
                    $(this).data('options', $('#select2 option').clone());

                }
                var id = $(this).val();
                var options = $(this).data('options').filter('[asa=' + id + ']');
                $('#select2').html(options);
            });

            $(document).ready(function () {
                $(".select2a").select2({allowClear: true, theme: "bsapp-dropdown"});
            });

        </script>
    <?php
        require_once '../app/views/footernew.php';
    else:
        redirect_to('index.php');
    endif;
endif;
?>