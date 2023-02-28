<?php
require_once '../../app/initcron.php';
require_once '../Classes/Company.php';
require_once '../Classes/Token.php';
require_once '../Classes/MeshulamPayments.php';

$company = Company::getInstance(false);
$mpId = $_REQUEST['TokenId'];
$pay = MeshulamPayments::getPayment($mpId);

$TokenInfo = Token::getById($pay->token_id);

$pp = new PaymentPage();
$payPage = $pp->getRow($pay->payment_page);

if ($pay->status=='1'){
    $TokensStatus = '<span class="text-success">פעיל</span>';
}
else if ($pay->status=='0') {
    $TokensStatus = '<span class="text-danger">בוטל</span>';
}
else{
    $TokensStatus = '<span class="text-danger">הושלם</span>';
}

$transactions = json_decode($pay->transactions);
$failedTransactions = json_decode($pay->failed_transactions);
?>


<div class="row">
    <div class="col-md-3">פריט: <?php echo $payPage->Title; ?></div>
    <div class="col-md-3">סוג ה.קבע: <?php echo $pay->total_payments == -1 ? 'מתחדש' : 'מוגבל בחזרות'; ?></div>
    <div class="col-md-3">הוגדר: <?php echo !isset($UsersName) ? 'אוטומטי' : $UsersName->display_name ?></div>
    <div class="col-md-3">סטטוס: <?php echo $TokensStatus; ?></div>

</div>


<div class="row">

    <div class="col-md-3"></div>
    <div class="col-md-3"><a href="javascript:void(0);" class="SuccessKevaNew" data-kevaid="<?php echo $mpId; ?>">חיובים מוצלחים (<?php echo Count($transactions); ?>)</a></div>
    <div class="col-md-3"><a href="javascript:void(0);" class="FailsKevaNew text-danger" data-kevaid="<?php echo $mpId; ?>">חיובים נכשלים (<?php echo Count($failedTransactions); ?>)</a></div>
<!--    <div class="col-md-3"><a href="javascript:void(0);" class="FailsTotalKeva text-warning" data-kevaid="--><?php //echo $mpId; ?><!--">חוב אבוד (--><?php //echo @$CountFailsTotalKeva; ?><!--)</a></div>-->

</div>

<hr>

<div class="col-md-12 DivScroll" style='min-height:320px; max-height:420px; overflow-y:scroll; overflow-x:hidden;'>
    <table class="table table-hover text-right" style="font-size:12px; font-weight:bold;" dir="rtl" id="Token">
        <thead >
        <tr style="background-color:#bce8f1;">
            <th align="right" style="text-align:right;" width="10%">מס' חיוב</th>
            <th align="right" style="text-align:right;">פריט</th>
            <th align="right" style="text-align:right;">סכום החיוב</th>
            <th align="right" style="text-align:right;">חיוב הבא</th>
            <th align="right" style="text-align:right;">טוקן</th>
            <th align="right" style="text-align:right;">פעולות</th>
        </tr>
        </thead>
        <tbody>

        <?php

            if ($pay->status=='0' || $pay->status=='2'){
                $ColorClass = 'class="text-secondary"';
                $TrueBalanceValueColor = 'text-secondary';
                $TrueDateColor = 'text-secondary';
                $ColorTextClass = 'class="text-secondary"';
                $LineThrough = 'style="text-decoration: line-through;"';
            }
            else{
                $ColorClass = '';
                $TrueBalanceValueColor = '';
                $TrueDateColor = '';
                $ColorTextClass = '';
                $LineThrough = '';
            }
            ?>

            <tr <?php echo $ColorClass; ?>>
                <td <?php echo $LineThrough; ?> ><?php echo $pay->last_payment_num; ?></td>
                <td <?php echo $LineThrough; ?>><?php echo $payPage->Title; ?></td>
                <td <?php echo $LineThrough; ?>>₪<?php echo $pay->payment_sum; ?></td>
                <td <?php echo $LineThrough; ?>><?php echo date("Y-m-d",strtotime("+1 month",strtotime($pay->last_payment_date))); ?></td>
                <td <?php echo $LineThrough; ?>><?php echo $TokenInfo->L4digit.'****'; ?></td>
                <td><a href="javascript:void(0);" class="EditKevaNew" data-kevaid="<?php echo $pay->id; ?>" >נהל חיוב</a></td>
            </tr>

        </tbody>

    </table>
</div>

<style>
    .DivScroll::-webkit-scrollbar {
        width: 5px;
        padding-left: 0px;
        margin-left: 0px;
    }

    .DivScroll::-webkit-scrollbar-thumb {
        background-color: darkgrey;
        outline: 1px solid slategray;
        padding-left: 0px;
        margin-left: 0px;
    }
</style>

<script>
    $(".EditKevaNew").click(function(){

        var TokenId = $(this).data("kevaid");
        document.getElementById("resultPayToken").innerHTML=" ";
        $('#resultPayToken').load('/office/action/editPayment.php?TokenId='+TokenId);
        $('#ShowSaveKeva').show();

    });
</script>
