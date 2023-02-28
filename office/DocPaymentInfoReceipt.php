<?php

require_once __DIR__ . '/../app/initcron.php';
require_once __DIR__ . '/services/PaymentService.php';
require_once __DIR__ . '/services/payment/PaymentStatusList.php';

$data = $_REQUEST;

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$studioSettings = Settings::getSettings($CompanyNum);

$TempId  = null;
$TypeDoc = null;

$CheckRefresh = $data['CheckRefresh'] ?? null;

try {
    $paymentResult = PaymentService::chargeClient($data, false, true);
    $TrueFinalinvoicenum = $paymentResult['TrueFinalinvoicenum'];
    $Finalinvoicenum = $paymentResult['Finalinvoicenum'];
    $TempId = $paymentResult['TempId'];
    $TypeDoc = $paymentResult['TypeDoc'];

    $Act = array_get($data, 'Act');
    $CheckDate = array_get($data, 'CheckDate');
    $CheckNumber = array_get($data, 'CheckNumber');
    $CreditStatus = array_get($paymentResult, 'CreditStatus');
    $CCode = array_get($paymentResult, 'CCode');
    $ErrorMessage = array_get($paymentResult, 'ErrorMessage');
} catch (\Throwable $e) {
    LoggerService::error($e);
    throw $e;
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

<?php if ($CheckRefresh === null) { ?>
    <?php if ($paymentResult['status'] !== 'success') { ?>
        <script>
            Swal.fire({
                text: '<?= PaymentStatusList::getErrorMessage($CCode) ?: $ErrorMessage ?: lang('processing_error_meshulam') ?>',
                icon: 'error'
            });
        </script>
    <?php } else { ?>
        <script>
            $(document).ready(function () {
                let act = '<?= $Act ?>';
                if(act == 3) {
                    $('#AddDocs').submit();
                }
            });
            Swal.fire({
                text: lang('processing_done_meshulam'),
                icon: 'success'
            });
        </script>
    <?php } ?>
<?php } ?>


<table class="table" dir="rtl">

    <thead>

    <tr>
        <th style="width: 5%; text-align: right;">#</th>
        <th style="width: 10%; text-align: right;"><?php echo lang('payment_method') ?></th>
        <th style="width: 50%; text-align: right;"><?php echo lang('detail') ?></th>
        <th style="width: 15%; text-align: right;"><?php echo lang('payment_date') ?></th>
        <th style="width: 10%; text-align: right;"><?php echo lang('total') ?></th>
        <th style="width: 10%; text-align: right;"><?php echo lang('actions') ?></th>
    </tr>

    </thead>

    <tbody>
    <?php

    $i = '1';
    $TempsPayments = DB::table('temp_receipt_payment')->where('TempId', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->get();

    foreach ($TempsPayments as $TempsPayment) {

        if ($TempsPayment->TypePayment == '1') {
            $DocPaymentNotes = '';
        } elseif ($TempsPayment->TypePayment == '2') {
            $DocPaymentNotes = lang('check_number') . ' ' . @$TempsPayment->CheckNumber . ' ' . lang('bank_code') . ' ' . @$TempsPayment->CheckBankCode . ' ' . lang('account_number') . ' ' . @$TempsPayment->CheckBank . ' ' . lang('branch_id') . ' ' . @$TempsPayment->CheckBankSnif;
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
                        data-tempid="<?php echo $TempsPayment->TempId; ?>"
                        data-templistid="<?php echo $TempsPayment->id; ?>"><?php echo lang('cancel_meshulam') ?></button>
            </td>
        </tr>

        <?php ++$i;
    } ?>


    </tbody>


</table>

<?php
$GetAmount = DB::table('temp_receipt_payment')->where('TempId', '=', $TempId)->where('TypeDoc', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->sum('Amount');
$GetExcess = DB::table('temp_receipt_payment')->where('TempId', '=', $TempId)->where('TypeDoc', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->sum('Excess');
$MoreAmount = $Finalinvoicenum - $GetAmount;
$TrueMoreAmount = $MoreAmount;

$CreditCounts = DB::table('temp_receipt_payment')->where('TempId', '=', $TempId)->where('CompanyNum', '=', $CompanyNum)->where('TypePayment', '=', '3')->count();

if (@$CreditCounts == '0' || @$CreditCounts == '') {
    $CreditCounts = '0';
}


?>
<div id="info" class="alertb alert-dark" dir="rtl">
    <?php echo lang('total_meshulam') ?> <span style='font-weight:bold; color:black; padding-right:5px;'><span
                id='TotalFinal'><?php echo number_format((float)$Finalinvoicenum, 2, '.', ''); ?></span> ₪</span>
</div>

<div id="info" class="alertb alert-dark" dir="rtl">
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

<?php if (@$GetExcess == '0' || @$GetExcess == '') {
} else { ?>
    <div id="infoAmountMore" class="alertb alert-info" dir="rtl">
        <?php echo lang('surplus_meshulam') ?> <span style='font-weight:bold; color:red; padding-right:5px;' dir="ltr">₪ <span
                    id='TotalFinalX2'>-<?php echo number_format((float)@$GetExcess, 2, '.', ''); ?></span></span>
    </div>
<?php } ?>

<script>


    $('#TrueFinalinvoicenum').val('<?php echo number_format((float)$TrueMoreAmount, 2, '.', ''); ?>');

    $('#CashValue').val('');
    $('#CheckValue').val('');
    $('#BankValue').val('');
    $('#BankDate').val('');
    $('#BankNumber').val('');
    $('#CreditValueButton').attr("disabled", true);

    <?php if ($Act == '1'){ ?>

    $('#CheckDate').val('');
    $('#CheckNumber').val('');
    $('#CheckSnif').val('');
    $('#CheckAccount').val('');
    $('#CheckBank').val('');

    <?php } else if ($Act == '2'){
    $CheckDate = date("Y-m-d", strtotime("+1 month", strtotime($CheckDate)));
    $CheckNumber = $CheckNumber + 1;
    ?>

    $('#CheckDate').val('<?php echo $CheckDate; ?>');
    $('#CheckNumber').val('<?php echo $CheckNumber; ?>');

    <?php } ?>

    $('.CancelPayments').click(function () {
        $("#meTest").trigger("click");
        var CancelPayments_TempsListsId = $(this).data("tempid");
        var CancelPayments_TempsListsId_new = $(this).data("templistid");
        $('#CancelPayments_TempsId').val(CancelPayments_TempsListsId);
        $('#CancelPayments_TempsListsId').val(CancelPayments_TempsListsId_new);
        $('#CancelPayments_Finalinvoicenum').val('<?php echo $Finalinvoicenum; ?>');
        $('#CancelPayments_TrueFinalinvoicenum').val('<?php echo $TrueMoreAmount; ?>');


    });


    <?php if ($GetAmount + $GetExcess > '0'){ ?>
    $('#CancelDocButton').attr("disabled", false);
    <?php if ($TypeDoc == '400'){ ?>
    //$('.CloseCheckBoxPayment').attr("disabled", true);
    <?php } ?>
    $('.CloseEditItems').hide();
    $('.OpenEditItems').show();
    $('#MakePaymentInput').val('1');
    $('#CancelDocs_TempsId').val('<?php echo $TempId; ?>');
    <?php } else if ($GetAmount + $GetExcess == '0' || $GetAmount + $GetExcess == '0.00') { ?>
    <?php if ($TypeDoc == '400'){ ?>
    //$('.CloseCheckBoxPayment').attr("disabled", false);
    <?php } ?>
    $('.CloseEditItems').show();
    $('.OpenEditItems').hide();
    $('#MakePaymentInput').val('2');
    <?php } ?>


    <?php if ($TypeDoc == '320' && $GetAmount == $Finalinvoicenum){ ?>
    $('#ReceiptBtn').prop('disabled', false);
    <?php } ?>

    $(function () {
        var time = function () {
            return '?' + new Date().getTime()
        };
        $('#CancelPaymentsPopup').imgPicker({});


    });


</script>
