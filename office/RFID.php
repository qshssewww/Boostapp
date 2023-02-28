<?php

require_once '../app/init.php';
$CompanyNum = Auth::user()->CompanyNum;

$Barcode = $_REQUEST['C'];

$Barcode = $Barcode;


$Client = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('RFID', '=', $Barcode)->first();

@$ClientId = @$Client->id;

if (@$ClientId==''){

	header("Location: Client.php?Act=0");
	
}

else {

	header("Location: ClientProfile.php?u=$ClientId");
	
}

?>