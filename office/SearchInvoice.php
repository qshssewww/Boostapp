<?php

require_once '../app/init.php';
$CompanyNum = Auth::user()->CompanyNum;

$answer = [];

if (isset($_GET['q']) && (!empty($_GET['q']))) {

$q = '%' . $_GET['q'] . '%';


$Items = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('Company', 'LIKE', $q)->where('StatusRefound', '!=', '1')->Orwhere('id', '=', $_GET['q'])->where('CompanyNum', '=', $CompanyNum)->where('StatusRefound', '!=', '1')->Orwhere('Mobile', '=', $_GET['q'])->where('StatusRefound', '!=', '1')->Orwhere('ClientId', '=', $_GET['q'])->where('StatusRefound', '!=', '1')->where('CompanyNum', '=', $CompanyNum)->get();
$ItemCount = count($Items);

	
foreach ($Items as $Item) {


$answer[] = [ 'id' => $Item->id, 'text' => $Item->Company.' :: '.$Item->Mobile.' :: ח-ן:'.$Item->id.' :: '.$Item->Amount.' ₪' ];

} 
	
} else $answer[] = [ 'id' => 0, 'text' => 'לא נמצאו תוצאות...' ];

?>

{
  "total_count": <?php echo $ItemCount; ?>,
  "incomplete_results": false,
  "items": <?php echo json_encode($answer); ?>
}