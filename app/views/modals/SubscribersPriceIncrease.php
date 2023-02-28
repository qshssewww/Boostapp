<?php
require_once  __DIR__.'/../../init.php';
if (Auth::check() && Auth::userCan('*') && (int)Auth::user()->role_id !== 1) {
    require_once __DIR__.'/../../helpers/TimeHelper.php';
    require_once __DIR__.'/../../../office/Classes/StudioPriceConfirm.php';
    require_once __DIR__.'/../../../office/Classes/Settings.php';

    $CompanyNum = (int)Auth::user()->CompanyNum;
    $companies = [
        10741,
        626825,
        804953,
        665309,
        53939,
        505280,
        572491,
        810388,
        912834,
        917802,
        937297,
        16064,
        180852,
        205059,
        234051,
        263630,
        294732,
        359144,
        425763,
        455146,
        521060,
        536847,
        613650,
        692955,
        730249,
        771698,
        882273,
        952424,
        52835,
        134705,
        578226,
        210877,
        807541,
        549552,
        749393,
        411196,
        539540,
        557822,
        504979,
        600728
    ];

    // check if the studio have confim the popup notice
    if (!StudioPriceConfirm::getByCompanyNum($CompanyNum)) {
        $hasPaytoken = false;
        $UserId = Auth::user()->id;
        $settings = Settings::getSettings($CompanyNum);

        $softClientId = DB::table('247softnew.client')
            ->where('FixCompanyNum', $CompanyNum)
            ->whereNotIn('FixCompanyNum', $companies)
            ->pluck('id') ?? 0;

        $Paytoken = DB::table('247softnew.paytoken')
            ->select('LastPayment', 'NextPayment')
            ->where('ClientId', $softClientId)
            ->where('ItemId', 2)
            ->where("Status", 0)
            ->orderBy('id', 'desc')
            ->first();

        if ($Paytoken && !empty($Paytoken->LastPayment)) {
            $hasPaytoken = true;
            $lastPayment = date("Y-m-d", strtotime('-1 month', strtotime($Paytoken->NextPayment)));

            $clientsCount = DB::table('boostapp.client_count')
                ->where('CompanyNum', '=', $CompanyNum)
                ->whereBetween('Date', array($lastPayment, $Paytoken->NextPayment))
                ->max('CountClient');

            $clientsCount = $clientsCount ?: 0;

            $Subscription = DB::table('247softnew.cleint_pricelist')
                ->select('Text', 'Amount')
                ->where('ClientId', $softClientId)
                ->where('NumClient', '<=', $clientsCount)
                ->orderBy('NumClient', 'desc')
                ->first();

            $subscriptionType = DB::table('247softnew.cleint_pricelist')->select('id')->where('ClientId', $softClientId)->get();
            $subscriptionType = !empty($subscriptionType) && count($subscriptionType) > 1 ? lang('billing_plan_active_clients') : lang('billing_plan_fix_price');

            $oldPrice = number_format($Subscription->Amount,2);
            $newPrice = number_format(ceil(((int)$Subscription->Amount) * 1.18), 2);

            $priceTranslation = lang('price');
            $subscriptionPlan = preg_replace("/$priceTranslation/i", lang('plan_single'), $Subscription->Text);
            $date = date('2023-01-01');
            $dfrom = date('d', strtotime($date));
            $dfrom .= " ";
            $dfrom .= TimeHelper::getHebrewMonthByNumber((int)date('m', strtotime($date)));
            $dfrom .= " ";
            $dfrom .= date('Y', strtotime($date));
        }

        if ($hasPaytoken) {
            ?>
            <link rel="stylesheet" href="assets/css/studioPriceConfirmModal.css">
            <div class="modal fade" id="SubscribersPriceIncreaseModal" data-backdrop="static" tabindex="-1"
                 aria-labelledby="SubscribersPriceIncreaseLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header d-none"></div>
                        <div class="modal-body text-center">
                            <img src="/assets/img/Logo.png" alt="Boostapp">
                            <h2 id="SubscribersPriceIncreaseLabel"
                                class="h3 font-weight-bold my-6"><?= lang('subscription_price_increase') ?></h2>
                            <p class="text-secondary text-gray-400 mb-50"><?= $subscriptionPlan ?? '' ?></p>
                            <h3 class="h6"><?= lang('billing_plan_price_update') ?></h3>
                            <div class="container bg-light rounded py-6 my-20 text-start">
                                <div class="d-flex flex-nowrap py-6 plan font-weight-500">
                                    <div class="col-4 text-gray-400 pl-0"><?= lang('plan_single') ?></div>
                                    <div class="col-8"><?= $subscriptionType ?? '' ?></div>
                                </div>
                                <div class="d-flex flex-nowrap py-6 font-weight-500 new-price">
                                    <div class="col-4 text-gray-400 pl-0"><?= lang('new_price') ?></div>
                                    <div class="col-8 font-weight-bold"><span class="unicode-plaintext"><?= isset($newPrice) ? lang('currency_symbol').$newPrice : '' ?></span> <?= lang('per_month') ?></div>
                                </div>
                                <div class="d-flex flex-nowrap py-6 font-weight-500 old-price">
                                    <div class="col-4 text-gray-400 pl-0"><?php echo lang('old_price'); ?></div>
                                    <div class="col-8"><span class="unicode-plaintext"><?= isset($oldPrice) ? lang('currency_symbol').$oldPrice : '' ?></span> <?= lang('per_month') ?></div>
                                </div>
                                <div class="d-flex flex-nowrap py-6 font-weight-500 apply-from">
                                    <div class="col-4 text-gray-400 pl-0"><?php echo lang('starts_from'); ?></div>
                                    <div class="col-8"><?= $dfrom ?? '' ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <p class="moreinfo text-center text-gray-400 w-100 mb-10"
                               style="font-size: 14px"><?= lang('for_moreinfo_goto_support') ?></p>
                            <button type="button" id="ConfirmSubscriptionPriceIncreasing" class="btn btn-dark w-100 py-10"
                                    data-dismiss="modal"><?= lang('confirm') ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                $(document).ready(function () {
                    setTimeout(() => {
                        if (!$('.modal-content').is(":visible")) {
                            $('#SubscribersPriceIncreaseModal').modal('show');
                        }
                    }, 1500);

                    $('#ConfirmSubscriptionPriceIncreasing').on('click', function (e) {
                        $.ajax({
                            method: "POST",
                            url: "ajax/StudioPriceConfirm.php",
                            data: {action: "insertPriceConfirm", status: 1}
                        }).done(function (response) {
                            if (response.success) {
                                console.log(response);
                            } else {
                                $.notify(
                                    {icon: 'fas fa-times-circle', message: lang('error_oops_something_went_wrong')},
                                    {type: 'danger'}
                                );
                            }
                        });

                    });
                });
            </script>
            <?php
        }
    }
}
