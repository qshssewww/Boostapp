<?php require_once '../../app/initcron.php'; ?>
<?php if (Auth::userCan('36')): ?>
<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;

$Items = DB::table('coupon')->where('CompanyNum','=',$CompanyNum)->where('id', '=' , $ItemId)->first();

?>

   
    
                <div class="form-group" dir="rtl">
                <label>כותרת הקופון</label>
                <input type="text" name="Title" id="Title" class="form-control" value="<?php echo htmlentities($Items->Title); ?>">
                </div>
    
                <div class="form-group" dir="rtl">
                <label>דף סליקה</label>
                <select name="PageId" class="form-control">
                <?php
				$payment_pages = DB::table('payment_pages')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->get();
				foreach ($payment_pages as $payment_page) {
				?>
                <option value="<?php echo $payment_page->id; ?>" <?php if ($Items->PageId==$payment_page->id) { echo 'selected'; } else {} ?>><?php echo $payment_page->Title; ?> :: <?php echo $payment_page->TitleUrl; ?></option>	
                <?php } ?>
                </select>
                </div>
    
                <div class="form-group" dir="rtl">
                <label>קוד</label>
                <input type="text" name="Code" id="Code" class="form-control" value="<?php echo htmlentities($Items->Code); ?>">
                </div>
    
                <div class="form-group" dir="rtl">
                <label>סכום</label>
                <input type="text" name="Amount" id="Amount" class="form-control" value="<?php echo htmlentities($Items->Amount); ?>">
                </div>
    
               	<?php $JustDate = date('Y-m-d'); ?>
                <div class="form-group" dir="rtl">
                <label>תאריך התחלה</label>
                <input type="date" class="form-control" name="StartDate" id="StartDate" value="<?php echo htmlentities($Items->StartDate); ?>">
                </div>
    
                <div class="form-group" dir="rtl">
                <label>תאריך סיום</label>
                <input type="date" class="form-control" name="EndDate" id="EndDate" value="<?php echo htmlentities($Items->EndDate); ?>">
                </div>
                
                <div class="form-group" dir="rtl">
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>  
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>מבוטל</option>      
                </select>
                </div>  

<?php endif ?>