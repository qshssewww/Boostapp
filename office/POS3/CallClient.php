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
        $Mobile = $Supplier->ContactMobile;
		$Token = $Settings->VoiceCenterToken;

        $Url = 'https://api.voicenter.co.il/ForwardDialer/click2call.aspx';	$params='code='.$Token.'&phone='.$AgentExt.'&phonecallerid='.$AgentNumber.'&target='.$Mobile.'&phoneautoanswer=true&record=true&format=json';

        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$Url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS ,$params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$server_output = curl_exec ($ch);
		curl_close ($ch);

		print($server_output);



        ///// לוג
        $Content = 'חייג ללקוח באמצעות המערכת'; 
        CreateLogMovement($Content, @$Supplier->id);




?>