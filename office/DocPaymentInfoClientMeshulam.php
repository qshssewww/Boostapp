<?php

require_once '../app/initcron.php';

$StatusreditCard = array(
    0 => lang('transaction_approved_meshulam'),
    1 => lang('doc_meshulam_1'),
    2 => lang('doc_meshulam_2'),
    3 => lang('doc_meshulam_3'),
    4 => lang('doc_meshulam_4'),
    5 => lang('doc_meshulam_5'),
    6 => lang('doc_meshulam_6'),
    7 => lang('doc_meshulam_7'),
    19 => lang('doc_meshulam_19'),
    33 => lang('doc_meshulam_33'),
    34 => lang('doc_meshulam_34'),
    35 => lang('doc_meshulam_35'),
    36 => lang('doc_meshulam_36'),
    37 => lang('doc_meshulam_37'),
    38 => lang('doc_meshulam_38'),
    39 => lang('doc_meshulam_39'),
    57 => lang('doc_meshulam_57'),
    58 => lang('doc_meshulam_58'),
    69 => lang('doc_meshulam_69'),
    101 => lang('doc_meshulam_101'),
    106 => lang('doc_meshulam_106'),
    107 => lang('doc_meshulam_107'),
    110 => lang('doc_meshulam_110'),
    111 => lang('doc_meshulam_111'),
    112 => lang('doc_meshulam_112'),
    113 => lang('doc_meshulam_113'),
    114 => lang('doc_meshulam_114'),
    118 => lang('doc_meshulam_118'),
    119 => lang('doc_meshulam_119'),
    124 => lang('doc_meshulam_124'),
    125 => lang('doc_meshulam_125'),
    127 => lang('doc_meshulam_127'),
    129 => lang('doc_meshulam_129'),
    133 => lang('doc_meshulam_133'),
    138 => lang('doc_meshulam_138'),
    146 => lang('doc_meshulam_146'),
    150 => lang('doc_meshulam_150'),
    151 => lang('doc_meshulam_151'),
    156 => lang('doc_meshulam_156'),
    160 => lang('doc_meshulam_160'),
    161 => lang('doc_meshulam_161'),
    162 => lang('doc_meshulam_162'),
    163 => lang('doc_meshulam_163'),
    164 => lang('doc_meshulam_164'),
    169 => lang('doc_meshulam_169'),
    171 => lang('doc_meshulam_171'),
    172 => lang('doc_meshulam_172'),
    173 => lang('doc_meshulam_173'),
    200 => lang('doc_meshulam_200'),
    251 => lang('doc_meshulam_251'),
    260 => lang('doc_meshulam_260'),
    280 => lang('doc_meshulam_280'),
    349 => lang('doc_meshulam_349'),
    447 => lang('doc_meshulam_447'),
    901 => lang('doc_meshulam_901'),
    902 => lang('doc_meshulam_902'),
    920 => lang('doc_meshulam_920'),
    998 => lang('doc_meshulam_998'),
    999 => lang('doc_meshulam_999')

);


$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
$Dates = date('Y-m-d H:i:s');
$UserDate = date('Y-m-d');
$CheckRefresh = @$_REQUEST['CheckRefresh'];
$CreditStatus = '0';

$TypeShva = '1';
$MeshulamAPI = $SettingsInfo->MeshulamAPI;
$MeshulamUserId = $SettingsInfo->MeshulamUserId;
$LiveMeshulam = $SettingsInfo->LiveMeshulam;


if (@$CheckRefresh == '2') {

    $TempId = $_REQUEST['TempId'];
    $TypeDoc = $_REQUEST['TypeDoc'];
    $TrueFinalinvoicenum = $_REQUEST['TrueFinalinvoicenum'];
    $Act = '999';

} else {

    $Act = $_REQUEST['Act'];
    $TempId = $_REQUEST['TempId'];
    $TypeDoc = $_REQUEST['TypeDoc'];
    $Finalinvoicenum = $_REQUEST['Finalinvoicenum'];
    $TrueFinalinvoicenum = $_REQUEST['TrueFinalinvoicenum'];

    $GetAmountNow = DB::table('temp_receipt_payment_client')->where('TempId', '=', $TempId)->where('TypeDoc', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->sum('Amount');
    $GetAmountExcessNow = DB::table('temp_receipt_payment_client')->where('TempId', '=', $TempId)->where('TypeDoc', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->sum('Excess');

    if ($Act == '1') {

        $CashValue = $_REQUEST['CashValue'];

        $TotalCash = $CashValue;
        $Excess = '0';


        DB::table('temp_receipt_payment_client')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '1', 'Amount' => $TotalCash, 'CheckDate' => $UserDate, 'Dates' => $Dates, 'UserId' => $UserId, 'Excess' => '0', 'UserDate' => $UserDate));

    } else if ($Act == '2') {

        $CheckValue = $_REQUEST['CheckValue'];
        $CheckDate = $_REQUEST['CheckDate'];
        $CheckSnif = $_REQUEST['CheckSnif'];
        $CheckAccount = $_REQUEST['CheckAccount'];
        $CheckBank = $_REQUEST['CheckBank'];
        $CheckNumber = $_REQUEST['CheckNumber'];

        DB::table('temp_receipt_payment_client')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '2', 'Amount' => $CheckValue, 'CheckBank' => $CheckAccount, 'CheckBankSnif' => $CheckSnif, 'CheckBankCode' => $CheckBank, 'CheckNumber' => $CheckNumber, 'CheckDate' => $CheckDate, 'Dates' => $Dates, 'UserId' => $UserId, 'UserDate' => $UserDate));

    } else if ($Act == '3') {

        $ActPaymetns = '0';

        $CheckPayments = DB::table('temp_receipt_payment_client')->where('TempId', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->where('TypePayment', '=', '3')->where('Amount', '=', $_REQUEST['CreditValue'])->first();

        if (@$CheckPayments->id != '') {

            DB::table('temp_receipt_payment_client')
                ->where('id', $CheckPayments->id)
                ->where('CompanyNum', '=', $CompanyNum)
                ->update(array('TypeDoc' => $TypeDoc));

            $ActPaymetns = '999';

            ?>

            <script>
                $(document).ready(function () {
                    $('#AddDocsClient').submit();
                });
            </script>
            <?php
        }

        $ClientInfo = DB::table('client')->where('id', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->first();
        if ($ClientInfo->parentClientId != 0) {
            $parentClient = DB::table('client')->where('id', '=', $ClientInfo->parentClientId)->where('CompanyNum', '=', $CompanyNum)->first();
            if (!empty($parentClient)) {
                // $ClientId = $parentClient->id;
                $ClientInfo = $parentClient;
            }
        }

        $ClinetId = '000000000';

        if ($ClientInfo->CompanyId != '') {
            $ContactMobile = htmlentities($ClientInfo->ContactMobile);
        } else {
            $ContactMobile = '000-0000000';
        }

        $CreditType = lang('magnet_meshulam');
// הקלדה ידנית    
        if ($_REQUEST['Credit'] == '2') {
            $Tokens = DB::table('token')->where('id', '=', $_REQUEST['CC3'])->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->first();

            $Token = @$Tokens->Token;
            $CardTokef = @$Tokens->Tokef;
            $CardCvv = @$Tokens->sme;

            $CreditType = lang('token_meshulam');

        } else if ($_REQUEST['Credit'] == '3') {
            $ClinetId = $_REQUEST['CCId'];
            $CreditType = lang('phone_meshulam');
        } else if ($_REQUEST['Credit'] == '4') {
            $CDate = $_REQUEST['CDate'];
            $OutSidecode = $_REQUEST['CCode'];
            $CreditType = lang('another_terminal_meshulam');
        }


        if ($_REQUEST['tashType'] == '0') {
            $tashType = '2';
        } else if ($_REQUEST['tashType'] == '1') {
            $tashType = '4';
        } else if ($_REQUEST['tashType'] == '6') {
            $tashType = '1';
        } else {
            $tashType = '2';
        }

        if ($_REQUEST['tashType'] == '0') {
            $tashTypeDB = '1';
        } else if ($_REQUEST['tashType'] == '1') {
            $tashTypeDB = '2';
        } else if ($_REQUEST['tashType'] == '6') {
            $tashTypeDB = '3';
        } else {
            $tashTypeDB = '5';
        }


        $CheckClient = DB::table('client')->where('id', '=', $TempId)->where('CompanyNum', $CompanyNum)->first();


        if ($_REQUEST['Credit'] == '4' && $ActPaymetns == '0') {
            $UserDate = $CDate;
            $Issuer = $_REQUEST['TypeBank'];
            $Bank = $_REQUEST['TypeBank'];
            $Brand = $_REQUEST['TypeBrand'];
            $L4digit = $_REQUEST['CC'];
            $Payments = $_REQUEST['Tash'];
            $tashType = $_REQUEST['tashType'];
            $YaadCode = '0';
            $ACode = $OutSidecode;
            $tashType = $tashType;
            $Payments = $_REQUEST['Tash'];
            $CCode = '0';

            $CardType = array(
                0 => "PL",
                1 => lang('mastercard'),
                2 => lang('visa'),
                3 => "Maestro",
                5 => lang('isracard'),
                66 => lang('diners'),
                77 => lang('american_express'),
                88 => lang('mastercard'),
            );


            if ($Issuer == '1') {
                $BrandName = lang('credit_card_meshulam ') . @$CardType[$Brand];
            } else if ($Issuer == '2') {
                $BrandName = lang('cal_card_meshulam ') . @$CardType[$Brand];
            } else if ($Issuer == '3') {
                $BrandName = lang('diners_card_meshulam');
            } else if ($Issuer == '4') {
                $BrandName = lang('amex_card_meshulam');
            } else if ($Issuer == '5') {
                $BrandName = lang('jcb_card_meshulam ') . @$CardType[$Brand];
            } else if ($Issuer == '6') {
                $BrandName = lang('leumi_card_meshulam ') . @$CardType[$Brand];
            } else {
                $BrandName = '';
            }

            if ($_REQUEST['Credit'] == '4') {
                $UserDate = $CDate;
                $BrandName = lang('another_terminal_process_meshulam');
            }


            DB::table('temp_receipt_payment_client')->insertGetId(
                array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '3', 'Amount' => $_REQUEST['CreditValue'], 'L4digit' => $L4digit, 'YaadCode' => $YaadCode, 'CCode' => $CCode, 'ACode' => $ACode, 'Bank' => $Bank, 'Payments' => $Payments, 'Brand' => $Brand, 'BrandName' => @$BrandName, 'Issuer' => $Issuer, 'tashType' => $tashTypeDB, 'CheckDate' => $UserDate, 'Dates' => $Dates, 'UserId' => $UserId, 'UserDate' => $UserDate, 'CreditType' => $CreditType));

            $CreditStatus = '1';


            ?>

            <script>
                $(document).ready(function () {
                    $('#AddDocsClient').submit();
                });
                BN('0', '<?php echo lang('processing_done_meshulam') ?>');
            </script>
            <?php
        }


        //// בדיקת מסוף לסניף שונה

        if ($CheckClient->Brands != '0') {

            $BrandCheckYaadNumber = DB::table('brands')->where('id', '=', $CheckClient->Brands)->where('CompanyNum', $CompanyNum)->first();

            if (@$BrandCheckYaadNumber->YaadNumber != '0') {
                $MeshulamUserId = $BrandCheckYaadNumber->YaadNumber;
            }

        }

        if ($_REQUEST['Credit'] != '4' && $ActPaymetns == '0') {

            //// חיוב אשראי שמור משולם API    
            if ($LiveMeshulam == '0') {
                $meshulam_url = 'https://dev.meshulam.co.il/api/server/1.0/doPaymentWithToken';
            } else {
                $meshulam_url = 'https://meshulam.co.il/api/server/1.0/doPaymentWithToken';
            }


            $post_data = array(
                'api_key' => $MeshulamAPI,
                'user_id' => $MeshulamUserId,
                'card_token_key' => $Token,
                'full_name' => htmlentities($ClientInfo->CompanyName),
                'phone' => htmlentities($ClientInfo->ContactMobile),
                'sum' => $_REQUEST['CreditValue'],
                //    'description' => 'עסקת חיוב חודשי',
                'type_id' => $tashType,
                'payment_num' => $_REQUEST['Tash'],
            );

            $defaults = array(
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_URL => $meshulam_url,
                CURLOPT_FRESH_CONNECT => 1,
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FORBID_REUSE => 1,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_POSTFIELDS => http_build_query($post_data),
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4
            );

            $ch = curl_init();

            curl_setopt_array($ch, $defaults);
            $json_response = curl_exec($ch);

            if (curl_errno($ch)) {

                $curl_error = curl_error($ch);
                //handle error, save api log with error etc.
                echo "Couldn't send request, error message: " . $curl_error;
            } else {
                // check the HTTP status code of the request
                $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($resultStatus == 200) {
                    // get status and payment url
                    $responseArr = json_decode($json_response, true);
                    $UpdateTransactionDetails = serialize($responseArr);

                    if ((int)$responseArr['status'] == 1) {

                        $CCode = '0';
                        $L4digit = $responseArr['data'][0]['card_suffix'];
                        $YaadCode = $responseArr['data'][0]['id'];
                        $PayToken = $responseArr['data'][0]['token'];

                        $Bank = '9'; /// משולם
                        $Brand = '0';
                        $Issuer = '0';

                        if ($responseArr['data'][0]['card_type'] == 'Local') {
                            $Local = lang('israel_meshulam');
                        } else {
                            $Local = lang('tourist_meshulam');
                        }

                        $BrandName = lang('card_meshulam ') . $responseArr['data'][0]['card_brand'] . ' - ' . $Local;

                        $ACode = $responseArr['data'][0]['asmachta'];
                        $tashType = $responseArr['data'][0]['payment_type'];
                        $Payments = $responseArr['data'][0]['all_payments_num'];


                        $InsertTransaction = DB::table('transaction')->insertGetId(
                            array('CompanyNum' => $CompanyNum, 'ClientId' => $TempId, 'UpdateTransactionDetails' => $UpdateTransactionDetails, 'UserId' => $UserId));

                    } else {

                        $cg_payment_url = $responseArr['err']['message'] ?? '';
                        $CCode = '999';
                        $Err_Message = $responseArr['err']['message'] ?? '';
                        DB::table('transaction_error')->insertGetId(
                            array('CompanyNum' => $CompanyNum, 'ClientId' => $TempId, 'UpdateTransactionDetails' => $UpdateTransactionDetails, 'UserId' => $UserId));
                    }

                } else { //// Err Server

                    $CCode = '902';
                    ?>
                    <script>
                        BN('1', '<?php echo lang('processing_error_meshulam') ?>');
                    </script>
                    <?php
                }
            }

            curl_close($ch);


            if ($CCode == '0' || $CCode == '700' || $CCode == '600') {

                if ($_REQUEST['Credit'] == '4') {
                    $UserDate = $CDate;
                    $BrandName = lang('processing_error_meshulam');
                }


                DB::table('temp_receipt_payment_client')->insertGetId(
                    array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '3', 'Amount' => $_REQUEST['CreditValue'], 'L4digit' => $L4digit, 'YaadCode' => $YaadCode, 'CCode' => $CCode, 'ACode' => $ACode, 'Bank' => $Bank, 'Payments' => $Payments, 'Brand' => $Brand, 'BrandName' => @$BrandName, 'Issuer' => $Issuer, 'tashType' => $tashTypeDB, 'CheckDate' => $UserDate, 'Dates' => $Dates, 'UserId' => $UserId, 'UserDate' => $UserDate, 'CreditType' => $CreditType, 'PayToken' => @$PayToken, 'TransactionId' => @$InsertTransaction));


                $CreditStatus = '1';

                $time = date('Y-m-d H:i:s');


                ?>
                <script>
                    //            document.getElementById('ReceiptBtn').click();
                    $(document).ready(function () {
                        $('#AddDocsClient').submit();
                    });
                    BN('0', '<?php echo lang('processing_done_meshulam') ?>');
                </script>
                <?php
            } else {

                if ($_REQUEST['Credit'] != '4') {
                    $CreditStatus = '2';
                    DB::table('log_yaad')->insertGetId(
                        array('UserId' => $UserId, 'Text' => @$TextResults, 'ClientId' => $TempId, 'CompanyNum' => $CompanyNum, 'Status' => $CCode));

                    $StatusPay = @$StatusreditCard[$CCode];
                    ?>
                    <script>
                        BN('1', '<?php echo $Err_Message; ?>');
                    </script>
                    <?php
                    if ($StatusPay == '') {
                        $StatusPay = lang('unknow_error_meshulam');
                        ?>
                        <script>
                            BN('1', '<?php echo $StatusPay; ?>');
                        </script>
                    <?php }

                }


            }
        }

    } else if ($Act == '4') {

        $BankValue = $_REQUEST['BankValue'];
        $BankDate = $_REQUEST['BankDate'];
        $BankNumber = $_REQUEST['BankNumber'];

        DB::table('temp_receipt_payment_client')->insertGetId(
            array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'TypePayment' => '4', 'Amount' => $BankValue, 'CheckDate' => $BankDate, 'BankNumber' => $BankNumber, 'Dates' => $Dates, 'UserId' => $UserId, 'UserDate' => $UserDate));

    }


}


$TypePayment = array(
    1 => lang('cash'),
    3 => lang('credit_card_single'),
    2 => lang('check'),
    4 => lang('bank_transfer'),
    5 => lang('payment_coupon'),
    6 => lang('return_note'),
    7 => lang('payment_bill'),
    8 => lang('standing_order'),
    9 => lang('other')
);

$TashType = array(
    1 => lang('regular'),
    3 => lang('payments'),
    2 => lang('credit'),
    4 => lang('billing_meshulam'),
    5 => lang('other')
);

?>


<table class="table" dir="rtl">

    <thead>

    <tr>
        <th style="width: 5%; text-align: right;">#</th>
        <th style="width: 25%; text-align: right;"><?php echo lang('payment_method') ?></th>
        <th style="width: 25%; text-align: right;"><?php echo lang('detail') ?></th>
        <th style="width: 20%; text-align: right;"><?php echo lang('reference') ?></th>
        <th style="width: 10%; text-align: right;"><?php echo lang('summary') ?></th>
        <th style="width: 15%; text-align: right;"><?php echo lang('actions') ?></th>
    </tr>

    </thead>

    <tbody>
    <?php

    $i = '1';
    $TempsPayments = DB::table('temp_receipt_payment_client')->where('TempId', '=', $TempId)->where('TypeDoc', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->get();

    foreach ($TempsPayments as $TempsPayment) {

        if ($TempsPayment->TypePayment == '1') {
            $DocPaymentNotes = '';
        } elseif ($TempsPayment->TypePayment == '2') {
            $DocPaymentNotes = lang('check_number') . ' ' . @$TempsPayment->CheckNumber . ' ' . lang('bank_code') . ' ' . @$TempsPayment->CheckBankCode . ' ' . lang('account_number') . ' ' . @$TempsPayment->CheckBank . ' ' . lang('branch_id') . @$TempsPayment->CheckBankSnif;
        } elseif ($TempsPayment->TypePayment == '3') {
            $DocPaymentNotes = @$TempsPayment->BrandName . ' ' . lang('ends_at') . ' ' . @$TempsPayment->L4digit . ' ' . lang('at_meshulam') . ' ' . @$TempsPayment->Payments . ' ' . lang('payments') . ' ' . array_search(@$TempsPayment->tashType, $TashType) . ' ' . lang('confirmation_num_meshulam') . ' ' . @$TempsPayment->ACode;
        } elseif ($TempsPayment->TypePayment == '4') {
            $DocPaymentNotes = lang('ref_number ') . @$TempsPayment->BankNumber;
        } elseif ($TempsPayment->TypePayment == '5') {
            $DocPaymentNotes = '';
        } elseif ($TempsPayment->TypePayment == '6') {
            $DocPaymentNotes = '';
        } elseif ($TempsPayment->TypePayment == '7') {
            $DocPaymentNotes = '';
        } elseif ($TempsPayment->TypePayment == '8') {
            $DocPaymentNotes = '';
        } elseif ($TempsPayment->TypePayment == '9') {
            $DocPaymentNotes = '';
        } else {
            $DocPaymentNotes = lang('without_details');
        }


        ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $TypePayment[$TempsPayment->TypePayment]; ?></td>
            <td><?php echo $DocPaymentNotes; ?></td>
            <td><?php echo with(new DateTime($TempsPayment->CheckDate))->format('d/m/Y'); ?></td>
            <td><?php echo $TempsPayment->Amount + $TempsPayment->Excess; ?> ₪</td>
            <td>
                <button class="btn btn-outline-danger btn-sm CancelPayments" type="button" name="CancelPayments"
                        data-ajax="<?php echo $TempsPayment->id; ?>"><?php echo lang('cancel_meshulam') ?></button>
            </td>
        </tr>

        <?php ++$i;
    } ?>


    </tbody>


</table>

<?php
$GetAmount = DB::table('temp_receipt_payment_client')->where('TempId', '=', $TempId)->where('TypeDoc', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->sum('Amount');
$GetExcess = DB::table('temp_receipt_payment_client')->where('TempId', '=', $TempId)->where('TypeDoc', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->sum('Excess');
$MoreAmount = $TrueFinalinvoicenum - $GetAmount;

$CreditCounts = DB::table('temp_receipt_payment_client')->where('TempId', '=', $TempId)->where('TypeDoc', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->where('TypePayment', '=', '3')->count();

if (@$CreditCounts == '0' || @$CreditCounts == '') {
    $CreditCounts = '0';
}

?>
<div id="info" class="alertb alert-dark" dir="rtl">
    <?php echo lang('total_meshulam') ?> <span style='font-weight:bold; color:black; padding-right:5px;'><span
                id='TotalFinal'><?php echo number_format((float)$TrueFinalinvoicenum, 2, '.', ''); ?></span> ₪</span>
</div>

<div id="info" class="alertb alert-info" dir="rtl">
    <?php echo lang('received_meshulam') ?> <span style='font-weight:bold; color:black; padding-right:5px;'><span
                id='TotalFinalX'><?php echo number_format((float)$GetAmount + $GetExcess, 2, '.', ''); ?></span> ₪</span>
</div>
<?php if ($MoreAmount == '0') {
} else { ?>
    <div id="infoAmountMore" class="alertb alert-warning" dir="rtl">
        <?php echo lang('remainder_of_payment') ?> <span style='font-weight:bold; color:red; padding-right:5px;'
                                                         dir="ltr">₪ <span
                    id='TotalFinalX2'><?php echo number_format((float)$MoreAmount, 2, '.', ''); ?></span></span>
    </div>
<?php } ?>

<?php if (@$GetExcess == '0' || @$GetExcess < '0') {
} else { ?>
    <div id="infoAmountMore" class="alertb alert-info" dir="rtl">
        <?php echo lang('surplus_meshulam') ?> <span style='font-weight:bold; color:red; padding-right:5px;' dir="ltr">₪ <span
                    id='TotalFinalX2'>-<?php echo number_format((float)@$GetExcess, 2, '.', ''); ?></span></span>
    </div>
<?php } ?>

<script>


    $('#Finalinvoicenum').val('<?php echo number_format((float)$MoreAmount, 2, '.', ''); ?>');
    $('#CancelDocs_Finalinvoicenum').val('<?php echo number_format((float)$TrueFinalinvoicenum, 2, '.', ''); ?>');

    <?php
    if (@$CheckRefresh == '2') {
    ?>
    $('#Creditdiv input').val('');
    $('#Creditdiv select').prop('selectedIndex', 0);
    $('#Cahshdiv input').val('');
    $('#Checkdiv input').val('');
    $('#Bankdiv input').val('');

    <?php } ?>

    $('#CreditValueButton').attr("disabled", true);
    $('.CloseCheckBoxPayment').attr("disabled", true);

    <?php if ($GetAmount + $GetExcess > '0'){ ?>
    $('#CancelDocButton').attr("disabled", false);
    <?php } else if ($GetAmount + $GetExcess == '0' || $GetAmount + $GetExcess == '0.00') { ?>
    $('.CloseCheckBoxPayment').attr("disabled", false);
    <?php } ?>

    <?php if ($Act == '1'){ ?>

    $('#Creditdiv input').val('');
    $('#Creditdiv select').prop('selectedIndex', 0);
    $('#Cahshdiv input').val('');
    $('#Checkdiv input').val('');
    $('#Bankdiv input').val('');

    <?php } else if ($Act == '2'){
    $CheckDate = date("Y-m-d", strtotime("+1 month", strtotime($CheckDate)));
    $CheckNumber = $CheckNumber + 1;
    ?>

    $('#CheckDate').val('<?php echo $CheckDate; ?>');
    $('#CheckNumber').val('<?php echo $CheckNumber; ?>');
    $('#CheckValue').val('');

    $('#Creditdiv input').val('');
    $('#Creditdiv select').prop('selectedIndex', 0);
    $('#Cahshdiv input').val('');
    $('#Bankdiv input').val('');


    <?php } else if ($Act == '3'){ ?>

    <?php if (isset($CreditStatus) && $CreditStatus == '2'){
    } else { ?>
    $('#Creditdiv input').val('');
    $('#Creditdiv select').prop('selectedIndex', 0);
    $('#Creditdiv #CreditOptionToken').prop('selectedIndex', 0).trigger('change');
    <?php } ?>
    $('#Cahshdiv input').val('');
    $('#Checkdiv input').val('');
    $('#Bankdiv input').val('');

    <?php } else if ($Act == '4'){ ?>

    $('#Creditdiv input').val('');
    $('#Creditdiv select').prop('selectedIndex', 0);
    $('#Cahshdiv input').val('');
    $('#Checkdiv input').val('');
    $('#Bankdiv input').val('');

    <?php } ?>


    $(function () {
        var time = function () {
            return '?' + new Date().getTime()
        };
        $('#CancelPaymentsPopup').imgPicker({});


    });


    $('.CancelPayments').click(function () {
        $("#meTest").trigger("click");
        var CancelPayments_TempsListsId = $(this).data("ajax");
        $('#CancelPayments_TempsListsId').val(CancelPayments_TempsListsId);
    });


    <?php if ($GetAmount == '0' || $GetAmount == '0.00' || $GetAmount == '' || $CreditCounts >= '1'){ ?>
    $('#MakePaymentInput').val('2').trigger("change");
    <?php } else {  ?>
    $('#MakePaymentInput').val('1').trigger("change");
    <?php } ?>


</script>



