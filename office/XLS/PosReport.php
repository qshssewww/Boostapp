<?php
header('Content-type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="file.xls"');

require_once '../../app/init.php';

/** Error reporting */
error_reporting(E_ALL);

/** Include path **/
ini_set('include_path', ini_get('include_path').';../Classes/');

/** PHPExcel */
include 'PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
include 'PHPExcel/Writer/Excel2007.php';

// Create new PHPExcel object
date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();
if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");

if (@$_REQUEST['Dates']==''){

$cMonth = $_REQUEST["month"];
$cYear = $_REQUEST["year"];
$Dates = $_REQUEST["year"].'-'.$_REQUEST["month"];
}

else {

$Dates = $_REQUEST['Dates'];
$cMonth = with(new DateTime($_REQUEST['Dates']))->format('m');
$cYear = with(new DateTime($_REQUEST['Dates']))->format('Y');	
	
}
 
$prev_year = $cYear;
$next_year = $cYear;
$prev_month = $cMonth-1;
$next_month = $cMonth+1;
 
if ($prev_month == 0 ) {
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13 ) {
    $next_month = 1;
    $next_year = $cYear + 1;
}

$StartDate = $cYear.'-'.$cMonth.'-01';
$EndDate = $cYear.'-'.$cMonth.'-'.date('t',strtotime($StartDate));


// Set properties
date('H:i:s') . " Set properties\n";
$objPHPExcel->getProperties()->setCreator("Yahalom");
$objPHPExcel->getProperties()->setLastModifiedBy("Yahalom");
$objPHPExcel->getProperties()->setTitle("כרטסת הנהלת חשבונות");
$objPHPExcel->getProperties()->setSubject("כרטסת הנהלת חשבונות");
$objPHPExcel->getProperties()->setDescription("כרטסת הנהלת חשבונות");

function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

$StyleTitle = array('font' => array('size' => 10,'bold' => true,'color' => array('rgb' => 'FFFFFF')));


$Type1 = "select * from positem where Status=0 ";


/// דוח X קופת חנות
$DocGets = DB::table('docs')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('TypeDoc', 'ASC')->orderBy('id', 'ASC')->get();
$DocsCount = count($DocGets);


$TotalIn = $DocsCount;
 
$Eshcol = '1';
$RowNum = '1';

$AddRowNum = '1';


// Add some data
$FileDates = date('d/m/y');
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->SetCellValue('A'.$RowNum, '#');
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$RowNum, 'סוג מסמך');
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$RowNum, 'מס׳ מסמך');
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$RowNum, 'שם הלקוח');
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$RowNum, 'תאריך ערך');
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$RowNum, 'תאריך המסמך');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$RowNum, 'ניכוי מס (0%)');
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$RowNum, 'סה״כ לפני מע״מ');
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$RowNum, 'מע״מ');
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$RowNum, 'סה"כ מע"מ');
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$RowNum, 'סה"כ כולל מע"מ');
$objPHPExcel->getActiveSheet()->SetCellValue('L'.$RowNum, 'סה"כ פטור מע״מ');

$i='1';
foreach ($DocGets as $DocGet) {

$AddRowNumNew = '1';
$TotalAmount = '0.00';


/// קופת לאומי קארד

$RowNum +=1;
$AddRowNumNew += 1;
$DocsTables = DB::table('docstable')->where('id','=',$DocGet->TypeDoc)->first();

$objPHPExcel->getActiveSheet()->SetCellValue('A'.$RowNum, @$Eshcol);
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$RowNum, @$DocsTables->TypeTitle);
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$RowNum, @$DocGet->TypeNumber);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$RowNum, @$DocGet->Company);
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$RowNum, with(new DateTime(@$DocGet->Dates))->format('d/m/Y'));
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$RowNum, with(new DateTime(@$DocGet->UserDate))->format('d/m/Y'));
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$RowNum, number_format(@$DocGet->NikoyMasAmount, 2));
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$RowNum, '11');
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$RowNum, number_format(@$DocGet->Vat, 2));
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$RowNum, number_format(@$DocGet->VatAmount, 2));
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$RowNum, number_format(@$DocGet->Amount, 2));
$objPHPExcel->getActiveSheet()->SetCellValue('L'.$RowNum, number_format(@$DocGet->Amount, 2));

$TotalAmount += '0';




++ $Eshcol;

}

$DocSum1Total = DB::table('docs')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('NikoyMasAmount');
$DocSum3Total = DB::table('docs')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('VatAmount');
$DocSum4Total = DB::table('docs')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');
$DocSum5Total = DB::table('docs')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');
$DocSum2Total = $DocSum5Total-$DocSum3Total;


$objPHPExcel->getActiveSheet()->SetCellValue('G'.($Eshcol+1), @$DocSum1Total);
$objPHPExcel->getActiveSheet()->SetCellValue('I'.($Eshcol+1), @$DocSum2Total);
$objPHPExcel->getActiveSheet()->SetCellValue('J'.($Eshcol+1), @$DocSum3Total);
$objPHPExcel->getActiveSheet()->SetCellValue('K'.($Eshcol+1), @$DocSum4Total);
$objPHPExcel->getActiveSheet()->SetCellValue('L'.($Eshcol+1), @$DocSum5Total);



cellColor('A1:L1', '24a3b8');
cellColor('A'.($Eshcol+1).':L'.$Eshcol+1, '24a3b8');



// Rename sheet
date('H:i:s') . " Rename sheet\n";
$objPHPExcel->getActiveSheet()->setTitle('דוח כרטסת');
$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($StyleTitle);

$objWriter = $objPHPExcel->getActiveSheet();
$cellIterator = $objWriter->getRowIterator()->current()->getCellIterator();
$cellIterator->setIterateOnlyExistingCells( true );
/** @var PHPExcel_Cell $cell */
foreach( $cellIterator as $cell ) {
        $objWriter->getColumnDimension( $cell->getColumn() )->setAutoSize( true );
}

		
// Save Excel 2007 file
$FileDate = date('d-m-Y');
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');

// Echo done
date('H:i:s') . " Done writing file.\r\n";


?>