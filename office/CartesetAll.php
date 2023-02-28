<?php
ini_set("max_execution_time", 0);

require_once '../app/init.php';
require_once 'Classes/Client.php';
require_once __DIR__.'/Classes/247SoftNew/UpdateBusinessNumber.php';

if (Auth::guest()) redirect_to(App::url());

$pageTitle = lang('carteret_page_title');
require_once '../app/views/headernew.php';
?>

<?php if (Auth::check()): ?>
    <?php if (Auth::userCan('20')): ?>

    <?php
    $CompanyNum = Auth::user()->CompanyNum;
    $BusinessSettings = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();



    if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
    if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");

    if (@$_REQUEST['Dates'] == '') {

        $cMonth = $_REQUEST["month"];
        $cYear = $_REQUEST["year"];
        $Dates = $_REQUEST["year"] . '-' . $_REQUEST["month"];
    } else {

        $Dates = $_REQUEST['Dates'];
        $cMonth = with(new DateTime($_REQUEST['Dates']))->format('m');
        $cYear = with(new DateTime($_REQUEST['Dates']))->format('Y');

    }

    if (!isset($_REQUEST["BrandId"])) $_REQUEST["BrandId"] = '';
    if ($_REQUEST["BrandId"] != '') {
        $BrandId = $_REQUEST["BrandId"];
    } else {
        $BrandId = '';
    }


    $prev_year = $cYear;
    $next_year = $cYear;
    $prev_month = $cMonth - 1;
    $next_month = $cMonth + 1;

    if ($prev_month == 0) {
        $prev_month = 12;
        $prev_year = $cYear - 1;
    }
    if ($next_month == 13) {
        $next_month = 1;
        $next_year = $cYear + 1;
    }

    $StartDate = $cYear . '-' . $cMonth . '-01';
    $EndDate = $cYear . '-' . $cMonth . '-31';

    $TypePayment = array(
        "1" => lang('cash'),
        "2" => lang('check'),
        "3" => lang('credit_card_single'),
        "4" => lang('bank_transfer'),
        "5" => lang('payment_coupon'),
        "6" => lang('return_note'),
        "7" => lang('payment_bill'),
        "8" => lang('standing_order'),
        "9" => lang('other'),
    );

    $TashType = array(
        "1" => lang('regular_payment'),
        "2" => lang('payments'),
        "3" => lang('credit_payments_carteset'),
        "4" => lang('deferred_debit_carteset'),
        "5" => lang('other_way_carteset')
    );

    $TypeBanks = array(
        "1" => lang('isracard'),
        "2" => lang('visa_cal'),
        "3" => lang('diners'),
        "4" => lang('american_express'),
        "6" => lang('leumi_card'),
    );

    $updateBusinessObj = UpdateBusinessNumber::where('company_num', $CompanyNum)->first();

    ?>


<link href="assets/css/fixstyle.css" rel="stylesheet">
    <script type="text/javascript" charset="utf-8">

        function myFunction(value) {

            <?php if ($BrandId != ''){ ?>
            var BrandId = <?= $BrandId ?>;
            window.location.href = 'CartesetAll.php?Dates=' + value + '&BrandId=' + BrandId;
            <?php } else { ?>
            window.location.href = 'CartesetAll.php?Dates=' + value;
            <?php } ?>
        }

    </script>

    <div class="row mx-0 px-0 ">
        <div class="col-12 mx-0 px-0">
            <div class="row">

                <?php include("ReportsInc/SideMenu.php"); ?>

                <div class="col-md-10 col-sm-12 ">
                    <div class="tab-content">


                        <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                            <div class="card spacebottom">
                                <div class="card-header text-start"><i
                                            class="fas fa-chart-pie fa-fw"></i><strong><?php echo lang('card_index_month') ?>
                                        <span class="text-primary"><?php echo $monthNames[$cMonth - 1] . ' ' . $cYear; ?></span></strong>
                                </div>
                                <div class="card-body">


                                    <div class="row">
                                        <div class="col-md-6 col-sm-12   d-flex justify-content-start">
                                            <span class="mie-6 mb-6"><input type="month" class="form-control" id="CDate"
                                                                            value="<?php echo $Dates; ?>"
                                                                            onChange="myFunction(this.value);"></span>
                                            <span class="mb-6"> <a href="javascript:void(0);"
                                                                   onclick="TINY.box.show({iframe:'PDF/CartesetAll.php?month=<?= $cMonth ?>&year=<?= $cYear; ?><?= !empty($BrandId) ? '&brandid='.$BrandId : '' ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})"
                                                                   class="btn btn-primary"><?php echo lang('action_print') ?></a></span>
                                        </div>
                                        <div class="col-md-6 col-sm-12  d-flex justify-content-end spacebottom flex-wrap ">
                                            <span class="mis-6 mb-6"> <a
                                                        href="<?php echo $_SERVER["PHP_SELF"] . "?month=" . sprintf('%02d', $prev_month) . "&year=" . $prev_year; ?>"
                                                        class="btn btn-light"><?php echo lang('to_prev_month') ?></a></span>
                                            <span class="mis-6 mb-6"> <a
                                                        href="<?php echo $_SERVER["PHP_SELF"] . "?month=" . sprintf('%02d', $next_month) . "&year=" . $next_year; ?>"
                                                        class="btn btn-light"><?php echo lang('to_next_month') ?></a></span>
                                            <span class="mis-6"> <a href="CartesetAll.php"
                                                                    class="btn btn-primary"><?php echo lang('this_month') ?></a></span>
                                        </div>
                                    </div>

                                    <hr>
                                    <?php
                                    $DocsBrands = DB::table('brands')->where('Status', '=', '0')->where('FinalCompanynum', '=', Auth::user()->CompanyNum)->get();
                                    $BrandsCounts = count($DocsBrands);
                                    if ($BrandsCounts >= '1'){
                                    ?>
                                    <div class="d-flex justify-content-start flex-wrap">
<span class="mie-6 mb-6">
<select name="BrandId" id="BrandId" class="form-control">
<option value=""><?php echo lang('all_branch') ?></option> 
<?php
$DocsBrands = DB::table('brands')->where('Status', '=', '0')->where('FinalCompanynum', '=', Auth::user()->CompanyNum)->get();
foreach ($DocsBrands as $DocsBrand) {
    ?>
    <option value="<?php echo $DocsBrand->id; ?>" <?php if ($BrandId == $DocsBrand->id) {
        echo 'selected';
    } ?> > <?php echo $DocsBrand->BrandName; ?> </option>
<?php } ?>  
</select>        
</span>
                                        <?php } ?>

                                        <?php
                                        $DocsTables = DB::table('docstable')->where('Status', '=', '0')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('Accounts', '=', '1')->get();
                                        $typeTitleArr = array(
                                            "0" => lang('settings_bids'),
                                            "200" => lang('Shipping_documents'),
                                            "100" => lang('docs_orders'),
                                            "305" => lang('tax_invoice'),
                                            "320" => lang('Tax_invoices_receipts'),
                                            "310" => lang('concentration_invoices'),
                                            "330" => lang('credit_tax_invoices'),
                                            "400" => lang('receipts'),
                                            "300" => lang('transaction_invoices'),
                                            "210" => lang('return_certificates'),
                                            "1" => lang('manual_tax_invoices'),
                                            "2" => lang('refund_receipt')
                                        );
                                        foreach ($DocsTables as $DocsTable) {
                                            echo '<a class="btn btn-dark mie-6 mb-6" href="#T' . $DocsTable->id . '">' . $typeTitleArr[$DocsTable->TypeHeader] . '</a> ';
                                        }
                                        ?>
                                    </div>
                                    <hr>
                                    <div class="row"
                                         style="padding-left:15px; padding-right:15px; overflow-x: overlay;">
                                        <table class="table table-hover dt-responsive text-start display wrap"
                                               id="categories" cellspacing="0" width="100%" style="font-size:14px;">
                                            <tbody>
                                            <?php
                                            //לופ לסוגי המסמכים
                                            foreach ($DocsTables

                                            as  $DocsTable) {

                                            if ($BrandId == '') {
                                                $DocGetsCountThisDates = DB::table('docs')->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->count();
                                            } else {
                                                $DocGetsCountThisDates = DB::table('docs')->where('Brands', '=', $BrandId)->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->count();
                                            }

                                            if ($DocGetsCountThisDates != '0') {
                                            ?>

                                            <thead id="T<?php echo $DocsTable->id; ?>">
                                            <tr>
                                                <td colspan="<?= $updateBusinessObj ? 13 : 12 ?>" class="bg-info text-white">
                                                    <strong><?php echo $typeTitleArr[$DocsTable->TypeHeader]; ?></strong>
                                                </td>
                                            </tr>
                                            <tr class="bg-dark text-white" style="font-size: 12px;">
                                                <th><?php echo lang('carteset_doc_num') ?></th>
                                                <th><?php echo lang('reports_card_name') ?></th>
                                                <th><?php echo lang('id') ?></th>
                                                <?php if($updateBusinessObj) { ?>
                                                <th><?= lang('license_number') ?></th>
                                                <?php } ?>
                                                <th><?php echo lang('branch') ?></th>
                                                <th><?php echo lang('table_value_date') ?></th>
                                                <th><?php echo lang('reports_document_date') ?></th>
                                                <th><?php echo lang('reports_tax_deduction') ?>
                                                    (<?php echo $BusinessSettings->NikuyMsBamakor; ?>%)
                                                </th>
                                                <th><?php echo lang('total_before_vat') ?></th>
                                                <th><?php echo lang('vat') ?></th>
                                                <th><?php echo lang('reports_total_vat') ?></th>
                                                <th><?php echo lang('reports_total_vat_included') ?></th>
                                                <th><?php echo lang('reports_total_vat_exemption') ?></th>
                                            </tr>

                                            </thead>

                                            <?php


                                            if ($BrandId == '') {

                                                $DocGets = DB::table('docs')->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
                                                $DocSum1 = DB::table('docs')->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('NikoyMasAmount');
                                                $DocSum3 = DB::table('docs')->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('VatAmount');
                                                $DocSum4 = DB::table('docs')->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');
                                                $DocSum5 = DB::table('docs')->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');
                                                $DocSum2 = $DocSum5 - $DocSum3;


                                                $DocSum1Total = DB::table('docs')->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('NikoyMasAmount');
                                                $DocSum3Total = DB::table('docs')->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('VatAmount');
                                                $DocSum4Total = DB::table('docs')->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('Amount');
                                                $DocSum5Total = DB::table('docs')->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('Amount');
                                                $DocSum2Total = $DocSum5Total - $DocSum3Total;


                                            } else {

                                                $DocGets = DB::table('docs')->where('Brands', '=', $BrandId)->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
                                                $DocSum1 = DB::table('docs')->where('Brands', '=', $BrandId)->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('NikoyMasAmount');
                                                $DocSum3 = DB::table('docs')->where('Brands', '=', $BrandId)->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('VatAmount');
                                                $DocSum4 = DB::table('docs')->where('Brands', '=', $BrandId)->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('Amount');
                                                $DocSum5 = DB::table('docs')->where('Brands', '=', $BrandId)->where('TypeDoc', '=', $DocsTable->id)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('Amount');
                                                $DocSum2 = $DocSum5 - $DocSum3;


                                                $DocSum1Total = DB::table('docs')->where('Brands', '=', $BrandId)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('NikoyMasAmount');
                                                $DocSum3Total = DB::table('docs')->where('Brands', '=', $BrandId)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('VatAmount');
                                                $DocSum4Total = DB::table('docs')->where('Brands', '=', $BrandId)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('Amount');
                                                $DocSum5Total = DB::table('docs')->where('Brands', '=', $BrandId)->where('CompanyNum', '=', Auth::user()->CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('Amount');
                                                $DocSum2Total = $DocSum5Total - $DocSum3Total;


                                            }


                                            foreach ($DocGets as $DocGet) {

                                                $Brands = DB::table('brands')->where('id', '=', $DocGet->Brands)->where('Status', '=', '0')->where('FinalCompanynum', '=', Auth::user()->CompanyNum)->first();

                                                if (@$Brands->id != '') {
                                                    $BrandsName = $Brands->BrandName;
                                                } else {
                                                    $BrandsName = lang('primary_branch');
                                                }

                                                $clientInfo = new Client($DocGet->ClientId);
                                                $getBusinessUpdate  = UpdateBusinessNumber::where('company_num', $CompanyNum)
                                                    ->where('until_date', '>', $DocGet->UserDate)
                                                    ->orderBy('until_date', 'ASC')
                                                    ->first();
                                                ?>
                                                <tr>
                                                    <td><a href="javascript:void(0);"
                                                           onclick="TINY.box.show({iframe:'PDF/Docs.php?DocType=<?php echo $DocsTable->id; ?>&DocId=<?php echo $DocGet->TypeNumber; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})"><span
                                                                    class="text-primary"><?php echo $DocGet->TypeNumber; ?></span></a>
                                                    </td>
                                                    <td>
                                                        <a href="ClientProfile.php?u=<?php echo $DocGet->ClientId; ?>"><span
                                                                    class="text-dark"><?php echo $DocGet->Company; ?></span></a>
                                                    </td>
                                                    <td><?= $clientInfo->CompanyId ?? '--' ?></td>
                                                    <?php if($updateBusinessObj) { ?>
                                                    <td><?= $getBusinessUpdate->business_number ?? $BusinessSettings->CompanyId ?></td>
                                                    <?php } ?>
                                                    <td><?php echo $BrandsName; ?></td>
                                                    <td><?php echo with(new DateTime($DocGet->Dates))->format('d/m/Y'); ?></td>
                                                    <td><?php echo with(new DateTime($DocGet->UserDate))->format('d/m/Y'); ?></td>
                                                    <td>
                                                        <span><?php echo @number_format(@$DocGet->NikoyMasAmount, 2); ?></span>
                                                        ₪
                                                    </td>
                                                    <?php if ($DocGet->Minus == '0') { ?>
                                                        <td>
                                                            <span><?php echo @number_format(@$DocGet->Amount - @$DocGet->VatAmount, 2); ?></span>
                                                            ₪
                                                        </td>
                                                    <?php } else { ?>
                                                        <td>
                                                            <span><?php echo @number_format(@$DocGet->Amount + @$DocGet->VatAmount, 2); ?></span>
                                                            ₪
                                                        </td>
                                                    <?php } ?>
                                                    <td><?php echo @number_format(@$DocGet->Vat, 2); ?>%</td>
                                                    <td>
                                                        <span><?php echo @number_format(@$DocGet->VatAmount, 2); ?></span>
                                                        ₪
                                                    </td>
                                                    <td><span><?php if (@$DocGet->Vat != '0') {
                                                                echo @number_format(@$DocGet->Amount, 2);
                                                            } else {
                                                                echo '0.00';
                                                            } ?></span> ₪
                                                    </td>
                                                    <td><span><?php if (@$DocGet->Vat == '0') {
                                                                echo @number_format(@$DocGet->Amount, 2);
                                                            } else {
                                                                echo '0.00';
                                                            } ?></span> ₪
                                                    </td>
                                                </tr>
                                                <?php
                                                $DocPayments = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('DocsId', '=', $DocGet->id)->get();
                                                if (($DocsTable->DocsPayment == '1') && (!empty($DocPayments))) {
                                                    ?>
                                                    <tr class="bg-light">
                                                        <td></td>
                                                        <td colspan="10">
                                                            <table class="" width="100%"
                                                                   style="font-size: 14px; text-align:center; border-collapse: collapse;">
                                                                <thead>
                                                                <tr style="border-bottom:1px solid #B9B9B9;">
                                                                    <td style="padding:5px;"><strong>#</strong></td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('receipt_type') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('reports_cc_type') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('four_numbers') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('payments_num_short') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('payment_method') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('reports_company_pay_off') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('a_bank') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('branch') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('a_account') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('check') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('payment_date_short') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('reference') ?></strong>
                                                                    </td>
                                                                    <td style="padding:5px;">
                                                                        <strong><?php echo lang('summary') ?></strong>
                                                                    </td>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                $i = 1;
                                                                foreach ($DocPayments as $DocPayment) {

                                                                    $PaymentNum = DB::table('docs_payment')->where('DocsId', '=', $DocGet->id)->where('CompanyNum', $CompanyNum)->where('L4digit', $DocPayment->L4digit)->where('TypePayment', '3')->count();

                                                                    ?>
                                                                    <tr style="border-bottom:1px solid #B9B9B9;">
                                                                        <td style="padding:5px;"><?php echo $i; ?></td>
                                                                        <td style="padding:5px;"><?php echo($TypePayment[$DocPayment->TypePayment] ?? ''); ?></td>
                                                                        <td style="padding:5px;"><?php echo $DocPayment->BrandName; ?></td>
                                                                        <td style="padding:5px;"><?php if ($DocPayment->L4digit != '0') echo $DocPayment->L4digit; ?></td>
                                                                        <td style="padding:5px;"><?php if ($DocPayment->Payments != '0') {
                                                                                echo $DocPayment->Payments;
                                                                                echo lang('of_user_manage');
                                                                                echo $PaymentNum;
                                                                            } ?></td>
                                                                        <td style="padding:5px;"><?php echo($TashType[$DocPayment->tashType] ?? ''); ?></td>
                                                                        <td style="padding:5px;"><?php echo($TypeBanks[$DocPayment->Bank] ?? ''); ?></td>
                                                                        <td style="padding:5px;"><?php echo $DocPayment->CheckBankCode; ?></td>
                                                                        <td style="padding:5px;"><?php if ($DocPayment->CheckBankSnif != '0') echo $DocPayment->CheckBankSnif; ?></td>
                                                                        <td style="padding:5px;"><?php echo $DocPayment->CheckBank; ?></td>
                                                                        <td style="padding:5px;"><?php if ($DocPayment->CheckNumber != '0') echo $DocPayment->CheckNumber; ?></td>
                                                                        <td style="padding:5px;"><?php echo with(new DateTime($DocPayment->CheckDate))->format('d/m/Y'); ?></td>
                                                                        <td style="padding:5px;"><?php if ($DocPayment->ACode != '0') echo $DocPayment->ACode; ?></td>
                                                                        <td style="padding:5px;">
                                                                            ₪<?php echo number_format($DocPayment->Amount, 2); ?></td>
                                                                    </tr>
                                                                    <?php
                                                                    $i++;
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } ?>
                                            <tr class="active" style="color: red;font-weight: bold;">
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><?php echo @number_format(str_replace('-', '', @$DocSum1), 2); ?>
                                                    ₪
                                                </td>
                                                <td><?php echo @number_format(str_replace('-', '', @$DocSum2), 2); ?>
                                                    ₪
                                                </td>
                                                <td></td>
                                                <td><?php echo @number_format(str_replace('-', '', @$DocSum3), 2); ?>
                                                    ₪
                                                </td>
                                                <td><?php echo @number_format(str_replace('-', '', @$DocSum4), 2); ?>
                                                    ₪
                                                </td>
                                                <td><?php echo @number_format(str_replace('-', '', '0'), 2); ?> ₪</td>
                                            </tr>
                                            <thead>
                                            <td colspan="11" height="20"></td>
                                            </thead>


                                            <?php
                                            }
                                            }
                                            //לופ לסוגי המסמכים
                                            ?>


                                            </tbody>

                                        </table>


                                    </div>

                                </div>
                            </div>

                            <script type="text/javascript" charset="utf-8">

                                $("#BrandId").change(function () {
                                    var BrandId = this.value;
                                    var startDate = '<?php echo $cMonth; ?>';
                                    var endDate = '<?php echo $cYear; ?>';

                                    window.location.href = 'CartesetAll.php?BrandId=' + BrandId + '&month=' + startDate + '&year=' + endDate;

                                });


                            </script>


                            <?php else: ?>
                                <?php redirect_to('../index.php'); ?>
                            <?php endif ?>


                            <?php endif ?>

                            <?php if (Auth::guest()): ?>

                                <?php redirect_to('../index.php'); ?>

                            <?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>