
<?php require_once '../app/init.php';

if (empty($_GET['u'])) redirect_to(App::url());

$Supplier = DB::table('client')->where('id', $_GET['u'])->first();

$datetime = date('Y-m-d');

$ClientId = $_GET['u'];

?>
<meta name="csrf-token" content="<?php echo csrf_token() ?>">

<link href="<?php echo asset_url('office/css/vendor/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('office/css/bootstrap-custom.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('office/css/main.css') ?>" rel="stylesheet">
    <link href="<?php echo asset_url('office/css/imgpicker.css') ?>" rel="stylesheet">
	<!-- <link href="<?php echo asset_url('office/css/flat.css') ?>" rel="stylesheet"> -->
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <script type="text/javascript" src="<?php echo asset_url('js/date_time.js') ?>"></script>
	<?php $color = Config::get('app.color_scheme'); ?>
	<link href="<?php echo asset_url("office/css/colors/{$color}.css") ?>" rel="stylesheet" id="color_scheme">
	<script src="<?php echo asset_url('office/js/vendor/jquery-1.11.1.min.js') ?>"></script>

    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="<?php echo asset_url('office/js/vendor/bootstrap.min.js') ?>"></script>    
	<script src="<?php echo asset_url('office/js/BeePOS2.js') ?>"></script>
     <script src="<?php echo asset_url('office/js/jquery.imgpicker.js') ?>"></script>
	<script src="<?php echo asset_url('office/js/main.js') ?>"></script>
    

	<script>
		BeePOS.options = {
			ajaxUrl: '<?php echo App::url("ajax.php") ?>',
			lang: <?php echo json_encode(trans('main.js')) ?>,
			debug: <?php echo Config::get('app.debug')?1:0 ?>,
			
		};
	</script>
      
<link rel="stylesheet" href="tinybox2/style.css" />
<script type="text/javascript" src="tinybox2/tinybox.js"></script>

<script src="<?php echo asset_url('js/jqueryExecuting.js') ?>"></script>

<br>
<div class="alert alert-danger" dir="rtl" style="font-weight: bold;">
<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> לידיעתך: הודעות ישלחו ממספר הטלפון <?php echo Auth::user()->ContactMobile; ?> ומכתובת המייל <?php echo Auth::user()->email; ?>
</div>
                     
                                         
<div class="btn-group">
  <button type="button" class="btn btn-primary" onClick="BankInfoMessage()">פרטי חשבון בנק</button>
  <button type="button" class="btn btn-primary" onClick="Webinar12112017()">הקלטת וובינר 12/11/2017</button>
  <button type="button" class="btn btn-primary" onClick="Webinar08112017()">הקלטת וובינר 08/11/2017</button>
  <button type="button" class="btn btn-primary" onClick="Perak1()">פרק 1</button>
  <button type="button" class="btn btn-primary" onClick="Perak2()">פרק 2</button>
  <button type="button" class="btn btn-primary" onClick="Perak3()">פרק 3</button>
  <button type="button" class="btn btn-primary" onClick="SalePageMessage()">לינק לדף מכירה</button>
  <button type="button" class="btn btn-primary" onClick="KenesInvite2012()">כנס 20/12</button>
  <button type="button" class="btn btn-primary" onClick="EndCallMessage()">סיכום שיחה ללקוח</button>
</div>
<br><br>
 <h4><i class="fa fa-envelope"></i> שליחת הודעה פרטית SMS</h4>

                        
<div class="form-group">
<form id="SendSmss" name="SendSmss" onsubmit="return SendSms();" method="post" dir="rtl" autocomplete="off">
<input type="hidden" name="ClientName" value="<?php echo $Supplier->CompanyName; ?>">
<input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">
<input type="hidden" name="Phone" value="<?php echo $Supplier->ContactMobile; ?>">
 
<label>הקלד מלל חופשי</label>
<textarea name="Message" id="Message" class="form-control" rows="3" dir="rtl" required></textarea>

<div style="padding-top:10px;" align="right">
<button type="submit" name="submit" id="SmsSubmit" class="btn btn-success btn-block">שלח SMS</button>
</div>
</form>
</div> 

<?php
function getTinyUrl($url) {
    return $url;
    return file_get_contents("http://tinyurl.com/api-create.php?url=".$url);
}
?>



<script>
    
function get_short_url(long_url, func)
{
    $.getJSON(
        "https://api-ssl.bitly.com/v3/shorten?callback=?", 
        { 
            "format": "json",
            "apiKey": "R_fac86e2ce9314b588f51f839803b34b2",
            "login": "o_1oeap6lge3",
            "longUrl": long_url
        },
        function(response)
        {
            func(response.data.url);
        }
    );
}  
    
    

     
 
    
function BankInfoMessage() {
document.getElementById( 'Message' ).value = 'בהתאם לבקשתך להלן פרטים להעברה בנקאית:\nבנק אוצר החייל :: 14\nסניף 317\nחשבון: 102222\nעל שם: קליינס מרקטינג\nלאחר העברה נא לשלוח אסמכתא למייל: support@kl.gl\nולמלא את הטופס הבא: https://goo.gl/forms/GVv8ZnwC64bmzjTn1\nבהצלחה!';
document.getElementById( 'emailmessage' ).value =  'בהתאם לבקשתך להלן פרטים להעברה בנקאית:\nבנק אוצר החייל :: 14\nסניף 317\nחשבון: 102222\nעל שם: קליינס מרקטינג\nלאחר העברה נא לשלוח אסמכתא למייל: support@kl.gl\nולמלא את הטופס הבא: https://goo.gl/forms/GVv8ZnwC64bmzjTn1\nבהצלחה!';
document.getElementById( 'emailsubject' ).value =  'פרטי העברה בנקאית לקליינ׳ס מרקטינג ->';
}
function SalePageMessage() {
 
var long_url = "https://msp.ilaunch.co.il?AffId=10339&Lp=1509550235749221&Ref=Webinar&Email=<?php echo $Supplier->Email; ?>";    
var long_urls = get_short_url(long_url, function(short_url) { 

document.getElementById( 'Message' ).value = 'היי, הנה הפרטים לגבי קורס המשת״פ שביקשת:\n'+ short_url +'\nבהצלחה!';
document.getElementById( 'emailmessage' ).value =  'היי, הנה הפרטים לגבי קורס המשת״פ שביקשת:\n'+ short_url +'\nבהצלחה!';
} );
    


document.getElementById( 'emailsubject' ).value =  'הפרטים המלאים על קורס המשת״פ ->';
    
    
    
}
function Webinar08112017() {
    
    
    
var long_url = "http://msp.whiplash.co.il/webinar08112017/?Email=<?php echo $Supplier->Email; ?>";    
var long_urls = get_short_url(long_url, function(short_url) { 

document.getElementById( 'Message' ).value = 'היי, הנה ההקלטה של השידור החי מהמשת״פ:\n'+ short_url +'\nבהצלחה!';
document.getElementById( 'emailmessage' ).value =  'היי, הנה ההקלטה של השידור החי מהמשת״פ:\n'+ short_url +'\nבהצלחה!';
} );    
    

document.getElementById( 'emailsubject' ).value =  'הקלטת השידור החי של המשת״פ ->';
}
    
    


function KenesInvite2012() {
    
    
    
var long_url = "http://msp.whiplash.co.il/E20122017/?Ref=&AffId=10339&Lp=15129263262583638&Email=<?php echo $Supplier->Email; ?>";    
var long_urls = get_short_url(long_url, function(short_url) { 

document.getElementById( 'Message' ).value = 'היי, מה שלומך?\nבהמשך לשיחתנו הנעימה בטלפון, אני מפנה אותך אל\nהפרטים המלאים על פגישת VIP הסגורה עם יוסי ובן קליין\nקישור כניסה לדף הפרטים >>\n'+ short_url;
document.getElementById( 'emailmessage' ).value = 'היי, מה שלומך?\nבהמשך לשיחתנו הנעימה בטלפון, אני מפנה אותך אל\nהפרטים המלאים על פגישת VIP הסגורה עם יוסי ובן קליין\nקישור כניסה לדף הפרטים >>\n'+ short_url;
} );    
    

document.getElementById( 'emailsubject' ).value =  'הזמנה אישית לכנס מיוחד ->';
}
    
    
function Webinar12112017() {
    

var long_url = "http://msp.whiplash.co.il/webinar12112017/?Email=<?php echo $Supplier->Email; ?>";    
var long_urls = get_short_url(long_url, function(short_url) { 

document.getElementById( 'Message' ).value = 'היי, הנה ההקלטה של השידור החי מהמשת״פ:\n'+ short_url +'\nבהצלחה!';
document.getElementById( 'emailmessage' ).value =  'היי, הנה ההקלטה של השידור החי מהמשת״פ:\n'+ short_url +'\nבהצלחה!';
} );        
    
document.getElementById( 'emailsubject' ).value =  'הקלטת השידור החי של המשת״פ ->';
}
    
    
    
    
    
function Perak1() {
    
var long_url = "https://msp.ilaunch.co.il/GetLead/?AffId=10339&Lp=150845909412821306&Ref=CRM&V=1&Email=<?php echo $Supplier->Email; ?>";    
var long_urls = get_short_url(long_url, function(short_url) { 

document.getElementById( 'Message' ).value = 'היי, הנה הפרק הראשון של המשת״פ שביקשת:\n'+ short_url +'\nבהצלחה!';
document.getElementById( 'emailmessage' ).value =  'היי, הנה הפרק הראשון של המשת״פ שביקשת:\n'+ short_url +'\nבהצלחה!';
} );   
    

document.getElementById( 'emailsubject' ).value =  'הפרק הראשון של המשת״פ ->';
}
    
    
function Perak2() {
    
var long_url = "https://msp.ilaunch.co.il/GetLead/?AffId=10339&Lp=150845909412821306&Ref=CRM&V=2&Email=<?php echo $Supplier->Email; ?>";    
var long_urls = get_short_url(long_url, function(short_url) { 

document.getElementById( 'Message' ).value = 'היי, הנה הפרק השני של המשת״פ שביקשת:\n'+ short_url +'\nבהצלחה!';
document.getElementById( 'emailmessage' ).value =  'היי, הנה הפרק השני של המשת״פ שביקשת:\n'+ short_url +'\nבהצלחה!';
} );  
    
    

document.getElementById( 'emailsubject' ).value =  'הפרק השני של המשת״פ ->';
}
    
    
function Perak3() {
    
var long_url = "https://msp.ilaunch.co.il/GetLead/?AffId=10339&Lp=150845909412821306&Ref=CRM&V=3&Email=<?php echo $Supplier->Email; ?>";    
var long_urls = get_short_url(long_url, function(short_url) { 

document.getElementById( 'Message' ).value = 'היי, הנה הפרק השלישי של המשת״פ שביקשת:\n'+ short_url +'\nבהצלחה!';
document.getElementById( 'emailmessage' ).value =  'היי, הנה הפרק השלישי של המשת״פ שביקשת:\n'+ short_url +'\nבהצלחה!';
} );  
    

document.getElementById( 'emailsubject' ).value =  'הפרק השלישי של המשת״פ ->';
}
    
    
    
function EndCallMessage() {
    
 var long_url = "https://msp.ilaunch.co.il?AffId=10339&Lp=1509550235749221&Ref=Webinar&Email=<?php echo $Supplier->Email; ?>";    
var long_urls = get_short_url(long_url, function(short_url) { 

document.getElementById( 'Message' ).value = 'היי, מה שלומך?\nבהמשך לשיחתנו הנעימה בטלפון, אני מפנה אותך אל\nהפרטים המלאים על תכנית ההכשרה והליווי שלנו + סילבוס:\nקישור כניסה לדף הפרטים >>'+short_url;
} );  
    


document.getElementById( 'emailmessage' ).value =  'היי, מה שלומך?\nבהמשך לשיחתנו הנעימה בטלפון, אני מפנה אותך אל\nהפרטים המלאים על תכנית ההכשרה והליווי שלנו + סילבוס:\nקישור כניסה לדף הפרטים >> <?php echo getTinyUrl("https://msp.ilaunch.co.il?AffId=10339&Lp=1509550235749221&Ref=Webinar&Email=".$Supplier->Email); ?>\nתוכל לקבל את כל המידע לגבי תכנית ההכשרה של המשת"פ.\nבאתר תוכל למצוא:\n1. סילבוס.\n2. פירוט על תוכנית ההכשרה.\n3. פירוט על חדר המסחר.\n4. טעימה מתוכנית ההכשרה המלאה.\nהכל בלינק הבא:\nתוכלי לצפות בהקלטה מוקלטת של הובינר הראשון שלנו >> <?php echo getTinyUrl("http://msp.whiplash.co.il/webinar08112017/?Email=".$Supplier->Email); ?>\nעלות תכנית ההכשרה– 5,500 ₪  כולל מע"מ.\nזה בעצם יוצא 12 תשלומים של 458 ₪ בלבד בפריסה נוחה וללא ריבית. \nקישור לסידרת הסרטונים החינמית ברשת >> <?php echo getTinyUrl("https://msp.ilaunch.co.il/GetLead/?AffId=10339&Lp=150845909412821306&Ref=CRM&Email=".$Supplier->Email); ?>\nנרשמת לתכנית ההכשרה ולאחר מכן התחרטת? אנו מתחייבים להחזיר לך את מלוא התשלום עבור תכנית ההכשרה במקרה של ביטול מכל סיבה שהיא עד 14 ימים, מיום פתיחת הקורס.\nתכנית ההכשרה כוללת:\n1.      10 מודולים פרקטיים הכוללים שידורים חיים אחת לשבוע.\n2.       חודשים וחצי של ליווי מקצועי ממנטורים שעברו הכשרה שלנו ויצרו כסף בתחום.\n3.      2 מפגשים פרונטליים המלווים באסטרטגיות כחלק מההכשרה.\n4.      נופש ל – 2 לילות הכולל כנסים מקצועיים עם סופר אפיליאייטס מהתחום.\n5.      בונוסים בשווי של 17,000 ₪.\n6.      מודול פרקטיקה המדגים תהליכים מלאים ברשתות שותפים\n7.      שקיפות מלאה לכל התוכנות והמערכות שלנו.\n8. אפשרות להצטרף לטופ אפיליאייט בארץ.\nההרשמה להכשרה נסגרת ב20.11.17, לא ניתן יהיה להירשם אחרי המועד הנ"ל';
document.getElementById( 'emailsubject' ).value =  'סיכום השיחה שלנו ->';
}
</script>

 
 <div id="IframeOpenCreditRunSMS" class="alert alert-warning" dir="rtl" style="margin-top:20px; display:none;">
 אנא המתן בזמן עיבוד הנתונים...
</div>

<div id="IframeOpenCreditSuccessSMS" class="alert alert-success" dir="rtl" style="margin-top:20px; display:none;">
ההודעה נשלחה בהצלחה
</div>
 
 <?php 
 if ($Supplier->Email==''){} else {
 ?>
 
 <hr>
 
 <h4><i class="fa fa-envelope"></i> שליחת הודעה פרטית EMAIL</h4>
 
 <form action="sendEmail" class="ajax-form">

			                <input type="hidden" name="to" value="<?php echo $Supplier->Email; ?>">
                            <input type="hidden" name="ClientId" value="<?php echo $Supplier->id; ?>">


			            <div class="form-group">
			                <input type="text" name="subject" id="emailsubject" placeholder="נושא" class="form-control">
			            </div>

			            <div class="form-group">
			                <textarea class="form-control" id="emailmessage" name="message" placeholder="תוכן ההודעה" rows="5"></textarea>
			            </div>

				
						<button type="submit" class="btn btn-success btn-block">שלח מייל</button>
				</form>
 
 <?php } ?>

