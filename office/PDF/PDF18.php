<?php

require_once '../../app/init.php'; 

require_once '../PDF18/vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'default_font' => 'assistant'
]);


$mpdf->useLang = true;
$mpdf->SetDirectionality('rtl');
$mpdf->WriteHTML('<h1>שלום עולם!</h1> <bi>בל בלה בלנהב</bi>');


$mpdf->Output();
//==============================================================
//==============================================================
//==============================================================


?>
