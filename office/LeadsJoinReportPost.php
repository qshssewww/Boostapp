<?php
require_once '../app/init.php';
require_once './Classes/ClassStatus.php';
require_once './Classes/ClassStudioDate.php';
require_once './Classes/PipelineCategory.php';
require_once './Classes/Pipereasons.php';
require_once './Classes/ClassStudioAct.php';
require_once __DIR__ . '/../app/helpers/TimeHelper.php';

$CompanyNum = Auth::user()->CompanyNum;
$dateFrom = !empty($_POST['startDate']) ? $_POST['startDate'] : date("Y-m-01");
$dateTo = !empty($_POST['endDate']) ? $_POST['endDate'] : date("Y-m-t");
$leadStatus = $_POST['leadStatus'] ?? 0;

$PipelineCategory = new PipelineCategory();
$PipeLine = new Pipeline();
$LeadStatus = new LeadStatus();

$CheckPipe = $PipelineCategory->getAllCategories($CompanyNum);

$Leads = $leadStatus == 0 ?
    $PipeLine->GetOpenLeadsByDates((int)$CompanyNum, $dateFrom, $dateTo) :
    $PipeLine->getLeadsByStatusFilter($dateFrom, $dateTo, $CompanyNum, $leadStatus);

$LeadsShow = array_filter($Leads, function($Lead) use ($CompanyNum){
    if(!empty(LeadStatus::getLeadStatus($CompanyNum, $Lead->PipeId))) return $Lead;
});

$NewTask = '0';
$resArr = ["data" => []];
foreach ($LeadsShow as $Leads) {
    $reportArray = [];
    $DataPopUp = '';
    $display = '--';
    $guide = '--';
    $statusTitle = '--';
    $Client = new Client();
    $ClientInfo = $Client->getClientByCompanyAndId($CompanyNum, $Leads->ClientId);
    $ClientLeadStatus = LeadStatus::getLeadStatus($CompanyNum, $Leads->PipeId);
    $PipeInfo = $PipelineCategory->getMainPipelineCategory($CompanyNum, $Leads->MainPipeId);

    if((int)$leadStatus !== 1 && $ClientInfo) {
        $ClassStudioDate = new ClassStudioDate();
        $joinDate = date('Y-m-d', strtotime($ClientInfo->Dates));

        $ClassAct = ClassStudioAct::getLastTryOutClass($CompanyNum, $ClientInfo->id, $joinDate);
        if($ClassAct) {
            $statusTitle = ClassStatus::find($ClassAct->Status)->Title ?? lang('error_admin');
            $ClassAct->ClassDate = TimeHelper::getHebrewDayName(date('D', strtotime($ClassAct->ClassDate))). " ". date('d/m', strtotime($ClassAct->ClassDate));
            $display = "$ClassAct->ClassName: $ClassAct->ClassDate";
            $ClassStudio = $ClassStudioDate->getClassById($ClassAct->ClassId, $CompanyNum);
            $guide = $ClassStudio->GuideName ?? "--";

            unset($ClassAct->ClassId, $ClassAct->ClassDate, $ClassAct->Status);
        } 
    }    

    
    $Pipeline = "--";
    $PipelineStatusInfo = "--";
    $FailureReason = "--";
    $ConvertDate = "--";
    if ($Leads->StatusFilter == "0") {
        $Pipeline = $PipeInfo->Title ?? "";
        $PipelineStatusInfo = $ClientLeadStatus->Title ?? "";
    } else if ($Leads->StatusFilter == "1"){
        $ConvertDate = date('d/m/Y H:i', strtotime($Leads->ConvertDate));
    }
     else if ($Leads->StatusFilter == "2") {
        $ConvertDate = date('d/m/Y H:i', strtotime($Leads->ConvertDate));
        if($Leads->ReasonsId != 0) {
            $pipeReason = Pipereasons::find($Leads->ReasonsId);
            if($pipeReason->Title) {
                $FailureReason = $Leads->FreeText ? $pipeReason->Title . ' : ' . $Leads->FreeText : $pipeReason->Title;
            }
        }
    }

    if (!empty($ClientInfo->id)) {
        $PipeInfo = $PipelineCategory->getMainPipelineCategory($CompanyNum, $Leads->MainPipeId);
        $PipeStatusInfo = $LeadStatus->getLeadStatus($CompanyNum, $Leads->PipeId);
        $Users = new Users();
        $UserInfo = $Users->getGuideFromAll($Leads->AgentId, $CompanyNum);
        $PipeLineColor = $Leads->StatusColor;


        if (empty($Leads->Tasks)) {

            $DataPopUp = '';
            $LeadTasks = '<i class="fas fa-exclamation" style="color:' . $PipeLineColor . '" data-toggle="tooltip" title="' . lang("tasks_are_not_defined") . '"></i>';

        } else {

            $Loops = json_decode($Leads->Tasks, true);
            foreach ($Loops['data'] as $key => $val) {
                $task = db::table('calendar')->where('id', '=', $val['Id'])->first();
                if (date('c',strtotime($task->start_date)) > date('c')){ //future task
                    $ColorRed = '#ff8080';
                    $textColor = 'text-info';
                    $PipeLineColor = '#40A4C5'; //blue
                } elseif (date('c',strtotime($task->start_date)) <= date('c') && date('c',strtotime($task->end_date)) >= date('c')){ //current task
                    $PipeLineColor = 'text-success'; //green
                    $ColorRed = '#ff8080';
                    $textColor = 'text-success';
                } elseif (date('c',strtotime($task->start_date)) <= date('c') && date('c',strtotime($task->end_date)) < date('c')){ //late task
                    $PipeLineColor = '#ff8080';
                    $ColorRed = '#ff8080';
                    $textColor = 'text-danger';
                } else $textColor = 'text-success';

                $DataPopUp .= "<div class='row " . $textColor . "'>
                <a class='col-12 js-new-task' data-task='" . $val['Id'] . "' data-client='" . $Leads->ClientId . "' data-pipe-id='" . $Leads->id . "' style='cursor: pointer;'><i class='" . $val['Icon'] . " fa-xs'></i> " . $val['Title'] . "<br>
                    <i class='fas fa-calendar-alt fa-xs'></i> <span style='font-size: 11px;'>" . date('H:i', strtotime($val['Time'])) . " " . date('d/m/Y', strtotime($val['Date'])) . "</span>
                </a>
                </div>
                <hr>";
            }

            $LeadTasks = '<i class="fas fa-tasks" style="color:' . $PipeLineColor . '" data-toggle="tooltip" title="' . lang("tasks") . '"></i>';
        }

        $str = "";
        if ((Auth::userCan('48'))) $str = '<a data-task="' . $NewTask . '" data-client="' . $Leads->ClientId . '"  data-pipe-id="' . $Leads->id . '" class="text-dark js-new-task">' . lang('new_task') . '</a>';

        $dataContent = '<div class="py-5 pr-5">
         <div class="DivScroll text-dark bsapp-max-h-250p m-0 p-0 ">' . $DataPopUp . '
          </div>
    <div class="text-center"> 
           ' . $str . '
         </div> 
      </div>';

        $reportArray["clientId"] = $ClientInfo->id;
        $reportArray["clientFullName"] = '<a href="/office/ClientProfile.php?u=' . $ClientInfo->id . '">' . $ClientInfo->CompanyName . '</a>';
        $reportArray["clientEmail"] = $ClientInfo->Email;
        $reportArray["clientPhone"] = $ClientInfo->ContactMobile;
        $reportArray["brandName"] = $ClientInfo->BrandName;
        $reportArray["pipeInfoTitle"] = $Pipeline;
        $reportArray["pipeStatusInfoTitle"] = $PipelineStatusInfo;
        $reportArray["leadSource"] = $Leads->Source;
        $reportArray["failureReason"] = $FailureReason;
        $reportArray["Dates"] = date('d/m/Y H:i', strtotime($Leads->Dates));
        $reportArray["convertDates"] = $Leads->ConvertDate;
        $reportArray["dataContent"] = $dataContent;
        $reportArray["leadTasks"] = $LeadTasks;
        $reportArray['ClientAct'] = $display ?? "--";
        $reportArray['ClassStatus'] = $statusTitle ?? "--";
        $reportArray['classGuide'] = $guide ?? "--";
        $reportArray['className'] = $ClassAct->ClassName ?? '--';

        $resArr["data"][] = $reportArray;
    }
}

echo json_encode($resArr, JSON_UNESCAPED_UNICODE);


