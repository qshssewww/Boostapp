<?php
require_once '../../app/init.php';
require_once "../Classes/PipelineCategory.php";
require_once "../Classes/LeadStatus.php";
require_once "../Classes/LeadSource.php";
require_once "../Classes/Brand.php";
require_once "../Classes/FBPipelineSettings.php";


header('Content-Type: application/json');
define('ERROR', 0);
define( 'SUCCESS', 1);
$LeadSource = new LeadSource();

if (Auth::guest()) exit;

$companyNum = Company::getInstance()->__get('CompanyNum');

if(!empty($_POST["fun"])) {

    switch ($_POST["fun"]) {

        case "GetPipeLineCategories":
            $res = PipelineCategory::getAllPipelineCategories($companyNum);
            if(!$res || count($res) == 0){
                echo json_encode(array("Message" => "PipeLine Not Found", "Status" => ERROR));
            } else {
                echo json_encode(array("response" => $res, "Status" => SUCCESS));
            }
            break;

        case "GetDataPipeLine":
            if (!isset($_POST["PipeId"]) || !is_numeric($_POST["PipeId"]))
                echo json_encode(array("Message" => "PipeId is required", "Status" => ERROR));
            else {
                $pipeLineCategory = PipelineCategory::getPipeLineById($companyNum, $_POST["PipeId"]);
                if (!$pipeLineCategory)
                    echo json_encode(array("Message" => "PipeLineCategory Not Exists", "Status" => ERROR));
                else {
                    $leadStatus = LeadStatus::getAllLeadStatusesByPipeId($companyNum, $_POST["PipeId"]);
                    if(!$leadStatus || count($leadStatus) < 4) // count need > 4, for 3 must leadStatus (success, failure, not_relevant) and least one regular leadStatus
                        echo json_encode(array("Message" => "LeadStatus of this PipeLine Not Exist", "Status" => ERROR));
                    else {
                        $response = array("PipeLineCategoryTitle" => $pipeLineCategory->Title, "PipeLineCategoryId" => $pipeLineCategory->id, "LeadStatus" => $leadStatus);
                        echo json_encode(array("response" => $response, "Status" => SUCCESS));
                    }
                }
            }
            break;
        case "getLeadStatuses":
            if (!isset($_POST["PipeId"]) || !is_numeric($_POST["PipeId"]))
                echo json_encode(array("Message" => "PipeId is required", "Status" => ERROR));
            else {
                $leadStatus = LeadStatus::getAllLeadStatusesByPipeId($companyNum, $_POST["PipeId"]);
                if(!$leadStatus || count($leadStatus) < 4) // count need > 4, for 3 must leadStatus (success, failure, not_relevant) and least one regular leadStatus
                    echo json_encode(array("Message" => "LeadStatus of this PipeLine Not Exist", "Status" => ERROR));
                else {
                    echo json_encode(array("response" => array("LeadStatus" => $leadStatus), "Status" => SUCCESS));
                }
            }
            break;

        case "GetLeadSources":
            $leadSources = $LeadSource->getAllLeadSources($companyNum);
            echo json_encode(array("LeadSources" => $leadSources, "Status" => SUCCESS));
            break;

        case "AddLeadSource":
            if (!isset($_POST["Title"]))
                echo json_encode(array("Message" => "Title is required", "Status" => ERROR));
            else {
                $dataToSave = array('CompanyNum' => $companyNum, 'Title' => $_POST["Title"]);
                $validator = Validator::make($dataToSave, LeadSource::$CreateRules);
                if ($validator->passes()) {
                    $res = new LeadSource($dataToSave);
                    $res->save();
                    echo json_encode(array("response" => $res->toArray(), "Status" => SUCCESS));
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;

        case "UpdateLeadSource":
            if (!isset($_POST["id"]) || !is_numeric($_POST["id"]))
                echo json_encode(array("Message" => "Id Not Valid", "Status" => ERROR));
            elseif((!isset($_POST["Title"]) && !isset($_POST["Status"])))
                echo json_encode(array("Message" => "Title or Status required to update", "Status" => ERROR));
            else {
                $keyData = (isset($_POST["Title"])) ? 'Title' : 'Status';
                $validator = Validator::make(array('id' => $_POST['id'], $keyData => $_POST[$keyData]), LeadSource::$updateRules);
                if ($validator->passes()) {
                    $LeadSource = $LeadSource::find($_POST["id"]);
                    if (!$LeadSource) {
                        echo json_encode(array("Message" => "Not Found Lead Source To Update", "Status" => ERROR));
                    } else {
                        $LeadSource[$keyData] = $_POST[$keyData];
                        $LeadSource->save();
                        echo json_encode(array("response" => $LeadSource->toArray(), "Status" => SUCCESS));
                    }
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;

        case "UpdateLeadStatus":
            if (!isset($_POST["id"]) || !is_numeric($_POST["id"]))
                echo json_encode(array("Message" => "Id Not Valid", "Status" => ERROR));
            elseif(!isset($_POST["PipeId"]) || !is_numeric($_POST["PipeId"]))
                echo json_encode(array("Message" => "PipeId Not Valid", "Status" => ERROR));
            elseif((!isset($_POST["Title"]) && !isset($_POST["Status"])))
                echo json_encode(array("Message" => "Title or Status required to update", "Status" => ERROR));
            else {
                $keyData = (isset($_POST["Title"])) ? 'Title' : 'Status';
                $validator = Validator::make(array('id' => $_POST['id'], $keyData => $_POST[$keyData]), LeadStatus::$updateRules);
                if (!$validator->passes()) {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                } else {
                    $canHide = true;

                    if($keyData == 'Status' && $_POST[$keyData] == '1'){
                        // If you want to update to inactive status, Check that there is another active status, If not found, can not be updated.
                        $countLeadStatusActive = (new LeadStatus())->getCountActiveLeadStatusByPipeId($companyNum, $_POST["PipeId"]);
                        if($countLeadStatusActive <= 1){
                            $canHide = false;
                            echo json_encode(array("Message" => "Mast minimum one leas status active", "Status" => ERROR));
                        }
                    }
                    if($canHide) {
                        $leadStatus = new LeadStatus($_POST["id"]);
                        if (!$leadStatus->__get("id") || $leadStatus->__get("PipeId") != $_POST["PipeId"] || $leadStatus->__get("CompanyNum") != $companyNum) {
                            echo json_encode(array("Message" => "Not Found Lead Status To Update", "Status" => ERROR));
                        } else {
                            $leadStatus->__set($keyData, $_POST[$keyData]);
                            $leadStatus->update();
                            echo json_encode(array("response" => $leadStatus->createArrayFromObj(), "Status" => SUCCESS));
                        }
                    }
                }

            }
            break;
        case "UpdateSortLeadStatus":
            if(!isset($_POST["PipeId"]) || !is_numeric($_POST["PipeId"]))
                echo json_encode(array("Message" => "Id Not Valid", "Status" => ERROR));
            elseif (!isset($_POST["leadStatusIdsArray"]))
                echo json_encode(array("Message" => "Ids Array is required", "Status" => ERROR));
            elseif(!isset($_POST["sortStart"]))
                echo json_encode(array("Message" => "sortStart is required", "Status" => ERROR));
            else {
                $updateLeadStatusIdsArray = $_POST['leadStatusIdsArray'];
                $PipeId = $_POST['PipeId'];

                $listingCounter = $_POST["sortStart"];
                foreach ($updateLeadStatusIdsArray as $leadStatusId) {

                    $leadStatus = new LeadStatus($leadStatusId);
                    if ($leadStatus->__get("id") && $leadStatus->__get("PipeId") == $_POST["PipeId"] && $leadStatus->__get("CompanyNum") == $companyNum) {
                        $leadStatus->__set('Sort', $listingCounter);
                        $leadStatus->update();
                    }
                    $listingCounter = $listingCounter + 1;
                }
                echo json_encode(array("Status" => SUCCESS));
            }
            break;

        case "AddLeadStatus":
            if(!isset($_POST["PipeId"]) || !is_numeric($_POST["PipeId"]))
                echo json_encode(array("Message" => "PipeId Not Valid", "Status" => ERROR));
            elseif(!isset($_POST["Title"]))
                echo json_encode(array("Message" => "Title is required", "Status" => ERROR));
            else {
                $lastSort = (new LeadStatus())->getLastSortNum($companyNum, $_POST["PipeId"]);
                $dataToSave = array('CompanyNum' => $companyNum, 'PipeId' => $_POST["PipeId"],'Title' => $_POST["Title"], 'Sort' => $lastSort + 1);
                $validator = Validator::make($dataToSave, LeadStatus::$CreateRules);
                if ($validator->passes()) {
                    $newId = LeadStatus::insert_into_table($dataToSave);
                    $res = new LeadStatus($newId);
                    echo json_encode(array("response" => $res->createArrayFromObj(), "Status" => SUCCESS));
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;
        case "UpdatePipeLineCategory":
            if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
                echo json_encode(array("Message" => "Id Not Valid", "Status" => ERROR));
            }
            elseif((!isset($_POST["Title"]) && !isset($_POST["Status"]))) {
                echo json_encode(array("Message" => "Title or Status required to update", "Status" => ERROR));
            }
            else {
                $keyData = (isset($_POST["Title"])) ? 'Title' : 'Status';
                $validator = Validator::make(array('id' => $_POST['id'], $keyData => $_POST[$keyData]), PipelineCategory::$updateRules);

                if (!$validator->passes()) {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                } else {
                    $pipeLineCategory = new PipelineCategory($_POST["id"]);
                    if (!$pipeLineCategory->__get("id") || $pipeLineCategory->__get("CompanyNum") != $companyNum) {
                        echo json_encode(array("Message" => "Not Found Lead Status To Update", "Status" => ERROR));
                    } else if($keyData == 'Status' && $pipeLineCategory->Act == 1) {
                        echo json_encode(array("response" => "cannot update main status", "Status" => ERROR));
                    } else {
                        $pipeLineCategory->__set($keyData, $_POST[$keyData]);
                        $pipeLineCategory->update();
                        echo json_encode(array("response" => $pipeLineCategory->createArrayFromObj(), "Status" => SUCCESS));
                    }
                }

            }
            break;

        case "AddPipeLineCategory":
            if (empty($_POST["Title"]))
                echo json_encode(array("Message" => "Title is required", "Status" => ERROR));
            elseif (empty($_POST["leadStatusArray"])
                || empty($_POST["leadStatusArray"][0]) || !isset($_POST["leadStatusArray"][0]["Title"])) // check if there is not at least one lead status, return error
                echo json_encode(array("Message" => "leadStatusArray is required", "Status" => ERROR));
            else {
                $dataPipeLine = array("Title" => $_POST["Title"], "CompanyNum" => $companyNum);
                $validator = Validator::make($dataPipeLine, PipelineCategory::$CreateRules);
                if (!$validator->passes()) {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                } else {
                    foreach ($_POST["leadStatusArray"] as $leadStatus) {
                        $validator = Validator::make($leadStatus, array('Title' => 'required|min:1|max:100', 'Status' => 'integer|between:0,1'));
                        if (!$validator->passes()) {
                            echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                            exit();
                        }
                    }
                    try {
                        $pipeLineCategoryId = PipelineCategory::insert_into_table($dataPipeLine);
                        $pipeLineCategory = new PipelineCategory($pipeLineCategoryId);
                        $sort = 0;

                        foreach ($_POST["leadStatusArray"] as $leadStatus) {
                            $dataLeadStatus = array(
                                'CompanyNum' => $companyNum,
                                'PipeId' => $pipeLineCategoryId,
                                'Title' => $leadStatus["Title"],
                                'Status' => $leadStatus["Status"] ?? 0,
                                'Sort' => $sort
                            );
                            $newId = LeadStatus::insert_into_table($dataLeadStatus);
                            if ($newId) $sort++;
                        }

                        $dataLeadStatus = array('CompanyNum' => $companyNum, 'PipeId' => $pipeLineCategoryId, 'Title' => lang('success'), 'Status' => '1', 'Act' => '1', 'Sort' => $sort);
                        $newId = LeadStatus::insert_into_table($dataLeadStatus);
                        if ($newId) $sort++;

                        $dataLeadStatus = array('CompanyNum' => $companyNum, 'PipeId' => $pipeLineCategoryId, 'Title' => lang('failure'), 'Status' => '1', 'Act' => '2', 'Sort' => $sort);
                        $newId = LeadStatus::insert_into_table($dataLeadStatus);
                        if ($newId) $sort++;

                        $dataLeadStatus = array('CompanyNum' => $companyNum, 'PipeId' => $pipeLineCategoryId, 'Title' => lang('not_relevant'), 'Status' => '1', 'Act' => '3', 'Sort' => $sort);
                        $newId = LeadStatus::insert_into_table($dataLeadStatus);

                        echo json_encode(array("response" => $pipeLineCategory->createArrayFromObj(), "Status" => SUCCESS));

                    } catch (Exception $e) {
                        if(isset($pipeLineCategoryId)){
                            $pipeLineCategory = new PipelineCategory($pipeLineCategoryId);
                            if($pipeLineCategory->__get('id')) {
                                (new LeadStatus())->deleteAllLeadStatusByPipeId($pipeLineCategory->__get('id'));
                                $pipeLineCategory->delete();
                            }
                        }
                        echo json_encode(array("response" => $e->getMessage(), "Status" => ERORR));
                    }
                }
            }
            break;

        case "facebook/page/register":
            unset($_POST['fun']);

            $ch = curl_init('https://api.boostapp.co.il/facebook/page/register');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            curl_setopt($ch, CURLOPT_VERBOSE, true);

            // // execute!
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // // close the connection, release resources used
            // curl_close($ch);
            if ($response === FALSE) {
                printf("cUrl error (#%d): %s<br>\n", curl_errno($handle),
                    htmlspecialchars(curl_error($handle)));
                http_response_code(500);
            } else {
                http_response_code($http_code);
                // header('Access-Control-Allow-Origin: *');
                header('Content-type: application/json');
                echo $response;
            }
            break;

        case "getBranches":
            break;
        case "getDataForFacebook":
            // get pipeLineCategoris:
            $dataPipe = PipelineCategory::getPipelineCategoriesWithLeadStatus($companyNum);
            $PipelineCategory = [];
            $tmpId = -1;
            foreach ($dataPipe as $row){
                if($tmpId != $row->pcId){
                    $PipelineCategory[] = array(
                        'id'=> $row->pcId,
                        'name' => $row->pcName,
                        'status' => $row->pcStatus,
                        'default' => $row->pcDefault == '1',
                        'values' => []
                    );
                    $tmpId = $row->pcId;
                }
                $PipelineCategory[count($PipelineCategory) -1]['values'][] = array(
                    'id'=> $row->lsId,
                    'name'=> $row->lsName,
                    'status' => $row->lsStatus
                );
            }

            // get branches:
            $branches = (new Brand())->getNameBranches($companyNum);
            if (!$branches || count($branches) == 0) {
                $branches = array(array('id' => 0, 'name' => 'סניף ראשי', 'status' => '0'));
            }

            // get pages
            $pages = (new FBPipelineSettings())->getPagesFB($companyNum);
            if(!$pages) $pages = [];
            foreach ($pages as $page) {
                $page->settings = json_decode($page->settings);
            }

            echo json_encode(array(
                "response" =>
                    array(
                        "pipelines" => array("err" => false, "message" => 'List of pipeline categories', "items" => $PipelineCategory),
                        "branches" => $branches,
                        "pages" => $pages
                    ), "Status" => SUCCESS));
            break;

        case "getPagesFacebookFromBoostapp":
            // get pages
            $pages = (new FBPipelineSettings())->getPagesFB($companyNum);
            if(!$pages) $pages = [];
            foreach ($pages as $page) {
                $page->settings = json_decode($page->settings);
            }
            echo json_encode(array("response" =>$pages, "Status" => SUCCESS));
            break;

        default:
            echo json_encode(array("Message" => "No Found Function","Status" => ERORR));
            break;

    }

}
else{
    echo json_encode(array("Message" => "No Function","Status" => ERORR));
}