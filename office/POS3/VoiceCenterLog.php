<?php

        require_once '../../app/initcron.php';

        if (empty($_GET['u'])) redirect_to(App::url());
        $LogUserId = Auth::user()->id;
        $CompanyNum = Auth::user()->CompanyNum;

        $Settings = DB::table('settings')->where('CompanyNum', $CompanyNum)->first();
        $Supplier = DB::table('client')->where('id', $_GET['u'])->where('CompanyNum', $CompanyNum)->first();
        $UserInfo = DB::table('users')->where('id', $LogUserId)->where('CompanyNum', $CompanyNum)->first();


        $AgentNumber = @$UserInfo->AgentNumber;
		if (strlen($AgentNumber)<=9) {$AgentNumber = @$Settings->VoiceCenterNumber;}
		if ($AgentNumber == '') {$AgentNumber = @$Settings->VoiceCenterNumber;}
        $AgentExt = @$UserInfo->AgentEXT;
        $Mobile = @$Supplier->ContactMobile;
		$Token = $Settings->VoiceCenterToken;

$todate = '2018-10-03T23:59';
$fromdate = '2018-09-03T00:00';

        $Url = 'https://api1.voicenter.co.il/hub/cdr/?code='.$Token.'&format=JSON&todate='.$todate.'&fromdate='.$fromdate.'&phones='.$Mobile.'&fields=Date&fields=Type&Fields=DID&Fields=CallerNumber&Fields=CallerExtension&Fields=Duration&Fields=RecordURL&Fields=RepresentativeName&Fields=DialStatus';

        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$Url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$server_output = curl_exec ($ch);
		curl_close ($ch);

//		print($server_output);

$result = $server_output;
$json = json_decode($result, true);
print_r($json);

//foreach($json['CDR_LIST'] as $key => $customer) {
// echo $customer['DID']; 
//    
//    
//    
//    
//    
//    
//}

//echo $json['CDR_LIST'][1]['RepresentativeName'];
//echo $json['CDR_LIST'][1]['RecordURL'];




?>