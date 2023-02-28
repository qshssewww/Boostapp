<?php
require_once '../app/init.php';
require_once './Classes/LeadSource.php';
require_once './Classes/LeadStatus.php';
require_once './Classes/Brand.php';
require_once './Classes/PipelineCategory.php';
require_once './Classes/Automation.php';
require_once './Classes/ClassesType.php';
require_once './Classes/Users.php';
$pageTitle = lang('uppercase_pipeline');
require_once '../app/views/headernew.php';
?>
<?php if (Auth::check()) {
    $CompanySettingsDash = $CompanySettingsDash ?? DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();

    if (Auth::userCan('47')) { ?>
        <?php
            $LeadSources = new LeadSource();
            $Brands = new Brand();
            $PipeLineCategories = new PipelineCategory();
            $LeadStatuses = new LeadStatus();
            $ClassesType = new ClassesType();
            $Automation = new Automation();
            $UserInfo = new Users();

            /** @var TYPE_NAME $CompanyNum */
            $Category2 = $Automation->getAutomationAmount($CompanyNum, 2);
            $CompanySettingsDash = Settings::getSettings($CompanyNum);
            $MainPipeLine = $PipeLineCategories->get_main_category($CompanyNum);
            $LeadStatuses = $LeadStatuses->getLeadStatusByActAndStatus($CompanyNum, '0' , '0');
            $AgentLoops = $UserInfo->getAgent($CompanyNum);
            $ClassTypes = $ClassesType->getClassesTypeOnlyLessons($CompanyNum);
            $PipeLineCategories = $PipeLineCategories->getPipelineCategories($CompanyNum);
            $Brands = $Brands->getAllByCompanyNum($CompanyNum);
            $LeadSources = $LeadSources->getLeadSources($CompanyNum);
        ?>

        <?php
        if (!is_object($MainPipeLine) || !isset($MainPipeLine->id)) {

            ErrorPage(
                lang('error_oops_something_went_wrong')
                , lang('error_pipeline_no_longer_exists')
            );
        }
        else {
            $MainPipeId = @$MainPipeLine->id;
            ?>
            <link href="assets/css/fixstyle.css?<?php echo date('YmdHis') ?>" rel="stylesheet">
            <link href="assets/css/manage-leads.css?<?= filemtime(__DIR__.'/assets/css/manage-leads.css') ?>" rel="stylesheet">
            <canvas id="SuccessConfetti" style="z-index: 200;display: none;"></canvas>
            <div class="col-md-12">
                <div >
                    <?php require_once '../app/views/manageLeadsSettings.php'; ?>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 js-manage-leads-body">
                <a href="javascript:;" class="floating-plus-btn d-flex d-flex bg-primary" onclick=NewClient('lead') title="<?= lang('new_lead') ?>">
                    <i class="fal fa-plus fa-lg margin-a"></i>
                </a>
                <nav class="d-flex justify-content-between align-items-center py-10 mb-20 shadow bg-light pie-15 rounded nav-manage-leads">
                    <ol class="breadcrumb align-middle bg-transparent  py-5 my-auto">
                        <li class="breadcrumb-item align-middle">
                            <select class="form-control" id="ChoosePipeline" name="ChoosePipeline"  >
                                <?php
                                foreach ($PipeLineCategories as $PipeLineCategory) {
                                    ?>
                                    <option value="<?php echo $PipeLineCategory->id; ?>" <?php echo ($PipeLineCategory->id == $MainPipeId) ? 'selected' : '' ?> ><?= lang('uppercase_pipeline') ?> :: <?php echo $PipeLineCategory->Title; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </li>
                        <li class="pis-20 button-report-leads"><a class="btn btn-outline-gray-400 text-black" href="/office/LeadsJoinReport.php" target="_blank" data-toggle="tooltip" title="<?= lang('leads_report') ?>"><i class="fal fa-file-alt"></i></a></li>
                    </ol>
                    <div class="d-flex legend-task-and-select-agent">
                        <div class="d-flex flex-row align-self-center ">
                            <a href="#" class="d-flex justify-content-center align-items-center rounded-circle" data-toggle="tooltip" title="<?= lang('tasks_planned_for_today') ?>" style="color : #9ce2a7 !important ;width:16px;height:16px;"><i class="fas fa-circle" ></i></a>
                            <a href="#"  class="mis-5 d-flex justify-content-center align-items-center rounded-circle" data-toggle="tooltip" title="<?= lang('expired_tasks') ?>" style="color : #ff8080 !important;width:16px;height:16px;"><i class="fas fa-circle" ></i></a>
                            <a href="#" class="mis-5 d-flex justify-content-center align-items-center rounded-circle" style="background:  #fff0b3 !important ;width:16px;height:16px;" data-toggle="tooltip" title="<?= lang('tasks_are_not_defined') ?>">
                                <span style="color: #efc15d;  font-size: 10px;"><i class="fas fa-exclamation"></i></span>
                            </a>
                            <a href="#"  class="mis-5 d-flex justify-content-center align-items-center rounded-circle"  data-toggle="tooltip" title="<?= lang('no_tasks_planned_tasks_were_in_past') ?>"  style="color : #abb1bf !important;width:16px;height:16px;" ><i class="fas fa-circle" ></i></a>
                            <a href="#" class="mis-5 d-flex justify-content-center align-items-center rounded-circle" data-toggle="tooltip" title="<?= lang('tasks_are_planned_not_expired_yet') ?>" style="color : #40A4C5 !important;width:16px;height:16px;"><i class="fas fa-circle" ></i></a>
                        </div>
                        <?php if (Auth::userCan('142'))  { ?>
                            <div class="pis-20">
                                <select class="form-control text-start ChooseAgentForPipeline" id="ChooseAgentForPipeline" name="AgentId"  data-placeholder="<?= lang('choose_taking_care_representative') ?>" style="max-width: 200px;">
                                    <option value="" selected><?= lang('everyone') ?></option>
                                    <?php
                                    foreach ($AgentLoops as $Agent) {
                                        ?>
                                        <option value="<?php echo $Agent->id; ?>"><?php echo $Agent->display_name; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        <?php } ?>
                    </div>
                </nav>
                <div class="row d-flex flex-row-reverse js-table-lead-statuses" style="padding: 15px;">
                    <div class="d-none text-center flex-grow-1" id="js-error-get-data-lead-statuses">
                        <h5 class="m-15"><?php echo lang('error_oops_something_went_wrong') ?></h5>
                        <h6 class="m-15"><?php echo lang('select_pipeline_try_again') ?></h6>
                        <button type="button" class="btn btn-info" onclick="LeadsData.GetDataManageLeads()"><?php echo lang('try_again_leads') ?></button>
                    </div>
                </div>

                <div class="row js-loading-leads-shimmer p-30">
                    <?php for ($i = 0; $i < 12; $i++) : ?>
                        <div class="col-md-3 col-sm-6 mb-20">
                            <div class="border border-light rounded ">
                                <div class="bsapp-loading-shimmer p-15">
                                    <div>
                                        <div class="mb-15 w-100  h-10p">
                                            <div class=" h-10p"></div>
                                        </div>
                                        <div class="mb-15 w-100  h-10p">
                                            <div class=" h-10p"></div>
                                        </div>
                                        <div class="mb-15 w-100  h-10p">
                                            <div class=" h-10p"></div>
                                        </div>
                                        <div class="mb-15 w-50  h-10p">
                                            <div class=" h-10p"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
            <div class="wonlosediv bg-light sortable" style="display: none; width: 100%; height: 95px; position: fixed; left:0; bottom: 0;margin: 0px;padding: 0px; border-top: 10px solid #000000; z-index: 100;" id="<?php echo Auth::user()->CompanyNum; ?>100"></div>

            <div class="ip-modal text-start"  role="dialog" id="PipLinePopUp" data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="ip-modal-dialog BigDialog">
                    <div class="ip-modal-content">
                        <div class="ip-modal-header d-flex justify-content-between">
                            <h4 class="ip-modal-title"></h4>
                            <a class="ip-close" title="Close"   data-dismiss="modal" aria-label="Close">&times;</a>
                        </div>
                        <div class="ip-modal-body">
                            <div id="DivPipLinePopUp"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- מודל פעולות חדש -->
            <div class="ip-modal text-start" role="dialog" id="PipeActionPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="ip-modal-dialog BigDialog">
                    <div class="ip-modal-content">
                        <div class="ip-modal-header d-flex justify-content-between">
                            <h4 class="ip-modal-title" id="PipeActionPopupTitle"></h4>
                            <a class="ip-close ClassClosePopUp" title="Close"  data-dismiss="modal" aria-label="Close">&times;</a>
                        </div>
                        <div class="ip-modal-body">
                            <div id="ResultPipeline">
                                <center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- מודל פעולות חדש -->
            <!-- מודל פעולות חדש -->
            <div class="ip-modal text-start" role="dialog" id="MoveLeadProfilePopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
                <div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
                    <div class="ip-modal-content">
                        <div class="ip-modal-header d-flex justify-content-between"  <?php //_e('main.rtl') ?>>
                            <!--                <a class="ip-close ClassClosePopUp" title="Close" style="float:left;" data-dismiss="modal" aria-label="Close">&times;</a>-->
                            <h4 class="ip-modal-title"><?= lang('redirect_to_client_card') ?></h4>
                        </div>
                        <form action="MoveLeadProfile"  class="ajax-form clearfix" autocomplete="off">
                            <input type="hidden" name="ItemId" id="MoveLeadProfileId" value="">
                            <div class="ip-modal-footer d-flex justify-content-between">
                                <button type="submit" name="submit" class="btn btn-primary"><?= lang('yes') ?></button>
                                <a  class="btn btn-dark ip-close text-white" data-dismiss="modal"><?= lang('no') ?></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php include_once __DIR__.'/partials-views/archive-popup/clientFailReasonPopup.php'; ?>
            <script type="text/javascript" src="js/manageLeadsView/manage-leads.js?<?php echo filemtime(__DIR__.'/js/manageLeadsView/manage-leads.js') ?>"></script>
            <script type="text/javascript" src="js/settingsDialog/settingsDialog.js?<?php echo filemtime(__DIR__.'/js/settingsDialog/settingsDialog.js') ?>"></script>
            <script type="text/javascript" src="js/settingsDialog/manageLeadsSettings.js?<?php echo filemtime(__DIR__.'/js/settingsDialog/manageLeadsSettings.js') ?>"></script>
            <script type="text/javascript" src="js/manageLeadsView/manageLeadsTable.js?<?php echo filemtime(__DIR__.'/js/manageLeadsView/manageLeadsTable.js') ?>"></script>
            <script type="text/javascript" src="js/settingsDialog/facebookAttachLeadsSettings.js?<?php echo filemtime(__DIR__.'/js/settingsDialog/facebookAttachLeadsSettings.js') ?>"></script>
<!--            <script src="--><?php //echo App::url('CDN/moment.js') ?><!--"></script>-->
            <script  type="text/javascript">
                var $companyNo;
                $(document).ready(function () {
                    $companyNo = <?php echo $CompanyNum ?>
                });
            </script>
        <?php } } else { ?>
        <?php redirect_to('../index.php'); ?>
    <?php } ?>
<?php } ?>

<?php require_once '../app/views/footernew.php'; ?>