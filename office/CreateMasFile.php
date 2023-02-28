<?php 
require_once '../app/init.php'; 

if (Auth::guest()) redirect_to(App::url());
$pageTitle = 'יצירת קובץ אחיד';
require_once '../app/views/headernew.php';

if (Auth::check()):
if (Auth::userCan('20')):


$CompanyNum = Auth::user()->CompanyNum;

CreateLogMovement('נכנס להפקת קובץ אחיד', '0');

if (!isset($_REQUEST["InputStartDate"])) $_REQUEST["InputStartDate"] = date("Y-m-d");
if (!isset($_REQUEST["InputEndDate"])) $_REQUEST["InputEndDate"] = date("Y-m-d");

if(!empty($_POST['MakeFile']) || !empty($_POST['MakeTrue'])) {

$StartDate = $_POST["InputStartDate"];
$EndDate = $_POST["InputEndDate"];

}

else {

$StartDate = date('Y-m-d');
$EndDate = date('Y-m-d');
	
}
 


$StartDateView = with(new DateTime($StartDate))->format('d/m/Y');
$EndDateView = with(new DateTime($EndDate))->format('d/m/Y');

	
$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();  	
	
?>

<link href="assets/css/fixstyle.css" rel="stylesheet">


<style>
.borderless td,
.borderless th {
    border: none;
}

.tab-content span {
    font-size: 14px;
}
</style>

<!-- <div class="row pb-3">

    <div class="col-md-6 col-sm-12 order-md-1">
        <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
            <?php //echo $DateTitleHeader; ?>
        </h3>
    </div>

    <div class="col-md-6 col-sm-12 order-md-4">
        <h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
            <div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
            	<i class="fas fa-window-restore fa-fw"></i> הנפקת קובץ אחיד
            </div>
        </h3>
    </div>


</div> -->
<div class="row" dir="rtl" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">
    <div class="col-12" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">


        <!-- <nav aria-label="breadcrumb" dir="rtl">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php" class="text-info">ראשי</a></li>
                <li class="breadcrumb-item active" aria-current="page">הפקת קובץ אחיד</li>
            </ol>
        </nav> -->

        <div class="row">

            <?php include("ReportsInc/SideMenu.php"); ?>

            <div class="col-md-10 col-sm-12 order-md-2">
                <div class="tab-content">


                    <div class="tab-pane fade show active text-right" role="tabpanel" id="user-overview">
                        <div class="card spacebottom">
                            <div class="card-header text-right" dir="rtl"><i class="fas fa-window-restore fa-fw"></i>
                                <strong>הפקת קובץ אחיד</strong></div>
                            <div class="card-body">


                                <?php if(!empty($_POST['MakeFile']) || !empty($_POST['MakeTrue'])) {} else { ?>
                                <div class="row" align="center">
                                    <div class="col-md-12 col-sm-12">

                                        <div style="width: 30%">
                                            <form name="ThisForm" method="post">

                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label">בית עסק</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" readonly class="form-control-plaintext"
                                                            id="staticEmail2"
                                                            value="<?php echo htmlentities($SettingsInfo->CompanyName); ?>">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="InputStartDate"
                                                        class="col-sm-2 col-form-label">מתאריך</label>
                                                    <div class="col-sm-10">
                                                        <input type="date" class="form-control" id="InputStartDate"
                                                            name="InputStartDate" value="<?php echo $StartDate; ?>"
                                                            placeholder="בחר תאריך">
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="InputEndDate" class="col-sm-2 col-form-label">עד
                                                        תאריך</label>
                                                    <div class="col-sm-10">
                                                        <input type="date" class="form-control" id="InputEndDate"
                                                            name="InputEndDate" value="<?php echo $EndDate; ?>"
                                                            min="<?php echo $StartDate; ?>" placeholder="בחר תאריך">
                                                    </div>
                                                </div>


                                                <div class="form-group row" align="left">
                                                    <div class="col-sm-10">
                                                        <button type="submit" class="btn btn-dark text-white"
                                                            id="MakeFile" name="MakeFile" value="MakeFile">הפק קובץ
                                                            אחיד</button> או <button type="submit"
                                                            class="btn btn-dark text-white" id="MakeTrue"
                                                            name="MakeTrue" value="MakeTrue">הפק פלט אימות
                                                            נתונים</button>
                                                    </div>
                                                </div>
                                            </form>

                                        </div>

                                    </div>
                                </div>

                                <hr>
                                <?php } /// הסתרת טופס בעת ביצוע ?>

                                <?php if(!empty($_POST['MakeFile'])) { 
	  
//// C100 סך רשומות מסוג כותרת מסמך
$DocsInfoC100 = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
//// D110 סך רשומות מסוג פרטי מסמך
$DocsInfoD110 = DB::table('docslist')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
//// D120 סך רשומות מסוג פרטי קבלה
$DocsInfoD120 = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();

$TotalResomot = $DocsInfoC100+$DocsInfoD110+$DocsInfoD120+2;	  

/// פתיחת תקיות
$DirName = 'Taxes/OPENFRMT'; // שם הספרייה בשרת

$IdBiz = mb_substr($SettingsInfo->CompanyId, 0, 8); // מספר עוסק מורשה או ח.פ. ללא ספרת ביקרות

/// נתונים משתנים
$Year = date('y'); /// שנה דו ספרתי YY לפי תאריך הנפקת הקובץ
$DateTime = date('mdHi'); /// MMDDhhmm
	  
$PathDir = $DirName.'/'.$IdBiz.'.'.$Year.'/'.$DateTime;	  	  
	  
?>

                                <div class="row" align="center" dir="rtl">
                                    <div class="col-md-12 col-sm-12">
                                        <span>הפקת קבצים במבנה אחיד עבור:</span>

                                        <table class="table borderless"
                                            style="font-size:12px; font-weight:bold; width: 30%" dir="rtl">
                                            <tbody>
                                                <tr>
                                                    <td>מס' עוסק מורשה / ח.פ : </td>
                                                    <td style="text-align: right; width: 70%;">
                                                        <?php echo $SettingsInfo->CompanyId; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>שם בית העסק : </td>
                                                    <td style="text-align: right; width: 70%;">
                                                        <?php echo $SettingsInfo->CompanyName; ?></td>
                                                </tr>

                                            </tbody>
                                        </table>

                                        <span>** ביצוע ממשק פתוח הסתיים בהצלחה **</span>

                                        <table class="table borderless"
                                            style="font-size:12px; font-weight:bold; width: 30%" dir="rtl">
                                            <tbody>
                                                <tr>
                                                    <td>הנתונים נשמרו בנתיב : </td>
                                                    <td colspan="3"
                                                        style="text-align: right; width: 40%; text-align: left; direction: ltr;">
                                                        <?php echo $PathDir; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>טווח תאריכים מתאריך : </td>
                                                    <td style="text-align: right; width: 20%;">
                                                        <?php echo $StartDateView; ?></td>
                                                    <td>ועד תאריך : </td>
                                                    <td style="text-align: right; width: 20%;">
                                                        <?php echo $EndDateView; ?></td>
                                                </tr>

                                            </tbody>
                                        </table>


                                    </div>
                                </div>




                                <div class="row" align="center" dir="rtl">
                                    <div class="col-md-12 col-sm-12">

                                        <span>פירוט סך סוגי הרשומות בקובץ BKMVDATA.TXT :</span>

                                        <table class="table table-hover"
                                            style="font-size:12px; font-weight:bold; width: 30%" dir="rtl">
                                            <thead>
                                                <tr class="bg-dark text-white">
                                                    <th style="text-align:right;" width="20%">קוד רשומה</th>
                                                    <th style="text-align:right;" width="60%">תיאור רשומה</th>
                                                    <th style="text-align:right;" width="20%">סך רשומות</th>
                                                </tr>
                                            </thead>
                                            <tbody>


                                                <tr>
                                                    <td>A100</td>
                                                    <td>רשומת פתיחה</td>
                                                    <td style="text-align: left;">1</td>
                                                </tr>
                                                <?php 
	if ($DocsInfoC100<='0'){} else { 
	?>
                                                <tr>
                                                    <td>C100</td>
                                                    <td>כותרת מסמך</td>
                                                    <td style="text-align: left;"><?php echo $DocsInfoC100; ?></td>
                                                </tr>
                                                <?php } 
	if ($DocsInfoD110<='0'){} else { 
	?>
                                                <tr>
                                                    <td>D110</td>
                                                    <td>פרטי מסמך</td>
                                                    <td style="text-align: left;"><?php echo $DocsInfoD110; ?></td>
                                                </tr>
                                                <?php } 
	if ($DocsInfoD120<='0'){} else { 
	?>
                                                <tr>
                                                    <td>D120</td>
                                                    <td>פרטי קבלות</td>
                                                    <td style="text-align: left;"><?php echo $DocsInfoD120; ?></td>
                                                </tr>
                                                <?php } ?>
                                                <tr>
                                                    <td>Z900</td>
                                                    <td>רשומת סגירה</td>
                                                    <td style="text-align: left;">1</td>
                                                </tr>



                                            </tbody>

                                            <tfoot>

                                                <tr>
                                                    <td></td>
                                                    <td>סה"כ : </td>
                                                    <td style="text-align: left;"><?php echo $TotalResomot; ?></td>
                                                </tr>

                                            </tfoot>

                                        </table>






                                        <table class="table borderless"
                                            style="font-size:12px; font-weight:bold; width: 30%" dir="rtl">
                                            <tbody>
                                                <tr>
                                                    <td>הנתונים הופקו באמצעות תוכנה : </td>
                                                    <td style="text-align: right; width: 20%;">247SOFT</td>
                                                    <td>מספר תעודת הרישום : </td>
                                                    <td style="text-align: right; width: 20%;">000000000</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="4">בתאריך : <?php echo date('d/m/Y') ?> בשעה :
                                                        <?php echo date('H:i') ?></td>
                                                </tr>

                                            </tbody>
                                        </table>


                                        <a href="<?php echo $PathDir; ?>/BKMVDATA.zip"
                                            class="btn btn-dark btn-sm text-white">שמור קבצים</a> | <a
                                            class="btn btn-success btn-sm text-white" href="javascript:void(0);"
                                            onclick="TINY.box.show({iframe:'pdf/PrintMakeFile.php?StartDate=<?php echo $StartDate ?>&EndDate=<?php echo $EndDate; ?>&Act=1',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})">הדפס</a>
                                        | <a href="CreateMasFile.php" class="btn btn-info btn-sm text-white">צור חדש</a>


                                    </div>
                                </div>

                                <?php } ?>


                                <?php if(!empty($_POST['MakeTrue'])) { 
	  
//// C100 סך רשומות מסוג כותרת מסמך
$DocsInfoC100 = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
//// D110 סך רשומות מסוג פרטי מסמך
$DocsInfoD110 = DB::table('docslist')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
//// D120 סך רשומות מסוג פרטי קבלה
$DocsInfoD120 = DB::table('docs_payment')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();

$TotalResomot = $DocsInfoC100+$DocsInfoD110+$DocsInfoD120+2;	  

/// פתיחת תקיות
$DirName = 'OPENFRMT'; // שם הספרייה בשרת

$IdBiz = mb_substr($SettingsInfo->CompanyId, 0, 8); // מספר עוסק מורשה או ח.פ. ללא ספרת ביקרות

/// נתונים משתנים
$Year = date('y'); /// שנה דו ספרתי YY לפי תאריך הנפקת הקובץ
$DateTime = date('mdHi'); /// MMDDhhmm
	  
$PathDir = $DirName.'/'.$IdBiz.'.'.$Year.'/'.$DateTime;	  
	
?>

                                <div class="row" align="center" dir="rtl">
                                    <div class="col-md-12 col-sm-12">
                                        <span>הפקת קבצים במבנה אחיד עבור:</span>

                                        <table class="table borderless"
                                            style="font-size:12px; font-weight:bold; width: 30%" dir="rtl">
                                            <tbody>
                                                <tr>
                                                    <td>מס' עוסק מורשה / ח.פ : </td>
                                                    <td style="text-align: right; width: 70%;">
                                                        <?php echo $SettingsInfo->CompanyId; ?></td>
                                                </tr>

                                                <tr>
                                                    <td>שם בית העסק : </td>
                                                    <td style="text-align: right; width: 70%;">
                                                        <?php echo $SettingsInfo->CompanyName; ?></td>
                                                </tr>

                                            </tbody>
                                        </table>

                                        <table class="table borderless"
                                            style="font-size:12px; font-weight:bold; width: 30%" dir="rtl">
                                            <tbody>
                                                <tr>
                                                    <td>טווח תאריכים מתאריך : </td>
                                                    <td style="text-align: right; width: 20%;">
                                                        <?php echo $StartDateView; ?></td>
                                                    <td>ועד תאריך : </td>
                                                    <td style="text-align: right; width: 20%;">
                                                        <?php echo $EndDateView; ?></td>
                                                </tr>

                                            </tbody>
                                        </table>


                                    </div>
                                </div>



                                <div class="row" align="center" dir="rtl">
                                    <div class="col-md-12 col-sm-12">

                                        <table class="table table-hover table-bordered"
                                            style="font-size:12px; font-weight:bold; width: 30%" dir="rtl">
                                            <thead>
                                                <tr class="bg-dark text-white">
                                                    <th style="text-align:right;" width="20%">מספר מסמך</th>
                                                    <th style="text-align:right;" width="30%">סוג מסמך</th>
                                                    <th style="text-align:right;" width="20%">סה"כ כמותי</th>
                                                    <th style="text-align:right;" width="30%">סה"כ כספי (בש"ח)</th>
                                                </tr>
                                            </thead>
                                            <tbody>


                                                <?php 
									  
	$DocsTables = DB::table('docstable')->where('CompanyNum', '=', $CompanyNum)->where('Misim', '=' ,'1')->orderBy('TypeHeader', 'ASC')->get();
									  
	foreach ($DocsTables as $DocsTable) {
		
	$DocsCount = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('TypeHeader', '=', $DocsTable->TypeHeader)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
	$DocsSum = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('TypeHeader', '=', $DocsTable->TypeHeader)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');	
	?>
                                                <tr>
                                                    <td><?php echo $DocsTable->TypeHeader; ?></td>
                                                    <td><?php echo $DocsTable->TypeTitleSingle; ?></td>
                                                    <td><?php echo $DocsCount; ?></td>
                                                    <td><?php if ($DocsSum==''){ echo '0'; } else { echo number_format(str_replace('-', "", $DocsSum), 2); } ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>

                                            </tbody>

                                        </table>






                                        <table class="table borderless"
                                            style="font-size:12px; font-weight:bold; width: 30%" dir="rtl">
                                            <tbody>
                                                <tr>
                                                    <td>הנתונים הופקו באמצעות תוכנה : </td>
                                                    <td style="text-align: right; width: 20%;">247SOFT</td>
                                                    <td>מספר תעודת הרישום : </td>
                                                    <td style="text-align: right; width: 20%;">000000000</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="4">בתאריך : <?php echo date('d/m/Y') ?> בשעה :
                                                        <?php echo date('H:i') ?></td>
                                                </tr>

                                            </tbody>
                                        </table>


                                        <a class="btn btn-success btn-sm text-white" href="javascript:void(0);"
                                            onclick="TINY.box.show({iframe:'pdf/PrintMakeFile.php?StartDate=<?php echo $StartDate ?>&EndDate=<?php echo $EndDate; ?>&Act=2',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})">הדפס</a>
                                        | <a href="CreateMasFile.php" class="btn btn-info btn-sm text-white">צור חדש</a>


                                    </div>
                                </div>




                                <?php } ?>














                            </div>
                        </div>


                        <script type="text/javascript" charset="utf-8">
                        $('#InputStartDate').change(function() {

                            $("#InputEndDate").attr({
                                "min": this.value
                            });

                            $('#InputEndDate').val(this.value);

                        });
                        </script>


                        <?php else: ?>
                        <?php redirect_to('../index.php'); ?>
                        <?php endif ?>


                        <?php endif ?>

                        <?php if (Auth::guest()): ?>

                        <?php redirect_to('../index.php'); ?>

                        <?php endif ?>

                        <?php require_once '../app/views/footernew.php'; ?>