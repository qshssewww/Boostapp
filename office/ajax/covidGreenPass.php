<?php

require_once '../../app/init.php';
require_once '../Classes/Company.php';
require_once '../Classes/Client.php';
if (Auth::check()) {

    if(isset($_POST["fun"])){
        $company = Company::getInstance();
        if($_POST["fun"] == "report"){
            $clientObj = new Client();
            $clients = $clientObj->getGreenPassReport($company->__get("CompanyNum"));
            $report = array(
                "data" => array()
            );
            foreach ($clients as $clientArr){
                $status = "";
                if($clientArr->greenPassStatus == 0){
                    $status = '<i class="far fa-badge fa-lg text-danger "><span class="d-none">'.lang('no_green_passport').'</span></i>';
                }
                elseif ($clientArr->greenPassStatus == 1){
                    $status = '<i class="far fa-badge-check fa-lg text-orange"><span class="d-none">'.lang('pending').'</span></i>';
                }
                elseif ($clientArr->greenPassStatus == 2){
                    $status = '<i class="fas fa-badge-check fa-lg text-success"><span class="d-none">'.lang('approved').'</span></i>';
                }
                $rep = array();
                $rep[0] = ($clientArr->CompanyName) ? '<a href="ClientProfile.php?u='.$clientArr->id.'">'.$clientArr->CompanyName.'</a>' : "";
                $rep[1] = ($clientArr->ContactMobile) ? $clientArr->ContactMobile : "";
                $rep[2] = ($clientArr->greenPassValid) ? $clientArr->greenPassValid : "";
                $rep[3] = '<div class="greenPassStatus cursor-pointer" data-id="'. $clientArr->id .'">'.$status.'</div>';
                $rep[4] = ($clientArr->greenActionDate) ? $clientArr->greenActionDate : "";
                array_push($report["data"],$rep);
            }
            echo json_encode($report,JSON_UNESCAPED_UNICODE);
        }
        else if($_POST["fun"] == "modal"){
            if (empty($_POST["client_id"]) || !is_numeric($_POST["client_id"])) {
                json_message("item_id must be numeric", false);
            }
            $clientObj = new Client();
            $Supplier = $clientObj->getRow($_POST["client_id"]);
            if ($Supplier) {
                if ($Supplier->greenPassStatus == 0) {
                    $greenPassText = lang('no_green_pass');
                    $cssClass = 'text-danger';
                    $badgeIcon = '<i class="far fa-badge fa-lg"></i>';
                    $modalIconStatus = '<i class="far fa-badge ' . $cssClass . '"></i>';
                } elseif ($Supplier->greenPassStatus == 1) {
                    $greenPassText = lang('green_pass_pending_notice');
                    $cssClass = 'text-orange';
                    $badgeIcon = '<i class="far fa-badge-check fa-lg"></i>';
                    $modalIconStatus = '<i class="far fa-badge-check ' . $cssClass . '"></i>';
                } else {
                    $greenPassText = lang('green_pass_confirmed_notice');
                    $cssClass = 'text-success';
                    $badgeIcon = '<i class="fas fa-badge-check fa-lg"></i>';
                    $modalIconStatus = '<i class="fas fa-badge-check ' . $cssClass . '"></i>';
                }
                $GetStudioStatus = DB::table('boostapplogin.studio')->where('ClientId', '=', $Supplier->id)->where('CompanyNum', '=', $CompanyNum)->first();
                if($GetStudioStatus) {
                    $CheckUserApp = DB::table('boostapplogin.users')->where('id', '=', $GetStudioStatus->UserId)->first();
                }
                include ("../greenPassModal.php");
            }
            else{
                echo("Client not Found");
            }
        }
    }
}

function hexcode($str) {
    $code = dechex(crc32($str));
    $code = substr($code, 0, 6);
    return $code;
}
