<?php
require_once '../app/initcron.php';
require_once 'Classes/CoronaHealthCheck.php';
if (Auth::userCan('138')) {
    header('Content-Type: application/json; charset=utf-8');
    if (Auth::guest()){
        exit;
    }

    $CompanyNum = Auth::user()->CompanyNum;
    $UserId = Auth::user()->id;

    $corona = new CoronaHealthCheck();
    $coronaArr = $corona->getCoronaCheck($CompanyNum);
    $data = array(
        "data" => array()
    );
    foreach ($coronaArr as $cor){
        $arrData = array();
        $coronaCheck = "";
        if(empty($cor->__get("client"))) {
            continue;
        }
        if ($cor->__get("classstudio_act")->coronaStmt == 1){
            $coronaCheck = '<span class="text-primary"><i class="fal fa-head-side-mask" title="מילא הצהרת קורונה"></i> יש</span>';
        }
        else {
            $coronaCheck = '<span class="text-danger"><i class="fal fa-virus" title="לא מילא הצהרת קורונה"></i> אין</span>';
        }
        $arrData[0] = "";
        $arrData[1] = $cor->__get("client")->CompanyName ?? '';
        $arrData[2] = $cor->__get("client")->BrandName;
        $arrData[3] = $cor->__get("client")->ContactMobile;
        $arrData[4] = $cor->__get("client")->Email;
        $arrData[5] = $cor->__get("classstudio_act")->ClassName;
        $arrData[6] = date("d/m/Y" ,strtotime($cor->__get("classstudio_act")->ClassDate));
        $arrData[7] = $cor->__get("classstudio_act")->ClassStartTime;
        $arrData[8] = date("d/m/Y H:i", strtotime($cor->__get("date")));
        $arrData[9] = $coronaCheck;

        array_push($data["data"],$arrData);
    }
    echo json_encode($data,JSON_UNESCAPED_UNICODE);

}