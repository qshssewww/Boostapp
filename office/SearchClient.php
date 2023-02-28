<?php

require_once '../app/initcron.php';
$CompanyNum = Auth::user()->CompanyNum;


$answer = [];

if (isset($_POST['q']) && (!empty($_POST['q']))) {

$q = '%' . $_POST['q'] . '%';


$Items = DB::table('client')
->where('CompanyName', 'LIKE', $q)->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '1')
->Orwhere('CompanyNum', '=', $CompanyNum)->where('ContactMobile', '=', $_POST['q'])->where('Status', '!=', '1')
->Orwhere('CompanyId', '=', $_POST['q'])->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '1')
->Orwhere('id', '=', $_POST['q'])->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '1')->get();
$ItemCount = count($Items);

	
foreach ($Items as $Item) {


$answer[] = [ 'id' => $Item->id, 'text' => $Item->CompanyName.' :: סלולרי: '.$Item->ContactMobile.' :: ת.ז.: '.$Item->CompanyId ];

} 
	
} else $answer[] = [ 'id' => 0, 'text' => 'לא נמצאו תוצאות...' ];


echo '{"results": '.json_encode($answer).'}';


?>
