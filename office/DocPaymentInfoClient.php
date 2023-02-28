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
    $paymentResult = PaymentService::chargeClient($data);
    $TrueFinalinvoicenum = $paymentResult['TrueFinalinvoicenum'];
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
    1 => "מזומן",
    3 => "כרטיס אשראי",
    2 => "המחאה",
    4 => "העברה בנקאית",
    5 => "תו",
    6 => "פתק החלפה",
    7 => "שטר",
    8 => "הוראת קבע",
    9 => "אחר"
);

$TashType = array(
    1 => "רגיל",
    3 => "תשלומים",
    2 => "קרדיט",
    4 => "חיוב נדחה",
    5 => "אחר"
);

?>

<script>
    window.console.log(JSON.parse('<?php echo json_encode($paymentResult); ?>'));
</script>

<?php if ($paymentResult['status'] !== 'success') { ?>
        <?php if ($Act == 2) {
            $errorMessage = lang('check_number_exists');
        } else {
            $errorMessage = PaymentStatusList::getErrorMessage($CCode) ?: $ErrorMessage ?: lang('processing_error_meshulam');
        } ?>
    <script>
        Swal.fire({
            text: '<?= $errorMessage ?>',
            icon: 'error'
        });
    </script>
<?php } else { ?>
    <script>
        $(document).ready(function () {
            let act = '<?= $Act ?>';
            if(act == 3) {
                $('#AddDocsClient').submit();
            }
        });
        Swal.fire({
            text: lang('processing_done_meshulam'),
            icon: 'success'
        });
    </script>
<?php } ?>

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
            $DocPaymentNotes = lang('ref_number') . @$TempsPayment->BankNumber;
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
    if ($CheckRefresh == '2') {
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

    <?php } elseif ($Act == '2'){
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


    <?php } elseif ($Act == '3'){ ?>

    <?php if ($CreditStatus == '2'){
    } else { ?>
    $('#Creditdiv input').val('');
    $('#Creditdiv select').prop('selectedIndex', 0);
    $('#Creditdiv #CreditOptionToken').prop('selectedIndex', 0).trigger('change');
    <?php } ?>
    $('#Cahshdiv input').val('');
    $('#Checkdiv input').val('');
    $('#Bankdiv input').val('');

    <?php } elseif ($Act == '4'){ ?>

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


