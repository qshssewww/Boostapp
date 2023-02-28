<?php
ini_set("max_execution_time", 0);
require_once __DIR__ . '/../app/init.php';
require_once __DIR__ . '/Classes/Item.php';
require_once __DIR__ . '/Classes/Client.php';
require_once __DIR__ . '/Classes/ClientRegistrationFees.php';
require_once __DIR__ . '/Classes/Brand.php';
require_once __DIR__ . '/Classes/ItemDetails.php';
require_once __DIR__ . '/Classes/MeshulamPayments.php';
require_once __DIR__ . '/Classes/Rank.php';
require_once __DIR__ . '/Classes/Token.php';
require_once __DIR__ . '/Classes/Users.php';
require_once __DIR__ . '/Classes/OrderLogin.php';
require_once __DIR__ . '/services/PaymentService.php';
require_once __DIR__ . '/Classes/Utils.php';
require_once __DIR__ . '/Classes/OrderItems.php';
require_once __DIR__ . '/Classes/PayToken.php';
require_once __DIR__ . '/Classes/Pipeline.php';
require_once __DIR__ . '/Classes/Uploads.php';
require_once __DIR__ . '/Classes/ClassStudioAct.php';
require_once __DIR__ . '/Classes/ClassStudioDate.php';
require_once __DIR__ . '/Classes/WhatsAppNotifications.php';
require_once __DIR__ . '/services/WhatsAppService.php';
require_once __DIR__ . '/Classes/ClientActivities.php';
require_once __DIR__ . '/Classes/DocsTable.php';
require_once __DIR__ . '/services/payment/PaymentTypeEnum.php';
require_once __DIR__ . '/services/ClientService.php';

if (Auth::check()):
    if (Auth::userCan('118') || (Auth::userCan('47'))):
        $CompanyNum = Auth::user()->CompanyNum;
        $SettingsInfo = Company::getInstance();
        $TypeShva = $SettingsInfo->TypeShva;
        $MeshulamAPI = $SettingsInfo->MeshulamAPI;
        $MeshulamUserId = $SettingsInfo->MeshulamUserId;
        $GooglePlayLink = 'https://play.google.com/store/apps/details?id=com.connect_computer.boostnew&gl=IL';
        $AppStoreLink = 'https://apps.apple.com/us/app/boost-%D7%91%D7%95%D7%A1%D7%98/id1479519489';
        if (!empty($SettingsInfo->GooglePlayLink)) {
            $GooglePlayLink = $SettingsInfo->GooglePlayLink;
        }
        if (!empty($SettingsInfo->AppStoreLink)) {
            $AppStoreLink = $SettingsInfo->AppStoreLink;
        }
        $AppStore = '<a href="' . $AppStoreLink . '">App Store</a>';
        $GooglePlay = '<a href="' . $GooglePlayLink . '">Google Play</a>';
        /* @var $Supplier Client */
        $Supplier = Client::where('id', $_GET['u'])->where('CompanyNum', $CompanyNum)->first();

        $parent = null;
        if ($Supplier && $Supplier->parentClientId) {
            $parent = Client::where('id', $Supplier->parentClientId)->where('CompanyNum', $CompanyNum)->first();
        }


        //Checking if there are permissions to see users who are leads only
        if (!Auth::userCan('118') && $Supplier && $Supplier->Status != 2 ) {
            redirect_to('../index.php');
        }

        //Auth have permissions to edit leads
        $editLeadsPermission = (Auth::userCan('158') && $Supplier && $Supplier->Status == 2);

        $c_name = $Supplier->CompanyName ?? '';
        $pageTitle = lang('customer_card_my_profile_app'). ' :: ' . $c_name;
        require_once '../app/views/headernew.php';

        if (empty($_GET['u']) || !is_object($Supplier)) {
            ErrorPage(
                lang('error_oops_something_went_wrong')
                , lang('customer_not_in_system')
            );
        } else {

            $trueBalanceAmount = ClientActivities::getBalanceAmount($CompanyNum, $Supplier->id);

            if ($trueBalanceAmount != $Supplier->BalanceAmount) {
                $Supplier->BalanceAmount = $trueBalanceAmount;
                Client::where('id', $Supplier->id)->update(['BalanceAmount' => $trueBalanceAmount]);
            }

            $datetime = date('Y-m-d');
            $ClientId = $Supplier->id;
            $isRandomClient = $Supplier->isRandomClient == 1;
            $PipeNow = DB::table('pipeline')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $Supplier->id)->first();

            CreateLogMovement(
                lang('customer_card_log').' <a href="ClientProfile.php?u=' .$ClientId. '">'.htmlentities($Supplier->CompanyName).'</a>',
                $ClientId
            );
            $time = date('Y-m-d H:i:s');
            DB::table('client')
                ->where('id', $Supplier->id)
                ->where('CompanyNum', '=', $CompanyNum)
                ->update(array('ChangeDate' => $time));
///הצלחה
///
///
/// +
            $GetSuccessInfo = DB::table('leadstatus')->where('CompanyNum', '=', $CompanyNum)->where('PipeId', '=', @$PipeNow->MainPipeId)->where('Act', '=', 1)->first();
/// כשלון
            $GetFailsInfo = DB::table('leadstatus')->where('CompanyNum', '=', $CompanyNum)->where('PipeId', '=', @$PipeNow->MainPipeId)->where('Act', '=', 2)->first();
            $GetSuccess = @$GetSuccessInfo->id;
            $GetFails = @$GetFailsInfo->id;
            $GetStudioStatus = DB::table('boostapplogin.studio')->where('ClientId', '=', $Supplier->id)->where('CompanyNum', '=', $CompanyNum)->first();
            $CheckUserApp = DB::table('boostapplogin.users')->where('id', '=', @$GetStudioStatus->UserId)->first();
            $PipeId = @$PipeNow->id;

//// minor client section
            if ($Supplier->parentClientId != 0) {    /// minor client
                $getRelatives = new Client($ClientId);
                $getRelatives = $getRelatives->getRelatives();
            } else {    //check if he is a parent
                $getRelatives = new Client($ClientId);
                $getRelatives = $getRelatives->getChilds();
            }
            $relArr = array(lang('father_array'), lang('mother_array'), lang('brother_sister_array'), lang('relative_array'), lang('another_relationship_array'));
            $minorArr = array(lang('boy'), lang('girl'), lang('brother_or_sister'), lang('relative'), lang('another_relation'));

            $GroupNumber = rand(1,9999999);
            $GroupNumberRefound = rand(1,9999999);
            $appClient = StudioBoostappLogin::findByClientIdAndCompanyNum($Supplier->id, $CompanyNum);

            $isPayedFor = false;
            $payTokens = (new PayToken())->getPayTokenByClient($CompanyNum, $ClientId);
            foreach($payTokens as $payToken) {
                $pT = Token::find($payToken->TokenId);
                if ($pT && $pT->ClientId != $payToken->ClientId) {
                    $isPayedFor = true;
                    break;
                }
            }
            if ($Supplier && $Supplier->Brands != 0) {
                /* @var $ClientBrands Brand */
                $ClientBrands = Brand::find($Supplier->Brands);
                $Supplier->BrandName = $ClientBrands->BrandName ?? lang('primary_branch');
            } else {
                $Supplier->BrandName = lang('primary_branch');
            }

?>
<link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js">
</script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js">
</script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js">
</script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js">
</script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js">
</script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js">
</script>
<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js">
</script>
<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js">
</script>
<script src="../assets/office/js/list.js">
</script>
<link href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css" rel="stylesheet" ></link>
<link href="assets/css/fixstyle.css?<?php echo filemtime('assets/css/fixstyle.css') ?>" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.1/jquery.validate.min.js"></script>
<script src="/office/js/updatedJs/clientProfile.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<link href="assets/css/multi-select2-bsapp.css?<?php echo filemtime('assets/css/multi-select2-bsapp.css') ?>" rel="stylesheet">
<link href="/office/calendarPopups/assets/css/popup-updated.css" rel="stylesheet">
<?php
if (@$Supplier->Dob=='' || @$Supplier->Dob=='0000-00-00'){ $NewAge = '0'; }else {
$from = new DateTime($Supplier->Dob);
$to   = new DateTime('today');
$NewAge =  $from->diff($to)->y.'.'.$from->diff($to)->m;
DB::table('client')
->where('id', $Supplier->id)
->update(array('Age' => $NewAge));
}
$UserId = $ClientId;
?>
<style>
    .list-special .list-group-item:first-child {
        border-top-right-radius: 0px !important;
        border-top-left-radius: 0px !important;
    }

    .list-special .list-group-item:last-child {
        border-bottom-right-radius: 0px !important;
        border-bottom-left-radius: 0px !important;
    }

    .cursorcursor:active {
        cursor: move;
    }

    .cursorcursor li.ui-sortable-helper {
        cursor: move;
    }

    table td {
        vertical-align: middle !important;
    }

    .hover {
        -moz-box-shadow: inset 0 0 10px #000000;
        -webkit-box-shadow: inset 0 0 10px #000000;
        box-shadow: inset 0 0 10px #000000;
    }

    input:required:invalid, input:focus:invalid {
        border-color: var(--danger);
    }

    input:required:valid {
        border-color: var(--success);
    }

    #CallClient {
        width: fit-content;
        margin: auto;
    }

    .DivCreateToken {
        margin-top: 20px;
        margin-bottom: 30px;
        border: hidden;
        overflow: auto;
        min-height: 690px;
        height: 100%;
    }

    #Tokendiv {
        max-height: calc(100vh - 349px);
        height: 645px;
        overflow-y: auto;
    }
</style>
<div class="col-md-12 col-sm-12">
  <div class="row pb-10">
    <?php if (!empty($SettingsInfo->VoiceCenterToken)) { ?>
    <a href="#"  class="btn btn-dark btn-block" id='CallClient' >
      <i class="fas fa-phone-square fa-fw">
      </i> <?php echo lang('call_to_customer') ?>
    </a>
    <?php } ?>
  </div>
  <div class="row">
    <div class="col-md-2 col-sm-12 mobile-p-0"  id="menusidebarborder">
      <div class="card spacebottom">
        <a data-toggle="collapse" href="#MenuCard" aria-expanded="true" aria-controls="MenuCard" style="color: black;">
          <div class="card-header text-start">
            <strong>
              <i class="fas fa-bars fa-fw">
              </i> <?php echo lang('customer_nav') ?>
            </strong>
          </div>
        </a>
        <div class="collapse show" id="MenuCard">
          <div class="card-body">
            <div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
              <a class="nav-link text-dark active"  data-toggle="pill" href="#user-overview" role="tab" aria-controls="v-pills-overview" aria-selected="true">
                <i class="fas fa-th fa-fw">
                </i> <?php echo lang('control_panel') ?>
              </a>
              <?php if (Auth::userCan('54')): ?>
              <a class="nav-link text-dark"  data-toggle="pill" data-target="#user-activity" href="#user-activity" role="tab" aria-controls="v-pills-task" aria-selected="false">
                <i class="fas fa-list-alt fa-fw">
                </i> <?php echo lang('customer_card_membership') ?>
              </a>
              <?php endif ?>
              <?php if (Auth::userCan('63') && !$isRandomClient): ?>
              <a class="nav-link text-dark"  data-toggle="pill" data-target="#user-ClientAddClass" href="#user-ClientAddClass" role="tab" aria-controls="v-pills-task" aria-selected="false">
                <i class="fas fa-plus fa-fw">
                </i> <?php echo lang('customer_card_embed') ?>
              </a>
              <?php endif ?>
              <?php if (Auth::userCan('65')): ?>
              <a class="nav-link text-dark"  data-toggle="pill" data-target="#user-ClassHistory" href="#user-ClassHistory" role="tab" aria-controls="v-pills-task" aria-selected="false">
                <i class="fas fa-history fa-fw">
                </i> <?php echo lang('customer_card_classes') ?>
              </a>
              <?php endif ?>
              <?php if (Auth::userCan('63')): ?>
                  <?php
                  // don't count meetings
                  $ClassRegular = DB::table('classstudio_dateregular')
                      ->join('classstudio_act', 'classstudio_dateregular.id', '=', 'classstudio_act.regularClassId')
                      ->join('classstudio_date', 'classstudio_date.id', '=', 'classstudio_act.ClassId')
                      ->whereNull('classstudio_date.meetingTemplateId')
                      ->where('classstudio_dateregular.CompanyNum', $CompanyNum)
                      ->where('classstudio_dateregular.ClientId', $Supplier->id)
                      ->where('classstudio_dateregular.Status', 0)
                      ->where(
                          function ($query) {
                              $query->whereNull('classstudio_dateregular.EndDate')
                                  ->orWhere('classstudio_dateregular.EndDate', '>', date('Y-m-d'));
                          }
                      )
                      ->count();
                  if ($ClassRegular > '0' && @$Supplier->Status != '2') {
                      ?>
              <a class="nav-link text-dark"  data-toggle="pill" data-target="#user-ClassRegular" href="#user-ClassRegular" role="tab" aria-controls="v-pills-task" aria-selected="false">
                <i class="fas fa-reply-all fa-fw">
                </i> <?php echo lang('setting_permanently') ?>
              </a>
              <?php } ?>
              <?php endif ?>
              <?php if (Auth::userCan('66') && !$Supplier->isRandomClient): ?>
              <a class="nav-link text-dark"  data-toggle="pill" data-target="#user-Medical" href="#user-Medical" role="tab" aria-controls="v-pills-task" aria-selected="false">
                <i class="fas fa-medkit fa-fw">
                </i> <?php echo lang('customer_card_medical_records') ?>
              </a>
              <?php endif ?>
              <?php if (Auth::userCan('67')): ?>
              <a class="nav-link text-dark"  data-toggle="pill" data-target="#user-task" href="#user-task" role="tab" aria-controls="v-pills-task" aria-selected="false">
                <i class="fas fa-calendar-check fa-fw">
                </i> <?php echo lang('tasks') ?>
              </a>
              <?php endif ?>
              <?php if (Auth::userCan('68')): ?>
              <a class="nav-link text-dark" data-toggle="pill" href="#user-crm" role="tab" aria-controls="v-pills-crm" aria-selected="false">
                <i class="fas fa-sticky-note fa-fw">
                </i> <?php echo lang('customer_card_phone_records') ?>
              </a>
              <?php endif ?>
              <?php if (!$Supplier->isRandomClient): ?>
              <a class="nav-link text-dark" data-toggle="pill" href="#user-Health" role="tab" aria-controls="v-pills-crm" aria-selected="false">
                <i class="fab fa-wpforms fa-fw">
                </i> <?php echo lang('customer_card_forms') ?>
              </a>
              <?php endif ?>
              <?php if ($SettingsInfo->VoiceCenterToken!=''){ ?>
              <?php if (Auth::userCan('133')): ?>
              <a class="nav-link text-dark" data-toggle="pill" href="#user-callcrm" role="tab" aria-controls="v-pills-callcrm" aria-selected="false">
                <i class="fas fa-headset fa-fw">
                </i> <?php echo lang('center_voice') ?>
              </a>
              <?php endif ?>
              <?php } ?>
              <?php if (Auth::userCan('130')): ?>
              <a class="nav-link text-dark" data-toggle="pill" href="#user-clientcontact" role="tab" aria-controls="v-pills-clientcontact" aria-selected="false">
                <i class="fas fa-users fa-fw">
                </i> <?php echo lang('customer_card_contacts') ?>
              </a>
              <?php endif ?>
              <?php if (is_object($PipeNow) != '0') { ?>
              <a class="nav-link text-dark" data-toggle="pill" href="#user-lead" role="tab" aria-controls="v-pills-lead" aria-selected="false">
                <i class="fas fa-at fa-fw">
                </i> <?php echo lang('lead_info') ?>
              </a>
              <?php } ?>
              <?php if (Auth::userCan('69')): ?>
              <div class="group">
                <a class="nav-link text-dark dropdown-toggle" id="btnGroupDrop1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                  <i class="fas fa-shekel-sign fa-fw">
                  </i> <?php echo lang('bookkeeping') ?>
                </a>
                <div class="dropdown-menu text-start dropdown-menu-right py-0" aria-labelledby="btnGroupDrop1">
                  <a class="dropdown-item py-7" data-toggle="pill" href="#user-account" role="tab" aria-controls="v-pills-sendit" aria-selected="false">
                    <i class="fas fa-shekel-sign fa-fw">
                    </i> <?php echo lang('docs') ?>
                  </a>
                  <a class="dropdown-item py-7" data-toggle="pill" href="#user-accountmoney" role="tab" aria-controls="v-pills-archivsms" aria-selected="false">
                    <i class="fas fa-list-ul fa-fw">
                    </i> <?php echo lang('detailed_receipt') ?>
                  </a>
                </div>
              </div>
              <?php endif; ?>
              <?php if ((!$isRandomClient) && (Auth::userCan('75') || $editLeadsPermission) ): ?>
              <div class="group">
                <a class="nav-link text-dark dropdown-toggle" id="btnGroupDrop1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                  <i class="fas fa-comments fa-fw">
                  </i> <?php echo lang('messages') ?>
                </a>
                <div class="dropdown-menu text-start dropdown-menu-right py-0" aria-labelledby="btnGroupDrop1">
                  <a class="dropdown-item py-7" data-toggle="pill" href="#user-sendit" role="tab" aria-controls="v-pills-sendit" aria-selected="false">
                    <i class="fas fa-share-square fa-fw">
                    </i> <?php echo lang('send_message') ?>
                  </a>
                  <?php if (Auth::userCan('77') || $editLeadsPermission): ?>
                  <a class="dropdown-item py-7" data-toggle="pill" href="#user-ArchiveMessage" role="tab" aria-controls="v-pills-archivsms" aria-selected="false">
                    <i class="fas fa-comments fa-fw">
                    </i> <?php echo lang('archive_msg') ?>
                  </a>
                  <?php endif; ?>
                </div>
              </div>
              <?php endif; ?>
              <?php if (Auth::userCan('70')): ?>
              <div class="group">
                <a class="nav-link text-dark dropdown-toggle" id="btnGroupDrop2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                  <i class="fas fa-credit-card fa-fw">
                  </i> <?php echo lang('customer_card_charges') ?>
                </a>
                <div class="dropdown-menu text-start dropdown-menu-right py-0" aria-labelledby="btnGroupDrop2">
                    <?php
                    //todo-bp-909 (cart) remove-beta
                    $CompanySettingsDash = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
                    if(!in_array($CompanySettingsDash->beta, [1]))
                    { ?>
                  <a class="dropdown-item py-7" data-toggle="pill" href="#user-pay" role="tab" aria-controls="v-pills-pay" aria-selected="false">
                    <i class="fas fa-credit-card fa-fw">
                    </i> <?php echo lang('charge_client') ?>
                  </a>
                  <?php }
                  if (Auth::userCan('72') && !$isRandomClient && $Supplier->Status!='2') { ?>
                  <a class="dropdown-item py-7" data-toggle="pill" href="#user-paytoken" role="tab" aria-controls="v-pills-paytoken" aria-selected="false">
                    <i class="fas fa-sync fa-fw">
                    </i> <?php echo lang('hok_title') ?>
                  </a>
                  <?php } ?>
                  <?php if (Auth::userCan('73') && !$Supplier->isRandomClient): ?>
                  <a class="dropdown-item SaveTokenMeshulam py-7" id="SaveTokenMeshulam" data-toggle="pill" href="#user-token" role="tab" aria-controls="v-pills-token" aria-selected="false">
                    <i class="fab fa-expeditedssl fa-fw">
                    </i> <?php echo lang('save_credit_card') ?>
                  </a>
                  <?php endif; ?>
                  <?php //todo-bp-909 (cart) remove-beta
                    if(Auth::userCan('71') && !in_array($CompanySettingsDash->beta, [1])) : ?>
                  <a class="dropdown-item text-danger py-7" data-toggle="pill" href="#user-refoundpay" role="tab" aria-controls="v-pills-pay" aria-selected="false">
                    <i class="fas fa-credit-card fa-fw">
                    </i> <?php echo lang('refund_title') ?>
                  </a>
                  <?php endif; ?>
                </div>
              </div>
              <?php endif; ?>
              <div class="group" role="group">
                <a class="nav-link text-dark dropdown-toggle" id="btnGroupDrop1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                  <i class="fas fa-cogs fa-fw">
                  </i> <?php echo lang('path_settings') ?>
                </a>
                <div class="dropdown-menu text-start dropdown-menu-right py-0" aria-labelledby="btnGroupDrop1">
                  <?php if (Auth::userCan('78' || $editLeadsPermission) && !$isRandomClient): ?>
                  <a class="dropdown-item py-7" data-toggle="pill" href="#user-settings" role="tab" aria-selected="false">
                    <i class="far fa-edit fa-fw">
                    </i> <?php echo lang('edit_customer_card') ?>
                  </a>
                  <?php endif ?>
                  <?php if (@$Supplier->Status!='2') { ?>
                  <a class="dropdown-item py-7" data-toggle="pill" href="#user-files" role="tab" aria-selected="false" style="display: none;">
                    <i class="far fa-hdd fa-fw">
                    </i> <?php echo lang('file') ?>
                  </a>
                  <?php } ?>
                  <?php if (Auth::userCan('79')): ?>
                  <a class="dropdown-item py-7" data-toggle="pill" href="#user-log" role="tab" aria-selected="false">
                    <i class="fas fa-bars fa-fw">
                    </i> <?php echo lang('log_single') ?>
                  </a>
                  <?php endif ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      $(document).click(function(event) {
        if(!$(event.target).closest('.dropdown-toggle').length) {
          $(".dropdown-item").removeClass("active");
          $(".dropdown-item").removeClass("show");
        }
      }
                       );
    </script>
    <div class="col-md-10 col-sm-12 mobile-p-0" >
      <div class="tab-content">
        <div class="alert alert-warning CallClientdivb text-start" style="display: none;">
          <i class="fas fa-spinner fa-spin">
          </i>
          <strong><?php echo lang('try_calling_client_few_moments') ?>
          </strong>
        </div>
        <div class="alert alert-success CallClientdiv text-start" style="display: none;">
          <span class="fa-layers fa-fw text-success">
            <i class="fas fa-circle">
            </i>
            <i class="fa-inverse fas fa-phone" data-fa-transform="shrink-6">
            </i>
          </span>
          <strong><?php echo lang('call_is_being_made_pleasant_call') ?>
          </strong>
        </div>
        <?php if (Auth::userCan('52') || (Auth::userCan('47') and $Supplier->Status == 2)): ?>
        <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
          <div class="card spacebottom">
            <div class="card-header text-start d-flex justify-content-between">
              <div>
                <i class="fas fa-th">
                </i>
                <b><?php echo lang('control_panel') ?>
                </b>
              </div>
            </div>
            <div class="card-body">

                <?php
                if($appClient && $appClient->StatusBadPoint != 0) {
                ?>
                <div id="OpenAppUsers" class="container-fluid">
                    <div class="row alert alert-danger d-flex justify-content-between ">
                        <div class="align-self-center">
                            <span><?php echo lang('clinet_blocked'); ?></span>
                        </div>
                        <div class="text-end">
                            <button onclick="UserAppAccess(this,0)" class="btn btn-danger text-end"><?php echo lang('remove_client_block'); ?></button>
                        </div>
                    </div>
                </div>
                <?php } ?>

              <div class="row" >
                <div class="col-md-6 col-sm-12 order-md-1" >
                  <div class="card spacebottom" style="margin-bottom: 20px;">
                    <a data-toggle="collapse"  aria-expanded="true" style="color: black;">
                      <div class="card-header text-start d-flex justify-content-between">
                        <strong>
                          <i class="fas fa-user fa-fw">
                          </i>
                          <span class="CompanyNameChange">
                            <?php echo htmlentities($Supplier->CompanyName); ?>
                          </span> ::
                          <?php echo $Supplier->MemberId; ?>
                        </strong>
                            <?php if ($Supplier->ContactMobile): ?>
                                <a class="text-success bsapp-text-sm-18"
                                   href="https://wa.me/<?php echo str_replace('+', '', $Supplier->ContactMobile) ?>"
                                   target="_blank" rel="noopener noreferrer">
                                    <i class="fab fa-whatsapp fa-lg"></i>
                                </a>
                            <?php endif; ?>
                      </div>
                    </a>
                    <div class="collapse show">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group">
                              <span class="ShowQuick" onclick="UpdateLive('EditClientQuick-companyname','companyname', <?= $isRandomClient ?>)" id="companyname">
                                <?php echo htmlentities($Supplier->CompanyName); ?>
                                <?php if($Supplier->parentClientId != 0) { ?>
                                <span class="text-info font-weight-bold">(<?php echo lang('minor') ?>)</span>
                                <?php } ?>
                              </span>
                              <form action="EditClientQuickcompanyname"  id="EditClientQuick-companyname" class="ajax-form text-start EditQuick" autocomplete="off" style="display: none;">
                                <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                                <div class="mb-3">
                                  <input type="text" class="form-control" id="companyname1" placeholder="<?php echo lang('name_table') ?>" name="companyname" value="<?php echo htmlentities(@$Supplier->CompanyName); ?>">
                                  <div class="text-start pt-1" >
                                    <?php if (Auth::userCan('78') || $editLeadsPermission): ?>
                                    <button type="submit" class="btn btn-outline-success btn-sm" type="button">
                                      <i class="fas fa-sync-alt fa-xs">
                                      </i> <?php echo lang('update') ?>
                                    </button>
                                    <?php endif ?>
                                    <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                      <i class="fas fa-times-circle fa-xs">
                                      </i> <?php echo lang('close') ?>
                                    </button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <span class="ShowQuick" onclick="UpdateLive('EditClientQuick-companyid','companyid', <?= $isRandomClient ?>)" id="companyid">
                                <?php
                                if (@$Supplier->CompanyId != '') {echo lang('id'); echo $Supplier->CompanyId;}
                                else {echo lang('customer_card_id');}
                                ?>
                              </span>
                              <form action="EditClientQuickcompanyid"  id="EditClientQuick-companyid" class="ajax-form text-start EditQuick" autocomplete="off" style="display: none;">
                                <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                                <div class="mb-3">
                                  <input type="text" class="form-control" id="companyid1" placeholder="<?php echo lang('id_card') ?>" name="companyid" value="<?php echo @$Supplier->CompanyId; ?>">
                                  <div class="text-start pt-1" >
                                    <?php if (Auth::userCan('78') || $editLeadsPermission): ?>
                                    <button type="submit" class="btn btn-outline-success btn-sm" type="button">
                                      <i class="fas fa-sync-alt fa-xs">
                                      </i> <?php echo lang('update') ?>
                                    </button>
                                    <?php endif ?>
                                    <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                      <i class="fas fa-times-circle fa-xs">
                                      </i> <?php echo lang('close') ?>
                                    </button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <span class="ShowQuick" onclick="UpdateLive('EditClientQuick-phone','phone', <?= $isRandomClient ?>)" id="phone">
                                <?php
                                if (!empty($Supplier->ContactMobile)) { echo lang('phone'); echo '<span dir="ltr">'.$Supplier->ContactMobile.'</span>';}
                                else {echo lang('no_phone');}
                                ?>
                              </span>
                              <form action="EditClientQuickphone"  id="EditClientQuick-phone" class="ajax-form text-start EditQuick" autocomplete="off" style="display: none;">
                                <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                                <div class="mb-3">
                                  <input type="text" class="form-control unicode-plaintext" id="phone1" placeholder="<?php echo lang('cell_table') ?>" name="phone" value="<?php echo $Supplier->ContactMobile; ?>" required pattern="^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$" title="לדוגמה 972501234567+ או 0501234567">
                                  <div class="text-start pt-1" >
                                    <?php if (Auth::userCan('78') || $editLeadsPermission): ?>
                                    <button type="submit" class="btn btn-outline-success btn-sm" type="button">
                                      <i class="fas fa-sync-alt fa-xs">
                                      </i> <?php echo lang('update') ?>
                                    </button>
                                    <?php endif ?>
                                    <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                      <i class="fas fa-times-circle fa-xs">
                                      </i> <?php echo lang('close') ?>
                                    </button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <?php
                              if ($Supplier->Status=='0'){
                              $StatusColor = "var(--success)";
                              $StatusText = lang('active');
                              }
                              else if ($Supplier->Status=='1'){
                              $StatusColor = "var(--danger)";
                              $StatusText = lang('archive');
                              }
                              else {
                              $StatusColor = "var(--warning)";
                              $StatusText = lang('interested_single');
                              }
                              ?>
                              <span  class="ShowQuick" onclick="UpdateLive('EditClientQuick-status','status', <?= $isRandomClient ?>)" id="status">
                                <span id="status1" style="color: <?php echo $StatusColor; ?>">
                                  <?php	echo $StatusText;	?>
                                </span>
                              </span>
                              <div id="EditClientQuick-status" class="ajax-form text-start EditQuick" autocomplete="off" style="display: none;">
                                <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                                <div class="mb-3">
                                    <input type="radio" class="status" name="status" id="0" value="0"
                                         <?php if ($Supplier->Status == '0') {echo 'checked';} ?>>
                                  <label for="0">&nbsp;
                                    <span style="color: var(--success)"><?php echo lang('active') ?>
                                    </span>
                                  </label>
                                  <br>
                                    <input type="radio" class="1" name="status" id="1" value="1"
                                         <?php if ($Supplier->Status == '1') {echo 'checked';} ?>>
                                  <label for="1">&nbsp;
                                    <span style="color: var(--danger)"><?php echo lang('archive') ?>
                                    </span>
                                  </label>
                                  <div class="w-100 text-start pt-1 d-inline-flex" >
                                    <?php if (Auth::userCan('80') || $editLeadsPermission):
                                        if (isset($PipeNow) && $Supplier->Status == 2) { ?>
                                      <button onclick="clientStatusChange(null,0)" class="btn btn-outline-success btn-sm" type="button">
                                          <?php } else {?>
                                        <button onclick="clientStatusChange(null,1)" class="btn btn-outline-success btn-sm" type="button">
                                        <?php } ?>
                                      <i class="fas fa-sync-alt fa-xs">
                                      </i> <?php echo lang('update') ?>
                                    </button>
                                    <?php endif ?>
                                    <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                      <i class="fas fa-times-circle fa-xs">
                                      </i> <?php echo lang('close') ?>
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-4">
                            <div class="form-group">
                              <span class="ShowQuick" onclick="UpdateLive('EditClientQuick-dob','dob', <?= $isRandomClient ?>)" id="dob">
                                <?php
                                if (!empty($Supplier->Dob) && $Supplier->Dob != '0000-00-00') { echo lang('birthday_short: '); echo with(new DateTime($Supplier->Dob))->format('d/m/Y');}
                                else {echo lang('no_birthday_date');}
                                ?>
                              </span>
                              <form action="EditClientQuickdob"  id="EditClientQuick-dob" class="ajax-form text-start EditQuick" autocomplete="off" style="display: none;">
                                <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                                <div class="mb-3">
                                  <input style="font-size: 12px;" type="date" class="form-control" id="dob1" placeholder="<?php echo lang('date_birthday') ?>" name="dob" value="<?php if (empty($Supplier->Dob) || $Supplier->Dob=='0000-00-00') {} else { echo $Supplier->Dob; } ?>">
                                  <div class="text-start pt-1" >
                                    <?php if (Auth::userCan('78') || $editLeadsPermission): ?>
                                    <button type="submit" class="btn btn-outline-success btn-sm" type="button">
                                      <i class="fas fa-sync-alt fa-xs">
                                      </i> <?php echo lang('update') ?>
                                    </button>
                                    <?php endif ?>
                                    <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                      <i class="fas fa-times-circle fa-xs">
                                      </i> <?php echo lang('close') ?>
                                    </button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="form-group">
                              <?php if ($NewAge!='0'){ echo lang('age_client_profile'); echo $NewAge; } ?>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                              <?php
                              if ($Supplier->Gender=='1'){$GenderText = '<i class="fas fa-male"></i>' .lang('male');}
                              elseif ($Supplier->Gender=='2'){$GenderText = '<i class="fas fa-female"></i>' .lang('female');}
                              else {$GenderText = lang('customer_card_gender');}
                              ?>
                              <span  class="ShowQuick" onclick="UpdateLive('EditClientQuick-gender','gender', <?= $isRandomClient ?>)" id="gender">
                                <span id="gender1">
                                  <?php
                                  if (@$Supplier->Gender != '0') {echo $GenderText;}
                                  else {echo lang('customer_card_gender');}
                                  ?>
                                </span>
                              </span>
                              <form action="EditClientQuickgender"  id="EditClientQuick-gender" class="ajax-form text-start EditQuick" autocomplete="off" style="display: none;">
                                <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                                <div class="mb-3">
                                  <input type="radio" class="gender" name="gender" id="male" value="1"
                                         <?php if ($Supplier->Gender == '1') {echo 'checked';}; ?>>
                                  <label for="male">&nbsp;
                                    <i class="fas fa-male">
                                    </i> <?php echo lang('male') ?>
                                  </label>
                                  <br>
                                  <input type="radio" class="gender" name="gender" id="female" value="2"
                                         <?php if ($Supplier->Gender == '2') {echo 'checked';}; ?>>
                                  <label for="female">&nbsp;
                                    <i class="fas fa-female">
                                    </i> <?php echo lang('female') ?>
                                  </label>
                                  <div class="text-start pt-1" >
                                    <?php if (Auth::userCan('78') || $editLeadsPermission): ?>
                                    <button type="submit" class="btn btn-outline-success btn-sm" type="button">
                                      <i class="fas fa-sync-alt fa-xs">
                                      </i> <?php echo lang('update') ?>
                                    </button>
                                    <?php endif ?>
                                    <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                      <i class="fas fa-times-circle fa-xs">
                                      </i> <?php echo lang('close') ?>
                                    </button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <span class="ShowQuick" onclick="UpdateLive('EditClientQuick-email','email', <?= $isRandomClient ?>)" id="email">
                              <?php
                              if (@$Supplier->Email != '') {echo $Supplier->Email;}
                              else {echo lang('customer_card_adress');}
                              ?>
                              </span>
                              <form action="EditClientQuickemail"  id="EditClientQuick-email" class="ajax-form text-start EditQuick" autocomplete="off" style="display: none;">
                                <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                                <div class="mb-3">
                                  <input type="text" class="form-control" id="email1" placeholder="<?php echo lang('email') ?>" name="email" value="<?php echo @$Supplier->Email; ?>">
                                  <div class="text-start pt-1" >
                                    <?php if (Auth::userCan('78') || $editLeadsPermission): ?>
                                    <button type="submit" class="btn btn-outline-success btn-sm" type="button">
                                      <i class="fas fa-sync-alt fa-xs">
                                      </i> <?php echo lang('update') ?>
                                    </button>
                                    <?php endif ?>
                                    <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                      <i class="fas fa-times-circle fa-xs">
                                      </i> <?php echo lang('close') ?>
                                    </button>
                                  </div>
                                </div>
                              </form>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                            <?php echo lang('join_date') ?>:
                              <?php echo with(new DateTime(@$Supplier->Dates))->format('d/m/Y'); ?>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                            <?php echo lang('branch') ?>:
                              <?php echo @$Supplier->BrandName; ?>
                            </div>
                          </div>
                          <?php
                          if (@$Supplier->Takanon=='0'){
                          $TakanonColor = '#ff003b';
                          $TakanonText = lang('term_not_accept');
                          }
                          else {
                          $TakanonColor = '#00c736';
                          $TakanonText = lang('terms_accept');
                          }
                          if (@$Supplier->Medical=='0'){
                          $MedicalColor = '#ff003b';
                          $MedicalText = lang('health_not_accept');
                          }
                          else {
                          $MedicalColor = '#00c736';
                          $MedicalText = lang('health_accept');
                          }
                          $ClientHealth = DB::table('healthforms_answers')->where('CompanyNum', $CompanyNum)->where('ClientId', $Supplier->id)->orderBy('created', 'DESC')->first();
                          $CheckTokenClient = DB::table('boostapplogin.studio')->where('CompanyNum', $CompanyNum)->where('ClientId', $Supplier->id)->first();
                          $CheckTokenClients = DB::table('boostapplogin.users')->where('id', @$CheckTokenClient->UserId)->first();
                          $TokenIcon = '';
                          if (@$CheckTokenClients->tokenFirebase=='' || @$CheckTokenClients->tokenFirebase=='No Token'){
                          $TokenColor = '#ff003b';
                          $TokenText = lang('push_not_accept');
                          }
                          else {
                          $TokenColor = '#00c736';
                          $TokenText = lang('push_accept');
                          }
                          if (@$CheckTokenClients->OS=='1'){
                          $TokenIcon = '<i class="fab fa-android"></i>';
                          }
                          else if (@$CheckTokenClients->OS=='2') {
                          $TokenIcon = '<i class="fab fa-apple"></i>';
                          }
                          else {
                          $TokenIcon = '<i class="fab fa-chrome"></i>';
                          }
                          ?>
                          <div class="col-md-3">
                            <div class="form-group">
                              <?php if (@$ClientHealth->id!=''){ ?>
                              <a href="javascript:void(0);" onclick="TINY.box.show({iframe:'PDF/HealthPDF.php?id=<?php echo @$ClientHealth->id; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})"><?php echo lang('health_declaration') ?>:
                                <i class="fas fa-briefcase-medical" style="color: <?php echo $MedicalColor; ?> " data-toggle="tooltip" data-placement="top" title="<?php echo @$MedicalText; ?>">
                                </i>
                              </a>
                              <?php } else { ?>
                                <?php echo lang('health_declaration') ?>:
                              <i class="fas fa-briefcase-medical" style="color: <?php echo $MedicalColor; ?> " data-toggle="tooltip" data-placement="top" title="<?php echo @$MedicalText; ?>">
                              </i>
                              <?php } ?>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                            <?php echo lang('terms') ?>:
                              <i class="fas fa-address-book" style="color: <?php echo $TakanonColor; ?> " data-toggle="tooltip" data-placement="top" title="<?php echo @$TakanonText; ?>">
                              </i>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="form-group">
                            <?php echo lang('push_notifications') ?>:
                              <i class="fas fa-bell" style="color: <?php echo $TokenColor; ?> " data-toggle="tooltip" data-placement="top" title="<?php echo @$TokenText; ?>">
                              </i>
                              <?php echo @$TokenIcon; ?>
                            </div>
                          </div>
                          <?php
                          $CRM = DB::table('clientcrm')
                          ->where('CompanyNum', $CompanyNum)->where('ClientId', '=', $Supplier->id)->where('StarIcon', '=', '1')->where('Status', '=', '0')->whereNull('TillDate')
                          ->Orwhere('CompanyNum', $CompanyNum)->where('ClientId', '=', $Supplier->id)->where('StarIcon', '=', '1')->where('Status', '=', '0')->where('TillDate', '>=', date('Y-m-d'))
                          ->orderBy('dates','DESC')
                          ->first();
                          if (@$CRM->id!=''){
                          if ($CompanyNum=='569121') {
                          ?>
                          <div class="col-md-3">
                            <div class="form-group">
                            <?php echo lang('push_notice') ?>:
                              <i class="fas fa-star-of-life" title="<?php echo lang('push_notice') ?>" style="color:sandybrown;" >
                              </i>
                            </div>
                          </div>
                          <?php } else { ?>
                          <div class="col-md-3">
                            <div class="form-group">
                            <?php echo lang('customer_card_phone_records') ?>:
                              <i class="fas fa-star-of-life" title="<?php echo lang('customer_card_phone_records') ?>" style="color:sandybrown;" >
                              </i>
                            </div>
                          </div>
                          <?php } } ?>
                          <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="form-group">
<!--                                        <label> --><?php //echo lang('cust_rank') ?><!-- </label>-->
                                        <div class="ShowQuick" onclick="UpdateLive('Editlevel','level', <?= $isRandomClient ?>)" id="level">
                                              <?php
                                              $myArray = (new Rank())->getRankNamesArrayByClientId($Supplier->id);
                                              $tempStr = implode(",", $myArray);
                                              if (!empty ($myArray)) {echo (lang('rank'). ': '. $tempStr);}
                                              else {echo lang('without_customer_rank'); }
                                              ?>
                                        </div>
                                        <div id="Editlevel" class="EditQuick" style="display: none;">

                                        <form action="EditClientQuickclasslevel"  id="EditClientQuick-classlevel" class="ajax-form text-start" autocomplete="off">
                                            <input type="hidden" name="ClientId" value="<?php echo $Supplier->id ?>">
                                        <select class="form-control js-example-basic-single select2Rank text-start" data-placeholder="<?php echo lang('select_rank') ?>"  name="Rank[]" id="Rank"   multiple="multiple" data-select2order="true">
                                                <?php
                                            $ClientLevels = DB::table('clientlevel')->where('CompanyNum', '=', $CompanyNum)->get();
                                            foreach ($ClientLevels as $ClientLevel) {
                                                $selected = (in_array($ClientLevel->Level, $myArray)) ? ' selected="selected"' : '';
                                                ?>
                                                <option value="<?php echo $ClientLevel->id; ?>" <?php echo @$selected; ?> ><?php echo $ClientLevel->Level; ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>

                                            <button type="submit" class="btn btn-outline-success btn-sm" type="button">
                                                <i class="fas fa-sync-alt fa-xs">
                                                </i> <?php echo lang('set_single') ?>
                                            </button>
                                            <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                                <i class="fas fa-times-circle fa-xs">
                                                </i> <?php echo lang('close') ?>
                                            </button>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                      </div>

                      <div class="col-md-12">
                        <div class="form-group">
                          <?php echo $Supplier->Remarks; ?>
                        </div>
                      </div>
                      <?php if ($Supplier->ParentsName!='') { ?>
                      <div class="col-md-12">
                        <div class="form-group">
                        <?php echo lang('parent_name') ?>:
                          <?php echo $Supplier->ParentsName; ?>
                        </div>
                      </div>
                      <?php } ?>
                      <?php if (Auth::user()->role_id=='1') { ?>
                      <div class="col-md-12">
                        <div class="form-group">
                          <span  class="ShowQuick" onclick="UpdateLive('AppPasswordAppUsers','AppPasswordsAppUsers', <?= $isRandomClient ?>)" id="AppPasswordsAppUsers">
                            <span id="AppPasswordsAppUsers">
                            <?php echo lang('support_password') ?>
                            </span>
                          </span>
                          <form action="AppPasswordAppUsers"  id="AppPasswordAppUsers" class="ajax-form text-start EditQuick" autocomplete="off" style="display: none;">
                            <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                            <div class="mb-3">
                              <div class="text-start pt-1" >
                                <button type="submit" class="btn btn-outline-success btn-sm" type="button">
                                  <i class="fas fa-sync-alt fa-xs">
                                  </i> <?php echo lang('set_single') ?>
                                </button>
                                <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                  <i class="fas fa-times-circle fa-xs">
                                  </i> <?php echo lang('close') ?>
                                </button>
                              </div>
                            </div>
                          </form>
                        </div>
                      </div>
                      <?php } ?>
                      <?php if (!$isRandomClient) { ?>
                      <div class="col-md-4">
                        <div class="form-group">
                          <a class="btn btn-dark btn-block text-white btn-sm" href="javascript:void(0);" onClick="SendAppPassModal(<?php echo $Supplier->id; ?>,1)"><?php echo lang('customer_card_send_login') ?>
                          </a>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <span  class="ShowQuick" onclick="UpdateLive('CloseAppUsers','ClosesAppUsers', <?= $isRandomClient ?>)" id="ClosesAppUsers">
                            <span id="ClosesAppUsers" class="btn btn-danger btn-block text-white btn-sm">
                            <?php echo lang('customer_card_disable_app') ?>
                            </span>
                          </span>
                            <form action="CloseAppUsers"  id="CloseAppUsers" class="ajax-form text-start EditQuick" autocomplete="off" style="display: none;">
                                <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                                <div class="mb-3">
                                    <div class="text-start pt-1" >
                                        <button type="submit" class="btn btn-outline-success btn-sm" type="button">
                                            <i class="fas fa-sync-alt fa-xs">
                                            </i> <?php echo lang('disconnect_single') ?>
                                        </button>
                                        <button onClick="CloseQuickEdit();" class="btn btn-outline-danger btn-sm mr-1" type="button">
                                            <i class="fas fa-times-circle fa-xs">
                                            </i> <?php echo lang('close') ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                      </div>
                      <?php } ?>
                    </div>
                    <?php if ($Supplier->Status=='2' && Auth::userCan('158')){ ?>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <a href='javascript:PipeSendForm(<?php echo @$PipeId; ?>,<?php echo $Supplier->id; ?>);' class='btn btn-info btn-block text-white btn-sm' ><?php echo lang('send_form') ?>
                          </a>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <a href='javascript:PipeSendForm2(<?php echo @$PipeId; ?>,<?php echo $Supplier->id; ?>);' class='btn btn-info btn-block text-white btn-sm' ><?php echo lang('send_health') ?>
                          </a>
                        </div>
                      </div>
                    </div>
                    <?php } ?>
<!--                    <hr>-->
<!--                    <div class="row">-->
<!--                      <div class="col-md-3">-->
<!--                        <div class="form-group">-->
<!--                          <div class="text-center">-->
<!--                            <a id="profileimageiframe" href="javascript:void(0);" target="_top">-->
<!--                              --><?php //if (empty($CheckUserApp->UploadImage)){ ?>
<!--                              <img class="rounded-circle img-fluid" alt="--><?php //echo htmlentities($Supplier->CompanyName); ?><!--" src="--><?php //echo 'https://ui-avatars.com/api/?name='.$Supplier->LastName.'+'.$Supplier->FirstName.'&background='.hexcode($Supplier->CompanyName).'&color=ffffff&font-size=0.5'; ?><!--">-->
<!--                              --><?php //} else { ?>
<!--                              <img class="rounded-circle img-fluid" alt="--><?php //echo htmlentities($Supplier->CompanyName); ?><!--" src="--><?php //echo get_appboostapp_domain(); ?><!--/camera/uploads/large/--><?php //echo @$CheckUserApp->UploadImage ?><!--">-->
<!--                              --><?php //} ?>
<!--                            </a>-->
<!--                          </div>-->
<!--                        </div>-->
<!--                      </div>-->
<!--                      <div class="col-md-9">-->
<!--                        <div class="form-group">-->
<!--                        --><?php //echo lang('customer_card_progress') ?>
<!--                        </div>-->
<!--                      </div>-->
<!--                    </div>-->
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 order-md-2" >
              <div class="d-flex flex-d-col">
                <?php
                if($SettingsInfo->greenPass) {
                  if($Supplier->greenPassStatus == 2) {     // confirmed
                    $greenPassText = lang('green_pass_confirmed_notice');
                    $cssClass = 'text-success';
                    $badgeIcon = '<i class="fas fa-badge-check fa-lg"></i>';
                    $modalIconStatus = '<i class="fas fa-badge-check '.$cssClass.'"></i>';
                  } else if($Supplier->greenPassStatus == 1) {  /// pending
                    $greenPassText = lang('green_pass_pending_notice');
                    $cssClass = 'text-orange';
                    $badgeIcon = '<i class="far fa-badge-check fa-lg"></i>';
                    $modalIconStatus = '<i class="far fa-badge-check '.$cssClass.'"></i>';

                  } else {
                    $greenPassText = lang('no_green_pass');
                    $cssClass = 'text-danger';
                    $badgeIcon = '<i class="far fa-badge fa-lg"></i>';
                    $modalIconStatus = '<i class="far fa-badge '.$cssClass.'"></i>';
                  }
                ?>
                <div class="card spacebottom mb-20">
                  <div>
                    <a data-toggle="collapse" aria-expanded="true" style="color: black;">
                      <div class="card-header text-start">
                        <strong>
                        <i class="fas fa-badge-check"></i>
                          <span><?php echo lang('green_passport') ?></span>
                        </strong>
                      </div>
                    </a>
                    <div class="card-body">
                      <div class="d-flex justify-content-between align-items-center js-greenpass-content">
                        <div>
                          <div class=" d-flex align-items-center js-greenpass-text-color <?php echo $cssClass ?>">
                            <?php echo $badgeIcon; ?>
                            <div class="mis-11 d-flex flex-column">
                              <span class="font-weight-bold bsapp-fs-13 js-greenpass-text"><?php echo $greenPassText; ?></span>
                              <?php if($Supplier->greenPassValid && $Supplier->greenPassStatus) { ?>
                              <span class="font-weight-bold bsapp-fs-13 line-1-5 js-greenpass-date <?php echo strtotime($Supplier->greenPassValid) > strtotime(date('Y-m-d')) ? 'text-black' : 'text-danger' ?>"><?php echo lang('expires_at').': ' ?><span class="js-greenpass-date-span"><?= $Supplier->greenPassValid; ?></span></span>
                              <?php } ?>
                            </div>

                          </div>
                        </div>
                        <div class="">
                          <a href="#green_pass_modal" data-toggle="modal" class="btn btn-light bsapp-fs-13 px-30"><?php echo lang('edit') ?></a>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <?php } ?>
                <?php if (Auth::userCan('53')): ?>
                <div class="card spacebottom" style="margin-bottom: 20px;">
                  <a data-toggle="collapse" aria-expanded="true" style="color: black;">
                    <div class="card-header text-start">
                      <strong>
                        <i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i>
                        <span><?php echo lang('accounts') ?>
                      </strong>
                    </div>
                  </a>
                    <div class="collapse show">
                      <div class="card-body">
                        <div class="row align-items-center">
                          <div class="col-md-7 order-md-2 text-start">
                            <canvas id="myChartTop">
                            </canvas>
                          </div>
                          <div class="col-md-5 order-md-1">
                            <?php
                            if ($Supplier->BalanceAmount=='0'){
                            $BalanceAmountColor = 'success';
                            }
                            else{
                            $BalanceAmountColor = 'danger';
                            }
                            ?>
                            <div class="card-body text-start" style="margin-bottom: 0px; padding-bottom: 0px;">
                              <span class="text-center text-secondary">
                                <small class="font-weight-bold"><?php echo lang('remainder_of_payment') ?>
                                </small>
                              </span>
                              <br>
                              <span class="text-center font-weight-bold text-<?php echo $BalanceAmountColor; ?>" style="font-size: 20px;">₪
                                <?php echo number_format(@$Supplier->BalanceAmount,2,".",",");  ?>
                              </span>
                              <p>
                              </p>
                              <p>
                              </p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    </div>
                  <?php endif; ?>
                </div>
                <?php if(!$isRandomClient) { ?>
                <div class="card spacebottom">
                  <div>
                    <a data-toggle="collapse" aria-expanded="true" style="color: black;">
                      <div class="card-header text-start">
                        <strong>
                        <i class="fas fa-house"></i>
                          <span><?php echo lang('family_relationship') ?></span>
                        </strong>
                      </div>
                    </a>
                    <div class="card-body">
                      <div class="family-card">
                        <?php
                        foreach($getRelatives as $client) {
                          $rel = '';
                          if($Supplier->parentClientId != 0) {  //// כרטיס לקוח קטין
                            if($client->parentClientId == 0 && $Supplier->relationship > 0) {
                              $rel = $relArr[$Supplier->relationship - 1];
                            } else if($client->parentClientId != 0) {
                              $rel = '('.lang('minor').')';
                            } else {
                              $rel = '('.lang('main_client').')';
                            }
                          } else {
                            if($client->relationship == 1 || $client->relationship == 2) {
                              $rel = '('.lang('boy_or_girl').')';
                              if($client->Gender == 1) {
                                $rel = '('.lang('boy').')';
                              } else if($client->Gender == 2) {
                                $rel = '('.lang('girl').')';
                              }
                            } else if($client->relationship > 0) {
                              $rel = $relArr[$client->relationship - 1];
                            } else {
                              $rel = '('.lang('minor').')';
                            }
                          }

                        ?>
                        <div id="minor-<?php echo $client->id ?>" class="d-flex justify-content-between align-items-center font-rubik-sans pb-15">
                          <div>
                            <div>
                              <a class="text-muted" href="/office/ClientProfile.php?u=<?php echo $client->id ?>"><span><?php echo $client->CompanyName ?></span> <span class="font-weight-bold <?php echo $client->parentClientId == 0 ? "text-info" : '' ?>"><?php echo !empty($rel) ? $rel : '' ?></span></a>
                            </div>
                          </div>
                          <div>
                            <div>
                              <!-- <select class="bottom-border-line py-4 w-90p" name="minor_actions" id="minor_actions">
                                <option selected="true" disabled="disabled">פעולות</option>
                                <option value="1">עריכה</option>
                                <option value="2">הסרה</option>
                              </select> -->
                              <a href="/office/ClientProfile.php?u=<?php echo $client->id; ?>#user-settings" title="<?php echo lang('edit_minor_details') ?>" class="text-gray mie-7"><i class="fad fa-edit fa-md"></i></a>
                              <?php if($Supplier->parentClientId == 0) { ?>
                              <a data-toggle="modal" data-id="<?php echo $client->id ?>" data-minor-mobile="<?php echo $client->ContactMobile ? $client->ContactMobile : '' ?>" data-minor-email="<?php echo $client->Email ? $client->Email : '' ?>"  data-name="<?php echo $client->CompanyName; ?>" href="#remove_minor" class="text-danger js-open-minor" title="<?php echo lang('disconnect_minor') ?>"><i class="fal fa-user-times fa-md"></i></a>
                              <?php } ?>
                            </div>
                          </div>
                        </div>
                        <?php } ?>
                      </div>
                      <?php if($Supplier->parentClientId == 0) { ?>
                      <div class="d-flex justify-content-between mt-11">
                        <a href="#add_minor" data-toggle="modal" class="btn btn-primary a-hover-none shadow"><i class="fal fa-plus fa-md margin-a"></i> <?php echo lang('add_minor_client') ?> </a>
                        <!-- <a href="#" class="text-primary"><i class="fad fa-user-plus fa-2x"></i></a> -->
                      </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <?php } ?>
              </div>
              <div class="ip-modal text-start" id="remove_minor" tabindex="-1" data-backdrop="static">
                <div class="ip-modal-dialog w-600p">
                  <div class="ip-modal-content">
                    <div class="ip-modal-header d-flex justify-content-between">
                      <h4 class="ip-modal-title"><?php echo lang('remove_minor_client') ?></h4>
                      <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="">&times;</a>
                    </div>
                    <div class="ip-modal-body">
                      <form id="remove-minor-form">
                        <input type="hidden" id="minor-client-id" name="minor-client-id" value="">
                        <div class="mb-7">
                          <span class="font-weight-bold"><?php echo lang('action_cut_off_minor_client') ?> <span class="text-info" id="minor-name"></span> <?php echo lang('from_this_client') ?></span>
                        </div>
                        <div>
                          <label class="font-weight-bold"><?php echo lang('choose_status') ?></label>
                          <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="active_status" name="archive_minor" value="0" checked>
                            <label class="custom-control-label" for="active_status"><?php echo lang('active') ?></label>
                          </div>
                          <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="archive_status" name="archive_minor" value="1">
                            <label class="custom-control-label" for="archive_status"><?php echo lang('archive') ?></label>
                          </div>
                        </div>
                        <div class="mt-19">
                          <label class="font-weight-bold"><?php echo lang('client_details') ?></label>
                          <div class="d-flex justify-content-between flex-row">
                            <div class="col-md-6">
                              <div class="form-group">
                                  <label class="mb-0 mobile-lbl"><?= lang('cellular') ?> <em class="text-danger font-rubik">*</em></label>
                                  <input name="minor-ContactMobile" type="tel" class="form-control-custom" id="minor-contactMobile" value="" required pattern="<?= ClientService::MOBILE_REGEX_FRONT ?>" title="<?php echo lang('incorrect_mobile') ?>">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                  <label class="mb-0"><?= lang('email') ?> </label>
                                  <input name="minor-email" type="text" class="form-control-custom" id="minor-email" lang="en">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="text-center text-danger" id="err-msg" style="display: none">
                          <span></span>
                        </div>
                    </div>
                    <div class="ip-modal-footer">
                      <div class="ip-actions">
                        <button type="submit" name="submit" class="btn btn-primary js-minor-action"><?php echo lang('save_changes_button') ?></button>
                      </div>
                      </form>
                      <button type="button" class="btn btn-light ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="ip-modal text-start" id="add_minor" data-backdrop="static">
                <div class="ip-modal-dialog w-600p">
                  <div class="ip-modal-content">
                    <div class="ip-modal-header d-flex justify-content-between">
                      <h4 class="ip-modal-title"><?php echo lang('add_minor_client') ?></h4>
                      <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="">&times;</a>
                    </div>
                    <div class="ip-modal-body">
                      <form id="add-minor-form">
                        <input type="hidden" name="parent_client_id" id="parent_client_id" value="<?php echo $ClientId; ?>">
                        <div>
                          <label class="font-weight-bold"><?php echo lang('minor_client_details') ?></label>
                          <div class="d-flex flex-row justify-content-around pb-19">
                            <div class="custom-control custom-radio">
                              <input type="radio" class="custom-control-input" id="new_minor" name="choose_client" value="0" checked>
                              <label class="custom-control-label" for="new_minor"><?php echo lang('new_client') ?></label>
                            </div>
                            <div class="custom-control custom-radio">
                              <input type="radio" class="custom-control-input" id="exist_minor" name="choose_client" value="1">
                              <label class="custom-control-label" for="exist_minor"><?php echo lang('exist_client') ?></label>
                            </div>
                          </div>
                          <div id="client_search" class="form-group px-13 pb-19" style="display:none;">
                            <div class="alert alert-warning" role="alert">
                            <?php echo lang('add_exist_minor_client_notice') ?>
                            </div>
                            <div class="form-group">
                              <select name="add_exist_minor" data-placeholder="<?php echo lang('choose_client') ?>" class="form-control js-select2-minor"></select>
                            </div>
                            <div class="col-md-6 form-group px-0">
                                <label class="mb-0"><?php echo lang('minor_relationship') ?></label>
                                <select name="relationship" id="exist_minor_relationship" class="form-control-custom">
                                    <option value="1"><?php echo lang('father') ?></option>
                                    <option value="2"><?php echo lang('mother') ?></option>
                                    <option value="3"><?php echo lang('brother_or_sister') ?></option>
                                    <option value="4"><?php echo lang('relative') ?></option>
                                    <option value="5"><?php echo lang('other') ?></option>
                                </select>
                            </div>
                            <div class="text-center text-danger js-exist-msg" style="display: none">
                              <span></span>
                            </div>
                          </div>
                          <div class="js-new-minor">
                            <div class="d-flex justify-content-between flex-row mb-15">
                              <div class="col-md-6 form-group">
                                  <label class="mb-0"><?php echo lang('first_name') ?> <em class="text-danger font-rubik">*</em></label>
                                  <input type="text" name="minor_firstName" id="minor_firstName" class="form-control-custom" required>
                              </div>
                              <div class="col-md-6 form-group">
                                  <label class="mb-0"><?php echo lang('last_name') ?> <em class="text-danger font-rubik">*</em></label>
                                <input type="text" name="minor_lastName" id="minor_lastName" value="<?php echo !empty($Supplier->LastName) ? $Supplier->LastName : '' ?>" class="form-control-custom" required>
                              </div>
                            </div>
                            <div class="d-flex justify-content-between flex-row mb-15">
                              <div class="col-md-6 form-group">
                                <label class="mb-0"><?= lang('cellular') ?> </label>
                                <input name="minor-ContactMobile" type="tel" class="form-control-custom" id="new-minor-contactMobile" pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$" title="<?php echo lang('incorrect_mobile') ?>">
                              </div>
                              <div class="col-md-6 form-group">
                                <label class="mb-0"><?php echo lang('minor_relationship') ?></label>
                                <select name="relationship" id="minor_relationship" class="form-control-custom">
                                    <option value="1"><?php echo lang('father') ?></option>
                                    <option value="2"><?php echo lang('mother') ?></option>
                                    <option value="3"><?php echo lang('brother_or_sister') ?></option>
                                    <option value="4"><?php echo lang('relative') ?></option>
                                    <option value="5"><?php echo lang('other') ?></option>
                                </select>
                              </div>
                            </div>
                            <div class="d-flex justify-content-between flex-row mb-15">
                              <div class="col-md-6 form-group">
                                <label class="mb-0"><?php echo lang('date_birthday') ?> </label>
                                <input name="Dob" type="date" class="form-control-custom" id="Dob">
                              </div>
                              <div class="col-md-6 form-group">
                                <label class="mb-0"><?php echo lang('gender') ?> </label>
                                <select name="Gender" id="Gender" class="form-control-custom">
                                  <option value="0"><?php echo lang('gender_not_defined') ?></option>
                                  <option value="1"><?php echo lang('male') ?></option>
                                  <option value="2"><?php echo lang('female') ?></option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="text-center text-danger js-err-msg" style="display: none">
                          <span></span>
                        </div>
                    </div>
                    <div class="ip-modal-footer">
                      <div class="ip-actions">
                        <button type="submit" class="btn btn-primary js-add-minor"><?php echo lang('save_changes_button') ?></button>
                      </div>
                      </form>
                      <button type="button" class="btn btn-light ip-close" data-dismiss="modal"><?php _e('main.close') ?></button>
                    </div>
                  </div>
                </div>
              </div>

              <?php include_once 'greenPassModal.php'; ?>

            </div>
            <div class="row" >
              <div class="col-md-3 col-sm-12 order-md-1" >
                <div class="card spacebottom">
                  <a data-toggle="collapse" aria-expanded="true" style="color: black;">
                    <div class="card-header text-start">
                      <strong>
                        <i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i><i class="fas fa-ellipsis-v"></i>
                        <span><?php echo lang('last_visit') ?></span>
                          </strong>
                        </div>
                      </a>
                    <div class="collapse show">
                      <div class="card-body">
                        <?php
                        $LastClass = DB::table('classstudio_act')
                            ->select('id', 'ClassDate', 'FloorId', 'GuideId', 'ClassName', 'ClassStartTime')
                            ->where('CompanyNum','=',$CompanyNum)
                            ->where('FixClientId','=',$Supplier->id)
                            ->where('StatusCount','=','0')
                            ->where('ClassDate','<', date('Y-m-d'))
                            ->where(function($q) {
                                $q->where('ClassDate','<', date('Y-m-d'))
                                    ->Orwhere('ClassDate','=', date('Y-m-d'))->where('ClassEndTime', '<', date('H:i:s'));
                            })
                            ->orderBy('ClassDate','DESC')->first();

                        $LastClassDate = $LastClass->ClassDate ?? '';
                        if(!empty($LastClass)) {
                            $FloorInfo = DB::table('sections')->where('CompanyNum','=',$CompanyNum)->where('id', '=', $LastClass->FloorId)->first();
                            $CoachInfo = DB::table('users')->where('CompanyNum','=',$CompanyNum)->where('id','=', $LastClass->GuideId)->first();
                        }
                        ?>
                        <div class="row align-items-center">
                          <div class="col-md-12 order-md-1 text-center">
                            <?php if (!empty($LastClass)) { ?>
                            <p>
                              <strong>
                                <?php echo $LastClass->ClassName; ?>
                              </strong>
                              <br>
                              <small>
                                <?php echo $FloorInfo->Title ?? ''; ?> |
                                <?php echo with(new DateTime($LastClass->ClassDate))->format('d/m/Y'); ?>
                                <?php echo with(new DateTime($LastClass->ClassStartTime))->format('H:i'); ?> | <?php echo lang('coach_single') ?>:
                                <?php echo $CoachInfo->display_name ?? ''; ?>
                              </small>
                            </p>
                            <?php } ?>
                            <?php if (!empty($LastClassDate)) {
                            $now = time(); // or your date as well
                            $your_date = strtotime($LastClassDate);
                            $datediff = $now - $your_date;
                            $DaysCount =  round($datediff / (60 * 60 * 24));
                            if ($LastClassDate == date('Y-m-d')){
                            $TextLastDay =  lang('visited_today');
                            }
                            else if ($LastClassDate>date('Y-m-d')){
                            $TextLastDay =  '';
                            }
                            else {
                            $TextLastDay =  lang('before_single').' '.$DaysCount.' '.lang('days');
                            }
                            ?>
                            <span class="text-center font-weight-bold text-primary" style="font-size: 16px;">
                              <?php echo $TextLastDay; ?>
                            </span>
                            <?php } else { ?>
                            <span class="text-center font-weight-bold text-primary" style="font-size: 16px;"><?php echo lang('last_class_not_found') ?>
                            </span>
                            <?php } ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 order-md-1" >
                </div>
                <div class="col-md-3 col-sm-12 order-md-2" >
                </div>
              </div>
            </div>

          </div>
        </div>
        <?php else: ?>
        <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
        </div>
        <?php endif; ?>
        <script>
          var ctxtop = document.getElementById("myChartTop");
          var optiontop = {
            legend: {
              display: false,
              position: 'bottom'
            }
            ,
            tooltips: {
              mode: 'index',
              intersect: false,
            }
            ,
            responsive: true,
            scales: {
              xAxes: [{
                display: true,
                scaleLabel: {
                  display: false,
                  labelString: '<?php echo lang('month') ?>',
                }
              }
                     ],
              yAxes: [{
                display: true,
                scaleLabel: {
                  display: true,
                  labelString: '<?php echo lang('total_invoice') ?>'
                }
              }
                     ]
            }
          };
          var datatop = {
            labels: ["<?php echo lang('february') ?>", "<?php echo lang('march') ?>", "<?php echo lang('april') ?>", "<?php echo lang('may') ?>"],
            datasets: [{
              label: '<?php echo lang('current_year') ?>',
              fill: false,
              backgroundColor: [
                '#00c736',
                '#00c736',
                '#00c736'
              ],
              borderColor: [
                '#00c736',
                '#00c736',
                '#00c736'
              ],
              data: [0, 0,0],
            }
                       ,
                       {
                         label: '<?php echo lang('previous_year') ?>',
                         fill: false,
                         backgroundColor: [
                           '#17a2b8',
                           '#17a2b8',
                           '#17a2b8'
                         ],
                         borderColor: [
                           '#17a2b8',
                           '#17a2b8',
                           '#17a2b8'
                         ],
                         data: [0,0,0],
                       }
                       ,
                      ]
          };
          var myPieChartTop = new Chart(ctxtop,{
            type: 'bar',
            data: datatop,
            options: optiontop
          }
                                       );
        </script>
        <?php if (Auth::userCan('63')): ?>
        <div class="tab-pane fade show text-start" role="tabpanel" id="user-ClassRegular">
          <div class="card spacebottom">
            <div class="card-header text-start">
              <i class="fas fa-reply-all">
              </i>
              <b> <?php echo lang('setting_permanently') ?>
              </b>
            </div>
            <div class="card-body">
              <?php
              $i = '1';
              $CheckRegulars = DB::table('classstudio_dateregular')->where('ClientId', '=', $Supplier->id)
                  ->where('Status',0)
                  ->where(
                      function ($query)  {
                          $query->whereNull('EndDate')
                              ->orWhere('EndDate', '>', date('Y-m-d'));
                      }
                  )->orderBy('DayNum', 'ASC')->orderBy('ClassTime', 'ASC')->get();
              foreach ($CheckRegulars as $CheckRegular) {
                  $CheckRegularAct = ClassStudioAct::getActByRegular($CheckRegular->id);
                  if ($CheckRegularAct) {
                      $CheckRegularDate = ClassStudioDate::find($CheckRegularAct->ClassId);
                      // skip if meeting
                      if ($CheckRegularDate && $CheckRegularDate->meetingTemplateId) {
                          continue;
                      }
                  }

                  if ($CheckRegular->StatusType == '12') {
                      $RegularStatus = lang('permanent_card');
                  } else {
                      $RegularStatus = lang('waiting_card');
                  }
                  $FloorName = DB::table('sections')->where('id', '=', $CheckRegular->Floor)->first();
                  $MemberShipTypeName = DB::table('membership_type')->where('id', '=', $CheckRegular->MemberShipType)->first();
              ?>
              <div class="">
                <div class="row pl-15" >
                  <div class="col-md-2 col-sm-12 order-md-1 align-self-center">
                    <span class="align-middle">
                      <?php echo $MemberShipTypeName->Type ?? lang('no_membership_type'); ?>
                    </span>
                  </div>
                  <div class="col-md col-sm-12 order-md-2 align-self-center">
                    <span class="align-middle">
                      <?php echo $FloorName->Title; ?>
                    </span>
                  </div>
                  <div class="col-md-2 col-sm-12 order-md-3 align-self-center">
                    <span class="align-middle">
                      <?php echo $CheckRegular->ClassName; ?>
                    </span>
                  </div>
                  <div class="col-md col-sm-12 order-md-4 align-self-center">
                    <span class="align-middle">
                      <?php echo transDbVal(trim($CheckRegular->ClassDay)); ?>
                    </span>
                  </div>
                  <div class="col-md col-sm-12 order-md-5 align-self-center">
                    <span class="align-middle">
                      <?php echo $CheckRegular->ClassTime; ?>
                      <?php echo $RegularStatus; ?>
                    </span>
                  </div>
                  <div class="col-md col-sm-12 order-md-6">
                    <div class="btn-group" role="group">
                      <button id="btnGroupDrop1" type="button" class="btn btn-dark text-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-edit" aria-hidden="true">
                        </i>  <?php echo lang('edit_recurring_class') ?>
                      </button>
                      <div class="dropdown-menu text-start" aria-labelledby="btnGroupDrop1">
                        <a  class="dropdown-item" href='javascript:UpdateRegularClass("<?php echo $CheckRegular->id; ?>");' data-ajax="<?php echo $CheckRegular->id; ?>">  <?php echo lang('manage_recurring_booking') ?>
                        </a>
                        <a class="dropdown-item" style="cursor:pointer;" id="RemoveAllClass<?php echo $CheckRegular->id; ?>" data-ajax="<?php echo $CheckRegular->id; ?>"> <?php echo lang('remove_recurring_booking') ?>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <hr>
              <script>
                $("#RemoveClass<?php echo $CheckRegular->id; ?>").click(function(){
                  var ClassId = $(this).attr("data-ajax");
                  $("#RemoveClassId").val(ClassId);
                  var modalclient = $('#RemoveClassPopUp');
                  modalclient.modal('show').draggable({
                    handle: ".modal-header"
                  }
                                                     );
                }
                                                                       );
                $("#RemoveAllClass<?php echo $CheckRegular->id; ?>").click(function(){
                  var ClassId = $(this).attr("data-ajax");
                  $("#RemoveAllClassId").val(ClassId);
                  var modalclient = $('#RemoveAllClassPopUp');
                  modalclient.modal('show').draggable({
                    handle: ".modal-header"
                  }
                                                     );
                }
                                                                          );
              </script>
              <?php ++ $i; } ?>
            </div>
          </div>
        </div>
        <div class="ip-modal" id="RemoveClassPopUp"  data-backdrop="static">
          <div class="ip-modal-dialog">
            <div class="ip-modal-content text-start">
              <div class="ip-modal-header" >
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="">&times;
                </a>
                <h4 class="ip-modal-title"><?php echo lang('cancel_recurring_class') ?>
                </h4>
              </div>
              <div class="ip-modal-body" >
                <form action="ClientRemoveRegularClass" class="ajax-form clearfix text-start" autocomplete="off">
                  <input type="hidden" name="RemoveClassClientId" id="RemoveClassClientId" value="<?php echo $Supplier->id; ?>">
                  <input type="hidden" name="RemoveClassId" id="RemoveClassId" value="">
                  <input type="hidden" name="Act" value="0">
                  <div class="form-group" >
                    <label><?php echo lang('cancel_recurring_class__notice') ?>
                    </label>
                  </div>
                  <div class="alertb alert-danger" >
                  <?php echo lang('cancel_notice_class') ?>
                    <br>
                    <?php echo lang('notice_3') ?>
                  </div>
                  </div>
                <div class="ip-modal-footer">
                  <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?php echo lang('no_close_both') ?>
                  </button>
                  <div class="ip-actions">
                    <button type="submit" name="submit"  class="btn btn-danger"><?php echo lang('yes') ?>
                    </button>
                  </div>
                  </form>
              </div>
            </div>
          </div>
        </div>
        <div class="ip-modal" id="RemoveAllClassPopUp"  data-backdrop="static">
          <div class="ip-modal-dialog">
            <div class="ip-modal-content text-start">
              <div class="ip-modal-header" >
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true">&times;
                </a>
                <h4 class="ip-modal-title"><?php echo lang('cancel_recurring_class') ?>
                </h4>
              </div>
              <div class="ip-modal-body" >
                <form action="ClientRemoveRegularClass" class="ajax-form clearfix text-start" autocomplete="off">
                  <input type="hidden" name="RemoveClassClientId" id="RemoveAllClientId" value="<?php echo $Supplier->id; ?>">
                  <input type="hidden" name="RemoveClassId" id="RemoveAllClassId" value="">
                  <input type="hidden" name="Act" value="1">
                  <div class="form-group" >
                    <label><?php echo lang('cancel_all_class_notice') ?>
                    </label>
                  </div>
                  <div class="alertb alert-danger" >
                  <?php echo lang('notice_1') ?>
                    <br>
                    <?php echo lang('notice_two') ?>
                    <br>
                    <?php echo lang('notice_3') ?>
                    <br>
                    <?php echo lang('notice_4') ?>
                  </div>
                  </div>
                <div class="ip-modal-footer">
                  <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?php echo lang('no_close_both') ?>
                  </button>
                  <div class="ip-actions">
                    <button type="submit" name="submit" class="btn btn-danger"><?php echo lang('yes') ?>
                    </button>
                  </div>
                  </form>
              </div>
            </div>
          </div>
        </div>
        <!-- end Edit Task -->
        <!-- Edit DepartmentsPopup -->
        <div class="ip-modal text-start" id="RegularClassPopup" tabindex="-1">
          <div class="ip-modal-dialog BigDialog">
            <div class="ip-modal-content">
              <div class="ip-modal-header" >
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="">&times;
                </a>
                <h4 class="ip-modal-title"><?php echo lang('manage_recurring_booking') ?>
                </h4>
              </div>
              <div class="ip-modal-body" >
                <form action="EditRegularClass"  class="ajax-form clearfix" autocomplete="off">
                  <div id="resultRegularClass">
                  </div>
                  </div>
                <div class="ip-modal-footer">
                  <div class="ip-actions" id="ShowSaveRegularClass" style="display: none;">
                    <button type="submit" name="submit" class="btn btn-dark text-white">
                    <?php echo lang('save_changes_button') ?>
                    </button>
                  </div>
                  <button type="button" class="btn btn-default ip-close" data-dismiss="modal">
                  <?php echo lang('close') ?>
                  </button>
                  </form>
              </div>
            </div>
          </div>
        </div>
        <!-- end Edit DepartmentsPopup -->
        <?php endif ?>
        <?php if (Auth::userCan('66')): ?>
        <div class="tab-pane fade show text-start" role="tabpanel" id="user-Medical">
          <div class="card spacebottom">
            <div class="card-header text-start">
              <i class="fas fa-medkit">
              </i>
              <b> <?php echo lang('customer_card_medical_records') ?>
              </b>
            </div>
            <div class="card-body">
              <div class="row" >
                <div class="col-md-12 col-sm-12 order-md-1">
                  <form action="AddClientMedical" class="ajax-form clearfix text-start" autocomplete="off">
                    <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                    <div class="form-group">
                      <label><?php echo lang('customer_card_insert_dec') ?>
                      </label>
                      <textarea name="Content" class="form-control" rows="2" >
                      </textarea>
                    </div>
                    <div class="form-group">
                      <label><?php echo lang('until_date') ?>
                      </label>
                      <input name="TillDate" type="date" min="<?php echo date('d-m-Y'); ?>" value="" class="form-control">
                    </div>
                    <div class="alertb alert-info my-6 p-6"><?php echo lang('card_health_notice') ?>
                    </div>
                    <div class="form-group">
                      <button type="submit" name="submit" class="btn btn-dark text-white"><?php echo lang('save_changes_button') ?>
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card-body" id="MedicalLogList">
              <?php
$MedicalLogList = DB::table('clientmedical')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $Supplier->id)->orderBy('Dates', 'DESC')->get();
if (empty($MedicalLogList)) {echo '<div  class="text-start">'.lang('card_health_notice_none').'</div>';}
else {
?>
              <input style='position: relative;' class="form-control search" type="text" placeholder="<?php echo lang('search_button') ?>" >
              <br>
              <ul class="timeline list">
                <?php
$i = '1';
foreach ($MedicalLogList as $MedicalLog) {
@$UsersDB = DB::table('users')->where('id', '=', @$MedicalLog->UserId)->first();
$MedicalStatus = '';
if ($MedicalLog->Status=='1'){
$MedicalStatus = '<span class="text-danger">(מוסתר)</span>';
}
?>
                <li id="EmailLogLI<?php echo strip_tags(@$MedicalLog->id); ?>">
                  <div class="timeline-panel" style="font-size: 12px;">
                    <div class="timeline-body" style="min-height: 60px;">
                      <div style="padding:10px;">
                        <div class="row">
                          <div class="col-md-6 col-sm-12">
                            <b><?php echo lang('customer_card_medical_records') ?>
                              <?php echo $MedicalStatus; ?>
                            </b>
                          </div>
                          <div class="col-md-6 col-sm-12 text-end">
                            <span class="">
                              <?php if (@$MedicalLog->TillDate==''){} else { ?><?php echo lang('until_date') ?>:
                              <?php echo with(new DateTime($MedicalLog->TillDate))->format('d/m/Y'); echo ' | '; } ?>
                              <a href='javascript:UpdateMedicalClient("<?php echo $MedicalLog->id; ?>","<?php echo $MedicalLog->ClientId; ?>");' ><?php echo lang('edit') ?>
                              </a>
                            </span>
                          </div>
                        </div>
                        <hr style="margin: 0;padding: 0;margin-top: 5px;margin-bottom: 5px;">
                        <?php echo @$MedicalLog->Content; ?>
                      </div>
                    </div>
                    <div class="timeline-footer primary" style="padding: 0;margin: 0;padding: 10px;">
                      <div class="row">
                        <div class="col-md-6 col-sm-12">
                          <a class="pull-right">
                            <?php echo @$UsersDB->display_name; ?>
                          </a>
                        </div>
                        <div class="col-md-6 col-sm-12  text-end">
                          <a class="" >
                            <?php echo with(new DateTime($MedicalLog->Dates))->format('d/m/Y H:i'); ?>
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
                <?php
++ $i; }
?>
              </ul>
              <div >
                <nav>
                  <ul class="pagination float-right">
                  </ul>
                </nav>
              </div>
              <?php } ?>
            </div>
            <div class="ip-modal" id="UpdateMedicalClientPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
              <div class="ip-modal-dialog BigDialog">
                <div class="ip-modal-content text-start">
                  <div class="ip-modal-header" >
                    <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="">&times;
                    </a>
                    <h4 class="ip-modal-title"><?php echo lang('update_medical_records') ?>
                    </h4>
                  </div>
                  <div class="ip-modal-body" >
                    <form action="UpdateMedicalClient"  class="ajax-form clearfix">
                      <input type="hidden" name="ItemId">
                      <input type="hidden" name="ClientId">
                      <div id="resultMedicalClient">
                      </div>
                      </div>
                    <div class="ip-modal-footer">
                      <div class="ip-actions">
                        <button type="submit" name="submit" class="btn btn-success">
                        <?php echo lang('save_changes_button') ?>
                        </button>
                      </div>
                      <button type="button" class="btn btn-dark ip-close" data-dismiss="modal">
                      <?php echo lang('close') ?>
                      </button>
                      </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endif ?>
        <?php if (Auth::userCan('63')): ?>
        <div class="tab-pane fade show text-start" role="tabpanel" id="user-ClientAddClass">
          <div class="card spacebottom">
            <div class="card-header text-start">
              <i class="fas fa-plus">
              </i>
              <b> <?php echo lang('customer_card_embed') ?>
              </b>
            </div>
            <div class="card-body">
              <div class="row" >
                <div class="col-md-12 col-sm-12 order-md-1">
                  <form action="AddClientAddClass" class="ajax-form clearfix text-start" autocomplete="off">
                    <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
                    <div class="form-group">
                      <label><?php echo lang('customer_card_schedule_type') ?>
                      </label>
                      <select class="form-control" name="ClientAddClassType" id="ClientAddClassType">
                        <option value="1"><?php echo lang('schedule_single') ?>
                        </option>
                        <?php if (@$Supplier->Status!='2') { ?>
                        <option value="2"><?php echo lang('schedule_dates') ?>
                        </option>
                        <option value="3"><?php echo lang('schedule_perm') ?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>
                    <div id="DivClientAddClassType1" class="alertb alert-warning"><?php echo lang('one_time_notice') ?>
                    </div>
                    <div id="DivClientAddClassType2" class="alertb alert-warning" style="display: none;"><?php echo lang('booking_notice_two') ?>
                      <br>
                      <?php echo lang('class_booking_notice_6') ?>
                    </div>
                    <div id="DivClientAddClassType3" class="alertb alert-warning" style="display: none;"><?php echo lang('booking_recurring_notice') ?>
                      <br>
                      <?php echo lang('class_booking_notice_6') ?>
                    </div>
                    <div id="DivClientAddClassType2_2" style="display: none;">
                      <div class="form-group">
                        <label><?php echo lang('from_date') ?>:
                        </label>
                        <input name="ClientAddClassDate" id="ClientAddClassDate" type="date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" class="form-control">
                      </div>
                    </div>
                    <div id="DivClientAddClassType2_1" style="display: none;">
                      <div class="form-group">
                        <label><?php echo lang('until_date') ?>:
                        </label>
                        <input name="ClientAddClassTillDate" id="ClientAddClassTillDate" type="date" min="<?php echo date('Y-m-d'); ?>" value="" class="form-control">
                      </div>
                    </div>
                    <div id="DivClientAddClassType2_3" style="display: none;">
                      <div class="form-group">
                        <label><?php echo lang('status_client_profile') ?>
                        </label>
                        <select class="form-control" name="ClassStatus" id="ClassStatus">
                          <option value="12" selected><?php echo lang('active_recurring_booking') ?>
                          </option>
                          <option value="9"><?php echo lang('recurring_booking_waiting') ?>
                          </option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label><?php echo lang('choose_class') ?>
                      </label>
                      <select name="ClientAddClassId" data-placeholder="<?php echo lang('choose_class') ?>" class="form-control ClientAddClassId" style="width:100%;" >
                        <option value="">
                        </option>
                      </select>
                    </div>
                    <div class="alertb alert-info my-6 p-6 border-radius-3r" id="DivClientAddClassType1_1"><?php echo lang('customer_card_embed_notice') ?>
                      <br>
                      <?php echo lang('date_format') ?>
                    </div>
                    <div id="ClientAddClassActivites">
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <?php endif ?>
        <?php if (Auth::userCan('65')): ?>
        <div class="tab-pane fade show text-start" role="tabpanel" id="user-ClassHistory">
          <div class="card spacebottom">
            <div class="card-header text-start">
              <i class="fas fa-history">
              </i>
              <b> <?php echo lang('customer_card_classes') ?>
              </b>
            </div>
            <div class="card-body" id="DivClassHistory">
            </div>
          </div>
        </div>
        <?php endif; ?>
        <script>
          $(document).ready(function(){
            <?php if (Auth::userCan('65')): ?>
              var ClassYear = '<?php echo date('Y'); ?>';
            var ClassMonth = '<?php echo date('m'); ?>';
            var ClientId =  '<?php echo $Supplier->id; ?>';
            var url = 'action/ClassHistory.php?ClassYear='+ClassYear+'&ClassMonth='+ClassMonth+'&ClientId='+ClientId;
            $('#DivClassHistory').empty();
            $('#DivClassHistory').load(url,function(){
            }
                                      );
            <?php endif ?>
              <?php if (Auth::userCan('63')): ?>
                var ClassId = $('.ClientAddClassId').children('option:selected').val();
            $( ".ClientAddClassId" ).select2( {
              theme:"bsapp-dropdown",
              placeholder: "<?php echo lang('search_class') ?>",
              language: "<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_lang'] : 'he' ?>",
              allowClear: false,
              width: '100%',
              ajax: {
                url: 'SearchClass.php',
                dataType: 'json',
                type: 'GET',
                cache: true,
                data: function (params) {
                  return {
                    q: params.term, // search term
                    ClassId: $('#ClientAddClassType').children('option:selected').val(),
                    ClientAddClassDate: $('#ClientAddClassDate').val(),
                  };
                }
                ,
              }
              ,
              minimumInputLength: 3,
            }
                                            );
            $('.ClientAddClassId').on('change',function(){
              var ClassId = $(this).children('option:selected').val();
              var ClientId = '<?php echo $Supplier->id; ?>';
              var ClientAddClassType = $('#ClientAddClassType').val();
              var ClassStatus = $('#ClassStatus').val();
              if ($('.ClientAddClassId option:selected').length > 0 ||  ClassId!=null) {
                var urls= 'action/ClientActivityMemberShip.php?ClientId='+ClientId+'&ClassId='+ClassId+'&ClientAddClassType='+ClientAddClassType+'&ClassStatus='+ClassStatus;
                $('#ClientAddClassActivites').load(urls,function(){
                  return false;
                }
                                                  );
              }
              else {
                $( "#ClientAddClassActivites" ).empty();
              }
            }
                                     );
            $("#ClientAddClassType").change(function(){
              var Id = $(this).val();
              if (Id=='1'){
                DivClientAddClassType1.style.display = "block";
                DivClientAddClassType2.style.display = "none";
                DivClientAddClassType3.style.display = "none";
                DivClientAddClassType2_1.style.display = "none";
                DivClientAddClassType2_2.style.display = "none";
                DivClientAddClassType2_3.style.display = "none";
                $('#DivClientAddClassType1_1').html('<?php echo lang('customer_card_embed_notice') ?>' + '<br>' + '<?php echo lang('date_format')?>');
              }
              else if (Id=='2'){
                DivClientAddClassType1.style.display = "none";
                DivClientAddClassType2.style.display = "block";
                DivClientAddClassType3.style.display = "none";
                DivClientAddClassType2_1.style.display = "block";
                DivClientAddClassType2_2.style.display = "block";
                DivClientAddClassType2_3.style.display = "block";
                $('#DivClientAddClassType1_1').html('<?php echo lang('class_notice_tip') ?>');
              }
              else if (Id=='3'){
                DivClientAddClassType1.style.display = "none";
                DivClientAddClassType2.style.display = "none";
                DivClientAddClassType3.style.display = "block";
                DivClientAddClassType2_2.style.display = "block";
                DivClientAddClassType2_1.style.display = "none";
                DivClientAddClassType2_3.style.display = "block";
                $('#DivClientAddClassType1_1').html('<?php echo lang('class_notice_tip') ?>');
                $( "#ClientAddClassActivites" ).empty();
              }
              else {
                DivClientAddClassType1.style.display = "block";
                DivClientAddClassType2.style.display = "none";
                DivClientAddClassType3.style.display = "none";
                DivClientAddClassType2_1.style.display = "none";
                DivClientAddClassType2_2.style.display = "none";
                DivClientAddClassType2_3.style.display = "none";
                $('#DivClientAddClassType1_1').html('<?php echo lang('customer_card_embed_notice') ?>' + '<br>' + '<?php echo lang('date_format') ?>');
              }
            }
                                           );
            $("#ClientAddClassDate").change(function() {
              var MaxDate = $('#ClientAddClassDate').val();
              $('#ClientAddClassTillDate').prop('min', MaxDate);
              $('#ClientAddClassTillDate').val(MaxDate);
            }
                                           );
            <?php endif;
            ?>
          }
            );
        </script>
        <?php if (Auth::userCan('54')): ?>
        <div class="tab-pane fade show" role="tabpanel" id="user-activity">
            <?php
            //todo-bp-909 (cart) remove-beta
            if(in_array($CompanySettingsDash->beta, [1])) {
                include_once 'clientProfile/tabPanel/userActivityNew.php';
            } else {
                include_once 'clientProfile/tabPanel/userActivity.php';
            } ?>
        </div>
<?php endif ?>
<?php if (Auth::userCan('75') || $editLeadsPermission): ?>
<div class="tab-pane fade text-start" role="tabpanel" id="user-sendit">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-share-square fa-fw">
      </i>
      <b><?php echo lang('send_message') ?>
      </b>
    </div>
    <div class="card-body">
      <div class="alertb alert-info my-6 p-6 border-radius-3r" style="font-size: 12px;">
        <strong><?php echo lang('option_to_use_params_inside_message') ?>
        </strong>
        <br>
        <strong>[[<?php echo lang('full_name') ?>]]
        </strong> <?php echo lang('will_be_changed_in_client_full_name') ?>
        <br>
        <strong>[[<?php echo lang('first_name') ?>]]
        </strong> <?php echo lang('will_be_replaced_in_private_name') ?>
        <br>
        <strong>[[<?php echo lang('full_representative_name') ?>]]
        </strong> <?php echo lang('will_be_replaced_in_representative_fullname') ?>
        <br>
        <strong>[[<?php echo lang('representative_name') ?>]]
        </strong> <?php echo lang('will_be_replaced_in_representative_firstname') ?>
        <br>
        <strong>[[<?php echo lang('studio_name') ?>]]
        </strong> <?php echo lang('will_be_replaced_in_studio_name') ?>
        <br>
      </div>
      <div class="row">
        <div class="col-12">
          <div class="btn-group">
            <button id="SavedNotes" type="button" class="btn btn-dark text-white dropdown-toggle text-start" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo lang('card_msg_format') ?>
            </button>
            <div class="dropdown-menu text-start dropdown-menu-right " aria-labelledby="SavedNotes">
              <?php
$TextSaveds = DB::table('textsaved')->where('CompanyNum', $CompanyNum)->where('Status', '0')->get();
foreach ($TextSaveds as $TextSaved) {
echo '<a class="dropdown-item" href="javascript:void(0)" onclick="SetSavedMessage('.$TextSaved->id.', '.$Supplier->id.')">'.$TextSaved->Title.'</a>';
}
?>
            </div>
          </div>
        </div>
        <br>
        <br>
      </div>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="form-group">
            <form action="SendNotificationClient" class="ajax-form clearfix"  autocomplete="off">
              <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
              <input type="hidden" name="TypeSend" value="0">
              <textarea name="Message" id="PushMessage" class="form-control" rows="3"  required maxlength="200">
              </textarea>
              <div style="padding-top:10px;" >
                <button type="submit" name="submit"  id="PushSubmit" class="btn btn-dark text-white "><?php echo lang('send_msg_push') ?>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <hr>
      <?php if (Auth::userCan('76')): ?>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="form-group">
            <form action="SendNotificationClient" class="ajax-form clearfix"  autocomplete="off">
              <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
              <input type="hidden" name="TypeSend" value="1">
              <textarea name="Message" id="Message" class="form-control" rows="3"  required>
              </textarea>
              <div style="padding-top:10px;" >
                <button type="submit" name="submit"  id="SmsSubmit" class="btn btn-dark text-white"><?php echo lang('send_msg_sms') ?>
                  <span  style="font-size: 12px;">(
                    <span id="count"><?php echo lang('zero_characters') ?>
                    </span>)
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <hr>
      <?php endif ?>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <form action="SendNotificationClient" class="ajax-form clearfix">
            <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
            <input type="hidden" name="TypeSend" value="2">
            <div class="form-group">
              <input type="text" name="Subject" id="emailsubject" placeholder="<?php echo lang('subject') ?>" class="form-control">
            </div>
            <div class="form-group">
              <textarea class="form-control summernote" id="emailmessage" name="Message" placeholder="<?php echo lang('class_send_message') ?>" rows="5">
              </textarea>
            </div>
            <button type="submit" class="btn btn-dark text-white"><?php echo lang('send_msg_email') ?>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif ?>
<?php if (Auth::userCan('69')): ?>
  <div  class="tab-pane fade" role="tabpanel" id="user-account">
      <?php
      //todo-bp-909 (cart) remove-beta
      if (!in_array($SettingsInfo->beta, [1])) {
          include_once 'clientProfile/tabPanel/userAccount.php';
      } else {
          include_once 'clientProfile/tabPanel/userAccountNew.php';
      } ?>



  <div  class="tab-pane fade" role="tabpanel" id="user-accountmoney">
    <?php
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");
$next_year = date($_REQUEST["year"], strtotime('+1 year'));
$StartDate = $_REQUEST["year"].'-01-01';
$EndDate = $_REQUEST["year"].'-12-31';
$DocGetsC = DB::table('docs_payment')->where('CompanyNum' ,'=', $CompanyNum)->where('ClientId','=', $Supplier->id)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'DESC')->get();
$DocCountC = count($DocGetsC);
?>
    <div class="card spacebottom">
      <div class="card-header text-start">
        <i class="fas fa-shekel-sign">
        </i>
        <strong><?php echo lang('detailed_receipt') ?> ::
        </strong>
        <?php echo @$DocCountC; ?>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3 col-sm-12">
            <span>
              <select class="form-control" id="CDate" onChange="SetYear(this.value);">
                <?php
$ThisYear = date('Y');
for ($x = $SettingsInfo->StartYear; $x <= $ThisYear; $x++) {
if ($x == $_REQUEST["year"]) {echo "<option selected>$x</option>";}	else {echo "<option>$x</option>";}
}
?>
              </select>
              <script type="text/javascript" charset="utf-8">
                function SetYear(value)
                {
                  window.location.href = 'ClientProfile.php?u=<?php echo @$Supplier->id; ?>&year='+value+'#user-accountmoney';
                }
              </script>
              </div>
          </div>
          <hr>
          <?php if ($DocCountC != '0') { ?>
          <table class="table table-bordered table-hover table-responsive-md text-start wrap Carteset"   cellspacing="0" width="100%" id="AccountsTable">
            <thead class="thead-dark">
              <tr style="background-color:#bce8f1;">
                <th  style="text-align:start;">#
                </th>
                <th  style="text-align:start;"><?php echo lang('actions') ?>
                </th>
                <th  style="text-align:start;"><?php echo lang('date') ?>
                </th>
                <th  style="text-align:start;"><?php echo lang('table_value_date') ?>
                </th>
                <th  style="text-align:start;"><?php echo lang('type') ?>
                </th>
                <th  style="text-align:start;"><?php echo lang('detail') ?>
                </th>
                <th  style="text-align:start;"><?php echo lang('payment_date') ?>
                </th>
                <th  style="text-align:start;"><?php echo lang('summary') ?>
                </th>
              </tr>
            </thead>
            <tbody>
              <?php
$BalanceTotal = '0.00';
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
// $TashType = array(
//   "בתשלום רגיל"=>"1",
//   ""=>"2",
//   "בתשלומי קרדיט"=>"3",
//   "בחיוב נדחה"=>"4",
//   "באחר"=>"5"
// );
$TashType = array(
  "1" => lang('regular_payment'),
  "2" => "",
  "3" => lang('credit_payments_carteset'),
  "4" => lang('deferred_debit_carteset'),
  "5" => lang('other_way_carteset')
);
$d = '1';
foreach ($DocGetsC as $DocGet) {
$DocsTables = DB::table('docstable')->where('CompanyNum' ,'=', $CompanyNum)->where('id','=',$DocGet->TypeDoc)->first();
$DocsInfo = DB::table('docs')->where('CompanyNum' ,'=', $CompanyNum)->where('id','=',$DocGet->DocsId)->first();
if(!is_object($DocGet) || !is_object($DocsTables) || !is_object($DocsInfo)) continue;
if ($DocGet->TypePayment == '1') {$DocPaymentNotes = '';}
elseif ($DocGet->TypePayment == '2') {$DocPaymentNotes = lang('check_number').' '.@$DocGet->CheckNumber.' '.lang('bank_code').' '.@$DocGet->CheckBankCode.' '.lang('account_number').' '.@$DocGet->CheckBank.' '.lang('branch_id').@$DocGet->CheckBankSnif;}
elseif ($DocGet->TypePayment == '3') {$DocPaymentNotes = @$DocGet->BrandName.' '.lang('ends_at').' '.@$DocGet->L4digit.' '.lang('in').' '.@$DocGet->Payments.' '.lang('payments').array_search(@$DocGet->tashType, $TashType).' '.lang('confirmation_number').' '.@$DocGet->ACode;}
elseif ($DocGet->TypePayment == '4') {$DocPaymentNotes = lang('ref_number').' '.@$DocGet->BankNumber;}
elseif ($DocGet->TypePayment == '5') {$DocPaymentNotes = '';}
elseif ($DocGet->TypePayment == '6') {$DocPaymentNotes = '';}
elseif ($DocGet->TypePayment == '7') {$DocPaymentNotes = '';}
elseif ($DocGet->TypePayment == '8') {$DocPaymentNotes = '';}
elseif ($DocGet->TypePayment == '9') {$DocPaymentNotes = '';}
else {$DocPaymentNotes = lang('without_details');}
?>
              <tr class="active">
                <td>
                  <?php echo $d; ?>
                </td>
                <td style="vertical-align: middle;">
                  <?php DocumentGroupButton($DocGet->TypeNumber,$DocsTables->id,$DocGet->TypeHeader,$DocsInfo->PayStatus, $DocsInfo); ?>
                </td>
                <td style="vertical-align: middle;">
                  <?php echo with(new DateTime($DocGet->UserDate))->format('d/m/Y'); ?>
                </td>
                <td style="vertical-align: middle;">
                  <?php echo with(new DateTime($DocGet->Dates))->format('d/m/Y'); ?>
                </td>
                <td style="vertical-align: middle;">
                  <?php echo $TypePayment[$DocGet->TypePayment]; ?>
                </td>
                <td style="vertical-align: middle;">
                  <?php echo $DocPaymentNotes; ?>
                </td>
                <td style="vertical-align: middle;">
                  <?php echo with(new DateTime($DocGet->CheckDate))->format('d/m/Y'); ?>
                </td>
                <?php
$BalanceTotal += $DocGet->Amount;
?>
                <td style="vertical-align: middle;">
                  <?php echo number_format($DocGet->Amount, 2); ?> ₪
                </td>
              </tr>
              <?php
++$d; }
?>
            </tbody>
            <tfoot>
              <td colspan="7">
                <strong><?php echo lang('total') ?>:
                </strong>
              </td>
              <td>
                <strong>
                  <?php echo number_format($BalanceTotal, 2); ?> ₪
                </strong>
              </td>
            </tfoot>
          </table>
          <?php } else {echo '<div class="row text-start p-3" ><strong>'.lang('no_data').'</strong></div>';} ?>
        </div>
      </div>
    </div>
    <?php endif ?>
    <?php if (Auth::userCan('78') || $editLeadsPermission ): ?>
    <div class="tab-pane fade" role="tabpanel" id="user-settings">
      <div class="card spacebottom">
        <div class="card-header text-start">
          <i class="far fa-edit fa-fw">
          </i>
          <strong><?php echo lang('edit_customer_card') ?>
          </strong>
        </div>
        <div class="card-body">
          <form action="EditClient" id="js-UserSettings-EditClient" class="ajax-form text-start" autocomplete="off">
            <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label><?php echo lang('branch') ?>
                  </label>
                  <select class="form-control text-start" name="Brands" id="BrandsTypeClass" >
                    <?php
$b = '1';
$ClassTypes = DB::table('brands')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();
if (!empty($ClassTypes)){
foreach ($ClassTypes as $ClassType) { ?>
                    <option value="<?php echo $ClassType->id; ?>"
                            <?php if ($Supplier->Brands==$ClassType->id){ echo 'selected';} else {} ?>>
                    <?php echo $ClassType->BrandName ?>
                    </option>
                  <?php ++$b; } } else { ?>
                  <option value="0"><?php echo lang('primary_branch') ?>
                  </option>
                  <?php } ?>
                  </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><?php echo lang('rfid') ?>
                </label>
                <input type="text" class="form-control" name="RFID" id="RFIDC" value="<?php echo @$Supplier->RFID; ?>" onkeypress='validate(event)' readonly>
              </div>
            </div>
            </div>
          <?php
$FirstName = str_replace('"',"``",@$Supplier->FirstName);
$FirstName = str_replace("'","`",@$FirstName);
$LastName = str_replace('"',"``",@$Supplier->LastName);
$LastName = str_replace("'","`",@$LastName);
$Company = str_replace('"',"``",@$Supplier->Company);
$Company = str_replace("'","`",@$Company);
?>
          <div class="row">
            <div class="col-md-3">
              <div class="form-group">
                <label><?php echo lang('first_name') ?>
                </label>
                <input type="text" class="form-control" name="FirstName" id="FirstName" value="<?php echo htmlspecialchars(@$Supplier->FirstName) ?>">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label><?php echo lang('last_name') ?>
                </label>
                <input type="text" class="form-control" name="LastName" id="LastName" value="<?php echo htmlspecialchars(@$Supplier->LastName) ?>">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label><?php echo lang('name_to_receipt') ?>
                </label>
                <input type="text" class="form-control" name="Company" id="Company" value="<?php echo htmlentities(@$Supplier->Company); ?>">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label><?php echo lang('customer_type') ?>
                </label>
                <select name="BusinessType" id="BusinessType" class="form-control">
                  <?php	$BusinessTypes = DB::table('businesstype')->get();	?>
                  <?php	foreach ($BusinessTypes as $BusinessType) {	?>
                  <option value="<?php echo $BusinessType->id; ?>"
                          <?php if (@$Supplier->BusinessType==$BusinessType->id) { echo 'selected'; } else {} ?>>
                  <?php echo $BusinessType->Type; ?>
                  </option>
                <?php	}	?>
                </select>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label><?php echo lang('id') ?>
              </label>
              <input type="text" class="form-control" name="CompanyId" id="CompanyId" onkeypress='validate(event)' value="<?php echo @$Supplier->CompanyId ?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?php echo lang('cell_table') ?>
              </label>
              <input name="ContactMobile" type="text" class="form-control"  id="ContactMobile" onkeypress='validate(event)' value="<?php echo $Supplier->ContactMobile ?>" <?php echo $Supplier->parentClientId == 0 ? "required" : '' ?> readonly pattern="^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$" title="<?php echo lang('phone_example') ?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?php echo lang('telephone') ?>
              </label>
              <input name="ContactPhone" type="text" class="form-control" id="ContactPhone" onkeypress='validate(event)' value="<?php echo @$Supplier->ContactPhone ?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?php echo lang('fax_single') ?>
              </label>
              <input name="ContactFax" type="text" class="form-control" id="ContactFax" onkeypress='validate(event)' value="<?php echo @$Supplier->ContactFax ?>">
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label><?php echo lang('email') ?>
              </label>
              <input name="Email" type="text" class="form-control"  id="Email" value="<?php echo @$Supplier->Email ?>" >
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?php echo lang('site') ?>
              </label>
              <input name="WebSite" type="text" class="form-control" id="WebSite" onkeypress='validate(event)' value="<?php echo @$Supplier->WebSite ?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?php echo lang('date_birthday') ?>
              </label>
              <input name="Dob" type="date" class="form-control" id="Dob" value="<?php if(empty($Supplier->Dob) || $Supplier->Dob=='0000-00-00'){} else { echo $Supplier->Dob; } ?>">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label><?php echo lang('gender') ?>
              </label>
              <select name="Gender" id="Gender" class="form-control">
                <option value="1"
                        <?php if (@$Supplier->Gender=='1') { echo 'selected'; } else {} ?>><?php echo lang('male') ?>
                </option>
              <option value="2"
                      <?php if (@$Supplier->Gender=='2') { echo 'selected'; } else {} ?>><?php echo lang('female') ?>
              </option>
            </select>
        </div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6 col-sm-12 AddressCols">
        <div class="form-group">
          <label><?php echo lang('city') ?>
          </label>
          <select class="CitiesSelect" name="City" id="CitiesSelect">
          </select>
        </div>
      </div>
      <div class="col-md-6 col-sm-12 AddressCols">
        <div class="form-group">
          <label><?php echo lang('street') ?>
          </label>
          <select class="StreetSelect" name="Street" id="StreetSelect">
          </select>
        </div>
      </div>
      <div class="col-md-4 col-sm-12 NoAddress" style="display: none;">
        <div class="form-group">
          <label><?php echo lang('street_name') ?>
          </label>
          <input name="StreetH" type="text" class="form-control" id="StreetH" value="<?php echo htmlentities(@$Supplier->StreetH); ?>">
        </div>
      </div>
      <?php
$CitiesSelect = DB::table('cities')->where('CityId', @$Supplier->City)->first();
$StreetSelect = DB::table('street')->where('id', @$Supplier->Street)->first();
if ($Supplier->Street && $Supplier->Street == '99999999') {
$StreetName = lang('without_street');
} else {
$StreetName = $StreetSelect->Street ?? '';
}
?>
      <?php if ($Supplier->City && $Supplier->City != 0) { ?>
      <script>
        $(".CitiesSelect").append(`<option value="<?= $Supplier->City ?>" selected><?= $CitiesSelect->City ?? '' ?></option>`).trigger('change');
      </script>
      <?php } ?>
      <?php if ($Supplier->Street && $Supplier->Street != 0) { ?>
      <script>
        $(".StreetSelect").append(`<option value="<?= $Supplier->Street ?>" selected><?= $StreetName ?? '' ?></option>`).trigger('change');
      </script>
      <?php } ?>
    </div>
    <div class="row">
      <div class="col-md-2 col-sm-12">
        <div class="form-group">
          <label><?php echo lang('home_number') ?>
          </label>
          <input name="Number" type="number" class="form-control" id="Number" value="<?php echo $Supplier->Number ?? '' ?>">
        </div>
      </div>
      <div class="col-md-2 col-sm-12">
        <div class="form-group">
          <label><?php echo lang('zip_code') ?>
          </label>
          <input name="PostCode" type="number" class="form-control" id="PostCode" value="<?php echo $Supplier->PostCode ?? '' ?>">
        </div>
      </div>
      <div class="col-md-2 col-sm-12">
        <div class="form-group">
          <label><?php echo lang('mailbox') ?>
          </label>
          <input name="POBox" type="text" class="form-control" id="POBox" value="<?php echo $Supplier->POBox ?? '' ?>">
        </div>
      </div>
      <div class="col-md-2 col-sm-12">
        <div class="form-group">
          <label><?php echo lang('apartmant') ?>
          </label>
          <input name="Flat" type="number" class="form-control" id="Flat" value="<?php echo $Supplier->Flat ?? '' ?>">
        </div>
      </div>
      <div class="col-md-2 col-sm-12">
        <div class="form-group">
          <label><?php echo lang('floor') ?>
          </label>
          <input name="Floor" type="number" class="form-control" id="Floor" value="<?php echo $Supplier->Floor ?? '' ?>">
        </div>
      </div>
      <div class="col-md-2 col-sm-12">
        <div class="form-group">
          <label><?php echo lang('entrance') ?>
          </label>
          <input name="Entry" type="text" class="form-control" id="Entry" value="<?php echo $Supplier->Entry ?? '' ?>">
        </div>
      </div>
    </div>
    <hr>
    <div class="form-group">
      <label><?php echo lang('recieve_mail') ?>
      </label>
      <div class="row">
        <div class="col-md-6 col-sm-12">
          <div class="input-group">
            <div class="input-group-icon" style="padding:5px;text-align: center; vertical-align: middle;">
              <center>
                <input type="checkbox" type="checkbox" id="GetEmail" name="GetEmail" value="0" style="width: 20px; height: 20px;text-align: center;margin:0;padding:0; text-align: center; vertical-align: middle;margin-top: 3px;margin-left: 3px;"
                       <?php echo ($Supplier->GetEmail == 0) ? 'checked' : '' ?>>
              </center>
            </div>
            <div class="input-group-area">
              <label for="GetEmail" class="text-start" style="text-align: start;padding: 0;margin:0;padding:5px;width: 100%;">Email
              </label>
            </div>
          </div>
        </div>
        <div class="col-md-6 col-sm-12">
          <div class="input-group">
            <div class="input-group-icon" style="padding:5px;text-align: center; vertical-align: middle;">
              <center>
                <input type="checkbox" type="checkbox" id="GetSMS" name="GetSMS" value="0" style="width: 20px; height: 20px;text-align: center;margin:0;padding:0; text-align: center; vertical-align: middle;margin-top: 3px;margin-left: 3px;"
                       <?php echo ($Supplier->GetSMS == 0) ? 'checked' : '' ?>>
              </center>
            </div>
            <div class="input-group-area">
              <label for="GetSMS" class="text-start" style="text-align: start;padding: 0;margin:0;padding:5px;width: 100%;">SMS
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <hr>
    <div class="form-group">
      <label><?php echo lang('certif_app') ?>
      </label>
      <div class="row">
        <div class="col-md-6 col-sm-12">
          <div class="input-group">
            <div class="input-group-icon" style="padding:5px;text-align: center; vertical-align: middle;"
                 <center>
            <input type="checkbox" type="checkbox" id="GetEmail" name="Medical" value="1" style="width: 20px; height: 20px;text-align: center;margin:0;padding:0; text-align: center; vertical-align: middle;margin-top: 3px;margin-left: 3px;"
                   <?php if (@$Supplier->Medical=='1') { echo 'checked'; } else {} ?>>
            </center>
        </div>
        <div class="input-group-area">
          <label for="Medical" class="text-start" style="text-align: start;padding: 0;margin:0;padding:5px;width: 100%;"><?php echo lang('health_declaration') ?>
          </label>
        </div>
      </div>
    </div>
    <div class="col-md-6 col-sm-12">
      <div class="input-group">
        <div class="input-group-icon" style="padding:5px;text-align: center; vertical-align: middle;"
             <center>
        <input type="checkbox" type="checkbox" id="GetSMS" name="Takanon" value="1" style="width: 20px; height: 20px;text-align: center;margin:0;padding:0; text-align: center; vertical-align: middle;margin-top: 3px;margin-left: 3px;"
               <?php if (@$Supplier->Takanon=='1') { echo 'checked'; } else {} ?>>
        </center>
    </div>
    <div class="input-group-area">
      <label for="Takanon" class="text-start" style="text-align: start;padding: 0;margin:0;padding:5px;width: 100%;"><?php echo lang('terms') ?>
      </label>
    </div>
  </div>
</div>
</div>
</div>
<hr>
<div class="row">
  <div class="col-md-6 col-sm-12">
    <div class="form-group">
      <label><?php echo lang('vat') ?>
      </label>
      <select name="Vat" id="Vat" class="form-control">
        <option value="0"
                <?php if (@$Supplier->Vat=='0') { echo 'selected'; } else {} ?>><?php echo lang('yes') ?>
        </option>
      <option value="1"
              <?php if (@$Supplier->Vat=='1') { echo 'selected'; } else {} ?>><?php echo lang('no') ?>
      </option>
    </select>
</div>
</div>
<div class="col-md-6 col-sm-12">
  <div class="form-group">
    <label><?php echo lang('payment_condition') ?>
    </label>
    <select name="PaymentRole" id="PaymentRole" class="form-control">
      <?php	$PaymentRoles = DB::table('paymentrole')->get();	?>
      <?php	foreach ($PaymentRoles as $PaymentRole) {	?>
      <option value="<?php echo $PaymentRole->id; ?>"
              <?php if (@$Supplier->PaymentRole==$PaymentRole->id) { echo 'selected'; } else {} ?>>
      <?php echo $PaymentRole->Role; ?>
      </option>
    <?php	}	?>
    </select>
</div>
</div>
</div>
<hr>
<div class="form-group">
  <label><?php echo lang('paying_customer') ?></label>
  <select name="PayClientId" id="select2ClientDesk" data-placeholder="<?php echo lang('select_paying_customer') ?>" class="form-control select2ClientDesk" style="width:100%;" >
  </select>
</div>
<?php if (@$Supplier->PayClientId == '0') { ?>
<script>
  $("#select2ClientDesk").append('<option value="0" selected >'+ '<?php echo lang('without_paying_cust') ?>' +'</option>').trigger('change');
</script>
<?php } else {
$CheckClientInfo = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $Supplier->PayClientId)->first();
?>
<script>
  $("#select2ClientDesk").append('<option value="<?php echo @$CheckClientInfo->id; ?>" selected ><?php echo htmlentities(@$CheckClientInfo->CompanyName); ?></option>');
</script>
<?php } ?>
<hr>

        <!-- Modal Paying Customer check -->
        <div class="modal fade" id="js-kevaExistPopup" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?= lang('attention_app_out') ?></h5>
                    </div>
                    <div class="modal-body">
                        <?= lang('paying_customer_subs') ?>
                    </div>
                    <div class="modal-footer">
                        <button onclick="$('select[name=PayClientId]').val('<?= $Supplier->PayClientId ?>').change();" class="btn btn-light" data-dismiss="modal"><?= lang('close') ?></button>
                    </div>
                </div>
            </div>
        </div>

<div class="form-group ">
    <label> <?php echo lang('cust_rank') ?> </label>
    <div>
            <select class="form-control select2-selection select2Rank text-start"
                    data-placeholder="<?php if (empty ($myArray)) {echo (lang('choose'));} ?>"
                    name="ClassLevel[]" id="ClassLevel"   multiple="multiple" data-select2order="true">
                              <?php
                $myArray = (new Rank())->getRankNamesArrayByClientId($Supplier->id);
                $ClientLevels = DB::table('clientlevel')->where('CompanyNum', '=', $CompanyNum)->get();
                foreach ($ClientLevels as $ClientLevel) {
                    $selected = (in_array($ClientLevel->Level, $myArray)) ? ' selected="selected"' : '';
                    ?>
                    <option value="<?php echo $ClientLevel->id; ?>" <?php if(isset($selected)){echo $selected;} ?> ><?php echo $ClientLevel->Level; ?></option>
                    <?php
                }
                ?>
            </select>
    </div>
</div>
<div class="form-group">
  <label><?php echo lang('customer_remark') ?>
  </label>
  <textarea name="Remarks" id="Remarks" class="form-control">
    <?php echo $Supplier->Remarks; ?>
  </textarea>
</div>
<hr>
<?php if (Auth::userCan('80') || $editLeadsPermission): ?>
<div class="form-group">
  <label><?php echo lang('status_table') ?>
  </label>
  <select onchange="clientStatusChange(null,2)" name="Status" id="Status" class="form-control">
    <option value="0"
            <?php if (@$Supplier->Status=='0') { echo 'selected'; } else {} ?>><?php echo lang('active') ?>
    </option>
  <option value="1"
          <?php if (@$Supplier->Status=='1') { echo 'selected'; } else {} ?>><?php echo lang('archive') ?>
  </option>
<?php if (@$Supplier->Status=='2') { ?>
<option value="2"
        <?php if (@$Supplier->Status=='2') { echo 'selected'; } else {} ?>><?php echo lang('interested_single') ?>
</option>
<?php } ?>
</select>
</div>
<?php else: ?>
<input type="hidden" name="Status" value="<?php echo @$Supplier->Status?> ">
<?php endif ?>
<hr>
<div class="form-group">
  <button type="submit" name="submit" class="btn btn-success shadow">
    <?php echo lang('save_changes_button') ?>
  </button>
</div>
</form>
</div>
</div>
</div>
<?php endif ?>
<?php if (Auth::userCan('68')): ?>
<div class="tab-pane fade" role="tabpanel" id="user-crm">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-sticky-note fa-fw">
      </i>
      <strong><?php echo lang('customer_card_phone_records') ?>
      </strong>
    </div>
    <div class="card-body" id="NotesList">
      <form action="AddCRM" class="ajax-form text-start" >
        <input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">
        <div class="form-group">
          <textarea name="Remarks" id="Remarks1" class="form-control" rows="3" >
          </textarea>
        </div>
        <hr>
        <div class="form-group">
          <label><?php echo lang('phone_record_comment') ?>
          </label>
          <select name="StarIcon" class="form-control">
            <option value="0"><?php echo lang('no') ?>
            </option>
            <option value="1"><?php echo lang('yes') ?>
            </option>
          </select>
        </div>
        <div class="form-group">
          <label><?php echo lang('until_date') ?>
          </label>
          <input name="TillDate" type="date" min="<?php echo date('d-m-Y'); ?>" value="" class="form-control">
        </div>
        <div class="alertb alert-info my-6 p-6 border-radius-3r"><?php echo lang('phone_records_notice') ?>
        </div>
        <div class="form-group">
          <button type="submit" name="submit" class="btn btn-dark text-white">
          <?php echo lang('save_changes_button') ?>
          </button>
        </div>
      </form>
      <?php
$NotesList = DB::table('clientcrm')->where('ClientId', '=', $Supplier->id)->where('CompanyNum' ,'=', $CompanyNum)->orderBy('Dates', 'DESC')->get();
if (!empty($NotesList)) {
?>
      <hr>
      <div class="row" >
        <div class="col-12">
          <input style='position: relative;' class="form-control search" type="text" placeholder="<?php echo lang('search_button') ?>" >
          <br>
          <ul class="timeline list">
            <?php
$i = '1';
foreach ($NotesList as $ClassAct) {
$UsersName = DB::table('users')->where('CompanyNum' ,'=', $CompanyNum)->where('id', '=', $ClassAct->User)->first();
$CrmStatus = '';
if ($ClassAct->Status=='1'){
$CrmStatus = '<span class="text-danger">'.lang('hidden_clientprofile').'</span>';
}
?>
            <li>
              <div class="timeline-panel" style="font-size: 12px;">
                <div class="timeline-body" style="min-height: 60px;">
                  <div style="padding:10px;">
                    <div class="row">
                      <div class="col-md-6 col-sm-12">
                        <b>
                          <?php if (@$ClassAct->StarIcon=='1'){ ?>
                          <i class="fas fa-star-of-life">
                          </i>
                          <?php } ?> <?php echo lang('customer_card_phone_records') ?>
                          <?php echo $CrmStatus; ?>
                        </b>
                      </div>
                      <div class="col-md-6 col-sm-12 text-end">
                        <span class="">
                          <?php if (@$ClassAct->TillDate==''){} else { ?><?php echo lang('until_date') ?>:
                          <?php echo with(new DateTime($ClassAct->TillDate))->format('d/m/Y'); echo ' | ';  } ?>
                          <a href='javascript:UpdateCRMClient("<?php echo $ClassAct->id; ?>","<?php echo $ClassAct->ClientId; ?>");' ><?php echo lang('edit') ?>
                          </a>
                        </span>
                      </div>
                    </div>
                    <hr style="margin: 0;padding: 0;margin-top: 5px;margin-bottom: 5px;">
                    <?php echo @$ClassAct->Remarks; ?>
                  </div>
                </div>
                <div class="timeline-footer primary" style="padding: 0;margin: 0;padding: 10px;">
                  <div class="row">
                    <div class="col-md-6 col-sm-12">
                      <a class="pull-right">
                        <?php echo @$UsersName->display_name; ?>
                      </a>
                    </div>
                    <div class="col-md-6 col-sm-12  text-end">
                      <a class="" >
                        <?php echo with(new DateTime($ClassAct->Dates))->format('d/m/Y H:i'); ?>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <?php
++ $i; }
?>
          </ul>
          <div >
            <nav>
              <ul class="pagination float-right">
              </ul>
            </nav>
          </div>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
<?php endif ?>
<div class="ip-modal" id="UpdateCRMClientPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="ip-modal-dialog BigDialog">
    <form  action="UpdateCRMClient"  class="ajax-form clearfix ip-modal-content text-start"  >
      <div class="ip-modal-header d-flex  justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('update_phone_records') ?>
        </h4>
        <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="">&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <input type="hidden" name="ItemId">
        <div id="resultCRMClient">
        </div>
      </div>
      <div class="ip-modal-footer d-flex  justify-content-between">
        <div class="ip-actions d-flex">
          <button type="submit" name="submit" class="btn btn-success">
          <?php echo lang('save_changes_button') ?>
          </button>
        </div>
        <button type="button" class="btn btn-dark ip-close" data-dismiss="modal">
        <?php echo lang('close') ?>
        </button>
      </div>
    </form>
  </div>
</div>
<?php if (Auth::userCan('133')): ?>
<div class="tab-pane fade" role="tabpanel" id="user-callcrm">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-headset fa-fw">
      </i>
      <strong><?php echo lang('center_voice') ?>
      </strong>
    </div>
    <div class="card-body" id="NotesList">
      <form action="LogCallRecord" class="ajax-form text-start" >
        <input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label><?php echo lang('from_date') ?>
              </label>
              <input name="StartDate" id="CallStartDate" type="date" class="form-control" value="">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label><?php echo lang('until_date') ?>
              </label>
              <input name="EndDate" id="CallEndDate" min="" type="date" class="form-control" value="">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label><?php echo lang('start_hour') ?>
              </label>
              <input name="StartTime" id="CallStartTime" type="time" class="form-control" value="">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label><?php echo lang('end_hour') ?>
              </label>
              <input name="EndTime" id="CallEndTime" type="time" class="form-control" value="">
            </div>
          </div>
        </div>
        <div class="form-group">
          <button type="submit" name="submit" class="btn btn-dark text-white"><?php echo lang('show_results') ?>
          </button>
        </div>
      </form>
      <hr>
      <div id="CallLogTable">
        <table class="table table-bordered table-hover dt-responsive text-start"   cellspacing="0" width="100%">
          <thead class="thead-dark">
            <tr>
              <th style="text-align:start;">#
              </th>
              <th style="text-align:start;"><?php echo lang('date') ?>
              </th>
              <th style="text-align:start;"><?php echo lang('hour') ?>
              </th>
              <th style="text-align:start;"><?php echo lang('type') ?>
              </th>
              <th style="text-align:start;"><?php echo lang('call_duration') ?>
              </th>
              <th style="text-align:start;"><?php echo lang('recording_single') ?>
              </th>
              <th style="text-align:start;"><?php echo lang('representative') ?>
              </th>
              <th style="text-align:start;"><?php echo lang('status_table') ?>
              </th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php endif ?>
<?php if (Auth::userCan('67')): ?>
<div class="tab-pane fade" role="tabpanel" id="user-task">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-calendar-check fa-fw">
      </i>
      <strong><?php echo lang('tasks') ?>
      </strong>
    </div>
    <div class="card-body">
      <div class="row text-start">
        <div class="col-md-12 col-sm-12 order-1">
            <button id="js-new-task" class="btn btn-dark text-white">
                <i class="fas fa-calendar-check">
                </i> <?php echo lang('new_task_button') ?>
            </button>
        </div>
      </div>
      <?php
$TasksLogs = DB::table('calendar')->where('ClientId', '=', $Supplier->id)->orderBy('StartDate', 'DESC')->orderBy('StartTime', 'DESC')->get();
if (!empty($TasksLogs)) {
?>
      <hr>
      <div id="TasksLog">
        <input style='position: relative;' class="form-control search" type="text" placeholder="<?php echo lang('search_button') ?>" >
        <br>
        <ul class="timeline list">
          <?php
$i = '1';
foreach ($TasksLogs as $ClassAct) {
$UsersName = DB::table('users')->where('id', '=', $ClassAct->AgentId)->first();
$UsersNames = DB::table('users')->where('id', '=', $ClassAct->User)->first();
?>
          <?php
if(@$ClassAct->Status == '2') {
$TaskColor = '#eec25d';
$FontAwesomeWithColor = '<i class="fas fa-calendar-times" style="color:'.$TaskColor.'"></i>';
$TaskExplain = lang('canceled_task');
$TaskBorderColor = '#cccccc';
$TaskBorderColor2 = '0, 0, 0, 0.175';
}
elseif(@$ClassAct->Status == '1') {
$TaskColor = '#abb1bf';
$FontAwesomeWithColor = '<i class="fas fa-calendar-check" style="color:'.$TaskColor.'"></i>';
$TaskExplain = lang('completed_task');
$TaskBorderColor = '#cccccc';
$TaskBorderColor2 = '0, 0, 0, 0.175';
}
else {
if(@$ClassAct->StartDate < date('Y-m-d') || @$ClassAct->StartDate == date('Y-m-d') && @$ClassAct->StartTime < date('H:i:s')) {
$TaskColor = '#fd7b80';
$FontAwesomeWithColor = '<i class="fas fa-calendar" style="color:'.$TaskColor.'"></i>';
$TaskExplain = lang('open_task').' <strong>'.lang('late').'</strong>';
$TaskBorderColor = '#ff003b';
$TaskBorderColor2 = '218, 40, 70, 0.175';
}
else {
$TaskColor = '#9CE2A7';
$FontAwesomeWithColor = '<i class="fas fa-calendar" style="color:'.$TaskColor.'"></i>';
$TaskExplain = lang('open_task');
$TaskBorderColor = '#cccccc';
$TaskBorderColor2 = '0, 0, 0, 0.175';
}
}
?>
          <li>
            <div class="timeline-panel" style="font-size: 12px;border: 1px solid <?php echo @$TaskBorderColor; ?> !important;webkit-box-shadow: 0 1px 6px rgba(<?php echo $TaskBorderColor2; ?>) !important;box-shadow: 0 1px 6px rgba(<?php echo $TaskBorderColor2; ?>) !important;}">
              <div class="timeline-body" style="min-height: 60px;">
                <div style="padding:10px;">
                  <div class="row">
                    <div class="col-md-6 col-sm-12">
                      <?php echo $FontAwesomeWithColor; ?>
                      <?php
$CalTypes = DB::table('caltype')->where('CompanyNum','=',$CompanyNum)->where('id','=',$ClassAct->Type)->first();
$TaskFA = @$CalTypes->Type;
?>
                      <?php
if (@$ClassAct->Level == '0') {$LevelIcon = '<i class="fas fa-star"></i>';}
elseif (@$ClassAct->Level == '1') {$LevelIcon = '<i class="fas fa-star"></i><i class="fas fa-star"></i>';}
elseif (@$ClassAct->Level == '2') {$LevelIcon = '<i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>';}
?>
                      <b>
                        <?php echo @$ClassAct->text; ?>
                        <?php echo @$LevelIcon; ?> //
                      </b>
                      <div  style="padding-top: -50px;display: inline-block;">
                        <span style="background-color: <?php echo $TaskColor; ?>;color:white;padding:2px;border-radius: 15px;padding-right:5px;padding-left:5px;">
                          <?php echo $TaskExplain; ?>
                        </span>
                      </div>
                    </div>
                    <div class="col-md-6 col-sm-12 text-end">
                      <span class="" style="font-weight: bold;">
                        <?php echo @$TaskFA; ?> |
                        <?php echo with(new DateTime(@$ClassAct->StartDate))->format('d/m/Y'); ?>
                        <?php echo with(new DateTime(@$ClassAct->StartTime))->format('H:i'); ?>
                      </span>
                    </div>
                  </div>
                  <hr style="margin: 0;padding: 0;margin-top: 5px;margin-bottom: 5px;">
                  <?php echo @$ClassAct->Content; ?>
                  <?php if (@$ClassAct->FloorName != '' && $ClassAct->Content != '') {echo '<br />';} ?>
                  <?php if (@$ClassAct->FloorName != '') {echo '<i class="fas fa-chess-rook text-dark"></i> <strong class="text-dark">'.$ClassAct->FloorName.'</strong>';} ?>
                </div>
              </div>
              <div class="timeline-footer primary" style="padding: 0;margin: 0;padding: 10px;">
                <div class="row">
                  <div class="col-md-6 col-sm-12">
                    <a class="pull-right"><?php echo lang('taking_care_representative') ?>
                      <?php echo @$UsersName->display_name; ?>
                    </a>
                    ::
                    <a href="javascript:void(0);" class="edit-task-btn" data-id='<?php echo $ClassAct->id; ?>' data-client-id="<?php echo $ClassAct->ClientId; ?>" ><?php echo lang('task_edit') ?>
                    </a>
                  </div>
                  <div class="col-md-6 col-sm-12  text-end">
                    <span class="">
                      <a ><?php echo lang('added_by') ?>
                        <?php echo @$UsersNames->display_name;  ?> ב:
                        <?php echo with(new DateTime($ClassAct->Dates))->format('d/m/Y H:i'); ?>
                      </a>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <?php
++ $i; }
?>
        </ul>
        <div >
          <nav>
            <ul class="pagination float-right">
            </ul>
          </nav>
        </div>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
<?php endif ?>
<?php if (Auth::userCan('77') || $editLeadsPermission): ?>
<div class="tab-pane fade" role="tabpanel" id="user-ArchiveMessage">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-comments fa-fw">
      </i> <?php echo lang('message_archive') ?>
    </div>
    <div class="card-body" id="SmsLogList">
      <?php
$NotificationsLogList = DB::table('appnotification')->where('ClientId', '=', $Supplier->id)->where('CompanyNum', '=', $CompanyNum)->whereIn('Type', array(0,1,2))->orderBy('Date', 'DESC')->orderBy('Time', 'DESC')->limit(50)->get();
$WALogList = WhatsAppNotifications::getMessages4ClientLog($CompanyNum, $Supplier->id);
if (!empty($WALogList)) {
    $WATemplateList = WhatsAppService::getTemplateList();
}

// make sorted array from 2 sources
if (empty($NotificationsLogList) && empty($WALogList)) {echo '<div  class="text-start">'.lang('no_archive_messages').'</div>';}
else {
    $SmsLogList = [];
    $indN = 0;
    $indWA = 0;

    // limit 50
    while (sizeof($SmsLogList) < 50 && (sizeof($NotificationsLogList) > $indN || sizeof($WALogList) > $indWA)) {
        if (sizeof($WALogList) <= $indWA) {
            $SmsLogList[] = $NotificationsLogList[$indN++];
        } elseif (sizeof($NotificationsLogList) <= $indN) {
            $SmsLogList[] = $WALogList[$indWA++];
        } elseif (strtotime($NotificationsLogList[$indN]->Dates) >= strtotime($WALogList[$indWA]->Dates)) {
            $SmsLogList[] = $NotificationsLogList[$indN++];
        } else {
            $SmsLogList[] = $WALogList[$indWA++];
        }
    }
?>
      <input style='position: relative;' class="form-control search" type="text" placeholder="<?php echo lang('search_button') ?>" >
      <br>
      <ul class="timeline list">
        <?php
$i = '1';
foreach ($SmsLogList as $SmsLog) {
    if($SmsLog->UserId) {
        $UsersDB = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $SmsLog->UserId)->first();
    }
    if ($SmsLog->Type == '0') {
        $Iconsms = '<i class="fas fa-mobile-alt"></i>';
    } elseif ($SmsLog->Type == '1') {
        $Iconsms = '<i class="fas fa-phone"></i>';
    } elseif ($SmsLog->Type == '2') {
        $Iconsms = '<i class="fas fa-envelope-open"></i>';
    }
    $isWhatsApp = false;
    // check if whatsapp
    if (isset($SmsLog->template_name)) {
        $Iconsms = '<i class="fab fa-whatsapp"></i>';
        $isWhatsApp = true;
    }
?>
        <li id="EmailLogLI<?php echo strip_tags($SmsLog->id); ?>">
          <div class="timeline-panel" style="font-size: 12px;">
            <div class="timeline-body" style="min-height: 60px;">
              <div style="padding:10px;">
                <div class="row">
                  <div class="col-md-6 col-sm-12">
                    <b>
                      <?php echo $Iconsms; ?>
                    </b>
                  </div>
                  <div class="col-md-6 col-sm-12 text-end">
<!--                    <span class="">--><?php ////echo lang('serial_number') ?><!--:-->
                      <?php //echo $SmsLog->id; ?>
                        <?php if (!$isWhatsApp) echo lang('counted_as') . " " . $SmsLog->Count . " " . lang('messages'); ?>
                    </span>
                  </div>
                </div>
                <hr style="margin: 0;padding: 0;margin-top: 5px;margin-bottom: 5px;">
                <?= $isWhatsApp ? $SmsLog->reconstructMessage($WATemplateList) : $SmsLog->Text; ?>
              </div>
            </div>
            <div class="timeline-footer primary" style="padding: 0;margin: 0;padding: 10px;">
              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <a class="pull-right">
                    <?php echo isset($UsersDB) ? $UsersDB->display_name: ''; ?>
                  </a>
                </div>
                <div class="col-md-6 col-sm-12 text-end">
                  <a  >
                    <?php echo with(new DateTime($SmsLog->Date))->format('d/m/Y'); ?>
                    <?php echo with(new DateTime($SmsLog->Time))->format('H:i'); ?>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </li>
        <?php
++$i; }
?>
      </ul>
      <div >
        <nav>
          <ul class="pagination float-right">
          </ul>
        </nav>
      </div>
      <?php } ?>
    </div>
  </div>
</div>
<?php endif ?>
<?php if (Auth::userCan('999')): ?>
<div class="tab-pane fade" role="tabpanel" id="user-files">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-hdd fa-fw">
      </i> <?php echo lang('file') ?>
    </div>
    <div class="card-body text-start">
    </div>
  </div>
</div>
<?php endif ?>
<?php if (Auth::userCan('70')): ?>
<div class="tab-pane fade" role="tabpanel" id="user-pay">
  <div class="card spacebottom">
    <div class="card-header d-flex justify-content-between text-start">
      <div>
        <i class="fas fa-credit-card fa-fw">
        </i> <?php echo lang('charge_client') ?>
      </div>
    </div>
    <div class="card-body text-start">
      <div class="row" >
        <div class="col-md-12">
          <form id="AddDocsClient" name="AddDocs" action="SaveReceipt"  autocomplete="off" class="ajax-form clearfix">
            <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
            <span>
              <h5>
                <i class="fas fa-credit-card">
                </i> <?php echo lang('customer_card_payments') ?>
                <strong style="color:red;" >₪
                  <?php echo number_format(@$Supplier->BalanceAmount,2,".",",");  ?>
                </strong>
              </h5>
            </span>
            <label><?php echo lang('customer_card_select_membership') ?></label>
              <div class="overflow-y-auto bsapp-max-h-500p">
            <?php
            $GetPays = DB::table('client_activities')
                ->where('CompanyNum','=',$CompanyNum)
                ->where('ClientId','=', $Supplier->id)
                ->where('BalanceMoney','>',0)
                ->where('CancelStatus', '=', 0)
//                ->where('isDisplayed', 1)
                ->orderBy('id', 'ASC')
                ->get();

            foreach ($GetPays as $key => $GetPay) {
            ?>
              <div class="custom-control custom-checkbox mb-3">
                <input type="checkbox" class="custom-control-input CloseCheckBoxPayment" id="invoicenum-<?php echo $key ?>" name="invoicenum[]" value="<?php echo $GetPay->id; ?>" data-weight="<?php echo @$GetPay->BalanceMoney; ?>">
                <label class="custom-control-label" for="invoicenum-<?php echo $key ?>">
                    <?php echo @$GetPay->ItemText; ?> :: <?php echo lang('remainder_of_payment') ?> ::
                    <?php echo @$GetPay->BalanceMoney; ?> ₪
                </label>
              </div>
            <?php } ?>
              <?php
              $CheckClientInfoer = DB::table('client')->where('CompanyNum', $CompanyNum)->where('PayClientId', $Supplier->id)->get();
              foreach ($CheckClientInfoer as $CheckClientInfo) {
                  if ($CheckClientInfo) {
                      $CheckClientInfos = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $CheckClientInfo->id)->first();
                      $GetPaysClient = DB::table('client_activities')
                          ->where('CompanyNum', '=', $CompanyNum)
                          ->where('ClientId', '=', $CheckClientInfo->id)
                          ->where('BalanceMoney', '>', 0)
                          ->where('CancelStatus', '=', 0)
                          ->orderBy('id', 'ASC')->get();
                      foreach ($GetPaysClient as $GetPayClient) {
                          ?>
                          <div class="custom-control custom-checkbox mb-3">
                              <input type="checkbox" class="custom-control-input CloseCheckBoxPayment" id="invoicenum-<?php echo $GetPayClient->id ?>" name="invoicenum[]" value="<?php echo @$GetPayClient->id; ?>" data-weight="<?php echo @$GetPayClient->BalanceMoney; ?>">
                              <label class="custom-control-label" for="invoicenum-<?php echo $GetPayClient->id ?>">
                                  <?php echo @$GetPayClient->ItemText; ?> ::
                                  <?php echo lang('remainder_of_payment') ?> ::
                                  <?php echo @$GetPayClient->BalanceMoney; ?> ₪
                                  (<?php echo htmlentities($CheckClientInfos->CompanyName); ?>)
                              </label>
                              <br>
                          </div>
                      <?php }
                  }
              } ?>
              </div>
            <input name="invoicenum[]" type="hidden" data-weight="0" value="0">
            <div class="form-group">
            <?php echo lang('customer_card_total_membership') ?>
              <strong style="color:red;" >₪
                <span id='total' >0
                </span>
              </strong>
            </div>
            <div class="alertb alert-warning"><?php echo lang('customer_card_payments_notice') ?>
              <u>
                <strong><?php echo lang('to_generate_receipt') ?></strong></u> <?php echo lang('bottom_of_the_screen') ?>
            </div>
            <input type='hidden' value="0" id='Finalinvoicenum' name='Finalinvoicenum'/>
            <input type='hidden' value="0" id='TrueFinalinvoicenum' name='TrueFinalinvoicenum'/>
            <input type='hidden' value="0" id='FinalinvoiceId' name='FinalinvoiceId'/>
            <input type='hidden' value="<?php echo $GroupNumber; ?>" name='GroupNumber'/>
            <input type='hidden' value="<?php echo $Supplier->id; ?>" name='ClientId'/>
            <div id="ShowPaymentDiv" style="display: none;">
              <div class="alertb alert-dark" role="alert" style="display:block; font-weight:bold; font-size:14px;"><?php echo lang('customer_card_details_payment') ?>
              </div>
              <div class="row mt-13">
                <div class="col-12 js-payment-method">
                  <button type="button" name="submit" class="btn btn-light btn-rounded mie-5" id="Chash"><?php echo lang('cash') ?>
                  </button>
                  <button type="button" name="submit" class="btn btn-light btn-rounded mie-5" id="Credit"><?php echo lang('credit_card_single') ?>
                  </button>
                  <button type="button" name="submit" class="btn btn-light btn-rounded mie-5" id="Check"><?php echo lang('check') ?>
                  </button>
                  <button type="button" name="submit" class="btn btn-light btn-rounded mie-5" id="Bank"><?php echo lang('bank_transfer') ?>
                  </button>
                </div>
              </div>
              <div id="Cahshdiv" style="display: none;">
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md col-sm-12 order-md-1">
                    <label class="control-label"><?php echo lang('summary') ?>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="CashValue"  onkeypress='validate(event)' id="CashValue" class="form-control w-unset" placeholder="<?php echo lang('type_cash_sum') ?>"  tabindex="1">
                  </div>
                </div>
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md-12 col-sm-12  order-md-2">
                    <button class="btn btn-success btn-rounded" type="button" id="CashValueButton" tabindex="2"><?php echo lang('add') ?>
                      <span class="glyphicon glyphicon-saved">
                      </span>
                    </button>
                  </div>
                </div>
              </div>
              <div id="Checkdiv" style="display: none;">
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md col-sm-12 order-md-3">
                    <label><?php echo lang('branch_id') ?>
                    </label>
                    <input type="text" class="form-control" name="CheckSnif" id="CheckSnif"  onkeypress='validate(event)'  tabindex="6">
                  </div>
                  <div class="col-md col-sm-12 order-md-2">
                    <label><?php echo lang('bank_code') ?>
                    </label>
                    <input type="text" class="form-control" name="CheckBank" id="CheckBank"  onkeypress='validate(event)'  tabindex="5">
                  </div>
                  <div class="col-md col-sm-12 order-md-1">
                    <label><?php echo lang('check_number') ?>
                    </label>
                    <input type="text" class="form-control" name="CheckNumber" id="CheckNumber"  onkeypress='validate(event)'  tabindex="4">
                  </div>
                </div>
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md col-sm-12 order-md-3">
                    <label class="control-label"><?php echo lang('check_sum') ?>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="CheckValue" id="CheckValue" class="form-control" onkeypress='validate(event)'  tabindex="9">
                  </div>
                  <div class="col-md col-sm-12 order-md-2">
                    <label class="control-label"><?php echo lang('payment_date') ?>
                    </label>
                    <input type="date" class="form-control" name="CheckDate" id="CheckDate"  onkeypress='validate(event)'  tabindex="8">
                  </div>
                  <div class="col-md col-sm-12 order-md-1">
                    <label><?php echo lang('account_number') ?>
                    </label>
                    <input type="text" class="form-control" name="CheckAccount" id="CheckAccount"  onkeypress='validate(event)'  tabindex="7">
                  </div>
                </div>
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md-12 col-sm-12">
                    <button class="btn btn-success btn-rounded" type="button" id="CheckValueButton" tabindex="9"><?php echo lang('add_check') ?>
                      <span class="glyphicon glyphicon-saved">
                      </span>
                    </button>
                  </div>
                </div>
              </div>
              <div id="Bankdiv" style="display: none;">
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md col-sm-12 pull-right order-md-3">
                    <label class="control-label"><?php echo lang('transfer_sum') ?>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="BankValue" id="BankValue" class="form-control" onkeypress='validate(event)'  tabindex="12">
                  </div>
                  <div class="col-md col-sm-12 pull-right order-md-2">
                    <label class="control-label"><?php echo lang('deposit_date') ?>
                    </label>
                    <input type="date" class="form-control" name="BankDate" id="BankDate"  onkeypress='validate(event)'  tabindex="11">
                  </div>
                  <div class="col-md col-sm-12 pull-right order-md-1">
                    <label><?php echo lang('ref_number') ?>
                    </label>
                    <input type="text" class="form-control" name="BankNumber" id="BankNumber"   tabindex="10">
                  </div>
                </div>
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md-12 col-sm-12 order-md-4">
                    <button class="btn btn-success btn-rounded" type="button" id="BankValueButton" tabindex="13"><?php echo lang('add_bank_transfer') ?>
                      <span class="glyphicon glyphicon-saved">
                      </span>
                    </button>
                  </div>
                </div>
              </div>
              <div id="Creditdiv" style="display: none;">
                <div class="row" style="padding-top: 10px;">
                  <div class="col-12">
                    <label><?php echo lang('choose_option') ?>
                    </label>
                    <select name="CreditOptionToken" id="CreditOptionToken" class="form-control w-unset" onchange="java_script_:showCredit(this.options[this.selectedIndex].value)" tabindex="14">
                      <?php if (!$Supplier->isRandomClient) { ?>
                        <option value="2"><?php echo lang('credit_card_saved_in_system') ?></option>
                      <?php } ?>
                      <option value="3"><?php echo lang('manual_type') ?>
                      </option>
                      <option value="4"><?php echo lang('transfer_made_by_other_terminal') ?>
                      </option>
                        <?php if ($TypeShva == '0') { ?>
                            <option value="1"><?php echo lang('credit_card_scanner') ?></option>
                        <?php } ?>
                    </select>
                  </div>
                </div>
                  <div style="display: none" id="CreditDiv1" class="credit-card-type" data-type="1">
                      <div class="row"  style="padding-top: 10px;">
                          <div class="col-md col-sm-12 pull-right order-md-3">
                              <label class="control-label"><?php echo lang('payments_num') ?>
                              </label>
                              <select name="Tash" id="Tash1" class="form-control input-lg Tash" tabindex="17">
                                  <option value="1">1
                                  </option>
                              </select>
                          </div>
                          <div class="col-md col-sm-12 pull-right order-md-2">
                              <label class="control-label"><?php echo lang('choose_payment_method') ?>
                              </label>
                              <select name="tashType" id="tashType1" class="form-control input-lg tashType" tabindex="16">
                                  <option value="0" selected><?php echo lang('regular') ?>
                                  </option>
                                  <option value="1"><?php echo lang('payments') ?>
                                  </option>
                              </select>
                          </div>
                          <div class="col-md col-sm-12 pull-right order-md-1">
                              <label><?php echo lang('price_to_charge') ?></label>
                              <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValue" class="form-control" onkeypress="validate(event)" placeholder="0.00"  tabindex="15">
                          </div>
                      </div>
                      <div class="row"  style="padding-top: 10px;">
                          <div class="col-md-12 col-sm-12order-md-4">
                              <label class="control-label"><?php echo lang('swipe_credit_card') ?>
                              </label>
                              <input type="text" class="form-control CC2" name="CC2" id="CC2"  tabindex="18">
                          </div>
                      </div>
                      <div class="row"  style="padding-top: 10px;">
                          <div class="col-md-12 col-sm-12  order-md-5">
                              <button class="btn btn-success btn-rounded" type="button" id="CreditValueButton" disabled tabindex="19"><?php echo lang('charge_client') ?>
                                  <span class="glyphicon glyphicon-saved"></span>
                              </button>
                          </div>
                      </div>
                  </div>
                <div style="display: none;" id="CreditDiv2" class="credit-card-type" data-type="2">
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md col-sm-12 pull-right order-md-4">
                      <label class="control-label"><?php echo lang('choose_credit_card_saved_in_system') ?>
                      </label>
                      <div id="ChangeTokenI">
                        <select name="CC3" id="CC3" class="form-control input-lg unicode-plaintext" tabindex="23">
                            <option value="" selected><?php echo lang('choose_token') ?>
                            </option>
                            <?php
                            $Tokens = DB::table('token')
                                ->where('CompanyNum', $CompanyNum)->where('ClientId', '=', $Supplier->id)
                                ->where('Status', '=', '0')
                                ->where('Token',"!=",'')
                                ->get();
                            if ($Supplier->parentClientId != 0) {
                                $Tokens = DB::table('token')->where('CompanyNum', $CompanyNum)->where('ClientId', '=', $Supplier->parentClientId)->where('Status', '=', '0')->where('Token',"!=",'')->get();
                            }
                            foreach ($Tokens as $Token) {
                                $L4digit = $Token->L4digit;
                                ?>
                                <option value="<?php echo $Token->id; ?>">
                                    <?php echo '****' . $L4digit; ?>
                                    <?php echo !empty($parent) ? ' (' . $parent->CompanyName . ')' : ''; ?>
                                </option>
                            <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-3">
                      <label class="control-label"><?php echo lang('payments_num') ?>
                      </label>
                      <select name="Tash" id="Tash2" class="form-control input-lg Tash" tabindex="22">
                        <option value="1">1
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-2">
                      <label class="control-label"><?php echo lang('choose_payment_method') ?>
                      </label>
                      <select name="tashType" id="tashType2" class="form-control input-lg tashType" tabindex="21">
                        <option value="0" selected><?php echo lang('regular') ?>
                        </option>
                        <option value="1"><?php echo lang('payments') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-1">
                      <label><?php echo lang('price_to_charge') ?>
                      </label>
                      <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValue2" class="form-control" onkeypress="validate(event)" tabindex="20" placeholder="0.00">
                    </div>
                  </div>
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md-12 col-sm-12 order-md-5">
                      <button class="btn btn-success btn-rounded" type="button" id="Credit2ValueButton" tabindex="24"><?php echo lang('charge_client') ?>
                        <span class="glyphicon glyphicon-saved">
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
                  <div style="display: none;" id="CreditDiv3" class="credit-card-type" data-type="3">
                      <div class="row" style="padding-top: 10px;">
                          <div class="col-md col-sm-12  pull-right order-md-2">
                              <label class="control-label"><?php echo lang('choose_payment_method') ?>
                              </label>
                              <select name="tashType" id="tashType3" class="form-control input-lg tashType" tabindex="26">
                                  <option value="0" selected><?php echo lang('regular') ?></option>
                                  <option value="1"><?php echo lang('payments') ?></option>
                              </select>
                          </div>
                          <div class="col-md col-sm-12  pull-right order-md-3">
                              <label class="control-label"><?php echo lang('payments_num') ?>
                              </label>
                              <select name="Tash" id="Tash3" class="form-control input-lg Tash" tabindex="27">
                                  <option value="1">1</option>
                              </select>
                          </div>
                          <div class="col-md col-sm-12  pull-right order-md-1">
                              <label><?php echo lang('price_to_charge') ?>
                              </label>
                              <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValue3" class="form-control"
                                     onkeypress="validate(event)" tabindex="25" placeholder="0.00">
                          </div>
                      </div>

                      <div class="row mt-20">
                          <div class="col-sm-12">
                              <button class="btn btn-success btn-rounded mb-20 js-pay-new-card-iframe-button" data-order-type="<?= OrderLogin::TYPE_PAYMENT_NEW_CARD ?>">
                                  <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                  <span class="js-loading-label d-none"><?php echo lang('loading') ?>...</span>
                                  <span class="js-btn-text"><?php echo lang('move_to_payment') ?></span>
                              </button>

                              <div class="iframe-wrapper mb-20 d-none">
                                  <iframe src="" frameborder="0" class="add-new-card-iframe w-100 vh-100"></iframe>
                              </div>
                          </div>
                      </div>
                  </div>
                <div style="display: none;" id="CreditDiv4" class="credit-card-type" data-type="4">
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md col-sm-12 pull-right order-md-5">
                      <label class="control-label"><?php echo lang('payments_num') ?>
                      </label>
                      <select name="Tash" id="Tash4" class="form-control input-lg Tash" tabindex="38">
                        <option value="1">1
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-4">
                      <label class="control-label"><?php echo lang('choose_payment_method') ?>
                      </label>
                      <select name="tashType" id="tashType4" class="form-control input-lg tashType" tabindex="37">
                        <option value="0" selected><?php echo lang('regular') ?>
                        </option>
                        <option value="1"><?php echo lang('payments') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-3">
                      <label class="control-label"><?php echo lang('confirmation_number') ?>
                      </label>
                      <input type="text" class="form-control" name="CCode" id="CCode4"  tabindex="36">
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-2">
                      <label class="control-label"><?php echo lang('original_charge_date') ?>
                      </label>
                      <input type="date" class="form-control" name="CDate" id="CDate4"  tabindex="35">
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-1">
                      <label><?php echo lang('charged_price') ?>
                      </label>
                      <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValue4" class="form-control" onkeypress="validate(event)" tabindex="34" placeholder="0.00">
                    </div>
                  </div>
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md col-sm-12 pull-right order-md-6">
                      <label class="control-label"><?php echo lang('choose_company_to_be_paid_off') ?>
                      </label>
                      <select name="TypeBank" id="TypeBank4" class="form-control input-lg" tabindex="41">
                        <option value=""><?php echo lang('choose') ?>
                        </option>
                        <option value="2"><?php echo lang('visa_cal') ?>
                        </option>
                        <option value="1"><?php echo lang('isracard') ?>
                        </option>
                        <option value="6"><?php echo lang('leumi_card') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-5">
                      <label class="control-label"><?php echo lang('choose_credit_card_type') ?>
                      </label>
                      <select name="TypeBrand" id="TypeBrand4" class="form-control input-lg" tabindex="40">
                        <option value=""><?php echo lang('choose') ?>
                        </option>
                        <option value="88"><?php echo lang('mastercard') ?>
                        </option>
                        <option value="2"><?php echo lang('visa') ?>
                        </option>
                        <option value="5"><?php echo lang('isracard') ?>
                        </option>
                        <option value="66"><?php echo lang('diners') ?>
                        </option>
                        <option value="77"><?php echo lang('american_express') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-4">
                      <label class="control-label"><?php echo lang('last_four_chars_on_cc') ?>
                      </label>
                      <input type="text" class="form-control" name="CC" id="CC4"  placeholder="<?php echo lang('type_manual') ?>" tabindex="39">
                    </div>
                  </div>
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md-12 col-sm-12">
                      <button class="btn btn-success btn-rounded" type="button" id="Credit4ValueButton" tabindex="44"><?php echo lang('save') ?>
                        <span class="glyphicon glyphicon-saved">
                        </span>
                      </button>
                    </div>
                  </div>
                  <hr>
                  <div class="alertb alert-info my-6 p-6 border-radius-3r"><?php echo lang('attention_typing_required_system_wont_charge') ?>
                    <u><?php echo lang('cc_system_notice') ?></u>
                    <br>
                    <?php echo lang('cc_notice_receipts') ?>
                  </div>
                </div>
                <br>
                <hr>
                <div class="alertb alert-warning"><?php echo lang('cc_accept_notice') ?>
                </div>
              </div>
                <hr>
                <div class="container-fluid">
                    <div><?= lang('notes_two'); ?></div>
                    <textarea class="form-control summernote d-none" name="Remarks"></textarea>
                </div>
                <hr>
              <div class="alertb alert-dark" role="alert" style="display:block; font-weight:bold; font-size:14px; text-align: start;" ><?php echo lang('detailed_receipt') ?>
              </div>
              <div class="row my-13">
                <div class="col-md-12 col-sm-12">
                  <div id="DocsPayments">
                    <table class="table  table-responsive w-100 d-block d-md-table"  style="width: 100%;">
                      <thead>
                        <tr>
                          <th style="width: 5%; text-align: start;">#
                          </th>
                          <th style="width: 25%; text-align: start;"><?php echo lang('detailed_receipt') ?>
                          </th>
                          <th style="width: 25%; text-align: start;"><?php echo lang('detail') ?>
                          </th>
                          <th style="width: 20%; text-align: start;"><?php echo lang('reference') ?>
                          </th>
                          <th style="width: 10%; text-align: start;"><?php echo lang('summary') ?>
                          </th>
                          <th style="width: 15%; text-align: start;"><?php echo lang('actions') ?>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="form-group float-md-right">
                <button type="submit" name="submit" id="ReceiptBtn" class="btn btn-primary btn-rounded" disabled><?php echo lang('receipt_button') ?>
                </button>
              </div>
              <div class="form-group float-md-left">
                <button type="button" name="CancelDoc" id="CancelDocButton" class="btn btn-danger btn-rounded" data-ip-modal="#CancelDoc" disabled><?php echo lang('cancel_doc') ?>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="meTest" data-ip-modal="#CancelPaymentsPopup">
<div class="ip-modal text-start" id="CancelPaymentsPopup" data-backdrop="static">
  <div class="ip-modal-dialog">
    <form   action="POSClientCancelPayments" id="POSClientCancelPayments" name="POSClientCancelPayments" class="ip-modal-content ajax-form clearfix">
      <div class="ip-modal-header d-flex  justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('cancel_charge') ?>
        </h4>
        <a class="ip-close CancelPaymentsClose" title="Close" style="" data-dismiss="modal">&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <input name="GroupNumber" type="hidden" value="<?php echo $GroupNumber; ?>">
        <input name="ClientId" id="CancelPayments_TempsId" type="hidden" value="<?php echo $Supplier->id; ?>">
        <input name="TempListsId" id="CancelPayments_TempsListsId" type="hidden" value="">
        <input name="Finalinvoicenum" id="CancelDocs_Finalinvoicenum" type="hidden" value="0">
        <div class="form-group" >
          <label><?php echo lang('are_you_sure_cancel_charge') ?>
          </label>
        </div>
      </div>
      <div class="ip-modal-footer">
        <div class="ip-actions d-flex">
          <button type="submit" name="submit" class="btn btn-primary text-white"><?php echo lang('yes') ?>
          </button>
        </div>
        <button type="button" class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php echo lang('no') ?>
        </button>
      </div>
    </form>
  </div>
</div>
<div class="ip-modal text-start" id="CancelDoc" data-backdrop="static">
  <div class="ip-modal-dialog">
    <div class="ip-modal-content">
      <div class="ip-modal-header" >
        <a class="ip-close" title="Close" style="" data-dismiss="modal">&times;
        </a>
        <h4 class="ip-modal-title"><?php echo lang('cancel_doc') ?>
        </h4>
      </div>
      <div class="ip-modal-body" >
        <form  action="POSClientCancelDocs"  class="ajax-form clearfix">
          <input name="ClientId" id="CancelDocs_TempsId" type="hidden" value="<?php echo $Supplier->id; ?>">
          <input name="GroupNumber" type="hidden" value="<?php echo $GroupNumber; ?>">
          <div class="form-group" >
            <label><?php echo lang('are_you_sure_cancel_doc') ?>
            </label>
          </div>
          <div class="alertb alert-danger" id="POSCancelDocsError" style="display: none;">
            <span id="POSCancelDocsErrorText">
            </span>
          </div>
          </div>
        <div class="ip-modal-footer">
          <div class="ip-actions">
            <button type="submit" name="submit" id="CancelReceiptBtn" class="btn btn-primary text-white"><?php echo lang('yes') ?>
            </button>
          </div>
          <button type="button" class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php echo lang('no') ?>
          </button>
          </form>
      </div>
    </div>
  </div>
</div>
<?php endif ?>

<div class="tab-pane fade" role="tabpanel" id="user-refoundpay">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-credit-card fa-fw">
      </i> <?php echo lang('refund_title') ?>
    </div>
    <div class="card-body text-start">
      <div class="row" >
        <div class="col-md-12">
          <form id="AddDocsRefoundClient" name="AddDocsRefound" action="SaveReceiptRefound"  autocomplete="off" class="ajax-form clearfix">
            <span>
              <h5>
                <i class="fas fa-credit-card">
                </i> <?php echo lang('exec_refund') ?>
              </h5>
            </span>
            <label><?php echo lang('choose_membership_refund') ?>
            </label>
              <?php
              $GetPays = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $Supplier->id)->where('CancelStatus', '=', '0')->where('Status', '!=', '3')->orderBy('id', 'ASC')->get();
              foreach ($GetPays as $key => $GetPay) {
                  $BalanceMoneyTrue = ($GetPay->ItemPriceVatDiscount + $GetPay->VatAmount) - $GetPay->BalanceMoney;
                  if ($GetPay->TrueBalanceRefoundMoney != 0) {
                      $BalanceRefoundMoney = round($BalanceMoneyTrue - $GetPay->TrueBalanceRefoundMoney, 2);
                  } else {
                      $BalanceRefoundMoney = $BalanceMoneyTrue;
                  }

                  if ($BalanceRefoundMoney != 0) {
                      ?>
                      <div class="custom-control custom-checkbox mb-3">
                          <input type="checkbox" class="custom-control-input CloseCheckBoxPaymentRefound"
                                 id="invoicenumrefound-<?php echo $key ?>" name="invoicenumrefound[]"
                                 value="<?php echo $GetPay->id; ?>" data-weight="<?php echo $BalanceRefoundMoney; ?>">
                          <label class="custom-control-label" for="invoicenumrefound-<?php echo $key ?>">
                              <?php echo $GetPay->ItemText; ?> :: <?php echo lang('max_refund') ?> ::
                              <?php echo $BalanceRefoundMoney; ?> ₪
                          </label>
                      </div>

                  <?php }
              } ?>
              <?php
              $CheckClientInfoer = DB::table('client')->where('CompanyNum', $CompanyNum)->where('PayClientId', $Supplier->id)->get();
              foreach ($CheckClientInfoer as $CheckClientInfo) {
                  if (!empty($CheckClientInfo)) {
                      $CheckClientInfos = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $CheckClientInfo->id)->first();
                      $GetPaysClient = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $CheckClientInfo->id)->where('Status', '!=', '3')->where('CancelStatus', '=', '0')->where('TruePays', '=', $Supplier->id)->orderBy('id', 'ASC')->get();
                      foreach ($GetPaysClient as $GetPayClient) {
                          $BalanceMoneyTrue = ($GetPayClient->ItemPriceVatDiscount + $GetPayClient->VatAmount) - $GetPayClient->BalanceMoney;
                          if ($GetPayClient->TrueBalanceRefoundMoney != '0' || $GetPayClient->TrueBalanceRefoundMoney != '0.00') {
                              $BalanceRefoundMoney = $BalanceMoneyTrue - $GetPayClient->TrueBalanceRefoundMoney;
                          } else {
                              $BalanceRefoundMoney = $BalanceMoneyTrue;
                          }
                          if ($BalanceRefoundMoney != '0') {
                              ?>
                              <div class="custom-control custom-checkbox mb-3">
                                  <input type="checkbox" class="custom-control-input CloseCheckBoxPaymentRefound"
                                         id="invoicenumrefound-<?php echo $GetPayClient->id ?>" name="invoicenumrefound[]"
                                         value="<?php echo $GetPayClient->id; ?>" data-weight="<?php echo $BalanceRefoundMoney; ?>">
                                  <label class="custom-control-label" for="invoicenumrefound-<?php echo $GetPayClient->id ?>">
                                      <?php echo $GetPayClient->ItemText; ?> ::
                                      <?php echo lang('remainder_of_payment') ?> ::
                                      <?php echo $BalanceRefoundMoney; ?> ₪
                                      (<?php echo htmlentities($CheckClientInfos->CompanyName); ?>)
                                  </label>
                              </div>
                          <?php }
                      }
                  }
              } ?>
            <input name="invoicenumrefound[]" type="hidden" data-weight="0" value="0">
            <div class="form-group mt-13">
            <?php echo lang('total_refund_membership') ?>
              <strong style="color:red;" >₪
                <span id='totalrefound' >0
                </span>
              </strong>
            </div>
            <div class="alertb alert-warning"><?php echo lang('refund_notice') ?>
              <u>
                <strong><?php echo lang('genereate_credit') ?>
                </strong>
              </u> <?php echo lang('bottom_of_the_screen') ?>
            </div>
            <input type='hidden' value="0" id='FinalinvoicenumRefound' name='Finalinvoicenum'/>
            <input type='hidden' value="0" id='TrueFinalinvoicenumRefound' name='TrueFinalinvoicenum'/>
            <input type='hidden' value="0" id='FinalinvoiceIdRefound' name='FinalinvoiceId'/>
            <input type='hidden' value="<?php echo $GroupNumberRefound; ?>" name='GroupNumber'/>
            <input type='hidden' value="<?php echo $Supplier->id; ?>" name='ClientId'/>
            <div id="ShowPaymentRefoundDiv" style="display: none;">
              <!-- <div class="alertb alert-dark" role="alert" style="display:block; font-weight:bold; font-size:14px;"><?php //echo lang('define_refund') ?>
              </div>    -->
              <div class="row mt-13">
                <div class="col-12 js-refund-method">
                  <button type="button" name="submit" class="btn btn-light btn-rounded mie-5" id="ChashRefound"><?php echo lang('cash') ?>
                  </button>
                  <button type="button" name="submit" class="btn btn-light btn-rounded mie-5" id="CreditRefound"><?php echo lang('credit_card_single') ?>
                  </button>
                  <button type="button" name="submit" class="btn btn-light btn-rounded mie-5" id="CheckRefound"><?php echo lang('check') ?>
                  </button>
                  <button type="button" name="submit" class="btn btn-light btn-rounded mie-5" id="BankRefound"><?php echo lang('bank_transfer') ?>
                  </button>
                </div>
              </div>
              <div id="CahshRefounddiv" style="display: none;">
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md col-sm-12 order-md-1">
                    <label class="control-label"><?php echo lang('summary') ?>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="CashValue"  onkeypress='validate(event)' id="CashValueRefound" class="form-control w-unset" placeholder="<?php echo lang('type_cash_sum') ?>"  tabindex="1">
                  </div>
                </div>
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md-12 col-sm-12  order-md-2">
                    <button class="btn btn-success btn-rounded" type="button" id="CashValueButtonRefound" tabindex="2"><?php echo lang('add') ?>
                      <span class="glyphicon glyphicon-saved">
                      </span>
                    </button>
                  </div>
                </div>
              </div>
              <div id="CheckRefounddiv" style="display: none;">
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md col-sm-12 order-md-3">
                    <label><?php echo lang('branch_id') ?>
                    </label>
                    <input type="text" class="form-control" name="CheckSnif" id="CheckSnifRefound"  onkeypress='validate(event)'  tabindex="6">
                  </div>
                  <div class="col-md col-sm-12 order-md-2">
                    <label><?php echo lang('bank_code') ?>
                    </label>
                    <input type="text" class="form-control" name="CheckBank" id="CheckBankRefound"  onkeypress='validate(event)'  tabindex="5">
                  </div>
                  <div class="col-md col-sm-12 order-md-1">
                    <label><?php echo lang('check_number') ?>
                    </label>
                    <input type="text" class="form-control" name="CheckNumber" id="CheckNumberRefound"  onkeypress='validate(event)'  tabindex="4">
                  </div>
                </div>
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md col-sm-12 order-md-3">
                    <label class="control-label"><?php echo lang('check_sum') ?>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="CheckValue" id="CheckValueRefound" class="form-control" onkeypress='validate(event)'  tabindex="9">
                  </div>
                  <div class="col-md col-sm-12 order-md-2">
                    <label class="control-label"><?php echo lang('payment_date') ?>
                    </label>
                    <input type="date" class="form-control" name="CheckDate" id="CheckDateRefound"  onkeypress='validate(event)'  tabindex="8">
                  </div>
                  <div class="col-md col-sm-12 order-md-1">
                    <label><?php echo lang('account_number') ?>
                    </label>
                    <input type="text" class="form-control" name="CheckAccount" id="CheckAccountRefound"  onkeypress='validate(event)'  tabindex="7">
                  </div>
                </div>
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md-12 col-sm-12">
                    <button class="btn btn-success btn-rounded" type="button" id="CheckValueButtonRefound" tabindex="9"><?php echo lang('add_check') ?>
                      <span class="glyphicon glyphicon-saved">
                      </span>
                    </button>
                  </div>
                </div>
              </div>
              <div id="BankRefounddiv" style="display: none;">
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md col-sm-12 pull-right order-md-3">
                    <label class="control-label"><?php echo lang('transfer_sum') ?>
                    </label>
                    <input type="number" step="0.01" min="0.01" name="BankValue" id="BankValueRefound" class="form-control" onkeypress='validate(event)'  tabindex="12">
                  </div>
                  <div class="col-md col-sm-12 pull-right order-md-2">
                    <label class="control-label"><?php echo lang('deposit_date') ?>
                    </label>
                    <input type="date" class="form-control" name="BankDate" id="BankDateRefound"  onkeypress='validate(event)'  tabindex="11">
                  </div>
                  <div class="col-md col-sm-12 pull-right order-md-1">
                    <label><?php echo lang('ref_number') ?>
                    </label>
                    <input type="text" class="form-control" name="BankNumber" id="BankNumberRefound" tabindex="10">
                  </div>
                </div>
                <div class="row"  style="padding-top: 10px;">
                  <div class="col-md-12 col-sm-12 order-md-4">
                    <button class="btn btn-success btn-rounded" type="button" id="BankValueButtonRefound" tabindex="13"><?php echo lang('add_bank_transfer') ?>
                      <span class="glyphicon glyphicon-saved">
                      </span>
                    </button>
                  </div>
                </div>
              </div>
              <div id="CreditRefounddiv" style="display: none;">
                <div class="row" style="padding-top: 10px;">
                  <div class="col-12">
                    <!-- <div class="alertb alert-warning d"><?php //echo lang('meshulam_notice') ?>
                    </div> -->
                    <label><?php echo lang('choose_option') ?>
                    </label>
                    <select name="CreditOptionToken" id="CreditOptionTokenRefound" class="form-control w-unset" onchange="java_script_:showCreditRefound(this.options[this.selectedIndex].value)" tabindex="14">
                      <?php if ($TypeShva == 0 && !$Supplier->isRandomClient) { ?>
                      <option value="2" selected><?php echo lang('credit_card_saved_in_system') ?>
                      </option>
                      <option value="3"><?php echo lang('manual_type') ?>
                      </option>
                      <?php } else if(in_array($TypeShva, [PaymentTypeEnum::TYPE_MESHULAM, PaymentTypeEnum::TYPE_TRANZILA])) { ?>
                      <option value="3" selected><?php echo lang('prev_transactions') ?>
                      </option>
                      <?php } ?>
                      <option value="4"><?php echo lang('transfer_made_by_other_terminal') ?>
                      </option>
                    </select>
                  </div>
                </div>
                <div style="display:none;" id="CreditDiv1Refound" class="refund-credit-type-card" data-type="1">
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md col-sm-12 pull-right order-md-3">
                      <label class="control-label"><?php echo lang('payments_num') ?>
                      </label>
                      <select name="Tash" id="Tash1Refound" class="form-control input-lg Tash" tabindex="17" >
                        <option value="1">1
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-2">
                      <label class="control-label"><?php echo lang('choose_payment_method') ?>
                      </label>
                      <select name="tashType" id="tashType1Refound" class="form-control input-lg tashType" tabindex="16">
                        <option value="0" selected><?php echo lang('regular') ?>
                        </option>
                        <option value="1"><?php echo lang('payments') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-1">
                      <label><?php echo lang('price_to_charge') ?>
                      </label>
                      <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValueRefound" class="form-control" onkeypress='validate(event)'  tabindex="15">
                    </div>
                  </div>
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md-12 col-sm-12order-md-4">
                      <label class="control-label"><?php echo lang('swipe_credit_card') ?>
                      </label>
                      <input type="text" class="form-control CC2" name="CC2" id="CC2Refound"  tabindex="18">
                    </div>
                  </div>
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md-12 col-sm-12  order-md-5">
                      <button class="btn btn-success btn-rounded" type="button" id="CreditValueButtonRefound" disabled tabindex="19"><?php echo lang('refund_title') ?>
                        <span class="glyphicon glyphicon-saved">
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
                <div style="display:none;" id="CreditDiv2Refound" class="refund-credit-type-card" data-type="2">
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md col-sm-12 pull-right order-md-4">
                      <label class="control-label"><?php echo lang('choose_credit_card_saved_in_system') ?>
                      </label>
                      <div id="ChangeTokenIRefound">
                        <select name="CC3" id="CC3Refound" class="form-control input-lg" tabindex="23">
                          <?php if($TypeShva == 0) { ?>
                          <option value="" selected><?php echo lang('choose_token') ?>
                          </option>
                          <?php

                              $Tokens = DB::table('token')->where('CompanyNum', $CompanyNum)->where('ClientId', '=', $Supplier->id)->where('Status', '=', '0')->where('Token',"!=",'')->get();
                              if ($Supplier->parentClientId != 0) {
                                  $Tokens = DB::table('token')->where('CompanyNum', $CompanyNum)->where('ClientId', '=', $Supplier->parentClientId)->where('Status', '=', '0')->where('Token',"!=",'')->get();
                              }
                              foreach ($Tokens as $Token) {
                                  $L4digit = $Token->L4digit;
                                  ?>
                          <option value="<?php echo $Token->id; ?>">
                            <?php echo '****' . $L4digit; ?>
                            <?php echo !empty($parent) ? ' ('.$parent->CompanyName.')' : ''; ?>
                          </option>
                          <?php
                          } } else if($TypeShva == 1) {

                          ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-3">
                      <label class="control-label"><?php echo lang('payments_num') ?>
                      </label>
                      <select name="Tash" id="Tash2Refound" class="form-control input-lg Tash" tabindex="22">
                        <option value="1">1
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-2">
                      <label class="control-label"><?php echo lang('choose_payment_method') ?>
                      </label>
                      <select name="tashType" id="tashType2Refound" class="form-control input-lg tashType" tabindex="21">
                        <option value="0" selected><?php echo lang('regular') ?>
                        </option>
                        <option value="1"><?php echo lang('payments') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-1">
                      <label><?php echo lang('refund_amount') ?></label>
                      <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValue2Refound" class="form-control" onkeypress='validate(event)'  tabindex="20">
                    </div>
                  </div>
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md-12 col-sm-12 order-md-5">
                      <button class="btn btn-success btn-rounded" type="button" id="Credit2ValueButtonRefound" tabindex="24"><?php echo lang('refund_title') ?>
                        <span class="glyphicon glyphicon-saved">
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
                <?php if($TypeShva == 0) { ?>
                <div style="display: block" id="CreditDiv3Refound"  class="refund-credit-type-card" data-type="3">
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md col-sm-12  pull-right order-md-3">
                      <label class="control-label"><?php echo lang('payments_num') ?>
                      </label>
                      <select name="Tash" id="Tash3Refound" class="form-control input-lg Tash" tabindex="27">
                        <option value="1">1
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12  pull-right order-md-2">
                      <label class="control-label"><?php echo lang('choose_payment_method') ?>
                      </label>
                      <select name="tashType" id="tashType3Refound" class="form-control input-lg tashType" tabindex="26">
                        <option value="0" selected><?php echo lang('regular') ?>
                        </option>
                        <option value="1"><?php echo lang('payments') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12  pull-right order-md-1">
                      <label><?php echo lang('refund_amount') ?>
                      </label>
                      <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValue3Refound" class="form-control" onkeypress='validate(event)'  tabindex="25">
                    </div>
                  </div>
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md-12 col-sm-12 order-md-9">
                        <button class="btn btn-success btn-rounded mb-20" id="Credit3ValueButtonRefound" data-order-type="<?= OrderLogin::TYPE_REFUND_NEW_CARD ?>">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="js-loading-label d-none">Loading...</span>
                            <span class="js-btn-text"><?= lang('refund_title') ?> <span class="glyphicon glyphicon-saved"></span></span>
                        </button>

                        <div class="iframe-wrapper mb-20 d-none">
                            <iframe src="" frameborder="0" class="add-new-card-iframe w-100 h-600p"></iframe>
                        </div>
                    </div>
                  </div>
                </div>
                <?php } else if(in_array($TypeShva, [PaymentTypeEnum::TYPE_MESHULAM, PaymentTypeEnum::TYPE_TRANZILA])) { ?>
                <div style="display: block;" id="CreditDiv3Refound" class="js-meshulamCreditRefund py-10 refund-credit-type-card" data-type="3">
                  <div>
                    <table class="table" id="meshulam_docs_table">
                      <thead>
                        <tr>
                          <th scope="col"><?php echo lang('choose') ?></th>
                          <th scope="col"><?php echo lang('carteset_doc_num') ?></th>
                          <th scope="col"><?php echo lang('detail') ?></th>
                          <th scope="col"><?php echo lang('date') ?></th>
                          <th scope="col"><?php echo lang('refund_amount') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                    <label for="meshulam_docs_table" id="meshulam_docs_table_lbl" class="text-danger bsapp-fs-13" style="display:none"><?php echo lang('req_field') ?></label>
                  </div>
                  <div class="js-refundMeshulamDiv pt-20" style="display: none">
                    <div class="d-flex">
                      <div class="mie-13">
                        <input class="form-control" id="meshulamRefundAmount" name="meshulamRefundAmount" min="0.00" type="number" placeholder="<?php echo lang('refund_amount') ?>">
                        <label for="meshulamRefundAmount" id="meshulam_refund_errMsg" class="text-danger bsapp-fs-13" style="display:none"><?php echo lang('req_field') ?></label>
                      </div>
                      <div>
                        <button type="button" class="btn btn-primary btn-rounded js-meshulamRefundBtn"><?php echo lang('refund_title') ?></button>
                      </div>
                    </div>
                  </div>
                </div>
                <?php } ?>
                <div style="display: none;" id="CreditDiv4Refound" class="refund-credit-type-card" data-type="4">
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md col-sm-12 pull-right order-md-5">
                      <label class="control-label"><?php echo lang('payments_num') ?>
                      </label>
                      <select name="Tash" id="Tash4Refound" class="form-control input-lg Tash" tabindex="38">
                        <option value="1">1
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-4">
                      <label class="control-label"><?php echo lang('choose_payment_method') ?>
                      </label>
                      <select name="tashType" id="tashType4Refound" class="form-control input-lg tashType" tabindex="37">
                        <option value="0" selected><?php echo lang('regular') ?>
                        </option>
                        <option value="1"><?php echo lang('payments') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-3">
                      <label class="control-label"><?php echo lang('confirmation_number') ?>
                      </label>
                      <input type="text" class="form-control" name="CCode" id="CCode4Refound"  tabindex="36">
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-2">
                      <label class="control-label"><?php echo lang('original_charge_date') ?>
                      </label>
                      <input type="date" class="form-control" name="CDate" id="CDate4Refound"  tabindex="35">
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-1">
                      <label><?php echo lang('charged_price') ?>
                      </label>
                      <input type="number" step="0.01" min="0.01" name="CreditValue" id="CreditValue4Refound" class="form-control" onkeypress='validate(event)'  tabindex="34">
                    </div>
                  </div>
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md col-sm-12 pull-right order-md-6">
                      <label class="control-label"><?php echo lang('choose_company_to_be_paid_off') ?>
                      </label>
                      <select name="TypeBank" id="TypeBank4Refound" class="form-control input-lg" tabindex="41">
                        <option value=""><?php echo lang('choose') ?>
                        </option>
                        <option value="2"><?php echo lang('visa_cal') ?>
                        </option>
                        <option value="1"><?php echo lang('isracard') ?>
                        </option>
                        <option value="6"><?php echo lang('leumi_card') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-5">
                      <label class="control-label"><?php echo lang('choose_credit_card_type') ?>
                      </label>
                      <select name="TypeBrand" id="TypeBrand4Refound" class="form-control input-lg" tabindex="40">
                        <option value=""><?php echo lang('choose') ?>
                        </option>
                        <option value="88"><?php echo lang('mastercard') ?>
                        </option>
                        <option value="2"><?php echo lang('visa') ?>
                        </option>
                        <option value="5"><?php echo lang('isracard') ?>
                        </option>
                        <option value="66"><?php echo lang('diners') ?>
                        </option>
                        <option value="77"><?php echo lang('american_express') ?>
                        </option>
                      </select>
                    </div>
                    <div class="col-md col-sm-12 pull-right order-md-4">
                      <label class="control-label"><?php echo lang('last_four_chars_on_cc') ?>
                      </label>
                      <input type="text" class="form-control" name="CC" id="CC4Refound"  placeholder="<?php echo lang('type_manual') ?>" tabindex="39">
                    </div>
                  </div>
                  <div class="row"  style="padding-top: 10px;">
                    <div class="col-md-12 col-sm-12">
                      <button class="btn btn-success btn-rounded" type="button" id="Credit4ValueButtonRefound" tabindex="44"><?php echo lang('save') ?>
                        <span class="glyphicon glyphicon-saved">
                        </span>
                      </button>
                    </div>
                  </div>
                  <hr>
                  <div class="alertb alert-info my-6 p-6 border-radius-3r"><?php echo lang('attention_typing_required_system_wont_charge') ?>
                    <u><?php echo lang('system_refund_notice') ?>
                    </u>.
                    <br> <?php echo lang('system_refund_invoice') ?>
                  </div>
                </div>
                <div class="alertb alert-warning mt-13"><?php echo lang('refund_notice_receipt') ?>
                </div>
              </div>
                <hr>
                <div class="container-fluid">
                    <div><?= lang('notes_two'); ?></div>
                    <textarea class="form-control summernote d-none" name="Remarks"></textarea>
                </div>
              <hr>
              <div class="alertb alert-dark" role="alert" style="display:block; font-weight:bold; font-size:14px; text-align: start;" ><?php echo lang('refund_details') ?>
              </div>
              <div class="row my-13">
                <div class="col-md-12 col-sm-12">
                  <div id="DocsPaymentsRefound">
                    <table class="table  table-responsive w-100 d-block d-md-table"  style="width: 100%;">
                      <thead>
                        <tr>
                          <th style="width: 5%; text-align: start;">#
                          </th>
                          <th style="width: 25%; text-align: start;"><?php echo lang('refund_type') ?>
                          </th>
                          <th style="width: 25%; text-align: start;"><?php echo lang('detail') ?>
                          </th>
                          <th style="width: 20%; text-align: start;"><?php echo lang('reference') ?>
                          </th>
                          <th style="width: 10%; text-align: start;"><?php echo lang('summary') ?>
                          </th>
                          <th style="width: 15%; text-align: start;"><?php echo lang('actions') ?>
                          </th>
                        </tr>
                      </thead>
                      <tbody>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div class="form-group float-md-right">
                <button type="submit" name="submit" id="ReceiptRefoundBtn" class="btn btn-danger btn-rounded" disabled ><?php echo lang('refund_button') ?>
                </button>
              </div>
              <div class="form-group float-md-left">
                <button type="button" name="CancelDoc" id="CancelDocButtonRefound" class="btn btn-danger d-none" data-ip-modal="#CancelDoc" disabled><?php echo lang('cancel_doc') ?>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<input type="hidden" id="meTestRefound" data-ip-modal="#CancelPaymentsRefoundPopup">
<div class="ip-modal text-start" id="CancelPaymentsRefoundPopup" data-backdrop="static">
  <div class="ip-modal-dialog">
    <form action="POSClientCancelPaymentsRefound" id="POSClientCancelPaymentsRefound" name="POSClientCancelPaymentsRefound" class="ip-modal-content ajax-form clearfix">
      <div class="ip-modal-header  d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('cancel_refund') ?>
        </h4>
        <a class="ip-close CancelPaymentsClose" title="Close" style="" data-dismiss="modal">&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <input name="GroupNumber" type="hidden" value="<?php echo $GroupNumberRefound; ?>">
        <input name="ClientId" id="CancelPayments_TempsIdRefound" type="hidden" value="<?php echo $Supplier->id; ?>">
        <input name="TempListsId" id="CancelPayments_TempsListsIdRefound" type="hidden" value="">
        <input name="Finalinvoicenum" id="CancelDocs_FinalinvoicenumRefound" type="hidden" value="0">
        <div class="form-group" >
          <label><?php echo lang('q_cancel_refund') ?>
          </label>
        </div>
      </div>
      <div class="ip-modal-footer d-flex justify-content-between">
        <div class="ip-actions">
          <button type="submit" name="submit" id="CancelReceiptRefoundBtn" class="btn btn-primary text-white"><?php echo lang('yes') ?>
          </button>
        </div>
        <button type="button" class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php echo lang('no') ?>
        </button>
      </div>
    </form>
  </div>
</div>
<div class="ip-modal text-start" id="CancelDocRefound" data-backdrop="static">
  <div class="ip-modal-dialog" >
    <form class="ip-modal-content ajax-form clearfix"   action="POSClientCancelDocsRefound" >
      <div class="ip-modal-header d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('cancel_doc') ?>
        </h4>
        <a class="ip-close" title="Close" data-dismiss="modal">&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <input name="ClientId" id="CancelDocs_TempsIdRefound" type="hidden" value="<?php echo $Supplier->id; ?>">
        <input name="GroupNumber" type="hidden" value="<?php echo $GroupNumberRefound; ?>">
        <div class="form-group" >
          <label><?php echo lang('are_you_sure_cancel_doc') ?>
          </label>
        </div>
        <div class="alertb alert-danger" id="POSCancelDocsErrorRefound" style="display: none;">
          <span id="POSCancelDocsErrorTextRefound">
          </span>
        </div>
      </div>
      <div class="ip-modal-footer d-flex justify-content-between">
        <div class="ip-actions d-flex">
          <button type="submit" name="submit" class="btn btn-primary text-white"><?php echo lang('yes') ?>
          </button>
        </div>
        <button type="button" class="btn btn-dark text-white ip-close" data-dismiss="modal"><?php echo lang('no') ?>
        </button>
      </div>
    </form>
  </div>
</div>
<?php if (Auth::userCan('72')): ?>
<div class="tab-pane fade" role="tabpanel" id="user-paytoken">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-sync fa-fw">
      </i> <?php echo lang('customer_direct_debit') ?>
    </div>
      <?php if($Supplier->Status == 1) { ?>
          <div class="card-body">
              <div class="text-center"><?= lang('unable_to_manage_keva_client_in_archive') ?></div>
          </div>
      <?php } else { ?>
          <div class="card-body">
      <div class="row" style="padding-bottom:10px; padding-left: 15px; padding-right: 15px;">
        <span style=" padding-top:5px;">
          <a href="#" data-ip-modal="#AddKevaPopup" name="AdKeva" class="btn btn-dark text-white"><?php echo lang('add_direct_debit_button') ?>
          </a>
        </span>
      </div>
      <div class="alertb alert-warning text-start" >
        <strong><?php echo lang('pay_attention') ?>
        </strong> <?php echo lang('recurring_payment_notice_one') ?>
        <br>
        <?php echo lang('recurring_payment_notice_two') ?>
        <br>
        <?php echo lang('recurring_payment_notice_three') ?>
      </div>
      <table class="table table-hover text-start table-responsive-md" style="font-size:12px; font-weight:bold;"  id="Token">
        <thead class="thead-dark">
          <tr style="background-color:#bce8f1;">
            <th  style="text-align:start;">#
            </th>
            <th  style="text-align:start;"><?php echo lang('item_single') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('table_charge_amount') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('table_number_of_cycles') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('total') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('table_cycle_balance') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('table_last_payment') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('table_next_payment') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('token') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('expires_at') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('status_table') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('table_date_add') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('table_defined_by') ?>
            </th>
            <th  style="text-align:start;"><?php echo lang('actions') ?>
            </th>
          </tr>
        </thead>
        <tbody>
        <?php
        $ClassActs = DB::table('paytoken')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $Supplier->id)->orderBy('Dates', 'DESC')->get();
        //$mp = new MeshulamPayments();
        //$newPayments = $mp->getPaymentsForClientProfile($Supplier->id,$CompanyNum);
        //if($newPayments != false && !empty($newPayments)){
        //    $ClassActs = array_merge($ClassActs,$newPayments);
        //}
        $i = '1';
        foreach ($ClassActs as $ClassAct) {
            $UsersName = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$ClassAct->UserId)->first();
            $TokenInfo = DB::table('token')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassAct->TokenId)->first();
            $CountPayment = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('KevaId', '=', $ClassAct->id)->whereIn('Status', array(1, 2))->where('ClientId', '=', $ClassAct->ClientId)->count();
            $NextPayment = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('KevaId', '=', $ClassAct->id)->where('Status', '=', '0')->where('ActStatus', '=', '0')->where('ClientId', '=', $ClassAct->ClientId)->orderBy('Date', 'ASC')->first();
            if (@$NextPayment->id != '') {
                $NextPayment = with(new DateTime(@$NextPayment->Date))->format('d/m/Y');
            } else {
                $NextPayment = '';
            }
            $L4digit = @$TokenInfo->L4digit;
            $CardTokef = @$TokenInfo->Tokef;

            if (@$TokenInfo->Type == '0') {
                $Month = mb_substr($CardTokef, 2);
                $Year = '20' . mb_substr($CardTokef, 0, 2);
            } else {
                $Month = mb_substr($CardTokef, 0, 2);
                $Year = '20' . mb_substr($CardTokef, 2);
            }
            ?>
            <tr>
                <td>
                    <?php echo $i; ?>
                </td>
                <td>
                    <?php echo $ClassAct->Text; ?>
                </td>
                <td>₪
                    <?php echo $ClassAct->Amount; ?>
                </td>
                <td>
                    <?php echo $ClassAct->NumPayment; ?>
                </td>
                <td>
                    <?php if ($ClassAct->NumPayment == '999') {
                    } else { ?> ₪
                        <?php echo $ClassAct->Amount * $ClassAct->NumPayment; ?>
                    <?php } ?>
                </td>
                <td>
                    <?php echo $ClassAct->NumPayment - $CountPayment; ?>
                </td>
                <td class="text-success">
                    <?php if (@$ClassAct->LastPayment == '') {
                    } else {
                        echo with(new DateTime(@$ClassAct->LastPayment))->format('d/m/Y');
                    } ?>
                </td>
                <td class="text-dark">
                    <?php
                    if (isset($ClassAct->newApi) && $ClassAct->newApi == 1) {
                        echo $ClassAct->NextPayment;
                    } else {
                        echo @$NextPayment;
                    }
                    ?>
                </td>
                <td>
                    <span class="unicode-plaintext"><?php echo '****' . $L4digit; ?></span>
                </td>
                <td>
                    <?php echo $Month ?>/
                    <?php echo $Year; ?>
                </td>
                <td>
                    <?php if (@$ClassAct->Status == '0') {
                        echo '<span class="text-success">' . lang('active') . '</span>';
                    } else if (@$ClassAct->Status == '1') {
                        echo '<span class="text-danger">' . lang('canceled') . '</span>';
                    } else if (@$ClassAct->Status == '2') {
                        echo '<span class="text-info">' . lang('completed_client_profile') . '</span>';
                    } ?>
                </td>
                <td>
                    <?php echo with(new DateTime(@$ClassAct->Dates))->format('d/m/Y H:i'); ?>
                </td>
                <td>
                    <?php if (@$UsersName->display_name == '') {
                        echo lang('automatic');
                    } else {
                        echo $UsersName->display_name;
                    } ?>
                </td>
                <td class="text-primary">
                    <?php if (isset($ClassAct->newApi) && $ClassAct->newApi == 1) { ?>
                        <a href='javascript:UpdatePayToken("<?php echo $ClassAct->id; ?>",1);' class="text-primary">
                    <span class="text-primary"><?php echo lang('recurring_payment_edit') ?>
                    </span>
                        </a>
                    <?php } else { ?>
                        <a href='javascript:UpdatePayToken("<?php echo $ClassAct->id; ?>",0);' class="text-primary">
                <span class="text-primary"><?php echo lang('recurring_payment_edit') ?>
                </span>
                        </a>
                    <?php } ?>
                </td>
            </tr>
            <?php ++$i;
            $NextPayment = '';
        } ?>
        </tbody>
      </table>
      <!-- Edit DepartmentsPopup -->
      <div class="ip-modal text-start" id="AddKevaPopup">
        <div class="ip-modal-dialog BigDialog">
          <form action="AddPayToken"  class="ip-modal-content ajax-form clearfix" autocomplete="off" >
            <div class="ip-modal-header d-flex justify-content-between" >
              <h4 class="ip-modal-title"><?php echo lang('add_dircet_debit') ?>
              </h4>
              <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="">&times;
              </a>
            </div>
            <div class="ip-modal-body" >
              <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
              <div class="form-group">
                <label><?php echo lang('payment_cycle_direct') ?>
                </label>
                <select name="TypeKeva" id="TypeKeva" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('select_payment_option') ?>">
                  <option value="0" selected><?php echo lang('membership_renew_perm') ?>
                  </option>
                  <option value="1"><?php echo lang('membreship_renew_rest') ?>
                  </option>
                </select>
              </div>
              <div id="DivNumPayment2">
                <div class="alertb alert-warning" >
                  <strong><?php echo lang('pay_attention') ?>
                  </strong> <?php echo lang('recurring_payment_notice_cycle') ?>
                  <br>
                  <?php echo lang('unrestricted_notice_2') ?>
                </div>
              </div>
              <div id="DivNumPayment" style="display: none;">
                <div class="form-group" >
                  <label><?php echo lang('number_of_cycles') ?>
                  </label>
                  <input type="number" name="NumPayment" id="NumPaymentTe" min="1"  class="form-control"  onkeypress='validate(event)' value="999">
                </div>
                <div class="alertb alert-warning" >
                  <strong><?php echo lang('pay_attention') ?>
                  </strong> <?php echo lang('cycle_notice') ?>
                  <br>
                  <?php echo lang('cycle_notice_2') ?>
                </div>
              </div>
              <div class="form-group">
                <label><?php echo lang('attached_to_item') ?>
                </label>
                <select name="ItemId" id="ItemsKeva" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('select_item') ?>">
                  <option value="" data-ajax="0"><?php echo lang('choose') ?>
                  </option>
                  <option value="" data-ajax="1"><?php echo lang('choose') ?>
                  </option>
                  <?php
$PagesInfos = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
    ->where('isPaymentForSingleClass', '=', 0)
    ->where('Disabled', '=', 0)
    ->orderBy('ItemName', 'ASC')->get();
foreach ($PagesInfos as $PagesInfo) {
if ($PagesInfo->MemberShip!='BA999'){
$DepartmentInfo = DB::table('membership_type')->where('CompanyNum', $CompanyNum)->where('id','=', $PagesInfo->MemberShip)->first();
}
if ($PagesInfo->Vaild=='1'){
$DataDates = '0';
}
else {
$DataDates = '1';
}
?>
                  <option value="<?php echo $PagesInfo->id; ?>" data-ajax="<?php echo $DataDates; ?>" >
                    <?php if ($PagesInfo->MemberShip!='BA999'){   echo @$DepartmentInfo->Type; ?> ::
                    <?php } ?>
                    <?php echo $PagesInfo->ItemName; ?> ::
                    <?php echo $PagesInfo->ItemPrice; ?> ₪
                  </option>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group" >
                <label><?php echo lang('price_to_direct_charge') ?>
                </label>
                <input type="number" min="1"  step="any" name="Amount" class="form-control"  value="">
              </div>
              <div class="form-group" >
                <label><?php echo lang('set_date_to_charge') ?>
                </label>
                <input type="date" name="NextPayment" class="form-control" value="" min="<?php echo (date('H:i:s') > '23:30:00') ? date('Y-m-d', strtotime('+1 day')) : date('Y-m-d'); ?>">
              </div>
              <div class="alertb alert-warning" >
                <strong><?php echo lang('pay_attention') ?>
                </strong> <?php echo lang('direct_charge_date_notice') ?>
              </div>
                <?php if(Auth::user()->role_id == 1 || $CompanyNum == 171894) { ?>
                  <div class="row">
                    <div class="col-md-6 col-sm-12">
                      <div class="form-group" >
                        <label><?php echo lang('next_direct_charge_date') ?>
                        </label>
                        <input type="number" name="NumDate" max="10" min="1" class="form-control" value="1" onkeypress='validate(event)' >
                      </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                      <div class="form-group">
                        <label><?php echo lang('choose_option') ?>
                        </label>
                        <select name="PayStep" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('select_payment_option') ?>">
                          <option value="1"><?php echo lang('days') ?>
                          </option>
                          <option value="2"><?php echo lang('weeks') ?>
                          </option>
                          <option value="3" selected><?php echo lang('months') ?>
                          </option>
                          <option value="4"><?php echo lang('years') ?>
                          </option>
                        </select>
                      </div>
                    </div>
                  </div>
              <?php } ?>
              <div class="form-group">
                <label><?php echo lang('select_cc_short') ?>
                </label>
                <select name="TokenId" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('select_cc') ?>">
                  <option value=""><?php echo lang('choose') ?>
                  </option>
                  <?php

                  $clientIdsArr = [$Supplier->id];
                  if ($Supplier->parentClientId != 0) {
                      $clientIdsArr[] = $Supplier->parentClientId;
                  }
                  if ($Supplier->PayClientId != 0) {
                      $clientIdsArr[] = $Supplier->PayClientId;
                  }
                  $TokenInfos = Token::where('CompanyNum', '=', $CompanyNum)
                      ->whereIn('ClientId', $clientIdsArr)
                      ->where('Status', '=', '0')
                      ->get();


                  foreach ($TokenInfos as $TokenInfo) {
                      $L4digit = $TokenInfo->L4digit;

                      $CardTokef = @$TokenInfo->Tokef;
                      if ($TokenInfo->Type == '0') {
                          $Month = mb_substr($CardTokef, 2);
                          $Year = '20' . mb_substr($CardTokef, 0, 2);
                      } else {
                          $Month = mb_substr($CardTokef, 0, 2);
                          $Year = '20' . mb_substr($CardTokef, 2);
                      }
                      ?>
                  <option value="<?= $TokenInfo->id ?>">
                      <?= '****' . $L4digit ?> ::
                      <?= $Month ?>/
                      <?= $Year ?>
                      <?php
                        if($Supplier->parentClientId == $TokenInfo->ClientId || $Supplier->PayClientId == $TokenInfo->ClientId) {
                            $payerName = new Client($TokenInfo->ClientId);
                            echo ' - '.lang('card_of').' '.$payerName->CompanyName;
                        }
                      ?>
                  </option>
                  <?php } ?>
                </select>
              </div>
              <input type="hidden" name="tashTypeKeva" value="0">
              <input type="hidden" name="TashKeva" value="1">
              <div class="row" style="display: none;">
                <div class="col-md-6 col-sm-12">
                  <div class="form-group">
                    <label><?php echo lang('choose_payment_method') ?>
                    </label>
                    <select name="tashTypeKeva_old"  class="form-control tashTypeKeva" style="width:100%;"  data-placeholder="<?php echo lang('select_buy_option') ?>">
                      <option value="0" selected><?php echo lang('regular') ?>
                      </option>
                      <option value="1"><?php echo lang('payments') ?>
                      </option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12">
                  <div class="form-group" >
                    <label><?php echo lang('cc_payments_number') ?>
                    </label>
                    <select name="TashKeva_old" class="form-control TashKeva" style="width:100%;"  data-placeholder="<?php echo lang('select_payments_number') ?>">
                      <option value="1" selected>1
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="alertb alert-warning" >
                <strong><?php echo lang('pay_attention') ?>
                </strong> <?php echo lang('save_subscription_notice') ?>
                <br>
                <?php echo lang('validity_calculation_notice') ?>
              </div>
            </div>
            <div class="ip-modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-light ip-close" data-dismiss="modal">
                    <?php echo lang('close') ?>
                </button>
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary">
                <?php echo lang('save_changes_button') ?>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
      <!-- end Edit DepartmentsPopup -->
      <script>
        $("#TypeKeva").change( function()
                              {
          var Id = $(this).val();
          if (Id=='0') {
            $('#DivNumPayment').hide();
            $('#DivNumPayment2').show();
            $('#NumPaymentTe').val('999');
          }
          else {
            $('#DivNumPayment').show();
            $('#DivNumPayment2').hide();
            $('#NumPaymentTe').val('1');
          }
        }
                             );
        $(document).ready(function() {
          $('#TypeKeva').trigger('change');
        }
                         );
        $('#TypeKeva').on('change', function() {
          var Id = this.value;
          $('#ItemsKeva option')
            .hide() // hide all
            .filter('[data-ajax="'+$(this).val()+'"]') // filter options with required value
            .show();
          // and show them
          $('#ItemsKeva').val('');
        }
                         );
      </script>
      <!-- Edit DepartmentsPopup -->
      <div class="ip-modal text-start" id="PayTokenEditPopup" tabindex="-1">
        <div class="ip-modal-dialog BigDialog">
          <form action="EditPayToken"  class="ajax-form clearfix ip-modal-content" autocomplete="off">
            <div class="ip-modal-header d-flex justify-content-between" >
              <h4 class="ip-modal-title"><?php echo lang('recurring_payment_edit') ?>
              </h4>
              <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="">&times;
              </a>
            </div>
            <div class="ip-modal-body" >
              <div id="resultPayToken">
              </div>
            </div>
            <div class="ip-modal-footer d-flex justify-content-between">
              <div class="ip-actions" id="ShowSaveKeva" style="display: none;">
                <button type="submit" name="submit" class="btn btn-dark text-white">
                <?php echo lang('save_changes_button') ?>
                </button>
              </div>
              <button type="button" class="btn btn-default ip-close" data-dismiss="modal">
              <?php echo lang('close') ?>
              </button>
            </div>
          </form>
        </div>
      </div>
      <!-- end Edit DepartmentsPopup -->
    </div>
      <?php } ?>
  </div>
</div>
<?php endif ?>
<?php if (Auth::userCan('79')): ?>
<div class="tab-pane fade" role="tabpanel" id="user-log">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-bars fa-fw">
      </i> <?php echo lang('log_single') ?>
    </div>
    <div class="card-body text-start" >
      <?php
$SmsLogs = DB::table('log')->where('ClientId', '=', $Supplier->id)->orderBy('Dates', 'DESC')->limit(300)->get();
if (empty($SmsLogs)) {
echo '<div  class="text-start">'.lang('no_log_data').'</div>';
}
else {
?>
      <table class="table table-bordered table-hover table-responsive-md text-start wrap Log"   cellspacing="0" width="100%" id="AccountsTable">
        <thead class="thead-dark">
          <tr>
            <th style="text-align:start;">#
            </th>
            <th style="text-align:start;"><?php echo lang('contet_single') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('date') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('hour') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('representative') ?>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php
$i = '1';
foreach ($SmsLogs as $SmsLog) {
    if($SmsLog->UserId != 0 && (!isset($UserNameLogs) || $UserNameLogs->id != $SmsLog->UserId)) {
        $UserNameLogs = DB::table('users')->where('id', $SmsLog->UserId)->first();
    }
?>
          <tr>
            <td>
              <?php echo $i; ?>
            </td>
            <td>
              <?php echo html_entity_decode(@$SmsLog->Text); ?>
            </td>
            <td  style="text-align: start;">
              <?php echo with(new DateTime($SmsLog->Dates))->format('d/m/Y'); ?>
            </td>
            <td  style="text-align: start;">
              <?php echo with(new DateTime($SmsLog->Dates))->format('H:i:s'); ?>
            </td>
            <td>
              <?php
              if (Auth::userCan('ManageAgents') && $SmsLog->UserId != 0)
                  echo '<a href="AgentProfile.php?u='.($UserNameLogs->id ?? 0).'">';

              echo $SmsLog->UserId != 0 ? htmlentities($UserNameLogs->display_name ?? '') : lang('system_log');

              if (Auth::userCan('ManageAgents') && $SmsLog->UserId != 0)
                  echo '</a>';
              ?>
            </td>
          </tr>
          <?php
++ $i; }
?>
        </tbody>
      </table>
      <?php } ?>
    </div>
  </div>
</div>
<?php endif ?>
<div class="tab-pane fade" role="tabpanel" id="user-Health">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fab fa-wpforms  fa-fw">
      </i> <?php echo lang('customer_card_forms') ?>
    </div>
    <div class="card-body text-start" >
      <?php
$ClientHealths = DB::table('healthforms_answers')->where('CompanyNum', $CompanyNum)->where('ClientId', $Supplier->id)
->orderBy('created', 'DESC')->get();
$ClientForms = DB::table('dynamicforms_answers')->where('CompanyNum', $CompanyNum)->where('ClientId', $Supplier->id)
->orderBy('created', 'DESC')->get();
$ClientUploads = Upload::getByClientId((int)$ClientId);
?>
      <form id="ClientDocUploadForm" action="ClientDocumentUpload" class="d-none">
      <input type="file" id="docUpload" name="docUpload" accept="image/jpeg, image/jpg, image/png, application/pdf">
      </form>
      
      <table class="table table-bordered table-hover dt-responsive text-start wrap tableForms"   cellspacing="0" width="100%" id="AccountsTable">
        <thead class="thead-dark">
          <tr>
            <th style="text-align:start;">#
            </th>
            <th style="text-align:start;"><?php echo lang('class_table_name') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('detail') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('date') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('actions') ?>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php
$i = '1';
foreach ($ClientHealths as $ClientHealth) {
$HealthInfo = DB::table('healthforms')->where('CompanyNum', $CompanyNum)->where('id', @$ClientHealth->FormId)->first();
$FormsTitle = '<i class="fas fa-heartbeat fa-fw"></i> '.lang('health_declaration');
?>
          <tr>
            <td>
              <?php echo $i; ?>
            </td>
            <td>
              <?php echo @$FormsTitle; ?>
            </td>
            <td>
              <?php echo @$HealthInfo->GroupNumber; ?>
            </td>
            <td  style="text-align: start;">
              <span style="display: none;">
                <?php echo with(new DateTime(@$ClientHealth->created))->format('Ymd'); ?>
              </span>
              <?php echo with(new DateTime(@$ClientHealth->created))->format('d/m/Y'); ?>
            </td>
            <td>
              <a href="javascript:void(0);" onclick="TINY.box.show({iframe:'PDF/HealthPDF.php?id=<?php echo @$ClientHealth->id; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})">
                <span class="text-primary"><?php echo lang('view_button') ?>
                </span>
              </a>
            </td>
          </tr>
          <?php
++ $i; }
foreach ($ClientForms as $ClientForm) {
$FormInfo = DB::table('dynamicforms')->where('CompanyNum', $CompanyNum)->where('id', @$ClientForm->FormId)->first();
$FormsTitle = '<i class="fab fa-wpforms fa-fw"></i> '.$FormInfo->name;
?>
          <tr>
            <td>
              <?php echo $i; ?>
            </td>
            <td>
              <?php echo @$FormsTitle; ?>
            </td>
            <td>
              <?php echo @$FormInfo->GroupNumber; ?>
            </td>
            <td  style="text-align: start;">
              <span style="display: none;">
                <?php echo with(new DateTime(@$ClientForm->created))->format('Ymd'); ?>
              </span>
              <?php echo with(new DateTime(@$ClientForm->created))->format('d/m/Y'); ?>
            </td>
            <td>
              <a href="javascript:void(0);" onclick="TINY.box.show({iframe:'PDF/FormsPDF.php?id=<?php echo @$ClientForm->id; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})">
                <span class="text-primary"><?php echo lang('view_button') ?>
                </span>
              </a>
            </td>
          </tr>
          <?php
++ $i; }
?>
          <?php if(!empty($ClientUploads) && is_array($ClientUploads)): ?>
            <?php foreach($ClientUploads as $Upload): ?>
            <tr>
              <td><?php echo $i++; ?></td>
              <td><?php echo $Upload->display_name ; ?></td>
              <td><?php echo $Upload->description; ?></td>
              <td><?php echo date('d/m/Y', strtotime($Upload->created_at)) ?></td>
              <td>
                <a href="javascript:void(0);" onclick="TINY.box.show({iframe:'<?php echo 'files/uploads/' . @$Upload->file_name; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})">
                  <span class="text-primary"><?php echo lang('view_button') ?></span>
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="tab-pane fade" role="tabpanel" id="user-clientcontact">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fas fa-users fa-fw">
      </i> <?php echo lang('customer_card_contacts') ?>
    </div>
    <div class="card-body text-start" >
      <?php
$ClientContacts = DB::table('clientcontact')->where('CompanyNum', $CompanyNum)->where('ClientId', $Supplier->id)->orderBy('ContactName', 'ASC')->get();
?>
      <div class="row text-start pb-15 pis-30"  >
        <a href="#" data-ip-modal="#AddClientContactPopup" name="AddClientContactPopup" class="btn btn-primary text-white"><?php echo lang('add_new_contact') ?>
        </a>
      </div>
      <table class="table table-bordered table-hover table-responsive-md text-start wrap Log"   cellspacing="0" width="100%" id="AccountsTable">
        <thead class="thead-dark">
          <tr>
            <th style="text-align:start;">#
            </th>
            <th style="text-align:start;"><?php echo lang('role_single') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('class_table_name') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('cell_table_search') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('telephone') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('fax_single') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('email_table') ?>
            </th>
            <th style="text-align:start;"><?php echo lang('actions') ?>
            </th>
          </tr>
        </thead>
        <tbody>
          <?php
$i = '1';
foreach ($ClientContacts as $ClientContact) {
?>
          <tr>
            <td>
              <?php echo $i; ?>
            </td>
            <td>
              <?php echo $ClientContact->JobsRole; ?>
            </td>
            <td>
              <?php echo $ClientContact->ContactName; ?>
            </td>
            <td>
              <?php echo $ClientContact->ContactMobile; ?>
            </td>
            <td>
              <?php echo $ClientContact->ContactPhone; ?>
            </td>
            <td>
              <?php echo $ClientContact->ContactFax; ?>
            </td>
            <td>
              <?php echo $ClientContact->ContactEmail; ?>
            </td>
            <td>
              <a href='javascript:UpdateClientContacts("<?php echo $ClientContact->id; ?>");' ><?php echo lang('edit_contact') ?>
              </a> |
              <a href='javascript:DelClientContacts("<?php echo $ClientContact->id; ?>");' ><?php echo lang('remove_contact') ?>
              </a>
            </td>
          </tr>
          <?php
++ $i; }
?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<div class="ip-modal" id="AddClientContactPopup"  data-backdrop="static">
  <div class="ip-modal-dialog">
    <form  action="AddNewContactClient"  class="ajax-form clearfix ip-modal-content text-start" autocomplete="off" >
      <div class="ip-modal-header d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('adding_new_contact') ?>
        </h4>
        <a class="ip-close" title="Close"  data-dismiss="modal">&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
        <div class="form-group" >
          <label><?php echo lang('role_single') ?>
          </label>
          <input type="text" name="JobsRole" class="form-control" >
        </div>
        <div class="form-group" >
          <label><?php echo lang('name_table') ?>
          </label>
          <input type="text" name="ContactName" class="form-control" required >
        </div>
        <div class="form-group" >
          <label><?php echo lang('cell_table') ?>
          </label>
          <input type="text" name="ContactMobile" class="form-control" onkeypress='validate(event)' required >
        </div>
        <div class="form-group" >
          <label><?php echo lang('telephone') ?>
          </label>
          <input type="text" name="ContactPhone" class="form-control" onkeypress='validate(event)' >
        </div>
        <div class="form-group" >
          <label><?php echo lang('fax_single') ?>
          </label>
          <input type="text" name="ContactFax" class="form-control" onkeypress='validate(event)' >
        </div>
        <div class="form-group" >
          <label><?php echo lang('email') ?>
          </label>
          <input type="email" name="ContactEmail" class="form-control" >
        </div>
      </div>
      <div class="ip-modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-light ip-close ip-closePopUp" data-dismiss="modal"><?php echo lang('action_cacnel') ?>
          </button>
          <div class="ip-actions">
          <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('add') ?>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="ip-modal" id="EditClientContactPopup" data-backdrop="static">
  <div class="ip-modal-dialog">
    <form action="EditClientContact"  class="ajax-form clearfix ip-modal-content text-start">
      <div class="ip-modal-header d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('contact_edit') ?>
        </h4>
        <a class="ip-close" title="Close"   data-dismiss="modal">&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <input type="hidden" name="ContactIds">
        <div id="resultUpdateClientContacts">
        </div>
      </div>
      <div class="ip-modal-footer">
        <div class="ip-actions">
          <button type="submit" name="submit" class="btn btn-success">
          <?php echo lang('save_changes_button') ?>
          </button>
        </div>
        <button type="button" class="btn btn-default ip-close" data-dismiss="modal">
        <?php echo lang('close') ?>
        </button>
      </div>
    </form>
  </div>
</div>
<div class="ip-modal" id="DelClientContactPopup" data-backdrop="static">
  <div class="ip-modal-dialog">
    <form  action="DelClientContact"    class="ajax-form clearfix ip-modal-content text-start">
      <div class="ip-modal-header d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('contact_remove') ?>
        </h4>
        <a class="ip-close" title="Close"  data-dismiss="modal">&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <input name="ContactId" id="ContactId" type="hidden" value="">
        <div class="form-group" >
          <label><?php echo lang('q_remove_contact') ?>
          </label>
        </div>
      </div>
      <div class="ip-modal-footer d-flex justify-content-between">
        <div class="ip-actions">
          <button type="submit" name="submit" class="btn btn-success ip-close"><?php echo lang('yes') ?>
          </button>
        </div>
        <button type="button" class="btn btn-default ip-close" data-dismiss="modal"><?php echo lang('no') ?>
        </button>
      </div>
    </form>
  </div>
</div>
<?php if (is_object($PipeNow) != '0') {
    include_once 'clientProfile/tabLeadInfo/tabLeadInfo.php';
} ?>
<script>
  $( ".AgentLoop" ).select2( {
    theme:"bsapp-dropdown",placeholder: "<?php echo lang('search_representative') ?>", minimumInputLength: 0,language: "<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_lang'] : 'he' ?>", allowClear: false, width: '100%' }
                           );
</script>
<?php if (Auth::userCan('73')): ?>
<div class="tab-pane fade" role="tabpanel" id="user-token">
  <div class="card spacebottom">
    <div class="card-header text-start">
      <i class="fab fa-expeditedssl fa-fw">
      </i> <?php echo lang('add_cc_title') ?>
    </div>
    <div class="card-body">
        <button class="btn btn-success mb-20 js-new-card-iframe-button"
                data-order-type="<?= OrderLogin::TYPE_ADD_NEW_CARD ?>">
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            <span class="js-loading-label d-none">Loading...</span>
            <span class="js-btn-text"><?= lang('encrypt_new_cc') ?></span>
        </button>

        <div class="iframe-wrapper mb-20 d-none">
            <iframe src="" frameborder="0" class="add-new-card-iframe w-100 h-600p"></iframe>
        </div>

        <table class="table table-hover table-responsive-md " style="font-size:12px; font-weight:bold;" id="Token">
            <thead class="thead-dark">
            <tr style="background-color:#bce8f1;">
                <th style="text-align:start;">#
                </th>
                <th style="text-align:start;"><?php echo lang('token') ?>
                </th>
                <th style="text-align:start;"><?php echo lang('expires_at') ?>
                </th>
                <th style="text-align:start;"><?php echo lang('status_table') ?>
                </th>
                <th style="text-align:start;"><?php echo lang('table_date_add') ?>
                </th>
                <th style="text-align:start;"><?php echo lang('table_defined_by') ?>
                </th>
                <th style="text-align:start;"><?php echo lang('actions') ?>
                </th>
            </tr>
            </thead>
            <tbody class="text-start">
            <?php
            $ClientIdForToken = $Supplier->parentClientId ?: $Supplier->id;

            $TokensList = Token::where('ClientId', '=', $ClientIdForToken)
                ->where('Private', '=', '0')
                ->orderBy('Dates', 'DESC')
                ->get();

            $tokenUsersIds = [];
            foreach ($TokensList as $token) {
                if ($token->UserId != 0) {
                    $tokenUsersIds[] = $token->UserId;
                }
            }

            $tokenUsersList = [];
            if (!empty($tokenUsersIds)) {
                $tokenUsersList = Users::whereIn('id', $tokenUsersIds)->get();
            }
            $tmp = [];
            foreach ($TokensList as $tokenModel) {
                $tokenAsArray = $tokenModel->toArray();

                if ($tokenModel->UserId) {
                    foreach ($tokenUsersList as $tokenUser) {
                        if ($tokenUser->id == $tokenModel->UserId) {
                            $tokenAsArray['user'] = $tokenUser;
                            break;
                        }
                    }
                }

                $tmp[] = $tokenAsArray;
            }

            $TokensList = $tmp;

            $i = 1;
            foreach ($TokensList as $token) {
                ?>
                <tr>
                    <td>
                        <?php echo $i; ?>
                        <?php echo !empty($parent) ? ' <span class="text-info">(' . $parent->CompanyName . ')</span>' : ''; ?>
                    </td>
                    <td>
                        <span class="unicode-plaintext"><?php echo '****' . $token['L4digit']; ?></span>
                    </td>
                    <td>
                        <?php if ($token['Type'] == '0') {
                            echo substr($token['Tokef'], -2) . '/' . substr($token['Tokef'], 0, 2);
                        } else {
                            echo substr($token['Tokef'], 0, 2) . '/' . substr($token['Tokef'], -2);
                        } ?>
                    </td>
                    <td>
                        <?php if ($token['Status'] == '0') {
                            echo lang('active');
                        } else if ($token['Status'] == '1') {
                            echo lang('canceled');
                        } ?>
                    </td>
                    <td>
                        <?php echo (new DateTime($token['Dates']))->format('d/m/Y H:i'); ?>
                    </td>
                    <td>
                        <?php echo $token['user']->display_name ?? ''; ?>
                    </td>
                    <td class="text-primary">
                        <a href='javascript:UpdateToken("<?php echo $token['id']; ?>");' class="text-primary">
                <span class="text-primary"><?php echo lang('edit_token') ?>
                </span>
                        </a>
                    </td>
                </tr>
                <?php
                ++$i;
            } ?>
            </tbody>
        </table>
      <!-- Edit DepartmentsPopup -->
      <div class="ip-modal text-start" id="TokenEditPopup" tabindex="-1">
        <div class="ip-modal-dialog">
          <form action="EditToken"  class="ajax-form clearfix ip-modal-content" >
            <div class="ip-modal-header d-flex justify-content-between" >
              <h4 class="ip-modal-title"><?php echo lang('edit_token') ?>
              </h4>
              <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true"  >&times;
              </a>
            </div>
            <div class="ip-modal-body" >
              <input type="hidden" name="TokenId">
              <div id="resultToken">
              </div>
            </div>
            <div class="ip-modal-footer d-flex justify-content-between">
              <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-dark text-white">
                <?php echo lang('save_changes_button') ?>
                </button>
              </div>
              <button type="button" class="btn btn-default ip-close" data-dismiss="modal">
              <?php echo lang('close') ?>
              </button>
            </div>
          </form>
        </div>
      </div>
      <!-- end Edit DepartmentsPopup -->
    </div>
  </div>
</div>
<?php endif ?>
</div>
</div>
</div>
</div>
</div>
<?php if (Auth::userCan('54')): ?>
<!-- Add Contact -->
<div class="ip-modal text-start" id="AddActivityPopup" tabindex="-1">
  <div class="ip-modal-dialog">
    <form class="ip-modal-content ajax-form clearfix" action="AddActivity"  >
      <div class="ip-modal-header d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('customer_card_add_membership') ?>
        </h4>
        <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
        <div class="form-group">
          <label><?php echo lang('select_subscription') ?>
            <em>
            <?php echo lang('req_field') ?>
            </em>
          </label>
          <select name="Items1" id="Items1" class="form-control select2" style="width:100%;"  data-placeholder="<?php echo lang('select_subscription') ?>"  >
            <option value="">
            </option>
            <?php
if ($Supplier->Status != '2') {
    $Activities = DB::table('items')
        ->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
        ->where('isPaymentForSingleClass', '=', 0)
        ->where('Disabled', '=', 0)
        ->where(function($q) {
            $q->where('Department', '=', 4)
                ->Orwhere('Department', '!=', 4)->where('MemberShip', '!=', 'BA999');
        })
        ->orderBy('Department', 'ASC')->get();
}
else {
  $Activities = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
      ->where('isPaymentForSingleClass', '=', 0)
      ->where('Disabled', '=', 0)
      ->where(function($q) {
          $q->where('Department', '=', 4)
              ->Orwhere('Department', '=', 3)->where('MemberShip', '!=', 'BA999');
      })
      ->orderBy('Department', 'ASC')->get();
}

foreach ($Activities as $Activitie) {
    if(!isset($SettingsInfo->membership_types[0]) || (count($SettingsInfo->membership_types) <= 1)) {
        $Type = '';
    } else if($Activitie->MemberShip =='BA999') {
        $Type = lang('no_membership_type');
    } else  {
        $membership_type = MembershipType::getRow($Activitie->MemberShip);
        $Type = $membership_type->Type ?? '';
    }

    $itemName = Utils::safeText($Activitie->ItemName);
?>
            <option value="<?= $Activitie->id ?>" data-price="<?= $Activitie->ItemPrice; ?>"
                    data-name="<?= $itemName; ?>" data-department="<?= $Activitie->Department ?>" >
              <?= $Type === '' ? '' : $Type . '::'; ?>
              <?= $Activitie->ItemName; ?> - ₪
              <?= $Activitie->ItemPrice; ?>
            </option>
            <?php } ?>
          </select>
        </div>
        <div id="item-details-select-content" class="row">

        </div>
        <div class="form-group">
          <label><?php echo lang('membership_title') ?>
            <em>
            <?php echo lang('req_field') ?>
            </em>
          </label>
          <input type="text" class="form-control" name="ItemNamep" id="ItemNamep" value="" required style="background-position: left 5px center;">
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label><?php echo lang('membership_price') ?>
                <em>
                <?php echo lang('req_field') ?>
                </em>
              </label>
              <input type="text" class="form-control" onkeypress="validate(event)" name="ItemPricep" id="ItemPricep" value="" required style="background-position: left 5px center;">
            </div>
          </div>
          <div class="col-md-6">
            <div hidden class="form-group">
              <label><?php echo lang('inc_vat') ?>
              </label>
              <select name="Vat" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('choose') ?>" required>
                <option value="0" selected ><?php echo lang('yes') ?>
                </option>
                <option value="1" ><?php echo lang('no') ?>
                </option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label><?php echo lang('membership_start') ?>
            <em>
            <?php echo lang('req_field') ?>
            </em>
          </label>
          <input type="date" class="form-control focus-me" name="ClassDate" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group" >
          <label><?php echo lang('customer_card_validity_count') ?>
          </label>
          <select name="Vaild_LastCalss" id="Vaild_LastCalss" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('choose') ?>">
            <option value="0" selected ><?php echo lang('customer_card_date_buy') ?>
            </option>
            <option value="2" ><?php echo lang('cusomer_card_validity_2') ?>
            </option>
            <option value="3" ><?php echo lang('cusomer_card_validity_3') ?>
            </option>
            <option value="5" ><?php echo lang('cusomer_card_validity_4') ?>
            </option>
            <option value="4" ><?php echo lang('cusomer_card_validity_5') ?>
            </option>
          </select>
        </div>
        <div id="Vaild_LastCalss1" class="form-group" style="display: none;">
          <label><?php echo lang('membership_end_date') ?>
            <em>
            <?php echo lang('req_field') ?>
            </em>
          </label>
          <input type="date" class="form-control" name="ClassDateEnd" id="ClassDateEnd" value="">
        </div>
        <div id="Vaild_LastCalss0" class="alertb alert-warning" style="display: none;"><?php echo lang('pay_attention') ?>
        <?php echo lang('1_membership_notice') ?>
          <br>
          <?php echo lang('membership_notice_2') ?>
        </div>
        <?php
$Fees = DB::table('registration_fees')->where('CompanyNum','=',$CompanyNum)->where('Type','=','1')->first();
$Insurance = DB::table('registration_fees')->where('CompanyNum','=',$CompanyNum)->where('Type','=','2')->first();
if ( Auth::user()->id=='1'){
if (@$Fees->Status=='0'){
?>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="Fees" value="1" class="pull-right" id="Feescheckbox1"> <?php echo lang('add_booking_fee') ?>
            <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo lang('by_settings_store') ?>">
            </i>
          </label>
        </div>
        <?php }
if (@$Insurance->Status=='0'){
?>
        <div class="checkbox">
          <label>
            <input type="checkbox" name="Insurance" value="1" class="pull-right" id="Insurancecheckbox1"> <?php echo lang('add_insurance_fee') ?>
            <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo lang('by_settings_store') ?>">
            </i>
          </label>
        </div>
        <?php } } ?>
      </div>
      <div class="ip-modal-footer d-flex justify-content-between">
        <button type="button" class="btn btn-light ip-close" data-dismiss="modal">
        <?php echo lang('close') ?>
        </button>
          <div class="ip-actions">
              <button type="submit" name="submit" class="btn btn-success">
                  <?php echo lang('save_changes_button') ?>
              </button>
          </div>
      </div>
    </form>
  </div>
</div>
<!-- end Add Contact -->
<?php endif ?>
<div class="ip-modal" id="LogActivityPopup"  data-backdrop="static">
  <div class="ip-modal-dialog BigDialog">
    <div class="ip-modal-content text-start">
      <div class="ip-modal-header  d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('membership_history') ?>
        </h4>
        <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <div id="resultLogActivity">
        </div>
      </div>
      <div class="ip-modal-footer">
        <button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal">
        <?php echo lang('close') ?>
        </button>
      </div>
    </div>
  </div>
</div>
<div class="ip-modal" id="ChangeActivityPopup"  data-backdrop="static">
  <div class="ip-modal-dialog BigDialog">
    <form  action="ChangeActivityClass" class="ajax-form clearfix text-start popup-ajax ip-modal-content text-start" autocomplete="off" >
      <div class="ip-modal-header" >
        <h4 class="ip-modal-title"><?php echo lang('change_membership') ?>
        </h4>
        <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true"  >&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <div id="resultChangeActivity">
        </div>
      </div>
      <div class="ip-modal-footer d-flex justify-content-between" >
        <div class="ip-actions">
          <button type="submit" name="submit" class="btn btn-primary text-white"><?php echo lang('save_changes_button') ?>
          </button>
        </div>
        <button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal">
        <?php echo lang('close') ?>
        </button>
      </div>
    </form>
  </div>
</div>
<div class="ip-modal" id="LogActivityRegularPopup"  data-backdrop="static">
  <div class="ip-modal-dialog BigDialog">
    <div class="ip-modal-content text-start">
      <div class="ip-modal-header d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('a_manage_recurring_booking') ?>
        </h4>
        <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <div id="resultLogActivityRegular">
        </div>
      </div>
      <div class="ip-modal-footer">
        <button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal">
        <?php echo lang('close') ?>
        </button>
      </div>
    </div>
  </div>
</div>
<div class="ip-modal" id="OptionsActivityPopup"  data-backdrop="static">
  <div class="ip-modal-dialog BigDialog">
    <div class="ip-modal-content text-start">
      <div class="ip-modal-header d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('actions') ?>
        </h4>
        <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <div id="resultOptionsActivity">
        </div>
      </div>
    </div>
  </div>
</div>
<div class="ip-modal" id="FreezOutActivityPopup"  data-backdrop="static">
  <div class="ip-modal-dialog">
    <form   action="FreezOutActivity"  class="ajax-form clearfix ip-modal-content text-start" >
      <div class="ip-modal-header d-flex justify-content-between" >
        <h4 class="ip-modal-title"><?php echo lang('unfreeze_membership') ?>
        </h4>
        <a class="ip-close" title="Close"  data-dismiss="modal">&times;
        </a>
      </div>
      <div class="ip-modal-body" >
        <input type="hidden" name="ClientId">
        <input type="hidden" name="ActivityId">
        <div class="form-group" >
          <label><?php echo lang('q_unfreeze_subscription') ?>
          </label>
        </div>
      </div>
      <div class="ip-modal-footer d-flex justify-content-between">
        <button type="submit" name="submit" class="btn btn-primary ip-close"><?php echo lang('yes') ?>
        </button>
        <button type="button" class="btn btn-dark text-white ip-close ip-closePopUp" data-dismiss="modal"><?php echo lang('no') ?>
        </button>
      </div>
    </form>
  </div>
</div>
<input type="hidden" id="MakePaymentInput" value="">
<div class="ip-modal text-start"  role="dialog" id="PipLinePopUp" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="ip-modal-dialog BigDialog">
    <div class="ip-modal-content">
      <div class="ip-modal-header d-flex justify-content-between">
        <h4 class="ip-modal-title">
        </h4>
        <a class="ip-close" title="Close" data-dismiss="modal" aria-label="Close">&times;
        </a>
      </div>
      <div class="ip-modal-body">
        <div id="DivPipLinePopUp">
        </div>
      </div>
    </div>
  </div>
</div>
<!-- מודל שיעור אישי חדש -->
<div class="ip-modal text-start" role="dialog" id="UpdateCancelDocumentModalRefoundPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
  <div class="ip-modal-dialog BigDialog">
    <div class="ip-modal-content">
      <div class="ip-modal-header d-flex justify-content-between">
        <h4 class="ip-modal-title"><?php echo lang('cancel_doc') ?>
        </h4>
        <a class="ip-close ClassClosePopUp" title="Close"   data-dismiss="modal" aria-label="Close">&times;
        </a>
      </div>
      <div class="ip-modal-body">
        <form action="UpdateCancelDocumentModalRefound" id="UpdateCancelDocumentModalRefound" class="ajax-form needs-validation" novalidate autocomplete="off">
          <div id="resultCancelDocumentModalRefound">
            <center>
              <i class="fas fa-spinner fa-pulse fa-5x p-3">
              </i>
            </center>
          </div>
          <div class="alertb alert-danger" id="RPOSCancelDocsError" style="display: none;">
            <span id="RPOSCancelDocsErrorText">
            </span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- מודל שיעור אישי חדש -->
<div class="ip-modal text-start  align-items-center" role="dialog" id="offset-debt-reception-popup" data-backdrop="static" data-keyboard="false" aria-hidden="true" style="overflow-y: unset;">
    <div class="BigDialog" id="credit-popup" >
        <div class="modal-content credit-modal-content "  style="">
            <div class="ip-modal-header d-flex justify-content-between">
                <h4 class="ip-modal-title"><?= lang("credit_monetary")?>
                </h4>
                <a class="ClassClosePopUp fal fa-times h4" title="Close" data-dismiss="modal" aria-label="Close" style="cursor:pointer">
                </a>

            </div>
            <div class="ip-modal-body d-flex flex-column justify-content-center align-items-center mt-20">
                <div class="d-flex justify-content-center" >
<!--                    <i class="fal fa-coins fa-7x"></i>-->
                    <img src="/assets/img/coin-light.svg" class="horizontal-coin" alt="coin" ></img>
                    <img src="/assets/img/coin-light.svg" class="vertical-coin" alt="coin"></img>
                </div>
                <form id="offset-debt-reception-form" onsubmit="OffsetDebtReceptionPost(event)" class="ajax-form needs-validation mt-30" novalidate autocomplete="off">
                    <div class="w-100 ">
                        <label class="font-weight-bold bsapp-fs-14 m-0"><?=lang('offset_amount_smart_link')?></label>
                        <div class="is-invalid-container">
                            <div class="col p-0 m-0 ">
                                <div class="position-relative ">
                                    <input id="offset-amount" inputmode="decimal" type="number" onchange="setTwoNumberDecimal(this)"
                                           max=999999 min=1 name="offsetAmount" required
                                           onkeyup="maxLengthCheck(this)"
                                           class="form-control border-gray-200 bsapp-rounded-8p m-0 p-14" style="height: unset!important; font-size:22px;"
                                           placeholder="<?=lang('summary') ?>">
                                    <span class="position-absolute nis-icon" >₪</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input class="hidden" type="number" name="docsId" id="docs-id">
                    <input class="hidden" type="number" name="balanceAmount" id="balance-amount">
                    <div class="d-flex justify-content-center credit-popup-text-wrapper">
                        <p class="text-center m-0 bsapp-fs-13 bsapp-lh-15">
                            <?=lang('credit_popup_text1') ?>
                        <span class='ingredient' id='balance-amount-text'></span>
                            <?=lang('credit_popup_text2') ?>
                        </p>
                    </div>
                    <!-- modal content end -->
                    <div class="d-flex justify-content-between border-top border-light mt-40 pt-16">
                        <button type="button" class="ip-close ClassClosePopUp btn  toggleClosePopup font-weight-bold credit-cancel-btn" title="Close" data-dismiss="modal" aria-label="Close">
                                <?=lang('action_cacnel')?></button>
                        <button type="submit" class="btn btn-outline-secondary  submit-button font-weight-bold" ><?= lang('confirm') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


            <?php include_once __DIR__.'/partials-views/archive-popup/clientFailReasonPopup.php'; ?>
<!-- מודל שיעור אישי חדש -->
<script type="text/javascript" src="<?php echo asset_url('js/jquery.scannerdetection.js') ?>">
</script>
<script>
// =========================== Grant/Deny client app access ========================================================

    function UserAppAccess(element, action) {
        $(element).prop('disabled', true);
        $.ajax({
            url: 'ajax/clientProfileAjax.php',
            type: 'POST',
            data: {
                'fun': 'UserAppAccess',
                'clientId': <?php echo $ClientId ?>,
                'companyNum': <?php echo $CompanyNum ?>,
                'newValue': action
            },
            success: function () {
                $(element).prop('disabled', false);
                const alertHide = $(element).closest('#user-overview').find('div#OpenAppUsers');

                if (action === 0)
                    alertHide.slideUp();
                else
                    alertHide.slideDown();

                $.notify(
                    {icon: 'fas fa-check-circle', message: lang('action_done')},
                    {type: 'success'}
                );
            },
            error: function (res) {
                $(element).prop('disabled', false);
                $.notify(
                    {icon: 'fas fa-times-circle', message: lang('error_oops_something_went_wrong')},
                    {type: 'danger'}
                );
            },
        });
    }

//==============================================================================================

  $( ".ChangeLeadAgentp" ).select2( {
    theme:"bsapp-dropdown", placeholder: "<?php echo lang('select_sales') ?>", 'language':"<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_lang'] : 'he' ?>" }
                                  );
  $(document).scannerDetection({
    timeBeforeScanTest: 200, // wait for the next character for upto 200ms
    endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
    avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
    ignoreIfFocusOn: 'input', // turn off scanner detection if an input has focus
    preventDefault: false,
    onComplete: function(barcode){
      console.log(arguments);
      $("#RFIDC").val(barcode);
    }, // main callback function
    scanButtonKeyCode: 116, // the hardware scan button acts as key 116 (F5)
    scanButtonLongPressThreshold: 5, // assume a long press if 5 or more events come in sequence
    //	onScanButtonLongPressed: showKeyPad, // callback for long pressing the scan button
    onError: function(string){
      console.log(arguments);
    }
  });
</script>
<script>
  $("#ReceiptBtn").click( function()
                         {
    $('#MakePaymentInput').val('2').trigger("change");
  }
                        );
  $("#ReceiptRefoundBtn").click( function()
                                {
    $('#MakePaymentInput').val('2').trigger("change");
  }
                               );
  $("#CancelReceiptBtn").click( function()
                               {
    $('#MakePaymentInput').val('2').trigger("change");
  }
                              );
  $("#CancelReceiptRefoundBtn").click( function()
                                      {
    $('#MakePaymentInput').val('2').trigger("change");
  }
                                     );
  var MakePayments = '';
  $("#MakePaymentInput").on("change",function() {
    var MakePaymentInput = $(this).val();
    if (MakePaymentInput=='1'){
      MakePayments = true;
    }
    else {
      MakePayments = '';
    }
  }
                           );
  $('a').mousedown(function(e) {
    var MakePaymentInput = $('#MakePaymentInput').val();
    if(MakePayments) {
      // if the user navigates away from this page via an anchor link,
      //    popup a new boxy confirmation.
      alert("<?php echo lang('you_have_charged_save_or_cancel_required') ?>");
    }
  }
                  );
  window.onbeforeunload = function() {
    if((MakePayments)){
      // call this if the box wasn't shown.
      return '<?php echo lang('you_have_charged_save_or_cancel_required') ?>';
    }
  };
</script>
<?php if (Auth::userCan('70')): ?>
<script>
    function closePaymentIframe() {
        var $buttonIframe = $('.js-pay-new-card-iframe-button');
        var $iframe = $buttonIframe.siblings('.iframe-wrapper').find('iframe');

        var orderType = $buttonIframe.attr('data-order-type');

        $iframe.parent().addClass('d-none');

        if ($iframe.attr('src')) {
            $iframe.attr('src', null);
        }

        $.ajax({
            url: '/office/payment/Payment.php',
            data: {
                action: 'cleanTempPayment',
                ClientId: '<?= $Supplier->id ?>',
                orderType: orderType,
            }
        })
    }

  $("#Chash").click(function(){
    $("#Cahshdiv").show();
    $("#Checkdiv").hide();
    $("#Bankdiv").hide();
    $("#Creditdiv").hide();
    $('.js-payment-method .active').removeClass('active');
    $(this).addClass('active');
    let paymentsTable = $('#DocsPayments').find('table').find('tbody');
    if(paymentsTable.find('tr').length) {
      $('#ReceiptBtn').attr("disabled", false);
    } else {
      $('#ReceiptBtn').attr("disabled", true);
    }
    closePaymentIframe();
  }
                   );
  $("#Check").click(function(){
    $("#Checkdiv").show();
    $("#Cahshdiv").hide();
    $("#Bankdiv").hide();
    $("#Creditdiv").hide();
    $('.js-payment-method .active').removeClass('active');
    $(this).addClass('active');
    let paymentsTable = $('#DocsPayments').find('table').find('tbody');
    if(paymentsTable.find('tr').length) {
      $('#ReceiptBtn').attr("disabled", false);
    } else {
      $('#ReceiptBtn').attr("disabled", true);
    }
    closePaymentIframe();
  }
                   );
  $("#Bank").click(function(){
    $("#Bankdiv").show();
    $("#Checkdiv").hide();
    $("#Cahshdiv").hide();
    $("#Creditdiv").hide();
    $('.js-payment-method .active').removeClass('active');
    $(this).addClass('active');
    let paymentsTable = $('#DocsPayments').find('table').find('tbody');
    if(paymentsTable.find('tr').length) {
      $('#ReceiptBtn').attr("disabled", false);
    } else {
        $('#ReceiptBtn').attr("disabled", true);
    }
    closePaymentIframe();
  }
                  );
  $("#Credit").click(function(){
    $("#Creditdiv").show();
    $("#Bankdiv").hide();
    $("#Checkdiv").hide();
    $("#Cahshdiv").hide();
    $('.js-payment-method .active').removeClass('active');
    $('#CreditOptionToken').change();
    $(this).addClass('active');

    $('#ReceiptBtn').attr("disabled", true);

  }
                    );
  $("#ChashRefound").click(function(){
    $("#CahshRefounddiv").show();
    $("#CheckRefounddiv").hide();
    $("#BankRefounddiv").hide();
    $("#CreditRefounddiv").hide();
    $('.js-refund-method .active').removeClass('active');
    $(this).addClass('active');

    let paymentsTable = $('#DocsPaymentsRefound').find('table').find('tbody');
    if(paymentsTable.find('tr').length) {
      $('#ReceiptRefoundBtn').attr("disabled", false);
    } else {
      $('#ReceiptRefoundBtn').attr("disabled", true);
    }
    closePaymentIframe();

  }
                          );
  $("#CheckRefound").click(function(){
    $("#CheckRefounddiv").show();
    $("#CahshRefounddiv").hide();
    $("#BankRefounddiv").hide();
    $("#CreditRefounddiv").hide();
    $('.js-refund-method .active').removeClass('active');
    $(this).addClass('active');

    let paymentsTable = $('#DocsPaymentsRefound').find('table').find('tbody');
    if(paymentsTable.find('tr').length) {
      $('#ReceiptRefoundBtn').attr("disabled", false);
    } else {
      $('#ReceiptRefoundBtn').attr("disabled", true);
    }
    closePaymentIframe();
  }
                          );
  $("#BankRefound").click(function(){
    $("#BankRefounddiv").show();
    $("#CheckRefounddiv").hide();
    $("#CahshRefounddiv").hide();
    $("#CreditRefounddiv").hide();
    $('.js-refund-method .active').removeClass('active');
    $(this).addClass('active');

    let paymentsTable = $('#DocsPaymentsRefound').find('table').find('tbody');
    if(paymentsTable.find('tr').length) {
      $('#ReceiptRefoundBtn').attr("disabled", false);
    } else {
      $('#ReceiptRefoundBtn').attr("disabled", true);
    }
    closePaymentIframe();
  }
                         );
  $("#CreditRefound").click(function(){
    $("#CreditRefounddiv").show();
    $("#BankRefounddiv").hide();
    $("#CheckRefounddiv").hide();
    $("#CahshRefounddiv").hide();
    $('.js-refund-method .active').removeClass('active');
    $('#CreditOptionTokenRefound').change();
    $(this).addClass('active');

    $('#ReceiptRefoundBtn').attr("disabled", true);
  }
                           );
  var delay = (function(){
    var timer = 0;
    return function(callback, ms){
      clearTimeout (timer);
      timer = setTimeout(callback, ms);
    };
  }
              )();
  $("#CC2").on('change keydown paste input', function(){
    delay(function(){
      $('#CreditValueButton').attr("disabled", false);
    }
          , 600 );
  }
              );
  $("#CC2Refound").on('change keydown paste input', function(){
    delay(function(){
      $('#CreditValueButtonRefound').attr("disabled", false);
    }
          , 600 );
  }
                     );
  function showCredit(select_item) {
      console.log('selected', select_item);
      $('.credit-card-type').hide();
      $('.credit-card-type[data-type=' + select_item + ']').show();

      closePaymentIframe();
  }

  function showCreditRefound(select_item) {
      console.log(select_item);
      $('.refund-credit-type-card').hide();
      $('.refund-credit-type-card[data-type=' + select_item + ']').show();

      closePaymentIframe();
  }
  $(document).on("keypress", "form", function(event) {
    return event.keyCode != 13;
  }
                );
  //// תשלומים
  <?php
  $starting_tash  = 2;
  $ending_tash    = $SettingsInfo->MaxPayment;
  for($starting_tash; $starting_tash <= $ending_tash; $starting_tash++) {
    $tash[] = "<option value='".$starting_tash."'>".$starting_tash."</option>";
  }
  ?>
    <?php
  $starting_tashc  = 3;
  $ending_tashc    = 36;
  for($starting_tashc; $starting_tashc <= $ending_tashc; $starting_tashc++) {
    $tashc[] = "<option value='".$starting_tashc."'>".$starting_tashc."</option>";
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

  $(".tashTypeKeva").change(function () {
    var val = $(this).val();
    if (val == "0") {
      $(".TashKeva").html("<option value='1'>1</option>");
    }
    else if (val == "1") {
      $(".TashKeva").html("<?php echo implode("", @$tash);?>");
    }
    else if (val == "2") {
      $(".TashKeva").html("<option value='1'>1</option>");
    }
    else if (val == "3") {
      $(".TashKeva").html("<option value='1'>1</option>");
    }
    else if (val == "6") {
      $(".TashKeva").html("<?php echo implode("", @$tashc);  ?>");
    }
    $('.tashTypeKeva').val(val);
  }
                           );
  (function () {
    var totalEl = document.getElementById('total'),
        total = 0,
        checkboxes = document.AddDocs['invoicenum[]'],
        handleClick = function (e) {
            total += parseFloat(this.getAttribute('data-weight'), 10) * (this.checked ? 1 : -1);
            totalEl.innerHTML = parseFloat(total).toFixed(2);
            $('#Finalinvoicenum').val(parseFloat(total).toFixed(2));
            $('#TrueFinalinvoicenum').val(parseFloat(total).toFixed(2));
            $('input[name="CashValue"]').val(parseFloat(total).toFixed(2));
            $('input[name="CreditValue"]').val(parseFloat(total).toFixed(2));
            $('input[name="CheckValue"]').val(parseFloat(total).toFixed(2));
            $('input[name="BankValue"]').val(parseFloat(total).toFixed(2));

            if (total == '0' || total == '0.00') {
                $('#ShowPaymentDiv').hide();
            } else {
                $('#ShowPaymentDiv').show();
            }

            var values = [];
            $.each($("input[name='invoicenum[]']:checked"), function () {
                values.push($(this).val());
                // or you can do something to the actual checked checkboxes by working directly with  'this'
                // something like $(this).hide() (only something useful, probably) :P
            });
            $('#FinalinvoiceId').val(values);
        }
        ,
        i, l
    ;

      for (i = 0, l = checkboxes.length; i < l; ++i) {
        $(checkboxes[i]).click(handleClick);
      }

      const queryString = window.location.search;
      const urlParams = new URLSearchParams(queryString);

      let clientActivityIds = urlParams.get('client_activity');

      if (clientActivityIds) {
          clientActivityIds = clientActivityIds.split(',');

          for (i = 0; i < clientActivityIds.length; i++) {
              $('.CloseCheckBoxPayment[value=' + clientActivityIds[i] + ']').trigger('click');
          }
          $('#Credit').click();
      }
  }
   ());
  (function () {
      var totalEl = document.getElementById('totalrefound'),
          total = 0,
          checkboxes = document.AddDocsRefound['invoicenumrefound[]'],
          handleClick = function () {
              total += parseFloat(this.getAttribute('data-weight'), 10) * (this.checked ? 1 : -1);
              totalEl.innerHTML = parseFloat(total).toFixed(2);
              $('#FinalinvoicenumRefound').val(parseFloat(total).toFixed(2));
              $('#TrueFinalinvoicenumRefound').val(parseFloat(total).toFixed(2));

              $('input[name="CashValue"]').val(parseFloat(total).toFixed(2));
              $('input[name="CreditValue"]').val(parseFloat(total).toFixed(2));
              $('input[name="CheckValue"]').val(parseFloat(total).toFixed(2));
              $('input[name="BankValue"]').val(parseFloat(total).toFixed(2));

              if (total == 0 || total == '0.00') {
                  $('#ShowPaymentRefoundDiv').hide();
              } else {
                  $('#ShowPaymentRefoundDiv').show();
              }
              var values = new Array();
              $.each($("input[name='invoicenumrefound[]']:checked"), function () {
                  values.push($(this).val());
                  // or you can do something to the actual checked checkboxes by working directly with  'this'
                  // something like $(this).hide() (only something useful, probably) :P
              });
              $('#FinalinvoiceIdRefound').val(values);
              <?php if(in_array($TypeShva, [PaymentTypeEnum::TYPE_MESHULAM, PaymentTypeEnum::TYPE_TRANZILA])) { ?>
                  var data = {
                      'activities_ids': values,
                      'client_id': '<?php echo $Supplier->id ?>'
                  }
                  $(".js-meshulamCreditRefund tbody").load('action/getRefundDocs.php', data);
              <?php } ?>
              var a = [];
              var cboxes = $('input[name="invoicenumrefound[]"]:checked');
              var len = cboxes.length;
              for (var i = 0; i < len; i++) {
                  a[i] = cboxes[i].value;
              }
          }, i, l;
      for (i = 0, l = checkboxes.length; i < l; ++i) {
        checkboxes[i].onclick = handleClick;
      }
  }
   ());
  $(document).ready(function() {

    $('.js-meshulamRefundBtn').on('click', function(e) {
      e.preventDefault();
      $(this).append(' <i class="fad fa-spinner-third fast-spin">');
      $(this).attr('disabled', true);
      var refund_amount = $('#meshulamRefundAmount').val();
      var doc_checked = $('input[name="doc_id"]').is(':checked');
      var clientActivityId = $('input[name="doc_id"]:checked').parents('tr').attr('data-client-activity-id');
      var docId = $('input[name="doc_id"]:checked');

      if(!refund_amount || refund_amount <= 0) {
        $('#meshulam_refund_errMsg').show();
        $('.js-meshulamRefundBtn i').remove();
        $(this).attr('disabled', false);
        return;
      }

      if(!doc_checked) {
        $('#meshulam_docs_table_lbl').show();
        $('.js-meshulamRefundBtn i').remove();
        $(this).attr('disabled', false);
        return;
      }

        const data = {
            DocsId: docId.val(),
            refund_amount: refund_amount,
            TypeDoc: <?= $GroupNumber ?? 0 ?>,
            clientActivityId: clientActivityId
        };

        $.ajax({
        url: 'action/ClientRefund.php',
        type: 'POST',
        data: data,
        success: function(response) {
          var res = JSON.parse(response);
          if(res.error) {
            $('.js-meshulamRefundBtn i').remove();
            $('.js-meshulamRefundBtn').attr('disabled', false);
            $.notify(
              { icon: 'fas fa-times-circle', message: res.msg},
              { type: 'danger'}
            );
          } else {  //// success
            $.notify(
              { icon: 'fas fa-check-circle', message: res.msg},
              { type: 'success'}
            );
            location.reload();
          }
        },
        error: function(response) {
          $('.js-meshulamRefundBtn i').remove();
          $('.js-meshulamRefundBtn').attr('disabled', false);
        }
      })
      return false;
    });

    $('#meshulamRefundAmount').on('input', function() {
      var amount = $(this).val();
      if(amount) {
        $('#meshulam_refund_errMsg').hide();
      }
    });

    $('')

  });

  $('#CashValueButton').click(function(){
    var CashValue = encodeURI(document.getElementById('CashValue').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#Finalinvoicenum').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
    var TypeDoc = '<?php echo $GroupNumber; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CashValue!='' && TempId!='' && parseFloat(CashValue) > 0 && parseFloat(CashValue) <= parseFloat(Finalinvoicenum)) {
      $("#DocsPayments").load("DocPaymentInfoClient.php?TempId="+TempId+"&CashValue="+CashValue+"&Act=1&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&ClientId="+ClientId, function() {
          that.attr('disabled', false);
          $('#ReceiptBtn').attr("disabled", false);
      });
      BN('0', '<?php echo lang('received_payment_in_cash') ?>');

    }
    else {
        that.attr('disabled', false);
        BN('1','<?php echo lang('type_amount_even') ?>');
    }
  }
                             );
  $('#CashValueButtonRefound').click(function(){
    var CashValue = encodeURI(document.getElementById('CashValueRefound').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#FinalinvoicenumRefound').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenumRefound').val();
    var TypeDoc = '<?php echo $GroupNumberRefound; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CashValue!='' && TempId!='' && parseFloat(CashValue) > 0 && parseFloat(CashValue)<=parseFloat(Finalinvoicenum)) {
      $("#DocsPaymentsRefound").load("DocPaymentInfoRefoundClient.php?TempId="+TempId+"&CashValue="+CashValue+"&Act=1&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&ClientId="+ClientId, function() {
          that.attr('disabled', false);
          $('#ReceiptRefoundBtn').attr("disabled", false);
      });
      BN('0', '<?php echo lang('received_refund_in_cash') ?>');

    } else {
        that.attr('disabled', false);
        BN('1','<?php echo lang('type_amount_even') ?>');
    }
  }
                                    );
  $('#CheckValueButton').click(function(){
    var CheckValue = encodeURI(document.getElementById('CheckValue').value);
    var CheckDate = encodeURI(document.getElementById('CheckDate').value);
    var CheckSnif = encodeURI(document.getElementById('CheckSnif').value);
    var CheckAccount = encodeURI(document.getElementById('CheckAccount').value);
    var CheckBank = encodeURI(document.getElementById('CheckBank').value);
    var CheckNumber = encodeURI(document.getElementById('CheckNumber').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#Finalinvoicenum').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
    var TypeDoc = '<?php echo $GroupNumber; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CheckValue != '' && CheckDate != '' && CheckNumber != '' && TempId != '' && parseFloat(CheckValue) > 0 && parseFloat(CheckValue) <= parseFloat(Finalinvoicenum)) {
        $("#DocsPayments").load("DocPaymentInfoClient.php?TempId=" + TempId + "&CheckValue=" + CheckValue + "&CheckDate=" + CheckDate + "&CheckSnif=" + CheckSnif + "&CheckAccount=" + CheckAccount + "&CheckBank=" + CheckBank + "&CheckNumber=" + CheckNumber + "&Act=2&Finalinvoicenum=" + Finalinvoicenum + "&TypeDoc=" + TypeDoc + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum + "&ClientId=" + ClientId, function () {
            that.attr('disabled', false);
            $('#ReceiptBtn').attr("disabled", false);
        });
        BN('0', '<?php echo lang('received_payment_in_check') ?>');

    } else {
        that.attr('disabled', false);
        BN('1', '<?php echo lang('check_amount_notice') ?>');
    }
  }
                              );
  $('#CheckValueButtonRefound').click(function(){
    var CheckValue = encodeURI(document.getElementById('CheckValueRefound').value);
    var CheckDate = encodeURI(document.getElementById('CheckDateRefound').value);
    var CheckSnif = encodeURI(document.getElementById('CheckSnifRefound').value);
    var CheckAccount = encodeURI(document.getElementById('CheckAccountRefound').value);
    var CheckBank = encodeURI(document.getElementById('CheckBankRefound').value);
    var CheckNumber = encodeURI(document.getElementById('CheckNumberRefound').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#FinalinvoicenumRefound').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenumRefound').val();
    var TypeDoc = '<?php echo $GroupNumberRefound; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CheckValue!='' && CheckDate!='' && CheckNumber!='' && TempId!='' && parseFloat(CheckValue) > 0 && parseFloat(CheckValue)<=parseFloat(Finalinvoicenum)) {
      $("#DocsPaymentsRefound").load("DocPaymentInfoRefoundClient.php?TempId="+TempId+"&CheckValue="+CheckValue+"&CheckDate="+CheckDate+"&CheckSnif="+CheckSnif+"&CheckAccount="+CheckAccount+"&CheckBank="+CheckBank+"&CheckNumber="+CheckNumber+"&Act=2&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&ClientId="+ClientId, function() {
          that.attr('disabled', false);
          $('#ReceiptRefoundBtn').attr("disabled", false);
      });
      BN('0', '<?php echo lang('received_payment_in_check') ?>');
    }
    else {
        that.attr('disabled', false);
        BN('1', '<?php echo lang('check_amount_notice') ?>');
    }
  }
                                     );
  $('#CreditValueButton').click(function(){
    var CreditValue = encodeURI(document.getElementById('CreditValue').value);
    var CC2 = encodeURI(document.getElementById('CC2').value);
    var Tash1 = encodeURI(document.getElementById('Tash1').value);
    var tashType1 = encodeURI(document.getElementById('tashType1').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#Finalinvoicenum').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
    var TypeDoc = '<?php echo $GroupNumber; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CreditValue!='' && CC2!='' && Tash1!='' && tashType1!='' && TempId!='' && parseFloat(CreditValue) > 0 && parseFloat(CreditValue)<=parseFloat(Finalinvoicenum)) {
      $("#DocsPayments").load("DocPaymentInfoClient.php?TempId="+TempId+"&CreditValue="+CreditValue+"&Tash="+Tash1+"&tashType="+tashType1+"&CC2="+CC2+"&Act=3&Credit=1&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&ClientId="+ClientId, function() {
          that.attr('disabled', false);
      });
      BN('2', '<?php echo lang('charging_please_wait_processing_data') ?>');
    }
    else {
        that.attr('disabled', false);
        BN('1', '<?php echo lang('type_refund_notice') ?>');
    }
  }
                               );
  $('#CreditValueButtonRefound').click(function(){
    var CreditValue = encodeURI(document.getElementById('CreditValueRefound').value);
    var CC2 = encodeURI(document.getElementById('CC2Refound').value);
    var Tash1 = encodeURI(document.getElementById('Tash1Refound').value);
    var tashType1 = encodeURI(document.getElementById('tashType1Refound').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#FinalinvoicenumRefound').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenumRefound').val();
    var TypeDoc = '<?php echo $GroupNumberRefound; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CreditValue!='' && CC2!='' && Tash1!='' && tashType1!='' && TempId!='' && parseFloat(CreditValue) > 0 && parseFloat(CreditValue)<=parseFloat(Finalinvoicenum)) {
      $("#DocsPaymentsRefound").load("DocPaymentInfoRefoundClient.php?TempId="+TempId+"&CreditValue="+CreditValue+"&Tash="+Tash1+"&tashType="+tashType1+"&CC2="+CC2+"&Act=3&Credit=1&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&ClientId="+ClientId, function() {
          that.attr('disabled', false);
      });
      BN('2', '<?php echo lang('refund_processing_notice') ?>');
    }
    else {
        that.attr('disabled', false);
        BN('1', '<?php echo lang('type_refund_notice') ?>');
    }
  }
                                      );
  $('#Credit2ValueButton').click(function(){
    var CreditValue2 = encodeURI(document.getElementById('CreditValue2').value);
    var CC3 = encodeURI($( "#CC3 option:selected" ).val());
    var Tash2 = encodeURI(document.getElementById('Tash2').value);
    var tashType2 = encodeURI(document.getElementById('tashType2').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#Finalinvoicenum').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
    var TypeDoc = '<?php echo $GroupNumber; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CreditValue2!='' && CC3!='' && Tash2!='' && tashType2!='' && TempId!='' && parseFloat(CreditValue2) > 0 && parseFloat(CreditValue2)<=parseFloat(Finalinvoicenum)) {
        $("#DocsPayments").load("DocPaymentInfoClient.php?TempId="+TempId+"&CreditValue="+CreditValue2+"&Tash="+Tash2+"&tashType="+tashType2+"&CC3="+CC3+"&Act=3&Credit=2&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&ClientId="+ClientId, function() {
            that.attr('disabled', false);
        });
        BN('2', '<?php echo lang('charging_please_wait_processing_data') ?>');
    }
    else {
        that.attr('disabled', false);
        BN('1', '<?php echo lang('sum_reg_cc_notice') ?>');
    }
  }
                                );
  $('#Credit2ValueButtonRefound').click(function () {
      var CreditValue2 = encodeURI(document.getElementById('CreditValue2Refound').value);
      var CC3 = encodeURI($("#CC3Refound option:selected").val());
      var Tash2 = encodeURI(document.getElementById('Tash2Refound').value);
      var tashType2 = encodeURI(document.getElementById('tashType2Refound').value);
      var TempId = '<?php echo $Supplier->id; ?>';
      var Finalinvoicenum = $('#FinalinvoicenumRefound').val();
      var TrueFinalinvoicenum = $('#TrueFinalinvoicenumRefound').val();
      var TypeDoc = '<?php echo $GroupNumberRefound; ?>';
      var ClientId = '<?php echo $Supplier->id; ?>';
      $(this).attr('disabled', true);
      let that = $(this);

      if (CreditValue2 != '' && CC3 != '' && Tash2 != '' && tashType2 != '' && TempId != '' && parseFloat(CreditValue2) > 0 && parseFloat(CreditValue2) <= parseFloat(Finalinvoicenum)) {
          $("#DocsPaymentsRefound").load("DocPaymentInfoRefoundClient.php?TempId=" + TempId + "&CreditValue=" + CreditValue2 + "&Tash=" + Tash2 + "&tashType=" + tashType2 + "&CC3=" + CC3 + "&Act=3&Credit=2&Finalinvoicenum=" + Finalinvoicenum + "&TypeDoc=" + TypeDoc + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum+"&ClientId="+ClientId, function() {
              that.attr('disabled', false);
          });
          BN('2', '<?php echo lang('refund_processing_notice') ?>');
      } else {
          that.attr('disabled', false);
          BN('1', '<?php echo lang('refund_reg_cc_notice') ?>');
      }
  });

  $("#CreateNewPayments").click(function () {
          var Tash = encodeURI(document.getElementById('Tash3').value);
          var tashType = encodeURI(document.getElementById('tashType3').value);
          var CreditValue = encodeURI(document.getElementById('CreditValue3').value);
          var TempId = '<?php echo $Supplier->id; ?>';
          var Finalinvoicenum = $('#Finalinvoicenum').val();
          var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
          var TypeDoc = '<?php echo $GroupNumber; ?>';
          $(this).attr('disabled', true);
          let that = $(this);

          if (CreditValue != '' && Tash != '' && tashType != '' && TempId != '' && parseFloat(CreditValue) > 0 && parseFloat(CreditValue) <= parseFloat(Finalinvoicenum)) {
              $.ajax({
                      type: "POST",
                      url: "rest/Meshulam/Payments.php?Tash=" + Tash + "&tashType=" + tashType + "&CreditValue=" + CreditValue + "&ClientId=" + TempId + "&Finalinvoicenum=" + Finalinvoicenum + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum + "&TypeDoc=" + TypeDoc,
                      success: function (dataN) {
                          that.attr('disabled', false);
                          $('#DivPayments').show();
                          $('#DivPayments').attr('src', dataN);
                      },
                      error: function(err) {
                          that.attr('disabled', false);
                      }
                  }
              );
              BN('2', '<?php echo lang('connection_to_meshulad_wait') ?>');
          } else {
              that.attr('disabled', false);
              BN('1', '<?php echo lang('charge_required_all_or_type_sum_membership') ?>');
          }
      }
  );
  $('#Credit3ValueButtonRefound').click(function(e) {
    e.preventDefault();

    var CreditValue3 = encodeURI(document.getElementById('CreditValue3Refound').value);
    var Tash3 = encodeURI(document.getElementById('Tash3Refound').value);
    var tashType3 = encodeURI(document.getElementById('tashType3Refound').value);

    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#FinalinvoicenumRefound').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenumRefound').val();
    var TypeDoc = '<?php echo $GroupNumberRefound; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';

    if (CreditValue3!='' && Tash3!='' && tashType3!='' && TempId!='' && parseFloat(CreditValue3) > 0 && parseFloat(CreditValue3) <= parseFloat(Finalinvoicenum)) {
        $button = $(this);
        openPaymentIframe($button, 0, 1, 'addNewCard', function () {
            var checkPaymentStatus = setInterval(function() {
                if (window.paymentStatus !== 'waiting') {
                    $button.siblings('.iframe-wrapper').hide();

                    if (window.paymentStatus === 'error') {
                        Swal.fire({
                            title: "",
                            text: lang('processing_error_meshulam'),
                            icon: "error"
                        });

                        clearInterval(checkPaymentStatus);
                        return;
                    }

                    if (window.paymentStatus == 'success' && window.paymentType === '<?= OrderLogin::TYPE_REFUND_NEW_CARD ?>') {
                        clearInterval(checkPaymentStatus);

                        var TokenId = window.paymentTokenId;
                        console.log('TokenId', TokenId);
                        console.log('window', window);

                        BN('2', '<?php echo lang('refund_processing_notice') ?>');

                        $("#DocsPaymentsRefound").load("DocPaymentInfoRefoundClient.php?TempId="+TempId+"&CreditValue="+CreditValue3+"&Tash="+Tash3+"&tashType="+tashType3+"&Act=3&Credit=3&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&ClientId="+ClientId+'&TokenId='+TokenId);
                    }
                }
            }, 1500);
        });
    } else {
      BN('1', '<?php echo lang('refund_notice_type_manual') ?>');
    }

    return false;
  });

  $('#Credit4ValueButton').click(function(){
        var CreditValue = encodeURI(document.getElementById('CreditValue4').value);
        var CC = encodeURI(document.getElementById('CC4').value);
        var CDate = encodeURI(document.getElementById('CDate4').value);
        var CCode = encodeURI(document.getElementById('CCode4').value);
        var Tash = encodeURI(document.getElementById('Tash4').value);
        var tashType = encodeURI(document.getElementById('tashType4').value);
        var TypeBank = encodeURI(document.getElementById('TypeBank4').value);
        var TypeBrand = encodeURI(document.getElementById('TypeBrand4').value);
        var TempId = '<?php echo $Supplier->id; ?>';
        var Finalinvoicenum = $('#Finalinvoicenum').val();
        var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
        var TypeDoc = '<?php echo $GroupNumber; ?>';
        var ClientId = '<?php echo $Supplier->id; ?>';
        $(this).attr('disabled', true);
        let that = $(this);

        if (CreditValue!='' && CC!='' && Tash!='' && tashType!='' && TypeBank!='' && TypeBrand!='' && TempId!='' && CDate!='' && CCode!='' && parseFloat(CreditValue) > 0 && parseFloat(CreditValue)<=parseFloat(Finalinvoicenum)) {
            $("#DocsPayments").load("DocPaymentInfoClient.php?TempId="+TempId+"&CreditValue="+CreditValue+"&Tash="+Tash+"&tashType="+tashType+"&CC="+CC+"&TypeBank="+TypeBank+"&TypeBrand="+TypeBrand+"&CCode="+CCode+"&Act=3&Credit=4&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&CDate="+CDate+"&CCode="+CCode+"&ClientId="+ClientId, function() {
                that.attr('disabled', false);
                $('#ReceiptBtn').attr("disabled", false);
            });
            BN('2', '<?php echo lang('saving_wait_while_processing') ?>');

        }
        else {
            that.attr('disabled', false);
            BN('1', '<?php echo lang('save_transaction_other_terminal') ?>');
        }
  }
                                );
  $('#Credit4ValueButtonRefound').click(function(){
    var CreditValue = encodeURI(document.getElementById('CreditValue4Refound').value);
    var CC = encodeURI(document.getElementById('CC4Refound').value);
    var CDate = encodeURI(document.getElementById('CDate4Refound').value);
    var CCode = encodeURI(document.getElementById('CCode4Refound').value);
    var Tash = encodeURI(document.getElementById('Tash4Refound').value);
    var tashType = encodeURI(document.getElementById('tashType4Refound').value);
    var TypeBank = encodeURI(document.getElementById('TypeBank4Refound').value);
    var TypeBrand = encodeURI(document.getElementById('TypeBrand4Refound').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#FinalinvoicenumRefound').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenumRefound').val();
    var TypeDoc = '<?php echo $GroupNumberRefound; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CreditValue!='' && CC!='' && Tash!='' && tashType!='' && TypeBank!='' && TypeBrand!='' && TempId!='' && CDate!='' && CCode!='' && parseFloat(CreditValue) > 0 && parseFloat(CreditValue)<=parseFloat(Finalinvoicenum)) {
    $("#DocsPaymentsRefound").load("DocPaymentInfoRefoundClient.php?TempId="+TempId+"&CreditValue="+CreditValue+"&Tash="+Tash+"&tashType="+tashType+"&CC="+CC+"&TypeBank="+TypeBank+"&TypeBrand="+TypeBrand+"&CCode="+CCode+"&Act=3&Credit=4&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&CDate="+CDate+"&CCode="+CCode+"&ClientId="+ClientId, function() {
      that.attr('disabled', false);
      $('#ReceiptRefoundBtn').attr("disabled", false);
    });
    BN('2', '<?php echo lang('saving_wait_while_processing') ?>');

    }
    else {
    that.attr('disabled', false);
    BN('1', '<?php echo lang('save_transaction_other_terminal') ?>');
    }
  }
                                       );
  $('#BankValueButton').click(function() {
    var BankValue = encodeURI(document.getElementById('BankValue').value);
    var BankDate = encodeURI(document.getElementById('BankDate').value);
    var BankNumber = encodeURI(document.getElementById('BankNumber').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#Finalinvoicenum').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
    var TypeDoc = '<?php echo $GroupNumber; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CheckValue != '' && BankDate != '' && BankNumber != '' && TempId != '' && parseFloat(BankValue) > 0 && parseFloat(BankValue) <= parseFloat(Finalinvoicenum)) {
        $("#DocsPayments").load("DocPaymentInfoClient.php?TempId=" + TempId + "&BankValue=" + BankValue + "&BankDate=" + BankDate + "&BankNumber=" + BankNumber + "&Act=4&Finalinvoicenum=" + Finalinvoicenum + "&TypeDoc=" + TypeDoc + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum + "&ClientId=" + ClientId, function () {
            that.attr('disabled', false);
            $('#ReceiptBtn').attr("disabled", false);
        });
        BN('0', '<?php echo lang('bank_transfer_detail_received') ?>');

    } else {
        that.attr('disabled', false);
        BN('1', '<?php echo lang('transfer_amount_notice') ?>');
    }
  }
                             );
  $('#BankValueButtonRefound').click(function(){
    var BankValue = encodeURI(document.getElementById('BankValueRefound').value);
    var BankDate = encodeURI(document.getElementById('BankDateRefound').value);
    var BankNumber = encodeURI(document.getElementById('BankNumberRefound').value);
    var TempId = '<?php echo $Supplier->id; ?>';
    var Finalinvoicenum = $('#FinalinvoicenumRefound').val();
    var TrueFinalinvoicenum = $('#TrueFinalinvoicenumRefound').val();
    var TypeDoc = '<?php echo $GroupNumberRefound; ?>';
    var ClientId = '<?php echo $Supplier->id; ?>';
    $(this).attr('disabled', true);
    let that = $(this);

    if (CheckValue!='' && BankDate!='' && BankNumber!='' && TempId!='' && parseFloat(BankValue) > 0 && parseFloat(BankValue)<=parseFloat(Finalinvoicenum)) {
      $("#DocsPaymentsRefound").load("DocPaymentInfoRefoundClient.php?TempId="+TempId+"&BankValue="+BankValue+"&BankDate="+BankDate+"&BankNumber="+BankNumber+"&Act=4&Finalinvoicenum="+Finalinvoicenum+"&TypeDoc="+TypeDoc+"&TrueFinalinvoicenum="+TrueFinalinvoicenum+"&ClientId="+ClientId, function() {
          that.attr('disabled', false);
          $('#ReceiptRefoundBtn').attr("disabled", false);
      });
      BN('0', '<?php echo lang('bank_transfer_detail_received') ?>');

    }
    else {
        that.attr('disabled', false);
        BN('1', '<?php echo lang('transfer_amount_notice') ?>');
    }
  }
                                    );
  $(function() {
    var time = function(){
      return'?'+new Date().getTime()};
    $('#CancelDoc').imgPicker({
    }
                             );
    $('#CancelPaymentsPopup').imgPicker({
    }
                                       );
    $('#CancelDocRefound').imgPicker({
    }
                                    );
    $('#CancelPaymentsRefoundPopup').imgPicker({
    }
                                              );
    $('#AddClientContactPopup').imgPicker({
    }
                                         );
  }
   );
</script>
<?php endif ?>
<script>
  $(function() {
    $('.select2ClientDesk').select2({
      theme:"bootstrap",
      placeholder: "<?php echo lang('search_client') ?>",
      language: "<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_lang'] : 'he' ?>",
      allowClear: true,
      width: '100%',
      ajax: {
        url: 'SearchClient.php',
        type: 'POST',
        dataType: 'json',
        cache: true
      },
      minimumInputLength: 3
    }).on('select2:unselecting', function (e) {
        $(this).select2('val', '');
        e.preventDefault();
    });

    $(".CompanyName").keyup(function() {
      var ret = $('.CompanyName').val().split(" ");
      var firstName = ret[0];
      var lastName =  $('.CompanyName').val().split(' ').slice(1).join(' ');
      $('.FirstName').val(firstName);
      $('.LastName').val(lastName);
    });
  });
  <?php if (!empty(@$MedicalLogList)) {
            ?>
            new List('MedicalLogList', {
            valueNames: ['timeline-panel'],
            page: 10,
            pagination: true
            }
           );
  <?php }
    ?>
      <?php if (!empty(@$SmsLogList)) {
                ?>
                new List('SmsLogList', {
                valueNames: ['timeline-panel'],
                page: 10,
                pagination: true
                }
               );
  <?php }
    ?>
      <?php if (!empty(@$NotesList)) {
                ?>
                new List('NotesList', {
                valueNames: ['timeline-panel'],
                page: 10,
                pagination: true
                }
               );
  <?php }
    ?>
      <?php if (!empty(@$TasksLogs)) {
                ?>
                new List('TasksLog', {
                valueNames: ['timeline-panel'],
                page: 10,
                pagination: true
                }
               );
  <?php }
    ?>
      $(document).ready(function() {
      $('.summernote').summernote({
        placeholder: '<?php echo lang('type_message_content') ?>',
        tabsize: 2,
        height: 153,
        toolbar: [
          // [groupName, [list of button]]
          ['style', ['bold', 'italic', 'underline', 'clear']],
          ['font', ['strikethrough']],
          ['para', ['ul', 'ol']]
        ]
      }
                                 );
    }
                       );
  $("#Items1").change(function() {
    var ItemName = $(this).find(":selected").data("name");
    var ItemPrice = $(this).find(":selected").data("price");
    var ItemDepartment = $(this).find(":selected").data("department");
    $('#ItemNamep').val(ItemName);
    $('#ItemPricep').val(ItemPrice);

    if (ItemDepartment == 4){
        $.ajax({
            method: 'GET',
            url: '/office/partials-views/client-profile/item-details-select.php',
            data: {
                itemId: $(this).find(":selected").val()
            },
            success: function(res){
                $('#item-details-select-content').html(res);
            }
        });
    }
    else
        $('#item-details-select-content').html('');

  }
                     );
  $("#Vaild_LastCalss").change(function() {
    var Id = this.value;
    if (Id=='0'){
      Vaild_LastCalss0.style.display = "block";
      Vaild_LastCalss1.style.display = "none";
      $("#ClassDateEnd").prop('required',false);
    }
    else if (Id=='4'){
      Vaild_LastCalss1.style.display = "block";
      Vaild_LastCalss0.style.display = "block";
      $("#ClassDateEnd").prop('required',true);
    }
    else {
      Vaild_LastCalss0.style.display = "none";
      Vaild_LastCalss1.style.display = "none";
      $("#ClassDateEnd").prop('required',false);
    }
  }
                              );
  $('#TypeOption_0').trigger('click');
  $('[data-toggle="tabajax"]').click(function(e) {
    var $this = $(this),
        loadurl = $this.attr('href'),
        targ = $this.attr('data-target');
    $.get(loadurl, function(data) {
      $(targ).html(data);
    }
         );
    $this.tab('show');
    return false;
  }
                                    );
  function showToken(select_item) {
    if (select_item == "2") {
      CreditToken1.style.display='none';
      CreditToken2.style.display='block';
    }
    else{
      CreditToken1.style.display='block';
      CreditToken2.style.display='none';
    }
  }
  $('#SetDate').on('change', function() {
    scheduler.clearAll();
    scheduler.setCurrentView(this.value);
    scheduler.load("new/data/events.php");
    /// שנה גלילה לפי שעה
    var StratDate = new Date(scheduler.getState().date);
    var EndDate = new Date(scheduler.getState().date);
    var SetTime = $('#SetTime').val();
    var SetToTime = $('#SetToTime').val();
    var SetTime_H = SetTime.split(':');
    var SetToTime_H = SetToTime.split(':');
    StratDate.setHours(SetTime_H[0]);
    StratDate.setMinutes(SetTime_H[1]);
    EndDate.setHours(SetToTime_H[0]);
    EndDate.setMinutes(SetToTime_H[1]);
    scheduler.showEvent({
      start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"}
    );
  });
  $('#SetTime').on('change', function() {
    var SetDate = $('#SetDate').val();
    scheduler.clearAll();
    scheduler.setCurrentView(SetDate);
    scheduler.load("new/data/events.php");
    /// שנה גלילה לפי שעה
    var StratDate = new Date(scheduler.getState().date);
    var EndDate = new Date(scheduler.getState().date);
    var SetTime = $('#SetTime').val();
    var SetToTime = $('#SetToTime').val();
    var SetTime_H = SetTime.split(':');
    var SetToTime_H = SetToTime.split(':');
    StratDate.setHours(SetTime_H[0]);
    StratDate.setMinutes(SetTime_H[1]);
    EndDate.setHours(SetToTime_H[0]);
    EndDate.setMinutes(SetToTime_H[1]);
    scheduler.showEvent({
      start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"}
    );
  });
  $('#SetToTime').on('change', function() {
    var SetDate = $('#SetDate').val();
    scheduler.clearAll();
    scheduler.setCurrentView(SetDate);
    scheduler.load("new/data/events.php");
    /// שנה גלילה לפי שעה
    var StratDate = new Date(scheduler.getState().date);
    var EndDate = new Date(scheduler.getState().date);
    var SetTime = $('#SetTime').val();
    var SetToTime = $('#SetToTime').val();
    var SetTime_H = SetTime.split(':');
    var SetToTime_H = SetToTime.split(':');
    StratDate.setHours(SetTime_H[0]);
    StratDate.setMinutes(SetTime_H[1]);
    EndDate.setHours(SetToTime_H[0]);
    EndDate.setMinutes(SetToTime_H[1]);
    scheduler.showEvent({
      start_date:StratDate, end_date:EndDate, text:"<?php echo lang('new_activity') ?>"}
    );
  });
  $("#CC2Token").keypress(function(event){
    if (event.which == '10' || event.which == '13') {
      event.preventDefault();
    }
  });
  $(document).ready(function(){
      $("#js-new-task").on("click", function () {
          if ($('#js-task-popup').length > 0) {
              handleNewTask(null, <?= $ClientId ?>);
          } else {
              NewCal('','<?= $ClientId ?>','');
          }
      });

      $('.edit-task-btn').on('click', (e)=>{
          const id=e.target.getAttribute('data-id');
          const clientId=e.target.getAttribute('data-client-id');

          if ($('#js-task-popup').length > 0) {
              handleNewTask(id);
          } else {
              NewCal(id,clientId);
          }
      });

    $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
      $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
    });
    var categoriesDataTable;
    BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
    categoriesDataTable = $("table.display").dataTable({
      language: BeePOS.options.datatables,
      responsive: true,
      processing: false,
      "scrollCollapse": true,
      "paging": true,
      fixedHeader: {
        headerOffset: 50
      },
      pageLength: 100,
      dom: "lBfrtip",
      <?php if (Auth::userCan('98')) { ?>
      buttons: [
        {
          extend: 'excelHtml5',
          text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
          filename: '<?php echo lang('customer_report') ?>',
          className: 'btn btn-dark'
        },
        {
          extend: 'csvHtml5',
          text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
          filename: '<?php echo lang('customer_report') ?>',
          className: 'btn btn-dark'
        }
      ]
      <?php } ?>
    }
  );

    // $('#remove_minor').on('hidden.bs.modal', function () {
    //   $(this).find('#archive_status').attr('checked', false);
    //   $(this).find('#active_status').attr('checked', true);
    //   $(this).find('#minor-contactMobile').prop('required', true);
    //   $(this).find("input,textarea,select").val('').end();

    // });
    $('input[name="archive_minor"]').on('change', function() {
      if($(this).val() == 1) {
        //// archive
        $('#minor-contactMobile').prop('required',false);
        $('.mobile-lbl em').remove();
      } else {
        //// active
        $('#minor-contactMobile').prop('required', true);
        $('.mobile-lbl').append(' <em class="text-danger font-rubik">*</em>');
      }
    });

    $('body').on('click', '.js-open-minor', function() {
      var minor_id = $(this).attr('data-id');
      var mobile = $(this).attr('data-minor-mobile');
      var email = $(this).attr('data-minor-email');
      var name = $(this).attr('data-name');
      $('#minor-client-id').val(minor_id);
      $('#minor-contactMobile').val(mobile);
      $('#minor-email').val(email);
      $('#minor-name').text(name);
    });

    $(".js-select2-minor").on("select2:select", function(e) {
      var data = e.params.data;
      var parent_id = $("#parent_client_id").val();
      if(data.id == parent_id) {
        $(".js-err-msg").text('<?php echo lang('cant_attached_to_itself') ?>');
        $(".js-err-msg").show();
        $(".js-err-msg").fadeOut(4000);
        $(".js-select2-minor").val(null).trigger("change");
      }
    });

    $('#add-minor-form').on('submit', function(e) {
      e.preventDefault();
      $('.js-add-minor').addClass('disabled');
      $('.js-add-minor').append(' <i class="fad fa-spinner-third fs-20 fast-spin">');
      var data;
      if($("#exist_minor").is(':checked')) {
        var exist_minor_id = $(".js-select2-minor").val();
        var relationship = $("#exist_minor_relationship").val();
        data = {
          exist_minor_id: exist_minor_id,
          relationship: relationship,
          parent_client_id: $("#parent_client_id").val(),
          exist: true
        }
      } else {
        data = $(this).serialize();
      }
      $.ajax({
        url: 'action/addMinorClient.php',
        type: 'POST',
        data: data,
        success: function(response) {
          var res = JSON.parse(response);
          $('.js-add-minor').removeClass('disabled');
          $('.js-add-minor i').remove();
          if(res.success == true && res.minor_id != '') {
            $.notify(
              { icon: 'fas fa-check-circle', message: '<?php echo lang('action_done') ?>'},
              { type: 'success'}
            );

            $('.family-card').append('<div id="minor-'+ res.minor_id +'" class="d-flex justify-content-between align-items-center font-rubik-sans pb-15"><div><div><a class="text-muted" href="/office/ClientProfile.php?u='+ res.minor_id +'"><span class="text-dark">'+res.companyName+'</span> <span class="font-weight-bold">(קטין)</span></a></div></div><div><div><a href="/office/ClientProfile.php?u='+ res.minor_id +'#user-settings" title="ערוך פרטי קטין" class="text-gray mie-7"><i class="fad fa-edit fa-md"></i></a> <a data-toggle="modal" data-id="'+ res.minor_id +'" data-minor-mobile="" data-name="'+ res.companyName +'" href="#remove_minor" class="text-danger js-open-minor" title="נתק קטין"><i class="fal fa-user-times fa-md"></i></a></div></div></div>');
            $("#add_minor").modal('hide');

          } else {
            if(res.msg) {
              $(".js-exist-msg").text(res.msg);
              $(".js-exist-msg").show();
              $(".js-exist-msg").fadeOut(4000);
            }
            $(".js-select2-minor").val(null).trigger("change");
            $.notify(
              { icon: 'fas fa-times-circle', message: '<?php echo lang('error_oops_something_went_wrong') ?>'},
              { type: 'danger'}
            );
          }
        },
        error: function() {
          $('.js-add-minor').removeClass('disabled');
          $('.js-add-minor i').remove();
          $.notify(
            { icon: 'fas fa-times-circle', message: '<?php echo lang('error_oops_something_went_wrong') ?>'},
            { type: 'danger'}
          );
        }
      });
    });

    $('#remove-minor-form').on('submit', function(e) {
      e.preventDefault();
      $('.js-minor-action').addClass('disabled');
      $('.js-minor-action').append(' <i class="fad fa-spinner-third fs-20 fast-spin">');

      var data = $(this).serialize();
      var minor_id = $('#minor-client-id').val();
      if(minor_id == "" || !minor_id) {
        $('.js-minor-action').removeClass('disabled');
        $('.js-minor-action i').remove();
        return;
      }
      $.ajax({
        url: 'action/removeMinor.php',
        type: 'POST',
        data: data,
        success: function(response) {
          var res = JSON.parse(response);
          $('.js-minor-action').removeClass('disabled');
          $('.js-minor-action i').remove();
          if(res.success == true) {
            $('#minor-'+ minor_id).remove();
            $.notify(
              { icon: 'fas fa-check-circle', message: '<?php echo lang('action_done') ?>'},
              { type: 'success'}
            );
            $("#remove_minor").modal('hide');
          } else {
            $('#err-msg').text(res.msg);
            $('#err-msg').show();
          }

        },
        error: function(err) {
          $('#err-msg').text(res.msg);
          $('#err-msg').show();
          $('.js-minor-action').removeClass('disabled');
          $('.js-minor-action i').remove();
        }
      });

    });

    $('input[name="choose_client"]').on('change', function() {
      if($("#exist_minor").is(':checked')) {
        $('#client_search').show();
        $(".js-new-minor").hide();
        $("#minor_firstName").prop('required', false);
        $("#minor_lastName").prop('required', false);
        $(".js-select2-minor").prop('required', true);
      } else {
        $('#client_search').hide();
        $(".js-new-minor").show();
        $("#minor_firstName").prop('required', true);
        $("#minor_lastName").prop('required', true);
        $(".js-select2-minor").prop('required', false);
      }
    });

    $( ".js-select2-minor" ).select2({
      theme: "bsapp-dropdown",
      placeholder: "<?php echo lang('search_client') ?>",
      language: "<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_lang'] : 'he' ?>",
      allowClear: true,
      ajax: {
        url: 'SearchClient.php',
        type: 'POST',
        dataType: 'json',
        cache: true
      },
      minimumInputLength: 3
    });



  });
  $("#CallClient").click(function(){
    $(".CallClientdivb").show();
    $.ajax({
      type: "POST",
      url: "POS3/CallClient.php?u=<?php echo $ClientId; ?>",
      // data: $("#SendSmss").serialize(),
      success: function(dataN)
      {
        $(".CallClientdivb").hide();
        $(".CallClientdiv").show();
        setTimeout(function() {
          $(".CallClientdiv").hide( "bounce", {
            times: 3 }
                                   , "slow" )}
                   , 5000);
      }
    }
          );
  }
                        );
  $(".ip-closePopUp").click(function(){
    $( "#resultOptionsActivity" ).empty();
    $( "#resultLogActivity" ).empty();
  }
                           );
  <?php if (!empty($PipeNow)) {
    ?>
      function TakeLead(LeadId) {
      var TakeLeadPleaseWait = $.notify(
        {
          icon: 'fas fa-spinner fa-spin',
          message: '<?php echo lang('pulling_lead') ?>',
        }
        ,{
          type: 'warning',
        }
      );
      $.ajax({
        type: "POST",
        url: "action/TakeLead.php?LeadId="+LeadId,
        success: function(dataN)
        {
          $('#TakeLeadTD').html('<?php echo addslashes(Auth::user()->display_name); ?>');
          TakeLeadPleaseWait.close();
          $.notify(
            {
              icon: 'fas fa-check-circle',
              message: '<?php echo lang('lead_pulled') ?>',
            }
            ,{
              type: 'success',
            }
          );
        }
        ,
        error: function(xhr, status, error) {
          TakeLeadPleaseWait.close();
          $.notify(
            {
              icon: 'fas fa-times-circle',
              message: '<?php echo lang('error_oops_something_went_wrong') ?>',
            }
            ,{
              type: 'danger',
            }
          );
        }
      }
            );
    }
      <?php }
  ?>
    <?php if (!empty($PipeNow) && Auth::userCan('141')) {
      ?>
        $('.ChangeLeadAgent').on('change', function() {
        var NewAgentId = $( ".ChangeLeadAgent" ).val();
        var TakeLeadPleaseWait = $.notify(
          {
            icon: 'fas fa-spinner fa-spin',
            message: '<?php echo lang('updating_represent') ?>',
          }
          ,{
            type: 'warning',
          }
        );
        $.ajax({
          type: "POST",
          url: "action/ChangeLeadAgent.php?LeadId=<?php echo $PipeNow->id; ?>&UserId="+NewAgentId,
          success: function(dataN)
          {
            TakeLeadPleaseWait.close();
            $.notify(
              {
                icon: 'fas fa-check-circle',
                message: '<?php echo lang('representative_updated') ?>',
              }
              ,{
                type: 'success',
              }
            );
          }
          ,
          error: function(xhr, status, error) {
            TakeLeadPleaseWait.close();
            $.notify(
              {
                icon: 'fas fa-times-circle',
                message: '<?php echo lang('error_oops_something_went_wrong') ?>',
              }
              ,{
                type: 'danger',
              }
            );
          }
        }
              );
      }
                                );
      <?php }
  ?>
    function SendSms(info) {
    $('#SmsSubmit').attr("disabled", true);
    var SendSmsPleaseWait = $.notify(
      {
        icon: 'fas fa-spinner fa-spin',
        message: '<?php echo lang('sending_message') ?>',
      }
      ,{
        type: 'warning',
      }
    );
    $.ajax({
      type: "POST",
      url: "POS3/SendRequestSMS2.php",
      data: $("#SendSmss").serialize(),
      success: function(dataN)
      {
        SendSmsPleaseWait.close();
        $.notify(
          {
            icon: 'fas fa-check-circle',
            message: '<?php echo lang('message_sent') ?>',
          }
          ,{
            type: 'success',
          }
        );
        //alert(dataN);
        var paramsN={
        };
        dataN.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(str,key,value){
          paramsN[key] = value;
        }
                     );
        $('#SmsSubmit').attr("disabled", false);
        $('#SendSmss')[0].reset();
        $('#Message').val('');
        var LengthM = $('#Message').val().length;
        var LengthT = Math.ceil(($('#Message').val().length)/<?php echo $SettingsInfo->SMSLimit; ?>);
        $("#count").text(LengthM + ' ' + lang('chars_divided_to') + ' ' + LengthT + ' ' + lang('messages'));
      }
      ,
      error: function(xhr, status, error) {
        SendSmsPleaseWait.close();
        $.notify(
          {
            icon: 'fas fa-times-circle',
            message: '<?php echo lang('error_oops_something_went_wrong') ?>',
          }
          ,{
            type: 'danger',
          }
        );
        $('#SmsSubmit').attr("disabled", false);
      }
    }
          );
    return false;
  }
</script>
<script>
  //שינוי עמוד בהתאם לטאב
  $('#newnavid a').click(function(e) {
    e.preventDefault();
    $(this).pill('show');
    $('.tab-content > .tab-pane.active').jScrollPane();
    scheduler.update_view();
    $('html,body').scrollTop(0);
  }
                        );
  $("a").on("shown.bs.tab", function(e) {
    var id = $(e.target).attr("href").substr(1);
    window.location.hash = id;
    //  scheduler.update_view();
    $('html,body').scrollTop(0);
  }
           );
  // on load of the page: switch to the currently selected tab
  var hash = window.location.hash;
  $('.nav-tabs a[href="' + hash + '"]').tab('show');
  //סיום שינוי עמוד בהתאם לטאב
  $( "#ChooseAgentForTask" ).select2( {
    theme:"bsapp-dropdown", placeholder: "Select a State" }
                                    );
  $( "#Items1" ).select2( {
    theme:"bsapp-dropdown", placeholder: "Select a State" }
                        );


  //$( "#ItemsKeva" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl" } );
  $(function() {
    var time = function(){
      return'?'+new Date().getTime()};
    // Header setup
    $('#EditPaymentTable').imgPicker({
    }
                                    );
    $('#AddKevaPopup').imgPicker({
    }
                                );
    $('#AddActivityPopup').imgPicker({
    }
                                    );
  }
   );
</script>
<script>
  $(document).ready(function(){
    var windowWidth = $(window).width();
    if(windowWidth <= 1024) //for iPad & smaller devices
      $('#MenuCard, #ClientInfoCard').removeClass('show');
    $('html,body').scrollTop(0);
  }
                   );

  function UpdateLive(show,hide, isRandom = false) {
      if (isRandom){
          return;
      }
    $(".EditQuick").hide();
    $(".ShowQuick").show();
    $("#"+show).show();
    $("#"+hide).hide();
  }
  function CloseQuickEdit() {
    $(".EditQuick").hide();
    $(".ShowQuick").show();
  }

  // Move to Archive popup

  function clientStatusChange(element = null, reqType) {
      debugger;//todo
      const submitBtn = archivePopupVars.popUp.find('#submitReason');

      archivePopupVars.oldStatus = <?= $Supplier->Status ?>;
      archivePopupVars.leadId = <?= $PipeId ?? 0 ?>;

      if (element == null) {
          if (reqType !== 2) {
              $('#user-overview').find('input[name="status"]:checked').val() == '1' ? archivePopupVars.newStatus = 1 : archivePopupVars.newStatus = 0; // General Tab

          } else {
              $('#js-UserSettings-EditClient').find('select[name="Status"]').val() == '1' ? archivePopupVars.newStatus = 1 : archivePopupVars.newStatus = 0; // Settings Tab
          }

      } else {
          archivePopupVars.pipeId = $(element).is('a') == true ? $(element).find('li').attr('id').replace('SetPipe','') : $(element).closest('div').attr('id');
          archivePopupVars.newStatus = 2;

          if(archivePopupVars.pipeId != <?= $GetFails ?? 0 ?>) {
              UpdateInProfilePipeline();
              return;
          } else {
              archivePopupVars.newStatus = 1;
          }
      }

      if (archivePopupVars.oldStatus == archivePopupVars.newStatus) return;

      archivePopupVars.requestType = reqType;

      if (!submitBtn.length) {
          CreateFailReasonPopupButtons(false);
      }

      if (archivePopupVars.newStatus === 1) {
          archivePopupVars.popUp.modal('show');
      }
  }

  function ChangeInput(name,placeholder,value) {
    //change to input
    $('#'+name).replaceWith($('<form action="EditClient" class="ajax-form text-start" autocomplete="off"><div class="input-group mb-3"><div class="input-group-prepend"><button type="submit" class="btn btn-outline-secondary" type="button"><i class="fas fa-sync-alt fa-xs"></i></button></div><input type="text" class="form-control" placeholder="'+ placeholder +'" name="'+ name +'" value="'+ value +'"></div></form>'))
    //change to input
  }
  $("#Message").keyup(function(){
    var LengthM = $(this).val().length;
    var LengthT = Math.ceil(($(this).val().length)/<?php echo $SettingsInfo->SMSLimit; ?>);
    $("#count").text(LengthM + ' <?= lang('chars_divided_to') ?> ' + LengthT + ' <?= lang('messages') ?>');
  }
                     );
  function SetSavedMessage(ItemId, ClientId) {
    var selectval = ItemId;
    var cid = ClientId;
    var SetSavedMsgNotifiWait = $.notify(
      {
        icon: 'fas fa-spinner fa-spin',
        message: '<?php echo lang('loading_message') ?>',
      }
      ,{
        type: 'warning',
      }
    );
    $.ajax({
      url: 'action/GetSavedMsg.php',
      type: 'POST',
      dataType : 'json',
      data: { id : selectval, cid : cid },
      success: function(data) {
        SetSavedMsgNotifiWait.close();
        $('#Message').val(data.smscontent);
        $('#PushMessage').val(data.smscontent);
        $('#emailsubject').val(data.emailtitle);
        $('#emailmessage').val(data.emailcontent);
        $('#emailmessage').summernote("code", data.emailcontent);
        var LengthM = $('#Message').val().length;
        var LengthT = Math.ceil(($('#Message').val().length)/<?php echo $SettingsInfo->SMSLimit; ?>);
        $("#count").text(LengthM + ' <?= lang('chars_divided_to') ?> ' + LengthT + ' <?= lang('messages') ?>');
      }
    }
          );
  }
  function submitDetailsForm() {
    if( !$('#Message').val() ) {
      $.notify(
        {
          icon: 'fas fa-times-circle',
          message: '<?php echo lang('sms_content_empty') ?>',
        }
        ,{
          type: 'danger',
        }
      );
    }
    else {
      $("#SendSmss").submit();
      $("#sendEmail").submit();
    }
  }
</script>
<script>

  $(document).ready(function() {

      $('#user-settings').find('select[name=PayClientId]').on('change', function (){
          if('<?= $Supplier->PayClientId ?>' != 0 && '<?= $isPayedFor ?: 0 ?>' == 1){
              $('#js-kevaExistPopup').modal('show');
          }
      });

    $('.Carteset').DataTable({
      responsive: true,
        paging: true,
        pagingType: "numbers",
      "language": {
        "processing":   "<?php echo lang('processing_client_profile') ?>",
        "lengthMenu":   "<?php echo lang('show_client_profile') ?> _MENU_ <?php echo lang('items') ?>",
        "zeroRecords":  "<?php echo lang('no_matcing_client_profile') ?>",
        "emptyTable":   "<?php echo lang('no_matcing_client_profile') ?>",
        "info": "_START_ <?php echo lang('to_user_manage') ?> _END_ <?php echo lang('of_user_manage') ?> _TOTAL_ <?php echo lang('records_user_manage') ?>" ,
        "infoEmpty":    "0 <?php echo lang('to_user_manage') ?> 0 <?php echo lang('of_user_manage') ?> 0 <?php echo lang('records_user_manage') ?>",
        "infoFiltered": "(<?php echo lang('filtered_client_profile') ?> _MAX_  <?php echo lang('records_user_manage') ?>)",
        "infoPostFix":  "",
        "search":       "<?php echo lang('search_client_profile') ?> ",
        "url":          "",
        "paginate": {
          "first":    "<?php echo lang('first') ?>",
          "previous": "<?php echo lang('previous') ?>",
          "next":     "<?php echo lang('next_client_profile') ?>",
          "last":     "<?php echo lang('last_client_profile') ?>"
        }
      }
      ,
      order: [[0, 'asc']],
      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
      buttons: [
        <?php if (Auth::userCan('98')):
        $CompanyName = str_replace('"',"``",@$Supplier->CompanyName);
        $CompanyName = str_replace("'","`",@$CompanyName);
        ?>
        {
        extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('index_client_profile').' '.htmlentities(trim($CompanyName)); ?>', className: 'btn btn-dark'}
        ,
        {
        extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('index_client_profile') .' ' .htmlentities(trim($CompanyName)); ?>' , className: 'btn btn-dark'}
        ,
        <?php endif ?>
      ],
    }
                            );
    $('.Log').DataTable({
      responsive: true,
        paging: true,
        pagingType: "numbers",
      "language": {
        "processing":   "<?php echo lang('processing_client_profile') ?>",
        "lengthMenu":   "<?php echo lang('show_client_profile') ?> _MENU_ <?php echo lang('items') ?>",
        "zeroRecords":  "<?php echo lang('no_matcing_client_profile') ?>",
        "emptyTable":   "<?php echo lang('no_matcing_client_profile') ?>",
        "info": "_START_ <?php echo lang('to_user_manage') ?> _END_ <?php echo lang('of_user_manage') ?> _TOTAL_ <?php echo lang('records_user_manage') ?>" ,
        "infoEmpty":    "0 <?php echo lang('to_user_manage') ?> 0 <?php echo lang('of_user_manage') ?> 0 <?php echo lang('records_user_manage') ?>",
        "infoFiltered": "(<?php echo lang('filtered_client_profile') ?> _MAX_  <?php echo lang('records_user_manage') ?>)",
        "infoPostFix":  "",
        "search":       "<?php echo lang('search_client_profile') ?> ",
        "url":          "",
        "paginate": {
          "first":    "<?php echo lang('first') ?>",
          "previous": "<?php echo lang('previous') ?>",
          "next":     "<?php echo lang('next_client_profile') ?>",
          "last":     "<?php echo lang('last_client_profile') ?>"
        }
      }
      ,
      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
      buttons: [
        <?php if (Auth::userCan('98')): ?>
        {
        extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('log_client_profile').' '.htmlentities(trim($CompanyName)); ?>', className: 'btn btn-dark'}
        ,
        {
        extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('log_client_profile').' '.htmlentities(trim($CompanyName)); ?>' , className: 'btn btn-dark'}
        ,
        <?php endif ?>
      ],
    }
                       );
    $('.tableForms').DataTable({
      responsive: true,
            paging: true,
            pagingType: "numbers",
      "language": {
        "processing":   "<?php echo lang('processing_client_profile') ?>",
        "lengthMenu":   "<?php echo lang('show_client_profile') ?> _MENU_ <?php echo lang('items') ?>",
        "zeroRecords":  "<?php echo lang('no_matcing_client_profile') ?>",
        "emptyTable":   "<?php echo lang('no_matcing_client_profile') ?>",
        "info": "_START_ <?php echo lang('to_user_manage') ?> _END_ <?php echo lang('of_user_manage') ?> _TOTAL_ <?php echo lang('records_user_manage') ?>" ,
        "infoEmpty":    "0 <?php echo lang('to_user_manage') ?> 0 <?php echo lang('of_user_manage') ?> 0 <?php echo lang('records_user_manage') ?>",
        "infoFiltered": "(<?php echo lang('filtered_client_profile') ?> _MAX_  <?php echo lang('records_user_manage') ?>)",
        "infoPostFix":  "",
        "search":       "<?php echo lang('search_client_profile') ?> ",
        "url":          "",
        "paginate": {
          "first":    "<?php echo lang('first') ?>",
          "previous": "<?php echo lang('previous') ?>",
          "next":     "<?php echo lang('next_client_profile') ?>",
          "last":     "<?php echo lang('last_client_profile') ?>"
        }
      }
      ,
      order: [[0, 'DESC']],
      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
      buttons: [
        <?php if (Auth::userCan('98')): ?>
          {
          text: '<?php echo lang('file_upload') ?> <i class="fas fa-file-upload" aria-hidden="true"></i>',
          className: 'btn btn-dark',
          action: function ( e, dt, node, config ) {
            $('#docUpload').click();
          }
        },
        {
        extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('log_client_profile').' '.htmlentities(trim($CompanyName)); ?>', className: 'btn btn-dark'}
        ,
        {
        extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('log_client_profile').' '. htmlentities(trim($CompanyName)); ?>' , className: 'btn btn-dark'}
        ,
        <?php endif ?>
      ],
    }
                              );
    $('.ActivityTable').DataTable({
      responsive: true,
            paging: true,
            pagingType: "numbers",
      "language": {
        "processing":   "<?php echo lang('processing_client_profile') ?>",
        "lengthMenu":   "<?php echo lang('show_client_profile') ?> _MENU_ <?php echo lang('items') ?>",
        "zeroRecords":  "<?php echo lang('no_matcing_client_profile') ?>",
        "emptyTable":   "<?php echo lang('no_matcing_client_profile') ?>",
        "info": "_START_ <?php echo lang('to_user_manage') ?> _END_ <?php echo lang('of_user_manage') ?> _TOTAL_ <?php echo lang('records_user_manage') ?>" ,
        "infoEmpty":    "0 <?php echo lang('to_user_manage') ?> 0 <?php echo lang('of_user_manage') ?> 0 <?php echo lang('records_user_manage') ?>",
        "infoFiltered": "(<?php echo lang('filtered_client_profile') ?> _MAX_  <?php echo lang('records_user_manage') ?>)",
        "infoPostFix":  "",
        "search":       "<?php echo lang('search_client_profile') ?> ",
        "url":          "",
        "paginate": {
          "first":    "<?php echo lang('first') ?>",
          "previous": "<?php echo lang('previous') ?>",
          "next":     "<?php echo lang('next_client_profile') ?>",
          "last":     "<?php echo lang('last_client_profile') ?>"
        }
      }
      ,
      pageLength: 50,
      bSort: false,
      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
      buttons: [
        <?php if (Auth::userCan('98')): ?>
        {
        extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('customer_card_membership'). ' ' .htmlentities(trim($CompanyName)); ?>', className: 'btn btn-dark'}
        ,{
        extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('customer_card_membership').' '.htmlentities(trim($CompanyName)); ?>' , className: 'btn btn-dark'}
        ,
        <?php endif ?>
      ],
    }
                                 );
  }
                   );
  function FixSmsEmailForm() {
    var phoneinput = $("#phone").text();
    var emailinput = $("#email").text();
    if (phoneinput.includes('<?php echo lang('no_phone') ?>') == false && emailinput.includes('<?php echo lang('customer_card_adress') ?>') == false) {
      $('.ShowEmailform').show();
      $('.ShowSMSform').show();
      $('.ShowEmailform').removeClass('col-md-12');
      $('.ShowSMSform').removeClass('col-md-12');
      $('.ShowEmailform').addClass('col-md-6');
      $('.ShowSMSform').addClass('col-md-6');
      $('.SendSmsEmailBoth').show();
    }
    else {
      if (phoneinput.includes('<?php echo lang('no_phone') ?>') == true) {
        $('.ShowSMSform').hide();
        $('.ShowEmailform').show();
        $('.ShowEmailform').addClass('col-md-12');
        $('.SendSmsEmailBoth').hide();
      }
      if (emailinput.includes('<?php echo lang('customer_card_adress') ?>') == true) {
        $('.ShowSMSform').show();
        $('.ShowEmailform').hide();
        $('.ShowSMSform').addClass('col-md-12');
        $('.SendSmsEmailBoth').hide();
      }
    }
  }
  FixSmsEmailForm();
  $(".CitiesSelect").on("select2:unselect", function(e) {
    $(".StreetSelect").select2("val", "");
    $('.AddressCols').removeClass('col-md-4');
    $('.AddressCols').addClass('col-md-6');
    $('.NoAddress').hide();
    $("#StreetH").val("");
  }
                       );
  $(".StreetSelect").on("select2:unselect", function(e) {
    $('.AddressCols').removeClass('col-md-4');
    $('.AddressCols').addClass('col-md-6');
    $('.NoAddress').hide();
    $("#StreetH").val("");
  }
                       );
  $('.CitiesSelect').select2({
    theme:"bsapp-dropdown",
    placeholder: "<?php echo lang('select_city') ?>",
    language: "<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_lang'] : 'he' ?>",
    allowClear: true,
    width: '100%',
    ajax: {
      url: 'action/CitiesSelect.php',
      dataType: 'json'
    }
    ,
    minimumInputLength: 3,
  }
                            );
  $('.StreetSelect').select2({
    theme:"bsapp-dropdown",
    placeholder: "<?php echo lang('select_address') ?>",
    language: "<?php echo isset($_COOKIE['boostapp_lang']) ? $_COOKIE['boostapp_lang'] : 'he' ?>",
    allowClear: true,
    width: '100%',
    ajax: {
      url: 'action/StreetsSelect.php',
      dataType: 'json',
      data: function (params) {
        var CityId = $(".CitiesSelect").val();
        var query = {
          q: params.term,
          CityId: CityId
        }
        return query;
      }
    }
    ,
    minimumInputLength: 3,
  }
                            );
  $('.StreetSelect').on('change', function() {
    if (this.value == '99999999') {
      $('.AddressCols').removeClass('col-md-6');
      $('.AddressCols').addClass('col-md-4');
      $('.NoAddress').show();
    }
    else {
      $('.AddressCols').removeClass('col-md-4');
      $('.AddressCols').addClass('col-md-6');
      $('.NoAddress').hide();
      $("#StreetH").val("");
    }
  }
                       )
  if ($('.StreetSelect').val() == '99999999') {
    $('.AddressCols').removeClass('col-md-6');
    $('.AddressCols').addClass('col-md-4');
    $('.NoAddress').show();
  }
  else {
    $('.AddressCols').removeClass('col-md-4');
    $('.AddressCols').addClass('col-md-6');
    $('.NoAddress').hide();
    $("#StreetH").val("");
  }
  function PipeSendForm(PipeLineId,ClientId) {
    var PipeId =  PipeLineId;
    var ClientId = ClientId;
    $( "#DivPipLinePopUp" ).empty();
    var modalcode = $('#PipLinePopUp');
    $('#PipLinePopUp .ip-modal-title').html('<?php echo lang('send_joining_form') ?>');
    modalcode.modal('show');
    var url = 'new/PipeLine_SendForm.php?Id='+PipeId+'&ClientId='+ClientId;
    $('#DivPipLinePopUp').load(url,function(e){
      $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
      return false;
    }
                              );
  };
  function PipeSendForm2(PipeLineId,ClientId) {
    var PipeId =  PipeLineId;
    var ClientId = ClientId;
    $( "#DivPipLinePopUp" ).empty();
    var modalcode = $('#PipLinePopUp');
    $('#PipLinePopUp .ip-modal-title').html('<?php echo lang('send_health_declaration_form') ?>');
    modalcode.modal('show');
    var url = 'new/PipeLine_SendMedicalForm.php?Id='+PipeId+'&ClientId='+ClientId;
    $('#DivPipLinePopUp').load(url,function(e){
      $('#DivPipLinePopUp .ajax-form').on('submit', BeePOS.ajaxForm);
      return false;
    }
                              );
  };
  // $(document).ready(function () {
  //   //detect javascript from credit card form
  //   window.addEventListener('message', function(e) {
  //     if (e.data.hasOwnProperty("MeshulamActiveLoader_nauK1M54J") && e.data.MeshulamActiveLoader_nauK1M54J == 1){
  //       //do your code like display loader //
  //       var spinnerVisible = false;
  //       //    $('.payment_loader').show();
  //       if (!spinnerVisible) {
  //         $(".payment_loader").fadeIn("fast");
  //         spinnerVisible = true;
  //         setTimeout(function () {
  //           $("#Text1").show();
  //           setTimeout(function () {
  //             $("#Text1").hide();
  //             //  toggleDiv();
  //           }
  //                      , 15000);
  //         }
  //                    , 1000);
  //         setTimeout(function () {
  //           $("#Text2").show();
  //           setTimeout(function () {
  //             $("#Text2").hide();
  //             // toggleDiv();
  //           }
  //                      , 15000);
  //         }
  //                    , 17000);
  //         setTimeout(function () {
  //           $("#Text3").show();
  //           setTimeout(function () {
  //             $("#Text3").hide();
  //             //  toggleDiv();
  //           }
  //                      , 35000);
  //         }
  //                    , 32000);
  //       }
  //     }
  //   }
  //                           , true);
  // }
  //                  );

  $(".select2Rank").select2({
      placeholder: "<?php echo lang('choose') ?>",
      width:"100%",
      'language':"he",
      // dir: "rtl",
      minimumResultsForSearch: -1
  });


  $('#Rank').on('select2:select', function (e) {
      var selected = $(this).val();

      if(selected != null)
      {
          if(selected.indexOf('0')>=0){
              $(this).val('0').select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose_class_type') ?>", 'language':"he", dir: "rtl" } );
          }
      }

  });

  $('#Rank').on('select2:open', function () {
      // get values of selected option
      var values = $(this).val();
      // get the pop up selection
      var pop_up_selection = $('.select2-results__options');
      if (values != null ) {
          // hide the selected values
          pop_up_selection.find("li[aria-selected=true]").hide();

      } else {
          // show all the selection values
          pop_up_selection.find("li[aria-selected=true]").show();
      }

  });

  <?php
  if(isset($_REQUEST['client_activities_number'])) { ?>
      $('#AddDocsClient input.CloseCheckBoxPayment[value=<?= $_REQUEST['client_activities_number']?>]').trigger('click')
  <?php }?>

  function openPaymentIframe($btn, amount, paymentNumber, action, callback) {

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
              ClientId: '<?= $Supplier->id ?>',
              amount: amount,
              orderType: orderType,
              TypeDoc: '<?= $GroupNumber ?? '' ?>',
              paymentNumber: paymentNumber
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
                  });
              } else {
                  $thisButton.find('.js-btn-text').removeClass('d-none');
                  $thisButton.find('.js-loading-label').addClass('d-none');
                  $thisButton.find('.spinner-border').addClass('d-none');

                  Swal.fire({
                      title: "",
                      text: response.message ?? lang('processing_error_meshulam'),
                      icon: "error"
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

              $thisButton.find('.js-btn-text').removeClass('d-none');
              $thisButton.find('.js-loading-label').addClass('d-none');
              $thisButton.find('.spinner-border').addClass('d-none');
          }
      });
  }

  var checkPaymentStatus;

  $('.js-new-card-iframe-button').click(function () {
      $button = $(this);
      openPaymentIframe($button, 0, 1, 'addNewCard', function () {
          checkPaymentStatus = setInterval(function() {
              if (window.paymentStatus !== 'waiting') {
                  $button.siblings('.iframe-wrapper').hide();

                  if (window.paymentStatus === 'error') {
                      Swal.fire({
                          title: "",
                          text: lang('processing_error_meshulam'),
                          icon: "error"
                      });

                      clearInterval(checkPaymentStatus);
                      return;
                  }

                  if (window.paymentStatus == 'success' || window.paymentStatus == 'success_meshulam') {
                      Swal.fire({
                          title: "",
                          text: lang('cc_saved_new_app_credit'),
                          icon: "success",
                          onAfterClose: function () {
                              window.location.reload();
                          }
                      });

                      clearInterval(checkPaymentStatus);
                  }
              }

          }, 1500);
      });
  });

  $('.js-pay-new-card-iframe-button').click(function (e) {
      e.preventDefault();

      $button = $(this);

      var CreditValue3 = encodeURI(document.getElementById('CreditValue3').value);
      var Tash3 = encodeURI(document.getElementById('Tash3').value);
      var tashType3 = encodeURI(document.getElementById('tashType3').value);

      var ClientId = '<?php echo $Supplier->id; ?>';
      var Finalinvoicenum = $('#Finalinvoicenum').val();
      var TrueFinalinvoicenum = $('#TrueFinalinvoicenum').val();
      var TypeDoc = '<?php echo $GroupNumber ?? ''; ?>';
      var TempId = '<?php echo $Supplier->id; ?>';

      if (CreditValue3!='' && Tash3!='' && tashType3!='' && TempId!='' && parseFloat(CreditValue3) > 0 && parseFloat(CreditValue3)<=parseFloat(Finalinvoicenum)) {
          openPaymentIframe($button, CreditValue3, Tash3, 'payWithNewCard', function (response) {
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
                          if (window.paymentType === 'addNewCard') {
                              var OrderId = response.orderId;

                              $("#DocsPayments").load("DocPaymentInfoClient.php?TempId=" + TempId + "&CreditValue=" + CreditValue3 + "&Tash=" + Tash3 + "&tashType=" + tashType3 + "&Act=3&Credit=3&Finalinvoicenum=" + Finalinvoicenum + "&TypeDoc=" + TypeDoc + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum + "&OrderId=" + OrderId + "&ClientId=" + ClientId);
                          } else {
                              var OrderId = response.orderId;
                              $("#DocsPayments").load("DocPaymentInfoClient.php?TempId=" + TempId + "&CreditValue=" + CreditValue3 + "&Tash=" + Tash3 + "&tashType=" + tashType3 + "&Act=3&Credit=3&Finalinvoicenum=" + Finalinvoicenum + "&TypeDoc=" + TypeDoc + "&TrueFinalinvoicenum=" + TrueFinalinvoicenum + "&OrderId=" + OrderId + "&ClientId=" + ClientId);

                              setTimeout(function () {
                                  window.$('#ReceiptBtn').trigger('click');
                              }, 3000);
                          }

                          Swal.fire({
                              text: lang('processing_done_meshulam'),
                              icon: 'success'
                          });

                          clearInterval(checkPaymentStatus);
                      }
                  }

              }, 1500);
          });

          BN('2',lang('charging_please_wait_processing_data'));
      }
      else {
          BN('1', '<?php echo lang('charge_required_all_or_type_sum_membership') ?>');
      }
  });

  <?php if ($TypeShva=='1') { ?>
  //$("#CreateNewToken").click(function() {
  //    $.ajax({
  //            type: "POST",
  //            url: "rest/Meshulam/Token.php?ClientId=<?php //echo $Supplier->id; ?>//",
  //            success: function(dataN)
  //            {
  //                $('#DivCreateToken').show();
  //                $('#DivCreateToken').attr('src',dataN);
  //            }
  //        }
  //    );
  //});

  $("#SaveTokenMeshulam").on('click' ,function () {
      const $elem = $('.js-new-card-iframe-button');
      if($elem.length) {
          $elem.trigger('click');
      }

  });
  <?php } ?>
  
  $('#docUpload', '#user-Health').on('change', function(event){
    if(event.target instanceof HTMLInputElement && event.target.type === 'file'){
      if(event.target.files.length > 0 && event.target.value !== ''){
        const Input = event.target;
        const File = event.target.files[0];
        const Form = event.target.parentElement;
        if(/pdf|jpeg|png/i.test(File.type)){
          if(/pdf/i.test(File.type)){
              if(File.size <= 5242880){
                console.log(File, Form);
                const data = new FormData(Form);
                data.append('action', Form.getAttribute('action'));
                data.append('clientId', location.href.split('u=')[1].replace(/[^0-9]/g, ''));

                $.ajax({
                  method: "POST",
                  url: "./ajax/FileUpload.php",
                  enctype: 'multipart/form-data',
                  processData: false,
                  contentType: false,
                  cache: false,
                  data: data,
                }).done(function( response ) {
                  try {
                      response = JSON.parse(response);
                      if(Array.isArray(response)){
                          response.forEach(data => {
                              if(data.success){
                                  $.notify({
                                      icon: 'fas fa-check',
                                      message: "<?php echo lang('file_upload_success'); ?>",
                                  }, {type: 'success', z_index: '99999999'});
                                  location.reload();
                              }else{
                                  $.notify({
                                      icon: 'fas fa-exclamation',
                                      message: data.message
                                  }, {type: 'danger', z_index: '99999999'});
                                }
                            });
                        }else{
                          if(response.success){
                              $.notify({
                                  icon: 'fas fa-check',
                                  message: "<?php echo lang('file_upload_success'); ?>",
                              }, {type: 'success', z_index: '99999999'});
                              location.reload();
                          }else{
                              $.notify({
                                  icon: 'fas fa-exclamation',
                                  message: data.message
                                }, {type: 'danger', z_index: '99999999'});
                              }
                        }
                  } catch (error) {
                      $.notify({
                          icon: 'fas fa-exclamation',
                          message: "<?php lang('error_oops_something_went_wrong'); ?>"
                      }, {type: 'danger', z_index: '99999999'});
                  }                
                });
            } else{
              <?php $fileType =  lang('for_file_type'); $fileType = preg_replace("/TYPE/", 'PDF', $fileType) ?>
              $.notify({
                icon: 'fas fa-exclamation',
                message: "<?php echo lang('file_upload_size_error') . ", 5MB $fileType";?>"
              }, {type: 'danger', z_index: '99999999'});
            }
          }
          if(/jpg|jpeg|png/i.test(File.type)){
            if(File.size <= 3145728){
              console.log(File, Form);
              const data = new FormData(Form);
              data.append('action', Form.getAttribute('action'));
              data.append('clientId', location.href.split('u=')[1].replace(/[^0-9]/g, ''));

              $.ajax({
                  method: "POST",
                  url: "./ajax/FileUpload.php",
                  enctype: 'multipart/form-data',
                  processData: false,
                  contentType: false,
                  cache: false,
                  data: data,
                }).done(function( response ) {
                  try {
                      response = JSON.parse(response);
                      if(Array.isArray(response)){
                          response.forEach(data => {
                              if(data.success){
                                  $.notify({
                                      icon: 'fas fa-check',
                                      message: "<?php echo lang('file_upload_success'); ?>",
                                  }, {type: 'success', z_index: '99999999'});
                                  location.reload();
                              }else{
                                  $.notify({
                                      icon: 'fas fa-exclamation',
                                      message: data.message
                                  }, {type: 'danger', z_index: '99999999'});
                                }
                            });
                        }else{
                          if(response.success){
                              $.notify({
                                  icon: 'fas fa-check',
                                  message: "<?php echo lang('file_upload_success'); ?>",
                              }, {type: 'success', z_index: '99999999'});
                              location.reload();
                          }else{
                              $.notify({
                                  icon: 'fas fa-exclamation',
                                  message: data.message
                                }, {type: 'danger', z_index: '99999999'});
                              }
                        }
                  } catch (error) {
                      $.notify({
                          icon: 'fas fa-exclamation',
                          message: "<?php lang('error_oops_something_went_wrong'); ?>"
                      }, {type: 'danger', z_index: '99999999'});
                  }                
                });
            } else{
              <?php $fileType =  lang('for_file_type'); $fileType = preg_replace("/TYPE/", lang('image_single'), $fileType) ?>
              $.notify({
              icon: 'fas fa-exclamation',
              message: "<?php echo lang('file_upload_size_error') . ", 3MB $fileType";?>"
              }, {type: 'danger', z_index: '99999999'});
            }
          }

        }else{
          $.notify({
            icon: 'fas fa-exclamation',
            message: "<?php echo lang('file_Incompatible');?>"
          }, {type: 'danger', z_index: '99999999'});
        }
      }
      else if(event.target.files.length > 1){
        $.notify({
            icon: 'fas fa-exclamation',
            message: "<?php echo lang('multiple_upload_forbidden');?>"
        }, {type: 'danger', z_index: '99999999'});

      }else{
        $.notify({
            icon: 'fas fa-exclamation',
            message: "<?php echo lang('no_file_selected');?>"
        }, {type: 'warning', z_index: '99999999'});
      }
    }
  });

</script>
<style>
  div#spinners
  {
    display: table;
    width:100%;
    height: 100%;
    position: fixed;
    top: 0%;
    left: 0%;
    background:url(assets/img/Preloader_8.gif) no-repeat center rgba(255, 255, 255, .5);
    text-align:center;
    padding:10px;
    font:normal 16px "Rubik", Geneva, sans-serif;
    margin-left: 0px;
    margin-top: 0px;
    z-index:10000;
    overflow: auto;
  }
  #spinners #b
  {
    display: table-cell;
    padding-top: 350px;
    text-align: center;
    vertical-align: middle;
  }
  #spinners span
  {
    font-size: 18px;
    font-weight: 400;
    background-color: white;
    padding: 10px;
    margin: auto;
  }
</style>
<?php
require_once 'InfoPopUpInc.php';
}
?>
<div id="spinners" class="payment_loader"  style="display: none;">
  <div id="b">
    <span id="Text1" style="display: none;"><?php echo lang('charging_do_not_close_window') ?>
    </span>
    <span id="Text2" style="display: none;"><?php echo lang('atm_charging_pay') ?>
    </span>
    <span id="Text3" style="display: none;"><?php echo lang('thanks_for_waiting') ?>
    </span>
  </div>
</div>
<?php

require_once '../app/views/footernew.php';
else:
  redirect_to('../index.php');
endif;
else:
  redirect_to('../index.php');
endif;

?>
