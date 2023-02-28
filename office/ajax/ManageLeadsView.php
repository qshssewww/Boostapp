<?php
require_once '../../app/init.php';
require_once "../Classes/PipelineCategory.php";
require_once "../Classes/LeadStatus.php";
require_once "../Classes/LeadSource.php";
require_once "../Classes/Pipeline.php";
require_once "../Classes/Settings.php";
require_once "../Classes/Clientcrm.php";
require_once "../Classes/ClassStudioAct.php";
header('Content-Type: application/json');
const ERROR = 0;
const SUCCESS = 1;
if (Auth::guest()) exit;
$companyNum = Company::getInstance()->__get('CompanyNum');
if (!empty($_POST["fun"])) {

    switch ($_POST["fun"]) {
        case "GetDataManageLeadsPage":
            if (!isset($_POST["PipeId"]) || !is_numeric($_POST["PipeId"]))
                echo json_encode(array("Message" => "PipeId is required", "Status" => ERROR));
            else {
                $PipeLineCategory = new PipelineCategory($_POST["PipeId"]);
                $MaxRecord = $PipeLineCategory->__get('MaxRecord');
                $Limit = $MaxRecord && $MaxRecord < 30 ? $MaxRecord : 30;
                $AgentId = (isset($_POST['AgentId']) && is_numeric($_POST["AgentId"])) ? $_POST['AgentId'] : null;
                $Settings = new Settings($companyNum);
                $VoiceCenterToken = $Settings->__get('VoiceCenterToken');
                $LeadStatus = new LeadStatus();
                $CanAddLead = Auth::userCan('51');
                $CanEditAndAddPipeLine = Auth::userCan('48');
                try {
                    $GetSuccessId = $LeadStatus->getLeadStatusByPipeIdByAct($companyNum, $_POST["PipeId"], '1');
                    $GetFailsId = $LeadStatus->getLeadStatusByPipeIdByAct($companyNum, $_POST["PipeId"], '2');
                    $GetNoneFailsId = $LeadStatus->getLeadStatusByPipeIdByAct($companyNum, $_POST["PipeId"], '3');
                    $leadStatuses = $LeadStatus->getActiveLeadStatusesByPipeId($companyNum, $_POST["PipeId"]);

                    foreach ($leadStatuses as $leadStatus) {
                        $leadStatus->count = (new Pipeline())->getCountPipeLineByPipeId($companyNum, $leadStatus->id, $AgentId);
                        $PipeLines = (new Pipeline())->getPipeLineByPipeId($companyNum, $leadStatus->id, $AgentId, $Limit);
                        foreach ($PipeLines as $PipeLine) {
                            $PipeLine->CheckNotes = (new Clientcrm())->countClientCrm($companyNum, $PipeLine->ClientId);
                            $CheckClass = (new ClassStudioAct())->getStatusTestClassByClientId($companyNum, $PipeLine->ClientId);
                            $PipeLine->CheckClassId = $CheckClass && $CheckClass->id ? $CheckClass->id : '';
                            $PipeLine->CheckClassStatus = $CheckClass && $CheckClass->id ? $CheckClass->Status : '';
                        }
                        $leadStatus->pipeLines = $PipeLines;
                    }

                    echo json_encode(array("response" => array(
                        "LeadStatuses" => $leadStatuses,
                        "VoiceCenterToken" => $VoiceCenterToken,
                        "GetSuccess" => $GetSuccessId,
                        "GetFails" => $GetFailsId,
                        "GetNoneFails" => $GetNoneFailsId,
                        "CanAddLead" => $CanAddLead,
                        "CanEditAndAddPipeLine" => $CanEditAndAddPipeLine,
                    ), "Status" => SUCCESS));

                } catch (Exception $e) {
                    echo json_encode(array("Message" => $e->getMessage(), "Status" => ERROR));
                }
            }
            break;

        default:
            echo json_encode(array("Message" => "No Found Function", "Status" => ERROR));
            break;
    }

} else {
    echo json_encode(array("Message" => "No Function", "Status" => ERROR));
}