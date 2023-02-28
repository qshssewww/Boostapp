<?php

require_once __DIR__ . '/../app/init.php';

if (Auth::guest()) redirect_to(App::url());

$pageTitle = lang('failed_report');
require_once '../app/views/headernew.php';

if (Auth::check()): ?>
    <?php if (Auth::userCan('21')):

    $CompanyNum = Auth::user()->CompanyNum;
    $DocGetNewSum = DB::table('payment')->where('Status', '=', '2')->where('CompanyNum', '=', $CompanyNum)->sum('Amount');

    ?>



<link href="<?php echo asset_url('office/css/vendor/dataTables.bootstrap.css') ?>" rel="stylesheet">
    <script src="<?php echo asset_url('office/js/vendor/jquery.dataTables.min.js') ?>"></script>
    <script src="<?php echo asset_url('office/js/vendor/dataTables.bootstrap.js') ?>"></script>
<link href="assets/css/fixstyle.css" rel="stylesheet">

    <div class="row">

        <?php include("ReportsInc/SideMenu.php"); ?>

        <div class="col-md-10 col-sm-12">
            <div class="tab-content">

                <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                    <div class="card spacebottom">
                        <div class="card-header text-start"><i
                                    class="fas fa-exclamation-triangle fa-fw"></i><strong><?php echo lang('reports_direct_fail_title') ?> <?php echo @number_format(str_replace('-', '', @$DocGetNewSum), 2); ?>
                                ₪</strong></div>
                        <div class="card-body">


                            <div class="row">
                                <div class="col-md-12 col-sm-12">

                                    <div align="left">
                                        <a href="#" id="SendEmailErrorGroup"
                                           class="btn btn-dark text-white"><?php echo lang('send_cc_update_button') ?></a>
                                        <a href="#" id="SendPaymnetErrorGroup"
                                           class="btn btn-primary"><?php echo lang('charge_client_button') ?></a>
                                    </div>
                                    <?php

                                    $DocGets = DB::table('payment')->where('Status', '=', '2')->groupBy('ClientId')->where('CompanyNum', '=', $CompanyNum)->orderBy('Date', 'ASC')->get();

                                    foreach ($DocGets as $DocGet) {
                                        $ClientInfo = DB::table('client')->where('id', '=', $DocGet->ClientId)->where('CompanyNum', '=', $CompanyNum)->first();

                                        if (!$ClientInfo) {
                                            continue;
                                        }
                                        ?>

                                        <div class="row" style="padding-left:15px; padding-right:15px;">

                                            <strong><a href="ClientProfile.php?u=<?php echo $DocGet->ClientId; ?>"><?php echo $ClientInfo->CompanyName; ?>
                                                    - <?php echo $ClientInfo->ContactMobile; ?></a></strong>

                                            <table class="table table-hover" style="font-size:12px; font-weight:bold;">
                                                <thead>
                                                <tr style="background-color:#bce8f1;">
                                                    <th style="text-align:start;"><?php echo lang('original_charge_date') ?></th>
                                                    <th style="text-align:start;"><?php echo lang('charging_attempts') ?></th>
                                                    <th style="text-align:start;"><?php echo lang('next_charge_date') ?></th>
                                                    <th style="text-align:start;"><?php echo lang('return_reason') ?></th>
                                                    <th style="text-align:start;"><?php echo lang('table_charge_amount') ?></th>
                                                    <th style="text-align:start; width: 200px;"><?php echo lang('actions') ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php

                                                $DocGetNews = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '2')->where('ClientId', '=', $DocGet->ClientId)->orderBy('Date', 'ASC')->get();
                                                $DocGetNewSum = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '2')->where('ClientId', '=', $DocGet->ClientId)->sum('Amount');


                                                foreach ($DocGetNews as $DocGetNew) {

                                                    ?>
                                                    <tr>
                                                        <td><?php echo with(new DateTime($DocGetNew->Date))->format('d/m/Y'); ?></td>
                                                        <td><?php echo $DocGetNew->NumTry; ?></td>
                                                        <td><?php echo with(new DateTime($DocGetNew->TryDate))->format('d/m/Y'); ?></td>
                                                        <td><?php echo $DocGetNew->Error; ?></td>
                                                        <td><?php echo @number_format(str_replace('-', '', @$DocGetNew->Amount), 2); ?>
                                                            ₪
                                                        </td>
                                                        <td>


                                                            <select name="StatusEvent"
                                                                    id="StatusEvent<?php echo $DocGetNew->id ?>"
                                                                    data-placeholder="בחר סטטוס" class="form-control"
                                                                    style="width:100%;">
                                                                <option value="<?php echo $DocGetNew->id ?>:2" <?php if ($DocGetNew->Status == '2') {
                                                                    echo 'selected';
                                                                } else {
                                                                } ?>><?php echo lang('failed_single') ?></option>
                                                                <option value="<?php echo $DocGetNew->id ?>:3" <?php if ($DocGetNew->Status == '3') {
                                                                    echo 'selected';
                                                                } else {
                                                                } ?>><?php echo lang('charge_again') ?></option>
                                                                <option value="<?php echo $DocGetNew->id ?>:4" <?php if ($DocGetNew->Status == '4') {
                                                                    echo 'selected';
                                                                } else {
                                                                } ?>><?php echo lang('mark_as_lost_debt') ?></option>
                                                            </select>

                                                        </td>
                                                    </tr>


                                                    <script>

                                                        $(document).ready(function () {
                                                            $("#StatusEvent<?php echo $DocGetNew->id ?>").change(function () {
                                                                var Acts = this.value;
                                                                $.ajax({
                                                                    type: 'POST',
                                                                    url: 'action/StatusChangeHK.php',
                                                                    data: 'Act=' + Acts,
                                                                    success: function (msg) {
                                                                        alert(msg);
                                                                    }
                                                                });
                                                            });
                                                        });

                                                    </script>


                                                <?php } ?>
                                                <tr class="active">

                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>


                                                    <td><?php echo @number_format(str_replace('-', '', @$DocGetNewSum), 2); ?>
                                                        ₪
                                                    </td>
                                                    <td></td>

                                                </tr>


                                                </tbody>
                                            </table>


                                        </div>


                                    <?php } ?>


                                </div>
                            </div>
                        </div>


                        <script>

                            $('#SendEmailErrorGroup').on('click', function () {

                                $.ajax({
                                    type: 'POST',
                                    url: 'action/SendEmailErrorGroup.php',
                                    success: function (msg) {
                                        alert(msg);
                                    }
                                });


                            });

                            $('#SendPaymnetErrorGroup').on('click', function () {

                                $.ajax({
                                    type: 'POST',
                                    url: 'action/SendPaymnetErrorGroup.php',
                                    success: function (msg) {
                                        alert(msg);
                                    }
                                });

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