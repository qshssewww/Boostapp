<?php

require_once '../../app/initcron.php';

header('Content-Type: text/html; charset=utf-8');

$CompanyNum = Auth::user()->CompanyNum;

$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();


$NewName = 'INI';
$NewNameData = 'BKMVDATA';

/// פתיחת תקיות
$DirName = 'OPENFRMT'; // שם הספרייה בשרת

$IdBiz = mb_substr($SettingsInfo->CompanyId, 0, 8); // מספר עוסק מורשה או ח.פ. ללא ספרת ביקרות

/// נתונים משתנים
$Year = date('y'); /// שנה דו ספרתי YY לפי תאריך הנפקת הקובץ
$DateTime = date('mdHi'); /// MMDDhhmm


$path1 = $DirName.'/'.$IdBiz.'.'.$Year;
$path2 = $DirName.'/'.$IdBiz.'.'.$Year.'/'.$DateTime;

if (!file_exists($path1)) {
    mkdir($DirName.'/'.$IdBiz.'.'.$Year, 0700);
}

if (!file_exists($path2)) {
    mkdir($DirName.'/'.$IdBiz.'.'.$Year.'/'.$DateTime, 0700);
}


$FileName = $DirName.'/'.$IdBiz.'.'.$Year.'/'.$DateTime.'/'.$NewName.'.txt';

$myfile = fopen($FileName, "w") or die('Cannot open file:  '.$FileName);



function mb_str_pad(
  $input,
  $pad_length,
  $pad_string=' ',
  $pad_style=STR_PAD_RIGHT,
  $encoding="UTF-8")
{
    return str_pad(
      $input,
      strlen($input)-mb_strlen($input,$encoding)+$pad_length,
      $pad_string,
      $pad_style);
}



$txt = '';
$txtdata = '';

$StartDate = '2018-03-01';
$EndDate = '2018-04-31';

/// מחולל מספר מזהה ראשי

$i = 0;
$Random = mt_rand(1,9);
do {
    $Random .= mt_rand(0, 9);
} while(++$i < 14);
$MainId =  $Random;

$txt .= "A000"; // קוד רשומה (4)
$Sapce2 = '';
$q = 0;
do {
    $Sapce2 .= ' ';
} while(++$q < 5);
$Sapces2 =  $Sapce2;

$txt .= $Sapces2; // שדה עתידני (5)

//// C100 סך רשומות מסוג כותרת מסמך
$DocsInfoC100 = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
//// D110 סך רשומות מסוג פרטי מסמך
$DocsInfoD110 = DB::table('docslist')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
//// D120 סך רשומות מסוג פרטי קבלה
$DocsInfoD120 = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
$DocsInfosB110 = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->groupBy('ClientId')->orderBy('id', 'ASC')->get();
$DocsInfoB110 = count($DocsInfosB110);
$TotalResomot = $DocsInfoC100+$DocsInfoD110+$DocsInfoD120+$DocsInfoB110+2;

$counts = mb_strlen($TotalResomot);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;

$txt .= $zeros.''.$TotalResomot; // סך כל הרשומות (15)


$txt .= $SettingsInfo->CompanyId; // מספר עוסק מורשה (9)
$txt .= $MainId; // מזהה ראשי (15)
$txt .= '&OF1.31&'; // קבוע מערכת (8)


$txt .= '12345678'; // מספר רישום תוכנה (8)

$SoftNameT = str_replace('"', "", '247 סופט');
$SoftNameT = str_replace("'", "", $SoftNameT);
$SoftNameT = str_replace(".", " ", $SoftNameT);
$SoftName = mb_strlen($SoftNameT,'UTF-8');

$txt .= mb_str_pad($SoftNameT, 20);	//// שם התוכנה 20

$VersioT = str_replace('"', "", 'מהדורה 2.0');
$VersioT = str_replace("'", "", $VersioT);
$Version = mb_strlen($VersioT,'UTF-8');

$txt .= mb_str_pad($VersioT, 20);	 // מהדורת התוכנה (20)
$txt .= '514329267'; // עוסק מורשה של יצרן התוכנה התוכנה (9)

$SoftT = str_replace('"', "", 'ב.קונקט מחשבים בע"מ');
$SoftT = str_replace("'", "", $SoftT);
$SoftT = str_replace(".", " ", $SoftT);
$Soft = mb_strlen($SoftT,'UTF-8');


$txt .= mb_str_pad($SoftT, 20); // שם יצרן התוכנה (20)
$txt .= '2'; // סוג תוכנה (1)

$Dirs = mb_strlen('C:/'.$DirName.'/'.$IdBiz.'.'.$Year.'/'.$DateTime);
$Sapce_dir = '';
$j = 0;
do {
    $Sapce_dir .= ' ';
} while(++$j < 50-$Dirs);
$SapcesDir =  $Sapce_dir;

$txt .=  'C:/'.$DirName.'/'.$IdBiz.'.'.$Year.'/'.$DateTime.''.$SapcesDir; // נתיב מיקום שמירת הקבצים (50)
$txt .=  '0'; // סוג הנהלת חשבונות (1)

$txt .=  '0'; // איזון חשבונאי (1)

if ($SettingsInfo->BusinessType=='3'){
$txt .=  $SettingsInfo->CompanyId; // מספר חברה ברשם החברות (במידה וחברה) (9)	
}
else {
$txt .=  '000000000'; // מספר חברה ברשם החברות (במידה וחברה) (9)
}

$txt .=  '000000000'; // תיק ניכויים (9)
$Sapce = '';
$q = 0;
do {
    $Sapce .= ' ';
} while(++$q < 10);
$Sapces =  $Sapce;

$txt .=  $Sapces; // שטח נתונים עתידי (10)

/// פרטי בית העסק

$NameT = str_replace('"', "", 'חברה לדוגמא');
$NameT = str_replace("'", "", $NameT);
$NameT = str_replace(".", " ", $NameT);
$Name = mb_strlen($NameT,'UTF-8');

$txt .= mb_str_pad($NameT, 50); // שם העסק (50)

$StreetT = str_replace('"', "", 'הבנאי');
$StreetT = str_replace("'", "", $StreetT);
$StreetT = str_replace(".", " ", $StreetT);
$Street = mb_strlen($StreetT,'UTF-8');

$txt .= mb_str_pad($StreetT, 50); // מען העסק רחוב (50)

$Num = mb_strlen('5');
$Sapce_num = '';
$j = 0;
do {
    $Sapce_num .= ' ';
} while(++$j < 10-$Num);
$SapcesNum =  $Sapce_num;


$txt .=  '5'.$SapcesNum; // מען העסק מספר בית (10)

$CityT = str_replace('"', "", 'אילת');
$CityT = str_replace("'", "", $CityT);
$CityT = str_replace(".", " ", $CityT);
$City = mb_strlen($CityT,'UTF-8');

$txt .= mb_str_pad($City, 30); // מען העסק עיר (30)

$posts = mb_strlen('88000');
$zeroP = '';
$k = 0;
do {
    $zeroP .= '0';
} while(++$k < 8-$posts);
$Post =  $zeroP;


$txt .=  $Post.'88000'; // מען העסק מיקוד (8)


$txt .= '    '; // שנת מס (4)

/// בחירת הלקוח (משתנה)
$txt .=  '20180101'; // תאריך התחלה (YYYYMMDD)
$txt .=  '20180218'; // תאריך סיום (YYYYMMDD)

/// קבוע
$txt .=  date('Ymd'); // תאריך תחילת התאריך
$txt .=  date('Hi'); // שעת התחלת התאריך 

/// שונות
$txt .=  '0'; // קוד שפה
$txt .=  '1'; // סט תווים

$winzips = mb_strlen('winzip');
$spacewinzip = '';
$k = 0;
do {
    $spacewinzip .= ' ';
} while(++$k < 20-$winzips);
$winzip =  $spacewinzip;

$txt .=  'winzip'.$winzip; // שם תוכנת כיווץ
$txt .=  'ILS'; // מטבע מוביל
$txt .=  '0'; // מידע על סניפים (1 במידה ויש סניפים)
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 46);
$Sapces1 =  $Sapce1;

$txt .=  $Sapces1; // שטח נתונים עתידי (46)


$txt .=  '
';  

//// C100 סך רשומות מסוג כותרת מסמך
$DocsInfoC100 = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();

$txt .=  'C100'; // קוד רשומה (4)

$TotalReshuma = $DocsInfoC100;
$counts = mb_strlen($TotalReshuma);

$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;

$txt .=  $zeros.$TotalReshuma; // סך רשומות (15)

$txt .=  '
';  

//// D110 סך רשומות מסוג פרטי מסמך
$DocsInfoD110 = DB::table('docslist')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();

$txt .=  'D110'; // קוד רשומה (4)

$TotalReshuma = $DocsInfoD110;
$counts = mb_strlen($TotalReshuma);

$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;

$txt .=  $zeros.$TotalReshuma; // סך רשומות (15)

$txt .=  '
';  

//// D120 סך רשומות מסוג פרטי קבלה
$DocsInfoD120 = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();

$txt .=  'D120'; // קוד רשומה (4)

$TotalReshuma = $DocsInfoD120;
$counts = mb_strlen($TotalReshuma);

$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;

$txt .=  $zeros.$TotalReshuma; // סך רשומות (15)

$txt .=  '
';  

//// B100 סך רשומות מסוג תנועות הנה"ח
$txt .=  'B100'; // קוד רשומה (4)

$TotalReshuma = '0';
$counts = mb_strlen($TotalReshuma);

$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;

$txt .=  $zeros.$TotalReshuma; // סך רשומות (15)

$txt .=  '
';  

//// B110 	סך רשומות מסוג חשבונות
$DocsInfosB110 = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->groupBy('ClientId')->orderBy('id', 'ASC')->get();
$DocsInfoB110 = count($DocsInfosB110);
$txt .=  'B110'; // קוד רשומה (4)

$TotalReshuma = $DocsInfoB110;
$counts = mb_strlen($TotalReshuma);

$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;

$txt .=  $zeros.$TotalReshuma; // סך רשומות (15)

$txt .=  '
';  

//// M100 סך רשומות מסוג פריטים במלאי
$txt .=  'M100'; // קוד רשומה (4)

$TotalReshuma = '0';
$counts = mb_strlen($TotalReshuma);

$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;

$txt .=  $zeros.$TotalReshuma; // סך רשומות (15)


$string = iconv("UTF-8", "Windows-1255", $txt);
fwrite($myfile, $string);
fclose($myfile);






///  הנפקת קובץ BKMVDATA.TXT

$RunNumber = '1';

$FileNameData = $DirName.'/'.$IdBiz.'.'.$Year.'/'.$DateTime.'/'.$NewNameData.'.txt';

$myfiledata = fopen($FileNameData, "w") or die('Cannot open file:  '.$FileNameData);


///// רשומת פתיחה

$txtdata .= 'A100'; /// קוד רשומה (4)

$counts = mb_strlen($RunNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 9-$counts);
$zeros =  $zero;

$txtdata .= $zeros.$RunNumber; /// מספר רץ
$txtdata .= $SettingsInfo->CompanyId; // מספר עוסק מורשה (9)
$txtdata .= $MainId; // מזהה ראשי (15)
$txtdata .= '&OF1.31&'; // קבוע מערכת (8)

$Sapce1= '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 50);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // שטח נתונים עתידי (50)

$txtdata .=  '
';  

//// כותרת מסמך

$DocsInfos = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
foreach ($DocsInfos as $DocsInfo) {
	
/// בדיקת סוג ממך
	
$DocTableInfo = DB::table('docstable')->where('CompanyNum', '=', $CompanyNum)->where('id','=',$DocsInfo->TypeDoc)->first();
$typecarteset = $DocTableInfo->TypeTitleSingle;
$TypeHeader = $DocTableInfo->TypeHeader;	
	
///  מבצע הפעולה   
$UserInfo = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id','=',$DocsInfo->UserId)->first();
    
    
$RunNumber += '1';
$txtdata .= 'C100'; /// קוד רשומה (4)
$counts = mb_strlen($RunNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 9-$counts);
$zeros =  $zero;

$txtdata .= $zeros.$RunNumber; /// מספר רשומה רץ 9
$txtdata .= $SettingsInfo->CompanyId; // מספר עוסק מורשה (9)
$txtdata .= $TypeHeader; /// סוג מסמך 3

$counts = mb_strlen($DocsInfo->TypeNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 20-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$DocsInfo->TypeNumber; //// מספר מסמך
$txtdata .= with(new DateTime($DocsInfo->Dates))->format('Ymd'); //// תאריך הפקה YYYYMMDD
$txtdata .= with(new DateTime($DocsInfo->Dates))->format('Hi'); //// שעת הפקה HHMM
    
$ClientNameT = str_replace('"', "", $DocsInfo->Company);
$ClientNameT = str_replace("'", "", $ClientNameT);
$ClientNameT = str_replace(".", " ", $ClientNameT);
$ClientName = mb_strlen($ClientNameT,'UTF-8'); 
if ($ClientName>50){
$txtdata .= mb_substr($ClientNameT, 0, 50);
}
else {      
$txtdata .= mb_str_pad($ClientNameT, 50); // שם לקוח/ספק
}
    
$Street = str_replace('"', "", $DocsInfo->Street);
$Street = str_replace("'", "", $Street);
$Street = str_replace(".", " ", $Street);
$StreetT = mb_strlen($Street,'UTF-8'); 
if ($StreetT>50){
$txtdata .= mb_substr($Street, 0, 50);
}
else {      
$txtdata .= mb_str_pad($Street, 50); // רחוב
}
    
$StreetNumber = str_replace('"', "", $DocsInfo->Number);
$StreetNumber = str_replace("'", "", $StreetNumber);
$StreetNumber = str_replace(".", " ", $StreetNumber);
$StreetNumberT = mb_strlen($StreetNumber,'UTF-8');  
if ($StreetNumberT>10){
$txtdata .= mb_substr($StreetNumber, 0, 10);
}
else {      
$txtdata .= mb_str_pad($StreetNumber, 10); // מספר בית    
}
    
$StreetCity = str_replace('"', "", $DocsInfo->City);
$StreetCity = str_replace("'", "", $StreetCity);
$StreetCity = str_replace(".", " ", $StreetCity);
$StreetCityT = mb_strlen($StreetCity,'UTF-8');  
if ($StreetCityT>30){
$txtdata .= mb_substr($StreetCity, 0, 30);
}
else {     
$txtdata .= mb_str_pad($StreetCity, 30); // עיר       
}
    
$StreetPostCode = str_replace('"', "", $DocsInfo->PostCode);
$StreetPostCode = str_replace("'", "", $StreetPostCode);
$StreetPostCode = str_replace(".", " ", $StreetPostCode);
$StreetPostCodeT = mb_strlen($StreetPostCode,'UTF-8');
if ($StreetPostCodeT>8){
$txtdata .= mb_substr($StreetPostCode, 0, 8);
}
else {        
$txtdata .= mb_str_pad($StreetPostCode, 8); // עיר     
}
    
$StreetIL = str_replace('"', "", 'ישראל');
$StreetIL = str_replace("'", "", $StreetIL);
$StreetIL = str_replace(".", " ", $StreetIL);
$StreetILT = mb_strlen($StreetIL,'UTF-8'); 
if ($StreetILT>30){
$txtdata .= mb_substr($StreetIL, 0, 30);
}
else {      
$txtdata .= mb_str_pad($StreetIL, 30); // מדינה         
}
    
$txtdata .= 'IL'; // קוד מדינה    
 
   
$txtdata .= '000000000000000'; // טלפון   

    
$counts = mb_strlen($DocsInfo->CompanyId);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 9-$counts);
$zeros =  $zero;
$txtdata .= $DocsInfo->CompanyId; //// עוסק מורשה של הלקוח  
    
$txtdata .= with(new DateTime($DocsInfo->UserDate))->format('Ymd'); //// תאריך הפקה YYYYMMDD   
    
$Sapce1= '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 15);
$Sapces1 =  $Sapce1;

$txtdata .= '+00000000000000'; // סכום מטח    
    
$Sapce1= '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 3);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // קוד מטח    
    
$Amount = str_replace('-', "", $DocsInfo->Amount); 
$VatAmount = str_replace('-', "", $DocsInfo->VatAmount);  
$DiscountAmount = str_replace('-', "", $DocsInfo->DiscountAmount); 
$NikoyMasAmount = str_replace('-', "", $DocsInfo->NikoyMasAmount);       
   
/// סכום המסמך לפני הנחה    
  
$AmountMinusDiscount = (str_replace('-', "", $DocsInfo->DiscountAmount)+str_replace('-', "", $DocsInfo->Amount))-str_replace('-', "", $DocsInfo->VatAmount);    
$AmountMinusDiscount = number_format($AmountMinusDiscount, 2, '.', '');
$segments = explode('.', $AmountMinusDiscount);

$AmountMinusDiscount = array_shift ($segments);
$AmountMinusDiscounts = array_shift ($segments);    
    
$counts = mb_strlen($AmountMinusDiscount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;

$txtdata .=  '+'.$zeros.''.$AmountMinusDiscount.''.$AmountMinusDiscounts; // סכום מסמך לפני הנחות   תמיד בפלוס     
$DiscountAmount = number_format($DiscountAmount, 2, '.', ''); 
$segments = explode('.', $DiscountAmount);

$DiscountAmount = array_shift ($segments);
$DiscountAmounts = array_shift ($segments);  
    
    
$counts = mb_strlen($DiscountAmount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;

$txtdata .=  '-'.$zeros.''.$DiscountAmount.''.$DiscountAmounts; // הנחת מסמך תמיד במינוס      
    
/// סכום המסמך לאחר הנחות ללא מע"מ    
  
$AmountMinusVat = str_replace('-', "", $DocsInfo->Amount)-str_replace('-', "", $DocsInfo->VatAmount);    
$AmountMinusVat = number_format($AmountMinusVat, 2, '.', '');
$segments = explode('.', $AmountMinusVat);

$AmountMinusVat = array_shift ($segments);
$AmountMinusVats = array_shift ($segments);      
    
$counts = mb_strlen($AmountMinusVat);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;
   
$txtdata .=  '+'.$zeros.''.$AmountMinusVat.''.$AmountMinusVats; // סכום מסמך לאחר הנחות ללא מעמ   תמיד בפלוס       
$VatAmount = number_format($VatAmount, 2, '.', ''); 
$segments = explode('.', $VatAmount);

$VatAmount = array_shift ($segments);
$VatAmounts = array_shift ($segments);     
    
$counts = mb_strlen($VatAmount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;
  
$txtdata .=  '+'.$zeros.''.$VatAmount.''.$VatAmounts; // סכום המעמ 
  
$AmountMinusNikoyMasAmount = str_replace('-', "", $DocsInfo->Amount)-str_replace('-', "", $DocsInfo->NikoyMasAmount);    
$AmountMinusNikoyMasAmount = number_format($AmountMinusNikoyMasAmount, 2, '.', '');
$segments = explode('.', $AmountMinusNikoyMasAmount);

$AmountMinusNikoyMasAmount = array_shift ($segments);
$AmountMinusNikoyMasAmounts = array_shift ($segments);     
    
$counts = mb_strlen($AmountMinusNikoyMasAmount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;
     
$txtdata .=  '+'.$zeros.''.$AmountMinusNikoyMasAmount.''.$AmountMinusNikoyMasAmounts; // סה"כ הסמך ללא ניכוי במקור  
$NikoyMasAmount = number_format($NikoyMasAmount, 2, '.', ''); 
$segments = explode('.', $NikoyMasAmount);

$NikoyMasAmount = array_shift ($segments);
$NikoyMasAmounts = array_shift ($segments);      
    
$counts = mb_strlen($NikoyMasAmount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;
     
$txtdata .=  '+'.$zeros.''.$NikoyMasAmount.''.$NikoyMasAmounts; // סכום ניכוי מס במקור    
 
$counts = mb_strlen($DocsInfo->ClientId);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;    

$txtdata .= $zeros.''.$DocsInfo->ClientId; // מפתח לקוח   
    
$Sapce1= '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 10);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // שדה התאמה  
    
    
if ($DocsInfo->Cancel=='1'){
$Cancel = '1';    
}   
else {
$Cancel = ' ';    
}    
$txtdata .=  $Cancel; // מסמך מבוטל

$txtdata .= with(new DateTime($DocsInfo->UserDate))->format('Ymd'); //// תאריך המסמך YYYYMMDD       
 
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 7);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // מזהה סניף/ענף  
    
$UserName = str_replace('"', "", @$UserInfo->display_name);
$UserName = str_replace("'", "", $UserName);
$UserName = str_replace(".", " ", $UserName);
$UserNameT = mb_strlen($UserName,'UTF-8'); 
if ($UserNameT>9){
$txtdata .= mb_substr($UserName, 0, 9);
}
else {    
$txtdata .= mb_str_pad($UserName, 9); // שם מבצע הפעולה    
}
    
$counts = mb_strlen($DocsInfo->id);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 7-$counts);
$zeros =  $zero;
  
$txtdata .=  $zeros.''.$DocsInfo->id; // שדה מקשר לשורה 
    
    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 13);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // שטח לנתונים עתידיים      

$txtdata .='
';	

}


//// פרטי מסמך

$ListRowNumers = '1';

$DocsLists = DB::table('docslist')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
foreach ($DocsLists as $DocsList) {
	
/// בדיקת סוג מסמך
	
$DocTableInfo = DB::table('docstable')->where('CompanyNum', '=', $CompanyNum)->where('id','=',$DocsList->TypeDoc)->first();
$typecarteset = $DocTableInfo->TypeTitleSingle;
$TypeHeader = $DocTableInfo->TypeHeader;	
    
/// בדיקת סוג מסמך בסיס

$TypeDocBasis = $DocsList->TypeDocBasis;
$TypeDocBasisNumber = $DocsList->TypeDocBasisNumber;	    
	
$RunNumber += '1';
$txtdata .= 'D110'; /// קוד רשומה (4)
$counts = mb_strlen($RunNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 9-$counts);
$zeros =  $zero;

$txtdata .= $zeros.$RunNumber; /// מספר רשומה רץ 9
$txtdata .= $SettingsInfo->CompanyId; // מספר עוסק מורשה (9)
$txtdata .= $TypeHeader; /// סוג מסמך 3
$counts = mb_strlen($DocsList->TypeNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 20-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$DocsList->TypeNumber; //// מספר מסמך
        
$counts = mb_strlen($ListRowNumers);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 4-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$ListRowNumers; //// מספר שורת המסמך
    

$counts = mb_strlen($TypeDocBasis);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 3-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$TypeDocBasis; ////  סוג מסמך בסיס   
    
$counts = mb_strlen($TypeDocBasisNumber);
$zero = '';
$j = 0;
do {
    $zero .= ' ';
} while(++$j < 20-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$TypeDocBasisNumber; ////  מספר מסמך בסיס      
    
$txtdata .= '1'; ////  סוג עסקה         
 
$SKU = str_replace('"', "", $DocsList->ItemId);
$SKU = str_replace("'", "", $SKU);
$SKU = str_replace(".", " ", $SKU);
$SKUT = mb_strlen($SKU,'UTF-8'); 
if ($SKUT>20){
$txtdata .= mb_substr($SKU, 0, 20);
}
else {    
$txtdata .= mb_str_pad($SKU, 20); // מק"ט פנימי   
}    
 
$ItemName = str_replace('"', "", $DocsList->ItemName);
$ItemName = str_replace("'", "", $ItemName);
$ItemName = str_replace(".", " ", $ItemName);   
$ItemNameT = mb_strlen($ItemName,'UTF-8');    
if ($ItemNameT>30){
$txtdata .= mb_substr($ItemName, 0, 30);
}
else {    
$txtdata .= mb_str_pad($ItemName, 30); // תיאור פריט או שם שירות 
}      
    
 
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= '!';
} while(++$q < 50);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // שם היצרן    
    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= '!';
} while(++$q < 30);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // מספר סידורי של המוצר       

$Mida = 'יחידה';    
$counts = mb_strlen($Mida,'UTF-8');    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 20-$counts);
$Sapces1 =  $Sapce1;    
$txtdata .= $Mida.$Sapces1; // תיאור יחידת מידה    

$ItemQuantity = str_replace('-', "", $DocsList->ItemQuantity); 
$ItemQuantity = number_format($ItemQuantity, 4, '.', '');    
$segments = explode('.', $ItemQuantity);

$ItemQuantity = array_shift ($segments);
$ItemQuantitys = array_shift ($segments);
    
        
$counts = mb_strlen($ItemQuantity);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;

$txtdata .=  '+'.$zeros.''.$ItemQuantity.''.$ItemQuantitys; // כמות   
 
$AmountMinusVatAmount = str_replace('-', "", $DocsList->ItemPriceVat);    
$AmountMinusVatAmount = number_format($AmountMinusVatAmount, 2, '.', ''); 
$segments = explode('.', $AmountMinusVatAmount);

$AmountMinusVatAmount = array_shift ($segments);
$AmountMinusVatAmounts = array_shift ($segments);     
    
$counts = mb_strlen($AmountMinusVatAmount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;
   
$txtdata .=  '+'.$zeros.''.$AmountMinusVatAmount.''.$AmountMinusVatAmounts; // מחיר ליחידה ללא מעמ  

$DiscountAmount = str_replace('-', "", $DocsList->ItemDiscountAmount); 
    
$DiscountAmount = number_format($DiscountAmount, 2, '.', ''); 
$segments = explode('.', $DiscountAmount);

$DiscountAmount = array_shift ($segments);
$DiscountAmounts = array_shift ($segments);      
    
$counts = mb_strlen($DiscountAmount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;
  
$txtdata .=  '-'.$zeros.''.$DiscountAmount.''.$DiscountAmounts; // הנחת שורה  
    
$ItemtotalMinusVatAmount = str_replace('-', "", $DocsList->ItemPriceVatDiscount);    
$ItemtotalMinusVatAmount = number_format($ItemtotalMinusVatAmount, 2, '.', '');
$segments = explode('.', $ItemtotalMinusVatAmount);

$ItemtotalMinusVatAmount = array_shift ($segments);
$ItemtotalMinusVatAmounts = array_shift ($segments);     
    
$counts = mb_strlen($ItemtotalMinusVatAmount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;

$txtdata .=  '+'.$zeros.''.$ItemtotalMinusVatAmount.''.$ItemtotalMinusVatAmounts; // סה"כ שורה ללא מעמ    
    
    
$VatAmount = str_replace('-', "", $DocsList->Vat);    
$VatAmount = str_replace('.', "", $VatAmount); 
    
$counts = mb_strlen($VatAmount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 4-$counts);
$zeros =  $zero;
      
$txtdata .=  $VatAmount.''.$zeros; // שיעור המעמ    
    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 7);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // מזהה סניף/ענף  
    
    
$txtdata .= with(new DateTime($DocsList->UserDate))->format('Ymd'); //// תאריך המסמך YYYYMMDD           
    
$counts = mb_strlen($DocsList->DocsId);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 7-$counts);
$zeros =  $zero;
  
$txtdata .=  $zeros.''.$DocsList->DocsId; // שדה מקשר לשורה     
    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 7);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // מזהה סניף/ענף  למסמך בסיס    
    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 21);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // שטח עתידי
       
$txtdata .='
';	    

++ $ListRowNumers;    
    
}


//// פרטי תקבולי מסמך

$ListRowNumers = '1';

$DocsPayments = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
foreach ($DocsPayments as $DocsPayment) {
	
/// בדיקת סוג מסמך
	
$DocTableInfo = DB::table('docstable')->where('CompanyNum', '=', $CompanyNum)->where('id','=',$DocsPayment->TypeDoc)->first();
$typecarteset = $DocTableInfo->TypeTitleSingle;
$TypeHeader = $DocTableInfo->TypeHeader;	
    
$RunNumber += '1';
$txtdata .= 'D120'; /// קוד רשומה (4)
$counts = mb_strlen($RunNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 9-$counts);
$zeros =  $zero;

$txtdata .= $zeros.$RunNumber; /// מספר רשומה רץ 9
$txtdata .= $SettingsInfo->CompanyId; // מספר עוסק מורשה (9)
$txtdata .= $TypeHeader; /// סוג מסמך 3
$counts = mb_strlen($DocsPayment->TypeNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 20-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$DocsPayment->TypeNumber; //// מספר מסמך
        
$counts = mb_strlen($ListRowNumers);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 4-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$ListRowNumers; //// מספר שורת המסמך

if ($DocsPayment->TypePayment>'9'){
$txtdata .= '9'; //// סוג אמצעי תשלום     
}   
else {    
$txtdata .= $DocsPayment->TypePayment; //// סוג אמצעי תשלום    
}
    
if ($DocsPayment->TypePayment=='2'){
 
$counts = mb_strlen($DocsPayment->CheckBank);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 10-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$DocsPayment->CheckBank; //// מספר הבנק
    
$counts = mb_strlen($DocsPayment->CheckBankSnif);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 10-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$DocsPayment->CheckBankSnif; //// מספר הסניף
    
$counts = mb_strlen($DocsPayment->CheckBankCode);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$DocsPayment->CheckBankCode; //// מספר חשבון
    
$counts = mb_strlen($DocsPayment->CheckNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 10-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$DocsPayment->CheckNumber; //// מספר המחאה
        
}
else {

$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 45);
$zeros =  $zero;

$txtdata .= $zeros; ///  אין המחאה
  
}    
    
if ($DocsPayment->TypePayment=='2' || $DocsPayment->TypePayment=='3'){
 
$txtdata .= with(new DateTime($DocsPayment->CheckDate))->format('Ymd'); //// תאריך פרעון YYYYMMDD  
    
}
else {

$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 8);
$zeros =  $zero;

$txtdata .= $zeros; ///  לא מחאה ולא כרטיס אשראי    
    
}    
    
$RowAmount = str_replace('-', "", $DocsPayment->Amount)-str_replace('-', "", $DocsPayment->Excess);    
$RowAmount = number_format($RowAmount, 2, '.', '');
$segments = explode('.', $RowAmount);

$RowAmount = array_shift ($segments);
$RowAmounts = array_shift ($segments);     
    
$counts = mb_strlen($RowAmount);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 12-$counts);
$zeros =  $zero;
     
$txtdata .=  '+'.$zeros.''.$RowAmount.''.$RowAmounts; // סכום השורה 

if ($DocsPayment->TypePayment=='3'){

if ($DocsPayment->Bank=='1') {   
$txtdata .= '1'; ///  קוד חברה סולקת  
}
else if ($DocsPayment->Bank=='2') {
$txtdata .= '2'; ///  קוד חברה סולקת     
}   
else if ($DocsPayment->Bank=='3') {
$txtdata .= '3'; ///  קוד חברה סולקת     
}   
else if ($DocsPayment->Bank=='4') {
$txtdata .= '4'; ///  קוד חברה סולקת     
}   
else if ($DocsPayment->Bank=='6') {
$txtdata .= '6'; ///  קוד חברה סולקת     
}       
    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 20);
$Sapces1 =  $Sapce1;
$txtdata .= $Sapces1; //  שם כרטיס שנסלק
    
if ($DocsPayment->tashType=='1') {   
$txtdata .= '1'; ///  סוג עסקת אשראי 
}
else if ($DocsPayment->tashType=='7') {
$txtdata .= '2'; ///  סוג עסקת אשראי     
}  
else if ($DocsPayment->tashType=='6') {
$txtdata .= '3'; ///  סוג עסקת אשראי     
}  
else if ($DocsPayment->tashType=='2') {
$txtdata .= '4'; ///  סוג עסקת אשראי     
}  
else {
$txtdata .= '5'; ///  סוג עסקת אשראי     
}      
    
    
}
else {

$txtdata .= '0'; ///  לא כרטיס אשראי   
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 20);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // לא כרטיס אשראי    
$txtdata .= '0'; ///  לא כרטיס אשראי 
    
}    
    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 7);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // מזהה סניף/ענף  
    
    
$txtdata .= with(new DateTime($DocsPayment->UserDate))->format('Ymd'); //// תאריך המסמך YYYYMMDD      
    
$counts = mb_strlen($DocsPayment->DocsId);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 7-$counts);
$zeros =  $zero;

$txtdata .=  $zeros.''.$DocsPayment->DocsId; // שדה מקשר לשורה    

$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 60);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // שטח עתידני   
    
$txtdata .='
';	    

++ $ListRowNumers;    

}


  


//// חשבון בהנהלת חשבונות B110

$ListRowNumers = '1';

$DocsClients = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->groupBy('ClientId')->orderBy('id', 'ASC')->get();
foreach ($DocsClients as $DocsClient) {
	
/// בדיקת סוג מסמך
	
$ClientInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id','=',$DocsClient->ClientId)->first();

    
$RunNumber += '1';
$txtdata .= 'B110'; /// קוד רשומה (4)
$counts = mb_strlen($RunNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 9-$counts);
$zeros =  $zero;

$txtdata .= $zeros.$RunNumber; /// מספר רשומה רץ 9
$txtdata .= $SettingsInfo->CompanyId; // מספר עוסק מורשה (9)
$counts = mb_strlen($DocsClient->ClientId);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;
  
$txtdata .=  $zeros.''.$DocsClient->ClientId; // מפתח חשבון 
    
$ClientNameT = str_replace('"', "", $DocsClient->Company);
$ClientNameT = str_replace("'", "", $ClientNameT);
$ClientNameT = str_replace(".", " ", $ClientNameT);
$ClientName = mb_strlen($ClientNameT,'UTF-8'); 
if ($ClientName>50){
$txtdata .= mb_substr($ClientNameT, 0, 50);
}
else {      
$txtdata .= mb_str_pad($ClientNameT, 50); // שם לקוח/ספק
}

$counts = '0';    
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;
  
$txtdata .=  $zeros; // קוד מאזן בוחן   
    
$counts = '0';    
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 30-$counts);
$zeros =  $zero;
  
$txtdata .=  $zeros; // תיאור מאזן בוחן  
    
$Street = str_replace('"', "", $DocsClient->Street);
$Street = str_replace("'", "", $Street);
$Street = str_replace(".", " ", $Street);
$StreetT = mb_strlen($Street,'UTF-8'); 
if ($StreetT>50){
$txtdata .= mb_substr($Street, 0, 50);
}
else {      
$txtdata .= mb_str_pad($Street, 50); // רחוב
}
    
$StreetNumber = str_replace('"', "", $DocsClient->Number);
$StreetNumber = str_replace("'", "", $StreetNumber);
$StreetNumber = str_replace(".", " ", $StreetNumber);
$StreetNumberT = mb_strlen($StreetNumber,'UTF-8');  
if ($StreetNumberT>10){
$txtdata .= mb_substr($StreetNumber, 0, 10);
}
else {      
$txtdata .= mb_str_pad($StreetNumber, 10); // מספר בית    
}
    
$StreetCity = str_replace('"', "", $DocsClient->City);
$StreetCity = str_replace("'", "", $StreetCity);
$StreetCity = str_replace(".", " ", $StreetCity);
$StreetCityT = mb_strlen($StreetCity,'UTF-8');  
if ($StreetCityT>30){
$txtdata .= mb_substr($StreetCity, 0, 30);
}
else {     
$txtdata .= mb_str_pad($StreetCity, 30); // עיר       
}
    
$StreetPostCode = str_replace('"', "", $DocsClient->PostCode);
$StreetPostCode = str_replace("'", "", $StreetPostCode);
$StreetPostCode = str_replace(".", " ", $StreetPostCode);
$StreetPostCodeT = mb_strlen($StreetPostCode,'UTF-8');
if ($StreetPostCodeT>8){
$txtdata .= mb_substr($StreetPostCode, 0, 8);
}
else {        
$txtdata .= mb_str_pad($StreetPostCode, 8); // עיר     
}
    
$StreetIL = str_replace('"', "", 'ישראל');
$StreetIL = str_replace("'", "", $StreetIL);
$StreetIL = str_replace(".", " ", $StreetIL);
$StreetILT = mb_strlen($StreetIL,'UTF-8'); 
if ($StreetILT>30){
$txtdata .= mb_substr($StreetIL, 0, 30);
}
else {      
$txtdata .= mb_str_pad($StreetIL, 30); // מדינה         
}
    
$txtdata .= 'IL'; // קוד מדינה       
   
$counts = '0';    
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;
  
$txtdata .=  $zeros; // חשבון מרכז     
    
$counts = '0';    
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 14-$counts);
$zeros =  $zero;
  
$txtdata .=  '+'.$zeros; // יתרת החשבון       
 
$counts = '0';    
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 14-$counts);
$zeros =  $zero;
  
$txtdata .=  '+'.$zeros; // סה"כ חובה       
    
    
$counts = '0';    
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 14-$counts);
$zeros =  $zero;
  
$txtdata .=  '+'.$zeros; // סה"כ זכות       
    
  
$counts = '0';    
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 4-$counts);
$zeros =  $zero;
  
$txtdata .=  $zeros; // קוד בסיווג חשבונאי       
    

$counts = mb_strlen($DocsClient->CompanyId);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 9-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$DocsClient->CompanyId; //// עוסק מורשה של הלקוח     
 
    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 7);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // מזהה סניף/ענף  למסמך בסיס     

$counts = '0';    
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;
  
$txtdata .=  $zeros; // יתרת חשבון בתחילת החתך     
$txtdata .=  '!!!'; /// קוד מטבע מט"ח ביתרת חשבון בתחילת חתך    
    
$Sapce1 = '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 16);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // שטח עתידני   
    
$txtdata .='
';	    

++ $ListRowNumers;    

}    


//// רשומת סגירה

$RunNumber += '1';

$txtdata .= 'Z900'; /// קוד רשומה (4)

$counts = mb_strlen($RunNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 9-$counts);
$zeros =  $zero;

$txtdata .= $zeros.$RunNumber; /// מספר רץ
$txtdata .= $SettingsInfo->CompanyId; // מספר עוסק מורשה (9)
$txtdata .= $MainId; // מזהה ראשי (15)
$txtdata .= '&OF1.31&'; // קבוע מערכת (8)

$counts = mb_strlen($RunNumber);
$zero = '';
$j = 0;
do {
    $zero .= '0';
} while(++$j < 15-$counts);
$zeros =  $zero;
$txtdata .= $zeros.$RunNumber; // סך כל הרשומות כולל בקובץ  (15)


$Sapce1= '';
$q = 0;
do {
    $Sapce1 .= ' ';
} while(++$q < 50);
$Sapces1 =  $Sapce1;

$txtdata .=  $Sapces1; // שטח נתונים עתידי (50)



$string = iconv("UTF-8", "Windows-1255", $txtdata);
fwrite($myfiledata, $string);
fclose($myfiledata);


/// יצירת קובץ ZIP
$files = array('INI.txt', 'BKMVDATA.txt');
$zipname = $path2.'/BKMVDATA.zip';
$zip = new ZipArchive;
$zip->open($zipname, ZipArchive::CREATE);
foreach ($files as $file) {
  $zip->addFile($path2.'/'.$file, $file);
}
$zip->close();

?>