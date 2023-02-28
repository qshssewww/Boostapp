<?php

/** @var $Types string */

require_once '../app/init.php';

if (Auth::guest()) {
    redirect_to(App::url());
}

$pageTitle = lang('generate_doc_title');
require_once '../app/views/headernew.php';
require_once __DIR__ . '/Classes/OrderLogin.php';

?>
<script src="<?php echo asset_url('js/jquery.maskedinput.js') ?>" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<style>
    @media screen and (max-width: 768px) {
        .bsapp-content {
            overflow-x: scroll;
        }
    }
</style>
<?php if (Auth::check()): ?>
    <?php if (Auth::userCan('39')): ?>
        <?php
        $UserId = Auth::user()->id;
        $CompanyNum = Auth::user()->CompanyNum;
        $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
        //todo-bp-909 (cart) remove-beta
        if (in_array($SettingsInfo->beta, [1])) {
            redirect_to('/office/cart.php');
        }

        $GeneralItemId = $SettingsInfo->GeneralItemId;
        $TypeShva = $SettingsInfo->TypeShva;
        $MeshulamAPI = $SettingsInfo->MeshulamAPI;
        $MeshulamUserId = $SettingsInfo->MeshulamUserId;
        $LiveMeshulam = $SettingsInfo->LiveMeshulam;

        $EditTempId = @$_REQUEST['EditTempId'];
        $ClientId = @$_REQUEST['ClientId'];
        $DocAction = @$_REQUEST['DocAction'];
        if ($DocAction == '') {
            $DocAction = '0';
        }

        $EditInfo = DB::table('temp')->where('id', '=', $EditTempId)->where('CompanyNum', '=', $CompanyNum)->first();
        include('DocsInc/DocsParameters.php');

        $DontShowSection = '0';
        $NewTypes = $Types;
        if ($Types == '400' || $Types == '2') {
            $DontShowSection = '1';
            $NewTypes = '400';
        }

/// בדיקת מספור מסמך + תאריך אחרון
        $DocsTableNew = DB::table('docstable')->where('TypeHeader', '=', $NewTypes)->where('CompanyNum', '=', $CompanyNum)->first();

        if ($DocsTableNew->Status == '1') {
            echo lang('please_reach_out_customer_service');
            redirect_to('Docs.php?Types=400');
            exit;
        }

        $DocsCountGets = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('TypeDoc', '=', $DocsTableNew->id)->orderBy('TypeNumber', 'DESC')->orderBy('id', 'DESC')->first();
        if (@$DocsCountGets->TypeNumber == '') {
            $Auto_increment = $DocsTableNew->TypeNumber;
        } else {
            $Auto_increment = $DocsCountGets->TypeNumber + 1;
        }

        ?>
        <link href="assets/css/fixstyle.css" rel="stylesheet">
        <!-- include summernote css/js -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>

        <div class="row">
            <?php include('DocsInc/RightCards.php'); ?>

            <div class="col-md-10 col-sm-12">
                <div class="card spacebottom">
                    <div class="card-header text-start">
                        <i class="fas fa-file-alt fa-fw"></i> <b><?php echo $TypeTitle; ?></b>
                    </div>
                    <div class="card-body">

                        <form action="AddDocs" name="AddDocs" id="AddDocs"
                              class="ajax-form clearfix fb_upload_id text-start" autocomplete="off"
                              onkeypress="return event.keyCode != 13;">
                            <?php
                            $GroupNumber = rand(1, 9999999);
                            ?>

                            <div class="row text-start">

                                <div class="col-md-6">
                                    <?php if (@$ClientId != '') { ?>
                                        <input type="hidden" name="Client" value="<?php echo @$ClientId; ?>">
                                    <?php } ?>
                                    <div class="form-group">
                                        <label><?= lang('client') ?> <em><?php echo lang('req_field') ?></em></label>
                                        <select name="Client" id="Client"
                                                data-placeholder="<?= lang('choose_client') ?>"
                                                class="form-control js-example-basic-single text-start selectclient CloseCheckBoxPayment"
                                                style="width:100%;" <?php if ($Types == '400' || $Types == '2') { ?> onChange="myFunctionnew(this.value)"<?php } else {
                                        } ?>>
                                            <option value=""></option>
                                            <?php if ($DontShowSection != '1') { ?>
                                                <option value="0"
                                                        data-vat="<?php echo $SettingsInfo->Vat == 17 && $SettingsInfo->BusinessType == 2 && $Types != '300' ? 17 : 0; ?>"
                                                        data-paymentrole="1"><?= lang('new_client') ?>
                                                </option>
                                            <?php } ?>
                                            <?php
                                            $Suppliers = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->orderBy('CompanyName', 'ASC')->get();
                                            foreach ($Suppliers as $Supplier) {
                                                ?>
                                                <option value="<?php echo $Supplier->id; ?>"
                                                        data-vat="<?php echo $Types == '300' ? 0 : $SettingsInfo->Vat; ?>"
                                                        data-paymentrole="<?php echo $Supplier->PaymentRole; ?>" <?php if (@$ClientId == $Supplier->id) {
                                                    echo 'selected';
                                                } else {
                                                } ?>><?php echo $Supplier->CompanyName; ?>
                                                    :: <?php echo @$Supplier->ContactMobile; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label><?= lang('date') ?></label>
                                        <?php
                                        $CheckDocDate = DB::table('docs')->where('TypeDoc', '=', $DocsTableNew->id)->where('CompanyNum', '=', $CompanyNum)->orderBy('UserDate', 'DESC')->first();
                                        if (@$CheckDocDate->id != '') {
                                            $LastDate = date('Y-m-01', strtotime($CheckDocDate->UserDate));
                                        } else {
                                            $LastDate = date('Y-m-d', strtotime(date('Y-01-01')));
                                        }
                                        ?>
                                        <input type="date" name="Dates" id="Dates"
                                               max="<?php echo date('Y-m-d'); ?>" min="<?php echo $LastDate; ?>"
                                               value="<?php echo date('Y-m-d'); ?>" class="form-control">
                                    </div>
                                </div>
                                <?php
                                $DocTempGroupNumber = rand(1, 9999999);
                                if ($Types == '400' || $Types == '2') {
                                    $DocsTempId = '1';
                                } else {
                                    $DocsTempId = '';
                                }
                                ?>

                                <div class="col-md-2">
                                    <label><?= lang('doc_type') ?></label>
                                    <input type="text" name="TypeName" id="TypeName" class="form-control"
                                           value="<?php echo $TypeTitleSingle; ?>" readonly>
                                    <input type="hidden" name="TypeDoc" id="TypeDoc" value="<?php echo $Types; ?>">
                                    <input type="hidden" name="DocTempId" id="DocTempId"
                                           value="<?php echo $DocsTempId; ?>">
                                    <input type="hidden" name="DocTempGroupNumber" id="DocTempGroupNumber"
                                           value="<?php echo $DocTempGroupNumber; ?>">
                                    <input type="hidden" name="DocAction" id="DocAction"
                                           value="<?php echo @$DocAction; ?>">
                                </div>
                                <div class="col-md-2">
                                    <label><?= lang('doc_id') ?></label>
                                    <input type="text" name="TypeDocNumber" id="TypeDocNumber" class="form-control"
                                           value="<?php echo @$Auto_increment; ?>" readonly>
                                </div>

                            </div>

                            <div id="newclient" style="display:none;">
                                <div class="row">
                                    <div class="col-md-2" style="">
                                        <label><?= lang('client_full_name') ?> <em><?php echo lang('req_field') ?></em></label>
                                        <input type="text" name="DocFullName" id="DocFullName" class="form-control"
                                               autocomplete="off">
                                    </div>
                                    <div class="col-md-2" style="">
                                        <label><?= lang('telephone') ?> <em><?php echo lang('req_field') ?></em></label>
                                        <input type="text" name="DocPhone" id="DocPhone" class="form-control" pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$" autocomplete="off">
                                    </div>
                                    <div class="col-md-2" style="">
                                        <label><?= lang('email_table') ?></label>
                                        <input type="text" name="DocEmail" id="DocEmail" class="form-control"
                                               autocomplete="off">
                                    </div>
                                    <div class="col-md-2" style="">
                                        <label><?= lang('client_status') ?></label>
                                        <select name="DocStatus" id="DocStatus" class="form-control"
                                                style="width:100%;">
                                            <option value="0"><?= lang('activated_client') ?></option>
                                            <option value="1" selected><?= lang('archived_client') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="alertb alert-dark" role="alert"
                                 style="display:none; font-weight:bold; font-size:14px;"><span id="ServicesText"></span>
                            </div>
                            <input type="hidden" name="ServicesTextId" id="ServicesTextId"
                                   value="<?php echo $Types; ?>">
                            <?php
                            /// קליטת חשבוניות פתוחות להנפקת קבלה
                            if ($ShowSelect == '1') { ?>
                                <div id="resultDivnew" style="text-decoration:none; font-style:normal;">
                                    <?= lang('choose_client_to_load_opened_invoices') ?>
                                </div>
                            <?php } ?>

                            <?php if ($Types == '12') { ?>
                                <select name="Items6" id="Items6" class="form-control select6 CloseCheckBoxPayment"
                                        style="width:100%;">
                                    <option value=""></option>
                                </select>
                                <div style="padding:10px;"></div>
                            <?php } ?>
                            <?php if ($DontShowSection != '1') { ?>
                                <select name="Items1" id="Items1" class="form-control select3 CloseCheckBoxPayment"
                                        style="width:100%;">
                                    <option value=""></option>
                                </select>
                                <div style="padding:10px;"></div>
                                <div class="input-group">
      <span class="input-group-btn">

      <button class="btn btn-dark CloseCheckBoxPayment" type="button" id="TextValueButton"><?= lang('add') ?> <span
                  class="glyphicon glyphicon-saved"></span></button>
      </span>
                                    <input type="text" name="TextValue" id="TextValue" class="form-control"
                                           placeholder="<?= lang('type_content_for_general_item') ?>">
                                </div><!-- /input-group -->
                                <div id="GetItems">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th style="width: 5%; text-align:start;"><?= lang('x') ?></th>
                                            <th style="width: 45%; text-align:start;"><?= lang('item_name') ?></th>
                                            <th style="width: 15%; text-align:start;"><?= lang('price_per_unit') ?></th>
                                            <th style="width: 10%; text-align:start;"><?= lang('quantity') ?></th>
                                            <th style="width: 10%; text-align:start;"><?= lang('line_discount') ?></th>
                                            <th style="width: 15%; text-align:start;"><?= lang('total') ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>

                                </div>

                                <div style="border-top: 1px solid #e5e5e5; margin-top: 0px; padding-top: 0px; padding-bottom: 5px;">
                                    <div class="d-flex flex-row-reverse justify-content-between">
                                        <div class="col-md-4 col-sm-12">
                                            <div class="col-12"
                                                 style="font-size:16px; font-weight:bold; padding-top:10px;">
                                                <div class="d-flex flex-row-reverse justify-content-between">
                                                    <div class="unicode-plaintext">
                                                        ₪ <span id="resultFinal">0.00</span>
                                                    </div>
                                                    <div class="unicode-plaintext"><?= lang('subtotal') ?></div>
                                                </div>
                                                <!-- Discount -->
                                                <div class="d-flex flex-row-reverse justify-content-between">
                                                    <div class="unicode-plaintext">
                                                        ₪ <span id="resultFinalDiscount">0.00</span>
                                                    </div>
                                                    <div class="unicode-plaintext">
                                                        <?= lang('discount') ?>
                                                        <span id="resultDiscountIn2">0</span><span
                                                                id="resultDiscountIn">%</span>
                                                        <a class="CloseEditItems" style="cursor:pointer;"
                                                           data-toggle="tooltip" id="DiscountPopups"
                                                           data-placement="top" title='<?= lang('set_discount_doc') ?>'
                                                           data-ip-modal="#DiscountPopup" name="Discount"><i
                                                                    class="fas fa-edit fa-fw"></i></a>
                                                    </div>
                                                </div>
                                                <!-- Round -->
                                                <div class="d-flex flex-row-reverse justify-content-between"
                                                     id="DivRound">
                                                    <div class="unicode-plaintext">
                                                        ₪ <span id="resultFinalRound">0.00</span>
                                                    </div>
                                                    <div class="unicode-plaintext">
                                                        <?= lang('round_cents') ?>
                                                    </div>
                                                </div>

                                                <!-- Vat -->
                                                <div class="d-flex flex-row-reverse justify-content-between">
                                                    <div class="unicode-plaintext">
                                                        ₪ <span id="resultVAT">0.00</span>
                                                    </div>
                                                    <div class="unicode-plaintext">
                                                        <?= lang('vat') ?> <span
                                                                id="resultVATIn"><?php echo $SettingsInfo->CompanyVat == 0 && $SettingsInfo->BusinessType == 2 && $Types != '300' ? 17 : 0; ?></span>%
                                                        <a
                                                                style="cursor:pointer;" class="CloseEditItems"
                                                                data-toggle="tooltip" id="Vat" data-placement="top"
                                                                title='<?= lang('set_vat') ?>' data-ip-modal="#VatPopup"
                                                                name="Vat"><i
                                                                    class="fas fa-edit fa-fw"></i></a>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-row-reverse justify-content-between">
                                                    <div class="unicode-plaintext">
                                                        ₪ <span id="resultFinal2">0.00</span>
                                                    </div>
                                                    <div class="unicode-plaintext">
                                                        <?= lang('total_to_pay') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-8 col-sm-12"
                                             style="font-size: 14px; padding-top: 10px; ">
                                            <strong><?= lang('permanent_notes_doc') ?></strong>
                                            <p>
                                                <?php echo @$DocsTableNew->DocsRemarks; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="NikoysTypes" id="NikoysTypes" value="1">
                                <input type="hidden" name="TotalFinal" id="TotalFinal" value="0">
                                <input type="hidden" value="0" id="TotalHide1" name="TotalHide1" readonly/>
                                <input type="hidden" value="0" id="TotalHide1New" name="TotalHide1New" readonly/>
                                <input type='hidden' value="0" id='TotalHide2' name='TotalHide2' readonly/>
                                <input type='hidden' value="0" id='TotalHide3' name='TotalHide3'/>
                                <input type="hidden" value="0" id="resultHide" name="resultHide" readonly/>
                                <input type='hidden' value="0" id='totalHide' name='totalHide' readonly/>
                                <input type='hidden' value="0" id='totalHidenew' name='totalHidenew'/>
                                <input name="Finalinvoicenum" id="Finalinvoicenum" type="hidden" value="0">
                                <input name="TrueFinalinvoicenum" id="TrueFinalinvoicenum" type="hidden" value="0">
                            <?php } else { ?>
                                <div style="border-top: 1px solid #e5e5e5; margin-top: 0px; padding-top: 0px; padding-bottom: 5px;">
                                    <div class="row">
                                        <div class="col-md-8 col-sm-12 order-md-2">
                                            <div class="col-12"
                                                 style="font-size:16px; font-weight:bold; padding-top:10px;">
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-3">
                                                        ₪ <span id="TotalHide8">0.00</span>
                                                    </div>
                                                    <div class="col-md-3 col-sm-4 text-end">
                                                        <input type="hidden" name="NikuyMsBamakor" id="NikuyMsBamakor"
                                                               value="<?php if (@$SettingsInfo->NikuyMsBamakor != '') {
                                                                   echo @$SettingsInfo->NikuyMsBamakor;
                                                               } else {
                                                                   echo '0';
                                                               } ?>">
                                                        <input type="hidden" name="cleanmas" id="cleanmas" value="0">
                                                        : <a style="cursor:pointer;" data-toggle="tooltip" id="Nikoy"
                                                             data-placement="top" title='<?= lang('set_withholding_tax') ?>'
                                                             data-ip-modal="#NikoyPopup" name="Nikoy"><span
                                                                    class="fas fa-edit"
                                                                    style="font-size:12px; text-align:start;"></span></a>
                                                        <span id="NikuyMsBamakorHTML"><?php if (@$SettingsInfo->NikuyMsBamakor != '') {
                                                                echo @$SettingsInfo->NikuyMsBamakor;
                                                            } else {
                                                                echo '0';
                                                            } ?></span><span
                                                                id="NikuyMsBamakorHTMLSign">%</span><?= lang('withholding_tax') ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-2 col-sm-3">
                                                        ₪ <span id="TotalHide7">0.00</span>
                                                    </div>
                                                    <div class="col-md-3 col-sm-4 text-end">
                                                        <?= lang('remainder_of_payment') ?>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="NikoysTypes" id="NikoysTypes" value="1">
                                                <input type="hidden" name="TotalFinal" id="TotalFinal" value="0">
                                                <input type="hidden" value="0" id="TotalHide1" name="TotalHide1"
                                                       readonly/>
                                                <input type="hidden" value="0" id="TotalHide1New" name="TotalHide1New"
                                                       readonly/>
                                                <input type='hidden' value="0" id='TotalHide2' name='TotalHide2'
                                                       readonly/>
                                                <input type='hidden' value="0" id='TotalHide3' name='TotalHide3'/>
                                                <input type="hidden" value="0" id="resultHide" name="resultHide"
                                                       readonly/>
                                                <input type='hidden' value="0" id='totalHide' name='totalHide'
                                                       readonly/>
                                                <input type='hidden' value="0" id='totalHidenew' name='totalHidenew'/>
                                                <input name="Finalinvoicenum" id="Finalinvoicenum" type="hidden"
                                                       value="0">
                                                <input name="TrueFinalinvoicenum" id="TrueFinalinvoicenum" type="hidden"
                                                       value="0">
                                            </div>
                                        </div>

                                        <div class="col-md-4 col-sm-12 order-md-1"
                                             style="font-size: 14px; padding-top: 10px; ">
                                            <strong><?= lang('permanent_notes_doc') ?></strong>
                                            <p>
                                                <?php echo @$DocsTableNew->DocsRemarks; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                            <hr>
                            <?php if ($Types == '1') { ?>
                                <div class="form-group">
                                    <label><?= lang('manual_Invoice_number') ?></label>
                                    <input type="text" name="manualinvoice" id="manualinvoice" class="form-control"
                                           required>
                                </div>
                            <?php } else { ?><input type="hidden" name="manualinvoice" id="manualinvoice"
                                                    value="0"><?php } ?>
                           <input type="hidden" name="PaymentRole" value="1">
                            <?php if ($Remarks == '1') { ?>
                                <div class="form-group">
                                    <label><?= lang('notes_two') ?></label>
                                    <textarea class="form-control summernote"
                                              name="Remarks"><?php echo @$DocsTableNew->DocsRemarks; ?></textarea>
                                </div>
                            <?php } else { ?><input type="hidden" name="Remarks" id="Remarks"><br><?php } ?>

                            <?php if ($Payment == '1') { ?>
                                <div id="ShowPaymentDiv" style="display: none;">
                                    <div class="alertb alert-dark mb-15 px-5 font-weight-bold" role="alert"
                                         style="font-size:14px;"><?= lang('payment_settings') ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="button" name="submit" class="btn btn-dark text-white"
                                                    id="Chash"><?= lang('cash') ?>
                                            </button>
                                            <button type="button" name="submit" class="btn btn-dark text-white"
                                                    id="Credit"><?= lang('credit_card') ?>
                                            </button>
                                            <button type="button" name="submit" class="btn btn-dark text-white"
                                                    id="Check"><?= lang('check') ?>
                                            </button>
                                            <button type="button" name="submit" class="btn btn-dark text-white"
                                                    id="Bank"><?= lang('bank_transfer') ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="Cahshdiv" style="display: none;">
                                        <div class="row" style="padding-top: 10px;">

                                            <div class="col-md col-sm-12 order-md-1">
                                                <label class="control-label"><?= lang('summary') ?></label>
                                                <input type="number" step="0.01" min="0.01" name="CashValue"
                                                       onkeypress='validate(event)' id="CashValue" class="form-control"
                                                       placeholder="<?= lang('type_cash_sum') ?>" tabindex="1">
                                            </div>
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-md-12 col-sm-12  order-md-2">
                                                <button class="btn btn-primary btn-block" type="button"
                                                        id="CashValueButton" tabindex="2"><?= lang('add') ?> <span class="glyphicon glyphicon-saved"></span></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="Checkdiv" style="display: none;">
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-md col-sm-12 order-md-3">
                                                <label><?= lang('branch_id') ?></label>
                                                <input type="text" class="form-control" name="CheckSnif" id="CheckSnif"
                                                       onkeypress='validate(event)' tabindex="6">
                                            </div>
                                            <div class="col-md col-sm-12 order-md-2">
                                                <label><?= lang('bank_code') ?></label>
                                                <input type="text" class="form-control" name="CheckBank" id="CheckBank"
                                                       onkeypress='validate(event)' tabindex="5">
                                            </div>
                                            <div class="col-md col-sm-12 order-md-1">
                                                <label><?= lang('check_number') ?></label>
                                                <input type="text" class="form-control" name="CheckNumber"
                                                       id="CheckNumber" onkeypress='validate(event)'
                                                       tabindex="4">
                                            </div>
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-md col-sm-12 order-md-3">
                                                <label class="control-label"><?= lang('check_sum') ?></label>
                                                <input type="number" step="0.01" min="0.01" name="CheckValue" id="CheckValue"
                                                       class="form-control" onkeypress='validate(event)'
                                                       tabindex="9">
                                            </div>
                                            <div class="col-md col-sm-12 order-md-2">
                                                <label class="control-label"><?= lang('payment_date') ?></label>
                                                <input type="date" class="form-control" name="CheckDate" id="CheckDate"
                                                       onkeypress='validate(event)' tabindex="8">
                                            </div>
                                            <div class="col-md col-sm-12 order-md-1">
                                                <label><?= lang('account_number') ?></label>
                                                <input type="text" class="form-control" name="CheckAccount"
                                                       id="CheckAccount" onkeypress='validate(event)'
                                                       tabindex="7">
                                            </div>
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-md-12 col-sm-12">
                                                <button class="btn btn-dark btn-block" type="button"
                                                        id="CheckValueButton" tabindex="9"><?= lang('add_check') ?>
                                                    <span class="glyphicon glyphicon-saved"></span></button>
                                            </div>
                                        </div>

                                    </div>

                                    <div id="Bankdiv" style="display: none;">
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-md col-sm-12  order-md-3">
                                                <label class="control-label"><?= lang('transfer_sum') ?></label>
                                                <input type="number" step="0.01" min="0.01" name="BankValue" id="BankValue"
                                                       class="form-control" onkeypress='validate(event)'
                                                       tabindex="12">
                                            </div>
                                            <div class="col-md col-sm-12  order-md-2">
                                                <label class="control-label"><?= lang('deposit_date') ?></label>
                                                <input type="date" class="form-control" name="BankDate" id="BankDate"
                                                       onkeypress='validate(event)' tabindex="11">
                                            </div>
                                            <div class="col-md col-sm-12  order-md-1">
                                                <label><?= lang('ref_number') ?></label>
                                                <input type="text" class="form-control" name="BankNumber"
                                                       id="BankNumber" tabindex="10">
                                            </div>
                                        </div>
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-md-12 col-sm-12 order-md-4">
                                                <button class="btn btn-dark btn-block" type="button" id="BankValueButton" tabindex="13">
                                                    <?= lang('add_bank_transfer') ?> <span class="glyphicon glyphicon-saved"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="Creditdiv" style="display: none;">
                                        <div class="row" style="padding-top: 10px;">
                                            <div class="col-12">
                                                <label><?= lang('choose_option') ?></label>
                                                <select name="CreditOptionToken" id="CreditOptionToken"
                                                        class="form-control input-lg"
                                                        onchange="showCredit(this.options[this.selectedIndex].value)"
                                                        tabindex="14">
                                                    <option value="2"><?= lang('credit_card_saved_in_system') ?></option>
                                                    <option value="3"><?= lang('manual_type') ?></option>
                                                    <option value="4"><?= lang('transfer_made_by_other_terminal') ?></option>
                                                    <?php if ($TypeShva == '0') { ?>
                                                        <option value="1"><?= lang('credit_card_scanner') ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div style="display: none" id="CreditDiv1"  class="credit-card-type" data-type="1">
                                            <div class="row"  style="padding-top: 10px;">
                                                <div class="col-md col-sm-12  order-md-3">
                                                    <label class="control-label"><?= lang('payments_num') ?></label>

                                                    <select name="Tash" id="Tash1" class="form-control input-lg Tash" tabindex="17">
                                                        <option value="1">1</option>
                                                    </select>
                                                </div>

                                                <div class="col-md col-sm-12  order-md-2">
                                                    <label class="control-label"><?= lang('choose_payment_method') ?></label>

                                                    <select name="tashType" id="tashType1" class="form-control input-lg tashType" tabindex="16">
                                                        <option value="0" selected><?= lang('regular') ?></option>
                                                        <option value="1"><?= lang('payments') ?></option>
                                                    </select>
                                                </div>

                                                <div class="col-md col-sm-12  order-md-1">
                                                    <label><?= lang('price_to_charge') ?></label>

                                                    <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValue" class="form-control" onkeypress="validate(event)" tabindex="15">
                                                </div>
                                            </div>

                                            <div class="row"  style="padding-top: 10px;">
                                                <div class="col-md-12 col-sm-12order-md-4">
                                                    <label class="control-label"><?= lang('swipe_credit_card') ?></label>
                                                    <input type="text" class="form-control CC2" name="CC2" id="CC2" tabindex="18">
                                                </div>
                                            </div>

                                            <div class="row"  style="padding-top: 10px;">
                                                <div class="col-md-12 col-sm-12  order-md-5">
                                                    <button class="btn btn-dark btn-block" type="button" id="CreditValueButton" disabled tabindex="19"><?= lang('charge_client') ?>
                                                        <span class="glyphicon glyphicon-saved"></span></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="CreditDiv2" class="credit-card-type" data-type="2">
                                            <div class="row" style="padding-top: 10px;">

                                                <div class="col-md col-sm-12  order-md-4">
                                                    <label class="control-label"><?= lang('choose_credit_card_saved_in_system') ?></label>
                                                    <div id="ChangeTokenI">
                                                        <select name="CC3" id="CC3" class="form-control input-lg"
                                                                tabindex="23">
                                                            <option value="" selected><?= lang('choose_token') ?></option>
                                                            <?php
                                                            $Tokens = DB::table('token')
                                                                ->where('CompanyNum', $CompanyNum)
                                                                ->where('ClientId', '=', @$Supplier->id)
                                                                ->where('Status', '=', '0')
                                                                ->where('Type', '=', $TypeShva)
                                                                ->where('Private', '=', 0)
                                                                ->where('Token',"!=",'')
                                                                ->get();
                                                            foreach ($Tokens as $Token) {
                                                                ?>
                                                                <option value="<?php echo $Token->id; ?>"><?= '****' . $Token->L4digit ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md col-sm-12 order-md-3" style="display: none">
                                                    <label class="control-label"><?= lang('payments_num') ?></label>
                                                    <select name="Tash" id="Tash2" class="form-control input-lg Tash"
                                                            tabindex="22">
                                                        <option value="1">1</option>
                                                    </select>
                                                </div>
                                                <div class="col-md col-sm-12  order-md-2">
                                                    <label class="control-label"><?= lang('choose_payment_method') ?></label>
                                                    <select name="tashType" id="tashType2" class="form-control input-lg tashType" tabindex="21">
                                                        <option value="0" selected><?= lang('regular') ?></option>
                                                        <option value="1"><?= lang('payments') ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-md col-sm-12  order-md-1">
                                                    <label><?= lang('price_to_charge') ?></label>
                                                    <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValue2" disabled="true" class="form-control" onkeypress='validate(event)' tabindex="20">
                                                </div>
                                            </div>

                                            <div class="row" style="padding-top: 10px;">
                                                <div class="col-md-12 col-sm-12 order-md-5">
                                                    <button class="btn btn-dark btn-block" type="button"
                                                            id="Credit2ValueButton"
                                                            tabindex="24"><?= lang('charge_client') ?> <span
                                                                class="glyphicon glyphicon-saved"></span></button>
                                                </div>
                                            </div>
                                        </div>

                                        <div style="display: none;" id="CreditDiv3" class="credit-card-type" data-type="3">
                                            <div class="row" style="padding-top: 10px;">
                                                <div class="col-md col-sm-12   order-md-3">
                                                    <label class="control-label"><?= lang('payments_num') ?></label>
                                                    <select name="Tash" id="Tash3" class="form-control input-lg Tash"
                                                            tabindex="27">
                                                        <option value="1">1</option>
                                                    </select>
                                                </div>
                                                <div class="col-md col-sm-12   order-md-2">
                                                    <label class="control-label"><?= lang('choose_payment_method') ?></label>
                                                    <select name="tashType" id="tashType3"
                                                            class="form-control input-lg tashType" tabindex="26">
                                                        <option value="0" selected><?= lang('regular') ?></option>
                                                        <option value="1"><?= lang('payments') ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-md col-sm-12   order-md-1">
                                                    <label><?= lang('price_to_charge') ?></label>
                                                    <input type="number" step="0.01" min="0.01" name="CreditValue"
                                                           id="CreditValue3" class="form-control"
                                                           onkeypress='validate(event)' tabindex="25" disabled="true">
                                                </div>
                                            </div>

                                            <div class="row mt-10">
                                                <div class="col-md-12 col-sm-12  order-md-9">
                                                    <button class="btn btn-dark btn-block mb-20 js-pay-new-card-iframe-button" id="CreateNewPayments" data-order-type="<?= OrderLogin::TYPE_PAYMENT_NEW_CARD_DOCS ?>">
                                                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                        <span class="js-loading-label d-none"><?php echo lang('loading') ?>...</span>
                                                        <span class="js-btn-text"><?php echo lang('move_to_payment') ?></span>
                                                    </button>

                                                    <div class="iframe-wrapper mb-20 d-none">
                                                        <iframe src="" frameborder="0" class="add-new-card-iframe w-100 h-600p"></iframe>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div style="display: none;" id="CreditDiv4" class="credit-card-type" data-type="4">
                                            <div class="row" style="padding-top: 10px;">

                                                <div class="col-md col-sm-12  order-md-5">
                                                    <label class="control-label"><?= lang('payments_num') ?></label>
                                                    <select name="Tash" id="Tash4" class="form-control input-lg Tash"
                                                            tabindex="38">
                                                        <option value="1">1</option>
                                                    </select>
                                                </div>
                                                <div class="col-md col-sm-12  order-md-4">
                                                    <label class="control-label"><?= lang('choose_payment_method') ?></label>
                                                    <select name="tashType" id="tashType4"
                                                            class="form-control input-lg tashType" tabindex="37">
                                                        <option value="0" selected><?= lang('regular') ?></option>
                                                        <option value="1"><?= lang('payments') ?></option>
                                                    </select>
                                                </div>
                                                <div class="col-md col-sm-12  order-md-3">
                                                    <label class="control-label"><?= lang('confirmation_number') ?></label>
                                                    <input type="text" class="form-control" name="CCode" id="CCode4"
                                                           tabindex="36">
                                                </div>
                                                <div class="col-md col-sm-12  order-md-2">
                                                    <label class="control-label"><?= lang('original_charge_date') ?></label>
                                                    <input type="date" class="form-control" name="CDate" id="CDate4"
                                                           tabindex="35">
                                                </div>
                                                <div class="col-md col-sm-12  order-md-1">
                                                    <label><?= lang('charged_price') ?></label>
                                                    <input type="number" step="0.01" min="0.01" name="CreditValue"
                                                           id="CreditValue4" class="form-control"
                                                           onkeypress='validate(event)' tabindex="34">
                                                </div>
                                            </div>

                                            <div class="row" style="padding-top: 10px;">
                                                <div class="col-md col-sm-12  order-md-6">
                                                    <label class="control-label"><?= lang('choose_company_to_be_paid_off') ?></label>
                                                    <select name="TypeBank" id="TypeBank4" class="form-control input-lg"
                                                            tabindex="41">
                                                        <option value=""><?= lang('choose') ?></option>
                                                        <option value="2"><?= lang('visa_cal') ?></option>
                                                        <option value="1"><?= lang('isracard') ?></option>
                                                        <option value="6"><?= lang('leumi_card') ?></option>
                                                    </select>
                                                </div>

                                                <div class="col-md col-sm-12  order-md-5">
                                                    <label class="control-label"><?= lang('choose_credit_card_type') ?></label>
                                                    <select name="TypeBrand" id="TypeBrand4"
                                                            class="form-control input-lg" tabindex="40">
                                                        <option value=""><?= lang('choose') ?></option>
                                                        <option value="88"><?= lang('mastercard') ?></option>
                                                        <option value="2"><?= lang('visa') ?></option>
                                                        <option value="5"><?= lang('isracard') ?></option>
                                                        <option value="66"><?= lang('diners') ?></option>
                                                        <option value="77"><?= lang('american_express') ?></option>
                                                    </select>
                                                </div>

                                                <div class="col-md col-sm-12  order-md-4">
                                                    <label class="control-label"><?= lang('last_four_chars_on_cc') ?></label>
                                                    <input type="text" class="form-control" name="CC" id="CC4"
                                                           placeholder="<?= lang('manual_type') ?>" tabindex="39">
                                                </div>
                                            </div>
                                            <div class="row" style="padding-top: 10px;">
                                                <div class="col-md-12 col-sm-12">
                                                    <button class="btn btn-dark btn-block" type="button"
                                                            id="Credit4ValueButton" tabindex="44"><?= lang('save') ?>
                                                        <span
                                                                class="glyphicon glyphicon-saved"></span></button>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="alertb alert-info">
                                                <?= lang('attention_typing_required_system_wont_charge') ?>
                                            </div>
                                        </div>

                                    </div>

                                    <hr>
                                    <div class="alertb alert-dark" role="alert"
                                         style="display:block; font-weight:bold; font-size:14px; text-align: right;"
                                    ><?= lang('detailed_receipt') ?>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div id="DocsPayments">
                                                <table class="table  table-responsive w-100 d-block d-md-table"
                                                       style="width: 100%;">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 5%; text-align: right;">#</th>
                                                        <th style="width: 25%; text-align: right;"><?= lang('payment_method') ?></th>
                                                        <th style="width: 25%; text-align: right;"><?= lang('detail') ?></th>
                                                        <th style="width: 20%; text-align: right;"><?= lang('reference') ?></th>
                                                        <th style="width: 10%; text-align: right;"><?= lang('summary') ?></th>
                                                        <th style="width: 15%; text-align: right;"><?= lang('actions') ?></th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>

                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($Types == '400' || $Types == '2') { ?>
                                        <div class="form-group d-flex justify-content-between mt-15">
                                            <button type="submit" name="submit" id="ReceiptBtn"
                                                    class="btn btn-primary mb-10" ><?php echo lang('save_changes_button') ?></button>

                                            <button type="button" name="CancelDoc" id="CancelDocButton"
                                                    class="btn btn-danger mb-10" data-ip-modal="#CancelDoc"
                                                    disabled><?= lang('cancel_doc') ?>
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                            <?php if ($DontShowSection != '1') { ?>
                                <div class="form-group d-flex justify-content-between mt-15">

                                    <button type="button" name="CancelDoc" id="CancelDocButton"
                                            class="btn btn-danger mb-10"
                                            data-ip-modal="#CancelDoc" disabled><?= lang('cancel_doc') ?>
                                    </button>
                                    <button type="submit" name="submit" id="ReceiptBtn"
                                            class="btn btn-primary mb-10" ><?php echo lang('save_changes_button') ?></button>

                                </div>
                            <?php } ?>

                        </form>
                        <div id="spinner">
                            <?= lang('wait_processing_data') ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <input name="TypeDoc" id="TypeDoc" type="hidden" value="<?php echo $Types; ?>">
        <!-- DiscountPopup -->
        <div class="ip-modal text-start" id="DiscountPopup">
            <div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
                <div class="ip-modal-content">
                    <div class="ip-modal-header">
                        <a class="ip-close" title="Close" style="" data-dismiss="modal">&times;</a>
                        <h4 class="ip-modal-title"><?= lang('discount') ?></h4>
                    </div>
                    <div class="ip-modal-body">
                        <form action="AddDiscountDoc" class="ajax-form clearfix" autocomplete="off">
                            <input name="TempsIdDiscount" id="TempsIdDiscount" type="hidden" value="">

                            <div class="form-group">
                                <label class="radio-inline"
                                       style="text-decoration:underline;"><?= lang('discount_type') ?> </label>
                                <label class="radio-inline">
                                    <input type="radio" name="DiscountsType" id="inlineRadio1" value="1" checked> %
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="DiscountsType" id="inlineRadio2" value="2"> ₪
                                </label>
                            </div>
                            <div class="form-group">
                                <label><?= lang('discount') ?></label>
                                <input type="text" name="Discounts" id="Discounts" min="0" class="form-control"
                                       placeholder="<?= lang('only_numbers') ?>" onkeypress='validate(event)'>
                            </div>
                    </div>
                    <div class="ip-modal-footer">
                        <div class="ip-actions">
                            <button type="submit" name="submit"
                                    class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
                            <button type="reset" name="reset"
                                    class="btn btn-light"><?php echo lang('clear_form_docs') ?></button>
                        </div>
                        <button type="button" class="btn btn-light ip-close"
                                data-dismiss="modal"><?php echo lang('close') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end DiscountPopup -->
        <!-- VatPopup -->
        <div class="ip-modal text-start" id="VatPopup">
            <div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
                <div class="ip-modal-content">
                    <div class="ip-modal-header">
                        <a class="ip-close" title="Close" style="" data-dismiss="modal">&times;</a>
                        <h4 class="ip-modal-title"><?= lang('set_vat') ?></h4>
                    </div>
                    <div class="ip-modal-body">
                        <form action="AddVatDoc" class="ajax-form clearfix">
                            <input name="TempsIdVat" id="TempsIdVat" type="hidden" value="">
                            <div class="form-group">
                                <label><?= lang('vat') ?></label>
                                <select name="Vats" id="Vats" class="form-control input-lg">
                                    <option value="1"><?= lang('doc_inc_vat') ?></option>
                                    <option value="2" <?= $SettingsInfo->CompanyVat == 1 || $Types == 300 ? 'selected' : '' ?> ><?= lang('doc_not_inc_vat') ?></option>
                                </select>
                            </div>
                    </div>
                    <div class="ip-modal-footer">
                        <div class="ip-actions">
                            <button type="submit" name="submit"
                                    class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
                            <button type="reset" name="reset"
                                    class="btn btn-light"><?php echo lang('clear_form_docs') ?></button>
                        </div>
                        <button type="button" class="btn btn-light ip-close"
                                data-dismiss="modal"><?php echo lang('close') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end DiscountPopup -->

        <!-- CancelPopup -->
        <input type="hidden" id="meTest" data-ip-modal="#CancelPaymentsPopup">
        <div class="ip-modal text-start" id="CancelPaymentsPopup">
            <div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
                <div class="ip-modal-content">
                    <div class="ip-modal-header">
                        <a class="ip-close CancelPaymentsClose" title="Close" style="" data-dismiss="modal">&times;</a>
                        <h4 class="ip-modal-title"><?= lang('cancel_charge') ?></h4>
                    </div>
                    <div class="ip-modal-body">
                        <form action="POSCancelPayments" class="ajax-form clearfix">
                            <input name="TempId" id="CancelPayments_TempsId" type="hidden" value="">
                            <input name="TempListsId" id="CancelPayments_TempsListsId" type="hidden" value="">
                            <input name="CancelPayments_Finalinvoicenum" id="CancelPayments_Finalinvoicenum"
                                   type="hidden" value="">
                            <input name="CancelPayments_TrueFinalinvoicenum" id="CancelPayments_TrueFinalinvoicenum"
                                   type="hidden" value="">
                            <div class="form-group">
                                <label><?= lang('are_you_sure_cancel_charge') ?></label>
                            </div>
                    </div>
                    <div class="ip-modal-footer">
                        <button type="submit" name="submit" class="btn btn-primary"
                                data-dismiss="modal"><?= lang('yes') ?></button>
                        <button type="button" class="btn btn-light ip-close CancelPaymentsClose" data-dismiss="modal">
                            <?= lang('no') ?>
                        </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end CancelPopup -->
        <!-- CancelPopup -->
        <div class="ip-modal text-start" id="CancelDoc">
            <div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
                <div class="ip-modal-content">
                    <div class="ip-modal-header">
                        <a class="ip-close" title="Close" style="" data-dismiss="modal">&times;</a>
                        <h4 class="ip-modal-title"><?= lang('cancel_doc') ?></h4>
                    </div>
                    <div class="ip-modal-body">
                        <form action="POSCancelDocs" class="ajax-form clearfix">
                            <input name="TempIdPOSCancelDocs" id="CancelDocs_TempsId" type="hidden" value="">
                            <div class="form-group">
                                <label><?= lang('are_you_sure_cancel_doc') ?></label>
                            </div>
                            <div class="alertb alert-danger" id="POSCancelDocsError" style="display: none;"><span
                                        id="POSCancelDocsErrorText"></span></div>

                    </div>
                    <div class="ip-modal-footer">
                        <div class="ip-actions">
                            <button type="submit" name="submit" class="btn btn-primary"><?= lang('yes') ?></button>
                        </div>
                        <button type="button" class="btn btn-light ip-close"
                                data-dismiss="modal"><?= lang('no') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end CancelPopup -->

        <div class="ip-modal text-start" id="NikoyPopup">
            <div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
                <div class="ip-modal-content">
                    <div class="ip-modal-header">
                        <a class="ip-close" title="Close" style="">&times;</a>
                        <h4 class="ip-modal-title"><?= lang('set_withholding_tax') ?></h4>
                    </div>
                    <div class="ip-modal-body">
                        <form onsubmit="return NikoyFunction()">
                            <div class="form-group">
                                <label class="radio-inline"
                                       style="text-decoration:underline;"><?= lang('choose_type') ?> </label>
                                <label class="radio-inline">
                                    <input type="radio" name="NikoysType" id="inlineRadio1" value="1" checked> %
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="NikoysType" id="inlineRadio2" value="2"> ₪
                                </label>
                            </div>
                            <div class="form-group">
                                <label><?= lang('withholding_tax') ?></label>
                                <input type="number" name="Nikoys" id="Nikoys" min="0"
                                       value="<?php if (@$SettingsInfo->NikuyMsBamakor != '') {
                                           echo @$SettingsInfo->NikuyMsBamakor;
                                       } else {
                                           echo '0';
                                       } ?>" class="form-control" placeholder="<?= lang('only_numbers') ?>">
                            </div>
                    </div>
                    <div class="ip-modal-footer">
                        <div class="ip-actions">
                            <button type="submit" name="submit" class="btn btn-success"
                                    data-dismiss="modal"><?php echo lang('save_changes_button') ?></button>
                            <button type="reset" name="reset" class="btn btn-dark"
                                    data-dismiss="modal"><?php echo lang('clear_form_docs') ?></button>
                        </div>
                        <button type="button" class="btn btn-default ip-close"
                                data-dismiss="modal"><?php echo lang('close') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="MakePaymentInput" value="">
        <script>
            $(document).ready(function () {
                $('.summernote').summernote({
                    placeholder: '<?= lang('type_notes_to_doc') ?>',
                    tabsize: 2,
                    height: 100,
                    toolbar: [
                        // [groupName, [list of button]]
                        ['style', ['bold', 'italic', 'underline', 'clear']],
                        ['font', ['strikethrough']],
                        ['para', ['ul', 'ol']]
                    ]
                });
            });
            $("#Chash").click(function () {
                $("#Cahshdiv").show();
                $("#Checkdiv").hide();
                $("#Bankdiv").hide();
                $("#Creditdiv").hide();
                updateTotalAmount();
                let paymentsTable = $('#DocsPayments').find('table').find('tbody');
                if(paymentsTable.find('tr').length) {
                    $('#ReceiptBtn').attr("disabled", false);
                } else {
                    $('#ReceiptBtn').attr("disabled", true);
                }
                closePaymentIframe();
            });
            $("#Check").click(function () {
                $("#Checkdiv").show();
                $("#Cahshdiv").hide();
                $("#Bankdiv").hide();
                $("#Creditdiv").hide();
                updateTotalAmount();
                let paymentsTable = $('#DocsPayments').find('table').find('tbody');
                if(paymentsTable.find('tr').length) {
                    $('#ReceiptBtn').attr("disabled", false);
                } else {
                    $('#ReceiptBtn').attr("disabled", true);
                }
                closePaymentIframe();
            });
            $("#Bank").click(function () {
                $("#Bankdiv").show();
                $("#Checkdiv").hide();
                $("#Cahshdiv").hide();
                $("#Creditdiv").hide();
                updateTotalAmount();
                let paymentsTable = $('#DocsPayments').find('table').find('tbody');
                if(paymentsTable.find('tr').length) {
                    $('#ReceiptBtn').attr("disabled", false);
                } else {
                    $('#ReceiptBtn').attr("disabled", true);
                }
                closePaymentIframe();
            });

            $("#Credit").click(function () {
                $("#Creditdiv").show();
                $("#Bankdiv").hide();
                $("#Checkdiv").hide();
                $("#Cahshdiv").hide();
                updateTotalAmount();
                var Finalinvoicenum = $('#Finalinvoicenum').val();
                $("CreditValue3").attr("disabled", true);
                $("CreditValue3").val(Finalinvoicenum);
                $('#ReceiptBtn').attr("disabled", true);
            });

            var delay = (function () {
                var timer = 0;
                return function (callback, ms) {
                    clearTimeout(timer);
                    timer = setTimeout(callback, ms);
                };
            })();
            $("#CC2").on('change keydown paste input', function () {
                delay(function () {
                    $('#CreditValueButton').attr("disabled", false);
                }, 600);

            });

            $(function () {
                var time = function () {
                    return '?' + new Date().getTime()
                };
                $('#PaymentPopup').imgPicker({});
                $('#NikoyPopup').imgPicker({});
                $('#CreditPaymentNew').imgPicker({});
                $('#DiscountPopup').imgPicker({});
                $('#VatPopup').imgPicker({});
                $('#CancelDoc').imgPicker({});

            });

            function NikoyFunction() {
                var Nikoys = $('#Nikoys').val();
                var Sumtotal = $('#TotalHide2').val();
                var NikoysType = $("input[name='NikoysType']:checked").val();
//alert(Sumtotal);
                if (NikoysType == '1') {
                    $('#NikuyMsBamakor').val(Nikoys);
//$('#TotalHide8').html(Nikoys);
                    $('#NikuyMsBamakorHTML').html(Nikoys);
                    $('#NikuyMsBamakorHTMLSign').html('%');
                    $('#TotalHide8').html(Sumtotal * Nikoys / 100);
                    $('#TotalHide7').html(Sumtotal - (Sumtotal * Nikoys / 100));
                    $('#Finalinvoicenum').val(Sumtotal - (Sumtotal * Nikoys / 100));
                    $('#TotalHide2').val(Sumtotal);
                    $('#cleanmas').val(Sumtotal * Nikoys / 100);
                    $('#totalHide').val(Sumtotal);
                    $('#NikoysTypes').val('1');
                    $("#NikoyPopup").fadeOut("fast");
                } else if (NikoysType == '2') {
                    $('#NikuyMsBamakor').val(Nikoys);
//$('#TotalHide8').html(Nikoys);
                    $('#NikuyMsBamakorHTML').html(Nikoys);
                    $('#NikuyMsBamakorHTMLSign').html('₪');
                    $('#TotalHide8').html(Nikoys);
                    $('#TotalHide7').html(Sumtotal - Nikoys);
                    $('#Finalinvoicenum').val(Sumtotal - Nikoys);
                    $('#TotalHide2').val(Sumtotal);
                    $('#cleanmas').val(Nikoys);
                    $('#totalHide').val(Sumtotal);
                    $('#NikoysTypes').val('2');
                    $("#NikoyPopup").fadeOut("fast");

                }
                return false;
            }

            $('.CancelPaymentsClose').click(function () {
                var modal = $('#CancelPaymentsPopup');
                modal.modal('hide');
            });

            function showCredit(select_item) {
                $('.credit-card-type').hide();
                $('.credit-card-type[data-type=' + select_item + ']').show();

                closePaymentIframe();

                var ClientIds = document.getElementById('Client').value;
                var TempId = document.getElementById('DocTempGroupNumber').value;
                var Finalinvoicenum = $('#Finalinvoicenum').val();
                var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();

                $("#DocsPayments").load("DocPaymentInfoReceipt.php?TypeDoc=<?php echo $Types; ?>&TempId=" + TempId + "&CheckRefresh=2&Act=3&ClientId=" + ClientIds + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum);
            }

            $(document).on("keypress", "form", function (event) {
                return event.keyCode != 13;
            });

            function openPaymentIframe($btn, data, action, callback) {
                var $thisButton = $btn;
                var $iframe = $thisButton.siblings('.iframe-wrapper').find('iframe.add-new-card-iframe');
                var orderType = $thisButton.attr('data-order-type');

                $thisButton.find('.js-btn-text').addClass('d-none');
                $thisButton.find('.js-loading-label').removeClass('d-none');
                $thisButton.find('.spinner-border').removeClass('d-none');

                $iframe.parent().addClass('d-none');

                $.ajax({
                    url: '/office/payment/Payment.php',
                    data: {
                        action: action,
                        ClientId: data.ClientId,
                        amount: data.amount,
                        orderType: orderType,
                        TempId: data.TempId,
                        TypeDoc: data.TypeDoc,
                        paymentNumber: data.paymentNumber
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response);

                        if (response.status === 'success') {
                            $iframe.parents('.iframe-wrapper').show();

                            $iframe.attr('src', response.url);
                            $iframe.on('load', function () {
                                $thisButton.find('.js-btn-text').removeClass('d-none');
                                $thisButton.find('.js-loading-label').addClass('d-none');
                                $thisButton.find('.spinner-border').addClass('d-none');

                                $iframe.parent().removeClass('d-none');

                                $('html,body').animate({scrollTop: $iframe.offset().top - 60});
                            });
                        }

                        if (response.status === 'error') {
                            Swal.fire({
                                text: response.message,
                                icon: 'error'
                            });
                        }

                        window.paymentStatus = 'waiting';
                        window.paymentType = null;

                        if (callback) {
                            return callback(response);
                        }
                    },
                    error: function (response) {
                        console.error(response);

                        Swal.fire({
                            text: response.message,
                            icon: 'error'
                        });

                        $thisButton.find('.js-btn-text').removeClass('d-none');
                        $thisButton.find('.js-loading-label').addClass('d-none');
                        $thisButton.find('.spinner-border').addClass('d-none');
                    }
                });
            }

            function closePaymentIframe() {
                var $buttonIframe = $('.js-pay-new-card-iframe-button');
                var $iframe = $buttonIframe.siblings('.iframe-wrapper').find('iframe');

                var orderType = $buttonIframe.attr('data-order-type');
                var TempId = document.getElementById('DocTempGroupNumber').value;

                $iframe.parent().addClass('d-none');
                if ($iframe.attr('src')) {
                    $iframe.attr('src', null);
                }

                $.ajax({
                    url: '/office/payment/Payment.php',
                    data: {
                        action: 'cleanTempPayment',
                        TempId: TempId,
                        orderType: orderType,
                    }
                });
            }

            $(document).ready(function () {
                window.updateTotalAmount = function () {
                    setTimeout(function () {
                        var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
                        console.log('updateTotalAmount');
                        $('#CashValue').val(TrueFinalinvoicenum);
                        //$('#CreditValue').val((+$('#resultFinal2').text()).toFixed(2));
                        //$('#CreditValue2').val((+$('#resultFinal2').text()).toFixed(2));
                        //$('#CreditValue3').val((+$('#resultFinal2').text()).toFixed(2));
                        $('#CreditValue').val(TrueFinalinvoicenum);
                        $('#CreditValue2').val(TrueFinalinvoicenum);
                        $('#CreditValue3').val(TrueFinalinvoicenum);
                        $('#CreditValue4').val(TrueFinalinvoicenum);
                        //$('#CreditValue4').val((+$('#resultFinal2').text()).toFixed(2));
                        $('#CheckValue').val(TrueFinalinvoicenum);
                        $('#BankValue').val(TrueFinalinvoicenum);
                        if(TrueFinalinvoicenum == 0){
                            $('#ReceiptBtn').attr("disabled", false);
                            $('#CashValue').attr('min', 0);
                            $('#CheckValue').attr('min', 0);
                            $('#BankValue').attr('min', 0);
                            $('#CreditValue').attr('min', 0);
                            $('#CreditValue2').attr('min', 0);
                            $('#CreditValue3').attr('min', 0);
                            $('#CreditValue4').attr('min', 0);
                        }
                    }, 500);
                };

                <?php
                if (@$EditTempId == ''){
            } else {
                ?>
                $("#GetItems").load('UpdatesItems.php?TempId=<?php echo @$EditTempId ?>&TypeDoc=<?php echo $Types; ?>' + '#MeItem');
                <?php
                }
                ?>

                $(".selectclient").select2({
                    theme: "bootstrap",
                    placeholder: "Select a State",
                    maximumSelectionSize: 6,
                    language: "he",
                    allowClear: false
                });

                <?php if ($Types == '12'){ ?>

                $('#Items6').on('change', function () {
                    if (this.value == '') {
                    } else {
                        var ClientId = document.getElementById('Client').value;
                        var url = 'GetInvoice.php?TypeDoc=<?php echo $Types; ?>&ItemId=' + this.value + '&ClientId=' + ClientId;
                        $('#GetItems').load(url + '#MeItem');
                    }
                });

                <?php }  ?>

                $('#Items1').on('change', function () {
                    if (this.value == '') {
                    } else {
                        var ClientId = document.getElementById('Client').value;
                        $("#GetItems").load('UpdatesItems.php?TypeDoc=<?php echo $Types; ?>&ItemId=' + this.value + '&ClientId=' + ClientId + '#MeItem', null, window.updateTotalAmount);
                    }
                });

                function htmlspecialchars(str) {
                    return str.replace('&', '&amp;').replace('"', '&quot;').replace("'", '&#039;').replace('<', '&lt;').replace('>', '&gt;').replace(' ', '&nbsp;');
                }

                $('#TextValueButton').click(function () {

                    var TextValue = encodeURI(document.getElementById('TextValue').value);
                    var ClientIds = document.getElementById('Client').value;
                    $("#GetItems").load("UpdatesItems.php?TypeDoc=<?php echo $Types; ?>&ItemId=<?php echo $GeneralItemId; ?>&ItemTextNew=" + TextValue + "&ClientId=" + ClientIds + "#MeItem", null, window.updateTotalAmount);

                });

                $('#CashValueButton').click(function () {
                    var CashValue = encodeURI(document.getElementById('CashValue').value);
                    var ClientIds = document.getElementById('Client').value;
                    var TempId = document.getElementById('DocTempGroupNumber').value;
                    var Finalinvoicenum = $('#Finalinvoicenum').val();
                    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
                    updateTotalAmount();
                    if (CashValue != '' && TempId != '' && parseFloat(CashValue) > 0 && parseFloat(CashValue) <= parseFloat(TrueFinalinvoicenum)) {
                        $("#DocsPayments").load("DocPaymentInfoReceipt.php?TypeDoc=<?php echo $Types; ?>&TempId=" + TempId + "&CashValue=" + CashValue + "&Act=1&ClientId=" + ClientIds + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum);
                        BN('0', '<?= lang('received_payment_in_cash') ?>');
                        if(TrueFinalinvoicenum == 0)
                            $('#ReceiptBtn').attr("disabled", false);
                    } else {
                        BN('1', '<?= lang('typing_sum_is_required') ?>');
                    }

                });
                $('#CheckValueButton').click(function () {
                    var CheckValue = encodeURI(document.getElementById('CheckValue').value);
                    var CheckDate = encodeURI(document.getElementById('CheckDate').value);
                    var CheckSnif = encodeURI(document.getElementById('CheckSnif').value);
                    var CheckAccount = encodeURI(document.getElementById('CheckAccount').value);
                    var CheckBank = encodeURI(document.getElementById('CheckBank').value);
                    var CheckNumber = encodeURI(document.getElementById('CheckNumber').value);
                    var ClientIds = document.getElementById('Client').value;
                    var TempId = document.getElementById('DocTempGroupNumber').value;
                    var Finalinvoicenum = $('#Finalinvoicenum').val();
                    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
                    updateTotalAmount();
                    if (CheckValue != '' && CheckDate != '' && CheckNumber != '' && TempId != '' && parseFloat(CheckValue) > 0 && parseFloat(CheckValue) <= parseFloat(TrueFinalinvoicenum)) {
                        $("#DocsPayments").load("DocPaymentInfoReceipt.php?TypeDoc=<?php echo $Types; ?>&TempId=" + TempId + "&CheckValue=" + CheckValue + "&CheckDate=" + CheckDate + "&CheckSnif=" + CheckSnif + "&CheckAccount=" + CheckAccount + "&CheckBank=" + CheckBank + "&CheckNumber=" + CheckNumber + "&Act=2&ClientId=" + ClientIds + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum);
                        BN('0', '<?= lang('received_payment_in_check') ?>');
                        if(TrueFinalinvoicenum == 0)
                            $('#ReceiptBtn').attr("disabled", false);
                    } else {
                        BN('1', '<?= lang('typing_check_details_is_required') ?>');
                    }
                });
                $('#CreditValueButton').click(function () {
                    var CreditValue = encodeURI(document.getElementById('CreditValue').value);
                    var CC2 = encodeURI(document.getElementById('CC2').value);
                    var Tash1 = encodeURI(document.getElementById('Tash1').value);
                    var tashType1 = encodeURI(document.getElementById('tashType1').value);
                    var ClientIds = document.getElementById('Client').value;
                    var TempId = document.getElementById('DocTempGroupNumber').value;
                    var Finalinvoicenum = $('#Finalinvoicenum').val();
                    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
                    $('#CreditValue').attr("disable", true);
                    $('#CreditValue').val(TrueFinalinvoicenum);
                    if (CreditValue != '' && CC2 != '' && Tash1 != '' && tashType1 != '' && TempId != '' && parseFloat(CreditValue) > 0 && parseFloat(CreditValue) == parseFloat(TrueFinalinvoicenum)) {
                        $("#DocsPayments").load("DocPaymentInfoReceipt.php?TypeDoc=<?php echo $Types; ?>&TempId=" + TempId + "&CreditValue=" + CreditValue + "&Tash=" + Tash1 + "&tashType=" + tashType1 + "&CC2=" + CC2 + "&Act=3&Credit=1&ClientId=" + ClientIds + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum);
                        BN('2', '<?= lang('charging_please_wait_processing_data') ?>');
                    } else {
                        BN('1', '<?= lang('type_sum_required_swipe_card') ?>');
                    }
                });

                $('#Credit2ValueButton').click(function () {
                    var CreditValue2 = encodeURI(document.getElementById('CreditValue2').value);
                    var CC3 = encodeURI(document.getElementById('CC3').value);
                    var Tash2 = encodeURI(document.getElementById('Tash2').value);
                    var tashType2 = encodeURI(document.getElementById('tashType2').value);
                    var ClientIds = document.getElementById('Client').value;
                    var TempId = document.getElementById('DocTempGroupNumber').value;
                    var Finalinvoicenum = $('#Finalinvoicenum').val();
                    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
                    $('#CreditValue2').attr("disable", true);
                    $('#CreditValue2').val(TrueFinalinvoicenum);
                    if (CreditValue2 != '' && CC3 != '' && Tash2 != '' && tashType2 != '' && TempId != '' && parseFloat(CreditValue2) > 0 && parseFloat(CreditValue2) == parseFloat(TrueFinalinvoicenum)) {
                        $("#DocsPayments").load("DocPaymentInfoReceipt.php?TypeDoc=<?php echo $Types; ?>&TempId=" + TempId + "&CreditValue=" + CreditValue2 + "&Tash=" + Tash2 + "&tashType=" + tashType2 + "&CC3=" + CC3 + "&Act=3&Credit=2&ClientId=" + ClientIds + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum);
                        BN('2', '<?= lang('charging_please_wait_processing_data') ?>');
                        if(Finalinvoicenum == 0)
                            $('#ReceiptBtn').attr("disabled", false);
                    } else {
                        BN('1', '<?= lang('sum_required_choose_credit_card') ?>');
                    }
                });

                $('#Credit3ValueButton').click(function () {
                    var CreditValue3 = encodeURI(document.getElementById('CreditValue3').value);
                    var CC = encodeURI(document.getElementById('CC').value);
                    var Tash3 = encodeURI(document.getElementById('Tash3').value);
                    var tashType3 = encodeURI(document.getElementById('tashType3').value);
                    var Tmonth = encodeURI(document.getElementById('Tmonth').value);
                    var Tyear = encodeURI(document.getElementById('Tyear').value);
                    var Cvv = encodeURI(document.getElementById('Cvv').value);
                    var CCId = encodeURI(document.getElementById('CCId').value);
                    var ClientIds = document.getElementById('Client').value;
                    var TempId = document.getElementById('DocTempGroupNumber').value;
                    var Finalinvoicenum = $('#Finalinvoicenum').val();
                    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
                    $('#CreditValue3').attr("disable", true);
                    $('#CreditValue3').val(TrueFinalinvoicenum);
                    if (CreditValue3 != '' && CC != '' && Tash3 != '' && tashType3 != '' && Tmonth != '' && Tyear != '' && CCId != '' && TempId != '' && TempId != '' && parseFloat(CreditValue3) > 0 && parseFloat(CreditValue3) == parseFloat(TrueFinalinvoicenum)) {
                        $("#DocsPayments").load("DocPaymentInfoReceipt.php?TypeDoc=<?php echo $Types; ?>&TempId=" + TempId + "&CreditValue=" + CreditValue3 + "&Tash=" + Tash3 + "&tashType=" + tashType3 + "&CC=" + CC + "&Tmonth=" + Tmonth + "&Tyear=" + Tyear + "&Cvv=" + Cvv + "&CCId=" + CCId + "&Act=3&Credit=3&ClientId=" + ClientIds + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum);
                        BN('2', '<?= lang('charging_please_wait_processing_data') ?>');
                        if(Finalinvoicenum == 0)
                            $('#ReceiptBtn').attr("disabled", false);
                    } else {
                        BN('1', '<?= lang('typing_manually_charge_require_all_fields') ?>');
                    }
                });

                $("#CreateNewPayments").click(function (e) {
                    e.preventDefault();

                    var $button = $(this);

                    var CreditValue3 = encodeURI(document.getElementById('CreditValue3').value);
                    var Tash3 = encodeURI(document.getElementById('Tash3').value);
                    var tashType3 = encodeURI(document.getElementById('tashType3').value);
                    var ClientIds = document.getElementById('Client').value;
                    <?php if ($Types == '320') { ?>
                    var TempId = document.getElementById('DocTempId').value;
                    <?php } else { ?>
                    var TempId = document.getElementById('DocTempGroupNumber').value;
                    <?php } ?>
                    var Finalinvoicenum = $('#Finalinvoicenum').val();
                    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
                    var ClientsName = $('#DocFullName').val();
                    var ClientMobile = $('#DocPhone').val();
                    if (CreditValue3 != '' && Tash3 != '' && ClientIds != '' && tashType3 != '' && TempId != '' && TempId != '' && parseFloat(CreditValue3) > 0 && parseFloat(CreditValue3) == parseFloat(TrueFinalinvoicenum)) {
                        // open iframe for payment
                        let data = {
                            ClientId: ClientIds,
                            TempId: TempId,
                            TypeDoc: <?php echo $Types; ?>,
                            paymentNumber: Tash3,
                            amount: CreditValue3
                        }

                        openPaymentIframe($button, data, 'payWithNewCard', function (response) {
                            var checkPaymentStatus = setInterval(function () {
                                if (window.paymentStatus !== 'waiting') {
                                    $button.siblings('.iframe-wrapper').hide();

                                    if (window.paymentStatus === 'error') {
                                        Swal.fire({
                                            text: lang('processing_error_meshulam'),
                                            icon: 'error'
                                        });

                                        clearInterval(checkPaymentStatus);
                                    }

                                    if (window.paymentStatus == 'success' || window.paymentStatus == 'success_meshulam') {
                                        $("#DocsPayments").load("DocPaymentInfoReceipt.php?TypeDoc=<?php echo $Types; ?>&TempId=" + TempId + "&CreditValue=" + CreditValue3 + "&Tash=" + Tash3 + "&tashType=" + tashType3 + "&Act=3&Credit=3&ClientId=" + ClientIds + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum);

                                        Swal.fire({
                                            text: lang('processing_done_meshulam'),
                                            icon: 'success'
                                        });

                                        clearInterval(checkPaymentStatus);
                                    }
                                }

                            }, 1500);
                        });

                        BN('2', '<?= lang('connection_to_meshulad_wait') ?>');
                    } else {
                        BN('1', '<?= lang('charge_required_all_or_type_sum_membership') ?>');
                    }
                });

                $('#Credit4ValueButton').click(function () {
                    var CreditValue = encodeURI(document.getElementById('CreditValue4').value);
                    var CC = encodeURI(document.getElementById('CC4').value);
                    var CDate = encodeURI(document.getElementById('CDate4').value);
                    var CCode = encodeURI(document.getElementById('CCode4').value);
                    var Tash = encodeURI(document.getElementById('Tash4').value);
                    var tashType = encodeURI(document.getElementById('tashType4').value);
                    var TypeBank = encodeURI(document.getElementById('TypeBank4').value);
                    var TypeBrand = encodeURI(document.getElementById('TypeBrand4').value);

                    var ClientIds = document.getElementById('Client').value;
                    var TempId = document.getElementById('DocTempGroupNumber').value;
                    var Finalinvoicenum = $('#Finalinvoicenum').val();
                    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
                    if (CreditValue != '' && CC != '' && Tash != '' && tashType != '' && TypeBank != '' && TypeBrand != '' && TempId != '' && CDate != '' && CCode != '' && parseFloat(CreditValue) > 0 && parseFloat(CreditValue) <= parseFloat(TrueFinalinvoicenum)) {
                        $("#DocsPayments").load("DocPaymentInfoReceipt.php?TypeDoc=<?php echo $Types; ?>&TempId=" + TempId + "&CreditValue=" + CreditValue + "&Tash=" + Tash + "&tashType=" + tashType + "&CC=" + CC + "&TypeBank=" + TypeBank + "&TypeBrand=" + TypeBrand + "&CCode=" + CCode + "&Act=3&Credit=4&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum + "&CDate=" + CDate + "&CCode=" + CCode + "&ClientId=" + ClientIds);
                        BN('2', '<?= lang('saving_wait_while_processing') ?>');
                        $('#ReceiptBtn').attr("disabled", false);
                    } else {
                        BN('1', '<?= lang('save_transaction_other_terminal') ?>');
                    }
                });

                $('#BankValueButton').click(function () {
                    var BankValue = encodeURI(document.getElementById('BankValue').value);
                    var BankDate = encodeURI(document.getElementById('BankDate').value);
                    var BankNumber = encodeURI(document.getElementById('BankNumber').value);
                    var ClientIds = document.getElementById('Client').value;
                    var TempId = document.getElementById('DocTempGroupNumber').value;
                    var Finalinvoicenum = $('#Finalinvoicenum').val();
                    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
                    updateTotalAmount();
                    if (CheckValue != '' && BankDate != '' && BankNumber != '' && TempId != '' && parseFloat(BankValue) > 0 && parseFloat(BankValue) <= parseFloat(TrueFinalinvoicenum)) {
                        $("#DocsPayments").load("DocPaymentInfoReceipt.php?TypeDoc=<?php echo $Types; ?>&TempId=" + TempId + "&BankValue=" + BankValue + "&BankDate=" + BankDate + "&BankNumber=" + BankNumber + "&Act=4&ClientId=" + ClientIds + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum);
                        BN('0', '<?= lang('bank_transfer_detail_received') ?>');
                        if(TrueFinalinvoicenum == 0)
                            $('#ReceiptBtn').attr("disabled", false);
                    } else {
                        BN('1', '<?= lang('ref_number_date_sum') ?>');
                    }
                });

                $('#Client').change(function () {
                    var divID = $(this).children('option:selected').attr('class');
                    var Vat = $(this).children('option:selected').attr('data-vat');
                    var ClientId = $(this).children('option:selected').val();
                    $('#PaymentSubmit').attr("disabled", false);
                    ////  בדיקת טוקן
                    var urls = 'ChangeToken.php?ClientId=' + ClientId;
                    $('#ChangeTokenI').load(urls + '#ChangeTokenI');

                    if (Vat == '17') {
                        $('#resultVATIn').html('17');
                    } else {
                        $('#resultVATIn').html('0');
                    }

                    if (ClientId == '0') {
                        /// מעבר לפתיחת לקוח חדש
                        newclient.style.display = 'block';
                        $('#DocFullName').prop('required', true);
                        $('#DocPhone').prop('required', true);
                    } else {
                        newclient.style.display = 'none';
                        $('#DocFullName').prop('required', false);
                        $('#DocPhone').prop('required', false);
                    }

                });
//// תשלומים
                <?php
                $starting_tash = 2;
                $ending_tash = 12;
                for ($starting_tash; $starting_tash <= $ending_tash; $starting_tash++) {
                    $tash[] = "<option value='" . $starting_tash . "'>" . $starting_tash . "</option>";
                }
                ?>
                <?php
                $starting_tashc = 3;
                $ending_tashc = 36;
                for ($starting_tashc; $starting_tashc <= $ending_tashc; $starting_tashc++) {
                    $tashc[] = "<option value='" . $starting_tashc . "'>" . $starting_tashc . "</option>";
                }
                ?>

                $(".tashType").change(function () {
                    var val = $(this).val();
                    $('.Tash').parent().show();

                    if (val == "0") {
                        $(".Tash").html("<option value='1'>1</option>");
                        $('.Tash').parent().hide();
                    } else if (val == "1") {
                        $(".Tash").html("<?php echo implode("", @$tash);?>");
                    } else if (val == "2") {
                        $(".Tash").html("<option value='1'>1</option>");
                    } else if (val == "3") {
                        $(".Tash").html("<option value='1'>1</option>");
                    } else if (val == "6") {
                        $(".Tash").html("<?php echo implode("", @$tashc);  ?>");
                    }
                    $('.tashType').val(val);
                });

                $(".tashType").change();
//// חיפוש שירותים
                $(".select3").select2({
                    minimumInputLength: 3,
                    theme: "bootstrap",
                    language: "<?php echo isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] == "eng" ? 'en' : 'he' ?>",
                    placeholder: "<?= lang('choose_item_from_existing_price_list') ?>",
                    allowClear: true,
                    ajax: {
                        url: "SearchItem.php",
                        type: 'POST',
                        dataType: 'json',
                        cache: true
                    },
                });

            });


            <?php if ($Types == '400'){ ?>
            function myFunctionnew(value) {
                if (value != "All") {
                    $.ajax(
                        {
                            type: "GET",
                            url: 'action/GetClient.php',
                            data: {company_id: value},
                            success: function (data) {
                                $('#resultDivnew').html(data);
                            }
                        });
                } else {
                    $('#resultDivnew').html("<?= lang('choose_client_required') ?>");
                }
            }
            <?php } else if ($Types == '2') { ?>
            function myFunctionnew(value) {
                if (value != "All") {
                    $.ajax(
                        {
                            type: "GET",
                            url: 'action/GetClientRefound.php',
                            data: {company_id: value},
                            success: function (data) {
                                $('#resultDivnew').html(data);
                            }
                        });
                } else {
                    $('#resultDivnew').html("<?= lang('choose_client_required') ?>");
                }
            }
            <?php } ?>
            <?php if ($Types == '320' || $Types == '400' || $Types == '2') { ?>
            $("#ReceiptBtn").click(function () {
                    $('#MakePaymentInput').val('2').trigger("change");
                }
            );
            $("#CancelDocButton").click(function () {
                    $('#MakePaymentInput').val('2').trigger("change");
                }
            );
            $(".CancelPayments").click(function () {
                    $('#MakePaymentInput').val('2').trigger("change");
                }
            );
            $(".ip-close").click(function () {
                    $('#MakePaymentInput').val('1').trigger("change");
                }
            );

            var MakePayments = '';
            $("#MakePaymentInput").on("change", function () {
                var MakePaymentInput = $(this).val();
                if (MakePaymentInput == '1') {
                    MakePayments = true;
                } else {
                    MakePayments = '';
                }
            });

            $('a').mousedown(function (e) {
                var MakePaymentInput = $('#MakePaymentInput').val();
                if (MakePayments) {
                    // if the user navigates away from this page via an anchor link,
                    //    popup a new boxy confirmation.
                    alert("<?= lang('you_have_charged_save_or_cancel_required') ?>");
                }
            });
            window.onbeforeunload = function () {
                if ((MakePayments)) {
                    // call this if the box wasn't shown.
                    return '<?= lang('you_have_charged_save_or_cancel_required') ?>';
                }
            };
            <?php } ?>

            $(document).ready(function () {
                //detect javascript from credit card form
                window.addEventListener('message', function (e) {
                    if (e.data.hasOwnProperty("MeshulamActiveLoader_nauK1M54J") && e.data.MeshulamActiveLoader_nauK1M54J == 1) {
                        //do your code like display loader //
                        var spinnerVisible = false;
//    $('.payment_loader').show();
                        if (!spinnerVisible) {
                            $(".payment_loader").fadeIn("fast");
                            spinnerVisible = true;
                            setTimeout(function () {
                                $("#Text1").show();
                                setTimeout(function () {
                                    $("#Text1").hide();
                                    //  toggleDiv();
                                }, 15000);
                            }, 1000);

                            setTimeout(function () {
                                $("#Text2").show();
                                setTimeout(function () {
                                    $("#Text2").hide();
                                    // toggleDiv();
                                }, 15000);
                            }, 17000);

                            setTimeout(function () {
                                $("#Text3").show();
                                setTimeout(function () {
                                    $("#Text3").hide();
                                    //  toggleDiv();
                                }, 35000);
                            }, 32000);

                        }

                    }
                }, true);
            });

        </script>
        <style>
            div#spinners {
                display: table;
                width: 100%;
                height: 100%;
                position: fixed;
                top: 0%;
                left: 0%;
                background: url(assets/img/Preloader_8.gif) no-repeat center rgba(255, 255, 255, .5);
                text-align: center;
                padding: 10px;
                font: normal 16px "Rubik", Geneva, sans-serif;
                margin-left: 0px;
                margin-top: 0px;
                z-index: 10000;
                overflow: auto;
            }

            #spinners #b {
                display: table-cell;
                padding-top: 350px;

                text-align: center;
                vertical-align: middle;
            }

            #spinners span {
                font-size: 18px;
                font-weight: 400;
                background-color: white;
                padding: 10px;
                margin: auto;
            }

            .DivCreateToken {
                margin-top: 20px;
                margin-bottom: 30px;
                border: hidden;
                overflow: hidden;
            }
        </style>
        <div id="spinners" class="payment_loader" style="display: none;">
            <div id="b">
                <span id="Text1" style="display: none;"><?= lang('charging_do_not_close_window') ?></span>
                <span id="Text2" style="display: none;"><?= lang('atm_charging_pay') ?></span>
                <span id="Text3" style="display: none;"><?= lang('thanks_for_waiting') ?></span>
            </div>
        </div>
    <?php else: ?>
        <?php redirect_to('../index.php'); ?>
    <?php endif ?>

<?php endif ?>
<?php if (Auth::guest()): ?>
    <?php redirect_to('../index.php'); ?>
<?php endif ?>
<?php require_once '../app/views/footernew.php'; ?>
