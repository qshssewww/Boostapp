<?php require_once '../../app/init.php'; ?>

<?php

$ClientId = $_POST['ClientId'];

$Month = $_POST['NewMonth'];

$Year = mb_substr($_POST['NewYear'], 2);

$Tokef = $Year.''.$Month;
	
	
	
$CreditCards = DB::table('token')->where('ClientId', $ClientId)->first();

if (@$CreditCards->id==''){}
else {
	
		    $Task = DB::table('token')
           ->where('id', $CreditCards->id)
           ->update(array('Tokef' => $Tokef));	
	
	
}

echo 'Close=0&Status=1&CCode=0&ClientId='.$ClientId;
?>

               