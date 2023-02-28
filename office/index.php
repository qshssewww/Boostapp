<?php
ini_set("max_execution_time", 0);

require_once '../app/init.php';
$pageTitle = lang('dashboard');
require_once '../app/views/headernew.php';
if (Auth::guest()) {
    redirect_to(App::url());
} else if (Auth::check()) {
ini_set("display_Errors", false);
/**
 * @param $todays_tasks_progress_percent
 * @param $MaxAct
 * @return array
 */
function getProgressColorClass($todays_tasks_progress_percent, $MaxAct)
{
    $progress_color_class = 'bg-danger';
    $percent_friendly = 0;
    if ($MaxAct > 0) {
        $percent = $todays_tasks_progress_percent / $MaxAct;
        $percent_friendly = number_format($percent * 100, 2);
        switch ($percent_friendly) {
            case $percent_friendly <= 50 :
                $progress_color_class = 'bg-danger';
                break;
            case ($percent_friendly >= 50 && $percent_friendly < 80):
                $progress_color_class = 'bg-info';
                break;

            case ($percent_friendly >= 80):
                $progress_color_class = 'bg-success';
                break;
        }
    }
    return ["color" => $progress_color_class, "percent" => $percent_friendly];
}

//Permissions
$CostomersList = DB::table('roleslist')->where('Category', '=', 5)->get();
$ReaportsList = DB::table('roleslist')->where('Category', '=', 2)->get();
$ClassesList = DB::table('roleslist')->where('Category', '=', 6)->get();
$DashboardList = DB::table('roleslist')->where('Category', '=', 7)->where('Status', 0)->get();

$CostomerPermission = false; //was used for dashboard row #1
$ReportsPermission = false; //was used for dashboard row #2
$ClassesPermission = false; //was used for dashboard row #3
$DashboardPermissionRow1 = false;
$DashboardPermissionRow2 = false;
$DashboardPermissionRow3 = false;
foreach ($CostomersList as $Costomer) {
    if (Auth::userCan($Costomer->id)) {
        $CostomerPermission = true;
        break;
    }
}
foreach ($ReaportsList as $Report) {
    if ($Report->id == 128) {
        if (Auth::userCan($Report->id)) {
            $ReportsPermission = true;
            break;
        }
    }
}
foreach ($ClassesList as $Class) {
    if (Auth::userCan($Class->id)) {
        $ClassesPermission = true;
        break;
    }
}

foreach ($DashboardList as $Dashboard) {
    if (($Dashboard->id == 155) && Auth::userCan($Dashboard->id)) {
        $DashboardPermissionRow1 = true;
    }
    if (($Dashboard->id == 156) && Auth::userCan($Dashboard->id)) {
        $DashboardPermissionRow2 = true;
    }
    if (($Dashboard->id == 157) && Auth::userCan($Dashboard->id)) {
        $DashboardPermissionRow3 = true;
    }

}





require_once "./Classes/Company.php";
require_once "./Classes/Pipeline.php";
require_once "./Classes/calendar.php";

require_once './Classes/CalType.php';
require_once "./Classes/Client.php";
require_once "./Classes/ActiveClients.php";
require_once "./Classes/CheckClient.php";
require_once "./Classes/ClientActivities.php";
require_once "./Classes/DocsPayments.php";
require_once "./Classes/ClassCalendar.php";
require_once  "./Classes/247SoftNew/SoftPayment.php";

/** @var TYPE_NAME $CompanyNum */

$company = Company::getInstance(false);
$companyNum = $company->__get("CompanyNum");
$BrandsMain = $company->BrandsMain;

$ClientActivities = new ClientActivities();
$Clients = new Client();
$CheckClient = new CheckClient();
$ActiveClients = new ActiveClients();
$DocsPayment = new DocsPayment();
$Pipeline = new Pipeline();
$ClassCalendar = new ClassCalendar();
$Calendar = new calendar();
$CounterOfLastThirtyDaysActive = $ActiveClients->GetCounterOfLastThirtyDays($companyNum);
$LastActiveClient = $ActiveClients->GetCompanyByLastDate($companyNum);
$LastActiveClient = $LastActiveClient->count ?? 0;

$CounterOfLastThirtyDaysCheck = $CheckClient->GetCounterOfLastThirtyDays($companyNum);

$LastCheckClient = $CheckClient->GetCompanyByLastDate($companyNum);
$LastCheckClient = $LastCheckClient->count ?? 0;

$ClientsCounter = $Clients->getActiveCheck($companyNum);
$ClientsMembershipCheck = $Clients->getClientsMembershipCheckCounter($companyNum);
$BalanceAmountClients = $Clients->GetBalanceAmountClients($companyNum);
$FreezesClients = $ClientActivities->GetFreezClients($companyNum);
$DailyDeals = $DocsPayment->getDailyDeals($companyNum);
$DailyDealsCount = $DocsPayment->getDailyDealsCount($companyNum);

$LastMonthDeals = $DocsPayment->getLastMonthDeals($companyNum);
$CurrentMonthDeals = $DocsPayment->getCurrentMonthDeals($companyNum);

$OpenLeads = array_filter($Pipeline->GetOpenLeads($companyNum), function($Lead) use ($companyNum){
    if(!empty(LeadStatus::getLeadStatus($companyNum, $Lead->PipeId))) return $Lead;
});

$CurrenDayLeads = $Pipeline->GetCurrenDayLeads($companyNum);
$LeadsAtLeatThirtyDays = $Pipeline->GetLeadsAtLeatThirtyDays($companyNum);

$ClassesAct = $ClassCalendar->getClassesAct($companyNum);

$OpenMission = $Calendar->GetOpenMissionCurrentLateDay($companyNum);

$LastweekDateOfBirth = $Clients->GetLastweekDateOfBirth($companyNum);


$Category2 = DB::table('automation')->where('CompanyNum', '=', $CompanyNum)->where('Category', '=', '2')->where('Type', '=', '1')->where('Status', '=', '0')->count();
$paymentAction = $_GET['action'] ?? null;
if(isset($_GET['spid']) && !empty($_GET['spid'])) {
    $softPayment = SoftPayment::getRow($_GET['spid']);
} else {
    $softPayment = [];
}
?>
</div><!-- closing the col from the common header -->
</div><!-- closing the row from the common header -->
<link href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/CDN/select2/select2.min.css' ?>" rel="stylesheet" />

<link href="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/CDN/select2/select2-bootstrap.css' ?>" rel="stylesheet">

<script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/CDN/select2/select2.min.js' ?>"></script>

<script src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/CDN/select2/he.js' ?>"></script>

<link href="/office/calendarPopups/assets/css/createNewCalendar.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/rr-1.2.7/sc-2.0.2/sl-1.3.1/datatables.min.css"/>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.21/fh-3.1.7/r-2.2.5/rr-1.2.7/sc-2.0.2/sl-1.3.1/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/clipboard-js@0.3.6/clipboard.min.js"></script>
<script>
    const js_app_url = '<?php echo $_SERVER["HTTP_HOST"] != "localhost:8000" ? App::url() : "http://localhost:8000" ?>';
</script>

<link href="/office/assets/css/fixstyle.css?<?php echo filemtime('assets/css/fixstyle.css') ?>" rel="stylesheet">
    <a href="#js-action-modal" data-toggle="modal" class="floating-plus-btn d-flex bg-primary">
<!--    <a href="/office/client-popup.php" class="floating-plus-btn d-flex bg-primary">-->
        <i class="fal fa-plus fa-lg margin-a"></i>
</a>
<?php if ($DashboardPermissionRow1) { ?>
    <section class="">
        <div class="container  px-0 px-sm-15" >
            <!-- slider section :: begin  -->
            <div class="row">
                <div class="col-md-12 px-0 position-relative ">
                    <div class="js-swiper swiper-container px-md-15  " >
                        <div class="swiper-wrapper px-15 px-md-15  d-lg-flex w-100" >
                            <div class="swiper-slide py-15 h-100 flex-lg-fill" style="min-width: 275px !important;">
                                <div class="card card-body  justify-content-between   shadow rounded text-start border-0  h-100 p-15">
                                    <div>
                                        <i class="fal fa-users bsapp-fs-24 text-primary"></i>
                                        <a href="/office/Client.php?Act=0" class="undecorate"><h5 class="text-black"><?php echo lang('active_clients') ?></h5></a>
                                    </div>
                                    <div>
                                        <a href="/office/Reports/membership.php" class="undecorate">
                                            <small class="text-muted"><?php echo lang('valid_membership_client') ?> <?php echo $ClientsMembershipCheck['clientsMembershipCounter'] ?></small>
                                        </a>
                                    </div>
                                    <div class="d-flex mt-5 justify-content-between align-items-end">
                                        <div>
                                            <?php if ($ClientsCounter['clientsActiveCounter'] >= $LastActiveClient) { ?>
                                            <h2><i class="fas fa-caret-up  text-primary"></i>
                                                <?php } else { ?>
                                                <h2><i class="fas fa-caret-down text-danger"></i>
                                                    <?php } ?>
                                                    <?php echo $ClientsCounter['clientsActiveCounter']; ?> </h2>
                                        </div>
                                        <div class="w-125p h-65p  overflow-hidden">
                                            <canvas id="js-line-chart-1" width="125" height="65"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide py-15 h-100  flex-lg-fill" style="min-width: 275px !important;">
                                <div class="card card-body justify-content-between  shadow rounded text-start border-0 h-100 p-15">
                                    <div>
                                        <i class="fal fa-exclamation-circle bsapp-fs-24 text-danger"></i>
                                        <h5> <?php echo lang('require_attention') ?> </h5>
                                        <a href="/office/Reports/CheckClients.php" class="stretched-link"></a>
                                        <small class="text-muted"><?php echo lang("membership_soon_expire") . " / " . lang("invalid_date_dashboard") ?></small>
                                    </div>
                                    <div class="d-flex  justify-content-between align-items-end mt-5">
                                        <div>
                                            <?php if ($ClientsCounter['clientCheckCounter'] >= $LastCheckClient) { ?>
                                            <h2><i class="fas fa-caret-up  text-danger"></i>
                                                <?php } else { ?>
                                                <h2><i class="fas fa-caret-down text-primary"></i>
                                                    <?php } ?>
                                                    <?php echo $ClientsCounter['clientCheckCounter']; ?>  </h2>
                                        </div>
                                        <div class="w-125p h-65p overflow-hidden">
                                            <canvas id="js-line-chart-2" width="125" height="65"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-slide py-15 h-100  flex-lg-fill">
                                <div class="card card-body  justify-content-between shadow rounded text-start border-0 h-100 p-15">
                                    <div>
                                        <div class="position-relative">
                                            <i class="fal fa-shekel-sign bsapp-fs-24"></i>
                                            <h5> <?php echo lang('debit') ?> </h5>
                                            <a href="/office/Reports/Debt.php" class="stretched-link"></a>
                                        </div>
                                        <small class="text-muted"> <?php echo lang('debts') ?></small>
                                    </div>
                                    <div class="d-flex  justify-content-between align-items-end  mt-5">
                                        <div>
                                            <h2><?= number_format($BalanceAmountClients, 2) ?> </h2>
                                        </div>
                                        <div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div  class="swiper-slide py-15 h-100  flex-lg-fill pie-30 pie-lg-0">
                                <div class="card card-body justify-content-between  shadow rounded text-start border-0 h-100 p-15">
                                    <div>
                                        <div class="position-relative">
                                            <i class="fal fa-snowflake bsapp-fs-24 text-info"></i>
                                            <h5> <?php echo lang('freeze') ?></h5>
                                            <a href="/office/MembershipFrozen.php" class="stretched-link"></a>
                                        </div>
                                        <small class="text-muted"><?php echo lang("on_hold_subscription") ?></small>
                                    </div>
                                    <div class="d-flex  justify-content-between align-items-end  mt-5">
                                        <div>
                                            <h2><?php echo $FreezesClients ?></h2>
                                        </div>
                                        <div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- slider section :: end  -->
        </div>
    </section>
<?php } if ($DashboardPermissionRow2) { ?>
    <section class="mt-sm-5 mt-5">
        <div class="container">
            <!-- slider section :: begin  -->
            <div class="row d-flex justify-content-between px-md-15">
                <div class="col-md-6 px-0 px-md-15">
                    <div class="row h-100">
                        <div  class="col-md-5 col-sm-6 col-6 my-10 bsapp-min-h-300p">
                            <div class="card card-body  bg-primary text-white  shadow rounded text-start border-0 h-100">
                                <div class="text-center position-relative">
                                    <h6 class="mb-15"><?php echo lang('today_revenue') ?></h6>
                                    <a href="/office/Reports/receipts.php" class="stretched-link"></a>
                                </div>
                                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                    <h4>
                                        <span class="h4"><i class="fal fa-shekel-sign"></i></i></span>
                                        <?php echo (empty($DailyDeals) ? 0 : $DailyDeals) ?>
                                        <!--                           </h2>-->
                                        <!--                           <span><u>--><?php //echo $DailyDealsCount == 0 ?  "": "Transactions: ". $DailyDealsCount                       ?><!--</u></span>-->
                                    </h4>
                                    <span><u><?php echo $DailyDealsCount == 0 ? "" : $DailyDealsCount . " " . lang("deals") ?></u></span>

                                </div>

                            </div>
                        </div>
                        <div class="col-md-7 col-sm-6 col-6 my-10 bsapp-min-h-300p">
                            <div class="card card-body bg-dark text-white   align-items-center justify-content-between  shadow rounded text-start border-0 h-100">
                                <div class="text-center position-relative">
                                    <h6 class="mb-2"><?php echo lang('total_revenue') ?> <a class="revenue-link" href="javascript:;" onclick="BarClick('cur')"  ><?php echo lang('this_month') ?></a> <?php echo lang('versus') ?></h6>
                                    <h6 class="mb-15"> <a class="revenue-link prev" href="javascript:;" onclick="BarClick('prev')"  > <?php echo lang('last_month') ?></a></h6>

                                </div>
                                <div class="w-100  bsapp-max-w-250p d-flex justify-content-center">
                                    <canvas id="js-bar-chart" width="150" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 px-0 px-md-15">
                    <div class="row h-100">
                        <div class="col-md-5 col-sm-6 col-6  my-10 bsapp-min-h-300p">
                            <div class="card card-body  flex-column justify-content-between align-items-center  shadow rounded text-start border-0 h-100">
                                <div  class="text-center" >
                                    <h6 class="mb-15 position-relative"><?php echo lang('open_leads') ?> <a  href="/office/LeadsJoinReport.php" class="stretched-link" ></a></h6>
                                    <h1><?php echo count($OpenLeads) ?><span> <i class="fal fa-users-class"></i></span></h1>
                                </div>
                                <div class="text-center" >
                                    <h6 class="mb-15" style="transform: rotate(0)"><?php echo lang('new_from_today') ?> <a  href="/office/LeadsJoinReport.php?today=1" class="stretched-link" ></a></h6>
                                    <h1> <?php echo count($CurrenDayLeads) ?></h1>

                                </div>

                            </div>
                        </div>
                        <div   class="col-md-7 col-sm-6 col-6 my-10 bsapp-min-h-300p">
                            <div class="card card-body flex-column align-items-center justify-content-between  shadow rounded text-start border-0 h-100">
                                <div class="position-relative">
                                    <h6 class="mb-5 text-center "><?php echo lang('leads') ?></h6>
                                    <h6 class="mb-15 text-center"><?php echo lang('last_30_days') ?></h6>
                                    <a  href="/office/LeadsJoinReport.php?lastmonth=1" class="stretched-link" ></a>
                                </div>
                                <div class="w-100  bsapp-max-w-250p  h-100 d-flex justify-content-center align-items-center" >
                                    <canvas id="js-doughnut-chart" width="150" height="200"></canvas>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- slider section :: end  -->
        </div>
    </section>
<?php } if ($DashboardPermissionRow3) { ?>
    <section class="mt-sm-5 mt-5">
        <div class="container">
            <!-- slider section :: begin  -->
            <div class="row d-flex px-md-15">
                <div class="col-md-4  px-0 px-md-15  my-10">
                    <div class="card card-body  shadow rounded text-start border-0 h-100 px-0">
                        <?php if (empty($ClassesAct)) { ?>
                            <div class="px-15 px-full-height">
                                <h6><?php echo lang('classes_today') ?></h6>
                                <div class="my-15 d-flex d-flex-center">
                                    <h5 class="font-weight-norma"><?php echo lang('no_classes_today') ?></h5>
                                    <svg class="icon-svg icon-svg-lessons" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400"><g data-name="Layer 2"><g data-name="Layer 5"><rect class="cls-1" x="4.3" y="113.84" width="248.64" height="196.41" rx="19.82"/><path class="cls-2" d="M252.93,154.12V133.66a19.82,19.82,0,0,0-19.82-19.82h-209A19.82,19.82,0,0,0,4.3,133.66v20.46Z"/><line class="cls-3" x1="192.25" y1="310.25" x2="180.12" y2="310.25"/><path class="cls-3" d="M252.93,269.07v21.36a19.82,19.82,0,0,1-19.82,19.82h-28.3"/><path class="cls-3" d="M226.6,113.84h6.51a19.82,19.82,0,0,1,19.82,19.82v31.15"/><line class="cls-3" x1="195.58" y1="113.84" x2="204.3" y2="113.84"/><line class="cls-3" x1="87.38" y1="113.84" x2="171.22" y2="113.84"/><line class="cls-3" x1="52.77" y1="113.84" x2="61.75" y2="113.84"/><path class="cls-3" d="M140.71,310.25H24.12A19.82,19.82,0,0,1,4.3,290.43V133.66a19.82,19.82,0,0,1,19.82-19.82h7.63"/><rect class="cls-1" x="36.13" y="89.81" width="12.23" height="33.21" rx="4.61"/><rect class="cls-1" x="67.38" y="89.75" width="12.23" height="33.21" rx="4.61"/><rect class="cls-1" x="177.62" y="89.81" width="12.23" height="33.21" rx="4.61"/><rect class="cls-1" x="208.87" y="89.75" width="12.23" height="33.21" rx="4.61"/><rect class="cls-3" x="36.13" y="89.81" width="12.23" height="33.21" rx="4.61"/><rect class="cls-3" x="67.38" y="89.75" width="12.23" height="33.21" rx="4.61"/><rect class="cls-3" x="177.62" y="89.81" width="12.23" height="33.21" rx="4.61"/><rect class="cls-3" x="208.87" y="89.75" width="12.23" height="33.21" rx="4.61"/><rect class="cls-4" x="76.91" y="172.85" width="47.47" height="34.05" rx="3.99"/><rect class="cls-4" x="76.91" y="215.17" width="47.47" height="34.05" rx="3.99"/><rect class="cls-4" x="188.39" y="172.85" width="47.47" height="34.05" rx="3.99"/><rect class="cls-4" x="76.91" y="257.49" width="47.47" height="34.05" rx="3.99"/><rect class="cls-4" x="21.37" y="215.17" width="47.28" height="34.05" rx="3.99"/><rect class="cls-4" x="132.65" y="215.17" width="47.47" height="34.05" rx="3.99"/><rect class="cls-4" x="21.37" y="257.49" width="47.28" height="34.05" rx="3.99"/><rect class="cls-4" x="21.37" y="172.85" width="47.28" height="34.05" rx="3.99"/><rect class="cls-4" x="132.65" y="172.85" width="47.47" height="34.05" rx="3.99"/><rect class="cls-4" x="132.65" y="257.49" width="47.47" height="34.05" rx="3.99"/><rect class="cls-4" x="188.39" y="215.17" width="47.47" height="34.05" rx="3.99"/><rect class="cls-3" x="76.91" y="172.85" width="47.47" height="34.05" rx="3.99"/><rect class="cls-3" x="76.91" y="215.17" width="47.47" height="34.05" rx="3.99"/><path class="cls-3" d="M226.6,172.85H192.38a4,4,0,0,0-4,4v26.08a4,4,0,0,0,4,4h7.69"/><path class="cls-3" d="M235.86,202.92V176.84"/><rect class="cls-3" x="76.91" y="257.49" width="47.47" height="34.05" rx="3.99"/><rect class="cls-3" x="21.37" y="215.17" width="47.28" height="34.05" rx="3.99"/><rect class="cls-3" x="132.65" y="215.17" width="47.47" height="34.05" rx="3.99"/><rect class="cls-3" x="21.37" y="257.49" width="47.28" height="34.05" rx="3.99"/><rect class="cls-3" x="21.37" y="172.85" width="47.28" height="34.05" rx="3.99"/><rect class="cls-3" x="132.65" y="172.85" width="47.47" height="34.05" rx="3.99"/><rect class="cls-3" x="132.65" y="257.49" width="47.47" height="34.05" rx="3.99"/><path class="cls-3" d="M197.91,215.17h-5.53a4,4,0,0,0-4,4v26.07a4,4,0,0,0,4,4H226.6"/><path class="cls-3" d="M235.86,245.23V219.16a4,4,0,0,0-4-4"/><line class="cls-3" x1="221.1" y1="154.12" x2="252.93" y2="154.12"/><line class="cls-3" x1="197.38" y1="154.12" x2="206.61" y2="154.12"/><line class="cls-3" x1="12.77" y1="154.12" x2="161.99" y2="154.12"/><path class="cls-1" d="M289.79,201H265.72V176.93a5.92,5.92,0,0,0-5.91-5.91h-20.1a5.91,5.91,0,0,0-5.91,5.91V201H209.72a5.91,5.91,0,0,0-5.91,5.9V227a5.92,5.92,0,0,0,5.91,5.91H233.8V257a5.9,5.9,0,0,0,5.91,5.91h20.1a5.91,5.91,0,0,0,5.91-5.91V232.93h24.07A5.91,5.91,0,0,0,295.7,227V206.91A5.9,5.9,0,0,0,289.79,201Z"/><path class="cls-3" d="M233.8,252.39V257a5.9,5.9,0,0,0,5.91,5.91h20.1a5.91,5.91,0,0,0,5.91-5.91V238.54"/><path class="cls-3" d="M228.87,201H209.72a5.91,5.91,0,0,0-5.91,5.9V227a5.92,5.92,0,0,0,5.91,5.91H233.8v12.9"/><path class="cls-3" d="M273.08,201h-7.36V176.93a5.92,5.92,0,0,0-5.91-5.91h-20.1a5.91,5.91,0,0,0-5.91,5.91v15.63"/><path class="cls-3" d="M270.9,232.93h18.89A5.91,5.91,0,0,0,295.7,227V206.91a5.9,5.9,0,0,0-5.91-5.9h-6.6"/><rect class="cls-5" width="300" height="400"/></g></g></svg>
                                    <a href="DeskPlanNew.php#js-action-modal"  class="btn btn-grey"><?php echo lang('set_new_class') ?></a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="px-15">
                                <h6><?php echo lang("classes_today") ?></h6>
                                <h5 class="my-15 font-weight-normal d-flex"><div class="mie-10"><span><?php echo count($ClassesAct) ?></span> <span><?php echo lang('classes'); ?></span></div>
                                    <?php
                                    $sumAct = 0;
                                    foreach ($ClassesAct as $class) {
                                        $sumAct += count($class->ClassParticipants);
                                    }
                                    ?>

                                    <div><span><?php echo (string)$sumAct ?> </span><span><?php echo lang("attendees") ?> </span></div></h5>

                            </div>
                            <div class="bsapp-card-scroll px-15">
                                <?php foreach ($ClassesAct as $class) {
                                    $classGuide = Users::find($class->GuideId);
                                    ?>
                                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom border-light py-10">
                                        <div class="d-flex flex-column ">
                                            <span class="font-weight-bold"><a class="ShowClass text-dark" data-classid="<?php echo $class->id ?>" href="javascript:;" ><?php echo $class->ClassName ?></a></span>
                                            <div class="text-muted d-flex align-items-center">
                                                <img src='<?php echo $class->UploadImage ?>' class="w-20p h-20p  rounded-circle mie-8" />
                                                <span class="mie-8"> <?php echo $classGuide->display_name ?? '' ?></span> <span>.</span>
                                                <span><?php echo (string)date('H:i', strtotime($class->start_date)) ?></span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <h6 class="font-weight-bold mie-8 "><?php echo count($class->ClassParticipants) ?>/<?php echo $class->MaxClient ?></h6>
                                            <?php
                                            $digit = count($class->ClassParticipants);
                                            $progress_color_class = getProgressColorClass($digit, $class->MaxClient);
                                            ?>
                                            <div class="progress flex-column-reverse"  style="width:6px;height: 50px;">
                                                <div class="progress-bar  <?php echo $progress_color_class["color"]; ?>" role="progressbar" style="height: <?php echo $progress_color_class["percent"]; ?>%;" ></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-4 px-0 px-md-15  my-10">
                    <div class="card card-body   shadow rounded text-start border-0 h-100 px-0">
                        <?php if (empty($OpenMission->MissionsCurrentDay) && empty($OpenMission->MissionsLate)) { ?>
                            <div class="px-15 px-full-height">
                                <h6><?php echo lang('tasks_for_today') ?></h6>
                                <div class="my-15 d-flex d-flex-center">
                                    <h5 class="font-weight-normal"><?php echo lang('no_tasks') ?></h5>
                                    <svg class="icon-svg icon-svg-tasks" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400"><g data-name="Layer 2"><g data-name="Layer 5"><path class="cls-1" d="M58.64,115.67C46.52,94.43,33.56,83.74,25.36,78.32c-5-3.28-14.23-9.27-19.12-6C4.35,73.62,3.51,76,3.14,79.27c-.58,5.14-.37,15.7,12.38,33.87,10.73,15.29,25,25.56,24.48,26.13s-7.48-6.43-15.61-5.76a6.76,6.76,0,0,0-4.3,1.64c-2.57,2.43-1.92,6.8-1.69,8.32,1.54,10.35,20.76,16.56,22.19,17-6.53,3.19-15.44,9-21.06,19.21-1.76,3.19-7.16,13-3.27,18.1.56.73,2.14,2.46,6.71,3,14,1.74,30.63-10.67,35.67-14.64"/><path class="cls-1" d="M241.45,115.67c12.12-21.24,25.08-31.93,33.28-37.35,5-3.28,14.23-9.27,19.13-6,1.88,1.27,2.72,3.64,3.09,6.92.58,5.14.37,15.7-12.38,33.87-10.73,15.29-25,25.56-24.47,26.13s7.47-6.43,15.6-5.76a6.76,6.76,0,0,1,4.3,1.64c2.57,2.43,1.92,6.8,1.69,8.32-1.54,10.35-20.76,16.56-22.19,17,6.53,3.19,15.44,9,21.06,19.21,1.76,3.19,7.16,13,3.27,18.1-.56.73-2.14,2.46-6.7,3-14,1.74-30.64-10.67-35.68-14.64"/><rect class="cls-2" x="58.48" y="74.28" width="182.81" height="263.27" rx="16.16"/><polyline class="cls-3" points="193.68 337.54 180.11 337.54 171.88 337.54"/><path class="cls-3" d="M210.62,74.28h14.51a16.16,16.16,0,0,1,16.16,16.16V321.38a16.16,16.16,0,0,1-16.16,16.16h-4.87"/><line class="cls-3" x1="108.03" y1="74.28" x2="119.88" y2="74.28"/><path class="cls-3" d="M161.41,337.54H74.64a16.16,16.16,0,0,1-16.16-16.16V90.44A16.16,16.16,0,0,1,74.64,74.28h12"/><rect class="cls-4" x="75.78" y="88.81" width="148.22" height="230.57" rx="6.76"/><rect class="cls-5" x="97.22" y="62.46" width="101.99" height="30.74" rx="5.96"/><path class="cls-2" d="M116.52,157H92.33a4,4,0,0,1-4-4V128.76a4,4,0,0,1,4-4h24.19a4,4,0,0,1,4,4V153A4,4,0,0,1,116.52,157Z"/><path class="cls-3" d="M118.48,125.28a4,4,0,0,1,2,3.48V153a4,4,0,0,1-4,4H92.33a4,4,0,0,1-4-4V128.76a4,4,0,0,1,4-4h17.76"/><rect class="cls-2" x="88.68" y="192.32" width="32.19" height="32.19" rx="4"/><path class="cls-3" d="M119.5,193.3a4,4,0,0,1,1.37,3v24.19a4,4,0,0,1-4,4H92.68a4,4,0,0,1-4-4V196.32a4,4,0,0,1,4-4H109.2"/><rect class="cls-2" x="88.34" y="259.82" width="32.19" height="32.19" rx="4"/><path class="cls-3" d="M119.14,260.79a4,4,0,0,1,1.39,3V288a4,4,0,0,1-4,4H92.34a4,4,0,0,1-4-4V263.82a4,4,0,0,1,4-4h17.25"/><path class="cls-5" d="M210.07,157H129.14a1.26,1.26,0,1,1,0-2.5h80.93a1.26,1.26,0,1,1,0,2.5Z"/><path class="cls-5" d="M196.31,142.11H129.14a1.26,1.26,0,1,1,0-2.5h67.17a1.26,1.26,0,1,1,0,2.5Z"/><path class="cls-5" d="M169.6,127.26H129.14a1.26,1.26,0,1,1,0-2.5H169.6a1.26,1.26,0,1,1,0,2.5Z"/><path class="cls-5" d="M204.59,194.79H129.14a1.26,1.26,0,1,1,0-2.5h75.45a1.26,1.26,0,1,1,0,2.5Z"/><path class="cls-5" d="M203,209.63H129.14a1.26,1.26,0,1,1,0-2.5H203a1.26,1.26,0,1,1,0,2.5Z"/><path class="cls-5" d="M166.9,224.48H129.18a1.26,1.26,0,1,1,0-2.5H166.9a1.26,1.26,0,1,1,0,2.5Z"/><path class="cls-5" d="M178,262.32H129.14a1.26,1.26,0,1,1,0-2.5H178a1.26,1.26,0,1,1,0,2.5Z"/><path class="cls-5" d="M204.5,277.08H129.14a1.26,1.26,0,1,1,0-2.5H204.5a1.26,1.26,0,1,1,0,2.5Z"/><path class="cls-5" d="M129,292a1.32,1.32,0,0,1-1.28-1,1.3,1.3,0,0,1,1-1.53c1.33-.3,46.28-.12,65.54,0a1.3,1.3,0,0,1,1.36,1.25,1.32,1.32,0,0,1-1.37,1.25h0c-25.09-.12-63.1-.22-65,0A1.86,1.86,0,0,1,129,292Z"/><path class="cls-6" d="M94.94,134.32q4.56,6.39,9.11,12.77a125.42,125.42,0,0,1,6.11-13.76,128.1,128.1,0,0,1,8.07-13.52"/><path class="cls-6" d="M94.94,202.13q4.56,6.39,9.11,12.77a125.42,125.42,0,0,1,6.11-13.76,128.1,128.1,0,0,1,8.07-13.52"/><path class="cls-6" d="M94.94,269.94q4.56,6.38,9.11,12.77A125.42,125.42,0,0,1,110.16,269a128.1,128.1,0,0,1,8.07-13.52"/><path class="cls-3" d="M260.16,206.18c10.9,5.1,22,4.29,27.75-1.8a15.61,15.61,0,0,0,3.79-7.58"/><path class="cls-3" d="M267.74,213.86a25,25,0,0,0,10.41.58,24.76,24.76,0,0,0,9.42-3.53"/><path class="cls-3" d="M278.15,68.05A17.65,17.65,0,0,1,295.83,65"/><path class="cls-3" d="M9.29,200.72a18.69,18.69,0,0,0,4.2,4.21,20.91,20.91,0,0,0,11.73,3.64C34.12,209,41,204.8,43.58,203"/><path class="cls-3" d="M17,211.57a22.7,22.7,0,0,0,8.18,2,22.94,22.94,0,0,0,10.58-2"/><path class="cls-3" d="M20.59,67.08a13.54,13.54,0,0,0-10.14-2,13.34,13.34,0,0,0-7.57,4.79"/><rect class="cls-7" width="300" height="400"/></g></g></svg>
                                    <div  class="btn btn-grey js-new-task-modal"><?php echo lang('create_new_task') ?></div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="px-15">
                                <h6><?php echo lang('tasks_for_today') ?></h6>
                                <ul class="nav nav-pills my-15 px-0" id="js-tabs-dashboard" role="tablist">
                                    <li class="nav-item mie-15" >
                                        <a class="btn btn-sm btn-light btn-rounded<?php if (count($OpenMission->MissionsCurrentDay) > 0) { ?> active<?php } ?>" id="home-tab" data-toggle="tab" href="#js-tab-1" ><span id="today-tab-counter"><?php echo sizeof($OpenMission->MissionsCurrentDay) ?></span> <?php echo " " . lang('today') ?></a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="btn btn-sm btn-light btn-rounded<?php if (count($OpenMission->MissionsCurrentDay) == 0) { ?> active<?php } ?>" id="profile-tab" data-toggle="tab" href="#js-tab-2" ><span id="late-tab-counter"><?php echo sizeof($OpenMission->MissionsLate) ?></span> <?php echo " " . lang('late') ?></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="bsapp-card-scroll px-15">
                                <div class="tab-content" id="js-tabs-dashboard-content">
                                    <div class="tab-pane fade<?php if (sizeof($OpenMission->MissionsCurrentDay) > 0) { ?> show active<?php } ?>" id="js-tab-1" >
                                        <?php foreach ($OpenMission->MissionsCurrentDay as $task) { ?>
                                            <div data-id-task='<?php echo $task->id ?>' class="js-task-action d-flex justify-content-between align-items-center w-100 border-bottom border-light py-10">
                                                <div class="d-flex flex-column ">
                                                    <span class="font-weight-bold"><a data-toggle="modal" data-target="#AddNewTask"  onclick="TaskPopup(<?php echo $task->id ?>,<?php echo (empty($task->Client) ? 'null' : $task->Client->id) ?>)" href="javascript:;"  class="text-decoration-none text-gray-900" ><?php echo $task->TypeTitle ?> </a></span>
                                                    <div class="text-muted d-flex align-items-center">
                                                        <?php if (!empty($task->Client)) { ?>
                                                            <span class="mie-8"><a class="text-dark " href="<?php echo "/office/ClientProfile.php?u=" . $task->Client->id ?>"><u> <?php echo $task->Client->FirstName . ' ' . $task->Client->LastName ?></u></a> </span> <span>.</span>
                                                        <?php } else { ?>
                                                            <!--                                         <span class="mie-8"></span> <span>.</span>-->
                                                        <?php } ?>
                                                        <span><?php echo (string)date('H:i', strtotime($task->StartTime)) ?></span>
                                                    </div>
                                                    <div class="text-muted">
                                                        <?php echo $task->Title ?>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end flex-column justify-content-between h-100">
                                                    <a data-id-task='<?php echo $task->id ?>'  href="javascript:;" data-target="#js-confirmation-modal" data-toggle="modal" class="confim_popup btn btn-sm  btn-rounded btn-light  mb-10 px-10">
                                                        <i class="fal fa-check text-success"></i>
                                                    </a>
                                                    <a data-id-task='<?php echo $task->id ?>' href="javascript:;"  data-target="#js-confirmation-modal-2" data-toggle="modal"  class="delete_popup btn btn-sm  btn-rounded btn-light  px-10">
                                                        <i class="fal fa-trash text-muted"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="tab-pane fade<?php if (count($OpenMission->MissionsCurrentDay) == 0) { ?> show active<?php } ?>" id="js-tab-2" >
                                        <?php foreach ($OpenMission->MissionsLate as $task) { ?>
                                            <div data-id-task='<?php echo $task->id ?>' class="js-task-action d-flex justify-content-between align-items-center w-100 border-bottom border-light py-10">
                                                <div class="d-flex flex-column ">
                                                    <span class="font-weight-bold"><a data-toggle="modal" data-target="#AddNewTask"   onclick="TaskPopup(<?php echo $task->id ?>,<?php echo (empty($task->Client) ? 'null' : $task->Client->id) ?>)" href="javascript:;"  class="text-decoration-none text-gray-900" ><?php echo $task->TypeTitle ?> </a></span>
                                                    <div class="text-muted d-flex align-items-center">
                                                        <?php if (!empty($task->Client)) { ?>
                                                            <span class="mie-8"><a class="text-dark"  data-toggle="modal" href="<?php echo "/office/ClientProfile.php?u=" . $task->Client->id ?>"> <u> <?php echo $task->Client->FirstName . ' ' . $task->Client->LastName ?> </u></a> </span> <span></span>
                                                        <?php } else { ?>
                                                            <!--                                          <span class="mie-8"></span> <span>.</span>-->
                                                        <?php } ?>
                                                        <span><?php echo (string)date('d/m', strtotime($task->StartDate)) ?></span>
                                                    </div>
                                                    <div class="text-muted">
                                                        <?php echo $task->Title ?>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-end flex-column justify-content-between h-100">
                                                    <a data-id-task='<?php echo $task->id ?>' href="javascript:" data-target="#js-confirmation-modal" data-toggle="modal" class="confim_popup btn btn-sm  btn-rounded btn-light  mb-10 px-10">
                                                        <i class="fal fa-check text-success"></i>
                                                    </a>
                                                    <a data-id-task='<?php echo $task->id ?>' href="javascript:;"  data-target="#js-confirmation-modal-2" data-toggle="modal"  class="delete_popup btn btn-sm  btn-rounded btn-light  px-10">
                                                        <i class="fal fa-trash text-muted"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-4 px-0 px-md-15 my-10">
                    <div class="card card-body  px-0 shadow rounded text-start border-0 h-100">
                        <?php if (empty($LastweekDateOfBirth)) { ?>
                            <div class="px-15 px-full-height">
                                <h6><?php echo lang('birthdays') ?></h6>
                                <div class="my-15 d-flex d-flex-center">
                                    <h5 class="font-weight-normal"><?php echo lang('no_bdays_this_week') ?></h5>
                                    <svg class="icon-svg icon-svg-birthday" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400"><g data-name="Layer 2"><g data-name="Layer 5"><path class="cls-1" d="M189.57,279.38c7.64-7.09-11.38-42.37-32.68-66.18s-54.21-46.63-62.1-39.83a4.57,4.57,0,0,0-.8.94,2.36,2.36,0,0,0-.78.87q-44,82.92-88,165.84a6.07,6.07,0,0,0,7.53,8.41l174.48-68.9A5.14,5.14,0,0,0,189.57,279.38Z"/><path class="cls-2" d="M82.86,194.26a225,225,0,0,0,18,62.29,229.64,229.64,0,0,0,28.46,46.82l-14,5.9a181.19,181.19,0,0,1-28.24-44.08,177.46,177.46,0,0,1-14.51-51.92Z"/><path class="cls-2" d="M63.44,231.48c1.86,21.09,7.83,48.36,24.92,74.81q3.57,5.5,7.34,10.36l-14.62,5.53a109.72,109.72,0,0,1-9.26-12.29c-14.48-22.35-17.69-45.24-18.22-59.84Z"/><path class="cls-2" d="M43.05,269.56c.4,12.39,3.16,32.46,16.66,50.8a80.48,80.48,0,0,0,6.78,8.05l-13.83,5a55.25,55.25,0,0,1-9.27-10.19C33.45,309.08,33,294,33.17,287.4Z"/><path class="cls-2" d="M24.06,306.18a40.43,40.43,0,0,0,4.06,20.76,41.28,41.28,0,0,0,9,12l-12,5.55a24.57,24.57,0,0,1-8.68-10,23.68,23.68,0,0,1-2-13.46Q19.25,313.6,24.06,306.18Z"/><path class="cls-2" d="M103.84,209.42a312.07,312.07,0,0,0,22.08,52.64,315.12,315.12,0,0,0,20.68,33.61l12.12-4.24c-.57-.82-1.13-1.65-1.69-2.48a323.68,323.68,0,0,1-23.57-41.32c-3.75-3.7-7.62-7.85-11.5-12.47A184.51,184.51,0,0,1,103.84,209.42Z"/><path class="cls-3" d="M120.45,181.57c-11.72-7.68-21.76-11.57-25.66-8.2a4.57,4.57,0,0,0-.8.94,2.36,2.36,0,0,0-.78.87q-44,82.92-88,165.84a6.07,6.07,0,0,0,7.53,8.41l174.48-68.9a5.14,5.14,0,0,0,2.35-1.15c2.72-2.52,2.06-8.61-.87-16.49"/><path class="cls-3" d="M164.72,222.59c-2.54-3.26-5.17-6.42-7.83-9.39-3.07-3.44-6.38-6.85-9.83-10.15"/><path class="cls-3" d="M181,246.88Q180,245,178.78,243"/><path class="cls-3" d="M188.71,262.93c2.92,7.85,3.57,13.93.86,16.45-3.65,3.38-13.37.31-24.94-6.55"/><path class="cls-3" d="M150.52,263.25a177.15,177.15,0,0,1-24.47-22.46C103.86,216,87,180.11,94.79,173.37c3.94-3.4,14.12.59,26,8.41"/><path class="cls-4" d="M137.71,112.36a11.86,11.86,0,0,1-11.46,1.88,32.38,32.38,0,0,0-3.12-19.08,12.87,12.87,0,0,0,11.45-1.88,38.16,38.16,0,0,1,3.13,19.08Z"/><path class="cls-4" d="M168.07,195.58a11.82,11.82,0,0,0,11.61-.28A32.09,32.09,0,0,1,178,186a32.78,32.78,0,0,1,1.24-10,13,13,0,0,1-5.67,1.51,12.79,12.79,0,0,1-5.93-1.24,38.41,38.41,0,0,0,.46,19.33Z"/><circle class="cls-2" cx="34.99" cy="186.26" r="9.33"/><circle class="cls-5" cx="194.5" cy="360.51" r="7.44"/><circle class="cls-5" cx="265.33" cy="231.95" r="8.81"/><ellipse class="cls-2" cx="169.18" cy="234.56" rx="10.46" ry="9.92"/><circle class="cls-2" cx="244.36" cy="122.09" r="8.92"/><circle class="cls-5" cx="123.69" cy="116.03" r="9.82"/><path class="cls-6" d="M147.42,83.82c4.4.14,21.49,1.16,28.9,13.16a23.81,23.81,0,0,1,3.23,9.75c3,28.2-45,46.66-42.12,75.2,1,9.42,6.82,13.42,6.27,27.49a53.71,53.71,0,0,1-5.54,21.43c-.63,1.11-1.52,2.47-2.18,2.31-1.37-.33.36-6.74-2.29-8.87-1.22-1-3.77-1.36-9.73,1.72a50.39,50.39,0,0,0,4-14c2.08-15.76-5.12-21-4.86-30.61.56-20.83,35.76-37.54,63.51-65.8,1.93-2,6.26-6.49,7.15-13.16,1.4-10.4-6.5-19-7.72-20.31A31.39,31.39,0,0,0,170,73c-2.12,4-4.52,5.59-6.29,6.3-3.48,1.38-6,0-10.58,1.43A17.16,17.16,0,0,0,147.42,83.82Z"/><path class="cls-6" d="M226,91.36s18.6-1.24,16.88-22.13,48.64-27.18,48.64-27.18c-1.1,2.2-.93,3.51-.57,4.3.54,1.19,1.62,1.39,2.86,2.86a8.52,8.52,0,0,1,1.71,3.43,72.76,72.76,0,0,0-13.16,2c-9,2.24-22.31,5.54-26.41,15-2.72,6.25,1.18,9.28-1.05,16.31-2.61,8.2-10.76,13-17.74,15.93.26-1,.88-4.13-.86-6.3-.27-.34-.93-1.06-4-2.29A38.81,38.81,0,0,0,226,91.36Z"/><path class="cls-6" d="M186.76,214c8.4-26.17,13.57-32.74,16.88-32.32,2.3.29,3,3.87,8,6.86a22.84,22.84,0,0,0,12.63,2.85C236.82,191,250,180.7,249.7,171.94c-.16-4.51-3.81-6-2.84-9.88,1.38-5.5,10-7.58,12.28-8.14,1.94-.47,11.07-2.69,17.45,2.28,4.24,3.3,4.68,7.74,6.58,7.44s3.73-5.26,2.86-9.44c-.61-2.95-2.27-4.15-1.71-6s3-2.89,5.15-3.43a52.31,52.31,0,0,0-23.75-3.43c-13.45,1.25-31,8.44-32,18-.42,3.92,2.14,6.31.57,11.16-1.06,3.29-3.76,6.89-7.15,8.3-9.13,3.78-17.67-10.56-27.75-10-7,.38-15.84,8-25.18,36.62,3.72-1.29,5.58-.73,6.58,0,1.22.89,1.32,2.12,2.86,4.6A22.68,22.68,0,0,0,186.76,214Z"/><path class="cls-6" d="M159.52,254.71c21-7.94,39.58-10.68,44.45-4.25a51.72,51.72,0,0,1,5.62,9.77c2.13,5.06,1.94,7.24,4.06,9.11,3.1,2.7,7.43,1.55,11.85,1.24,9.68-.68,22.1,2.55,27.4,9.17,7.14,8.91,1.37,24,.37,26.53-3.25-2.37-5.27-2.42-6.52-2-1.91.66-2.46,2.55-5.38,4a11.28,11.28,0,0,1-3.4,1c6.55-14.79,5.29-21.34,2.55-24.53-7.54-8.74-30.65,2.9-37.68-6.8-3.76-5.18,1.23-10.73-2.84-15-7.58-8-33.43,2.27-33.43,2.27h0a7.19,7.19,0,0,1-2.83-3.69c-.48-1.63,0-2.6-.57-4C162.75,256.6,161.79,255.5,159.52,254.71Z"/><g class="cls-7"><path class="cls-8" d="M140.94,155.34c-1.82,4-9.1,21.81-2.34,30.92a1.66,1.66,0,0,0,.86.62,25.61,25.61,0,0,1-1-4.95c-2.07-20.28,21.58-35.47,34.37-52.68C161.35,139,149.87,147.38,140.94,155.34Z"/><path class="cls-8" d="M188.79,114.35s-5.65,4.53-13.06,10.6q-1.33,2.17-2.93,4.3c5-4.27,10-8.79,14.82-13.67C188,115.24,188.35,114.83,188.79,114.35Z"/></g><path class="cls-9" d="M255.9,69.61c4.1-9.43,17.41-12.73,26.41-15a72.76,72.76,0,0,1,13.16-2,8.52,8.52,0,0,0-1.71-3.43c-1.24-1.47-2.32-1.67-2.86-2.86-.36-.79-.53-2.1.57-4.3,0,0-1.3.17-3.47.53-11.2,4.17-29.58,11.3-32.63,14.2C250.94,61,250.5,62.4,252.08,72c1.55,9.4,3.33,11.11,3.29,11.81C256.3,78.2,253.48,75.17,255.9,69.61Z"/><path class="cls-9" d="M234.71,166.39h0a9.87,9.87,0,0,1-.46,4.12c-1.06,3.29-3.76,6.89-7.15,8.3-8,3.33-15.59-7.35-24.15-9.61a2.88,2.88,0,0,0-.58,1.58c0,6.15,12.12,13.88,20.9,17.57s18.27-9.31,22-13.7S242.06,169.9,234.71,166.39Z"/><path class="cls-9" d="M225.5,270.58c-4.42.31-8.75,1.46-11.85-1.24-2.12-1.87-1.93-4-4.06-9.11a51.9,51.9,0,0,0-2.83-5.55c.76,4.22-1.83,7.14-.86,13.9.41,2.86.91,6.36,3.36,8.24,4.21,3.25,11.33-.47,16.79-2.13,4.75-1.45,11.13-2.19,19.51-.41A42.15,42.15,0,0,0,225.5,270.58Z"/><path class="cls-4" d="M193.85,58.39a14.2,14.2,0,0,1-13.14-4.45,38.72,38.72,0,0,0,7.41-21.89,15.4,15.4,0,0,0,13.14,4.45,45.71,45.71,0,0,1-7.41,21.89Z"/><rect class="cls-10" width="300" height="400"/></g></g></svg>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="px-15">
                                <h6><?php echo lang('birthdays') ?></h6>
                                <h5 class="my-15 font-weight-normal d-flex align-items-center"><?php echo sizeof($LastweekDateOfBirth) ?> <?php echo lang('celebrates_this_week') ?>
                                    <img class="mis-10" src="<?php echo '//' . $_SERVER['HTTP_HOST'] . '/assets/img/tada.png'; ?>"/>
                                </h5>
                            </div>
                            <div class="bsapp-card-scroll">
                                <?php foreach ($LastweekDateOfBirth as $client) {
                                    $CheckUserApp = (new UserBoostappLogin())->findUserByClientIDCompanyNum($client->id, $CompanyNum);
                                    ?>
                                    <div class="d-flex justify-content-between align-items-center w-100 border-bottom border-light py-10 px-15">
                                        <div class="d-flex position-relative align-items-center">
                                            <a target="_blank" href="<?php echo "/office/ClientProfile.php?u=" . $client->id ?>">
                                            <?php if (!empty($CheckUserApp->__get('UploadImage'))) { ?>
                                                <img class="js-img-to-check bsapp-img-to-check  mie-8 w-40p h-40p rounded-circle border <?php echo ($client->BalanceAmount > 0) ? 'border-danger' : '' ?>"  src="<?= get_appboostapp_domain().'/camera/uploads/large/'. $CheckUserApp->__get('UploadImage') ?>" class="w-40p h-40p  rounded-circle mie-8" />
                                            <?php } else { ?>
                                                <img class="js-img-to-check bsapp-img-to-check  mie-8 w-40p h-40p rounded-circle border <?php echo ($client->BalanceAmount > 0) ? 'border-danger' : '' ?>" src="<?php echo 'https://ui-avatars.com/api/?length=1&name=' . $client->FirstName . '&background=f3f3f4&color=000&font-size=0.5'; ?>" class="w-40p h-40p  rounded-circle mie-8" />
                                            <?php } ?>
                                            </a>
                                            <div class="bsapp-absolute-circle h-20p w-20p bg-danger mis-20 bsapp-top-25p bsapp-fs-12" <?php if($client->BalanceAmount <= 0) echo 'style="visibility: hidden;"' ?>>
                                                <a data-toggle="tooltip" data-placement="top"
                                                   title="<?php echo lang('client_has_debt') ?>" class="text-white"><i
                                                            class="fal fa-shekel-sign"></i></a>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="mie-8"><a target="_blank" class="text-dark" href="<?php echo "/office/ClientProfile.php?u=" . $client->id ?>"> <u> <?php echo $client->FirstName . ' ' . $client->LastName ?></u></a>

                                                    <?php
                                                        //code for icons
                                                    $clientCRM = new Clientcrm();
                                                    $clientMed = new ClientMedical();

                                                    $ClassClientInfo = new Client($client->id);

                                                    if ($clientCRM->GetClientcrmByClientId($CompanyNum,$client->id))
                                                        echo '<a class="mie-5 js-client-crm-icon bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('note_exists_client') . '">
                                                    <i class="fal fa-clipboard text-warning">
                                                    </i></a>';

                                                    if ($clientMed->GetMdicalByClientId($companyNum,$client->id))
                                                        echo '<a class="mie-5 js-client-medical-icon bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('customer_card_medical_records') . '">
                                                    <i class="fal fa-notes-medical text-danger"></i></a>';

                                                    if (date('m-d', strtotime($ClassClientInfo->Dob)) == date('m-d', strtotime("now")))
                                                        echo '<a class="mie-5  bsapp-fs-18" data-toggle="tooltip" data-placement="top" title="' . lang('celebrate_birthday_today') . '">
                                                    <i class="fal fa-birthday-cake text-danger"></i></a>';


                                                    echo $ClassClientInfo->getGreenPassIcon();

                                                    ?>

                                                </span>
                                                <span class="text-muted"><b><?php echo explode('-', $client->Dob)[2] . '-' . explode('-', $client->Dob)[1] ?></b>
                                                    <?php
                                                    $CurrentYear = date("Y");
                                                    $date = new DateTime($client->Dob);
                                                    $Year = $date->format("Y");
                                                    if ($client->Gender == 2) {
                                                        echo lang("celebrate2") . " " . ($CurrentYear - $Year);
                                                    } else {
                                                        echo lang("celebrate1") . " " . ($CurrentYear - $Year);
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- slider section :: end  -->
        </div>
    </section>
<?php } ?>
</div>
<div class="modal px-0 px-sm-auto" tabindex="-1" role="dialog" id="js-confirmation-modal">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content">
            <div class="modal-body h-100">
                <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
                    <div class="d-flex justify-content-between w-100">
                        <h5><?php echo lang('complete_taks') ?></h5>
                        <a href="javascript:;"  class="text-dark" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="d-flex  flex-column text-center  my-50">
                        <div class="w-100 mb-10">
                            <label class="badge badge-light badge-pill px-30">
                                <h1 class="text-primary"><i class="fal fa-check"></i></h1>
                            </label>
                        </div>
                        <div class="w-100 "><?php echo lang('complete_taks_q') ?></div>
                    </div>
                    <div class="d-flex justify-content-around w-100">
                        <a  class="btn btn-primary flex-fill mie-15 confim-btn" href="javascript:;"><?php echo lang('yes') ?></a>
                        <a  class="btn btn-light flex-fill" href="javascript:;" data-dismiss="modal"><?php echo lang('no') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal px-0 px-sm-auto" tabindex="-1" role="dialog" id="js-confirmation-modal-2">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content">
            <div class="modal-body  h-100">
                <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
                    <div class="d-flex justify-content-between w-100">
                        <h5><?php echo lang('cancel_task') ?></h5>
                        <a href="javascript:;"  class="text-dark" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="d-flex  flex-column text-center  my-50">
                        <div class="w-100 mb-10">
                            <label class="badge badge-light badge-pill px-30">
                                <h1 class="text-danger"><i class="fal fa-trash-alt"></i></h1>
                            </label>
                        </div>
                        <div class="w-100 "><?php echo lang('cancel_task_q') ?></div>
                    </div>
                    <div class="d-flex justify-content-around w-100">
                        <a class="confim-btn btn btn-danger flex-fill mie-15" href="javascript:;"><?php echo lang('yes') ?></a>
                        <a class="btn btn-light flex-fill" href="javascript:;" data-dismiss="modal"><?php echo lang('no') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if(!empty($softPayment)) { ?>
    <div class="modal px-0" tabindex="-1" role="dialog" id="js-success-update-payment">
        <div class="modal-dialog modal-dialog-centered m-0 m-sm-auto">
            <div class="modal-content">
                <div class="modal-body  h-100">
                    <div class="">
                        <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-600p bsapp-fs-16">
                            <div class="d-flex justify-content-between">
                                <h5 class="text-start font-weight-bold text-gray-400"><?php echo lang('confirmation_of_payment_bit') ?></h5>
                                <a href="javascript:;"  class="text-dark" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                            </div>
                            <div class="d-flex flex-column text-center my-50">
                                <div class="w-100 mb-10">
                                    <label class="">
                                        <h1 class="text-success"><i class="fal fa-check-circle fa-2x"></i></h1>
                                    </label>
                                </div>
                                <div class="w-100">
                                    <h5 class="mb-15 font-weight-bold"><?php echo lang('transaction_completed_bit') ?></h5>
                                    <!--                                    <div class="text-gray-400">קבלה תישלח לכתובת המייל שלך.</div>-->
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <div class="w-100 p-20">
                                    <?php if(isset($softPayment->Amount)) { ?>
                                        <div class="d-flex justify-content-between mb-10 font-weight-bold">
                                            <div class=""><?= lang('table_total_to_pay') ?></div>
                                            <div class=""><?= number_format(147, 2) ?> <span><i class="fal fa-shekel-sign"></i></span>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="d-flex justify-content-between mb-10">
                                        <div class=""><?= lang('payments') ?></div>
                                        <div class="">1</div>
                                    </div>
                                    <?php if (isset($softPayment->ACode)) { ?>
                                        <div class="d-flex justify-content-between mb-10">
                                            <div class=""><?= lang('ref_number') ?></div>
                                            <div class=""><?= $softPayment->ACode ?></div>
                                        </div>
                                    <?php } ?>
                                    <!--                                    <div class="d-flex justify-content-between mb-10">-->
                                    <!--                                        <div class="">מספר הזמנה</div>-->
                                    <!--                                        <div class="">--><?//= 1234 ?><!--</div>-->
                                    <!--                                    </div>-->
                                </div>
                            </div>
                            <div class="d-flex justify-content-around w-100">
                                <a class="btn btn-light flex-fill p-12 font-weight-bold " data-dismiss="modal"><?php echo lang('approval') ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div class="modal px-0" tabindex="-1" role="dialog" id="js-error-update-res">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content">
            <div class="modal-body  h-100">
                <div class="">
                    <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p bsapp-fs-16">
                        <div class="d-flex justify-content-between">
                            <h5 class="text-start font-weight-bold text-gray-400"><?php echo lang('confirmation_of_payment_bit') ?></h5>
                            <a href="javascript:;"  class="text-dark" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                        </div>
                        <div class="d-flex flex-column text-center my-50">
                            <div class="w-100 mb-10">
                                <label class="">
                                    <h1 class="text-danger"><i class="fal fa-times-circle fa-2x"></i></h1>
                                </label>
                            </div>
                            <div class="w-100">
                                <h5 class="mb-15 font-weight-bold"><?= lang('billing_failed_notice') ?></h5>
                                <?php if(isset($_GET['err'])) { ?>
                                    <div class="text-gray-400"><?= $_GET['err'] ?></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-around w-100">
                            <a class="btn btn-light flex-fill p-12 font-weight-bold " data-dismiss="modal"><?= lang('close') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal px-0 px-sm-auto" tabindex="-1" role="dialog" id="js-action-modal">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content">
            <div class="modal-body  h-100">
                <div class="d-flex flex-column  h-100 ">
                    <div class="d-flex justify-content-between w-100">
                        <h5><?php echo lang('actions_menu') ?> </h5>
                        <a href="javascript:;"  class="text-dark  pie-9" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="">
                        <ul class="list-group list-group-flush px-0 my-20">
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="js-newClient">
                                <div class="bsapp-fs-20"><i class="fal fa-user pl-7"></i> <?php echo lang('new_client') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="new-client" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="new-client"></label>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="js-newLead">
                                <div class="bsapp-fs-20"><i class="fal fa-users-class pl-7"></i> <?php echo lang('a_new_lead') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="new-lead" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="new-lead"></label>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="js-applicationForm">
                                <div class="bsapp-fs-20"><i class="fal fa-file-user pl-7"></i> <?php echo lang('send_joining_form') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="application-form" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="application-form"></label>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="js-newTask">
                                <div class="bsapp-fs-20"><i class="fal fa-thumbtack pl-7"></i> <?php echo lang('new_task') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="tasks" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="tasks"></label>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 cursor-pointer" id="js-distributionList">
                                <div class="bsapp-fs-20"><i class="fal fa-paper-plane pl-7"></i> <?php echo lang('mailing_list') ?></div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" id="distribution-list" name="customRadio" class="custom-control-input">
                                    <label class="custom-control-label" for="distribution-list"></label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ip-modal" id="SendClientForm">
    <div class="ip-modal-dialog">
        <div class="ip-modal-content ">
            <div class="ip-modal-header d-flex justify-content-between">
                <h4 class="ip-modal-title"><?= lang('send_joining_form_to_new_client') ?></h4>
                <a class="ip-close" title="Close"  data-dismiss="modal">&times;</a>

                </div>
                <div class="ip-modal-body text-start" >
                    <form action="SendClientForm"  class="ajax-form clearfix" autocomplete="off">
                        <div class="form-group">
                            <a class="btn btn-light js-copy-link-to-clipboard">
                                <i class="fal fa-paste payment-icon" ></i>
                                <?= lang('copy_link') ?>
                            </a>
                        </div>
                    <div class="form-group">
                        <label><?= lang('branch') ?></label>
                        <select class="form-control " name="Brands" id="BrandsTypeClass">
                            <?php
                            $b = '1';
                            $ClassTypes = DB::table('brands')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('id', 'ASC')->get();
                            if (!empty($ClassTypes)) {
                                foreach ($ClassTypes as $ClassType) {
                                    ?>
                                    <option value="<?php echo $ClassType->id; ?>" <?php
                                    if ($b == '1') {
                                        echo 'selected';
                                    } else {

                                    }
                                    ?>><?php echo $ClassType->BrandName ?></option>
                                    <?php
                                    ++$b;
                                }
                            } else {
                                ?>
                                <option value="0"><?= lang('primary_branch') ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= lang('email_table') ?></label>
                        <input type="email" name="Email" class="form-control">
                    </div>

            </div>
            <div class="ip-modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-light ip-close" data-dismiss="modal"><?php echo lang('close') ?></button>
                <div class="ip-actions">
                    <button type="submit" name="submit" class="btn btn-primary text-white"><?= lang('send') ?></button>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>



<div class="ip-modal" id="SendClientPush">
    <div class="ip-modal-dialog BigDialog">
        <div class="ip-modal-content text-start">
            <div class="ip-modal-header d-flex justify-content-between">
                <h4 class="ip-modal-title"><?= lang('send_message_to_distribution_list') ?></h4>
                <a class="ip-close" title="Close" data-dismiss="modal">&times;</a>

            </div>

            <div class="ip-modal-body text-start">
                <form action="SendClientPush"  class="ajax-form clearfix" autocomplete="off">
                    <input type="hidden" name="me" value="1">
                    <input type="hidden" id="sms-limit" value="<?= $company->SMSLimit ?? '' ?>">
                    <?php if((!isset($_COOKIE['boostapp_lang'])) || (isset($_COOKIE['boostapp_lang']) && $_COOKIE['boostapp_lang'] == 'he')) { ?>
                    <div class="alertb alert-info bsapp-fs-14">
                        <strong><?= lang('option_to_use_params_inside_message') ?></strong><br>
                        <strong>[[<?= lang('full_name') ?>]]</strong> <?= lang('will_be_changed_in_client_full_name') ?><br>
                        <strong>[[<?= lang('first_name') ?>]]</strong> <?= lang('will_be_replaced_in_private_name') ?><br>
                        <strong>[[<?= lang('full_representative_name') ?>]]</strong> <?= lang('will_be_replaced_in_representative_fullname') ?><br>
                        <strong>[[<?= lang('representative_name') ?>]]</strong> <?= lang('will_be_replaced_in_representative_firstname') ?><br>
                        <strong>[[<?= lang('studio_name') ?>]]</strong> <?= lang('will_be_replaced_in_studio_name') ?>
                    </div>
                    <?php } ?>
                    <div class="form-group" >
                        <label><?= lang('sending_option') ?></label>
                        <select class="form-control" name="Type" id="sendPlatform">
                            <option value="0" selected><?= lang('free_push_message') ?></option>
                            <option value="1"><?= lang('sms_message_pay') ?></option>
                            <option value="2"><?= lang('email_free') ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= lang('clients') ?></label>
                        <select class="form-control" name="TypeSend">
                            <option value="0" selected><?= lang('active_clients_only') ?></option>
                            <option value="1"><?= lang('active_clients_plus_interested') ?></option>
                            <option value="3"><?= lang('archived_clients_only') ?></option>
                            <option value="4"><?= lang('interested_only') ?></option>
                            <option value="2"><?= lang('all_clients_archived_included') ?></option>
                        </select>
                    </div>

                    <div class="form-group" id="subjectDiv" style="display: none">
                        <label><?= lang('type_subject') ?></label>
                        <input type="text" id="subject" name="Subject" placeholder="<?= lang('subject') ?>" class="form-control">
                    </div>


                    <div class="form-group" style="padding-top:10px;">

                        <div class="alert alert-warning" role="alert" style="display: block">
                            <?php echo lang('emoji_add_notice') ?>
                        </div>

                        <label id="letterCntLbl"><?= lang('message_content_hundred_chars_limit') ?><br><span dir="rtl" style="font-size: 12px;">(<span id="count"><?= lang('zero_chars_zero_messages') ?></span>)</span></label>

                        <textarea class="form-control summernote" name="emailContent" style="display: none"></textarea>

                        <textarea name="Content" id="Message" placeholder="<?= lang('type_message_content') ?>" class="form-control" rows="7" required></textarea>

                    </div>



            </div>
            <div class="ip-modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-light ip-close" data-dismiss="modal"><?php echo lang('close') ?></button>
                <div class="ip-actions">
                    <?php if ($CompanyNum != '100') { ?>
                        <button type="submit" name="submit" class="btn btn-primary text-white"><?= lang('send') ?></button>
                    <?php } else { ?>
                        <button type="button" name="submit" class="btn btn-primary text-white"><?= lang('send') ?></button>
                    <?php } ?>
                </div>

                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal" role="dialog" id="AddNewTask" >
    <div class="ip-modal-dialog BigDialog" >
        <div class="ip-modal-content">
            <div class="ip-modal-header d-flex justify-content-between ">
                <h4 class="ip-modal-title"><?php echo lang('task_window_title') ?></h4>
                <a class="ip-close" title="Close" data-dismiss="modal" aria-label="Close">&times;</a>

            </div>
            <div class="ip-modal-body text-start">

                <form action="AddCalendarClient" id="FormCalendarClient"  class="ajax-form clearfix dashboardTasks" autocomplete="off">


                    <div class="row">
                        <div class="col-md-12 col-sm-12 order-1">
                            <input type="hidden" id="CalPage" value="0">
                            <input type="hidden" name="ClientId" id="AddEditTaskClientId" value="0">
                            <input type="hidden" name="PipeLineId" id="AddEditTaskPipeLineId" value="0">
                            <input type="hidden" name="CalendarId" id="AddEditTaskCalendarId" value="">

                            <div class="form-group" id="ClientNameDiv" style="display: none;">
                                <label><?= lang('client') ?></label>
                                <div class="row">
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="ClientName" disabled>
                                    </div>
                                    <div class="col-sm-5">
                                        <span class="input-group-text" id="ClientPhone"></span>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group" id="ClientDiv">
                                <label><?php echo lang('client') ?></label>
                                <select class="form-control select2ClientDesk" id="ChooseClientForTask" name="ClientForTask" data-placeholder="<?php echo lang('choose_client') ?>" style="width: 100%">
                                    <option value="0"  selected><?php echo lang('without_customer_affiliation_cal') ?></option>

                                </select>
                            </div>



                            <div class="form-group" style="display: none;">
                                <label><?php echo lang('meeting_room_cal') ?></label>
                                <select class="form-control js-example-basic-single select2" id="ChooseFloorForTask" name="FloorId" data-placeholder="<?php echo lang('select_meeting_room_cal') ?>" style="width: 100%" onChange="UpdateCalView(this.value)">
                                    <option value="0"  selected><?php echo lang('no_conference_room') ?></option>
                                    <?php
                                    $SectionInfos = DB::table('sections')->where('CompanyNum', $CompanyNum)->where('Status', '=', '0')->orderBy('Floor', 'ASC')->get();
                                    foreach ($SectionInfos as $SectionInfo) {
                                        ?>
                                        <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->Title; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?php echo lang('task_title') ?></label>
                                <input type="text" class="form-control" name="TaskTitle" id="CalTaskTitle">
                            </div>

                            <div class="form-group">
                                <label><?php echo lang('task_type') ?></label>
                                <select class="form-control " name="TypeOption" id="CalTypeOption">
                                    <?php foreach (CalType::getAllActiveByCompanyNum($CompanyNum) as $CalType) { ?>
                                        <option value="<?php echo $CalType->id; ?>"><?php echo $CalType->Type; ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <div class="form-row">
                                    <?php

                                    function blockMinutesRound($hour, $minutes = '5', $format = "H:i") {
                                        $seconds = strtotime($hour);
                                        $rounded = round($seconds / ($minutes * 60)) * ($minutes * 60);
                                        return date($format, $rounded);
                                    }
                                    ?>
                                    <div class="col">
                                        <label><?php echo lang('date') ?></label>
                                        <input name="SetDate" id="SetDate" type="date"  value="<?php echo date('Y-m-d') ?>" class="form-control" placeholder="<?php echo lang('set_reminder_cal') ?>">
                                    </div>
                                    <div class="col">
                                        <label><?php echo lang('start_hour') ?></label>
                                        <input name="SetTime" id="SetTime" type="time" step="300" value="<?php echo blockMinutesRound(date('H:i')); ?>" class="form-control" placeholder="<?php echo lang('set_reminder_cal') ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="form-row">
                                    <div class="col">
                                        <label><?php echo lang('end_hour') ?></label>
                                        <input name="SetToTime" id="SetToTime" type="time" step="300" min="<?php echo blockMinutesRound(date(('H:i'), strtotime("+5 minutes"))); ?>" value="<?php echo blockMinutesRound(date(('H:i'), strtotime("+30 minutes"))); ?>" class="form-control" placeholder="<?php echo lang('set_reminder_cal') ?>">
                                    </div>
                                    <div class="col">
                                        <label><?php echo lang('priority') ?></label>
                                        <select class="form-control " name="Level" id="CalLevel" >
                                            <option value="0"><?php echo lang('low_priority_cal') ?></option>
                                            <option value="1"><?php echo lang('medium_priority_cal') ?></option>
                                            <option value="2"><?php echo lang('high_priority_cal') ?></option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group"></div>

                            <div class="form-row">
                                <div class="col">
                                    <label><?php echo lang('taking_care_representative') ?></label>
                                    <select class="form-control js-example-basic-single select2" id="ChooseAgentForTask" name="AgentId" data-placeholder="<?php echo lang('choose_taking_care_representative') ?>" style="width: 100%">
                                        <?php
                                        if ($BrandsMain == '0') {
                                            $UserInfos = DB::table('users')->where('ActiveStatus', '=', '0')->where('CompanyNum', $CompanyNum)->orderBy('display_name', 'ASC')->get();
                                        } else {
                                            $UserInfos = DB::table('users')->where('ActiveStatus', '=', '0')->where('BrandsMain', $BrandsMain)->orderBy('display_name', 'ASC')->get();
                                        }
                                        foreach ($UserInfos as $UserInfo) {
                                            ?>
                                            <option value="<?php echo $UserInfo->id; ?>"  ><?php echo $UserInfo->display_name; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="col">
                                    <label><?php echo lang('task_auth_group') ?></label>

                                    <select class="form-control js-example-basic-single select2multipleDesk " name="SendStudioOption[]" id="SendStudioOption" multiple="multiple" data-select2order="true" style="width: 100%;">
                                        <?php
                                        $SectionInfos = DB::table('roles')->where('CompanyNum', '=', $CompanyNum)->get();
                                        foreach ($SectionInfos as $SectionInfo) {
                                            ?>
                                            <option value="<?php echo $SectionInfo->id; ?>"  ><?php echo $SectionInfo->name; ?></option>
                                        <?php } ?>

                                    </select>



                                </div>

                            </div>

                            <div class="form-group"></div>
                            <div class="form-group">
                                <label><?= lang('contet_single') ?></label>
                                <textarea name="Remarks" id="CalRemarks" class="form-control" rows="3" ></textarea>
                            </div>


                            <input type="hidden" name="SendMail" value="0">

                        </div>




                    </div>







            </div>
            <div class="ip-modal-footer d-flex justify-content-between">
                <a  class="btn btn-light ip-close" data-dismiss="modal"><?php echo lang('close') ?></a>
                <div class="ip-actions d-flex flex-row">
                    <div class="pie-20 w-200p ">
                        <select name="TaskStatus" id="CalTaskStatus" class="form-control" disabled>
                            <option value="0"><?= lang('open_task') ?></option>
                            <option value="1"><?= lang('completed_task') ?></option>
                            <option value="2"><?= lang('canceled_task') ?></option>
                        </select>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('save_changes_button') ?></button>
                </div>
                </form>


            </div>
        </div>
    </div>
</div>

<div class="modal text-start"  role="dialog" id="ViewDeskInfo" data-backdrop="static" data-keyboard="false" aria-hidden="true">
    <div class="ip-modal-dialog BigDialog">
        <div class="ip-modal-content">
            <div class="ip-modal-header d-flex justify-content-between ">
                <h4 class="ip-modal-title"><?php echo lang('set_trainees') ?></h4>
                <a class="ip-close" title="Close" style="" data-dismiss="modal" aria-label="Close" id="ClosePOPUP">&times;</a>
            </div>
            <div class="ip-modal-body">
                <div id="DivViewDeskInfo"></div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="tokenFirebase" name="tokenFirebase" value="">

</div>


<div><!-- start tag for the two closed divs -->
    <div><!-- start tag for the two closed divs -->
        <?php
        require_once '../app/views/footernew.php';
        ?>

<!--        <script src="https://momentjs.com/downloads/moment.js"></script>-->
        <script src='./assets/js/Dashboard.js?<?php echo filemtime('assets/js/Dashboard.js') ?>' type="text/javascript" charset="utf-8"></script>
<!--        <script src="js/trainee/trainee.js?--><?php //echo filemtime('js/trainee/trainee.js') ?><!--"></script>-->
<!--        <script src="js/createClass/createClass.js?--><?php //echo filemtime('js/createClass/createClass.js') ?><!--"></script>-->

        <?php if(isset($paymentAction) && $paymentAction == "PaymentSuccess" && $company->lockStatus == 0) { ?>
            <script src="lockCompany/js/paymentSuccessModal.js?<?php echo filemtime('lockCompany/js/paymentSuccessModal.js') ?>"></script>
        <?php } else if(isset($paymentAction) && $paymentAction == "PaymentError" && $company->lockStatus == 1) { ?>
            <script src="lockCompany/js/paymentErrorModal.js?<?php echo filemtime('lockCompany/js/paymentErrorModal.js') ?>"></script>
        <?php } ?>
        <?php
        $line_chart_1 = max($CounterOfLastThirtyDaysActive);
        $line_chart_max_1 = 2.5 * $line_chart_1;
        $line_chart_2 = max($CounterOfLastThirtyDaysCheck);
        $line_chart_max_2 = 2.5 * $line_chart_2;
        ?>
        <script type="text/javascript">
            
            $(document).ready(function () {

                setTimeout(GetToken, 0);

                // SetTime change handler
                $('#SetTime').on('change', function () {
                    const SetTime = $('#SetTime').val();
                    const FixToTime = moment(SetTime, 'HH:mm').add(30, 'minutes').format('HH:mm');
                    const FixToTimes = moment(SetTime, 'HH:mm').add(5, 'minutes').format('HH:mm');
                    $('#SetToTime').val(FixToTime);
                    $('#SetToTime').prop('min', FixToTimes);
                });

                $('.js-new-task-modal').on('click', function() {
                    $('#AddNewTask').modal('show');
                });

                // dashboard :: begin

                function initCharts() {

                    var style = getComputedStyle(document.body);
                    var jsCssPrimaryColor = style.getPropertyValue('--primary');
                    var jsCssWhiteColor = style.getPropertyValue('--white');
                    var jsCssDarkColor = style.getPropertyValue('--dark');
                    var jsCssRedColor = style.getPropertyValue('--red');
                    var jsCssBlueColor = style.getPropertyValue('--blue');
                    var jsCssGray400Color = style.getPropertyValue('--gray-400');

                    var jsCssPrimaryDarkColor = '#03a52f';
                    var jsCssWhiteDarkColor = '#f1f1f1';
                    var jsCssDarkDarkerColor = '#07111f';

                    let arrow1 = "<?php echo $ClientsCounter['clientsActiveCounter'] >= $LastActiveClient ? true : false ?>";
                    var dynamicColor1 = (arrow1) ? jsCssPrimaryColor : jsCssRedColor;

                    let arrow2 = "<?php echo $ClientsCounter['clientCheckCounter'] >= $LastCheckClient ? true : false ?>";
                    var dynamicColor2 = (arrow2) ? jsCssRedColor : jsCssPrimaryColor;

                    var ctx_line_1 = $("#js-line-chart-1")

                    var myLineChart = new Chart(ctx_line_1, {
                        type: 'line',
                        data: {
                            labels: ['', '', '', '', '', ''],
                            datasets: [{
                                label: '',
                                data: [<?php echo implode(",", $CounterOfLastThirtyDaysActive) ?>],
                                backgroundColor: [
                                    '#fff',
                                ],
                                borderColor: [
                                    dynamicColor1,
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        display: false,
                                        max: <?php echo $line_chart_max_1; ?>
                                    },
                                    gridLines: {
                                        display: false,
                                    },
                                }],
                                xAxes: [{
                                    ticks: {
                                        display: false //this will remove only the label,
                                    },
                                    gridLines: {
                                        display: false,
                                    },
                                }],
                            },
                            elements: {
                                point: {
                                    radius: 0
                                }
                            },
                            legend: {
                                display: false
                            }
                        }
                    });

                    var ctx_line_2 = $("#js-line-chart-2")

                    var myLineChart = new Chart(ctx_line_2, {
                        type: 'line',
                        data: {
                            labels: ['', '', '', '', '', ''],
                            datasets: [{
                                label: '',
                                data: [<?php echo implode(",", $CounterOfLastThirtyDaysCheck) ?>],
                                backgroundColor: [
                                    '#fff',
                                ],
                                borderColor: [
                                    dynamicColor2

                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        display: false,
                                        max: <?php echo $line_chart_max_2; ?>
                                    },
                                    gridLines: {
                                        display: false,
                                    },
                                }],
                                xAxes: [{
                                    ticks: {
                                        display: false //this will remove only the label
                                    },
                                    gridLines: {
                                        display: false,
                                    },
                                }],
                            },
                            elements: {
                                point: {
                                    radius: 0
                                }
                            },
                            legend: {
                                display: false
                            }

                        }
                    });
                    var data = {
                        labels: ['<?php echo $LeadsAtLeatThirtyDays['AllLeads'] . ' ' . lang('new2') ?>', '<?php echo $LeadsAtLeatThirtyDays['ConvertLeads'] . ' ' . lang('converted') ?>'],
                        datasets: [{
                            label: '',
                            data: [<?php echo $LeadsAtLeatThirtyDays['percentOpenLeads'] ?>, <?php echo $LeadsAtLeatThirtyDays['PercentConvertLeads'] ?>],
                            backgroundColor: [
                                jsCssDarkColor,
                                jsCssPrimaryColor,
                            ],
                            hoverBackgroundColor: [
                                jsCssDarkDarkerColor,
                                jsCssPrimaryDarkColor
                            ],
                            borderColor: [
                                jsCssDarkColor,
                                jsCssPrimaryColor
                            ],
                            borderWidth: 0
                        }]
                    };
                    var precent = '<?php echo ceil($LeadsAtLeatThirtyDays['PercentConvertLeads']) ?>%';
                    var ctx = $("#js-doughnut-chart");
                    var myDoughnutChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: data,
                        options: {

                            responsive: false,
                            legend: {
                                position: 'bottom',
                                padding: 20,
                                labels: {
                                    padding: 10
                                }
                            },
                            elements: {
                                center: {
                                    text: precent,
                                    color: jsCssGray400Color, // Default is #000000
                                    fontStyle: 'Arial', // Default is Arial
                                    sidePadding: 15, // Default is 20 (as a percentage)
                                    minFontSize: 14, // Default is 20 (in px), set to false and text will not wrap.
                                    maxFontSize: 16, // Default is 20 (in px), set to false and text will not wrap.
                                    lineHeight: 18 // Default is 25 (in px), used for when text wraps
                                }
                            }
                        }
                    });

                    var ctx_bar = $("#js-bar-chart");
                    var myChart = new Chart(ctx_bar, {
                        type: 'bar',
                        data: {
                            labels: ['<?php echo empty($LastMonthDeals) ? 0 : $LastMonthDeals ?>', '<?php echo empty($CurrentMonthDeals) ? 0 : $CurrentMonthDeals ?>'],

                            datasets: [{
                                label: '',
                                data: ['<?php echo empty($LastMonthDeals) ? 0 : $LastMonthDeals ?>', '<?php echo empty($CurrentMonthDeals) ? 0 : $CurrentMonthDeals ?>'],

                                backgroundColor: [
                                    jsCssWhiteColor,
                                    jsCssPrimaryColor
                                ],
                                hoverBackgroundColor: [
                                    jsCssWhiteDarkColor,
                                    jsCssPrimaryDarkColor
                                ],
                                borderColor: [
                                    jsCssWhiteColor,
                                    jsCssPrimaryColor,
                                ],
                                borderWidth: 1,

                            }]
                        },
                        options: {
                            responsive: false,
                            tooltips: {
                                "enabled": false
                            },
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true,
                                        display: false,
                                    },
                                    gridLines: {
                                        display: false,
                                    },
                                }],
                                xAxes: [{
                                    barPercentage: 1.5,
                                    categoryPercentage: 0.4,

                                    ticks: {
                                        display: true, //this will remove only the label
                                        fontColor: jsCssWhiteColor,
                                        fontSize: 12
                                    },
                                    gridLines: {
                                        display: false,
                                    },
                                }],
                            },
                            legend: {
                                display: false,
                            }
                        },

                    });

                }

                initCharts();


                var mySwiper = new Swiper('.js-swiper', {
                    speed: 400,
                    spaceBetween: 30,
                    freeMode: false,
                    centerInsufficientSlides: true,

                    breakpoints: {
                        // when window width is >= 480px
                        992: {
                            slidesPerView: 4,
                            spaceBetween: 30,
                            allowTouchMove: false
                        },
                        436: {
                            slidesPerView: 1.5,
                            allowTouchMove: true,
                            centerInsufficientSlides: true
                        },
                        320: {
                            slidesPerView: 1.15,
                            allowTouchMove: true,
                            centerInsufficientSlides: true
                        },
                        0: {
                            slidesPerView: 1,
                            allowTouchMove: true

                        }
                    }
                });
                // dashboard :: end
            })

            function BarClick(type) {

                if (type == "prev") {
                    //first day of previous month
                    var startDate = new Date('<?php echo (string)date("Y-m-d", strtotime("first day of previous  month")) ?>')
                    var endDate = new Date('<?php echo (string)date("Y-m-d", strtotime("Last day of previous  month")) ?>')
                    startDate = startDate.getFullYear() + "-" + (startDate.getMonth() + 1) + "-" + startDate.getDate();
                    endDate = endDate.getFullYear() + "-" + (endDate.getMonth() + 1) + "-" + endDate.getDate();
                } else if (type == "cur") {
                    //first day of current month
                    var startDate = new Date('<?php echo (string)date("Y-m-d", strtotime("first day of this  month")) ?>')
                    var endDate = new Date('<?php echo (string)date("Y-m-d", strtotime("Last day of this  month")) ?>')
                    startDate = startDate.getFullYear() + "-" + (startDate.getMonth() + 1) + "-" + startDate.getDate();
                    endDate = endDate.getFullYear() + "-" + (endDate.getMonth() + 1) + "-" + endDate.getDate();

                }
                location.replace("/office/Reports/receipts.php?startDate=" + startDate + "&endDate=" + endDate)
            }


            Chart.pluginService.register({
                beforeDraw: function (chart) {
                    if (chart.config.options.elements.center) {
                        // Get ctx from string
                        var ctx = chart.chart.ctx;

                        // Get options from the center object in options
                        var centerConfig = chart.config.options.elements.center;
                        var fontStyle = centerConfig.fontStyle || 'Arial';
                        var txt = centerConfig.text;
                        var color = centerConfig.color || '#000';
                        var maxFontSize = centerConfig.maxFontSize || 75;
                        var sidePadding = centerConfig.sidePadding || 20;
                        var sidePaddingCalculated = (sidePadding / 100) * (chart.innerRadius * 2)
                        // Start with a base font of 30px
                        ctx.font = "30px " + fontStyle;

                        // Get the width of the string and also the width of the element minus 10 to give it 5px side padding
                        var stringWidth = ctx.measureText(txt).width;
                        var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

                        // Find out how much the font can grow in width.
                        var widthRatio = elementWidth / stringWidth;
                        var newFontSize = Math.floor(30 * widthRatio);
                        var elementHeight = (chart.innerRadius * 2);

                        // Pick a new font size so it will not be larger than the height of label.
                        var fontSizeToUse = Math.min(newFontSize, elementHeight, maxFontSize);
                        var minFontSize = centerConfig.minFontSize;
                        var lineHeight = centerConfig.lineHeight || 25;
                        var wrapText = false;

                        if (minFontSize === undefined) {
                            minFontSize = 20;
                        }

                        if (minFontSize && fontSizeToUse < minFontSize) {
                            fontSizeToUse = minFontSize;
                            wrapText = true;
                        }

                        // Set font settings to draw it correctly.
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                        var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
                        ctx.font = fontSizeToUse + "px " + fontStyle;
                        ctx.fillStyle = color;

                        if (!wrapText) {
                            ctx.fillText(txt, centerX, centerY);
                            return;
                        }

                        var words = txt.split(' ');
                        var line = '';
                        var lines = [];

                        // Break words up into multiple lines if necessary
                        for (var n = 0; n < words.length; n++) {
                            var testLine = line + words[n] + ' ';
                            var metrics = ctx.measureText(testLine);
                            var testWidth = metrics.width;
                            if (testWidth > elementWidth && n > 0) {
                                lines.push(line);
                                line = words[n] + ' ';
                            } else {
                                line = testLine;
                            }
                        }

                        // Move the center up depending on line height and number of lines
                        centerY -= (lines.length / 2) * lineHeight;

                                                    for (var n = 0; n < lines.length; n++) {
                                                        ctx.fillText(lines[n], centerX, centerY);
                                                        centerY += lineHeight;
                                                    }
                                                    //Draw text in center
                                                    ctx.fillText(line, centerX, centerY);
                                                }
                                            }
                                        });

                                        $('.js-copy-link-to-clipboard').on("click",function (e) {
                                            $(this).append(' <i class="fas fa-spinner-third fast-spin">');
                                            var e = document.getElementById("BrandsTypeClass");
                                            const brandSelected = e.options[e.selectedIndex].value;

                                            $.ajax({
                                                url: "/office/ajax/ajaxCopyClipboard.php",
                                                type: "POST",
                                                data: {"Brands": brandSelected},
                                                success: function (response) {

                                                    document.addEventListener('copy', function(e) {
                                                        e.clipboardData.setData('text/plain', response);
                                                        e.preventDefault();
                                                    }, true);

                                                    $(".fa-spinner-third.fast-spin").remove();
                                                    document.execCommand('copy');

                                                    if (!$('.js-copy-link-to-clipboard').hasClass('js-after-copy-link')) {
                                                        $('.js-copy-link-to-clipboard').toggleClass('js-after-copy-link');
                                                        $(".js-after-copy-link i").removeClass('fa-paste').addClass('fa-check text-success');
                                                        setTimeout(function(){
                                                            $(".js-after-copy-link i").removeClass('fa-check text-success').addClass('fa-paste');
                                                            $(".js-after-copy-link").toggleClass('js-after-copy-link');
                                                        }, 2000);
                                                    }
                                                },
                                                error: function (jqXHR, textStatus, errorThrown) {
                                                    $(".fa-spinner-third.fast-spin").remove();
                                                    console.log(textStatus, errorThrown);
                                                }
                                            });
                                        });

            function GetToken() {
                let userAgent = navigator.userAgent || navigator.vendor || window.opera;

                // Windows Phone must come first because its UA also contains 'Android'
                if (/windows phone/i.test(userAgent)) {}

                if (/android/i.test(userAgent)) {
                    let AnToken = '';
                    try {
                        AnToken = app.getFirebaseToken();
                    } catch (error) {
                        AnToken = android.getFirebaseToken();
                    }
                    SendToken(AnToken);
                }

                // iOS detection from: http://stackoverflow.com/a/9039885/177710
                if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
                    let token = document.getElementById('tokenFirebase').value;
                    SendToken(token);
                }
            }

            function SendToken(tokenFirebase) {
                $.ajax({
                    url:'ajax/updateFirebaseToken.php?tokenFirebase='+tokenFirebase,
                    dataType : 'json',
                    success  : function (response) {}
                });
            }
            </script>
        <?php } ?>
