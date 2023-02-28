<?php require_once '../../app/initcron.php'; ?>
<?php if (Auth::userCan('34')): ?>
<?php

$ItemId = $_POST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;

$Items = DB::table('paymentstep')->where('CompanyNum','=',$CompanyNum)->where('id', '=' , $ItemId)->first();

?>

                <div class="form-group" >
                <label>כותרת למדרגה</label>
                <input type="text" name="Title" id="Title" class="form-control" value="<?php echo htmlentities($Items->Title); ?>">
                </div>
    
                <div class="form-group" >
                <label>מספר תשלומים</label>
                <input type="text" name="NumPayment" id="NumPayment" class="form-control"  onkeypress='validate(event)' value="<?php echo $Items->NumPayment; ?>" required>
                </div>
    
    
                <div class="form-group" >
                <label>סכום</label>
                <input type="text" name="Amount" id="Amount" class="form-control" onkeypress='validate(event)' value="<?php echo $Items->Amount; ?>" required>
                </div> 
                
                <div class="form-group" >
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>מוצג</option>  
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>מוסתר</option>      
                </select>
                </div>   
<?php endif ?>